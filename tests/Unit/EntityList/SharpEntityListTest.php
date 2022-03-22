<?php

namespace Code16\Sharp\Tests\Unit\EntityList;

use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Tests\Unit\EntityList\Utils\SharpEntityDefaultTestList;
use Illuminate\Pagination\LengthAwarePaginator;

class SharpEntityListTest extends SharpTestCase
{
    /** @test */
    public function we_can_get_containers()
    {
        $list = new class() extends SharpEntityDefaultTestList
        {
            public function buildListDataContainers(): void
            {
                $this->addDataContainer(
                    EntityListDataContainer::make('name')
                        ->setLabel('Name'),
                );
            }
        };

        $this->assertEquals(['name' => [
            'key' => 'name',
            'label' => 'Name',
            'sortable' => false,
            'html' => true,
        ]], $list->dataContainers());
    }

    /** @test */
    public function we_can_get_layout()
    {
        $list = new class() extends SharpEntityDefaultTestList
        {
            public function buildListDataContainers(): void
            {
                $this->addDataContainer(
                    EntityListDataContainer::make('name'),
                )->addDataContainer(
                    EntityListDataContainer::make('age'),
                );
            }

            public function buildListLayout(): void
            {
                $this->addColumn('name', 6, 12)
                    ->addColumnLarge('age', 6);
            }
        };

        $this->assertEquals([
            [
                'key' => 'name', 'size' => 6, 'sizeXS' => 12, 'hideOnXS' => false,
            ], [
                'key' => 'age', 'size' => 6, 'sizeXS' => 6, 'hideOnXS' => true,
            ],
        ], $list->listLayout());
    }

    /** @test */
    public function we_can_get_list_data()
    {
        $form = new class() extends SharpEntityDefaultTestList
        {
            public function getListData(EntityListQueryParams $params): array
            {
                return [
                    ['name' => 'John Wayne', 'age' => 22, 'job' => 'actor'],
                    ['name' => 'Mary Wayne', 'age' => 26, 'job' => 'truck driver'],
                ];
            }

            public function buildListDataContainers(): void
            {
                $this->addDataContainer(
                    EntityListDataContainer::make('name'),
                )->addDataContainer(
                    EntityListDataContainer::make('age'),
                );
            }
        };

        $this->assertEquals([
            'items' => [
                ['name' => 'John Wayne', 'age' => 22],
                ['name' => 'Mary Wayne', 'age' => 26],
            ],
        ], $form->data());
    }

    /** @test */
    public function we_can_get_paginated_list_data()
    {
        $form = new class() extends SharpEntityDefaultTestList
        {
            public function getListData(EntityListQueryParams $params)
            {
                $data = [
                    ['name' => 'John Wayne', 'age' => 22, 'job' => 'actor'],
                    ['name' => 'Mary Wayne', 'age' => 26, 'job' => 'truck driver'],
                ];

                return new LengthAwarePaginator($data, 10, 2, 1);
            }

            public function buildListDataContainers(): void
            {
                $this->addDataContainer(
                    EntityListDataContainer::make('name'),
                )->addDataContainer(
                    EntityListDataContainer::make('age'),
                );
            }
        };

        $this->assertEquals([
            'items' => [
                ['name' => 'John Wayne', 'age' => 22],
                ['name' => 'Mary Wayne', 'age' => 26],
            ], 'page' => 1, 'pageSize' => 2, 'totalCount' => 10,
        ], $form->data());
    }

    /** @test */
    public function we_can_get_list_config()
    {
        $list = new class() extends SharpEntityDefaultTestList
        {
            public function buildListConfig(): void
            {
                $this->setSearchable()
                    ->setPaginated();
            }
        };

        $list->buildListConfig();

        $this->assertEquals([
            'searchable' => true,
            'paginated' => true,
            'reorderable' => false,
            'hasShowPage' => false,
            'instanceIdAttribute' => 'id',
            'multiformAttribute' => null,
            'defaultSort' => null,
            'defaultSortDir' => null,
        ], $list->listConfig());
    }
}
