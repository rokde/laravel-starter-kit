<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Data\Facets\FilterValueEnum;
use App\Enums\SortDirection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;

class PaginationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page' => 'integer|min:1',
            'size' => 'sometimes|integer|between:1,100',
            'fields' => 'sometimes|string',
            'sort' => 'sometimes|string',
            'term' => 'sometimes|string',
            'filter' => 'sometimes|array',
            'filter.*' => 'required|string',
        ];
    }

    public function page(int $default = 1): int
    {
        return $this->integer('page', $default);
    }

    public function size(?int $default = null): ?int
    {
        return $this->integer('size', $default);
    }

    public function fields(string $default = '*'): array
    {
        return explode(',', (string) $this->input('fields', $default));
    }

    /**
     * @return array<number, array<{field: string, direction: SortDirection}>>
     */
    public function sort(?string $field = null, SortDirection $direction = SortDirection::ASC): array
    {
        $sortString = $this->input('sort');
        if (! $sortString) {
            if ($field === null || $field === '' || $field === '0') {
                return [];
            }

            return [
                [
                    'field' => $field,
                    'direction' => $direction,
                ],
            ];
        }

        $result = [];
        foreach (explode(',', (string) $sortString) as $field) {
            $field = mb_trim($field);
            $result[] = [
                'field' => mb_ltrim($field, '-'),
                'direction' => SortDirection::fromString($field),
            ];
        }

        return $result;
    }

    public function term(): ?string
    {
        return $this->input('term');
    }

    public function facets(): Collection
    {
        return $this->collect('filter')
            ->map(fn (string $filterValues) => explode(',', $filterValues))
            ->map(fn (array $filterValues) => collect($filterValues)
                ->map(fn (string $value): mixed => FilterValueEnum::transformFilterValue($value))
                ->all()
            );
    }

    public function filter(): array
    {
        return $this->collect('filter')
            ->map(fn (string $filterValues) => explode(',', $filterValues))
            ->map(fn (array $filterValues) => collect($filterValues)->all())
            ->all();
    }

    public function pageName(): string
    {
        return 'page';
    }
}
