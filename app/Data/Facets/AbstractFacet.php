<?php

declare(strict_types=1);

namespace App\Data\Facets;

use App\Data\Facets\DataTransferObjects\FacetOption;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use JsonSerializable;

abstract class AbstractFacet implements Arrayable, JsonSerializable
{
    protected array $options = [];

    protected ?int $displaySelectedItems = null;

    protected readonly string $column;

    public function __construct(
        protected readonly string $label,
        protected readonly string $key,
        string|null $column = null,
    ) {
        $this->column = $column ?? $key;
    }

    final public function displaySelectedItems(?int $displaySelectedItems = null): self
    {
        $this->displaySelectedItems = $displaySelectedItems;

        return $this;
    }

    final public function setCounts(Collection $valueMap): self
    {
        $this->options = collect($this->options)->map(fn (array $option): FacetOption => new FacetOption(
            label: $option['label'],
            value: $option['value'],
            count: $valueMap->get($option['value'], 0),
        ))->all();

        return $this;
    }

    final public function toArray(): array
    {
        return array_filter([
            'label' => $this->label,
            'key' => $this->key,
            'displaySelectedItems' => $this->displaySelectedItems,
            'options' => $this->options,
        ]);
    }

    final public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    final public function key(): string
    {
        return $this->key;
    }

    public function assignConditionToQuery(Builder $query, array $values): void
    {
        $relation = null;
        $column = $this->column;
        if (mb_substr_count($this->column, '.') > 0) {
            [$relation, $column] = explode('.', $this->column);
        }

        $query->when($relation, function (Builder $query) use ($relation, $column, $values): void {
            $query->whereHas($relation, function (Builder $query) use ($column, $values): void {
                $query->whereIn($column, $values);
            });
        });

        $query->unless($relation, function (Builder $query) use ($column, $values): void {
            $query->where(function (Builder $query) use ($column, $values): void {
                $nullValues = array_filter($values, fn ($value): bool => $value === null);
                if ($nullValues !== []) {
                    $query->orWhereNull($column);
                }

                $valueValues = array_filter($values, fn ($value): bool => $value !== null);
                if ($valueValues !== []) {
                    $query->orWhereIn($column, $valueValues);
                }
            });
        });
    }
}
