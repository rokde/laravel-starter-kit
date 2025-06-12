<?php

declare(strict_types=1);

namespace App\Models;

use App\DataTransferObjects\User as UserDto;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Stringable;
use Modules\Workspace\Models\Concerns\ManagesWorkspaces;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\LaravelPasskeys\Models\Concerns\InteractsWithPasskeys;

#[UseFactory(UserFactory::class)]
class User extends Authenticatable implements HasLocalePreference, HasPasskeys, MustVerifyEmail
{
    use HasFactory, InteractsWithPasskeys, ManagesWorkspaces, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'locale',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function preferredLocale(): string
    {
        if ($this->locale instanceof Stringable) {
            return $this->locale->toString();
        }

        return $this->locale ?? config('app.fallback_locale', 'en');
    }

    public function toDto(): UserDto
    {
        return new UserDto(
            id: $this->id,
            name: $this->name,
            email: $this->email,
            verified: $this->hasVerifiedEmail(),
            locale: $this->preferredLocale(),
            timezone: $this->timezone,
        );
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'preferred_notification_channels' => AsArrayObject::class,
        ];
    }
}
