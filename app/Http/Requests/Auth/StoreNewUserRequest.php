<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Data\Timezones;
use App\DataTransferObjects\NewUser;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

final class StoreNewUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    public function toDto(): NewUser
    {
        return new NewUser(
            name: $this->validated('name'),
            email: $this->validated('email'),
            locale: $this->locale(),
            timezone: $this->timezone(),
        );
    }

    private function locale(): string
    {
        $locale = null;
        $requestLocale = $this->string('locale')->value();
        if (in_array($requestLocale, config('app.locales', [config('app.fallback_locale', 'en')]))) {
            $locale = $requestLocale;
        }

        return mb_strtolower($locale ?? config('app.locale', 'en'));
    }

    private function timezone(): string
    {
        $timezone = null;
        $requestTimezone = $this->string('timezone')->value();
        if (in_array($requestTimezone, Timezones::identifiers())) {
            $timezone = $requestTimezone;
        }

        return $timezone ?? 'UTC';
    }
}
