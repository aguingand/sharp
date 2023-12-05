<?php

namespace Code16\Sharp\Tests\Fixtures\Sharp;

use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class PersonForm extends SharpForm
{
    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields->addField(SharpFormTextField::make('name'));
    }

    public function buildFormLayout(FormLayout $formLayout): void
    {
        $formLayout->addColumn(6, function (FormLayoutColumn $column) {
            return $column->withField('name');
        });
    }

    public function find($id): array
    {
        return ['name' => 'John Wayne', 'job' => 'actor'];
    }

    public function update($id, array $data)
    {
        return 1;
    }
}
