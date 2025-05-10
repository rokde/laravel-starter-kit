<?php

namespace Modules\FoundationLayout\Data;

class LayoutVariant
{
    /**
     * @var array<int, LayoutFile>
     */
    private array $filesToModify;

    public function add(LayoutFile $file): self
    {
        $this->filesToModify[] = $file;

        return $this;
    }

    /**
     * returns the files to modify
     * @return array<int, LayoutFile>
     */
    public function filesToModify(): array
    {
        return $this->filesToModify;
    }
}
