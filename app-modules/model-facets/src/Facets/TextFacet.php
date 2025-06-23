<?php

declare(strict_types=1);

namespace Modules\ModelFacets\Facets;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class TextFacet extends AbstractFacet
{
    public function __construct(
        string $key,
        string $label,
        public string $value_field,
        public ?string $label_field = null,
        public ?Closure $label_resolver = null,
        public array $select_fields = [],
        public string $null_value_placeholder = '_null_',
        public string $null_label = 'Nicht zugewiesen'
    ) {
        parent::__construct($key, $label);
        $this->label_field ??= $this->value_field;
    }

    public function applyFilter(Builder $query, mixed $values): void
    {
        $values = Arr::wrap($values);

        $containsNull = in_array($this->null_value_placeholder, $values);

        // replace NULL-values for whereIn calls
        $otherValues = array_filter($values, fn ($v): bool => $v !== $this->null_value_placeholder);

        // Kapsle die Logik in einer WHERE-Klammer, um Konflikte mit anderen Filtern zu vermeiden.
        // Z.B. `AND (color IN ('red', 'blue') OR color IS NULL)`
        $query->where(function (Builder $q) use ($containsNull, $otherValues): void {
            if ($otherValues !== []) {
                $q->whereIn($this->value_field, $otherValues);
            }

            if ($containsNull) {
                $q->orWhereNull($this->value_field);
            }
        });
    }

    public function getFacetOptions(Builder $baseQuery): array
    {
        $query = clone $baseQuery;

        $valueSelect = DB::raw("COALESCE(CAST({$this->value_field} AS CHAR), '{$this->null_value_placeholder}') as value");

        $selects = [
            DB::raw('COUNT(*) as count'),
            $valueSelect,
        ];

        if ($this->label_resolver && $this->select_fields !== []) {
            // Wenn ein Resolver da ist, selektieren wir die Rohdaten.
            // Der Resolver muss dann NULL-Werte selbst behandeln können.
            $selects = array_merge($selects, $this->select_fields);
        } else {
            // Andernfalls erzeugen wir das Label direkt in SQL.
            $labelSelect = DB::raw("COALESCE(CAST({$this->label_field} AS CHAR), '{$this->null_label}') as label");
            $selects[] = $labelSelect;
        }

        // `value` ist hier bereits der COALESCE-Ausdruck
        $groupByFields = ['value'];
        if ($this->label_resolver && $this->select_fields !== []) {
            $groupByFields = array_merge($groupByFields, $this->select_fields);
        } elseif (isset($labelSelect)) {
            $groupByFields[] = 'label';
        }

        $results = $query
            ->select(...$selects)
            ->groupBy(...$groupByFields)
            ->orderBy('count', 'desc')
            ->get();

        // Label-Generierung via PHP-Callback nach der DB-Abfrage
        if ($this->label_resolver instanceof Closure) {
            $results->each(function ($item): void {
                // Wenn der Wert unsere NULL-Kennung ist, setzen wir ein Standard-Label,
                // ansonsten rufen wir den Resolver auf.
                if ($item->value === $this->null_value_placeholder) {
                    // Der Resolver könnte auch den Fall `is_available = null` direkt behandeln
                    $item->label = $this->null_label;
                } else {
                    $item->label = ($this->label_resolver)($item);
                }
            });
        }

        return $results->toArray();
    }
}
