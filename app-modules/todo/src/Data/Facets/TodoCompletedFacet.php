<?php

namespace Modules\Todo\Data\Facets;

use App\Data\Facets\AbstractFacet;

class TodoCompletedFacet extends AbstractFacet
{
    public function __construct(string $label, string $key = 'completed')
    {
        parent::__construct($label, $key);

        $this->options = [
            [
                'label' => __('Completed'),
                'value' => 'true',
            ],
            [
                'label' => __('Incomplete'),
                'value' => 'false',
            ],
        ];
    }
}
