<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model; // Import class Model từ jenssegers/mongodb

class MedicalRecord extends Model
{
    use HasFactory;

    protected $collection = 'medicalrecords';
    protected $fillable = [
        'branchID',
        'medicalrecordbookID',
        'userID',
        'date',
        'resuft',
        'unit',
        'status',
        'diagnosis'
    ];

    public function medicalRecordBook()
    {
        return $this->belongsTo(MedicalRecordBook::class, 'medicalrecordbookID', '_id');
    }
}
