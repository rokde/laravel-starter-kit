<?php

declare(strict_types=1);

namespace Modules\Todo\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Todo\Models\Todo;

class UpdateTodoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $todo = $this->route('todo');

        if (! $todo instanceof Todo) {
            return false;
        }

        // Check if the user is authorized to update the todo using the policy
        return $this->user()->can('update', $todo);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'user_id' => ['sometimes', 'exists:users,id'],
            'completed' => ['sometimes', 'boolean'],
            'due_date' => ['nullable', 'date'],
        ];
    }
}
