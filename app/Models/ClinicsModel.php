<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class ClinicsModel extends Model
{
    use HasFactory;

    protected $collection = 'clinics';

    protected $fillable = [
        'code', 'name', 'address', 'phone', 'doctors', 'services', 'tokenorg', 'branch', 'roomType', 'departmentType', 'selectedService',
    ];

    protected $casts = [
        'doctors' => 'array',
        'services' => 'array',
    ];

    protected $primaryKey = '_id';

    // Mối quan hệ n-1: Một phòng khám thuộc về một dịch vụ
    public function serviceBranch()
    {
        return $this->belongsTo(ServiceBranch::class, 'selectedService', '_id');
    }

    public function doctors()
    {
        return $this->hasMany(User::class, 'tokenuser', 'doctors');
    }

    /**
     * Lấy toàn bộ thông tin người dùng chưa được thêm vào phòng khám.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnassignedUsers()
    {
        $assignedTokens = $this->doctors ?? []; 
        return User::whereNotIn('tokenuser', $assignedTokens)->get();
    }
}
