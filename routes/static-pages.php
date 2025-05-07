<?php

use App\Services\StaticPageFileService;
use Inertia\Inertia;

collect([
    'en' => collect([
        'imprint' => resource_path('markdown/imprint.md'),
        'policy' => resource_path('markdown/policy.md'),
        'terms' => resource_path('markdown/terms.md'),
    ]),
    'de' => collect([
        'imprint' => resource_path('markdown/imprint.de.md'),
        'policy' => resource_path('markdown/policy.de.md'),
        'terms' => resource_path('markdown/terms.de.md'),
    ]),
])->get(auth()->user()?->preferredLocale() ?? app()->getLocale(), collect([]))
    ->each(function ($markdownFile, $path) {
        Route::get($path, function (StaticPageFileService $pageFileService) use ($path, $markdownFile) {
            try {
                $staticPage = $pageFileService->parseFile($markdownFile);
            } catch (\Throwable $e) {
                abort(404);
            }

            return Inertia::render('StaticPage', [
                'title' => $staticPage->get('title', Str::title($path)),
                'content' => $staticPage->getHtml(),
            ]);
        })->name('static.' . $path);
    });
