<?php

declare(strict_types=1);

namespace Modules\ModelFacets\Models\Concerns;

trait HasFacetSearch
{
    abstract public function getFacetSearchConfig(): array;
}
