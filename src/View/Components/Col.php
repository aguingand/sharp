<?php

namespace Code16\Sharp\View\Components;

use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\Layout\FormLayoutFieldset;
use Code16\Sharp\Form\Layout\FormLayoutTab;
use Code16\Sharp\Form\Layout\HasFieldRows;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Utils\Layout\LayoutColumn;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class Col extends Component
{
    public self $colComponent;
    public ?LayoutColumn $column = null;
    /**
     * @var HasFieldRows
     */
    protected $rowsContainer = null;
    protected ?self $parentColComponent = null;
    protected ?SharpForm $form;
    protected ?FormLayoutTab $tab;
    protected ?FormLayoutFieldset $fieldset;

    public function __construct(
        public ?int $size = null,
        public ?int $sizeXs = null,
        ?string $class = null,
    ) {
        $this->colComponent = $this;
        $this->parentColComponent = view()->getConsumableComponentData('colComponent');
        $this->tab = view()->getConsumableComponentData('tab');
        $this->form = view()->getConsumableComponentData('form');
        $this->fieldset = view()->getConsumableComponentData('fieldset');
        if($class) {
            $this->sizeXs ??= (int)Str::match("/col-(\d+)/", $class) ?: null;
            $this->size ??= (int)Str::match("/col-md-(\d+)/", $class) ?: $this->sizeXs;
        }
        
        if ($this->fieldset) {
            $this->rowsContainer = $this->fieldset;
        } elseif($this->parentColComponent) {
            $this->column = $this->parentColComponent->column;
            $this->rowsContainer = $this->parentColComponent->column;
        } elseif($this->tab) {
            $this->tab->addColumn($this->size, function (FormLayoutColumn $column) {
                $this->column = $column;
            });
        } elseif($this->form) {
            $this->form->formLayoutInstance()->addColumn($this->size, function (FormLayoutColumn $column) {
                $this->column = $column;
            });
        }
    }
    
    public function addField(string $fieldKey)
    {
        if ($this->rowsContainer) {
            $this->rowsContainer->appendLastRowField($this->fieldLayoutKey($fieldKey));
        } else {
            $this->column?->withSingleField($fieldKey);
        }
    }
    
    private function fieldLayoutKey(string $fieldKey): string
    {
        if($this->size) {
            $fieldKey .= "|$this->size";
            if($this->sizeXs) {
                $fieldKey .= ",$this->sizeXs";
            }
        }
        return $fieldKey;
    }
    
    public function render(): callable
    {
        return function () {};
    }
}
