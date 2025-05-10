<?php

declare(strict_types=1);

namespace Modules\FoundationLayout\Variants;

use Modules\FoundationLayout\Data\LayoutFile;
use Modules\FoundationLayout\Data\LayoutVariant;

class ApplicationSidebarInsetLayout extends Variant
{
    public function __construct()
    {
        $this->variant = new LayoutVariant();
        $this->variant->add(new LayoutFile(
            filename: resource_path('js/layouts/AppLayout.vue'),
            pattern: "~import AppLayout from '@\/layouts\/app\/AppHeaderLayout\.vue';~",
            replacement: "import AppLayout from '@/layouts/app/AppSidebarLayout.vue';",
        ))
            ->add(new LayoutFile(
                filename: resource_path('js/components/AppSidebar.vue'),
                pattern: '~<Sidebar collapsible="icon" variant=".*?">~',
                replacement: '<Sidebar collapsible="icon" variant="inset">',
            ));
    }
}
