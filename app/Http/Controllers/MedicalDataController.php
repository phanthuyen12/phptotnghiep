<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalDataModel;
use App\Models\MedicalConclusionModel;

class MedicalDataController extends Controller
{
    // Hàm lưu dữ liệu từ form
    public function store(Request $request)
    {
        // Xác nhận các trường đầu vào từ request
        // tableData,
        //     symptomData,
        //     cccd,
        //     tokeorg,
        //     tokenbranch,
        //     tokenuser,
        $request->validate([
            'tableData' => 'required|array',                   // Các chuyên mục khám và kết quả
            'symptomData.symptom' => 'required|string',        // Biểu hiện
            'symptomData.conclusion' => 'required|string',     // Kết luận
            'cccd' => 'required|string',                       // CCCD của bệnh nhân
            'patientImage' => 'nullable|string',               // URL ảnh bệnh nhân, không bắt buộc
            'tokenbranch' => 'nullable|string',                // Token của chi nhánh, không bắt buộc
            'tokeorg' => 'nullable|string',                   // Token của tổ chức, không bắt buộc
            'tokenuser' => 'nullable|string',                // Token của bác sĩ, không bắt buộc
        ]);

        // Lưu dữ liệu vào MongoDB
        $medicalRecord = MedicalDataModel::create([
            'exam_records' => $request->tableData,
            'diagnosis_info' => $request->symptomData,
            'patient_cccd' => $request->cccd,
            'patient_image' => $request->patientImage,         // Lưu URL ảnh bệnh nhân nếu có
            'token_branch' => $request->tokenbranch,           // Lưu token chi nhánh nếu có
            'token_org' => $request->tokeorg,                 // Lưu token tổ chức nếu có
            'token_doctor' => $request->tokenuser,           // Lưu token bác sĩ nếu có
        ]);

        return response()->json([
            'success' => true,
            'data' => $medicalRecord
        ]);
    }
    public function getMedicalRecordByDoctorToken(Request $request)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'token_doctor' => 'required|string'
        ]);
    
        try {
            // Lấy giá trị token_doctor từ request
            $doctorToken = $request->token_doctor;
    
            // Tìm kiếm trong MedicalDataModel theo token_doctor
            $medicalRecords = MedicalDataModel::where('token_doctor', $doctorToken)
            ->select('_id', 'created_at', 'patient_cccd') // Chỉ chọn các trường cần thiết

            ->get();
    
            // // Nếu không tìm thấy trong MedicalDataModel, tìm trong MedicalConclusionModel
            // if ($medicalRecords->isEmpty()) {
            //     $medicalRecords = MedicalConclusionModel::where('doctor', $doctorToken)
            //         ->select('_id', 'created_at', 'diseasecodes') // Chỉ chọn các trường cần thiết
            //         ->get();
            // }
    
            // // Kiểm tra lần cuối nếu vẫn không có kết quả từ cả hai model
            // if ($medicalRecords->isEmpty()) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'No medical records found for the specified doctor'
            //     ], 404);
            // }
    
            // Trả về thông tin sổ khám bệnh nếu tìm thấy
            return response()->json([
                'success' => true,
                'data' => $medicalRecords
            ], 200);
    
        } catch (\Exception $e) {
            // \Log::error('Error fetching medical records by doctor token: ' . $e->getMessage());
    
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi tìm kiếm sổ khám bệnh!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getMedicalRecordByCode(Request $request)
{
    // Validate dữ liệu đầu vào
    $request->validate([
        'medicalRecordCode' => 'required|string',
        'doctor'=>'required|string',
    ]);

    try {
        // Lấy giá trị cccd từ request
        $medicalRecordCode = $request->medicalRecordCode;
        $doctor = $request->doctor;

        // Tìm kiếm sổ khám bệnh theo CCCD
        $medicalRecordBook = MedicalDataModel::where('patient_cccd', $medicalRecordCode)
        ->where('token_doctor', $doctor)
        ->get();
    
        // Kiểm tra nếu không tìm thấy
        if (!$medicalRecordBook) {
            return response()->json([
                'message' => 'Không tìm thấy sổ khám bệnh với CCCD đã cung cấp.'
            ], 404);
        }

        // Trả về thông tin sổ khám bệnh nếu tìm thấy
        return response()->json([
            'message' => 'Thông tin sổ khám bệnh.',
            'data' => $medicalRecordBook
        ], 200);

    } catch (\Exception $e) {
        // Log::error('Error fetching medical record book by CCCD: ' . $e->getMessage());

        return response()->json([
            'message' => 'Đã xảy ra lỗi khi tìm kiếm sổ khám bệnh!',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function getMedicalRecordByCodeOne(Request $request)
{
    // Validate dữ liệu đầu vào
    $request->validate([
        'medicalRecordCode' => 'required|string',
    ]);

    try {
        // Lấy giá trị cccd từ request
        $medicalRecordCode = $request->medicalRecordCode;
        $doctor = $request->doctor;

        // Tìm kiếm sổ khám bệnh theo CCCD
        $medicalRecordBook = MedicalDataModel::where('patient_cccd', $medicalRecordCode)
        ->get();
    
        // Kiểm tra nếu không tìm thấy
        if (!$medicalRecordBook) {
            return response()->json([
                'message' => 'Không tìm thấy sổ khám bệnh với CCCD đã cung cấp.'
            ], 404);
        }

        // Trả về thông tin sổ khám bệnh nếu tìm thấy
        return response()->json([
            'message' => 'Thông tin sổ khám bệnh.',
            'data' => $medicalRecordBook
        ], 200);

    } catch (\Exception $e) {
        // Log::error('Error fetching medical record book by CCCD: ' . $e->getMessage());

        return response()->json([
            'message' => 'Đã xảy ra lỗi khi tìm kiếm sổ khám bệnh!',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
