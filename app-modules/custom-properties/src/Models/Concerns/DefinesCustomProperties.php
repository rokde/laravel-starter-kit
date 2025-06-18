<?php

namespace Modules\CustomProperties\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\CustomProperties\Models\CustomPropertyDefinition;

/**
 * @uses \Eloquent
 */
trait DefinesCustomProperties
{
    public function customPropertyDefinitions(): MorphMany
    {
        return $this->morphMany(CustomPropertyDefinition::class, 'definable');
    }
}
