<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'alpha_dash', 'min:3', 'max:50', Rule::unique(User::class)->ignore($this->user()->id)],
            'email' => ['required', 'string', 'email', 'max:150', Rule::unique(User::class)->ignore($this->user()->id)],
            'monthly_budget' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'budget_warn_limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'theme_color' => ['nullable', 'string', Rule::in(['blue', 'emerald', 'indigo', 'violet', 'rose', 'teal', 'amber', 'slate'])],
            'dark_mode' => ['nullable', 'boolean'],
            'profile_pic' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }
}
