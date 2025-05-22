<?php

declare(strict_types=1);

use Illuminate\Console\Command;
use Modules\FoundationLayout\Console\Commands\ConfigureLayoutsCommand;
use Modules\FoundationLayout\Service\LayoutService;

test('configure layouts command can be instantiated', function (): void {
    $command = new ConfigureLayoutsCommand();

    expect($command)->toBeInstanceOf(ConfigureLayoutsCommand::class)
        ->and($command)->toBeInstanceOf(Command::class);
});

test('configure layouts command handle method configures layouts', function (): void {
    $layoutService = Mockery::mock(LayoutService::class);
    $layoutService->shouldReceive('getAuthenticationLayoutVariants')->once()->andReturn(['simple', 'split']);
    $layoutService->shouldReceive('getDefaultAuthenticationLayoutVariant')->once()->andReturn('simple');
    $layoutService->shouldReceive('configureAuthenticationLayout')->once()->with('simple');
    $layoutService->shouldReceive('getApplicationLayoutVariants')->once()->andReturn(['header', 'sidebar-inset']);
    $layoutService->shouldReceive('getDefaultApplicationLayoutVariant')->once()->andReturn('header');
    $layoutService->shouldReceive('configureApplicationLayout')->once()->with('header');

    $command = new ConfigureLayoutsCommand();

    // Mock the choice method to return predefined values
    $command = Mockery::mock(ConfigureLayoutsCommand::class)->makePartial();
    $command->shouldReceive('choice')->twice()->andReturn('simple', 'header');
    $command->shouldReceive('info')->times(3);

    $result = $command->handle($layoutService);

    expect($result)->toBe(Command::SUCCESS);
});

afterEach(function (): void {
    Mockery::close();
});
