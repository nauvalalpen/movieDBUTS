<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request untuk validasi pembuatan (penyimpanan) film baru.
 *
 * REF.: Logika validasi untuk penyimpanan film baru dipindahkan dari MovieController.
 */
class MovieStoreRequest extends FormRequest
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
     * Aturan ini spesifik untuk membuat film baru.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // ID film: wajib diisi, string, max 255 char, dan harus unik di tabel 'movies'.
            'id' => ['required', 'string', 'max:255', Rule::unique('movies', 'id')],
            'judul' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'sinopsis' => 'required|string',
            'tahun' => 'required|integer',
            'pemain' => 'required|string',
            // Foto sampul: wajib diisi saat membuat film baru, harus gambar, format & ukuran tertentu.
            'foto_sampul' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}