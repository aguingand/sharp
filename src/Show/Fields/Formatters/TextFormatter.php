<?php

namespace Code16\Sharp\Show\Fields\Formatters;

use Code16\Sharp\Show\Fields\SharpShowField;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Utils\Fields\Formatters\FormatsEditorEmbeds;
use Code16\Sharp\Utils\Fields\Formatters\FormatsEditorUploads;
use Illuminate\Support\Collection;

class TextFormatter extends SharpShowFieldFormatter
{
    use FormatsEditorUploads;
    use FormatsEditorEmbeds;
    
    /**
     * @param  SharpShowTextField  $field
     */
    public function toFront(SharpShowField $field, $value)
    {
        return collect(['text' => $value])
            ->pipeThrough([
                fn (Collection $collection) => $collection->merge(
                    $this->formatEditorUploadsToFront($field, $collection['text'])
                ),
                fn (Collection $collection) => $collection->merge(
                    $this->formatEditorEmbedsToFront($field, $collection['text'])
                ),
            ])
            ->toArray();
    }
}
