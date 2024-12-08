<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DepartmentModel;
use App\Models\Branch; // Để kiểm tra sự tồn tại của branch theo tokenbranch

class DepartmentController extends Controller
{
    // Hiển thị danh sách tất cả các khoa
    public function index()
    {
        $departments = DepartmentModel::all(); // Lấy tất cả các khoa
        return response()->json($departments);
    }

    // Hiển thị chi tiết một khoa theo id
    public function show($id)
    {
        $department = DepartmentModel::find($id);
        if (!$department) {
            return response()->json(['message' => 'Department not found'], 404);
        }

        return response()->json($department);
    }

    // Thêm khoa mới
    public function store(Request $request)
    {
        $request->validate([
            'departmentCode' => 'required|string|unique:departments,departmentCode', // Kiểm tra trùng lặp mã khoa trong bảng departments
            'departmentName' => 'required|string',
            'description' => 'nullable|string',
            'model' => 'required|exists:branches,tokenbranch', // Kiểm tra tokenbranch trong bảng branches
            
        ]);

        // Tạo khoa mới
        $department = DepartmentModel::create([
            'departmentCode' => $request->departmentCode,
            'departmentName' => $request->departmentName,
            'description' => $request->description,
            'tokenbranch' => $request->model, // Liên kết khoa với bệnh viện
        ]);

        return response()->json($department, 201); // Trả về khoa mới tạo
    }

    // Cập nhật thông tin khoa
    public function update(Request $request, $id)
    {
        $department = DepartmentModel::find($id);

        if (!$department) {
            return response()->json(['message' => 'Department not found'], 404);
        }

        // Validate dữ liệu đầu vào
        $request->validate([
            'departmentCode' => 'required|string',
            'departmentName' => 'required|string',
            'description' => 'nullable|string',
            'tokenbranch' => 'required|exists:branches,tokenbranch', // Kiểm tra tokenbranch
        ]);

        // Cập nhật khoa
        $department->update([
            'departmentCode' => $request->departmentCode,
            'departmentName' => $request->departmentName,
            'description' => $request->description,
            'tokenbranch' => $request->tokenbranch, // Cập nhật tokenbranch nếu có
        ]);

        return response()->json($department);
    }

    // Xóa khoa
    public function destroy(Request $request)
{
    // Kiểm tra xem departmentCode có tồn tại trong request không
    $request->validate([
        'departmentCode' => 'required|string', // Không cần kiểm tra unique khi xóa
    ]);

    // Tìm department theo departmentCode
    $department = DepartmentModel::where('departmentCode', $request->departmentCode)->first();

    if (!$department) {
        // Nếu không tìm thấy khoa, trả về lỗi 404
        return response()->json(['message' => 'Department not found'], 404);
    }

    // Xóa khoa
    $department->delete();

    // Trả về phản hồi thành công
    return response()->json(['message' => 'Department deleted successfully']);
}


    // Lấy tất cả khoa của một bệnh viện theo tokenbranch
    public function getDepartmentsByBranch(Request $request)
    {
        // Kiểm tra sự tồn tại của bệnh viện với tokenbranch
        $request->validate([
            'tokenbranch' => 'required|string|exists:branches,tokenbranch', // Kiểm tra tokenbranch có tồn tại trong bảng branches
        ]);
        
        // Lấy giá trị tokenbranch từ request
        $tokenbranch = $request->tokenbranch;
        
        // Tìm chi nhánh với tokenbranch
        $branch = Branch::where('tokenbranch', $tokenbranch)->first();
        
        // Kiểm tra xem chi nhánh có tồn tại không
        if (!$branch) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Branch not found'
            ], 404);
        }
    
        // Lấy các khoa của bệnh viện này
        $departments = $branch->departments;
    
        // Trả về danh sách khoa kèm theo trạng thái
        return response()->json([
            'status' => 'success',
            'message' => 'Departments fetched successfully',
            'data' => $departments
        ], 200);
    }
    
}
