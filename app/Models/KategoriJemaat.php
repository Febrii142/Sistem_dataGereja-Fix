<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriJemaat extends Model
{
    protected $table = 'kategori_jemaat';

    protected $fillable = [
        'nama',
        'deskripsi',
    ];
}
