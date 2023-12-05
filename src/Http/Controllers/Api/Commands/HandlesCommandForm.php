<?php

namespace Code16\Sharp\Http\Controllers\Api\Commands;

use Code16\Sharp\Dashboard\Commands\DashboardCommand;
use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\Commands\InstanceCommand;

trait HandlesCommandForm
{
    protected function getCommandForm(InstanceCommand|EntityCommand|DashboardCommand $commandHandler): array
    {
        if (! count($formFields = $commandHandler->form())) {
            return [];
        }

        $locales = $commandHandler->getDataLocalizations();

        return array_merge(
            [
                'fields' => $formFields,
                'layout' => $commandHandler->formLayout(),
            ],
            $locales ? ['locales' => $locales] : [],
        );
    }
}
