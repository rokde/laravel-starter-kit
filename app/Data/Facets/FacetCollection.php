<?php

declare(strict_types=1);

namespace App\Data\Facets;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class FacetCollection
{
    private readonly Collection $facets;

    private function __construct()
    {
        $this->facets = collect();
    }

    public static function make(): self
    {
        return new self();
    }

    public function add(AbstractFacet $facet): self
    {
        $this->facets->put($facet->key(), $facet);

        return $this;
    }

    public function get(): Collection
    {
        return $this->facets;
    }

    public function filterQuery(Collection $filters, Builder $query): void
    {
        $filters->each(function (array $filterValues, string $filterKey) use ($query): void {
            if (! $this->facets->has($filterKey)) {
                return;
            }

            $this->facets->get($filterKey)->assignConditionToQuery($query, $filterValues);
        });
    }
}
