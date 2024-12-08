<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalConclusionModel;

class MedicalConclusionController extends Controller
{
    //
     // Tạo phương thức để lưu dữ liệu
     public function add(Request $request)
     {
         $data = $request->validate([
             'tokeorg' => 'required|string',
             'tokenbranch' => 'required|string',
             'doctor' => 'required|string',
             'diseasecodes' => 'required|string',
             'namedisease' => 'required|string',
             'cccd' => 'required|string',
             'newData' => 'required|array'
         ]);
 
         $medicalRecord = MedicalConclusionModel::create($data);
 
         return response()->json([
             'success' => true,
             'data' => $medicalRecord
         ]);
     }
 
     // Phương thức để lấy dữ liệu theo CCCD
     public function show($cccd)
     {
         $medicalRecord = MedicalConclusionModel::where('cccd', $cccd)->first();
 
         if (!$medicalRecord) {
             return response()->json([
                 'success' => false,
                 'message' => 'Medical record not found'
             ], 404);
         }
 
         return response()->json([
             'success' => true,
             'data' => $medicalRecord
         ]);
     }
     public function getByCode(Request $request)
     {
         // Validate để đảm bảo trường doctor có trong request
         $request->validate([
            'diseasecodes' => 'required|string',
            'doctor' => 'required|string',
        ]);
     
         // Lấy các trường cụ thể
         $medicalRecords = MedicalConclusionModel::where('diseasecodes', $request->diseasecodes)
         ->where('doctor', $request->doctor)

                         ->get();
     
         if ($medicalRecords->isEmpty()) {
             return response()->json([
                 'success' => false,
                 'message' => 'No medical records found for the specified doctor'
             ], 404);
         }
     
         return response()->json([
             'success' => true,
             'data' => $medicalRecords
         ]);
     }
     public function getByDoctor(Request $request)
     {
         // Validate để đảm bảo trường doctor có trong request
         $request->validate([
             'doctor' => 'required|string',
         ]);
     
         // Lấy các trường cụ thể
         $medicalRecords = MedicalConclusionModel::where('doctor', $request->doctor)
                         ->select('_id', 'created_at', 'diseasecodes') // chỉ chọn các trường cần thiết
                         ->get();
     
         if ($medicalRecords->isEmpty()) {
             return response()->json([
                 'success' => false,
                 'message' => 'No medical records found for the specified doctor'
             ], 404);
         }
     
         return response()->json([
             'success' => true,
             'data' => $medicalRecords
         ]);
     }
     
     
}
