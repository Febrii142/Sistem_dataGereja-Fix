<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'nama',
        'alamat',
        'kontak',
        'status',
        'tanggal_lahir',
        'jenis_kelamin',
        'pekerjaan',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
