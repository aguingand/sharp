<?php

namespace Code16\Sharp\Http\Api\Commands;

use Code16\Sharp\Dashboard\DashboardQueryParams;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Http\Api\ApiController;

class DashboardCommandController extends ApiController
{
    use HandleCommandReturn;

    /**
     * @param string $entityKey
     * @param string $commandKey
     *
     * @throws \Code16\Sharp\Exceptions\Auth\SharpAuthorizationException
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($entityKey, $commandKey)
    {
        $dashboard = $this->getDashboardInstance($entityKey);
        $dashboard->buildDashboardConfig();
        $commandHandler = $this->getCommandHandler($dashboard, $commandKey);

        return response()->json([
            'data' => $commandHandler->formData(),
        ]);
    }

    /**
     * @param string $entityKey
     * @param string $commandKey
     *
     * @throws \Code16\Sharp\Exceptions\Auth\SharpAuthorizationException
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($entityKey, $commandKey)
    {
        $dashboard = $this->getDashboardInstance($entityKey);
        $dashboard->buildDashboardConfig();
        $commandHandler = $this->getCommandHandler($dashboard, $commandKey);

        return $this->returnCommandResult(
            $dashboard,
            $commandHandler->execute(
                DashboardQueryParams::create()->fillWithRequest('query'),
                $commandHandler->formatRequestData((array) request('data'))
            )
        );
    }

    /**
     * @param SharpDashboard $dashboard
     * @param string         $commandKey
     *
     * @throws \Code16\Sharp\Exceptions\Auth\SharpAuthorizationException
     *
     * @return \Code16\Sharp\Dashboard\Commands\DashboardCommand|null
     */
    protected function getCommandHandler(SharpDashboard $dashboard, $commandKey)
    {
        $commandHandler = $dashboard->dashboardCommandHandler($commandKey);

        if (!$commandHandler->authorize()) {
            throw new SharpAuthorizationException();
        }

        return $commandHandler;
    }
}
