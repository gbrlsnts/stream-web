<?php

use Slim\Http\Request;
use App\Models\Stream;

/**
 * Fetch an env variable or return default if not set
 */
function appenv(string $variable, string $default = ''): string
{
    $env = getenv($variable);

    return $env && $env !== '' ? $env : $default;
}

/**
 * Format a stream url by replacing the %stream% placeholder
 */
function format_stream_url(string $url, ?string $user, string $stream, string $expires, string $token): string
{
    return str_replace([
        '%user%',
        '%stream%',
        '%expire%',
        '%token%',
    ], [
        $user,
        $stream,
        $expires,
        $token,
    ], $url);
}

/**
 * Returns an absolute path relative to the project's root
 * 
 * string $relative The relative path from the project's root
 */
function absolute_path(string $relative): string
{
    $root = dirname(__FILE__) . '/../';

    return $root . $relative;
}

/**
 * Generate a token with a given size. Default = 5
 *
 * @param integer $size
 * @return string
 */
function generate_token(int $size = 5): string
{
    return strtoupper(substr(bin2hex(random_bytes($size)), 0, $size));
}

/**
 * Get the tokens cookie name
 *
 * @return string
 */
function token_cookie_name(): string
{
    return 'TOKENS';
}

/**
 * Get secure link params for hls
 *
 * @param string $user user accessing the stream
 * @param string $stream
 * @param string $ip client ip address
 * @param string $secret
 * @param integer $ttl token time to live
 * @return array ['expires', 'token']
 */
function get_secure_link_params($user, $stream, $ip, $secret, $ttl = 3600): array
{
    $find = array('/\+/', '/\//', '/==/');
    $replace = array('-', '_', '');

    $expires = time() + $ttl;

    $string = "$expires $ip $stream $user $secret";
    $ingest = base64_encode(pack('H*', md5($string)));

    return [
        'expires' => $expires,
        'token' => preg_replace($find, $replace, $ingest),
    ];
}

/**
 * Get a stream url with tokens
 *
 * @param Request $request
 * @param Stream $stream
 * @param array $settings
 * @return string
 */
function get_secured_stream_url(Request $request, Stream $stream, object $settings): string
{
    $user = getUserIdOrToken($request);

    $secureLink = get_secure_link_params(
        $user,
        $stream->name,
        $request->getAttribute('ip_address'),
        $settings['app']['secure_link_secret'],
        $settings['app']['secure_link_ttl'],
    );

    return format_stream_url(
        $settings['player']['hls_url'],
        $user,
        $stream->name,
        $secureLink['expires'],
        $secureLink['token']
    );
}

/**
 * Get an user id or token in current request
 *
 * @param Request $request
 * @return string
 */
function getUserIdOrToken(Request $request): string {
    $user = $request->getAttribute('user');
    $token = $request->getAttribute('stream_token');

    if($user)
        return (string) $user->id;

    return $token ?? '';
}

/**
 * Get a list of trusted proxies
 *
 * @return string
 */
function get_trusted_proxies($settings): array
{
    $contents = $settings['app']['trusted_proxy'];

    return explode(',', $contents);
}