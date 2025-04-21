<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    /**
     * Atribut yang diizinkan untuk diisi secara massal (mass assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'judul',
        'category_id',
        'sinopsis',
        'tahun',
        'pemain',
        'foto_sampul',
    ];

    /**
     * Menerapkan scope pencarian film berdasarkan judul atau sinopsis.
     * Metode ini memungkinkan `Movie::search($keyword)->get()`.
     *
     * REF.: Fungsionalitas pencarian diekstrak dari MovieController ke dalam scope ini.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Instance query builder.
     * @param string|null $searchTerm Kata kunci pencarian.
     * @return \Illuminate\Database\Eloquent\Builder Instance query builder yang dimodifikasi.
     */
    public function scopeSearch($query, $searchTerm = null)
    {
        // Hanya tambahkan kondisi where jika ada kata kunci pencarian
        if ($searchTerm) {
            return $query->where('judul', 'like', '%' . $searchTerm . '%')
                         ->orWhere('sinopsis', 'like', '%' . $searchTerm . '%');
        }

        // Kembalikan query asli jika tidak ada kata kunci
        return $query;
    }

    /**
     * Mendefinisikan relasi "belongsTo" ke model Category.
     * Ini berarti setiap Movie dimiliki oleh satu Category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}