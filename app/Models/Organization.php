<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model; // Import class Model từ jenssegers/mongodb

class Organization extends Model
{
    use HasFactory;

    protected $collection = 'organizations';
    protected $fillable = [
        'nameorg',
        'nameadmin',
        'addressadmin',
        'emailadmin',
        'phoneadmin',
        'businessBase64',
        'tokenorg',
        'statusorg'
    ];
}
