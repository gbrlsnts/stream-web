<?php

namespace App\Services;

use \Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Token as TokenModel;
use Carbon\Carbon;

class Token
{
    /**
     * Token model
     *
     * @var TokenModel
     */
    protected $token;

    /**
     * Initialize the service
     */
    public function __construct()
    {
        $this->token = new TokenModel();
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
}