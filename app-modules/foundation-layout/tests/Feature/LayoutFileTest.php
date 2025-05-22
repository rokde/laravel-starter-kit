<?php

declare(strict_types=1);

use Modules\FoundationLayout\Data\LayoutFile;

beforeEach(function (): void {
    $this->tempFile = tempnam(sys_get_temp_dir(), 'layout_test_');
    file_put_contents($this->tempFile, 'import AuthLayout from \'@/layouts/auth/AuthSplitLayout.vue\';');
});

afterEach(function (): void {
    if (file_exists($this->tempFile)) {
        unlink($this->tempFile);
    }
});

test('layout file can be modified when file exists and pattern matches', function (): void {
    $layoutFile = new LayoutFile(
        filename: $this->tempFile,
        pattern: "~import AuthLayout from '@\/layouts\/auth\/AuthSplitLayout\.vue';~",
        replacement: "import AuthLayout from '@/layouts/auth/AuthSimpleLayout.vue';",
    );

    expect($layoutFile->canBeModified())->toBeTrue();
});

test('layout file cannot be modified when file does not exist', function (): void {
    $layoutFile = new LayoutFile(
        filename: 'non_existent_file.txt',
        pattern: "~import AuthLayout from '@\/layouts\/auth\/AuthSplitLayout\.vue';~",
        replacement: "import AuthLayout from '@/layouts/auth/AuthSimpleLayout.vue';",
    );

    expect($layoutFile->canBeModified())->toBeFalse();
});

test('layout file cannot be modified when pattern does not match', function (): void {
    $layoutFile = new LayoutFile(
        filename: $this->tempFile,
        pattern: "~non matching pattern~",
        replacement: "replacement",
    );

    expect($layoutFile->canBeModified())->toBeFalse();
});

test('layout file modify method replaces content correctly', function (): void {
    $layoutFile = new LayoutFile(
        filename: $this->tempFile,
        pattern: "~import AuthLayout from '@\/layouts\/auth\/AuthSplitLayout\.vue';~",
        replacement: "import AuthLayout from '@/layouts/auth/AuthSimpleLayout.vue';",
    );

    $layoutFile->modify();

    $content = file_get_contents($this->tempFile);
    expect($content)->toBe("import AuthLayout from '@/layouts/auth/AuthSimpleLayout.vue';");
});
