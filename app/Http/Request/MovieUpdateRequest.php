<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
// use Illuminate\Validation\Rule; // Dihapus karena tidak digunakan di aturan di bawah

/**
 * Form Request untuk validasi pembaruan data film.
 *
 * REF.: Logika validasi untuk pembaruan film dipindahkan dari MovieController.
 */
class MovieUpdateRequest extends FormRequest
{
    /**
     * Menentukan apakah pengguna diizinkan untuk membuat request ini.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Izinkan semua request secara default. Otorisasi spesifik bisa ditambahkan di sini.
        return true;
    }

    /**
     * Mendapatkan aturan validasi yang diterapkan pada request.
     * Aturan ini untuk memperbarui data film yang sudah ada.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'judul' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'sinopsis' => 'required|string',
            'tahun' => 'required|integer',
            'pemain' => 'required|string',
            // Foto sampul bersifat opsional saat pembaruan.
            // Validasi gambar hanya berlaku jika ada file yang diunggah ('sometimes').
            'foto_sampul' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}