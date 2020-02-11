<?php
function prefixUri($uri)
{
    $prefix = config('ark.prefix');

    $uri = trim($uri, '/');

    if ( !isset($prefix) || !$prefix) {
        return '/'.$uri;
    }

    /*
    if (Str::startsWith($uri, $this->prefix)) {
        return $uri;
    }
    */

    return rtrim('/'.trim($prefix, '/').'/'.$uri, '/');
}
?>