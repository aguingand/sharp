<?php

namespace Code16\Sharp\Tests\Fixtures;

use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpSingleShow;

class PersonSharpSingleShow extends SharpSingleShow
{
    public function buildShowFields(): void
    {
        $this->addField(SharpShowTextField::make('name'));
    }

    public function buildShowLayout(): void
    {
        $this
            ->addSection('Identity', function (ShowLayoutSection $section) {
                $section
                    ->addColumn(6, function (ShowLayoutColumn $column) {
                        $column->withSingleField('name');
                    });
            });
    }

    public function findSingle(): array
    {
        return ['name' => 'John Wayne', 'job' => 'actor', 'state' => 'active'];
    }
}
