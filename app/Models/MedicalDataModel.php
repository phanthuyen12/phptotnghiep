<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model; // Import class Model từ jenssegers/mongodb

class MedicalDataModel extends Model
{
    use HasFactory;
    
    protected $connection = 'mongodb'; // Kết nối MongoDB
    protected $collection = 'medical_records'; // Tên collection trong MongoDB

    protected $fillable = [
        'exam_records',        // Các chuyên mục khám và kết quả
        'diagnosis_info',      // Thông tin chẩn đoán (biểu hiện và kết luận)
        'patient_cccd',        // CCCD của bệnh nhân
        'patient_image',       // URL ảnh bệnh nhân (tùy chọn)
        'token_branch',        // Token của chi nhánh (tùy chọn)
        'token_org',           // Token của tổ chức (tùy chọn)
        'token_doctor',        // Token của bác sĩ (tùy chọn)
    ];

    protected $casts = [
        'exam_records' => 'array',
        'diagnosis_info' => 'array',
    ];
}
