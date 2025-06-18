<?php

declare(strict_types=1);

namespace Modules\CustomProperties\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\CustomProperties\Models\CustomPropertyDefinition;

/**
 * @uses \Eloquent
 */
trait DefinesCustomProperties
{
    abstract public function getCustomizableItemsRelation(): HasMany;

    public function customPropertyDefinitions(): MorphMany
    {
        return $this->morphMany(CustomPropertyDefinition::class, 'definable');
    }
}
