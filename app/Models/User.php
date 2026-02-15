<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Nama primary key di DB kamu
    protected $primaryKey = 'iduser';

    // Jika primary key auto-increment integer
    public $incrementing = true;
    protected $keyType = 'int';

    // Kalau tabel yang dipakai adalah 'users' (default), tidak perlu set $table.
    // Jika berbeda, uncomment dan ganti:
    // protected $table = 'users';

    // Jika tabel users TIDAK punya created_at / updated_at, set false
    public $timestamps = false;

    // kolom yang bisa di-mass assign sesuai skema DB-mu
    protected $fillable = [
        'username',
        'password',
        'idrole',
    ];

    protected $hidden = [
        'password',
        // 'remember_token' // hapus kalau kolom ini nggak ada
    ];

    // Casts yang benar: pakai properti $casts (bukan method)
    protected $casts = [
        // opsional: membantu Laravel meng-hash saat create/update via model
        'password' => 'hashed',
    ];
}
