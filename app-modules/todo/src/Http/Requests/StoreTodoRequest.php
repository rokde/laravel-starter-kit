<?php

declare(strict_types=1);

namespace Modules\Todo\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Todo\Models\Todo;

class StoreTodoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $workspace = $this->user()?->currentWorkspace;

        if ($workspace === null) {
            return false;
        }

        // Check if the user is authorized to create a todo for the specified user
        return $this->user()->can('create', [
            Todo::class,
            $workspace,
            $this->input('user_id'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'user_id' => ['required', 'exists:users,id'],
            'completed' => ['boolean'],
            'due_date' => ['nullable', 'date'],
        ];
    }

    /**
     * Get the user ID from the request.
     */
    public function userId(): int
    {
        return $this->user()->id;
    }
}
