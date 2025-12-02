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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Allow all possible appointment statuses, including declined
            'appstatus' => 'required|string|in:pending,approved,completed,declined,rejected',
            'appdetails' => 'nullable|string|min:15|max:255',
            'contactnumber' => 'nullable|numeric|digits_between:10,15',
        ];
    }
}
