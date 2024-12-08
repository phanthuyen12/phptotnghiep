<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class PatientBillModel extends Eloquent
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    // Đảm bảo sử dụng MongoDB
    protected $connection = 'mongodb';
    protected $collection = 'patientbill';

    // Các thuộc tính có thể thay đổi
    protected $fillable = [
        'insurancestatus',
        'medicalRecordCode',
        'totalsum',
        'status',
    ];

    // Quan hệ với PatientBillDetailModel (1 PatientBill có nhiều PatientBillDetail)
    public function billDetails()
    {
        return $this->hasMany(PatientBillDetailModel::class, 'idpatientbill');
    }
}
