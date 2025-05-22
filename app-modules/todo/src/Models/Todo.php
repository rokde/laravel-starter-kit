<?php

declare(strict_types=1);

namespace Modules\Todo\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Todo\Database\Factories\TodoFactory;
use Modules\Workspace\Models\Workspace;

#[UseFactory(TodoFactory::class)]
class Todo extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'completed',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'completed' => 'boolean',
    ];

    /**
     * Get the workspace that the todo belongs to.
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Get the user that is assigned to the todo.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
