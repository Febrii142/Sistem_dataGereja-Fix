<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Jemaat extends Model
{
    protected $table = 'jemaat';

    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'kota',
        'kode_pos',
        'no_telepon',
        'email',
        'status_baptis',
        'tanggal_baptis',
        'kepala_keluarga_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function keluarga(): HasMany
    {
        return $this->hasMany(AnggotaKeluarga::class, 'kepala_keluarga_id');
    }

    public function kepalaKeluarga(): BelongsTo
    {
        return $this->belongsTo(self::class, 'kepala_keluarga_id');
    }

    public function baptisan(): HasOne
    {
        return $this->hasOne(Baptisan::class, 'jemaat_id');
    }

    public function isKepalaKeluarga(): bool
    {
        return $this->kepala_keluarga_id === null;
    }

    public function getAnggotaKeluarga()
    {
        return $this->keluarga()->with('jemaat')->get();
    }

    public function addAnggotaKeluarga(array $data): AnggotaKeluarga
    {
        return $this->keluarga()->create([
            ...$data,
            'kepala_keluarga_id' => $this->id,
        ]);
    }

    public function updateBaptisan(?array $data): void
    {
        if ($this->status_baptis !== 'sudah' || empty($data)) {
            $this->baptisan()->delete();

            return;
        }

        $this->baptisan()->updateOrCreate(
            ['jemaat_id' => $this->id],
            $data
        );
    }
}
