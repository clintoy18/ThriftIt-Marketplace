<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreFeaturedBuyerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
          return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
    return [
        'name' => 'required|string|max:255',
        'handle' => 'nullable|string',
        'avatar' => 'nullable|image|max:2048',
        'items' => 'required|array|min:1',
        'items.*.product_name' => 'required|string',
        'items.*.price' => 'required|numeric',
        'items.*.image' => 'required|image|max:2048',
    ];
}
}
