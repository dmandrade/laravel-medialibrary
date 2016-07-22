<?php

namespace Spatie\MediaLibrary\Exceptions;

use Exception;

class UrlCannotBeDetermined extends Exception
{
    public static function mediaNotPubliclyAvailable($storagePath, $publicPath)
    {
        return new static("Storagepath `{$storagePath}` is not part of public path `{$publicPath}`");
    }
}
