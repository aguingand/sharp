<?php

namespace Code16\Sharp\Http\Controllers\Api\Commands;

use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Show\SharpShow;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait HandleCommandReturn
{
    protected function returnCommandResult(
        SharpEntityList|SharpShow|SharpDashboard $commandContainer,
        array $returnedValue
    ): StreamedResponse|JsonResponse {
        if ($returnedValue['action'] == 'download') {
            return Storage::disk($returnedValue['disk'])
                ->download(
                    $returnedValue['file'],
                    $returnedValue['name'],
                );
        }

        if ($returnedValue['action'] == 'streamDownload') {
            return response()->streamDownload(
                function () use ($returnedValue) {
                    echo $returnedValue['content'];
                },
                $returnedValue['name'],
            );
        }

        if ($returnedValue['action'] == 'refresh' && $commandContainer instanceof SharpEntityList) {
            // We have to load and build items from ids
            $returnedValue['items'] = $commandContainer
                ->updateQueryParamsWithSpecificIds($returnedValue['items'])
                ->data()['items'];
        }

        return response()->json($returnedValue);
    }

    protected function getInstanceCommandHandler(
        SharpEntityList|SharpShow $commandContainer,
        string $commandKey,
        mixed $instanceId
    ): ?InstanceCommand {
        $commandHandler = $commandContainer->findInstanceCommandHandler($commandKey);
        $commandHandler->buildCommandConfig();

        if (! $commandHandler->authorize() || ! $commandHandler->authorizeFor($instanceId)) {
            throw new SharpAuthorizationException();
        }

        return $commandHandler;
    }
}
