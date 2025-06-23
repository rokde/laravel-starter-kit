<?php

declare(strict_types=1);

namespace Modules\CustomProperties\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\CustomProperties\DataTransferObjects\CustomProperty;
use Modules\CustomProperties\Models\CustomPropertyType;

class StoreCustomPropertyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'label' => 'required|string',
            'type' => ['required', Rule::enum(CustomPropertyType::class)],
            'rules' => 'sometimes|array',
            'default_value' => 'sometimes|nullable',
            'property_options' => ['sometimes', 'array', 'required_if:type,number,select'],
            'property_options.decimal_places' => ['sometimes', 'numeric', 'present_if:type,number', 'min:0', 'max:10'],
            'property_options.suffix' => ['nullable', 'string', 'present_if:type,number', 'min:0', 'max:10'],
            'property_options.sort' => ['sometimes', 'string', 'present_if:type,select', 'in:asc,desc,-'],
            'display_options' => ['sometimes', 'array', 'required_if:type,number'],
            'display_options.display' => ['sometimes', 'string', 'required_if:type,number', 'in:value,progress,progress-with-value,ring,ring-with-value'],
            'options' => 'sometimes|array',
        ];
    }

    public function toDto(): CustomProperty
    {
        return new CustomProperty(
            name: Str::of($this->validated('label'))->trim()->slug()->value(),
            label: Str::of($this->validated('label'))->trim()->value(),
            type: CustomPropertyType::tryFrom($this->validated('type')),
            rules: $this->has('rules') ? $this->array('rules') : null,
            defaultValue: $this->get('default_value'),
            propertyOptions: $this->has('property_options') ? $this->array('property_options') : null,
            options: $this->has('options') ? $this->array('options') : null,
        );
    }
}
