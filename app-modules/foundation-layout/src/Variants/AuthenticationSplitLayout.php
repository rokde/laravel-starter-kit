<?php

namespace Modules\FoundationLayout\Variants;

use Modules\FoundationLayout\Data\LayoutFile;
use Modules\FoundationLayout\Data\LayoutVariant;

class AuthenticationSplitLayout extends Variant
{
    public function __construct()
    {
        $this->variant = new LayoutVariant();
        $this->variant->add(new LayoutFile(
            filename: resource_path('js/layouts/AuthLayout.vue'),
            pattern: "~import AuthLayout from '@\/layouts\/auth\/AuthSimpleLayout\.vue';~",
            replacement: "import AuthLayout from '@/layouts/auth/AuthSplitLayout.vue';",
        ));
    }
}
