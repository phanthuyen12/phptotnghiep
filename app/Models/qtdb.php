<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model; // Import class Model từ jenssegers/mongodb

class qtdb extends Model
{
    use HasFactory;

    protected $collection = 'qtdbs';
    protected $fillable = ['username', 'password', 'token'];

}
