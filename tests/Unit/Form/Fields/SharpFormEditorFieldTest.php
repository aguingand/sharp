<?php

use Code16\Sharp\Exceptions\SharpInvalidConfigException;
use Code16\Sharp\Form\Fields\Editor\Uploads\SharpFormEditorUpload;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Tests\Unit\Form\Fields\Fakes\FakeSharpEditorEmbed;

it('sets only default values', function () {
    $formField = SharpFormEditorField::make('text');

    expect($formField->toArray())
        ->toEqual([
            'key' => 'text',
            'type' => 'editor',
            'minHeight' => 200,
            'showCharacterCount' => false,
            'toolbar' => [
                SharpFormEditorField::B,
                SharpFormEditorField::I,
                SharpFormEditorField::SEPARATOR,
                SharpFormEditorField::UL,
                SharpFormEditorField::SEPARATOR,
                SharpFormEditorField::A,
            ],
            'uploads' => [],
            'embeds' => [],
            'markdown' => false,
            'inline' => false,
        ]);
});

it('allows to define height', function () {
    $formField = SharpFormEditorField::make('text')
        ->setHeight(50);

    expect($formField->toArray())
        ->toHaveKey('minHeight', 50)
        ->toHaveKey('maxHeight', 50);
});

it('allows to define height with maxHeight', function () {
    $formField = SharpFormEditorField::make('text')
        ->setHeight(50, 100);

    expect($formField->toArray())
        ->toHaveKey('minHeight', 50)
        ->toHaveKey('maxHeight', 100)
        ->and($formField->setHeight(50, 0)->toArray())
        ->toHaveKey('minHeight', 50)
        ->not->toHaveKey('maxHeight');
});

it('allows to allow uploads with configuration', function () {
    $formField = SharpFormEditorField::make('text')
        ->allowUploads(function (SharpFormEditorUpload $upload) {
            $upload->setFileFilterImages()
                ->setMaxFileSize(50)
                ->setCropRatio('16:9')
                ->setFileFilter(['jpg', 'pdf'])
                ->setHasLegend();
        });

    expect($formField->toArray())
        ->toHaveKey('uploads.fields.file.maxFileSize', 50)
        ->toHaveKey('uploads.fields.file.transformable', true)
        ->toHaveKey('uploads.fields.file.ratioX', 16)
        ->toHaveKey('uploads.fields.file.ratioY', 9)
        ->toHaveKey('uploads.fields.file.fileFilter', ['.jpg', '.pdf'])
        ->toHaveKey('uploads.fields.legend');

    $formField = SharpFormEditorField::make('text')
        ->allowUploads(function (SharpFormEditorUpload $upload) {
            $upload->setFileFilterImages()
                ->setTransformable(false);
        });

    expect($formField->toArray())
        ->toHaveKey('uploads.fields.file.transformable', false);
});

it('allows to define toolbar', function () {
    $formField = SharpFormEditorField::make('text')
        ->setToolbar([
            SharpFormEditorField::TABLE,
            SharpFormEditorField::SEPARATOR,
            SharpFormEditorField::UL,
        ]);

    expect($formField->toArray())
        ->toHaveKey('toolbar', [
            SharpFormEditorField::TABLE,
            SharpFormEditorField::SEPARATOR,
            SharpFormEditorField::UL,
        ]);
});

it('allows to hide toolbar', function () {
    $formField = SharpFormEditorField::make('text')
        ->setHeight(50)
        ->hideToolbar();

    expect($formField->toArray())
        ->not->toHaveKey('toolbar');
});

it('allows to define markdown as content renderer', function () {
    $formField = SharpFormEditorField::make('text')
        ->setHeight(50)
        ->setRenderContentAsMarkdown();

    expect($formField->toArray())
        ->toHaveKey('markdown', true);
});

it('allows to define setWithoutParagraphs', function () {
    $formField = SharpFormEditorField::make('text')
        ->setWithoutParagraphs();

    expect($formField->toArray())
        ->toHaveKey('inline', true);
});

it('allows to define maxLength and showCount', function () {
    expect(SharpFormEditorField::make('text')->setMaxLength(500)->toArray())
        ->toHaveKey('maxLength', 500)
        ->toHaveKey('showCharacterCount', true)
        ->and(SharpFormEditorField::make('text')->showCharacterCount()->toArray())
        ->toHaveKey('showCharacterCount', true);
});

it('throws an exception when setting an UPLOAD item in the toolbar without defining allowUploads', function () {
    $formField = SharpFormEditorField::make('text')
        ->setToolbar([
            SharpFormEditorField::UPLOAD,
        ]);

    $formField->toArray();
})->expectException(SharpInvalidConfigException::class);

it('allows to allows embeds', function () {
    $formField = SharpFormEditorField::make('text')
        ->allowEmbeds([
            FakeSharpEditorEmbed::class,
        ])
        ->setToolbar([
            SharpFormEditorField::H1,
            FakeSharpEditorEmbed::class
        ]);

    expect($formField->toArray()['embeds'])
        ->toHaveKey(app(FakeSharpEditorEmbed::class)->key());
});

it('allows to place an allowed embed in the toolbar', function () {
    $formField = SharpFormEditorField::make('text')
        ->allowEmbeds([
            FakeSharpEditorEmbed::class,
        ])
        ->setToolbar([
            SharpFormEditorField::H1,
            FakeSharpEditorEmbed::class
        ]);

    expect($formField->toArray()['toolbar'][1])
        ->toEqual('embed:'.app(FakeSharpEditorEmbed::class)->key());
});

it('throws an exception when setting an embed item in the toolbar without allowing it', function () {
    $formField = SharpFormEditorField::make('text')
        ->setToolbar([
            FakeSharpEditorEmbed::class
        ]);

    $formField->toArray();
})->expectException(SharpInvalidConfigException::class);