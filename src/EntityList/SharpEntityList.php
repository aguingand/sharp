<?php

namespace Code16\Sharp\EntityList;

use Code16\Sharp\EntityList\Commands\ReorderHandler;
use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\Layout\EntityListLayoutColumn;
use Code16\Sharp\EntityList\Traits\HandleEntityCommands;
use Code16\Sharp\EntityList\Traits\HandleEntityState;
use Code16\Sharp\EntityList\Traits\HandleInstanceCommands;
use Code16\Sharp\Utils\Filters\HandleFilters;
use Code16\Sharp\Utils\Transformers\WithCustomTransformers;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class SharpEntityList
{
    use HandleFilters,
        HandleEntityState,
        HandleEntityCommands,
        HandleInstanceCommands,
        WithCustomTransformers;
    
    const LAYOUT_LIST = "list";
    const LAYOUT_CARD = "card";

    protected array $containers = [];
    protected array $columns = [];
    protected bool $listBuilt = false;
    protected bool $layoutBuilt = false;
    protected string $layoutType = self::LAYOUT_LIST;
    protected array $layoutOptions = [];
    protected string $instanceIdAttribute = "id";
    protected ?string $multiformAttribute = null;
    protected bool $searchable = false;
    protected bool $paginated = false;
    protected ?ReorderHandler $reorderHandler = null;
    protected ?string $defaultSort= null;
    protected ?string $defaultSortDir = null;

    public final function dataContainers(): array
    {
        $this->checkListIsBuilt();

        return collect($this->containers)
            ->map(function(EntityListDataContainer $container) {
                return $container->toArray();
            })
            ->keyBy("key")
            ->all();
    }

    public final function listLayout(): array
    {
        if(!$this->layoutBuilt) {
            $this->buildListLayout();
            $this->layoutBuilt = true;
        }

        return array_merge([
            "type" => $this->layoutType,
            "columns" => collect($this->columns)
                ->map(function(EntityListLayoutColumn $column) {
                    return $column->toArray();
                })
                ->all()
        ], $this->layoutOptions);
    }

    public final function data($items = null): array
    {
        $this->putRetainedFilterValuesInSession();

        $items = $items ?: $this->getListData(
            EntityListQueryParams::create()
                ->setDefaultSort($this->defaultSort, $this->defaultSortDir)
                ->fillWithRequest()
                ->setDefaultFilters($this->getFilterDefaultValues())
        );

        if($items instanceof LengthAwarePaginator) {
            $page = $items->currentPage();
            $totalCount = $items->total();
            $pageSize = $items->perPage();
            $items = $items->items();
        }

        $this->addInstanceCommandsAuthorizationsToConfigForItems($items);

        $keys = $this->getDataKeys();

        return [
            "items" =>
                collect($items)
                    ->map(function($row) use($keys) {
                        // Filter model attributes on actual form fields
                        return collect($row)
                            ->only(
                                array_merge(
                                    $this->entityStateAttribute ? [$this->entityStateAttribute] : [],
                                    $this->multiformAttribute ? [$this->multiformAttribute] : [],
                                    [$this->instanceIdAttribute],
                                    $keys
                                )
                            )
                            ->all();
                    })
                    ->all()
        ] + (isset($page) ? compact('page', 'totalCount', 'pageSize') : []);
    }

    public final function listConfig(bool $hasShowPage = false): array
    {
        $config = [
            "instanceIdAttribute" => $this->instanceIdAttribute,
            "multiformAttribute" => $this->multiformAttribute,
            "searchable" => $this->searchable,
            "paginated" => $this->paginated,
            "reorderable" => !is_null($this->reorderHandler),
            "defaultSort" => $this->defaultSort,
            "defaultSortDir" => $this->defaultSortDir,
            "hasShowPage" => $hasShowPage,
        ];
        
        $this->appendFiltersToConfig($config);
        $this->appendEntityStateToConfig($config);
        $this->appendInstanceCommandsToConfig($config);
        $this->appendEntityCommandsToConfig($config);

        return $config;
    }

    public function setInstanceIdAttribute(string $instanceIdAttribute): self
    {
        $this->instanceIdAttribute = $instanceIdAttribute;

        return $this;
    }

    public function setReorderable($reorderHandler): self
    {
        $this->reorderHandler = $reorderHandler instanceof ReorderHandler
            ? $reorderHandler
            : app($reorderHandler);

        return $this;
    }

    public function setNotReorderable(): self
    {
        $this->reorderHandler = null;

        return $this;
    }

    public function setSearchable(bool $searchable = true): self
    {
        $this->searchable = $searchable;

        return $this;
    }

    public function setDefaultSort(string $sortBy, string $sortDir = "asc"): self
    {
        $this->defaultSort = $sortBy;
        $this->defaultSortDir = $sortDir;

        return $this;
    }

    public function setPaginated(bool $paginated = true): self
    {
        $this->paginated = $paginated;

        return $this;
    }

    protected function setMultiformAttribute(string $attribute): self
    {
        $this->multiformAttribute = $attribute;

        return $this;
    }

    public function reorderHandler(): ?ReorderHandler
    {
        return $this->reorderHandler;
    }

    protected function addDataContainer(EntityListDataContainer $container): self
    {
        $this->containers[] = $container;
        $this->listBuilt = false;

        return $this;
    }
    
    protected function setLayoutTypeCard(?int $cardsPerRow, ?string $cardWidth = null): self
    {
        $this->layoutBuilt = false;
        
        $this->layoutType = self::LAYOUT_CARD;
        $this->layoutOptions = [
            'itemsPerRow' => $cardsPerRow,
            'itemWidth' => $cardWidth,
        ];
        
        return $this;
    }

    protected function addColumn(string $label, int $size, $sizeXS = null): self
    {
        $this->layoutBuilt = false;

        $this->columns[] = new EntityListLayoutColumn($label, $size, $sizeXS);

        return $this;
    }

    protected function addColumnLarge(string $label, int $size): self
    {
        $this->layoutBuilt = false;

        $column = new EntityListLayoutColumn($label, $size);
        $column->setLargeOnly(true);
        $this->columns[] = $column;

        return $this;
    }

    private function checkListIsBuilt(): void
    {
        if (!$this->listBuilt) {
            $this->buildListDataContainers();
            $this->listBuilt = true;
        }
    }

    protected final function getDataKeys(): array
    {
        return collect($this->dataContainers())
            ->pluck("key")
            ->all();
    }

    /**
     * Retrieve all rows data as array.
     *
     * @param EntityListQueryParams $params
     * @return array|Arrayable
     */
    abstract function getListData(EntityListQueryParams $params);

    /**
     * Build list containers using ->addDataContainer()
     *
     * @return void
     */
    abstract function buildListDataContainers(): void;

    /**
     * Build list layout using ->addColumn()
     *
     * @return void
     */
    abstract function buildListLayout(): void;

    /**
     * Build list config
     *
     * @return void
     */
    abstract function buildListConfig(): void;
}
