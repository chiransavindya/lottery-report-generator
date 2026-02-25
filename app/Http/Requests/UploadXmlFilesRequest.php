<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadXmlFilesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow operators and admins (auth checked by middleware)
        return $this->user() && in_array($this->user()->role, ['operator', 'admin', 'super_admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     * Updated v2.0: Allow up to 16 files for multi-date batch uploads.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'files' => ['required', 'array', 'min:1', 'max:16'],
            'files.*' => ['required', 'file', 'mimes:xml,text/xml', 'max:10240'], // 10MB max
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'files.required' => 'Please select XML files to upload.',
            'files.min' => 'You must upload at least 1 XML file.',
            'files.max' => 'You can upload a maximum of 16 XML files at once.',
            'files.*.required' => 'All files are required.',
            'files.*.file' => 'Each upload must be a valid file.',
            'files.*.mimes' => 'Only XML files are allowed.',
            'files.*.max' => 'Each file must not exceed 10MB.',
        ];
    }
}
