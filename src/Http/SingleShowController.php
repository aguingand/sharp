<?php

namespace Code16\Sharp\Http;

use Code16\Sharp\Auth\SharpAuthorizationManager;
use Code16\Sharp\Data\BreadcrumbData;
use Code16\Sharp\Data\NotificationData;
use Code16\Sharp\Data\Show\ShowData;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Inertia\Inertia;

class SingleShowController extends SharpProtectedController
{
    use HandlesSharpNotificationsInRequest;

    public function __construct(
        private SharpAuthorizationManager $sharpAuthorizationManager,
        private SharpEntityManager $entityManager,
    ) {
        parent::__construct();
    }

    public function show(string $entityKey)
    {
        sharp_check_ability('view', $entityKey);

        $show = $this->entityManager->entityFor($entityKey)->getShowOrFail();

        $show->buildShowConfig();

        $data = [
            'config' => $show->showConfig(null),
            'fields' => $show->fields(),
            'layout' => $show->showLayout(),
            'data' => $show->instance(null),
            'locales' => $show->hasDataLocalizations()
                ? $show->getDataLocalizations()
                : null,
            'authorizations' => [
                'create' => $this->sharpAuthorizationManager->isAllowed('create', $entityKey),
                'view' => $this->sharpAuthorizationManager->isAllowed('view', $entityKey),
                'update' => $this->sharpAuthorizationManager->isAllowed('update', $entityKey),
                'delete' => $this->sharpAuthorizationManager->isAllowed('delete', $entityKey),
            ],
        ];

        return Inertia::render('Show', [
            'show' => ShowData::from($data),
            'breadcrumb' => BreadcrumbData::from(['items' => []]), // TODO
            'notifications' => NotificationData::collection($this->getSharpNotifications()),
        ]);
    }
}
