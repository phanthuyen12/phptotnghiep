<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model; // Import class Model từ jenssegers/mongodb

class MedicalConclusionModel extends Model
{
    use HasFactory;
    protected $collection = 'medical_conclusion';

    protected $fillable = [
        'tokeorg', 'tokenbranch', 'doctor', 'diseasecodes', 'namedisease', 'cccd', 'newData'
    ];
}
