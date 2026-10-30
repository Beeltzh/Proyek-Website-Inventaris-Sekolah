<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'kode_barang',
        'nama_barang',
        'merk',
        'lokasi',
        'stok_total',
        'stok_tersedia',
        'satuan',
        'kondisi',
        'foto',
        'deskripsi',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class)->withTrashed();
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function getKondisiLabelAttribute(): string
    {
        return match ($this->kondisi) {
            'baik' => 'Baik',
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_berat' => 'Rusak Berat',
            default => ucfirst((string) $this->kondisi),
        };
    }

    public function getSedangDipinjamAttribute(): int
    {
        return max(0, $this->stok_total - $this->stok_tersedia);
    }
}
