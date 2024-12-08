<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model; // Import class Model từ jenssegers/mongodb

class MedicalRecordDetail extends Model
{
    use HasFactory;

    protected $collection = 'medicalrecorddetails';
    protected $fillable = [
        'examinationsection',
        'index',
        'medicalserviceID'
    ];
}
