<?php

declare(strict_types=1);

namespace Modules\Workspace\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Workspace\Models\RoleRegistry;

class ModifyMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', Rule::exists('workspace_members', 'user_id')],
            'role' => ['required', 'string', 'in:'.implode(',', RoleRegistry::roleKeys())],
        ];
    }
}
