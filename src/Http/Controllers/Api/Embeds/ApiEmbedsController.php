<?php

namespace Code16\Sharp\Http\Controllers\Api\Embeds;

use Illuminate\Routing\Controller;

class ApiEmbedsController extends Controller
{
    use HandleEmbed;

    /**
     * Return formatted data to display an embed (in an Editor field or in a Show field).
     */
    public function show(string $embedKey, string $entityKey, ?string $instanceId = null)
    {
        if ($instanceId) {
            sharp_check_ability('view', $entityKey, $instanceId);
        } else {
            sharp_check_ability('entity', $entityKey);
        }

        $embed = $this->getEmbedFromKey($embedKey);

        return response()->json([
            'embeds' => collect(request()->get('embeds'))
                ->map(fn (array $attributes) => (object) $embed->transformDataForTemplate($attributes, request()->boolean('form'))),
        ]);
    }
}
