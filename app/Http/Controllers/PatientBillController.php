<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientBillModel;
use App\Models\PatientBillDetailModel;
use Carbon\Carbon;

class PatientBillController extends Controller
{
    // Hàm thêm hóa đơn và cập nhật trạng thái hóa đơn
    public function addBill(Request $request)
    {
        try {
            // Xử lý dữ liệu JSON từ request
            $data = $request->validate([
                'server' => 'required|array',
                'totalsum' => 'required|numeric',
                'insurancestatus' => 'required|numeric',
                'medicalRecordCode' => 'required|string',
            ]);
    
            // Tạo hóa đơn mới
            $patientBill = new PatientBillModel([
                'insurancestatus' => $data['insurancestatus'],
                'medicalRecordCode' => $data['medicalRecordCode'],
                'totalsum' => $data['totalsum'],
                'status' => 'pending', // Trạng thái mặc định có thể là 'pending' hoặc giá trị khác
            ]);
    
            // Lưu hóa đơn vào MongoDB
            $patientBill->save();
    
            // Lấy thông tin chi tiết hóa đơn từ 'server'
            foreach ($data['server'] as $server) {
                $patientBillDetail = new PatientBillDetailModel([
                    'idpatientbill' => $patientBill->_id,  // Liên kết với hóa đơn đã tạo
                    'idserver' => $server['clinics']['service_branch']['_id'],
                    'titleserver' => $server['title'],
                    'idclinic' => $server['clinic'],
                    'tokendoctors' => $server['accepted_by_doctor'],
                ]);
    
                // Lưu chi tiết hóa đơn vào MongoDB
                $patientBillDetail->save();
            }
    
            // Trả về kết quả thành công
            return response()->json([
                'status' => true,
                'message' => 'Hóa đơn đã được thêm thành công.',
                'data' => $patientBill
            ], 201);
    
        } catch (\Exception $e) {
            // Xử lý lỗi nếu có
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ], 500); // 500 Internal Server Error
        }
    }
    public function showBillByID(Request $request)
    {
        try {
            // Validate the incoming request
            $validatedData = $request->validate([
                'medicalRecordCode' => 'required|string',
            ]);
    
            // Fetch branch data with specified fields and conditions
            $branchData = PatientBillModel::where('_id', $request->medicalRecordCode) // Thêm điều kiện id
                ->get(['_id', 'totalsum', 'status','medicalRecordCode','created_at']); // Select fields
    
            if ($branchData->isEmpty()) {
                return response()->json([
                    'message' => 'No data found for the specified branch and id.',
                    'status' => false,
                ], 404); // Return 404 Not Found if no data
            }
    
            return response()->json([
                'message' => 'Data retrieved successfully.',
                'branch' => $branchData,
                'status' => true,
            ], 200); // Return 200 OK for successful retrieval
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
                'status' => false,
            ], 422); // Return 422 Unprocessable Entity for validation errors
        } catch (\Exception $e) {
            // Handle any other exceptions
            return response()->json([
                'message' => 'Failed to retrieve data.',
                'error' => $e->getMessage(),
                'status' => false,
            ], 500); // Return 500 Internal Server Error for other issues
        }
    }
    // Hàm cập nhật trạng thái hóa đơn
    public function getById(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'idpatientbill' => 'required|string',
        ]);
    
        // Find all patient bills with the given idpatientbill
        $bills = PatientBillDetailModel::with('server')->where('idpatientbill', $request->idpatientbill)->get();
    
        // Check if any bills are found
        if ($bills->isEmpty()) {
            return response()->json(['message' => 'Hóa đơn không tồn tại.'], 404);
        }
    
        // Return the bills found
        return response()->json([
            'message' => 'Hóa đơn đã được tìm thấy.',
            'data' => $bills
        ]);
    }
    
    public function updateBillStatus(Request $request, $billId)
    {
        $bill = PatientBillModel::find($billId);
        
        if (!$bill) {
            return response()->json(['message' => 'Hóa đơn không tồn tại.'], 404);
        }

        // Cập nhật trạng thái hóa đơn
        $bill->status = $request->status;
        $bill->updated_at = Carbon::now();
        $bill->save();

        return response()->json([
            'message' => 'Trạng thái hóa đơn đã được cập nhật.',
            'data' => $bill
        ]);
    }
}
