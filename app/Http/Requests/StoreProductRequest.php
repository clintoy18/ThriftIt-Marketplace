<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreProductRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string|min:15',
            'price' => 'required|numeric|min:0',
            'size' => 'required|string',

            // --- CHANGED HERE ---
            // 1. Changed 'required' to 'nullable' (so it doesn't fail if you already have images in session)
            // 2. Removed 'min:2' (We check the total count in the Controller now)
            'images' => 'nullable|array|max:8',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp,gif|max:5120',

            'qty' => 'integer',
            'segment_id' => 'required|exists:segments,id',
            'barangay_id' => 'required|exists:barangays,id',
            'qr_code' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            // We removed the generic 'required' message since the controller handles the specific error now
            'images.array' => 'Images must be uploaded as an array.',
            'images.max' => 'You cannot upload more than 8 images.',
            'images.*.image' => 'Each file must be an image.',
            'images.*.mimes' => 'Images must be JPG, PNG, WEBP or GIF.',
            'images.*.max' => 'Each image cannot exceed 5MB.',
        ];
    }
}
