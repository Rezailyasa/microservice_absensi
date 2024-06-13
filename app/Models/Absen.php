<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absen extends Model
{
    use HasFactory;
    protected $table = 'absen';
    protected $fillable = [
        'users_id',
        'lokasi_user',
        'waktu_absen_masuk',
        'waktu_absen_pulang',
        'tanggal_hari_ini',
        'status',
    ];
}
