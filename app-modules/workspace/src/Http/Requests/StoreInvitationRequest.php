<?php

declare(strict_types=1);

namespace Modules\Workspace\Http\Requests;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Modules\Workspace\Models\WorkspaceInvitation;
use Modules\Workspace\Rules\KnownRolesRule;

class StoreInvitationRequest extends FormRequest
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
        $workspace = auth()->user()->currentWorkspace;

        return [
            'email' => ['required', 'string', 'max:255', 'email', Rule::unique(WorkspaceInvitation::class)->where(function (Builder $query) use ($workspace): void {
                $query->where('workspace_id', $workspace->id);
            })],
            'role' => ['required', 'string', new KnownRolesRule()],
        ];
    }

    public function after(): array
    {
        $workspace = auth()->user()->currentWorkspace;

        return [
            function (Validator $validator) use ($workspace): void {
                $validator->errors()->addIf(
                    $workspace->hasUserWithEmail($this->getEmail()),
                    'email',
                    __('This user already belongs to the workspace.'),
                );
            },
        ];
    }

    public function messages(): array
    {
        return [
            'role.unique' => __('This user has already been invited to the workspace.'),
        ];
    }

    public function getEmail(): string
    {
        return Str::of($this->validated('email'))->lower()->trim()->toString();
    }
}
