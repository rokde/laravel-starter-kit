<?php

declare(strict_types=1);

use App\Models\FrontMatter;
use App\Models\StaticPage;

test('it can be instantiated with content and front matter', function (): void {
    $content = '# Test Content';
    $frontMatter = new FrontMatter(['title' => 'Test Title']);

    $staticPage = new StaticPage($content, $frontMatter);

    expect($staticPage)->toBeInstanceOf(StaticPage::class);
});

test('it can get content using get method', function (): void {
    $content = '# Test Content';
    $frontMatter = new FrontMatter(['title' => 'Test Title']);

    $staticPage = new StaticPage($content, $frontMatter);

    expect($staticPage->get('content'))->toBe($content);
});

test('it can get front matter data using get method', function (): void {
    $content = '# Test Content';
    $frontMatter = new FrontMatter([
        'title' => 'Test Title',
        'meta' => ['description' => 'Test Description'],
    ]);

    $staticPage = new StaticPage($content, $frontMatter);

    expect($staticPage->get('title'))->toBe('Test Title');
    expect($staticPage->get('meta.description'))->toBe('Test Description');
    expect($staticPage->get('non_existent'))->toBeNull();
    expect($staticPage->get('non_existent', 'default'))->toBe('default');
});

test('it can convert markdown content to HTML', function (): void {
    $content = '# Test Heading';
    $frontMatter = new FrontMatter(['title' => 'Test Title']);

    $staticPage = new StaticPage($content, $frontMatter);

    $html = $staticPage->getHtml();

    expect($html)->toContain('<h1>Test Heading</h1>');
});

test('it respects markdown conversion options', function (): void {
    $content = '[Link](javascript:alert("XSS"))';
    $frontMatter = new FrontMatter(['title' => 'Test Title']);

    $staticPage = new StaticPage($content, $frontMatter);

    // Default: unsafe links not allowed
    $safeHtml = $staticPage->getHtml();
    expect($safeHtml)->not()->toContain('javascript:alert');

    // With unsafe links allowed
    $unsafeHtml = $staticPage->getHtml(['allow_unsafe_links' => true]);
    expect($unsafeHtml)->toContain('javascript:alert');
});
