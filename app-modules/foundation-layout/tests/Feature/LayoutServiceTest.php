<?php

declare(strict_types=1);

use Modules\FoundationLayout\Service\LayoutService;

test('layout service provides authentication layout variants', function (): void {
    $layoutService = new LayoutService();

    $variants = $layoutService->getAuthenticationLayoutVariants();

    expect($variants)->toBeArray()
        ->and($variants)->toContain('simple')
        ->and($variants)->toContain('split');
});

test('layout service provides application layout variants', function (): void {
    $layoutService = new LayoutService();

    $variants = $layoutService->getApplicationLayoutVariants();

    expect($variants)->toBeArray()
        ->and($variants)->toContain('header')
        ->and($variants)->toContain('sidebar-inset')
        ->and($variants)->toContain('sidebar-sidebar')
        ->and($variants)->toContain('sidebar-floating');
});

test('layout service provides default authentication layout variant', function (): void {
    $layoutService = new LayoutService();

    $defaultVariant = $layoutService->getDefaultAuthenticationLayoutVariant();

    expect($defaultVariant)->toBeString()
        ->and($defaultVariant)->toBe('simple');
});

test('layout service provides default application layout variant', function (): void {
    $layoutService = new LayoutService();

    $defaultVariant = $layoutService->getDefaultApplicationLayoutVariant();

    expect($defaultVariant)->toBeString()
        ->and($defaultVariant)->toBe('header');
});

test('layout service throws exception for invalid authentication layout variant', function (): void {
    $layoutService = new LayoutService();

    expect(fn () => $layoutService->configureAuthenticationLayout('invalid'))
        ->toThrow(InvalidArgumentException::class, 'Invalid variant');
});

test('layout service throws exception for invalid application layout variant', function (): void {
    $layoutService = new LayoutService();

    expect(fn () => $layoutService->configureApplicationLayout('invalid'))
        ->toThrow(InvalidArgumentException::class, 'Invalid variant');
});
