<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'loan_id',
        'received_by',
        'tanggal_pengajuan',
        'tanggal_dikembalikan',
        'jumlah_kembali',
        'kondisi_saat_kembali',
        'terlambat_hari',
        'catatan_pengembalian',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengajuan' => 'datetime',
            'tanggal_dikembalikan' => 'datetime',
        ];
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class)->withTrashed();
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by')->withTrashed();
    }

    public function getKondisiLabelAttribute(): string
    {
        return match ($this->kondisi_saat_kembali) {
            'baik' => 'Baik',
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_berat' => 'Rusak Berat',
            default => ucfirst(str_replace('_', ' ', (string) $this->kondisi_saat_kembali)),
        };
    }
}
