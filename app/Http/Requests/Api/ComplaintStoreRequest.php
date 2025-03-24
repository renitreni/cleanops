<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ComplaintStoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'attachments1.*' => 'file|mimes:jpeg,png,jpg,pdf,doc,docx|max:2048',
            'attachments2.*' => 'file|mimes:jpeg,png,jpg,pdf,doc,docx|max:2048',
            'attachments3.*' => 'file|mimes:jpeg,png,jpg,pdf,doc,docx|max:2048',
            'attachments4.*' => 'file|mimes:jpeg,png,jpg,pdf,doc,docx|max:2048',
            'lat' => 'required|string|max:255',
            'lang' => 'required|string|max:255',
            'status' => 'required|in:pending, progress, resolved, rejected',
            'message' => 'required|string',
        ];
    }
}
