<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent\Uploads;

use Code16\Sharp\Form\Eloquent\Uploads\Thumbnails\Thumbnail;
use Code16\Sharp\Tests\Unit\Form\Eloquent\Utils\TestWithSharpUploadModel;
use Code16\Sharp\Tests\Unit\SharpEloquentBaseTestCase;
use Illuminate\Support\Facades\Storage;

class SharpUploadModelTest extends SharpEloquentBaseTestCase
{
    use TestWithSharpUploadModel;

    /** @test */
    public function when_setting_the_magic_file_attribute_we_can_fill_several_attributes()
    {
        $file = $this->createImage();
        $upload = $this->createSharpUploadModel($file);

        $upload->file = [
            'file_name' => 'test/test.png',
            'mime_type' => 'test_mime',
            'size' => 1,
        ];

        $this->assertEquals('test/test.png', $upload->file_name);
        $this->assertEquals('test_mime', $upload->mime_type);
        $this->assertEquals(1, $upload->size);
    }

    /** @test */
    public function a_thumbnail_is_created_when_asked()
    {
        $file = $this->createImage();
        $upload = $this->createSharpUploadModel($file);

        $this->assertStringStartsWith(
            '/storage/thumbnails/data/-150_q-90/'.basename($file),
            $upload->thumbnail(null, 150),
        );

        $this->assertTrue(
            Storage::disk('public')->exists('thumbnails/data/-150_q-90/'.basename($file)),
        );
    }

    /** @test */
    public function thumbnail_creation_returns_null_on_error()
    {
        $file = $this->createImage();
        $upload = $this->createSharpUploadModel($file);

        // Corrupt data
        $upload->update(['file_name' => null]);

        $this->assertNull($upload->thumbnail());
    }

    /** @test */
    public function transformation_filters_are_considered_when_creating_a_thumbnail()
    {
        $filters = [
            'crop' => [
                'height' => .5,
                'width' => .75,
                'x' => .3,
                'y' => .34,
            ],
            'rotate' => [
                'angle' => 45,
            ],
        ];

        $upload = $this->createSharpUploadModel($this->createImage());
        $upload->filters = $filters;
        $upload->save();

        $folderPath = 'thumbnails/data/-150_'.md5(serialize($filters)).'_q-90';

        $this->assertStringStartsWith(
            "/storage/{$folderPath}/".basename($upload->file_name),
            $upload->thumbnail(null, 150),
        );

        $this->assertTrue(Storage::disk('public')->exists("{$folderPath}/".basename($upload->file_name)));
    }

    /** @test */
    public function we_can_call_a_closure_after_a_thumbnail_creation()
    {
        $thumbWasCreated = null;
        $thumbWasCreatedTwice = null;
        $file = $this->createImage();
        $upload = $this->createSharpUploadModel($file);

        (new Thumbnail($upload))
            ->setAfterClosure(function (bool $wasCreated, string $path, $disk) use (&$thumbWasCreated) {
                $thumbWasCreated = $wasCreated;
            })
            ->make(150);

        (new Thumbnail($upload))
            ->setAfterClosure(function (bool $wasCreated, string $path, $disk) use (&$thumbWasCreatedTwice) {
                $thumbWasCreatedTwice = $wasCreated;
            })
            ->make(150);

        $this->assertTrue($thumbWasCreated);
        $this->assertFalse($thumbWasCreatedTwice);
    }
}
