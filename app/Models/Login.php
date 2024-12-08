<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;
use Jenssegers\Mongodb\Eloquent\Model; 

class Login extends Model
{
    use HasFactory;

    protected $collection = 'logins';
    protected $fillable = ['cccd', 'typeusers', 'password'];

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'cccd', 'cccd');
    }
}
