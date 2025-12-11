<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreAppointmentRequest extends FormRequest
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
            'upcycler_id'   => 'required|exists:users,id',
            'appdetails'    => 'required|string|min:10|max:255',
            'contactnumber' => ['required', 'string', 'regex:/^[0-9]+$/', 'digits_between:10,15'],
            'apptype'       => ['required', 'string', 'in:Resize,Customize,Patchwork,Fabric Dyeing'],
            'app_time'      => 'required|date_format:H:i',
            'appdate'       => 'required|date|after_or_equal:today',
            'images'        => 'required|array|min:1|max:8',
            'images.*'      => 'image|mimes:jpg,jpeg,png,webp,gif|max:5120',
        ];
    }
}
