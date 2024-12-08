<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Login;
use App\Models\Organization;
use App\Models\Branch;
use App\Models\ClinicsModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

 class UserController extends Controller
{
    public function createUser(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'fullname' => 'required',
                'address' => 'required',
                'phone' => 'required',
                'cccd' => 'required',
                'tokeorg' => 'required',
                'branch' => 'required',
                'typeusers' => 'required',
                'password' => 'required',
            ]);
    
            // Check if the organization exists
            $organization = Organization::where('tokenorg', $request->tokeorg)->first();
            if (!$organization) {
                return response()->json([
                    'message' => 'Organization not found. Please provide a valid organizationsID.'
                ], 404);
            }
    
            // Check if the branch exists and belongs to the organization
            $branch = Branch::where('tokenbranch', $request->branch)
                ->first();
            if (!$branch) {
                return response()->json([
                    'message' => 'Branch not found or does not belong to the provided organization.'
                ], 404);
            }
    
            // Create the user
            $user = User::create([
               'fullname' => $request->fullname,
            'address' => $request->address,
            'organizationalvalue' => $request->typeusers, 
            'phone' => $request->phone,
            'imgidentification' => $request->imgidentification,
            'cccd' => $request->cccd,
            'tokenuser' => $request->tokenuser,
            'specialized'=>$request->specialized,
            'tokenbranch' => $request->branch,
            'tokenorg' => $request->tokeorg,
            'License'=>$request->License,
            'avatar'=>$request->avatar,
            ]);
    
            // Optionally create a login record (uncomment if needed)
            // $login = Login::create([
            //     'cccd' => $user->cccd,
            //     'typeusers' => $request->typeusers,
            //     'password' => Hash::make($request->password),
            // ]);
    
            return response()->json([
                'status'=>true,
                'message' => 'User and login created successfully!',
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            // Return error response if any exception occurs
            return response()->json([
                'message' => 'An error occurred while creating the user.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function getAvailableUsersByBranch(Request $request){
        try {
            // Validate the request input
            $request->validate([
                'branch' => 'required', // Ensure branch is provided
            ]);
    
            // Fetch branch by token
            $branch = User::where('tokenbranch', $request->branch)->get();
    
            // Check if branch exists
            if (!$branch) {
                return response()->json([
                    'status' => false,
                    'message' => 'Branch not found.',
                ], 404);
            }
    
            // Return successful response
            return response()->json([
                'status' => true,
                'data' => $branch
            ], 200);
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while counting users.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function countUsersByBranch(Request $request)
{
    try {
        // Validate the request
        $request->validate([
            'branch' => 'required', // Ensure branch is provided
        ]);

        // Check if the branch exists
        $branch = Branch::where('tokenbranch', $request->branch)->first();
        if (!$branch) {
            return response()->json([
                'status' => false,
                'message' => 'Branch not found. Please provide a valid branch token.',
            ], 404);
        }

        // Count the number of users in the branch
        $userCount = User::where('tokenbranch', $request->branch)->count();

        return response()->json([
            'status' => true,
            'message' => 'Total users counted successfully!',
            'branch' => $branch->tokenbranch,
            'total_users' => $userCount,
        ], 200);
    } catch (\Exception $e) {
        // Return error response if an exception occurs
        return response()->json([
            'status' => false,
            'message' => 'An error occurred while counting users.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    public function updateUser(Request $request, $cccd)
    {
        $request->validate([
            'fullname' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
        ]);

        $user = User::where('cccd', $cccd)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User not found.'
            ], 404);
        }

        $user->update($request->only(['fullname', 'address', 'phone']));

        return response()->json([
            'message' => 'User information updated successfully!',
            'user' => $user,
        ]);
    }

    public function changePassword(Request $request, $cccd)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required',
        ]);

        $login = Login::where('cccd', $cccd)->first();
        if (!$login) {
            return response()->json([
                'message' => 'Login not found for this User.'
            ], 404);
        }

        if (!Hash::check($request->old_password, $login->password)) {
            return response()->json([
                'message' => 'Old password is incorrect.'
            ], 400);
        }

        $login->password = Hash::make($request->new_password);
        $login->save();

        return response()->json([
            'message' => 'Password changed successfully!',
        ]);
    }
    public function getUsersBySpecialized(Request $request)
    {
        // Validate specialized input
        $request->validate([
            'specialized' => 'required|string',
        ]);
    
        // Retrieve users with the specified specialized field
        $users = User::where('specialized', $request->specialized)->get();
    
        if ($users->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No users found with this specialization.'
            ], 404);
        }
    
        return response()->json([
            'status' => true,
            'data' => $users
        ], 200);
    }
    
    public function getAllDoctors()
    {
        try {
            $logins = Login::where('typeusers', 'doctor')->get();

            $doctors = $logins->map(function ($login) {
                $user = User::where('cccd', $login->cccd)->first();
                return [
                    'user' => $user,
                ];
            });

            return response()->json($doctors, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve doctors.', 'message' => $e->getMessage()], 500);
        }
    }
    public function getAvailableUsers()
    {
        try {
            // Lấy tất cả các tokenuser trong trường `doctors` từ ClinicsModel
    
            // Lọc danh sách người dùng không nằm trong các tokenuser đã sử dụng
            $availableUsers = User::all();
    
            // Nếu không có người dùng nào phù hợp, trả về thông báo lỗi
            if ($availableUsers->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No available users found.'
                ], 404);
            }
    
            // Trả về danh sách người dùng chưa được liên kết với bất kỳ phòng khám nào
            return response()->json([
                'status' => true,
                'data' => $availableUsers
            ], 200);
        } catch (\Exception $e) {
            // Xử lý lỗi nếu có
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while retrieving the available users.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    

    public function getAllUsers()
{
    try {
        // Lấy tất cả người dùng từ cơ sở dữ liệu
        $users = User::all();

        // Kiểm tra xem có người dùng nào không
        if ($users->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No users found.'
            ], 404);
        }

        // Trả về danh sách người dùng
        return response()->json([
            'status' => true,
            'data' => $users
        ], 200);
    } catch (\Exception $e) {
        // Xử lý lỗi nếu có
        return response()->json([
            'message' => 'An error occurred while retrieving the users.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


}
