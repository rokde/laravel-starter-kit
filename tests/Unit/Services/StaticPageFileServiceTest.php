<?php

declare(strict_types=1);

use App\Models\StaticPage;
use App\Services\StaticPageFileService;
use Illuminate\Support\Facades\File;

beforeEach(function (): void {
    $this->service = new StaticPageFileService();
});

test('it can parse content without front matter', function (): void {
    $content = "# Hello World\n\nThis is a test.";

    $result = $this->service->parse($content);

    expect($result)->toBeInstanceOf(StaticPage::class)
        ->and($result->get('content'))->toBe($content);
});

test('it can parse content with front matter', function (): void {
    $content = <<<'EOT'
---
title: Test Title
description: Test Description
---
# Hello World

This is a test.
EOT;

    $result = $this->service->parse($content);

    expect($result)->toBeInstanceOf(StaticPage::class)
        ->and($result->get('title'))->toBe('Test Title')
        ->and($result->get('description'))->toBe('Test Description')
        ->and($result->get('content'))->toContain('# Hello World');
});

test('it can parse content with multiple front matter delimiters', function (): void {
    $content = <<<'EOT'
---
title: Test Title
---
# Hello World

This is a test with --- in the content.

---
This is still part of the content.
EOT;

    $result = $this->service->parse($content);

    expect($result)->toBeInstanceOf(StaticPage::class)
        ->and($result->get('title'))->toBe('Test Title')
        ->and($result->get('content'))->toContain('# Hello World')
        ->and($result->get('content'))->toContain('This is a test with --- in the content.')
        ->and($result->get('content'))->toContain('This is still part of the content.');
});

test('it can parse file', function (): void {
    // Create a temporary file
    $tempFile = tempnam(sys_get_temp_dir(), 'test_');
    $content = <<<'EOT'
---
title: Test Title
description: Test Description
---
# Hello World

This is a test.
EOT;
    file_put_contents($tempFile, $content);

    // Mock the file_get_contents function
    $result = $this->service->parseFile($tempFile);

    expect($result)->toBeInstanceOf(StaticPage::class)
        ->and($result->get('title'))->toBe('Test Title')
        ->and($result->get('description'))->toBe('Test Description')
        ->and($result->get('content'))->toContain('# Hello World');

    // Clean up
    unlink($tempFile);
});
