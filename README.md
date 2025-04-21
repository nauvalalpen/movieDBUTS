# Laravel Movie DB

## Dokumentasi Refactoring

`MovieController` telah di-refactor (diperbaiki strukturnya) untuk meningkatkan kualitas kode dan kemudahan pemeliharaan (maintainability). Berikut adalah rincian detail refactoring yang dilakukan:

### 1. Refactoring Logika Validasi

-   **Apa**: Memindahkan (mengekstrak) aturan validasi yang sama (duplikat) dari method `store` dan `update`.
-   **Bagaimana**: Membuat method `private validateMovie()` yang menangani validasi baik untuk pembuatan data baru maupun pembaruan data. _[Catatan: Kemudian ditingkatkan lagi dengan Form Request, lihat poin 4]_.
-   **Manfaat**:
    -   Mengurangi duplikasi kode.
    -   Memusatkan aturan validasi di satu tempat.
    -   Membuat logika validasi bisa digunakan ulang (reusable).
    -   Lebih mudah untuk memelihara dan mengubah aturan validasi.

### 2. Refactoring Fungsionalitas Pencarian

-   **Apa**: Memindahkan logika pencarian dari method `index`.
-   **Bagaimana**:
    -   Membuat method `private searchMovies()` di dalam controller.
    -   _Alternatif/Peningkatan_: Menambahkan method `scopeSearch()` di model `Movie`.
-   **Manfaat**:
    -   Method `index` menjadi lebih sederhana.
    -   Fungsi pencarian menjadi reusable.
    -   Kode lebih mudah dibaca (readability).
    -   Pemisahan tanggung jawab (separation of concerns) yang lebih baik.

### 3. Refactoring Penanganan File

-   **Apa**: Memindahkan logika unggah (upload), penyimpanan, dan penghapusan gambar.
-   **Bagaimana**: Membuat class khusus bernama `FileService` dengan method:
    -   `saveImage()`: Menangani proses unggah dan penyimpanan gambar.
    -   `deleteImage()`: Menangani proses penghapusan gambar.
-   **Manfaat**:
    -   Memusatkan operasi terkait file.
    -   Menghilangkan kode yang sama di beberapa method.
    -   Membuat logika penanganan file reusable di bagian lain aplikasi jika diperlukan.
    -   Operasi file jadi lebih mudah diuji (testability).

### 4. Validasi Menggunakan Form Request

-   **Apa**: Memindahkan aturan validasi ke class Form Request khusus (cara yang lebih direkomendasikan Laravel daripada method private di controller).
-   **Bagaimana**: Membuat class:
    -   `MovieStoreRequest`: Untuk validasi saat membuat film baru.
    -   `MovieUpdateRequest`: Untuk validasi saat memperbarui film.
-   **Manfaat**:
    -   Method di Controller menjadi lebih bersih dan fokus pada logika utama.
    -   Logika validasi terpisah (terisolasi) dan reusable.
    -   Organisasi kode menjadi lebih rapi.
    -   Mengikuti _best practice_ (praktik terbaik) dari Laravel.

### 5. Refactoring Method `delete`

-   **Apa**: Memperbaiki method `delete` dengan memindahkan logika penghapusan file gambar.
-   **Bagaimana**: Menggunakan `FileService` yang sudah dibuat untuk menangani penghapusan gambar.
-   **Manfaat**:
    -   Cara penghapusan file menjadi konsisten (menggunakan `FileService`).
    -   Menghilangkan duplikasi kode (jika ada logika hapus file di tempat lain).
    -   Lebih mudah dipelihara.

## Manfaat Keseluruhan dari Refactoring

1.  **Mengurangi Duplikasi Kode**: Menghilangkan pola kode yang berulang di berbagai method.
2.  **Kemudahan Pemeliharaan (Maintainability) Lebih Baik**: Kode lebih mudah diperbarui dan dirawat karena logikanya terpusat.
3.  **Pemisahan Tanggung Jawab (Separation of Concerns) Lebih Baik**: Setiap class dan method punya satu tugas yang jelas.
4.  **Kemudahan Pengujian (Testability) Meningkat**: Komponen yang terpisah lebih mudah untuk dites secara individual.
5.  **Mengikuti Prinsip SOLID**: Kode menjadi lebih sesuai dengan prinsip-prinsip desain perangkat lunak yang baik.
6.  **Mengikuti Best Practice Laravel**: Mengikuti pola dan praktik yang dianjurkan oleh framework Laravel.
7.  **Organisasi Kode Lebih Baik**: Struktur kode yang lebih jelas membuatnya lebih mudah dipahami.

_Kredit Awal: Yori Adi Atma_
_Diperbarui oleh: [Nauval Alpen Perdana](https://github.com/nauvalalpen)_
