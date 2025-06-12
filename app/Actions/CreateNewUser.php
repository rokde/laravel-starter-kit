<?php

declare(strict_types=1);

namespace App\Actions;

use App\DataTransferObjects\NewUser;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use SensitiveParameter;

class CreateNewUser
{
    public function handle(
        NewUser $user,
        #[SensitiveParameter] string $password,
    ): User {
        $user = DB::transaction(fn (): User => User::create([
            'name' => $user->name,
            'email' => $user->email,
            'password' => Hash::make($password),
            'locale' => $user->locale,
            'timezone' => $user->timezone,
        ]));

        event(new Registered($user));

        return $user;
    }
}
