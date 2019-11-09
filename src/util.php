<?php

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
function format_stream_url(string $url, string $stream): string
{
    return str_replace('%stream%', $stream, $url);
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