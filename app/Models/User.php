<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'iduser';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'username','email','password','id_google','otp','idrole'
    ];

    protected $casts = [
        // tambahkan yg perlu
    ];

    // jika login default pakai username:
    public function getAuthIdentifierName()
    {
        return 'iduser';
    }
}