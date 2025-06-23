<?php

declare(strict_types=1);

namespace Modules\ModelFacets;

use Illuminate\Database\Eloquent\Builder;
use Modules\ModelFacets\DataTransferObjects\FacetSearchResult;

class FacetSearchManager
{
    protected array $config;

    protected array $facets = [];

    public function __construct(protected Builder $query, protected array $input)
    {
        $this->config = $this->query->getModel()->getFacetSearchConfig();

        // initialize facet configuration
        foreach ($this->config['facets'] ?? [] as $key => $facetConfig) {
            $this->facets[$key] = new $facetConfig['class']($key, ...array_slice($facetConfig, 1));
        }
    }

    public function getResult(): FacetSearchResult
    {
        $this->applyFuzzySearch();
        $this->applyFilters(); // Wende alle Filter auf die Haupt-Query an
        $this->applySorting();

        $facetData = $this->generateFacets();

        $paginatedResults = $this->query->paginate($this->input['per_page'] ?? 15)
            ->withQueryString();

        return new FacetSearchResult($paginatedResults, $facetData);
    }

    protected function applyFuzzySearch(): void
    {
        $term = $this->input['q'] ?? null;
        if (! $term || empty($this->config['searchable'])) {
            return;
        }

        $this->query->where(function (Builder $q) use ($term): void {
            foreach ($this->config['searchable'] as $field) {
                $q->orWhere($field, 'LIKE', "%{$term}%");
            }
        });
    }

    protected function applyFilters(): void
    {
        $filters = $this->input['filters'] ?? [];
        foreach ($filters as $key => $value) {
            if (isset($this->facets[$key])) {
                $this->facets[$key]->applyFilter($this->query, $value);
            }
        }
    }

    protected function applySorting(): void
    {
        $sortBy = $this->input['sort_by'] ?? $this->config['default_sort'] ?? null;
        if (! $sortBy) {
            return;
        }

        $direction = 'asc';
        if (str_starts_with((string) $sortBy, '-')) {
            $direction = 'desc';
            $sortBy = mb_ltrim($sortBy, '-');
        }

        if (in_array($sortBy, $this->config['sortable'] ?? [])) {
            $this->query->orderBy($sortBy, $direction);
        }
    }

    protected function generateFacets(): array
    {
        $facetData = [];
        $appliedFilters = $this->input['filters'] ?? [];

        foreach ($this->facets as $key => $facet) {
            // "Self-Excluding" Logik: Erzeuge eine Basis-Query für die Count-Berechnung
            $baseQueryForFacet = $this->query->getModel()->query();

            // 1. Wende die globale Suche an
            $this->applyFuzzySearchToQuery($baseQueryForFacet);

            // 2. Wende ALLE ANDEREN Filter an
            foreach ($appliedFilters as $otherKey => $value) {
                if ($key !== $otherKey && isset($this->facets[$otherKey])) {
                    $this->facets[$otherKey]->applyFilter($baseQueryForFacet, $value);
                }
            }

            // 3. Hole die Optionen für die aktuelle Facette
            $facetData[$key] = [
                'label' => $facet->label,
                'options' => $facet->getFacetOptions($baseQueryForFacet),
            ];
        }

        return $facetData;
    }

    // Hilfsmethode, um Fuzzy-Suche auf beliebige Queries anzuwenden
    protected function applyFuzzySearchToQuery(Builder $query): void
    {
        $term = $this->input['q'] ?? null;
        if (! $term || empty($this->config['searchable'])) {
            return;
        }

        $query->where(function (Builder $q) use ($term): void {
            foreach ($this->config['searchable'] as $field) {
                $q->orWhere($field, 'LIKE', "%{$term}%");
            }
        });
    }
}
