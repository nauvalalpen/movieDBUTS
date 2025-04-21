<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Service untuk menangani operasi terkait file (khususnya gambar).
 *
 * REF.: Logika penanganan unggah dan hapus file diekstrak dari MovieController.
 */
class FileService
{
    /**
     * Menyimpan file gambar yang diunggah ke direktori publik yang ditentukan.
     * Menghasilkan nama file acak (UUID) untuk menghindari konflik nama.
     *
     * @param UploadedFile $image Objek file yang diunggah dari request.
     * @param string $directory Nama direktori tujuan (relatif terhadap folder public). Contoh: 'images/covers'.
     * @return string Nama file yang baru saja disimpan (termasuk ekstensinya).
     */
    public function saveImage(UploadedFile $image, string $directory): string
    {
        // Buat nama acak menggunakan UUID
        $randomName = Str::uuid()->toString();
        // Ambil ekstensi asli file
        $fileExtension = $image->getClientOriginalExtension();
        // Gabungkan nama acak dan ekstensi
        $fileName = $randomName . '.' . $fileExtension;

        // Pindahkan file yang diunggah ke direktori tujuan di dalam folder public
        $image->move(public_path($directory), $fileName);

        return $fileName; // Kembalikan nama file yang disimpan
    }

    /**
     * Menghapus file gambar dari direktori publik yang ditentukan.
     * Memeriksa apakah file tersebut ada sebelum mencoba menghapusnya.
     *
     * @param string $fileName Nama file yang akan dihapus (termasuk ekstensi).
     * @param string $directory Nama direktori tempat file berada (relatif terhadap folder public).
     * @return bool True jika file berhasil dihapus atau tidak ditemukan, false jika terjadi error saat menghapus.
     */
    public function deleteImage(string $fileName, string $directory): bool
    {
        // Bentuk path lengkap ke file gambar di folder public
        $imagePath = public_path($directory . '/' . $fileName);

        // Cek apakah file ada
        if (File::exists($imagePath)) {
            // Hapus file jika ada
            return File::delete($imagePath);
        }

        // Jika file tidak ditemukan, anggap operasi "penghapusan" berhasil (karena file memang tidak ada)
        return true; // Diubah dari false menjadi true untuk konsistensi (jika tujuan adalah memastikan file tidak ada)
                     // Jika ingin tahu apakah *proses* delete berjalan, kembalikan false. Pilih sesuai kebutuhan.
    }
}