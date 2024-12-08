<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class MedicalRecordBook extends Model
{
    use HasFactory;

    protected $collection = 'medicalrecordbooks';

    protected $fillable = [
        'fullname', 
        'birthday', 
        'address', 
        'sobh', 
        'tokenmedical', 
        'sex', 
        'weight', 
        'height', 
        'email', 
        'phoneNumber', 
        'cccd', 
        'avatar', 
        'tokenbranch', 
        'tokeorg', 
        'medicalRecordCode', 
        'fieldsToShare',
    ];

    protected $casts = [
        'fieldsToShare' => 'array',
    ];

    // Quan hệ với lịch hẹn: Một sổ khám bệnh có thể có nhiều lịch hẹn
    public function schedules()
    {
        return $this->hasMany(MedicalScheduleModel::class, 'patient', 'medicalRecordCode');
    }
}
