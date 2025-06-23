<?php

declare(strict_types=1);

namespace Modules\ModelFacets\DataTransferObjects;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FacetSearchResult
{
    public function __construct(
        public LengthAwarePaginator $results,
        public array $facets
    ) {}

    public function toArray(): array
    {
        return [
            'results' => $this->results,
            'facets' => $this->facets,
        ];
    }
}
