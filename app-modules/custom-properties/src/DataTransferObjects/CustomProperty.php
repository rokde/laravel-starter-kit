<?php

declare(strict_types=1);

namespace Modules\CustomProperties\DataTransferObjects;

use Modules\CustomProperties\Models\CustomPropertyType;

final readonly class CustomProperty
{
    public ?array $rules;

    public ?array $options;

    public function __construct(
        public string $name,
        public string $label,
        public CustomPropertyType $type,
        ?array $rules = null,
        public mixed $defaultValue = null,
        ?array $options = null,
        public ?int $sequence = null,
    ) {
        if (is_array($rules) && count($rules) < 1) {
            $rules = null;
        }
        $this->rules = $rules;

        if (is_array($options) && count($options) < 1) {
            $options = null;
        }
        $this->options = $options;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'type' => $this->type->value,
            'rules' => $this->rules,
            'default_value' => $this->defaultValue,
            'options' => $this->options,
            'sequence' => $this->sequence,
        ];
    }
}
