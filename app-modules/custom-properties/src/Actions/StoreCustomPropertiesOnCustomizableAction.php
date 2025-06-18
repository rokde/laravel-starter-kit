<?php

namespace Modules\CustomProperties\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class StoreCustomPropertiesOnCustomizableAction
{
    /**
     * before using this action method you have had to call:
     * $customizable->definable()->associate($definer); // important for getDefinableParent()
     * // e.g. $task->project()->associate($project);
     */
    public function handle(Model $customizable, Request $request, string $customPropertiesKey = 'custom'): void
    {
        if ($request->filled($customPropertiesKey)) {
            $validatedCustom = $customizable->validateCustomProperties($request->input($customPropertiesKey));

            foreach ($validatedCustom as $key => $value) {
                $customizable->setCustomProperty($key, $value);
            }
        }

        $customizable->save();
    }
}
