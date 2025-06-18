<?php

declare(strict_types=1);

namespace Modules\CustomProperties\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * @uses \Eloquent
 */
trait HasCustomProperties
{
    /**
     * Forces models to implement the return of a parent model which defines the properties
     */
    abstract public function getDefinableParent(): ?Model;

    public function initializeHasCustomProperties(): void
    {
        $this->casts['custom_properties'] = 'array';
    }

    public function setCustomProperty(string $name, mixed $value): self
    {
        $properties = $this->custom_properties ?? [];
        $properties[$name] = $value;
        $this->custom_properties = $properties;

        return $this;
    }

    public function getCustomProperty(string $name, mixed $default = null): mixed
    {
        if (isset($this->custom_properties) && array_key_exists($name, $this->custom_properties)) {
            return $this->custom_properties[$name];
        }

        if ($parent = $this->getDefinableParent()) {
            $definition = $parent->customPropertyDefinitions()->where('name', $name)->first();

            if ($definition && ! is_null($definition->default_value)) {
                return $definition->default_value;
            }
        }

        return $default;
    }

    /**
     * @throws ValidationException
     */
    public function validateCustomProperties(array $propertiesToValidate): array
    {
        if (! $parent = $this->getDefinableParent()) {
            return [];
        }

        $definitions = $parent->customPropertyDefinitions;

        $rules = [];
        $attributes = [];

        foreach ($definitions as $definition) {
            if (array_key_exists($definition->name, $propertiesToValidate)) {
                $rules[$definition->name] = $definition->rules;
                $attributes[$definition->name] = $definition->label;
            }
        }

        if ($rules === []) {
            return [];
        }

        return Validator::make($propertiesToValidate, $rules)
            ->setAttributeNames($attributes)
            ->validate();
    }

    public function scopeWhereCustom(Builder $query, string $name, mixed $value, string $operator = '='): Builder
    {
        // Der Operator wird für zukünftige Erweiterungen (>, <, LIKE) beibehalten.
        // Für eine einfache Gleichheitssuche ist whereJsonContains am besten.
        if ($operator === '=') {
            return $query->whereJsonContains("custom_properties->{$name}", $value);
        }

        // Für andere Operatoren
        return $query->where("custom_properties->{$name}", $operator, $value);
    }
}
