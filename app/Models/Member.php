<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Member extends Model
{
    public const STATUSES = ['aktif', 'tidak_aktif', 'pindah'];

    protected $fillable = [
        'user_id',
        'nama',
        'alamat',
        'kontak',
        'status',
        'tanggal_lahir',
        'jenis_kelamin',
        'pekerjaan',
        'baptism_status',
        'baptism_date',
        'baptism_location',
        'catechism_batch',
        'parent_guardian_name',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFilterCategories(Builder $query, array $filters): Builder
    {
        return $query
            ->when(! empty($filters['search']), function (Builder $query) use ($filters) {
                $search = $filters['search'];

                $query->where(function (Builder $query) use ($search) {
                    $query->where('nama', 'like', '%'.$search.'%')
                        ->orWhere('kontak', 'like', '%'.$search.'%');
                });
            })
            ->when(! empty($filters['status']), fn (Builder $query) => $query->where('status', $filters['status']))
            ->when(! empty($filters['gender']), fn (Builder $query) => $query->where('jenis_kelamin', $filters['gender']))
            ->when(! empty($filters['year']), fn (Builder $query) => $query->whereYear('created_at', $filters['year']))
            ->when(! empty($filters['age_category']), function (Builder $query) use ($filters) {
                $today = Carbon::today();

                match ($filters['age_category']) {
                    'bayi' => $query->whereBetween('tanggal_lahir', [
                        $today->copy()->subYears(3)->addDay()->toDateString(),
                        $today->toDateString(),
                    ]),
                    'anak' => $query->whereBetween('tanggal_lahir', [
                        $today->copy()->subYears(13)->addDay()->toDateString(),
                        $today->copy()->subYears(3)->toDateString(),
                    ]),
                    'remaja' => $query->whereBetween('tanggal_lahir', [
                        $today->copy()->subYears(19)->addDay()->toDateString(),
                        $today->copy()->subYears(13)->toDateString(),
                    ]),
                    'dewasa' => $query->whereBetween('tanggal_lahir', [
                        $today->copy()->subYears(60)->addDay()->toDateString(),
                        $today->copy()->subYears(19)->toDateString(),
                    ]),
                    'lansia' => $query->whereDate('tanggal_lahir', '<=', $today->copy()->subYears(60)->toDateString()),
                    default => null,
                };
            })
            ->when(
                ! empty($filters['wilayah']) && ! empty($filters['wilayah_field']),
                fn (Builder $query) => $query->where($filters['wilayah_field'], $filters['wilayah'])
            );
    }
}
