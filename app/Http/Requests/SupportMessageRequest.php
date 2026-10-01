<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupportMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'              => ['required', 'string', 'max:100'],
            'email'             => ['required', 'email', 'max:150'],
            'subject'           => ['required', 'string', 'max:255'],
            'message'           => ['required', 'string', 'min:5', 'max:3000'],
            'attachments'       => ['nullable', 'array', 'max:5'],
            'attachments.*'     => ['image', 'mimes:jpeg,jpg,png,webp,gif', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'attachments.max'    => 'You may attach up to 5 images.',
            'attachments.*.image' => 'Each attachment must be an image.',
            'attachments.*.max'  => 'Each image must not exceed 5 MB.',
        ];
    }
}
