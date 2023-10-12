<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    config()->set('sharp.uploads.tmp_dir', 'tmp');
    Storage::fake('local');
    $this->withoutExceptionHandling();
});

it('allows to upload a file', function () {
    $this
        ->postJson('/sharp/api/upload', [
            'file' => UploadedFile::fake()->image('image.jpg', 600, 600),
        ])
        ->assertOk()
        ->assertJson(['name' => 'image.jpg']);
});

it('allows to upload a file on a custom defined disk', function () {
    config()->set('sharp.uploads.tmp_disk', 'uploads');
    Storage::fake('uploads');

    $this
        ->postJson('/sharp/api/upload', [
            'file' => UploadedFile::fake()->image('image.jpg', 600, 600),
        ])
        ->assertOk();

    Storage::disk('uploads')->assertExists('/tmp/image.jpg');
});

it('adds a increment to the file name if needed', function () {
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
});

it('copies the file to the wanted directory', function () {
    $this
        ->postJson('/sharp/api/upload', [
            'file' => UploadedFile::fake()->image('image.jpg', 600, 600),
        ]);

    $this->assertTrue(Storage::disk('local')->exists('/tmp/image.jpg'));
});
