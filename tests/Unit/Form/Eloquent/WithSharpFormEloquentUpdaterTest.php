<?php

use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Form\Fields\SharpFormTagsField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Unit\Form\Fakes\FakeSharpForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;

it('allows to update a simple attribute', function () {
    $person = Person::create(['name' => 'Marie Curry']);

    $form = new class extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'));
        }

        public function update($id, array $data)
        {
            return $this->save(Person::findOrFail($id), $data);
        }
    };

    $form->updateInstance($person->id, ['name' => 'Marie Curie']);

    expect($person->fresh()->name)->toBe('Marie Curie');
})->group('eloquent');

it('allows to store a new instance', function () {
    $form = new class extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'));
        }

        public function update($id, array $data)
        {
            return $this->save(new Person(), $data);
        }
    };

    $form->storeInstance(['name' => 'Niehls Bohr']);

    $this->assertDatabaseHas('people', [
        'name' => 'Niehls Bohr',
    ]);
});

it('undeclared fields are ignored', function () {
    $person = Person::create(['name' => 'Marie Curie', 'age' => 21]);

    $form = new class extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'));
        }

        public function update($id, array $data)
        {
            return $this->save(Person::findOrFail($id), $data);
        }
    };

    $form->updateInstance($person->id, ['id' => 1200, 'age' => 38]);

    $this->assertDatabaseHas('people', [
        'id' => $person->id,
        'name' => 'Marie Curie',
        'age' => 21,
    ]);
});

it('allows to manually ignore a field', function () {
    $person = Person::create(['name' => 'Niels Bohr', 'age' => 21]);

    $form = new class extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'))
                ->addField(SharpFormTextField::make('age'));
        }

        public function update($id, array $data)
        {
            return $this
                ->ignore('age')
                ->save(Person::findOrFail($id), $data);
        }
    };

    $form->updateInstance($person->id, ['name' => 'Marie Curie', 'age' => 40]);

    $this->assertDatabaseHas('people', [
        'id' => $person->id,
        'name' => 'Marie Curie',
        'age' => 21,
    ]);
});

it('allows to manually ignore multiple field', function () {
    $person = Person::create(['name' => 'Niels Bohr', 'age' => 21]);

    $form = new class extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'))
                ->addField(SharpFormTextField::make('age'));
        }

        public function update($id, array $data)
        {
            return $this
                ->ignore(['age', 'name'])
                ->save(Person::findOrFail($id), $data);
        }
    };

    $form->updateInstance($person->id, ['name' => 'Marie Curie', 'age' => 40]);

    $this->assertDatabaseHas('people', [
        'id' => $person->id,
        'name' => 'Niels Bohr',
        'age' => 21,
    ]);
});
//
//it('allows to update a belongsTo attribute', function () {
//    $mother = Person::create(['name' => 'Jane Wayne']);
//    $person = Person::create(['name' => 'John Wayne']);
//
//    $form = new class extends FakeSharpForm
//    {
//        public function buildFormFields(FieldsContainer $formFields): void
//        {
//            $formFields->addField(SharpFormSelectField::make('mother', Person::all()->pluck('name', 'id')->all()));
//        }
//    };
//
//    $form->updateInstance($person->id, ['mother' => $mother->id]);
//
//    $this->assertDatabaseHas('people', [
//        'id' => $person->id,
//        'mother_id' => $mother->id,
//    ]);
//});
//
//it('allows to update a hasOne attribute', function () {
//    $mother = Person::create(['name' => 'Jane Wayne']);
//    $son = Person::create(['name' => 'John Wayne']);
//
//    $form = new class extends FakeSharpForm
//    {
//        public function buildFormFields(FieldsContainer $formFields): void
//        {
//            $formFields->addField(SharpFormSelectField::make(
//                'elderSon', Person::all()->pluck('name', 'id')->all()),
//            );
//        }
//    };
//
//    $form->updateInstance($mother->id, ['elderSon' => $son->id]);
//
//    $this->assertDatabaseHas('people', [
//        'id' => $son->id,
//        'mother_id' => $mother->id,
//    ]);
//});
//
//it('allows to update a hasMany attribute', function () {
//    $mother = Person::create(['name' => 'Jane Wayne']);
//    $son = Person::create(['name' => 'AAA', 'mother_id' => $mother->id]);
//
//    $form = new class extends FakeSharpForm
//    {
//        public function buildFormFields(FieldsContainer $formFields): void
//        {
//            $formFields->addField(
//                SharpFormListField::make('sons')
//                    ->addItemField(SharpFormTextField::make('name')),
//            );
//        }
//    };
//
//    $form->updateInstance($mother->id, [
//        'sons' => [
//            ['id' => $son->id, 'name' => 'John Wayne'],
//            ['id' => null, 'name' => 'Mary Wayne'],
//        ],
//    ]);
//
//    $this->assertDatabaseHas('people', [
//        'id' => $son->id,
//        'mother_id' => $mother->id,
//        'name' => 'John Wayne',
//    ]);
//
//    $this->assertDatabaseHas('people', [
//        'mother_id' => $mother->id,
//        'name' => 'Mary Wayne',
//    ]);
//});
//
//it('allows to update a belongsToMany attribute', function () {
//    $person1 = Person::create(['name' => 'John Ford']);
//    $person2 = Person::create(['name' => 'John Wayne']);
//
//    $form = new class extends FakeSharpForm
//    {
//        public function buildFormFields(FieldsContainer $formFields): void
//        {
//            $formFields
//                ->addField(
//                    SharpFormTagsField::make('friends', Person::all()->pluck('name', 'id')->all())
//                        ->setCreatable(),
//                );
//        }
//    };
//
//    $form->updateInstance($person1->id, [
//        'friends' => [
//            ['id' => $person2->id],
//        ],
//    ]);
//
//    $this->assertDatabaseHas('friends', [
//        'person1_id' => $person1->id,
//        'person2_id' => $person2->id,
//    ]);
//});
//
//it('allows to create a new related in a belongsToMany attribute', function () {
//    $person1 = Person::create(['name' => 'John Ford']);
//
//    $form = new class extends FakeSharpForm
//    {
//        public function buildFormFields(FieldsContainer $formFields): void
//        {
//            $formFields->addField(
//                SharpFormTagsField::make('friends', Person::all()->pluck('name', 'id')->all())
//                    ->setCreatable()
//                    ->setCreateAttribute('name'),
//            );
//        }
//    };
//
//    $form->updateInstance($person1->id, [
//        'friends' => [
//            ['id' => null, 'label' => 'John Wayne'],
//        ],
//    ]);
//
//    $this->assertDatabaseHas('people', [
//        'name' => 'John Wayne',
//    ]);
//
//    $person2 = Person::where('name', 'John Wayne')->first();
//
//    $this->assertDatabaseHas('friends', [
//        'person1_id' => $person1->id,
//        'person2_id' => $person2->id,
//    ]);
//});
//
//it('handles the order attribute in a hasMany relation in a creation case', function () {
//    $mother = Person::create(['name' => 'Jane Wayne']);
//
//    $form = new class extends FakeSharpForm
//    {
//        public function buildFormFields(FieldsContainer $formFields): void
//        {
//            $formFields->addField(
//                SharpFormListField::make('sons')
//                    ->addItemField(SharpFormTextField::make('name'))
//                    ->setSortable()->setOrderAttribute('order'),
//            );
//        }
//    };
//
//    $form->updateInstance($mother->id, [
//        'sons' => [
//            ['id' => null, 'name' => 'John Wayne'],
//            ['id' => null, 'name' => 'Mary Wayne'],
//        ],
//    ]);
//
//    $this->assertDatabaseHas('people', [
//        'mother_id' => $mother->id,
//        'name' => 'John Wayne',
//        'order' => 1,
//    ]);
//
//    $this->assertDatabaseHas('people', [
//        'mother_id' => $mother->id,
//        'name' => 'Mary Wayne',
//        'order' => 2,
//    ]);
//});
//
//it('handles the order attribute in a hasMany relation in an update case', function () {
//    $mother = Person::create(['name' => 'A']);
//    $son = Person::create(['name' => 'B', 'order' => 30, 'mother_id' => $mother->id]);
//    $daughter = Person::create(['name' => 'C', 'order' => 50, 'mother_id' => $mother->id]);
//
//    $form = new class extends FakeSharpForm
//    {
//        public function buildFormFields(FieldsContainer $formFields): void
//        {
//            $formFields->addField(
//                SharpFormListField::make('sons')
//                    ->addItemField(SharpFormTextField::make('name'))
//                    ->setSortable()->setOrderAttribute('order'),
//            );
//        }
//    };
//
//    $form->updateInstance($mother->id, [
//        'sons' => [
//            ['id' => $daughter->id, 'name' => 'C'],
//            ['id' => $son->id, 'name' => 'B'],
//        ],
//    ]);
//
//    $this->assertDatabaseHas('people', [
//        'id' => $daughter->id,
//        'order' => 1,
//    ]);
//
//    $this->assertDatabaseHas('people', [
//        'id' => $son->id,
//        'order' => 2,
//    ]);
//});
//
//it('allows to update a morphOne attribute', function () {
//    $person = Person::create(['name' => 'John Wayne']);
//
//    $form = new class extends FakeSharpForm
//    {
//        public function buildFormFields(FieldsContainer $formFields): void
//        {
//            $formFields->addField(SharpFormTextField::make('picture:file'));
//        }
//    };
//
//    $form->updateInstance($person->id, ['picture:file' => 'picture']);
//
//    $this->assertDatabaseHas('pictures', [
//        'picturable_type' => Person::class,
//        'picturable_id' => $person->id,
//        'file' => 'picture',
//    ]);
//});
//
//it('handles the relation separator in a belongsTo case', function () {
//    $mother = Person::create(['name' => 'AAA']);
//    $son = Person::create(['name' => 'John Wayne', 'mother_id' => $mother->id]);
//
//    $form = new class extends FakeSharpForm
//    {
//        public function buildFormFields(FieldsContainer $formFields): void
//        {
//            $formFields->addField(SharpFormTextField::make('mother:name'))
//                ->addField(SharpFormTextField::make('mother:age'));
//        }
//    };
//
//    $form->updateInstance($son->id, [
//        'mother:name' => 'Jane Wayne',
//        'mother:age' => 92,
//    ]);
//
//    $this->assertDatabaseHas('people', [
//        'id' => $mother->id,
//        'age' => 92,
//        'name' => 'Jane Wayne',
//    ]);
//});
//
//it('handles the relation separator in a hasOne case', function () {
//    $mother = Person::create(['name' => 'Jane Wayne']);
//    $son = Person::create(['name' => 'AAA', 'mother_id' => $mother->id]);
//
//    $form = new class extends FakeSharpForm
//    {
//        public function buildFormFields(FieldsContainer $formFields): void
//        {
//            $formFields->addField(SharpFormTextField::make('elderSon:name'))
//                ->addField(SharpFormTextField::make('elderSon:age'));
//        }
//    };
//
//    $form->updateInstance($mother->id, [
//        'elderSon:name' => 'John Wayne',
//        'elderSon:age' => 52,
//    ]);
//
//    $this->assertDatabaseHas('people', [
//        'id' => $son->id,
//        'name' => 'John Wayne',
//        'age' => 52,
//        'mother_id' => $mother->id,
//    ]);
//});
//
//it('handles the relation separator in a hasOne creation case', function () {
//    $mother = Person::create(['name' => 'Jane Wayne']);
//
//    $form = new class extends FakeSharpForm
//    {
//        public function buildFormFields(FieldsContainer $formFields): void
//        {
//            $formFields->addField(SharpFormTextField::make('elderSon:name'))
//                ->addField(SharpFormTextField::make('elderSon:age'));
//        }
//    };
//
//    $form->updateInstance($mother->id, [
//        'elderSon:name' => 'John Wayne',
//        'elderSon:age' => 52,
//    ]);
//
//    $this->assertDatabaseHas('people', [
//        'name' => 'John Wayne',
//        'age' => 52,
//        'mother_id' => $mother->id,
//    ]);
//});