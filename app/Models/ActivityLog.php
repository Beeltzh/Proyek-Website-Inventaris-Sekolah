<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_user',
        'role_user',
        'action',
        'module',
        'subject_type',
        'subject_id',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mencatat jejak audit (audit trail) aktivitas pengguna di dalam sistem.
     *
     * @param  array<string, mixed>|null  $properties
     */
    public static function record(
        string $action,
        string $module,
        string $description,
        ?Model $subject = null,
        ?array $properties = null,
        ?User $actor = null
    ): self {
        /** @var User|null $user */
        $user = $actor ?? Auth::user();
        $request = app()->bound('request') ? request() : null;

        return self::create([
            'user_id' => $user?->id,
            'nama_user' => $user?->name ?? ($properties['email'] ?? 'Tamu / Sistem'),
            'role_user' => $user?->role ?? 'guest',
            'action' => strtoupper($action),
            'module' => $module,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'description' => $description,
            'properties' => $properties,
            'ip_address' => $request?->ip(),
            'user_agent' => $request ? substr((string) $request->userAgent(), 0, 500) : null,
        ]);
    }
}
