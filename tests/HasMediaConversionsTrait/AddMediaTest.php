<?php

namespace Spatie\MediaLibrary\Test\HasMediaConversionsTrait;

use Spatie\MediaLibrary\Test\TestCase;
use Spatie\MediaLibrary\Test\TestModelWithConversion;

class AddMediaTest extends TestCase
{
    /** @test */
    public function it_can_add_an_file_to_the_default_collection()
    {
        $media = $this->testModelWithoutMediaConversions
            ->copyMedia($this->getTestFilesDirectory('test.jpg'))
            ->toMediaLibrary();

        $this->assertEquals('default', $media->collection_name);
    }

    /** @test */
    public function it_can_create_a_derived_version_of_an_image()
    {
        $media = $this->testModelWithConversion->addMedia($this->getTestJpg())->toCollection('images');

        $this->assertFileExists($this->getMediaDirectory($media->id.'/conversions/thumb.jpg'));
    }

    /** @test */
    public function it_will_not_create_a_derived_version_for_non_registered_collections()
    {
        $media = $this->testModelWithoutMediaConversions->addMedia($this->getTestJpg())->toCollection('downloads');

        $this->assertFileNotExists($this->getMediaDirectory($media->id.'/conversions/thumb.jpg'));
    }

    /** @test */
    public function it_will_create_a_derived_version_for_an_image_without_an_extension()
    {
        $media = $this->testModelWithConversion
            ->addMedia($this->getTestFilesDirectory('image'))
            ->toCollection('images');

        $this->assertFileExists($this->getMediaDirectory($media->id.'/conversions/thumb.jpg'));
    }

    /** @test */
    public function it_will_keep_the_original_file_extension_when_using_the_src_format()
    {
        $modelClass = new class extends TestModelWithConversion {
                 public function registerMediaConversions()
                 {
                     $this->addMediaConversion('thumb')
                                ->setCrop(50, 50, 10, 10)
                                ->setFormat('src')
                                ->nonQueued();
                 }
             };

        $model = $modelClass::first();

        $media = $model
            ->addMedia($this->getTestFilesDirectory('test.png'))
            ->toCollection('images');

        $this->assertFileExists($this->getMediaDirectory($media->id.'/conversions/thumb.png'));
    }

    /** @test */
    public function it_can_create_a_derived_version_of_a_pdf_if_imagick_exists()
    {
        $media = $this->testModelWithConversion
            ->addMedia($this->getTestFilesDirectory('test.pdf'))
            ->toCollection('images');

        $thumbPath = $this->getMediaDirectory($media->id.'/conversions/thumb.jpg');

        class_exists('Imagick') ? $this->assertFileExists($thumbPath) : $this->assertFileNotExists($thumbPath);
    }
}
