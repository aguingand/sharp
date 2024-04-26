<?php

namespace Code16\Sharp\Http\Controllers\Api\Commands;

use Code16\Sharp\Dashboard\DashboardQueryParams;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Data\Commands\CommandFormData;
use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Http\Controllers\Api\ApiController;
use Code16\Sharp\Utils\Uploads\SharpUploadManager;

class ApiDashboardCommandController extends ApiController
{
    use HandlesCommandReturn;
    use HandlesCommandForm;

    public function __construct(
        private readonly SharpUploadManager $uploadManager,
    ) {
        parent::__construct();
    }

    public function show(string $entityKey, string $commandKey)
    {
        $dashboard = $this->getDashboardInstance($entityKey);
        $dashboard->buildDashboardConfig();
        $dashboard->initQueryParams(request()->all());

        $commandHandler = $this->getCommandHandler($dashboard, $commandKey);

        return response()->json(
            CommandFormData::from([
                ...$this->getCommandForm($commandHandler),
                'data' => $commandHandler->applyFormatters($commandHandler->formData() ?: null),
                'pageAlert' => $commandHandler->pageAlert($commandHandler->allFormData()),
            ]),
        );
    }

    public function update(string $entityKey, string $commandKey)
    {
        $dashboard = $this->getDashboardInstance($entityKey);
        $dashboard->buildDashboardConfig();
        $dashboard->initQueryParams(request()->input('query'));

        $commandHandler = $this->getCommandHandler($dashboard, $commandKey);

        $formattedData = $commandHandler->formatAndValidateRequestData((array) request('data'));
        $result = $this->returnCommandResult($dashboard, $commandHandler->execute($formattedData));
        $this->uploadManager->dispatchJobs();

        return $result;
    }

    protected function getCommandHandler(SharpDashboard $dashboard, string $commandKey)
    {
        if ($handler = $dashboard->findDashboardCommandHandler($commandKey)) {
            $handler->buildCommandConfig();

            if (! $handler->authorize()) {
                throw new SharpAuthorizationException();
            }
        }

        return $handler;
    }
}
