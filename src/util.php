<?php

/**
 * Fetch an env variable or return default if not set
 */
function env(string $variable, string $default = ''): string {
    $env = getenv($variable);

    return $variable !== '' ? $variable : $default;
}

/**
 * Format a stream url by replacing the %stream% placeholder
 */
function format_stream_url(string $url, string $stream): string {
    return str_replace('%stream%', $stream, $url);
}