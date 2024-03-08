<?php

namespace Code16\Sharp\Tests\Unit\Show;

use Code16\Sharp\Show\Fields\SharpShowEntityListField;
use Code16\Sharp\Show\Fields\SharpShowHtmlField;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayout;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Tests\Unit\Show\Utils\BaseSharpShowTestDefault;
use Code16\Sharp\Tests\Unit\Show\Utils\BaseSharpSingleShowTestDefault;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class SharpShowTest extends SharpTestCase
{
    /** @test */
    public function we_can_add_an_entity_list_section_to_the_layout()
    {
        $sharpShow = new class extends BaseSharpShowTestDefault
        {
            public function buildShowFields(FieldsContainer $showFields): void
            {
                $showFields->addField(
                    SharpShowEntityListField::make('entityList', 'entityKey')
                        ->setLabel('Test'),
                );
            }

            public function buildShowLayout(ShowLayout $showLayout): void
            {
                $showLayout->addEntityListSection('entityList');
            }
        };

        $this->assertEquals(
            [
                'sections' => [
                    [
                        'collapsable' => false,
                        'title' => '',
                        'columns' => [
                            [
                                'size' => 12,
                                'fields' => [
                                    [
                                        [
                                            'key' => 'entityList',
                                            'size' => 12,
                                            'sizeXS' => 12,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'key' => null,
                    ],
                ],
            ],
            $sharpShow->showLayout()
        );
    }

    /** @test */
    public function we_can_declare_a_collapsable_section()
    {
        $sharpShow = new class extends BaseSharpShowTestDefault
        {
            public function buildShowFields(FieldsContainer $showFields): void
            {
                $showFields->addField(
                    SharpShowTextField::make('test')
                        ->setLabel('Test'),
                );
            }

            public function buildShowLayout(ShowLayout $showLayout): void
            {
                $showLayout->addSection('test', function (ShowLayoutSection $section) {
                    $section->setCollapsable()
                        ->addColumn(12, function (ShowLayoutColumn $column) {
                            $column->withSingleField('test');
                        });
                });
            }
        };

        $this->assertEquals(
            [
                'sections' => [
                    [
                        'collapsable' => true,
                        'title' => 'test',
                        'columns' => [
                            [
                                'size' => 12,
                                'fields' => [
                                    [
                                        [
                                            'key' => 'test',
                                            'size' => 12,
                                            'sizeXS' => 12,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'key' => null,
                    ],
                ],
            ],
            $sharpShow->showLayout()
        );
    }

    /** @test */
    public function we_can_define_a_collapsable_entity_list_section_with_a_boolean()
    {
        $sharpShow = new class extends BaseSharpShowTestDefault
        {
            public function buildShowFields(FieldsContainer $showFields): void
            {
                $showFields->addField(
                    SharpShowEntityListField::make('entityList', 'entityKey')
                        ->setLabel('Test'),
                );
            }

            public function buildShowLayout(ShowLayout $showLayout): void
            {
                $showLayout->addEntityListSection('entityList', true);
            }
        };

        $this->assertTrue($sharpShow->showLayout()['sections'][0]['collapsable']);
    }

    /** @test */
    public function we_can_define_a_custom_key_to_a_section()
    {
        $sharpShow = new class extends BaseSharpShowTestDefault
        {
            public function buildShowFields(FieldsContainer $showFields): void
            {
                $showFields->addField(
                    SharpShowTextField::make('test'),
                );
            }

            public function buildShowLayout(ShowLayout $showLayout): void
            {
                $showLayout->addSection('test', function (ShowLayoutSection $section) {
                    $section
                        ->setKey('my-section')
                        ->addColumn(12, function (ShowLayoutColumn $column) {
                            $column->withSingleField('test');
                        });
                });
            }
        };

        $this->assertEquals('my-section', $sharpShow->showLayout()['sections'][0]['key']);
    }

    /** @test */
    public function we_can_declare_a_multiformAttribute()
    {
        $sharpShow = new class extends BaseSharpShowTestDefault
        {
            public function buildShowConfig(): void
            {
                $this->configureMultiformAttribute('role');
            }
        };

        $sharpShow->buildShowConfig();

        $this->assertArraySubset(
            [
                'multiformAttribute' => 'role',
            ],
            $sharpShow->showConfig(1),
        );
    }

    /** @test */
    public function we_can_declare_a_global_message_field()
    {
        $sharpShow = new class extends BaseSharpShowTestDefault
        {
            public function buildShowConfig(): void
            {
                $this->configurePageAlert('template', static::$pageAlertLevelWarning, 'test-key');
            }
        };

        $sharpShow->buildShowConfig();

        $this->assertEquals(
            'test-key',
            $sharpShow->showConfig(1)['globalMessage']['fieldKey'],
        );

        $this->assertEquals(
            'warning',
            $sharpShow->showConfig(1)['globalMessage']['alertLevel'],
        );

        $this->assertEquals(
            SharpShowHtmlField::make('test-key')->setInlineTemplate('template')->toArray(),
            $sharpShow->fields()['test-key'],
        );
    }

    /** @test */
    public function we_can_associate_data_to_a_global_message_field()
    {
        $sharpShow = new class extends BaseSharpShowTestDefault
        {
            public function buildShowConfig(): void
            {
                $this->configurePageAlert('Hello {{name}}', null, 'test-key');
            }

            public function find($id): array
            {
                return [
                    'test-key' => [
                        'name' => 'Bob',
                    ],
                ];
            }
        };

        $sharpShow->buildShowConfig();

        $this->assertEquals(
            ['name' => 'Bob'],
            $sharpShow->instance(1)['test-key'],
        );
    }

    /** @test */
    public function we_can_declare_a_simple_page_title_field()
    {
        $sharpShow = new class extends BaseSharpShowTestDefault
        {
            public function buildShowConfig(): void
            {
                $this->configurePageTitleAttribute('title');
            }

            public function find($id): array
            {
                return [
                    'title' => 'Some title',
                ];
            }
        };

        $sharpShow->buildShowConfig();

        $this->assertEquals(
            'title',
            $sharpShow->showConfig(1)['titleAttribute'],
        );

        $this->assertEquals(
            SharpShowTextField::make('title')->toArray(),
            $sharpShow->fields()['title'],
        );

        $this->assertArrayHasKey('title', $sharpShow->instance(1));
    }

    /** @test */
    public function we_can_declare_a_localized_page_title_field()
    {
        $sharpShow = new class extends BaseSharpShowTestDefault
        {
            public function buildShowConfig(): void
            {
                $this->configurePageTitleAttribute('title', localized: true);
            }

            public function find($id): array
            {
                return [
                    'title' => ['en' => 'Some title', 'fr' => 'Un titre'],
                ];
            }
        };

        $sharpShow->buildShowConfig();

        $this->assertEquals(
            SharpShowTextField::make('title')->setLocalized()->toArray(),
            $sharpShow->fields()['title'],
        );

        $this->assertArrayHasKey('title', $sharpShow->instance(1));
        $this->assertIsArray($sharpShow->instance(1)['title']);
    }

    /** @test */
    public function single_shows_have_are_declared_in_config()
    {
        $sharpShow = new class extends BaseSharpSingleShowTestDefault {
        };

        $this->assertArraySubset(
            [
                'isSingle' => true,
            ],
            $sharpShow->showConfig(null),
        );
    }
}
