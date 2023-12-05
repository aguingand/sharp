<?php

namespace Code16\Sharp\Show\Layout;

use Code16\Sharp\Form\Layout\HasLayout;
use Illuminate\Support\Traits\Conditionable;

class ShowLayoutSection implements HasLayout
{
    use Conditionable;

    protected ?string $title = null;
    protected array $columns = [];
    protected bool $collapsable = false;
    protected ?string $sectionKey = null;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function addColumn(int $size, \Closure $callback = null): self
    {
        $column = $this->addColumnLayout(new ShowLayoutColumn($size));

        if ($callback) {
            $callback($column);
        }

        return $this;
    }

    public function setCollapsable(bool $collapsable = true): self
    {
        $this->collapsable = $collapsable;

        return $this;
    }

    public function setKey(string $key): self
    {
        $this->sectionKey = $key;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'key' => $this->sectionKey,
            'title' => $this->title,
            'collapsable' => $this->collapsable,
            'columns' => collect($this->columns)
                ->map(function ($column) {
                    return $column->toArray();
                })
                ->all(),
        ];
    }

    public function addColumnLayout(ShowLayoutColumn $column): ShowLayoutColumn
    {
        $this->columns[] = $column;

        return $column;
    }
}
