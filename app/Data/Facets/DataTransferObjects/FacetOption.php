<?php

declare(strict_types=1);

namespace App\Data\Facets\DataTransferObjects;

final readonly class FacetOption
{
    public function __construct(
        public string $label,
        public string $value,
        public int $count = 0,
    )
    {}
}
