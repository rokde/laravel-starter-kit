<?php

namespace Modules\FoundationLayout\Variants;

use Modules\FoundationLayout\Data\LayoutVariant;

abstract class Variant
{

    protected LayoutVariant $variant;

    public function proceed(): void
    {
        foreach ($this->variant->filesToModify() as $layoutFile) {
            if (!$layoutFile->canBeModified()) {
                continue;
            }

            $layoutFile->modify();
        }
    }
}
