<?php

declare(strict_types=1);

namespace Modules\Workspace\Http\Requests;

use App\ValueObjects\Id;
use Illuminate\Foundation\Http\FormRequest;

class StoreWorkspaceRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
        ];
    }

    public function userId(): Id
    {
        return new Id($this->user()->getAuthIdentifier());
    }
}
