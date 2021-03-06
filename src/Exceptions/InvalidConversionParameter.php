<?php

namespace Spatie\MediaLibrary\Exceptions;

use Exception;

class InvalidConversionParameter extends Exception
{
    public static function invalidWidth()
    {
        return new static('Width should be numeric an greater than 0');
    }

    public static function invalidHeight()
    {
        return new static('Height should be numeric an greater than 0');
    }

    public static function invalidFormat($givenFormat, array $validFormats)
    {
        $validFormats = implode(', ', $validFormats);

        return new static("Format `{$givenFormat}` is not one of the allowed formats: {$validFormats}");
    }

    public static function invalidFit($givenFit, array $givenFits)
    {
        $givenFits = implode(', ', $givenFits);

        return new static("Format `{$givenFit}` is not one of the allowed formats: {$givenFits}");
    }

    public static function shouldBeNumeric($name, $value)
    {
        return new static("{$name} should be numeric. `{$value}` given.");
    }

    public static function shouldBeGreaterThanOne($name, $value)
    {
        return new static("{$name} should be greater than one. `{$value}` given.");
    }
}
