<?php

declare(strict_types=1);

namespace Modules\ModelFacets\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\ModelFacets\Facets\BooleanFacet;
use Modules\ModelFacets\Facets\RangeFacet;
use Modules\ModelFacets\Facets\TextFacet;
use Modules\ModelFacets\Models\Concerns\HasFacetSearch;

class Product extends Model
{
    use HasFacetSearch;

    protected $table = 'test__products';

    protected $guarded = [];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getFacetSearchConfig(): array
    {
        return [
            'searchable' => ['name', 'description'],
            'sortable' => ['price', 'name'],
            'facets' => [
                'brand' => [
                    'class' => TextFacet::class,
                    'label' => 'Marke',
                    'value_field' => 'brand',
                ],
                'price' => [
                    'class' => RangeFacet::class,
                    'label' => 'Preis',
                    'value_field' => 'price',
                ],
                'color' => [
                    'class' => TextFacet::class,
                    'label' => 'Farbe',
                    'value_field' => 'color',
                    'null_label' => 'Nicht angegeben',
                    'null_value_placeholder' => '_null_',
                ],
                'available' => [
                    'class' => TextFacet::class,
                    'label' => 'Verfügbarkeit',
                    'value_field' => 'is_available',
                    'label_resolver' => fn (Product $row): string => $row->value ? 'Ja' : 'Nein',
                ],
                'is_available_as_boolean' => [
                    'class' => BooleanFacet::class,
                    'label' => 'Verfügbarkeit',
                    'value_field' => 'is_available',
                    'label_true' => 'Auf Lager',
                    'label_false' => 'Ausverkauft',
                ],
            ],
        ];
    }
}
