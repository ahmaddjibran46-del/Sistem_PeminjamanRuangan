<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KeputusanPengajuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // dicek lewat Policy di controller
    }

    public function rules(): array
    {
        // Menolak WAJIB disertai catatan (Rule 6); menyetujui opsional.
        $wajib = $this->routeIs('admin.pengajuan.tolak') ? 'required' : 'nullable';

        return ['catatan_admin' => [$wajib, 'string', 'max:1000']];
    }

    public function messages(): array
    {
        return ['catatan_admin.required' => 'Catatan wajib diisi saat menolak pengajuan.'];
    }
}
