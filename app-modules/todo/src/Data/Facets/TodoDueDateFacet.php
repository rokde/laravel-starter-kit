<?php

declare(strict_types=1);

namespace Modules\Todo\Data\Facets;

use App\Data\Facets\AbstractFacet;
use App\Data\Facets\FilterValueEnum;
use Illuminate\Database\Eloquent\Builder;

class TodoDueDateFacet extends AbstractFacet
{
    public function __construct(string $label, string $key = 'due_date')
    {
        parent::__construct($label, $key);

        $this->options = [
            [
                'label' => __('Without due date'),
                'value' => FilterValueEnum::NULL->value,
            ],
            [
                'label' => __('Overdue'),
                'value' => 'past',
            ],
            [
                'label' => __('On time'),
                'value' => 'future',
            ],
        ];
    }

    public function assignConditionToQuery(Builder $query, array $values): void
    {
        $column = $this->column;

        $query->where(function (Builder $query) use ($column, $values): void {
            $nullValues = array_filter($values, fn ($value): bool => $value === null);
            if ($nullValues !== []) {
                $query->orWhereNull($column);
            }

            foreach ($values as $value) {
                if ($value === 'past') {
                    $query->orWhereNowOrPast($column);
                }
                if ($value === 'future') {
                    $query->orWhereNowOrFuture($column);
                }
            }
        });
    }
}
