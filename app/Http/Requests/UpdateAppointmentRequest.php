<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            // Status is specific to update
            'appstatus'     => 'required|string|in:pending,approved,completed,declined,rejected,cancelled',
            'appdetails'    => 'required|string|min:10|max:255',
            'contactnumber' => ['required', 'string', 'regex:/^[0-9]+$/', 'digits_between:10,15'],
            // Validation for adding multiple new images (similar to your Store Request)
            'images'          => 'nullable|array|max:8', // Optional: set a max limit per upload
            'images.*'        => 'image|mimes:jpg,jpeg,png,webp,gif|max:5120',
        ];
    }
}
