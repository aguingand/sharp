<?php

use Code16\Sharp\Enums\PageAlertLevel;
use Code16\Sharp\Form\Fields\SharpFormCheckField;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Tests\Unit\Form\Fakes\FakeSharpForm;
use Code16\Sharp\Tests\Unit\Form\Fakes\FakeSharpSingleForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\PageAlerts\PageAlert;

it('returns form fields', function () {
    $form = new class extends FakeSharpForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'));
        }
    };

    expect($form->fields())
        ->toEqual([
            'name' => [
                'key' => 'name',
                'type' => 'text',
                'inputType' => 'text',
            ],
        ]);
});

it('returns form layout', function () {
    $form = new class extends FakeSharpForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'))
                ->addField(SharpFormTextField::make('age'));
        }

        public function buildFormLayout(FormLayout $formLayout): void
        {
            $formLayout->addColumn(6, fn ($column) => $column->withField('name'))
                ->addColumn(6, fn ($column) => $column->withField('age'));
        }
    };

    expect($form->formLayout())
        ->toEqual([
            'tabbed' => true,
            'tabs' => [[
                'title' => 'one',
                'columns' => [[
                    'size' => 6,
                    'fields' => [[
                        [
                            'key' => 'name',
                            'size' => 12,
                            'sizeXS' => 12,
                        ],
                    ]],
                ], [
                    'size' => 6,
                    'fields' => [[
                        [
                            'key' => 'age',
                            'size' => 12,
                            'sizeXS' => 12,
                        ],
                    ]],
                ]],
            ]],
        ]);
});

it('gets an instance', function () {
    $form = new class extends FakeSharpForm
    {
        public function find($id): array
        {
            return [
                'name' => 'Marie Curie',
                'age' => 22,
                'job' => 'actor',
            ];
        }

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'))
                ->addField(SharpFormTextField::make('age'));
        }
    };

    expect($form->instance(1))
        ->toEqual([
            'name' => 'Marie Curie',
            'age' => 22,
        ]);
});

it('formats data in creation with the default create function', function () {
    $sharpForm = new class extends FakeSharpForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(SharpFormEditorField::make('md'))
                ->addField(SharpFormCheckField::make('check', 'text'));
        }
    };

    $this->assertEquals(
        [
            'md' => ['text' => null],
            'check' => false,
        ],
        $sharpForm->newInstance(),
    );
});

it('formats data in creation with the default create function with subclasses', function () {
    $sharpForm = new class extends FakeSharpForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(SharpFormTextField::make('name'))
                ->addField(SharpFormEditorField::make('subclass:company'));
        }
    };

    $this->assertEquals(
        [
            'name' => '',
            'subclass:company' => ['text' => null],
        ],
        $sharpForm->newInstance(),
    );
});

it('handles single forms', function () {
    $sharpForm = new FakeSharpSingleForm();

    $sharpForm->buildFormConfig();

    $this->assertEquals(
        [
            'isSingle' => true,
            'hasShowPage' => false,
        ],
        $sharpForm->formConfig(),
    );
});

it('allows to declare setDisplayShowPageAfterCreation in config', function () {
    $sharpForm = new class extends FakeSharpForm
    {
        public function buildFormConfig(): void
        {
            $this->configureDisplayShowPageAfterCreation();
        }
    };

    $sharpForm->buildFormConfig();

    $this->assertEquals(
        [
            'hasShowPage' => true,
        ],
        $sharpForm->formConfig(),
    );
});

it('allows to declare a page alert', function () {
    $sharpForm = new class extends FakeSharpForm
    {
        public function buildPageAlert(PageAlert $pageAlert): void
        {
            $pageAlert
                ->setLevelInfo()
                ->setMessage('My page alert');
        }
    };

    expect($sharpForm->pageAlert())
        ->toEqual([
            'text' => 'My page alert',
            'level' => PageAlertLevel::Info,
        ]);
});
