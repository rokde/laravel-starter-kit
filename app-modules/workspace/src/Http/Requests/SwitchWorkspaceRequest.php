<?php

namespace Modules\Workspace\Http\Requests;

use App\ValueObjects\Id;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SwitchWorkspaceRequest extends FormRequest
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
            'workspace_id' => ['required', Rule::exists('workspaces', 'id')],
        ];
    }

    public function userId(): Id
    {
        return new Id($this->user()->getAuthIdentifier());
    }

    public function workspaceId(): Id
    {
        return new Id($this->validated('workspace_id'));
    }
}
