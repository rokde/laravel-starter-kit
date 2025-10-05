<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Str;

final readonly class StaticPage
{
    public function __construct(
        private string $content,
        private FrontMatter $data,
    ) {}

    public function get(string $key, mixed $default = null): mixed
    {
        if ($key === 'content') {
            return $this->content;
        }

        return $this->data->get($key, $default);
    }

    /**
     * @see https://commonmark.thephpleague.com/security/ for options
     */
    public function getHtml(array $options = ['allow_unsafe_links' => false], array $extensions = []): string
    {
        return Str::of($this->content)->markdown($options, $extensions)->value();
    }
}
