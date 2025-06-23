<?php

declare(strict_types=1);

namespace Modules\CustomProperties\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CustomPropertyDefinition extends Model
{
    use HasFactory;

    protected $fillable = [
        'definable_id',
        'definable_type',
        'name',
        'label',
        'type',
        'rules',
        'default_value',
        'options',
    ];

    protected $casts = [
        'rules' => 'array',
        'options' => 'array',
    ];

    public function definable(): MorphTo
    {
        return $this->morphTo();
    }
}
