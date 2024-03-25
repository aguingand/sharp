<?php

use Code16\Sharp\Form\Fields\Formatters\UploadFormatter;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
});

it('allows to format value to front', function () {
    $formatter = app(UploadFormatter::class);

    $field = SharpFormUploadField::make('upload');
    $this->assertEquals(
        [
            'name' => 'test.png',
            'path' => 'files/test.png',
            'disk' => 'local',
            'thumbnail' => 'path/of/thumbnail.png',
            'size' => 2000,
        ],
        $formatter
            ->toFront($field, [
                'name' => 'test.png',
                'path' => 'files/test.png',
                'disk' => 'local',
                'thumbnail' => 'path/of/thumbnail.png',
                'size' => 2000,
            ]),
    );
});

it('ignores not existing file from front', function () {
    $formatter = app(UploadFormatter::class);
    $field = SharpFormUploadField::make('upload');

    $this->assertEquals(
        [],
        $formatter->fromFront(
            $field,
            'attribute',
            [
                'name' => 'test.png',
            ]),
    );
});

it('allows to use a closure as storageBasePath', function () {
    UploadedFile::fake()
        ->image('image.jpg')
        ->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

    $path = '/some/path';

    $field = SharpFormUploadField::make('upload')
        ->setStorageBasePath(function () use (&$path) {
            return $path;
        })
        ->setImageCropRatio('16:9')
        ->setStorageDisk('local');

    $path = '/some/updated/path';

    expect(
        app(UploadFormatter::class)
            ->fromFront($field, 'attr', ['name' => '/image.jpg', 'uploaded' => true])
    )->toHaveKey('file_name', '/some/updated/path/image.jpg');
});

it('returns full object after no change was made if configured', function () {
    $field = SharpFormUploadField::make('upload')
        ->setStorageDisk('local')
        ->setImageTransformable()
        ->setStorageBasePath('data/Test');

    $value = [
        'path' => 'data/Test/image.jpg',
        'size' => 0,
        'mime_type' => 'image/jpeg',
        'disk' => 'local',
        'filters' => ['rotate' => ['angle' => 20]],
    ];

    expect(
        app(UploadFormatter::class)
        ->setAlwaysReturnFullObject()
        ->fromFront($field, 'attribute', $value)
    )
        ->toEqual([
            'file_name' => 'data/Test/image.jpg',
            'size' => 0,
            'mime_type' => 'image/jpeg',
            'disk' => 'local',
            'filters' => ['rotate' => ['angle' => 20]],
        ]);
});

it('returns full object after only transformations if configured', function () {
    $field = SharpFormUploadField::make('upload')
        ->setStorageDisk('local')
        ->setImageTransformable()
        ->setStorageBasePath('data/Test');

    $value = [
        'path' => 'data/Test/image.jpg',
        'disk' => 'local',
        'size' => 120,
        'uploaded' => false,
        'transformed' => true,
        'filters' => [
            'crop' => [
                'height' => .5,
                'width' => .75,
                'x' => .3,
                'y' => .34,
            ],
            'rotate' => [
                'angle' => 45,
            ],
        ],
    ];

    expect(
        app(UploadFormatter::class)
            ->setAlwaysReturnFullObject()
            ->fromFront($field, 'attr', $value)
    )
        ->toEqual([
            'file_name' => 'data/Test/image.jpg',
            'disk' => 'local',
            'size' => 120,
            'filters' => [
                'crop' => [
                    'height' => .5,
                    'width' => .75,
                    'x' => .3,
                    'y' => .34,
                ],
                'rotate' => [
                    'angle' => 45,
                ],
            ],
        ]);
});
