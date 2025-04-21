<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Category;
use Illuminate\Support\Str; // Tidak digunakan secara langsung, bisa dihapus jika tidak ada rencana penggunaan
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File; // Tidak digunakan secara langsung setelah refactoring ke FileService
use Illuminate\Support\Facades\Validator;
use App\Services\FileService;

class MovieController extends Controller
{
    /**
     * Properti untuk menyimpan instance FileService.
     * Digunakan untuk menangani operasi terkait file gambar.
     */
    protected $fileService;

    /**
     * Konstruktor untuk menginjeksikan dependensi FileService.
     *
     * @param FileService $fileService Instance dari FileService.
     */
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Menampilkan daftar film di halaman utama.
     * Mendukung fitur pencarian.
     *
     * @return \Illuminate\View\View
     *
     * REF.: Fungsionalitas pencarian diekstrak ke metode searchMovies().
     */
    public function index()
    {
        // Mengambil daftar film dengan paginasi, menerapkan pencarian jika ada
        $movies = $this->searchMovies(request('search'))
            ->paginate(6);

        return view('homepage', compact('movies'));
    }

    /**
     * Mencari film berdasarkan judul atau sinopsis.
     * Metode ini dipanggil oleh index() untuk memisahkan logika pencarian.
     *
     * @param string|null $searchTerm Kata kunci pencarian.
     * @return \Illuminate\Database\Eloquent\Builder Query builder untuk pencarian film.
     *
     * REF.: Logika pencarian diekstrak dari metode index().
     */
    private function searchMovies($searchTerm = null)
    {
        $query = Movie::latest(); // Mulai query dengan urutan terbaru

        // Jika ada kata kunci pencarian, tambahkan kondisi where
        if ($searchTerm) {
            $query->where('judul', 'like', '%' . $searchTerm . '%')
                ->orWhere('sinopsis', 'like', '%' . $searchTerm . '%');
        }

        return $query;
    }

    /**
     * Menampilkan detail film tertentu.
     *
     * @param string $id ID unik film yang akan ditampilkan.
     * @return \Illuminate\View\View
     */
    public function detail($id)
    {
        $movie = Movie::find($id); // Cari film berdasarkan ID
        return view('detail', compact('movie'));
    }

    /**
     * Menampilkan formulir untuk membuat (input) data film baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = Category::all(); // Ambil semua kategori untuk dropdown
        return view('input', compact('categories'));
    }

    /**
     * Menyimpan data film baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request Data dari formulir input.
     * @return \Illuminate\Http\RedirectResponse
     *
     * REF.: Logika validasi dan penanganan file diekstrak ke metode validateMovie() dan FileService.
     */
    public function store(Request $request)
    {
        // Validasi data input untuk film baru
        $validator = $this->validateMovie($request, true); // true menandakan ini film baru

        // Jika validasi gagal, kembali ke halaman input dengan error dan input sebelumnya
        if ($validator->fails()) {
            return redirect('movies/create')
                ->withErrors($validator)
                ->withInput();
        }

        // Simpan gambar sampul menggunakan FileService
        $fileName = $this->fileService->saveImage($request->file('foto_sampul'), 'images');

        // Buat data film baru di database
        Movie::create([
            'id' => $request->id,
            'judul' => $request->judul,
            'category_id' => $request->category_id,
            'sinopsis' => $request->sinopsis,
            'tahun' => $request->tahun,
            'pemain' => $request->pemain,
            'foto_sampul' => $fileName, // Simpan nama file gambar yang sudah di-generate
        ]);

        // Redirect ke halaman utama dengan pesan sukses
        return redirect('/')->with('success', 'Data film berhasil disimpan');
    }

    /**
     * Menampilkan daftar film dalam format tabel untuk halaman admin/data.
     *
     * @return \Illuminate\View\View
     */
    public function data()
    {
        // Ambil semua film dengan urutan terbaru dan paginasi
        $movies = Movie::latest()->paginate(10);
        return view('data-movies', compact('movies'));
    }

    /**
     * Menampilkan formulir untuk mengedit data film yang sudah ada.
     *
     * @param string $id ID unik film yang akan diedit.
     * @return \Illuminate\View\View
     */
    public function form_edit($id)
    {
        $movie = Movie::find($id); // Cari film berdasarkan ID
        $categories = Category::all(); // Ambil semua kategori untuk dropdown
        return view('form-edit', compact('movie', 'categories'));
    }

    /**
     * Memperbarui data film tertentu di database.
     *
     * @param  \Illuminate\Http\Request  $request Data dari formulir edit.
     * @param  string  $id ID unik film yang akan diperbarui.
     * @return \Illuminate\Http\RedirectResponse
     *
     * REF.: Logika validasi dan penanganan file diekstrak ke metode validateMovie() dan FileService.
     */
    public function update(Request $request, $id)
    {
        // Validasi data input untuk pembaruan film
        $validator = $this->validateMovie($request, false); // false menandakan ini pembaruan

        // Jika validasi gagal, kembali ke halaman edit dengan error dan input sebelumnya
        if ($validator->fails()) {
            return redirect("/movies/edit/{$id}")
                ->withErrors($validator)
                ->withInput();
        }

        // Cari film yang akan diperbarui atau tampilkan error 404 jika tidak ditemukan
        $movie = Movie::findOrFail($id);

        // Siapkan data yang akan diperbarui
        $movieData = [
            'judul' => $request->judul,
            'sinopsis' => $request->sinopsis,
            'category_id' => $request->category_id,
            'tahun' => $request->tahun,
            'pemain' => $request->pemain,
        ];

        // Cek apakah ada file gambar baru yang diunggah
        if ($request->hasFile('foto_sampul')) {
            // Hapus gambar lama menggunakan FileService
            $this->fileService->deleteImage($movie->foto_sampul, 'images');

            // Simpan gambar baru menggunakan FileService
            $fileName = $this->fileService->saveImage($request->file('foto_sampul'), 'images');
            $movieData['foto_sampul'] = $fileName; // Tambahkan nama file baru ke data pembaruan
        }

        // Lakukan pembaruan data film
        $movie->update($movieData);

        // Redirect ke halaman data film dengan pesan sukses
        return redirect('/movies/data')->with('success', 'Data film berhasil diperbarui');
    }

    /**
     * Menghapus data film tertentu dari database beserta file gambarnya.
     *
     * @param  string  $id ID unik film yang akan dihapus.
     * @return \Illuminate\Http\RedirectResponse
     *
     * REF.: Logika penghapusan file gambar ditangani oleh FileService.
     */
    public function delete($id)
    {
        // Cari film yang akan dihapus atau tampilkan error 404 jika tidak ditemukan
        $movie = Movie::findOrFail($id);

        // Hapus file gambar sampul terkait menggunakan FileService
        $this->fileService->deleteImage($movie->foto_sampul, 'images');

        // Hapus data film dari database
        $movie->delete();

        // Redirect ke halaman data film dengan pesan sukses
        return redirect('/movies/data')->with('success', 'Data film berhasil dihapus');
    }

    /**
     * Memvalidasi data input untuk operasi store (simpan baru) dan update (perbarui).
     *
     * @param Request $request Data request dari formulir.
     * @param bool $isNewMovie Menandakan apakah ini validasi untuk film baru (true) atau pembaruan (false).
     * @return \Illuminate\Validation\Validator Instance Validator.
     *
     * REF.: Logika validasi diekstrak dari metode store() dan update() untuk reusabilitas.
     */
    private function validateMovie(Request $request, bool $isNewMovie = true)
    {
        // Aturan validasi dasar yang berlaku untuk store dan update
        $rules = [
            'judul' => 'required|string|max:255',
            'category_id' => 'required|integer|exists:categories,id', // Pastikan category_id ada di tabel categories
            'sinopsis' => 'required|string',
            'tahun' => 'required|integer|digits:4', // Pastikan tahun berupa 4 digit angka
            'pemain' => 'required|string',
        ];

        // Tambahkan aturan validasi khusus berdasarkan apakah ini film baru atau pembaruan
        if ($isNewMovie) {
            // Untuk film baru, ID wajib diisi dan harus unik di tabel movies
            $rules['id'] = ['required', 'string', 'max:255', Rule::unique('movies', 'id')];
            // Untuk film baru, foto sampul wajib diunggah
            $rules['foto_sampul'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        } else {
            // Untuk pembaruan, foto sampul bersifat opsional
            $rules['foto_sampul'] = 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }

        // Lakukan validasi menggunakan data request dan aturan yang telah ditentukan
        return Validator::make($request->all(), $rules);
    }
}