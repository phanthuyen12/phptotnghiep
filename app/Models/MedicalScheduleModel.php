<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class MedicalScheduleModel extends Model
{
    use HasFactory;

    protected $collection = 'medicalSchedule';

    protected $fillable = [
        'branch',
        'className', 
        'condition', 
        'department', 
        'patient',
        'timeschedule',
        'title',
        'tokenorg',
        'type',
        'notes',
        'accepted_by_doctor',
        'status',
        'clinic',
    ];
    

    // Các giá trị cho trường 'type'
    const TYPE_INITIAL = 'initial';
    const TYPE_FOLLOW_UP = 'follow_up';

    // Các giá trị cho trường 'status'
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_COMPLETED = 'completed';

    // Quan hệ với bệnh nhân
    public function patient()
    {
        return $this->belongsTo(MedicalRecordBook::class, 'patient', 'medicalRecordCode');  
    }

    // Quan hệ với phòng khám
   // Quan hệ với phòng khám (Lấy phòng khám dựa trên ID)
// public function clinics()
// {
//     return $this->belongsTo(ClinicsModel::class, 'clinic', '_id');  
// }

    // Quan hệ với lịch hẹn trước đó
    public function previousSchedule()
    {
        return $this->belongsTo(MedicalScheduleModel::class, 'previous_schedule_id', '_id');
    }

    // Quan hệ với bác sĩ đã tiếp nhận lịch hẹn
    public function acceptedByDoctor()
    {
        return $this->belongsTo(User::class, 'accepted_by_doctor', '_id');
    }
    public function departments()
    {
        return $this->belongsTo(DepartmentModel::class, 'department', '_id');  
    }
   
    public function clinics()
    {
        return $this->belongsTo(ClinicsModel::class, 'clinic', '_id');
    }
    
    public function medicalServices()
    {
        return $this->hasOneThrough(
            ServiceBranch::class,   // Model ServiceBranch
            ClinicsModel::class,    // Model ClinicsModel
            'selectedService',      // Khóa ngoại trong ClinicsModel (liên kết tới ServiceBranch)
            '_id',          // Khóa chính trong ServiceBranch
            '_id',                  // Khóa chính của ClinicsModel
            '_id'                   // Khóa chính của ServiceBranch
        );
    }
    
    

}
