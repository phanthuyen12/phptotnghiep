<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class PatientBillDetailModel extends Eloquent
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    // Đảm bảo sử dụng MongoDB
    protected $connection = 'mongodb';
    protected $collection = 'patientbilldetail'; 

    // Các thuộc tính có thể thay đổi
    protected $fillable = [
        'idpatientbill',
        'idserver',
        'titleserver',
        'idclinic',
        'tokendoctors',
    ];

    // Quan hệ với PatientBillModel (1 PatientBill có nhiều PatientBillDetail)
    public function patientBill()
    {
        return $this->belongsTo(PatientBillModel::class, 'idpatientbill');
    }
    public function server()
    {
        return $this->belongsTo(ServiceBranch::class, 'idserver');
    }
}
