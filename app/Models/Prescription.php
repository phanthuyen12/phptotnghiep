<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model; // Import class Model từ jenssegers/mongodb

class Prescription extends Model
{
    protected $collection = 'prescriptions';
    protected $fillable = [
        'namePrescription',
        'quatity',
        'unitOfMeasurement',
        'userManual',
        'medicalrecordID',
    ];
}
