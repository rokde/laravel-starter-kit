<?php

namespace Modules\Analytics\Http\Controllers;

use App\Enums\SortDirection;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Pan\Contracts\AnalyticsRepository;

class AnalyticsController
{
    public function __invoke(Request $request, AnalyticsRepository $repository): Response
    {
        $analytics = collect($repository->all());

        $sortColumn = null;
        $sortDirection = null;
        if ($request->has('sort')) {
            $sortColumn = mb_ltrim($request->get('sort'), '-');
            $sortDirection = SortDirection::fromString($request->get('sort'));
        }

        if ($sortColumn) {
            $analytics = $analytics->sortBy($sortColumn, descending: $sortDirection === SortDirection::DESC);
        }

        return Inertia::render('analytics::Analytics', [
            'analytics' => $analytics->values(),
            'sort' => [
                'column' => $sortColumn,
                'direction' => $sortDirection,
            ],
        ]);
    }
}
