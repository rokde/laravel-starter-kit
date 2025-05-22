<?php

declare(strict_types=1);

use Modules\FoundationLayout\Variants\ApplicationHeaderLayout;
use Modules\FoundationLayout\Variants\ApplicationSidebarFloatingLayout;
use Modules\FoundationLayout\Variants\ApplicationSidebarInsetLayout;
use Modules\FoundationLayout\Variants\ApplicationSidebarSidebarLayout;
use Modules\FoundationLayout\Variants\AuthenticationSimpleLayout;
use Modules\FoundationLayout\Variants\AuthenticationSplitLayout;

test('authentication simple layout can be instantiated', function (): void {
    $variant = new AuthenticationSimpleLayout();

    expect($variant)->toBeInstanceOf(AuthenticationSimpleLayout::class);
});

test('authentication split layout can be instantiated', function (): void {
    $variant = new AuthenticationSplitLayout();

    expect($variant)->toBeInstanceOf(AuthenticationSplitLayout::class);
});

test('application header layout can be instantiated', function (): void {
    $variant = new ApplicationHeaderLayout();

    expect($variant)->toBeInstanceOf(ApplicationHeaderLayout::class);
});

test('application sidebar floating layout can be instantiated', function (): void {
    $variant = new ApplicationSidebarFloatingLayout();

    expect($variant)->toBeInstanceOf(ApplicationSidebarFloatingLayout::class);
});

test('application sidebar inset layout can be instantiated', function (): void {
    $variant = new ApplicationSidebarInsetLayout();

    expect($variant)->toBeInstanceOf(ApplicationSidebarInsetLayout::class);
});

test('application sidebar sidebar layout can be instantiated', function (): void {
    $variant = new ApplicationSidebarSidebarLayout();

    expect($variant)->toBeInstanceOf(ApplicationSidebarSidebarLayout::class);
});
