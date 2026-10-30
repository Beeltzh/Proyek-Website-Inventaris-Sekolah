<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Loan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kode_peminjaman',
        'user_id',
        'item_id',
        'approved_by',
        'jumlah',
        'tanggal_pinjam',
        'rencana_kembali',
        'keperluan',
        'status',
        'catatan_admin',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'date',
            'rencana_kembali' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by')->withTrashed();
    }

    public function returnLog(): HasOne
    {
        return $this->hasOne(ReturnLog::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu_persetujuan' => 'Menunggu Persetujuan',
            'dipinjam' => 'Sedang Dipinjam',
            'menunggu_pengembalian' => 'Menunggu Verifikasi Kembali',
            'dikembalikan' => 'Sudah Dikembalikan',
            'ditolak' => 'Ditolak',
            default => ucfirst((string) $this->status),
        };
    }

    public function getIsTerlambatAttribute(): bool
    {
        if (! in_array($this->status, ['dipinjam', 'menunggu_pengembalian'], true)) {
            return false;
        }

        return $this->rencana_kembali && $this->rencana_kembali->lt(now()->startOfDay());
    }

    public function calculateLateDays(?\DateTimeInterface $returnDate = null): int
    {
        if (! $this->rencana_kembali) {
            return 0;
        }

        $compareDate = $returnDate
            ? Carbon::instance($returnDate)->startOfDay()
            : now()->startOfDay();

        $dueDate = $this->rencana_kembali->copy()->startOfDay();

        if ($compareDate->lte($dueDate)) {
            return 0;
        }

        return (int) $dueDate->diffInDays($compareDate);
    }
}
