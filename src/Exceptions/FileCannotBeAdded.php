<?php

namespace Spatie\MediaLibrary\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\Helpers\File;

class FileCannotBeAdded extends Exception
{
    public static function unknownType()
    {
        return new static('Only strings, FileObjects and UploadedFileObjects can be imported');
    }

    public static function fileIsTooBig($path)
    {
        $fileSize = File::getHumanReadableSize(filesize($path));

        $maxFileSize = File::getHumanReadableSize(config('laravel-medialibrary.max_file_size'));

        return new static("File `{$path}` has a size of {$fileSize} which is greater than the maximum allowed {$maxFileSize}");
    }

    public static function fileDoesNotExist($path)
    {
        return new static("File `{$path}` does not exist");
    }

    public static function unreachableUrl($url)
    {
        return new static("Url `{$url}` cannot be reached");
    }

    public static function diskDoesNotExist($diskName)
    {
        return new static("There is no filesystem disk named `{$diskName}`");
    }

    public static function modelDoesNotExist(Model $model)
    {
        $modelClass = get_class($model);

        return new static("Before adding media to it, you should first save the $modelClass-model");
    }

    public static function requestDoesNotHaveFile($key)
    {
        return new static("The current request does not have a file in a key named `{$key}`");
    }
}
