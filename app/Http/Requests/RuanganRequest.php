<?php

namespace App\Http\Requests;

use App\Models\Ruangan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RuanganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // dicek lewat Policy di controller
    }

    public function rules(): array
    {
        return [
            'nama_ruangan' => ['required', 'string', 'max:100'],
            'deskripsi' => ['required', 'string', 'max:2000'],
            'kapasitas' => ['required', 'integer', 'min:1', 'max:100000'],
            'lokasi' => ['nullable', 'string', 'max:150'],
            'fasilitas' => ['nullable', 'string', 'max:1000'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'hapus_foto' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in([Ruangan::STATUS_TERSEDIA, Ruangan::STATUS_TIDAK_TERSEDIA])],
        ];
    }
}
