<?php

declare(strict_types=1);

namespace Modules\ModelFacets\Facets;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class RangeFacet extends AbstractFacet {
    public function __construct(
        string $key,
        string $label,
        public string $value_field
    ) {
        parent::__construct($key, $label);
    }

    /**
     * Wendet den Bereichsfilter an.
     * Erwartet ein Array wie ['min' => 100, 'max' => 500].
     */
    public function applyFilter(Builder $query, mixed $values): void
    {
        if (! is_array($values)) {
            return;
        }

        if (isset($values['min']) && is_numeric($values['min'])) {
            $query->where($this->value_field, '>=', $values['min']);
        }

        if (isset($values['max']) && is_numeric($values['max'])) {
            $query->where($this->value_field, '<=', $values['max']);
        }
    }

    public function getFacetOptions(Builder $baseQuery): array
    {
        $query = clone $baseQuery;

        $result = $query
            ->select(
                DB::raw("MIN({$this->value_field}) as min_val"),
                DB::raw("MAX({$this->value_field}) as max_val")
            )
            ->first();

        return [
            'min' => $result->min_val ?? 0,
            'max' => $result->max_val ?? 0,
        ];
    }
}
