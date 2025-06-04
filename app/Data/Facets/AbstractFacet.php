<?php

namespace App\Data\Facets;

use App\Data\Facets\DataTransferObjects\FacetOption;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use JsonSerializable;

abstract class AbstractFacet implements Arrayable, JsonSerializable
{
    protected array $options = [];

    protected ?int $displaySelectedItems = null;

    public function __construct(
        protected readonly string $label,
        protected readonly string $key,
    ) {}

    public function displaySelectedItems(?int $displaySelectedItems = null): self
    {
        $this->displaySelectedItems = $displaySelectedItems;

        return $this;
    }

    public function setCounts(Collection $valueMap): self
    {
        $this->options = collect($this->options)->map(fn (array $option): FacetOption => new FacetOption(
            label: $option['label'],
            value: $option['value'],
            count: $valueMap->get($option['value'], 0),
        ))->all();

        return $this;
    }

    public function toArray(): array
    {
        return array_filter([
            'label' => $this->label,
            'key' => $this->key,
            'displaySelectedItems' => $this->displaySelectedItems,
            'options' => $this->options,
        ]);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
