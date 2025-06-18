<?php

declare(strict_types=1);

namespace Modules\CustomProperties\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Modules\CustomProperties\Jobs\CleanupCustomPropertyJob;
use Modules\CustomProperties\Models\CustomPropertyDefinition;

class CustomPropertyDefinitionController
{
    public function destroy(
        CustomPropertyDefinition $definition,
    ): RedirectResponse {
        $definable = $definition->definable;
        $propertyName = $definition->name;

        $definition->delete();

        CleanupCustomPropertyJob::dispatch($definable, $propertyName);

        return back()->with('success', 'Custom property values will be deleted.');
    }
}
