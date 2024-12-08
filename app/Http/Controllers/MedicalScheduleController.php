<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalScheduleModel;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MedicalScheduleController extends Controller
{
    // Thêm mới một lịch hẹn
    public function addMedicalSchedule(Request $request)
    {
        try {
            // Validate dữ liệu nhập vào
            $validated = $request->validate([
                'branch' => 'required',
                'className' => 'required',
                'condition' => 'nullable',
                'department' => 'required',
                'patient' => 'required',
                'timeschedule' => 'required|date',
                'title' => 'required|string',
                'tokenorg' => 'nullable',
                'notes' => 'nullable|string',
                'type' => 'required|in:initial,follow_up',
                'clinic' => 'required'
            ]);
    
            // Thêm giá trị mặc định cho accepted_by_doctor
            $validated['accepted_by_doctor'] = null;
    
            // Tạo mới lịch hẹn
            $medicalSchedule = MedicalScheduleModel::create($validated);
    
            // Trả về kết quả
            return response()->json($medicalSchedule, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to add medical schedule', 'message' => $e->getMessage()], 500);
        }
    }
    public function updateAcceptedByDoctor(Request $request)
{
    try {
        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'medical' => 'required|string', // ID hoặc mã của bệnh nhân
            'accepted_by_doctor' => 'required|string', // ID của bác sĩ tiếp nhận
        ]);

        // Tìm lịch hẹn theo patient
        $medicalSchedule = MedicalScheduleModel::where('_id',$request->medical)->first();

        if (!$medicalSchedule) {
            return response()->json(['error' => 'Medical schedule not found',
                'patient'=>$request->patient
    
        ], 404);
        }

        // Cập nhật giá trị accepted_by_doctor và className
        $medicalSchedule->update([
            'accepted_by_doctor' => $validated['accepted_by_doctor'],
            'className' => 'Received', // Giá trị mới
        ]);

        // Trả về kết quả
        return response()->json([
            'status' => true, // Add status field
            'message' => 'Medical schedule updated successfully',
            'data' => $medicalSchedule
        ], 200);
            } catch (Exception $e) {
        return response()->json([
            'status' => false, // Add status field
            
            'error' => 'Failed to update medical schedule', 'message' => $e->getMessage()], 500);
    }
}


    // Lấy thông tin một lịch hẹn cụ thể
    public function show($id)
    {
        try {
            // Lấy thông tin lịch hẹn theo ID
            $medicalSchedule = MedicalScheduleModel::findOrFail($id);

            return response()->json($medicalSchedule);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Medical schedule not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve medical schedule', 'message' => $e->getMessage()], 500);
        }
    }

    // Cập nhật lịch hẹn
    public function update(Request $request, $id)
    {
        try {
            // Tìm lịch hẹn theo ID
            $medicalSchedule = MedicalScheduleModel::findOrFail($id);

            // Validate dữ liệu nhập vào
            $validated = $request->validate([
                'branch' => 'required',
                'className' => 'required',
                'condition' => 'nullable',
                'department' => 'required',
                'doctor' => 'required',
                'patient' => 'required',
                'timeschedule' => 'required|date',
                'title' => 'required|string',
                'tokenorg' => 'nullable',
                'notes' => 'nullable|string',
                'type' => 'required|in:initial,follow_up',
            ]);

            // Cập nhật thông tin lịch hẹn
            $medicalSchedule->update($validated);

            return response()->json($medicalSchedule);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Medical schedule not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update medical schedule', 'message' => $e->getMessage()], 500);
        }
    }
// Lấy danh sách lịch hẹn theo bệnh nhân
public function getByPatient(Request $request)
{
    try {
        $patient = $request->patient;

        // Truy vấn các lịch hẹn của bệnh nhân
        $medicalSchedules = MedicalScheduleModel::with(['clinics', 'clinics.serviceBranch']) // Eager load phòng khám và dịch vụ
            ->where('patient', $patient) // Lọc theo bệnh nhân
            ->get();

        // Kiểm tra nếu có lịch hẹn
        if ($medicalSchedules->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Không có lịch hẹn nào cho bệnh nhân này'
            ], 404);
        }

        // Trả về danh sách các lịch hẹn của bệnh nhân cùng với phòng khám và dịch vụ
        return response()->json([
            'status' => true,
            'data' => $medicalSchedules
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => false,
            'error' => 'Failed to retrieve medical schedules',
            'message' => $e->getMessage()
        ], 500);
    }
}





    // Xóa lịch hẹn
    public function destroy($id)
    {
        try {
            // Tìm lịch hẹn theo ID
            $medicalSchedule = MedicalScheduleModel::findOrFail($id);

            // Xóa lịch hẹn
            $medicalSchedule->delete();

            return response()->json(['message' => 'Medical schedule deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Medical schedule not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete medical schedule', 'message' => $e->getMessage()], 500);
        }
    }

    // Lấy danh sách lịch hẹn theo bác sĩ
    public function getByDoctor(Request $request)
    {
        try {
            // Lấy lịch hẹn theo bác sĩ
            $doctor = $request->doctor;
            $medicalSchedules = MedicalScheduleModel::where('doctor', $doctor)->get();

            return response()->json($medicalSchedules);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve medical schedules by doctor', 'message' => $e->getMessage()], 500);
        }
    }
    public function getByClinics(Request $request)
    {
        try {
            // Lấy lịch hẹn theo bác sĩ
            $clinic = $request->clinic;
            $medicalSchedules = MedicalScheduleModel::where('clinic', $clinic)->get();

            return response()->json($medicalSchedules);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve medical schedules by doctor', 'message' => $e->getMessage()], 500);
        }
    }

    // Lấy danh sách lịch hẹn theo bệnh nhân
    public function index(Request $request)
    {
        try {
            // Lấy lịch hẹn theo bệnh nhân
            $patient = $request->patient;
            $medicalSchedules = MedicalScheduleModel::where('patient', $patient)->get();

            return response()->json($medicalSchedules);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve medical schedules', 'message' => $e->getMessage()], 500);
        }
    }
}
