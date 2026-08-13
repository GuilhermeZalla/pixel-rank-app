<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:100'],
            'nickname' => ['string', 'max:35', 'unique:users,nickname'],
            'email' => ['string', 'email', 'max:255', 'unique:users'],
            'password' => ['string', 'confirmed', Password::defaults()],
            'bio' => ['nullable', 'string', 'max:160'],
            'avatar' => ['nullable', 'image', File::types(['jpg', 'jpeg', 'png', 'gif'])->max(1024)],
        ];
    }
}
