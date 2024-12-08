<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $collection = 'users';

    protected $fillable = [
        'fullname',
        'address',
        'organizationalvalue',
        'phone',
        'imgidentification',
        'cccd',
        'tokenuser',
        'tokenorg',
        'tokenbranch',
        'License',
        'specialized',
        'avatar',
    ];

    protected $casts = [
        'specialized' => 'array',
    ];

    public function login()
    {
        return $this->hasOne(Login::class, 'cccd', 'cccd');
    }

    public function department()
    {
        return $this->belongsTo(DepartmentModel::class, 'department_id', '_id');
    }

    public function acceptedSchedules()
    {
        return $this->hasMany(MedicalScheduleModel::class, 'accepted_by_doctor', '_id');
    }
}
