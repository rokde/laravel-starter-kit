<?php

declare(strict_types=1);

namespace Modules\CustomProperties\Http\Requests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;
use Modules\CustomProperties\Models\Concerns\DefinesCustomProperties;

class StoreCustomPropertyWithDefinerRequest extends StoreCustomPropertyRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        return [
            ...$rules,
            'definable_type' => ['required', 'string',
                // make sure it defines custom properties
                function ($attribute, $value, $fail): void {
                    if (! class_exists($value) || ! in_array(DefinesCustomProperties::class, class_uses_recursive($value))) {
                        $fail('The given model is not as "Definable" configured.');
                    }
                },
            ],
            'definable_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'alpha_dash',
                Rule::unique('custom_field_definitions')->where(function ($query) {
                    return $query->where('definable_type', $this->definable_type)
                        ->where('definable_id', $this->definable_id);
                }),
            ],

        ];
    }

    /**
     * @throws ModelNotFoundException
     */
    public function definableModel(): Model
    {
        $definableModelClass = $this->validated('definable_type');
        return $definableModelClass::findOrFail($this->validated('definable_id'));
    }
}
