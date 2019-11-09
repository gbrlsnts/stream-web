<?php

namespace App\Services;

use App\Models\Stream;
use App\Models\Token as TokenModel;

use App\Services\Crypto;

use Carbon\Carbon;
use \Illuminate\Database\Eloquent\ModelNotFoundException;
use Psr\Http\Message\ServerRequestInterface as PsrRequest;
use Psr\Http\Message\ResponseInterface as PsrResponse;

use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\Cookie;
use Dflydev\FigCookies\SetCookie;

class Token
{
    /**
     * Crypto service
     *
     * @var Crypto
     */
    protected $crypto;

    /**
     * Token model
     *
     * @var TokenModel
     */
    protected $token;

    /**
     * Initialize the service
     */
    public function __construct(Crypto $crypto)
    {
        $this->token = new TokenModel();
        $this->crypto = $crypto;
    }

    /**
     * Generate and save a token
     *
     * @param integer $streamId
     * @param integer $size
     * @param string $description
     * @param integer $maxUsages
     * @param Carbon $expiresAt
     * @return TokenModel
     */
    public function generateToken(int $streamId, int $size = 5, string $description, int $maxUsages, Carbon $expiresAt): TokenModel
    {
        $expires = Carbon::parse($body['expires_at']);

        $token = new TokenModel;
        $token->stream_id = $streamId;
        $token->token = generate_token($size);
        $token->description = $description;
        $token->num_usages = 0;
        $token->max_usages = $maxUsages > 0 ? $maxUsages : 0;
        $token->expires_at = $expires->greaterThanOrEqualTo(Carbon::now()) ? $expires->toDateTimeString() : NULL;
        $token->created_at = Carbon::now()->toDateTimeString();
        $token->saveOrFail();

        return $token;
    }

    /**
     * Increment the token usages counter
     *
     * @param int $streamId
     * @param string $token
     * @return void
     */
    public function incrementTokenUsages(int $streamId, string $token)
    {
        try {
            $token = $this->token
                ->where('stream_id', $streamId)
                ->where('token', $token)
                ->firstOrFail();

            $token->num_usages += 1;
            $token->save();
        } catch (ModelNotFoundException $e) {}
    }

    /**
     * Validate a token
     *
     * @param integer $streamId
     * @param string $code The token
     * @return boolean
     */
    public function isTokenValid(int $streamId, string $code): bool
    {
        $token = null;

        try {
            $token = $this->token
                ->where('stream_id', $streamId)
                ->where('token', $code)
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return false;
        }

        if($token->max_usages > 0 && $token->num_usages >= $token->max_usages)
            return false;

        $expiresAt = Carbon::parse($token->expires_at)->addDays(1); // treat expire day as valid day

        if(!is_null($token->expires_at) && Carbon::now()->greaterThan($expiresAt))
            return false;

        return true;
    }

    /**
     * Extract a stream's token from a request. Checks request first then fallbacks to cookie.
     *
     * @param string $streamName
     * @param PsrRequest $request
     * @return string
     */
    public function getStreamTokenFromRequest(PsrRequest $request, string $streamName): string
    {
        $requestToken = $request->getQueryParams()['token'];

        if(!is_null($requestToken))
            return $requestToken;

        $cookie = FigRequestCookies::get($request, \token_cookie_name())->getValue();

        try {
            return \json_decode($this->crypto->decrypt($cookie), true)[$streamName] ?? '';
        } catch (\Throwable $th) {
            return '';
        }
    }

    /**
     * Add a token to the response cookie
     *
     * @param string $streamName
     * @param string $token
     * @param PsrResponse $response
     * @return PsrResponse
     */
    public function addTokenToResponse(string $streamName, string $token, PsrResponse $response): PsrResponse
    {
        return FigResponseCookies::modify($response, \token_cookie_name(), function(SetCookie $cookie) use ($streamName, $token) {
            $tokens = [];
            $value = $cookie->getValue();

            if(!\is_null($value))
                $tokens = \json_decode($value);

            $tokens[$streamName] = $token;

            return $cookie
                ->withValue($this->crypto->encrypt(\json_encode($tokens)))
                ->withHttpOnly()
                ->withPath('/')
                ->withExpires(Carbon::now()->addYears(1));
        });
    }


}