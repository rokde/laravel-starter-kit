<?php

declare(strict_types=1);

use App\Models\FrontMatter;

test('it can be instantiated with empty data', function (): void {
    $frontMatter = new FrontMatter();

    expect($frontMatter)->toBeInstanceOf(FrontMatter::class);
});

test('it can be instantiated with data', function (): void {
    $data = ['title' => 'Test Title', 'description' => 'Test Description'];
    $frontMatter = new FrontMatter($data);

    expect($frontMatter)->toBeInstanceOf(FrontMatter::class);
});

test('it can get values using get method', function (): void {
    $data = ['title' => 'Test Title', 'meta' => ['description' => 'Test Description']];
    $frontMatter = new FrontMatter($data);

    expect($frontMatter->get('title'))->toBe('Test Title');
    expect($frontMatter->get('meta.description'))->toBe('Test Description');
    expect($frontMatter->get('non_existent'))->toBeNull();
    expect($frontMatter->get('non_existent', 'default'))->toBe('default');
});

test('it implements array access interface', function (): void {
    $data = ['title' => 'Test Title', 'description' => 'Test Description'];
    $frontMatter = new FrontMatter($data);

    // offsetExists
    expect(isset($frontMatter['title']))->toBeTrue();
    expect(isset($frontMatter['non_existent']))->toBeFalse();

    // offsetGet
    expect($frontMatter['title'])->toBe('Test Title');
    expect($frontMatter['description'])->toBe('Test Description');

    // offsetSet
    $frontMatter['new_key'] = 'New Value';
    expect($frontMatter['new_key'])->toBe('New Value');

    // offsetUnset
    unset($frontMatter['title']);
    expect(isset($frontMatter['title']))->toBeFalse();
});
