<?php

declare(strict_types=1);

namespace Modules\ModelFacets\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;
use Modules\ModelFacets\DataTransferObjects\FacetSearchResult;
use Modules\ModelFacets\FacetSearchManager;

class ModelFacetsServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Builder::macro('withFacetSearch', function (array $input): FacetSearchResult {
            /** @var Builder $this */
            return (new FacetSearchManager($this, $input))->getResult();
        });
    }
}
