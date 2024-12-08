<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use App\Models\Login;
use App\Models\qtdb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class OrganizationController extends Controller
{
    public function addOrganization(Request $request)
    {
        $request->validate([
            'nameorg' => 'required',
            'nameadmin' => 'required',
            'emailadmin' => 'required',
            'phoneadmin' => 'required',
            'businessBase64' => 'required',
            'password' => 'required',
            'addressadmin' => 'required',
            'imgidentification' => 'required',
            'username' => 'required',
            'cccd' => 'required'
        ]);
    
        // Khởi tạo biến status
        $status = true;
    
        try {
            $organization = Organization::create([
                'nameorg' => $request->nameorg,
                'nameadmin' => $request->nameadmin,
                'emailadmin' => $request->emailadmin,
                'addressadmin' => $request->addressadmin,
                'phoneadmin' => $request->phoneadmin,
                'businessBase64' => $request->businessBase64,
                'tokenorg' =>  $request->tokenorg,
                'statusorg' => 'active',
            ]);
    
            $adminUser = User::create([
                'fullname' => $request->nameadmin,
                'address' => $request->addressadmin,
                'organizationalvalue' => 'admin',
                'phone' => $request->phoneadmin,
                'imgidentification' => $request->businessBase64,
                'cccd' => $request->cccd,
                'tokenuser' => $request->tokenuser,
                'tokenorg' => $organization->tokenorg,
                'branchID' => null,
            ]);
    
          
        } catch (\Exception $e) {
            // Nếu có lỗi xảy ra, thiết lập status thành false
            $status = false;
            return response()->json([
                'status' => $status,
                'message' => 'Failed to create organization, admin user, or login!',
                'error' => $e->getMessage(),
            ], 500);
        }
    
        return response()->json([
            'status' => $status,
            'message' => 'Organization, admin user, and login created successfully!',
            'organization' => $organization,
            'adminUser' => $adminUser,
           
        ], 201);
    }
    
    public function getOrganizationDetails($id)
    {
        $organization = Organization::find($id);
        if (!$organization) {
            return response()->json(['message' => 'Organization not found'], 404);
        }
        return response()->json($organization, 200);
    }

    public function changePassword(Request $request, $id)
    {
        $request->validate(['new_password' => 'required']);

        $organization = Organization::find($id);
        if (!$organization) {
            return response()->json(['message' => 'Organization not found'], 404);
        }

        $login = Login::where('cccd', $organization->cccd)->first();
        $login->password = bcrypt($request->new_password);
        $login->save();

        return response()->json(['message' => 'Password changed successfully'], 200);
    }

    public function updateOrganization(Request $request, $id)
    {
        $organization = Organization::find($id);
        if (!$organization) {
            return response()->json(['message' => 'Organization not found'], 404);
        }
        $organization->update($request->all());
        return response()->json($organization, 200);
    }

    public function getAllOrganization()
    {
        return response()->json(Organization::all(), 200);
    }
}
