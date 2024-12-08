<?php

// DepartmentModel
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class DepartmentModel extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'departments';

    protected $fillable = [
        'departmentCode',  // Mã khoa
        'departmentName',  // Tên khoa
        'description',     // Mô tả về khoa
        'tokenbranch',
        'clinic_id'        // Khóa ngoại liên kết với phòng khám
    ];

    // Mối quan hệ với phòng khám
    public function clinic()
    {
        return $this->belongsTo(ClinicsModel::class, 'clinic_id', '_id');
    }

    // Định nghĩa mối quan hệ với User (Bác sĩ)
    public function users()
    {
        return $this->hasMany(User::class, 'department_id', '_id');
    }
}
