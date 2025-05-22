<?php

declare(strict_types=1);

namespace Modules\FoundationLayout\Data;

class LayoutFile
{
    public function __construct(
        private readonly string $filename,
        private readonly string $pattern,
        private readonly string $replacement,
    ) {}

    public function canBeModified(): bool
    {
        if (! file_exists($this->filename)) {
            return false;
        }

        $match = preg_match($this->pattern, file_get_contents($this->filename));
        return $match !== false
            && $match > 0;
    }

    public function modify(): void
    {
        $content = file_get_contents($this->filename);
        $content = preg_replace($this->pattern, $this->replacement, $content);
        file_put_contents($this->filename, $content);
    }
}
