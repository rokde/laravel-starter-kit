<?php

declare(strict_types=1);

namespace Modules\Todo\Data\Facets;

use App\Data\Facets\AbstractFacet;
use App\Data\Facets\FilterValueEnum;

class TodoCompletedFacet extends AbstractFacet
{
    public function __construct(string $label, string $key = 'completed')
    {
        parent::__construct($label, $key);

        $this->options = [
            [
                'label' => __('Completed'),
                'value' => FilterValueEnum::TRUE->value,
            ],
            [
                'label' => __('Incomplete'),
                'value' => FilterValueEnum::FALSE->value,
            ],
        ];
    }
}
