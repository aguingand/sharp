<?php

use Code16\Sharp\Form\Layout\FormLayoutField;

it('allows to set a key', function () {
    $formTab = new FormLayoutField('name');

    expect($formTab->toArray()['key'])
        ->toEqual('name');
});

it('allows to set a general size', function () {
    $formTab = new FormLayoutField('name|6');

    expect($formTab->toArray())
        ->toHaveKey('size', 6)
        ->toHaveKey('sizeXS', 12)
        ->toHaveKey('key', 'name');
});

it('allows to set a xs size', function () {
    $formTab = new FormLayoutField('name|6,8');

    expect($formTab->toArray())
        ->toHaveKey('size', 6)
        ->toHaveKey('sizeXS', 8)
        ->toHaveKey('key', 'name');
});

it('allows to define a sublayout for a field', function () {
    $formTab = new FormLayoutField('name', function ($item) {
        $item->withField('age')
            ->withField('size');
    });

    expect($formTab->toArray())
        ->toHaveKey('key', 'name')
        ->and($formTab->toArray()['item'])
        ->toEqual([[
            ['key' => 'age', 'size' => 12, 'sizeXS' => 12],
        ], [
            ['key' => 'size', 'size' => 12, 'sizeXS' => 12],
        ]]);
});
