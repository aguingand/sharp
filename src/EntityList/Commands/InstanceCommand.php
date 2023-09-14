<?php

namespace Code16\Sharp\EntityList\Commands;

abstract class InstanceCommand extends Command
{
    protected array $authorizedInstances = [];

    public function type(): string
    {
        return 'instance';
    }

    public final function formData(mixed $instanceId): array
    {
        return collect($this->initialData($instanceId))
            ->only($this->getDataKeys())
            ->all();
    }

    protected function initialData(mixed $instanceId): array
    {
        return [];
    }

    abstract public function execute(mixed $instanceId, array $data = []): array;

    /**
     * Check if the current user is allowed to use this Command for this instance.
     */
    public function authorizeFor(mixed $instanceId): bool
    {
        return true;
    }

    public final function checkAndStoreAuthorizationFor(mixed $instanceId): void
    {
        if ($this->authorizeFor($instanceId)) {
            $this->authorizedInstances[] = $instanceId;
        }
    }

    public function getGlobalAuthorization(): bool|array
    {
        if (! $this->authorize()) {
            return false;
        }

        return $this->authorizedInstances;
    }
}
