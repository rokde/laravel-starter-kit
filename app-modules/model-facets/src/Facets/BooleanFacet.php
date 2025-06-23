<?php

declare(strict_types=1);

namespace Modules\ModelFacets\Facets;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class BooleanFacet extends AbstractFacet
{
    public function __construct(
        string $key,
        string $label,
        public string $value_field,
        public string $label_true = 'Yes',
        public string $label_false = 'No'
    ) {
        parent::__construct($key, $label);
    }

    public function applyFilter(Builder $query, mixed $values): void
    {
        $boolValue = filter_var($values, FILTER_VALIDATE_BOOLEAN);
        $query->where($this->value_field, '=', $boolValue);
    }

    public function getFacetOptions(Builder $baseQuery): array
    {
        $query = clone $baseQuery;

        $counts = $query->select(
            DB::raw("SUM(CASE WHEN {$this->value_field} = 1 THEN 1 ELSE 0 END) as true_count"),
            DB::raw("SUM(CASE WHEN {$this->value_field} = 0 THEN 1 ELSE 0 END) as false_count")
        )->first();

        return [
            [
                'value' => 1,
                'label' => $this->label_true,
                'count' => (int) ($counts->true_count ?? 0),
            ],
            [
                'value' => 0,
                'label' => $this->label_false,
                'count' => (int) ($counts->false_count ?? 0),
            ],
        ];
    }
}
