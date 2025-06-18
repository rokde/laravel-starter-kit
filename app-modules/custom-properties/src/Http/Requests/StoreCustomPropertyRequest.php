<?php

declare(strict_types=1);

namespace Modules\CustomProperties\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\CustomProperties\DataTransferObjects\CustomProperty;
use Modules\CustomProperties\Models\CustomPropertyType;

class ModifyCustomPropertyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|alpha_dash',
            'label' => 'required|string',
            'type' => ['required', Rule::enum(CustomPropertyType::class)],
            'rules' => 'sometimes|array',
            'default_value' => 'sometimes|nullable',
            'options' => 'sometimes|array',
        ];
    }

    public function toDto(): CustomProperty
    {
        return new CustomProperty(
            name: Str::of($this->validated('name'))->trim()->lower()->value(),
            label: Str::of($this->validated('label'))->trim()->value(),
            type: CustomPropertyType::tryFrom($this->validated('type')),
            rules: $this->has('rules') ? $this->array('rules') : null,
            defaultValue: $this->get('default_value'),
            options: $this->has('options') ? $this->array('options') : null,
        );
    }
}
