<?php

declare(strict_types=1);

namespace Modules\CustomProperties\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Modules\CustomProperties\Actions\AddCustomPropertyToDefinerAction;
use Modules\CustomProperties\Http\Requests\StoreCustomPropertyWithDefinerRequest;
use Modules\CustomProperties\Jobs\CleanupCustomPropertyJob;
use Modules\CustomProperties\Models\CustomPropertyDefinition;

class CustomPropertyDefinitionController
{
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
