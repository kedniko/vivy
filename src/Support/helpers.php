<?php

function base_path($path = ''): string
{
    return __DIR__.'/../../'.str_replace(['/', '\\'], DIRECTORY_SEPARATOR, ltrim((string) $path, '/'));
}
