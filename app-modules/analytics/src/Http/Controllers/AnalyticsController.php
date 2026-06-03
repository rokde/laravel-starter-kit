<?php

declare(strict_types=1);

namespace Modules\Analytics\Http\Controllers;

use App\Enums\SortDirection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Pan\Contracts\AnalyticsRepository;

class AnalyticsController
{
    public function index(Request $request, AnalyticsRepository $repository): Response
    {
        $analytics = collect($repository->all());

        $sortColumn = null;
        $sortDirection = null;
        if ($request->has('sort')) {
            $sortColumn = mb_ltrim($request->get('sort'), '-');
            $sortDirection = SortDirection::fromString($request->get('sort'));
        }

        if (! in_array($sortColumn, [null, '', '0'], true)) {
            $analytics = $analytics->sortBy($sortColumn, descending: $sortDirection === SortDirection::DESC);
        }

        $flows = config('analytics.flows', []);

        $resultFlow = [];

        foreach ($flows as $flow => $steps) {
            $index = count($resultFlow);
            $resultFlow[$index] = [
                'name' => $flow,
                'steps' => [],
                'clicks' => 0,
            ];

            foreach ($steps as $step) {
                $clicks = 0;
                foreach ((array) $step as $subStep) {
                    $clicks += $analytics->pluck('clicks', 'name')->get($subStep, 0);
                }

                $resultFlow[$index]['steps'][] = [
                    'name' => implode(', ', (array) $step),
                    'clicks' => $clicks,
                ];
                $resultFlow[$index]['clicks'] += $clicks;
            }
        }

        return Inertia::render('analytics::Analytics', [
            'analytics' => $analytics->values(),
            'sort' => [
                'column' => $sortColumn,
                'direction' => $sortDirection,
            ],
            'flows' => $resultFlow,
        ]);
    }

    public function destroy(AnalyticsRepository $repository): RedirectResponse
    {
        $repository->flush();

        return back()
            ->with('message', __('Analytics data has been deleted.'));
    }
}
