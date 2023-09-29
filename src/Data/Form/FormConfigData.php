<?php

namespace Code16\Sharp\Data\Form;


use Code16\Sharp\Data\Data;
use Spatie\TypeScriptTransformer\Attributes\Optional;

final class FormConfigData extends Data
{
    public function __construct(
        public bool $hasShowPage,
        public bool $isSingle = false,
        public ?string $breadcrumbAttribute = null,
    ) {
    }

    public static function from(array $config): self
    {
        return new self(...$config);
    }
}
