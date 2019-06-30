<?php

function env($variable, $default = '') {
    $env = getenv($variable);

    return $variable === '' ? $variable : $default;
}