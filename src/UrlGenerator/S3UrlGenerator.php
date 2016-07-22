<?php

namespace Spatie\MediaLibrary\UrlGenerator;

class S3UrlGenerator extends BaseUrlGenerator
{
    /**
     * Get the url for the profile of a media item.
     *
     * @return string
     */
    public function getUrl()
    {
        return config('laravel-medialibrary.s3.domain').'/'.$this->getPathRelativeToRoot();
    }
}
