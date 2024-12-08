<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecordBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class MedicalRecordBookController extends Controller
{
    public function addMedicalRecordBook(Request $request)
    {
        $request->validate([
            'fullname' => 'required',
            'birthday' => 'required|date',
            'address' => 'required',
            'tokenmedical' => 'required',
            'sobh' => 'required',
            'sex' => 'required',
            'weight' => 'required',
            'height' => 'required',
            'email' => 'required',
            'phoneNumber' => 'required',
            'avatar' => 'required',
            'cccd' => 'required',
            'fieldsToShare' => 'array'
        ]);

        try {
            // Tạo mã sổ khám bệnh 20 ký tự
            $medicalRecordCode = substr(md5(uniqid(rand(), true)), 0, 20);

            $medicalRecordBook = MedicalRecordBook::create([
                'fullname' => $request->fullname,
                'birthday' => $request->birthday,
                'address' => $request->address,
                'sobh' => $request->sobh,
                'tokenmedical' => $request->tokenmedical,
                'sex' => $request->sex,
                'weight' => $request->weight,
                'height' => $request->height,
                'email' => $request->email,
                'phoneNumber' => $request->phoneNumber,
                'avatar' => $request->avatar,
                'tokenbranch' => $request->tokenbranch,
                'tokeorg' => $request->tokeorg,
                'cccd' => $request->cccd,
                'medicalRecordCode' => $medicalRecordCode,
                'fieldsToShare' => $request->fieldsToShare // Lưu mảng fieldsToShare
            ]);

            return response()->json([
                'message' => 'Sổ khám bệnh đã được tạo thành công!',
                'data' => $medicalRecordBook
            ], 201);
        } catch (Exception $e) {
            Log::error('Error creating medical record book: ' . $e->getMessage());

            return response()->json([
                'message' => 'Đã xảy ra lỗi khi tạo sổ khám bệnh!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function countMedicalRecordsByBranch(Request $request)
    {
        $request->validate([
            'tokenbranch' => 'required', // Đảm bảo 'tokenbranch' được truyền vào
        ]);

        try {
            // Tính tổng số sổ khám bệnh theo tokenbranch
            $tokenbranch = $request->tokenbranch;
            $totalRecords = MedicalRecordBook::where('tokenbranch', $tokenbranch)->count();

            return response()->json([
                'message' => 'Tổng số sổ khám bệnh đã được tính thành công!',
                'tokenbranch' => $tokenbranch,
                'total_records' => $totalRecords,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error counting medical records: ' . $e->getMessage());

            return response()->json([
                'message' => 'Đã xảy ra lỗi khi tính tổng số sổ khám bệnh!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function countMedicalRecordsByOrganization(Request $request)
    {
        $request->validate([
            'tokenorg' => 'required', // Đảm bảo 'tokenorg' được truyền vào
        ]);

        try {
            $tokenorg = $request->tokenorg;

            // Tính tổng số sổ khám bệnh theo tokenorg
            $totalRecords = MedicalRecordBook::where('tokenorg', $tokenorg)->count();

            // Trả về phản hồi thành công
            return response()->json([
                'message' => 'Tổng số sổ khám bệnh đã được tính thành công!',
                'tokenorg' => $tokenorg,
                'total_records' => $totalRecords,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error counting medical records by organization: ' . $e->getMessage());

            return response()->json([
                'message' => 'Đã xảy ra lỗi khi tính tổng số sổ khám bệnh!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function getAllMedicalRecordBook()
    {
        try {
            $records = MedicalRecordBook::all();

            return response()->json($records, 200);
        } catch (Exception $e) {
            Log::error('Error fetching medical record books: ' . $e->getMessage());

            return response()->json([
                'message' => 'Đã xảy ra lỗi khi lấy danh sách sổ khám bệnh!',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function getMedicalRecordByCCCD(Request $request)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'cccd' => 'required|string'
        ]);

        try {
            // Lấy giá trị cccd từ request
            $cccd = $request->cccd;

            // Tìm kiếm sổ khám bệnh theo CCCD
            $medicalRecordBook = MedicalRecordBook::where('cccd', $cccd)->first();

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
        } catch (Exception $e) {
            Log::error('Error fetching medical record book by CCCD: ' . $e->getMessage());

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
            'medicalRecordCode' => 'required|string'
        ]);

        try {
            // Lấy giá trị cccd từ request
            $medicalRecordCode = $request->medicalRecordCode;

            // Tìm kiếm sổ khám bệnh theo CCCD
            $medicalRecordBook = MedicalRecordBook::where('medicalRecordCode', $medicalRecordCode)->first();

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
        } catch (Exception $e) {
            Log::error('Error fetching medical record book by CCCD: ' . $e->getMessage());

            return response()->json([
                'message' => 'Đã xảy ra lỗi khi tìm kiếm sổ khám bệnh!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getPatientsByHospital($tokenorg)
    {
        try {
            $patients = MedicalRecordBook::where('tokeorg', $tokenorg)->get();

            // Kiểm tra nếu danh sách rỗng
            if ($patients->isEmpty()) {
                return response()->json([
                    'message' => 'Không có bệnh nhân nào thuộc bệnh viện với tokenorg: ' . $tokenorg
                ], 404);
            }

            return response()->json([
                'message' => 'Danh sách bệnh nhân tại bệnh viện.',
                'data' => $patients
            ], 200);
        } catch (Exception $e) {
            Log::error('Error fetching patients by hospital: ' . $e->getMessage());

            return response()->json([
                'message' => 'Đã xảy ra lỗi khi lấy danh sách bệnh nhân tại bệnh viện!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getPatientsByBranch($tokenbranch)
    {
        try {
            $patients = MedicalRecordBook::where('tokenbranch', $tokenbranch)->get();

            // Kiểm tra nếu danh sách rỗng
            if ($patients->isEmpty()) {
                return response()->json([
                    'message' => 'Không có bệnh nhân nào thuộc chi nhánh với tokenbranch: ' . $tokenbranch
                ], 404);
            }

            return response()->json([
                'message' => 'Danh sách bệnh nhân tại chi nhánh.',
                'data' => $patients
            ], 200);
        } catch (Exception $e) {
            Log::error('Error fetching patients by branch: ' . $e->getMessage());

            return response()->json([
                'message' => 'Đã xảy ra lỗi khi lấy danh sách bệnh nhân tại chi nhánh!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function searchPatients(Request $request)
    {
        $request->validate([
            'fullname' => 'nullable|string', // Tên
            'cccd' => 'nullable|string',    // CCCD
        ]);

        try {
            $query = MedicalRecordBook::query();

            if ($request->has('fullname') && !empty($request->fullname)) {
                $query->where('fullname', 'like', '%' . $request->fullname . '%');
            }

            if ($request->has('cccd') && !empty($request->cccd)) {
                $query->where('cccd', $request->cccd);
            }
            $patients = $query->get();
            return response()->json([
                'message' => 'Tìm kiếm bệnh nhân thành công!',
                'patients' => $patients,
            ], 200);
        } catch (Exception $e) {
            // Ghi log lỗi và trả về phản hồi lỗi
            Log::error('Error searching patients: ' . $e->getMessage());

            return response()->json([
                'message' => 'Đã xảy ra lỗi khi tìm kiếm bệnh nhân!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
