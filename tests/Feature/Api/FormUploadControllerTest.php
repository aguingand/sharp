<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FormUploadControllerTest extends BaseApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['sharp.uploads.tmp_dir' => 'tmp']);
        Storage::fake('local');
    }

    /** @test */
    public function we_can_upload_a_file()
    {
        $this
            ->postJson('/sharp/api/upload', [
                'file' => UploadedFile::fake()->image('image.jpg', 600, 600),
            ])
            ->assertOk()
            ->assertJson(['name' => 'image.jpg']);
    }

    /** @test */
    public function we_can_upload_a_file_on_a_custom_defined_disk()
    {
        config(['sharp.uploads.tmp_disk' => 'uploads']);
        Storage::fake('uploads');

        $this
            ->postJson('/sharp/api/upload', [
                'file' => UploadedFile::fake()->image('image.jpg', 600, 600),
            ])
            ->assertOk();

        Storage::disk('uploads')->assertExists('/tmp/image.jpg');
    }

    /** @test */
    public function when_uploading_an_already_existing_filename_we_change_the_name()
    {
        $this
            ->postJson('/sharp/api/upload', [
                'file' => UploadedFile::fake()->image('image.jpg', 600, 600),
            ])
            ->assertOk()
            ->assertJson(['name' => 'image.jpg']);

        $this
            ->postJson('/sharp/api/upload', [
                'file' => UploadedFile::fake()->image('image.jpg', 600, 600),
            ])
            ->assertOk()
            ->assertJson(['name' => 'image-1.jpg']);
    }

    /** @test */
    public function file_is_copied_in_the_wanted_directory()
    {
        $this
            ->postJson('/sharp/api/upload', [
                'file' => UploadedFile::fake()->image('image.jpg', 600, 600),
            ]);

        $this->assertTrue(Storage::disk('local')->exists('/tmp/image.jpg'));
    }
}
