<?php

declare(strict_types=1);

namespace Modules\Todo\Data\Facets;

use App\Data\Facets\AbstractFacet;

class TodoDueDateFacet extends AbstractFacet
{
    public function __construct(string $label, string $key = 'due_date')
    {
        parent::__construct($label, $key);

        $this->options = [
            [
                'label' => __('Without due date'),
                'value' => 'none',
            ],
            [
                'label' => __('Overdue'),
                'value' => 'past',
            ],
            [
                'label' => __('On time'),
                'value' => 'future',
            ],
        ];
    }
}
