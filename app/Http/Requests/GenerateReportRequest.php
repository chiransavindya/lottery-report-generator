<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Report;

class GenerateReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Operators and admins can generate reports
        return $this->user() && in_array($this->user()->role, ['operator', 'admin', 'super_admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'draw_id' => [
                'required',
                'exists:draws,id',
                function ($attribute, $value, $fail) {
                    // Check if report already exists for this draw
                    if (Report::where('draw_id', $value)->exists()) {
                        $fail('A report has already been generated for this draw.');
                    }
                }
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'draw_id.required' => 'Please select a draw.',
            'draw_id.exists' => 'Selected draw does not exist.',
        ];
    }
}
