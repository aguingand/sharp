<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Illuminate\Contracts\Support\Arrayable;

class MultiFormEntityListControllerTest extends BaseApiTestCase
{
    /** @test */
    public function we_get_the_forms_attributes_on_a_multiform_entity()
    {
        $this->withoutExceptionHandling();
        $this->login();
        $this->buildTheWorld();

        app(SharpEntityManager::class)
            ->entityFor('person')
            ->setList(PersonWithMultiformSharpEntityList::class)
            ->setMultiforms([
                'big' => [BigPersonSharpForm::class, 'Big person'],
                'small' => [SmallPersonSharpForm::class, 'Small person'],
            ]);

        $this->json('get', '/sharp/api/list/person')
            ->assertStatus(200)
            ->assertJson(['forms' => [
                'big' => [
                    'label' => 'Big person',
                    'instances' => [2],
                ],
                'small' => [
                    'label' => 'Small person',
                    'instances' => [1],
                ],
            ]]);
    }
}

class PersonWithMultiformSharpEntityList extends SharpEntityList
{
    public function getListData(): array|Arrayable
    {
        return $this
            ->setCustomTransformer('type', function ($a, $person) {
                return $person['id'] % 2 == 0 ? 'big' : 'small';
            })
            ->transform([
                ['id' => 1, 'name' => 'John Wayne'],
                ['id' => 2, 'name' => 'Mary Wayne'],
            ]);
    }

    public function buildList(EntityListFieldsContainer $fields): void
    {
        $fields
            ->addField(
                EntityListField::make('name'),
            );
    }

    public function buildListConfig(): void
    {
        $this->configureMultiformAttribute('type');
    }
}
