<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_ruangan' => ['nullable', 'integer', 'exists:ruangan,id_ruangan'],
            'isi_feedback' => ['required', 'string', 'min:10', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'isi_feedback.required' => 'Isi feedback wajib diisi.',
            'isi_feedback.min' => 'Feedback minimal 10 karakter.',
            'isi_feedback.max' => 'Feedback maksimal 500 karakter.',
        ];
    }
}
