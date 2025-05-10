<?php

declare(strict_types=1);

namespace Modules\FoundationLayout\Variants;

use Modules\FoundationLayout\Data\LayoutVariant;

abstract class Variant
{
    protected LayoutVariant $variant;

    final public function proceed(): void
    {
        foreach ($this->variant->filesToModify() as $layoutFile) {
            if (! $layoutFile->canBeModified()) {
                continue;
            }

            $layoutFile->modify();
        }
    }
}
