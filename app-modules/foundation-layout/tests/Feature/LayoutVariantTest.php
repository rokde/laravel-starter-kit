<?php

declare(strict_types=1);

use Modules\FoundationLayout\Data\LayoutFile;
use Modules\FoundationLayout\Data\LayoutVariant;

test('layout variant can add layout files', function (): void {
    $layoutVariant = new LayoutVariant();
    $layoutFile = new LayoutFile(
        filename: 'test.txt',
        pattern: '~pattern~',
        replacement: 'replacement',
    );

    $result = $layoutVariant->add($layoutFile);

    expect($result)->toBeInstanceOf(LayoutVariant::class);
});

test('layout variant returns added files', function (): void {
    $layoutVariant = new LayoutVariant();
    $layoutFile1 = new LayoutFile(
        filename: 'test1.txt',
        pattern: '~pattern1~',
        replacement: 'replacement1',
    );
    $layoutFile2 = new LayoutFile(
        filename: 'test2.txt',
        pattern: '~pattern2~',
        replacement: 'replacement2',
    );

    $layoutVariant->add($layoutFile1);
    $layoutVariant->add($layoutFile2);

    $files = $layoutVariant->filesToModify();
    expect($files)->toBeArray()
        ->and($files)->toHaveCount(2)
        ->and($files[0])->toBeInstanceOf(LayoutFile::class)
        ->and($files[1])->toBeInstanceOf(LayoutFile::class);
});
