<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormField;
use Illuminate\Support\Collection;

class EditorFormatter extends SharpFieldFormatter implements FormatsAfterUpdate
{
    use HasMaybeLocalizedValue;

    /**
     * @param  SharpFormEditorField  $field
     */
    public function toFront(SharpFormField $field, $value)
    {
        return collect([
            'text' => $value,
        ])
            ->pipeThrough([
                fn (Collection $collection) => $collection->merge(
                    $this->editorUploadsFormatter()->toFront($field, $collection['text'])
                ),
                fn (Collection $collection) => $collection->merge(
                    $this->editorEmbedsFormatter()->toFront($field, $collection['text'])
                ),
            ])
            ->toArray();
    }

    /**
     * @param  SharpFormEditorField  $field
     */
    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        if ($value === null) {
            return null;
        }

        $text = $this->maybeLocalized(
            $field,
            $value['text'] ?? null,
            fn (string $content) => preg_replace('/\R/u', "\n", $content)
        );
        $text = $this->editorUploadsFormatter()->fromFront($field, $attribute, [...$value, 'text' => $text]);
        $text = $this->editorEmbedsFormatter()->fromFront($field, $attribute, [...$value, 'text' => $text]);

        return $text;
    }

    /**
     * @param  SharpFormEditorField  $field
     */
    public function afterUpdate(SharpFormField $field, string $attribute, mixed $value): ?string
    {
        $text = $value;
        $text = $this->editorUploadsFormatter()->afterUpdate($field, $attribute, $text);
        $text = $this->editorEmbedsFormatter()->afterUpdate($field, $attribute, $text);

        return $text;
    }

    protected function editorUploadsFormatter(): EditorUploadsFormatter
    {
        return (new EditorUploadsFormatter())
            ->setDataLocalizations($this->dataLocalizations ?? [])
            ->setInstanceId($this->instanceId);
    }

    protected function editorEmbedsFormatter(): EditorEmbedsFormatter
    {
        return (new EditorEmbedsFormatter())
            ->setDataLocalizations($this->dataLocalizations ?? [])
            ->setInstanceId($this->instanceId);
    }
}
