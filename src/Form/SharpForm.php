<?php

namespace Code16\Sharp\Form;

use Code16\Sharp\Exceptions\Form\SharpFormUpdateException;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\Layout\FormLayoutTab;
use Code16\Sharp\Utils\SharpNotification;
use Code16\Sharp\Utils\Traits\HandleCustomBreadcrumb;
use Code16\Sharp\Utils\Transformers\WithCustomTransformers;

abstract class SharpForm
{
    use WithCustomTransformers;
    use HandleFormFields;
    use HandleCustomBreadcrumb;

    protected array $tabs = [];
    protected bool $displayShowPageAfterCreation = false;
    protected bool $tabbed = true;
    protected bool $layoutBuilt = false;

    /**
     * Return the form fields layout.
     */
    public function formLayout(): array
    {
        if (!$this->layoutBuilt) {
            $this->buildFormLayout();
            $this->layoutBuilt = true;
        }

        return [
            'tabbed' => $this->tabbed,
            'tabs'   => collect($this->tabs)
                ->map->toArray()
                ->all(),
        ];
    }

    /**
     * Return the entity instance, as an array.
     */
    public function instance($id): array
    {
        return collect($this->find($id))
            // Filter model attributes on actual form fields
            ->only(
                array_merge(
                    $this->breadcrumbAttribute ? [$this->breadcrumbAttribute] : [],
                    $this->getDataKeys()
                )
            )
            ->all();
    }

    public function newInstance(): ?array
    {
        $data = collect($this->create())
            // Filter model attributes on actual form fields
            ->only(
                array_merge(
                    $this->breadcrumbAttribute ? [$this->breadcrumbAttribute] : [],
                    $this->getDataKeys()
                )
            )
            ->all();

        return sizeof($data) ? $data : null;
    }

    public function hasDataLocalizations(): bool
    {
        foreach ($this->fields() as $field) {
            if ($field['localized'] ?? false) {
                return true;
            }

            if ($field['type'] === 'list') {
                foreach ($field['itemFields'] as $itemField) {
                    if ($itemField['localized'] ?? false) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function getDataLocalizations(): array
    {
        return [];
    }

    public function buildFormConfig(): void
    {
    }

    public function formConfig(): array
    {
        return tap(
            [
                'hasShowPage' => $this->displayShowPageAfterCreation,
            ],
            function (&$config) {
                $this->appendBreadcrumbCustomLabelAttribute($config);
            }
        );
    }

    protected function setDisplayShowPageAfterCreation(bool $displayShowPage = true): self
    {
        $this->displayShowPageAfterCreation = $displayShowPage;

        return $this;
    }

    public function isDisplayShowPageAfterCreation(): bool
    {
        return $this->displayShowPageAfterCreation;
    }

    protected function addTab(string $label, \Closure $callback = null): self
    {
        $this->layoutBuilt = false;

        $tab = $this->addTabLayout(new FormLayoutTab($label));

        if ($callback) {
            $callback($tab);
        }

        return $this;
    }

    protected function addColumn(int $size, \Closure $callback = null): self
    {
        $this->layoutBuilt = false;

        $column = $this->getLonelyTab()->addColumnLayout(
            new FormLayoutColumn($size)
        );

        if ($callback) {
            $callback($column);
        }

        return $this;
    }

    protected function setTabbed(bool $tabbed = true): self
    {
        $this->tabbed = $tabbed;

        return $this;
    }

    private function addTabLayout(FormLayoutTab $tab): FormLayoutTab
    {
        $this->tabs[] = $tab;

        return $tab;
    }

    private function getLonelyTab(): FormLayoutTab
    {
        if (!sizeof($this->tabs)) {
            $this->addTabLayout(new FormLayoutTab('one'));
        }

        return $this->tabs[0];
    }

    public function updateInstance($id, $data)
    {
        list($formattedData, $delayedData) = $this->formatRequestData($data, $id, true);

        $id = $this->update($id, $formattedData);

        if ($delayedData) {
            // Some formatters asked to delay their handling after a first pass.
            // Typically, this is used if the formatter needs the id of the
            // instance: in a creation case, we must store it first.
            if (!$id) {
                throw new SharpFormUpdateException(
                    sprintf('The update method of [%s] must return the instance id', basename(get_class($this)))
                );
            }

            $this->update($id, $this->formatRequestData($delayedData, $id, false));
        }

        return $id;
    }

    public function storeInstance($data)
    {
        return $this->updateInstance(null, $data);
    }

    /**
     * Pack new Model data as JSON.
     *
     * @return array
     */
    public function create(): array
    {
        $attributes = collect($this->getDataKeys())
            ->flip()
            ->map(function () {
                return null;
            })->all();

        // Build a fake Model class based on attributes
        return $this->transform(new class($attributes) extends \stdClass {
            public function __construct($attributes)
            {
                $this->attributes = $attributes;

                foreach ($attributes as $name => $value) {
                    $this->$name = $value;
                }
            }

            public function toArray()
            {
                return $this->attributes;
            }
        });
    }

    /**
     * Display a notification next time entity list is shown.
     *
     * @param string $title
     *
     * @return SharpNotification
     */
    public function notify(string $title): SharpNotification
    {
        return new SharpNotification($title);
    }

    /**
     * Retrieve a Model for the form and pack all its data as JSON.
     *
     * @param $id
     *
     * @return array
     */
    abstract public function find($id): array;

    /**
     * @param $id
     * @param array $data
     *
     * @return mixed the instance id
     */
    abstract public function update($id, array $data);

    /**
     * @param $id
     */
    abstract public function delete($id): void;

    /**
     * Build form fields using ->addField().
     *
     * @return void
     */
    abstract public function buildFormFields(): void;

    /**
     * Build form layout using ->addTab() or ->addColumn().
     *
     * @return void
     */
    abstract public function buildFormLayout(): void;
}
