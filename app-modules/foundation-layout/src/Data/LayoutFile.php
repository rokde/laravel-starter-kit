<?php

namespace Modules\FoundationLayout\Data;

class LayoutFile
{
    public function __construct(
        private readonly string $filename,
        private readonly string $pattern,
        private readonly string $replacement,
    )
    {
    }

    public function canBeModified(): bool
    {
        return file_exists($this->filename)
            && preg_match($this->pattern, file_get_contents($this->filename)) !== false;
    }

    public function modify(): void
    {
        $content = file_get_contents($this->filename);
        $content = preg_replace($this->pattern, $this->replacement, $content);
        file_put_contents($this->filename, $content);
    }
}
