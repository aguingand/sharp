<?php

namespace Code16\Sharp\EntityList\Traits\Utils;

use Code16\Sharp\EntityList\Commands\Command;
use Illuminate\Support\Collection;

trait CommonCommandUtils
{
    protected function appendCommandsToConfig(Collection $commandHandlers, array &$config, $instanceId = null): void
    {
        $commandHandlers
            ->each(function (Command $handler) use (&$config, $instanceId) {
                $handler->buildCommandConfig();

                $config['commands'][$handler->type()][$handler->groupIndex()][] = [
                    'key' => $handler->getCommandKey(),
                    'label' => $handler->label(),
                    'description' => $handler->getDescription(),
                    'type' => $handler->type(),
                    'confirmation' => $handler->getConfirmationText() ?: null,
                    'modal_title' => $handler->getFormModalTitle() ?: null,
                    'has_form' => count($handler->form()) > 0,
                    'authorization' => $instanceId
                        ? $handler->authorizeFor($instanceId)
                        : $handler->getGlobalAuthorization(),
                ];
            });
    }
}
