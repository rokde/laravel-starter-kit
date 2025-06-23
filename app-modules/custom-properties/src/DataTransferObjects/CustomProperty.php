<?php

declare(strict_types=1);

namespace Modules\CustomProperties\DataTransferObjects;

use Modules\CustomProperties\Models\CustomPropertyType;

final class CustomProperty
{
    public readonly ?array $rules;

    public readonly ?array $propertyOptions;
    public readonly ?array $displayOptions;

    public readonly ?array $options;

    public function __construct(
        public readonly string $name,
        public readonly string $label,
        public readonly CustomPropertyType $type,
        ?array $rules = null,
        public readonly mixed $defaultValue = null,
        ?array $propertyOptions = null,
        ?array $displayOptions = null,
        ?array $options = null,
    ) {
        if ($rules === null) {
            $rules = [];
        }
        $this->rules = [
            ...$type->defaultRules(),
            ...$rules,
        ];

        if (is_array($options) && count($options) < 1) {
            $options = null;
        }
        $this->options = $options;

        $this->propertyOptions = $type->resolvePropertyOptions($propertyOptions);
        $this->displayOptions = $type->resolveDisplayOptions($displayOptions);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'type' => $this->type->value,
            'rules' => $this->rules,
            'default_value' => $this->defaultValue,
            'property_options' => $this->propertyOptions,
            'display_options' => $this->displayOptions,
            'options' => $this->options,
        ];
    }
}
