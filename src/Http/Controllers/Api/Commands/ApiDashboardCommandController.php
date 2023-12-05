<?php

namespace Code16\Sharp\Http\Controllers\Api\Commands;

use Code16\Sharp\Dashboard\DashboardQueryParams;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Data\Commands\CommandFormData;
use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Http\Controllers\Api\ApiController;
use Code16\Sharp\Http\Controllers\HandlesUploadedFilesInRequest;

class ApiDashboardCommandController extends ApiController
{
    use HandlesCommandReturn;
    use HandlesCommandForm;
    use HandlesUploadedFilesInRequest;

    public function show(string $entityKey, string $commandKey)
    {
        $dashboard = $this->getDashboardInstance($entityKey);
        $dashboard->buildDashboardConfig();

        $commandHandler = $this->getCommandHandler($dashboard, $commandKey);
        $formData = $commandHandler->formData() ?: null;

        return response()->json(
            CommandFormData::from([
                ...$this->getCommandForm($commandHandler),
                'data' => $formData,
                'pageAlert' => $commandHandler->pageAlert($formData),
            ]),
        );
    }

    public function update(string $entityKey, string $commandKey)
    {
        $dashboard = $this->getDashboardInstance($entityKey);
        $dashboard->buildDashboardConfig();

        $commandHandler = $this->getCommandHandler($dashboard, $commandKey);

        $formattedData = $commandHandler->formatAndValidateRequestData((array) request('data'));
        $result = $this->returnCommandResult($dashboard, $commandHandler->execute($formattedData));
        $this->handlePostedFiles($commandHandler, request()->all(), $formattedData);

        return $result;
    }

    protected function getCommandHandler(SharpDashboard $dashboard, string $commandKey)
    {
        if ($handler = $dashboard->findDashboardCommandHandler($commandKey)) {
            $handler->buildCommandConfig();

            if (! $handler->authorize()) {
                throw new SharpAuthorizationException();
            }

            $handler->initQueryParams(DashboardQueryParams::create()->fillWithRequest());
        }

        return $handler;
    }
}
