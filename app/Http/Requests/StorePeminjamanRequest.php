<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePeminjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // dilindungi middleware guard mahasiswa
    }

    protected function prepareForValidation(): void
    {
        // Normalisasi nomor telepon ke format lokal 08xxxxxxxxxx (kolom varchar(13)).
        $no = preg_replace('/[^\d+]/', '', (string) $this->no_telfon);
        $no = preg_replace('/^(\+62|62)/', '0', $no);
        $this->merge(['no_telfon' => $no]);
    }

    public function rules(): array
    {
        return [
            'nama_pengaju' => ['required', 'string', 'max:100'],
            'id_ruangan' => ['required', 'integer', 'exists:ruangan,id_ruangan'],
            'tanggal_mulai' => ['required', 'date', 'after:now'],
            'tanggal_selesai' => ['required', 'date', 'after:tanggal_mulai'],
            'alasan' => ['required', 'string', 'max:1000'],
            'jumlah_peserta' => ['required', 'integer', 'min:1'],
            'no_telfon' => ['required', 'regex:/^08\d{8,11}$/', 'max:13'],
            'dokumen_pendukung' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal_mulai.after' => 'Tanggal mulai harus di masa depan.',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai.',
            'jumlah_peserta.min' => 'Jumlah peserta harus lebih dari 0.',
            'no_telfon.regex' => 'Nomor telepon tidak valid (contoh: 081234567890).',
            'dokumen_pendukung.mimes' => 'Dokumen harus berformat PDF, JPG, atau PNG.',
            'dokumen_pendukung.max' => 'Ukuran dokumen maksimal 5 MB.',
        ];
    }
}
