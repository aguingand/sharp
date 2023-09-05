<?php

namespace Code16\Sharp\Utils\Links;

use Closure;

class LinkToShowPage extends SharpLinkTo
{
    protected string $instanceId;
    protected BreadcrumbBuilder $breadcrumbBuilder;

    public static function make(string $entityKey, string $instanceId): self
    {
        $instance = new static($entityKey);
        $instance->instanceId = $instanceId;

        return $instance;
    }

    public function withBreadcrumb(Closure $closure): self
    {
        $this->breadcrumbBuilder = $closure(new BreadcrumbBuilder());

        return $this;
    }

    public function renderAsUrl(): string
    {
        if (isset($this->breadcrumbBuilder)) {
            return url(
                sprintf(
                    '%s/%s/%s',
                    config('sharp.custom_url_segment', 'sharp'),
                    $this->breadcrumbBuilder->generateUri(),
                    sprintf('s-show/%s/%s', $this->entityKey, $this->instanceId)
                )
            );
        }

        return route('code16.sharp.show.show', [
            'uri' => sprintf('s-list/%s', $this->entityKey),
            'entityKey' => $this->entityKey,
            'instanceId' => $this->instanceId,
        ]);
    }
}
