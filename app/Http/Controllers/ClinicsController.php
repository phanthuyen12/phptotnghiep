<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClinicsModel;
use App\Models\User;
use Exception;  // Thêm dòng này để sử dụng Exception
use Illuminate\Validation\ValidationException; // Đảm bảo import đúng lớp
class ClinicsController extends Controller
{
    //
    public function StoreClinics(Request $request)
    {
        try {
            // Validation
            $validated = $request->validate([
                'code' => 'required|string|unique:clinics,code',
                'name' => 'required|string',
                'address' => 'required|string',
                'phone' => 'required|string',
                'doctors' => 'required|array',
                'selectedService'=>'required|string',
                // 'doctors.*._id' => 'required|string',
                // 'doctors.*.fullname' => 'required|string',
                // 'doctors.*.phone' => 'required|string',
                // 'services' => 'required|array',
                'tokenorg' => 'required|string',
                'branch' => 'required|string',
                'roomType'=>'required|string',
                'departmentType' => 'required|string|exists:departments,_id', // hoặc trường phù hợp
            ]);
        
            // Create clinic
            $data = ClinicsModel::create([
                'code' => $request->code,
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'selectedService'=>$request->selectedService,
                'services' => $request->services,
                'tokenorg' => $request->tokenorg,
                'branch' => $request->branch,
                'doctors' => $request->doctors, // Đảm bảo mảng doctors được lưu
                'roomType'=>$request->roomType,
                'departmentType'=>$request->departmentType,
            ]);
        
            // Kiểm tra dữ liệu đã được tạo chưa
            if (!$data) {
                return response()->json([
                    'status' => false,
                    'message' => 'Clinic could not be created.'
                ], 500);
            }
        
            // Trả về dữ liệu phòng khám mới tạo
            return response()->json([
                'status' => true,
                'data' => $data
            ], 200);
        
        } catch (ValidationException $e) {
            // Trả về thông báo lỗi chi tiết khi xác thực không thành công
            return response()->json([
                'status' => false,
                'message' => 'The given data was invalid.',
                'errors' => $e->errors() // Trả về các lỗi chi tiết từ ValidationException
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getClinicByDoctors(Request $request)
    {
        try {
            // Validate request để đảm bảo nhận được danh sách doctors
            $validated = $request->validate([
                'doctors' => 'required', // Có thể là chuỗi hoặc mảng
            ]);
    
            // Lấy danh sách tokenuser từ request, chuyển thành mảng nếu cần
            $tokenusers = $request->input('doctors');
            if (!is_array($tokenusers)) {
                $tokenusers = [$tokenusers]; // Đảm bảo tokenusers luôn là mảng
            }
    
            // Sử dụng regex để tìm tokenuser trong chuỗi doctors
            $clinics = ClinicsModel::where(function ($query) use ($tokenusers) {
                foreach ($tokenusers as $tokenuser) {
                    $query->orWhere('doctors', 'like', '%"' . $tokenuser . '"%');
                }
            })->get();
    
            // Kiểm tra xem có phòng khám nào được tìm thấy không
            if ($clinics->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No clinics found for the given doctors.'
                ], 404);
            }
    
            // Trả về thông tin các phòng khám
            return response()->json([
                'status' => true,
                'data' => $clinics
            ], 200);
    
        } catch (Exception $e) {
            // Xử lý lỗi và trả về thông báo lỗi
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    public function getClinicsByDepartmentType(Request $request)
{
    try {
        // Validation để đảm bảo departmentType được cung cấp và hợp lệ
        $validated = $request->validate([
            'departmentType' => 'required|string|exists:departments,_id', // Kiểm tra departmentType tồn tại trong bảng departments
        ]);

        // Lọc các phòng khám theo departmentType
        $clinics = ClinicsModel::where('departmentType', $request->departmentType)->get();

        // Kiểm tra nếu không tìm thấy phòng khám nào
        if ($clinics->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No clinics found for the given departmentType.'
            ], 404);
        }

        // Trả về danh sách phòng khám tìm được
        return response()->json([
            'status' => true,
            'data' => $clinics
        ], 200);

    } catch (ValidationException $e) {
        // Trả về thông báo lỗi chi tiết khi xác thực không thành công
        return response()->json([
            'status' => false,
            'message' => 'The given data was invalid.',
            'errors' => $e->errors() // Trả về các lỗi chi tiết từ ValidationException
        ], 422);
    } catch (Exception $e) {
        // Xử lý các lỗi khác
        return response()->json([
            'status' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}
public function getUnassignedUsers()
{
    // Lấy tất cả các phòng khám
    $allClinics = ClinicsModel::all();

    // Tổng hợp tất cả các tokenuser đã được gán vào phòng khám
    $assignedTokens = $allClinics->pluck('doctors')->flatten()->unique()->toArray();

    // Lấy danh sách người dùng chưa được thêm vào bất kỳ phòng khám nào
    $unassignedUsers = User::whereNotIn('tokenuser', $assignedTokens)->get();

    return response()->json([
        'status' => 'success',
        'unassigned_users' => $unassignedUsers
    ]);
}
    public function getClinicsAllByBranch(Request $request)
    {
        try {
            // Kiểm tra xem giá trị branch có tồn tại trong request không
            $branch = $request->input('branch');
            
            if (!$branch) {
                return response()->json([
                    'status' => false,
                    'message' => 'Branch is required.'
                ], 400); // Trả về mã lỗi 400 khi không có giá trị branch
            }
    
            // Truy vấn các phòng khám theo branch
            $data = ClinicsModel::where('branch', $branch)->get();
    
            // Kiểm tra xem có phòng khám nào không
            if ($data->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Clinic not found.'
                ], 404); // Trả về mã lỗi 404 khi không tìm thấy phòng khám
            }
    
            // Trả về dữ liệu phòng khám nếu tìm thấy
            return response()->json([
                'status' => true,
                'data' => $data
            ], 200); // Trả về mã lỗi 200 khi tìm thấy phòng khám
    
        } catch (Exception $e) {
            // Xử lý lỗi nếu có
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500); // Trả về mã lỗi 500 khi có lỗi server
        }
    }
    
    public function getClinicsById(Request $request)
    {
        try {
            // Xác thực ID từ request
            $validated = $request->validate([
                'id' => 'required|string'
            ]);
    
            // Lấy ID từ validated dữ liệu
            $id = $validated['id'];
    
            // Truy vấn phòng khám theo ID
            $data = ClinicsModel::where('_id', $id)->get();
    
            // Kiểm tra nếu không có dữ liệu
            if ($data->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Clinic not found.'
                ], 404); // Trả về mã lỗi 404 khi không tìm thấy phòng khám
            }
    
            // Trả về dữ liệu nếu tìm thấy
            return response()->json([
                'status' => true,
                'data' => $data
            ], 200); // Trả về mã lỗi 200 khi tìm thấy phòng khám
        } catch (Exception $e) {
            // Xử lý ngoại lệ nếu có lỗi xảy ra
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500); // Trả về lỗi 500 khi có ngoại lệ xảy ra
        }
    }
    public function DeleteClinics(Request $request)
    {
        try {
            // Xác thực id từ request
            $validated = $request->validate([
                'id' => 'required|string'
            ]);
    
            // Lấy id từ validated
            $id = $validated['id'];
    
            // Tìm phòng khám theo id
            $clinic = ClinicsModel::find($id);
    
            // Kiểm tra nếu phòng khám không tồn tại
            if (!$clinic) {
                return response()->json([
                    'status' => false,
                    'message' => 'Clinic not found.'
                ], 404); // Trả về mã lỗi 404 khi không tìm thấy phòng khám
            }
    
            // Xóa phòng khám
            $clinic->delete();
    
            return response()->json([
                'status' => true,
                'message' => 'Clinic deleted successfully.'
            ], 200); // Trả về mã 200 khi xóa thành công
        } catch (Exception $e) {
            // Xử lý ngoại lệ nếu có lỗi xảy ra
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500); // Trả về lỗi 500 khi có ngoại lệ xảy ra
        }
    }
    
    public function UpdateClinics(Request $request ,$id){
        try{
            $validated = $request->validate([
                'code' => 'required|string|unique:clinics,code',
                'name' => 'required|string',
                'address' => 'required|string',
                'phone' => 'required|string',
                'doctors' => 'required|array',
                'services' => 'required|array',
                'tokenorg' => 'required|string',
                'branch' => 'required|string'
            ]);
            $clinic = ClinicsModel::find($id);      
            if(!$clinic){
                return response()->json([
                    'status' => false,
                    // 'error' => $e->getMessage()
                ], 500); // Trả về lỗi 500 khi có ngoại lệ xảy ra
            }      
            
            $clinic->update(array_filter($validated));
            return response()->json([
                'status' => true,
                'data' => $clinic
            ], 200); // Trả về mã lỗi 200 khi tìm thấy phòng khám
        }catch(Exception $e){
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500); // Trả về lỗi 500 khi có ngoại lệ xảy ra
        }


    }
    public function addServicesToClinic(Request $request)
    {
        try {
            // Xác thực dữ liệu
            $validated = $request->validate([
                'clinic_id' => 'required|string',
                'services' => 'required|array'
            ]);

            // Tìm phòng khám theo ID
            $clinic = ClinicsModel::find($validated['clinic_id']);
            if (!$clinic) {
                return response()->json([
                    'status' => false,
                    'message' => 'Clinic not found.'
                ], 404); // Trả về mã lỗi 404 khi không tìm thấy phòng khám
            }

            // Thêm dịch vụ mới vào trường `services`
            $clinic->services = array_merge($clinic->services, $validated['services']);
            $clinic->save();

            return response()->json([
                'status' => true,
                'data' => $clinic->services,
                'message' => 'Services added successfully.'
            ], 200); // Trả về mã 200 khi thêm thành công

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500); // Trả về lỗi 500 khi có ngoại lệ xảy ra
        }
    }

    // Hàm thêm nhiều bác sĩ vào phòng khám
    public function addDoctorsToClinic(Request $request)
    {
        try {
            // Xác thực dữ liệu
            $validated = $request->validate([
                'clinic_id' => 'required|string',
                'doctors' => 'required|array'
            ]);

            // Tìm phòng khám theo ID
            $clinic = ClinicsModel::find($validated['clinic_id']);
            if (!$clinic) {
                return response()->json([
                    'status' => false,
                    'message' => 'Clinic not found.'
                ], 404); // Trả về mã lỗi 404 khi không tìm thấy phòng khám
            }

            // Thêm bác sĩ mới vào trường `doctors`
            $clinic->doctors = array_merge($clinic->doctors, $validated['doctors']);
            $clinic->save();

            return response()->json([
                'status' => true,
                'data' => $clinic->doctors,
                'message' => 'Doctors added successfully.'
            ], 200); // Trả về mã 200 khi thêm thành công

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500); // Trả về lỗi 500 khi có ngoại lệ xảy ra
        }
    }
    
}
