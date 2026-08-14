<?php

namespace App\Http\Requests;

use App\ReviewRecommendation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewRequest extends FormRequest
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
            'game_id' => ['required', 'integer'],
            'game_name' => ['required', 'string'],
            'game_cover' => ['required', 'string'],
            'title' => ['required', 'min:8', 'max:120'],
            'recommendation' => ['required', Rule::enum(ReviewRecommendation::class)],
            'contains_spoilers' => ['boolean'],
            'body' => ['required', 'min: 150'],
            'rating' => ['required', 'numeric', 'between:0,10', 'regex:/^(10(\.0)?|[0-9](\.(0|5))?)$/'],
            'pros' => ['nullable', 'array', 'max:10'],
            'pros.*' => ['string', 'max:100'],
            'cons' => ['nullable', 'array', 'max:10'],
            'cons.*' => ['string', 'max:100'],
            'platform_playable' => ['required', 'string']
        ];
    }
}
