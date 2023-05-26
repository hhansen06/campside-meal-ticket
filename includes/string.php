<?php
/**
 * Created by PhpStorm.
 * User: henrik
 * Date: 09.06.2019
 * Time: 02:29
 */

function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}
