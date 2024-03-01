<?php

namespace Code16\Sharp\Form\Fields;

use Closure;
use Code16\Sharp\Form\Fields\Formatters\UploadFormatter;
use Code16\Sharp\Form\Fields\Utils\IsUploadField;
use Code16\Sharp\Utils\Fields\Validation\SharpFileValidation;
use Illuminate\Validation\Rules;

class SharpFormUploadField extends SharpFormField implements IsUploadField
{
    const FIELD_TYPE = 'upload';

    protected ?array $cropRatio = null;
    protected ?array $transformableFileTypes = null;
    protected string $storageDisk = 'local';
    protected string|Closure $storageBasePath = 'data';
    protected bool $transformable = true;
    protected ?bool $transformKeepOriginal = null;
    protected bool $compactThumbnail = false;
    protected bool $shouldOptimizeImage = false;
    protected ?Rules\File $validationRule = null;

    /** @deprecated */
    protected ?float $maxFileSize = null;
    /** @deprecated */
    protected string|array|null $fileFilter = null;

    public static function make(string $key): self
    {
        return new static($key, static::FIELD_TYPE, app(UploadFormatter::class));
    }

    /** @deprecated use setValidationRule() instead */
    public function setMaxFileSize(float $maxFileSizeInMB): self
    {
        $this->maxFileSize = $maxFileSizeInMB;

        return $this;
    }

    /** @deprecated */
    public function maxFileSize(): ?float
    {
        return $this->maxFileSize;
    }

    public function setValidationRule(Rules\File $validationRule): self
    {
        $this->validationRule = $validationRule;

        return $this;
    }

    public function setCropRatio(string $ratio = null, ?array $transformableFileTypes = null): self
    {
        if ($ratio) {
            $this->cropRatio = explode(':', $ratio);

            $this->transformableFileTypes = $transformableFileTypes
                ? $this->formatFileExtension($transformableFileTypes)
                : null;
        } else {
            $this->cropRatio = null;
            $this->transformableFileTypes = null;
        }

        return $this;
    }

    public function shouldOptimizeImage(bool $shouldOptimizeImage = true): self
    {
        $this->shouldOptimizeImage = $shouldOptimizeImage;

        return $this;
    }

    public function isShouldOptimizeImage(): bool
    {
        return $this->shouldOptimizeImage;
    }

    public function setCompactThumbnail(bool $compactThumbnail = true): self
    {
        $this->compactThumbnail = $compactThumbnail;

        return $this;
    }

    public function setTransformable(bool $transformable = true, ?bool $transformKeepOriginal = null): self
    {
        $this->transformable = $transformable;

        if ($transformable && ! is_null($transformKeepOriginal)) {
            $this->transformKeepOriginal = $transformKeepOriginal;
        }

        return $this;
    }

    public function isTransformable(): bool
    {
        return $this->transformable;
    }

    public function isTransformOriginal(): bool
    {
        return $this->transformable && ! $this->isTransformKeepOriginal();
    }

    public function isTransformKeepOriginal(): bool
    {
        return $this->transformKeepOriginal ?? config('sharp.uploads.transform_keep_original_image', true);
    }

    public function transformableFileTypes(): ?array
    {
        return $this->transformableFileTypes;
    }

    public function setStorageDisk(string $storageDisk): self
    {
        $this->storageDisk = $storageDisk;

        return $this;
    }

    public function setStorageBasePath(string|Closure $storageBasePath): self
    {
        $this->storageBasePath = $storageBasePath;

        return $this;
    }

    /** @deprecated use setValidationRule() instead */
    public function setFileFilter(string|array $fileFilter): self
    {
        $this->fileFilter = $this->formatFileExtension($fileFilter);

        return $this;
    }

    /** @deprecated use setValidationRule() instead */
    public function setFileFilterImages(): self
    {
        $this->setFileFilter(['.jpg', '.jpeg', '.gif', '.png']);

        return $this;
    }

    public function storageDisk(): string
    {
        return $this->storageDisk;
    }

    public function storageBasePath(): string
    {
        return value($this->storageBasePath);
    }

    public function cropRatio(): ?array
    {
        return $this->cropRatio;
    }

    /** @deprecated */
    public function fileFilter(): ?array
    {
        return $this->fileFilter;
    }

    private function formatFileExtension(string|array $fileFilter): array
    {
        if (! is_array($fileFilter)) {
            $fileFilter = explode(',', $fileFilter);
        }

        return collect($fileFilter)
            ->map(function ($filter) {
                $filter = trim($filter);
                if (! str_starts_with($filter, '.')) {
                    $filter = ".$filter";
                }

                return $filter;
            })
            ->all();
    }

    protected function validationRules(): array
    {
        return [
            'rule' => 'array',
            'ratioX' => 'integer|nullable',
            'ratioY' => 'integer|nullable',
            'transformable' => 'boolean',
            'transformableFileTypes' => 'array',
            'transformKeepOriginal' => 'boolean',
            'compactThumbnail' => 'boolean',
        ];
    }

    public function toArray(): array
    {
        return parent::buildArray([
            'validation' => $this->buildValidation(),
            'ratioX' => $this->cropRatio ? (int) $this->cropRatio[0] : null,
            'ratioY' => $this->cropRatio ? (int) $this->cropRatio[1] : null,
            'transformable' => $this->transformable,
            'transformableFileTypes' => $this->transformableFileTypes,
            'transformKeepOriginal' => $this->isTransformKeepOriginal(),
            'compactThumbnail' => (bool) $this->compactThumbnail,
            'storageBasePath' => $this->storageBasePath,
            'storageDisk' => $this->storageDisk,
            'shouldOptimizeImage' => $this->shouldOptimizeImage,
        ]);
    }
    
    private function buildValidation(): array
    {
        // Backward compatibility
        $rule = $this->validationRule ?: (new Rules\File())
            ->when($this->fileFilter, fn (SharpFileValidation $file) => $file->extensions($this->fileFilter))
            ->when($this->maxFileSize, fn (SharpFileValidation $file) => $file->max($this->maxFileSize * 1024));
        
        $rulesArray = SharpFileValidation::getRulesArrayFrom($rule);
        
        return [
            'rule' => $rulesArray,
            'allowedExtensions' => $this->getAllowedExtensions($rulesArray),
            'maximumFileSize' => $this->getMaximumFileSize($rulesArray),
        ];
    }
    
    private function getMaximumFileSize(array $rules): ?int
    {
        $rule = collect($rules)->first(fn ($rule) => str_starts_with($rule, 'max:'));
        
        return $rule ? (int) str_replace('max:', '', $rule) : null;
    }
    
    private function getAllowedExtensions(array $rules): array
    {
        $rule = collect($rules)->first(fn ($rule) => str_starts_with($rule, 'extensions:'));
        
        $allowedExtensions = $rule
            ? str($rule)
                ->remove('extensions:')
                ->explode(',')
                ->filter()
                ->map(fn ($ext) => str($ext)->start('.')->value())
                ->toArray()
            : [];
        
        /**
         * @see \Illuminate\Validation\Concerns\ValidatesAttributes::validateImage()
         */
        if(in_array('image', $rules) && empty($allowedExtensions)) {
            $allowedExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.svg', '.webp'];
        }
        
        return $allowedExtensions;
    }
}
