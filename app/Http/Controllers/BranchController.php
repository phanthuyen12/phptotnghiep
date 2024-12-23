<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\Organization;
use App\Models\User;
use App\Models\MedicalRecordBook;

class BranchController extends Controller
{
    public function addBranch(Request $request)
    {
        try {
            // Xác thực yêu cầu đầu vào
            $request->validate([
                'branchname' => 'required',
                'branchaddress' => 'required',
                'tokenbranch' => 'required',
                'branchemail' => 'required',
                'branchphone' => 'required',
                'branchbusinesslicense' => 'required',
                'tokeorg' => 'required',
            ]);
    
            // Tìm kiếm tổ chức dựa vào token tổ chức
            $organization = Organization::where('tokenorg', $request->tokeorg)->first();
    
            if (!$organization) {
                return response()->json([
                    'message' => 'Organization not found. Please provide a valid tokeorg.'
                ], 404);
            }
    
            // Tạo chi nhánh mới
            $branch = Branch::create([
                'branchname' => $request->branchname,
                'tokenbranch' => $request->tokenbranch,
                'branchaddress' => $request->branchaddress,
                'branchemail' => $request->branchemail,
                'branchphone' => $request->branchphone,
                'branchbusinesslicense' => $request->branchbusinesslicense,
                'tokeorg' => $request->tokeorg,
            ]);
    
            return response()->json($branch, 201);
    
        } catch (\Exception $e) {
            // Trả về thông báo lỗi nếu xảy ra lỗi
            return response()->json([
                'message' => 'An error occurred while adding the branch.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function countBranchesByOrganization(Request $request)
    {
        try {
            // Xác thực đầu vào
            $request->validate([
                'tokeorg' => 'required',
            ]);
    
            // Tìm tổ chức theo token
            $organization = Organization::where('tokenorg', $request->tokeorg)->first();
    
            if (!$organization) {
                return response()->json([
                    'message' => 'Organization not found. Please provide a valid tokeorg.'
                ], 404);
            }
    
            // Đếm số chi nhánh liên quan đến tổ chức
            $branchCount = Branch::where('tokeorg', $request->tokeorg)->count();
    
            // Đếm số người dùng liên quan đến tổ chức
            $userCount = User::where('tokenorg', $request->tokeorg)->count();
    
            // Đếm số hồ sơ y tế liên quan đến tổ chức
            $medicalRecordCount = MedicalRecordBook::where('tokeorg', $request->tokeorg)->count();
    
            // Trả về dữ liệu đếm được
            return response()->json([
                'message' => 'Counts retrieved successfully.',
                'tokeorg' => $request->tokeorg,
                'branch_count' => $branchCount,
                'user_count' => $userCount,
                'medical_record_count' => $medicalRecordCount,
            ], 200);
    
        } catch (\Exception $e) {
            // Trả về thông báo lỗi nếu có lỗi xảy ra
            return response()->json([
                'message' => 'An error occurred while retrieving counts.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function updateBranch(Request $request, $id)
    {
        $branch = Branch::find($id);
        if (!$branch) {
            return response()->json(['message' => 'Branch not found'], 404);
        }
        $branch->update($request->all());
        return response()->json($branch, 200);
    }

    public function deleteBranch($id)
    {
        $branch = Branch::find($id);
        if (!$branch) {
            return response()->json(['message' => 'Branch not found'], 404);
        }
        $branch->delete();
        return response()->json(['message' => 'Branch deleted successfully'], 200);
    }

    public function getAllBranches()
    {
        $branches = Branch::all();

        return response()->json($branches, 200);
    }
}
