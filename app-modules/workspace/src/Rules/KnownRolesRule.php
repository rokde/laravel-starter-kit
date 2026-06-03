<?php

declare(strict_types=1);

namespace Modules\Workspace\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Modules\Workspace\Models\RoleRegistry;

class KnownRolesRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=):PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! in_array($value, RoleRegistry::roleKeys())) {
            $fail('validation.role')->translate();
        }
    }
}
