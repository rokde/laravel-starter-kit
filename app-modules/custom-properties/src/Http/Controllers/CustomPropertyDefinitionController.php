<?php

declare(strict_types=1);

namespace Modules\CustomProperties\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\CustomProperties\Actions\AddCustomPropertyToDefinerAction;
use Modules\CustomProperties\Http\Requests\StoreCustomPropertyWithDefinerRequest;
use Modules\CustomProperties\Jobs\CleanupCustomPropertyJob;
use Modules\CustomProperties\Models\Concerns\DefinesCustomProperties;
use Modules\CustomProperties\Models\CustomPropertyDefinition;

class CustomPropertyDefinitionController
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'definable_type' => ['required', 'string',
                function ($attribute, $value, $fail): void {
                    if (! class_exists($value) || ! in_array(DefinesCustomProperties::class, class_uses_recursive($value))) {
                        $fail('The given model is not as "Definable" configured.');
                    }
                },
            ],
            'definable_id' => ['required', 'integer'],
        ]);

        $definable = $validated['definable_type']::findOrFail($validated['definable_id']);

        $definitions = $definable->customPropertyDefinitions()
            ->orderBy('sequence')
            ->orderBy('label')
            ->get();

        return response()->json(['data' => $definitions]);
    }

    public function store(StoreCustomPropertyWithDefinerRequest $request, AddCustomPropertyToDefinerAction $action): RedirectResponse
    {
        $action->handle($request->definableModel(), $request->toDto());

        return back()->with('message', 'Custom property was created.');
    }

    public function destroy(
        CustomPropertyDefinition $definition,
    ): RedirectResponse {
        $definable = $definition->definable;
        $propertyName = $definition->name;

        $definition->delete();

        CleanupCustomPropertyJob::dispatch($definable, $propertyName);

        return back()->with('message', 'Custom property values will be deleted.');
    }
}
