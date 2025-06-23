<?php

declare(strict_types=1);

namespace Modules\ModelFacets\Facets;

use Illuminate\Database\Eloquent\Builder;

abstract class AbstractFacet
{
    public function __construct(
        public string $key,
        public string $label
    ) {}

    abstract public function applyFilter(Builder $query, mixed $values): void;

    abstract public function getFacetOptions(Builder $baseQuery): array;
}
