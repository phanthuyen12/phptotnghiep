<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Login;
use App\Models\Organization;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function createDoctor(Request $request)
    {
        $request->validate([
            'fullname' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'cccd' => 'required',
            'typeusers' => 'required',
            'branch' => 'required',
            'tokeorg' => 'required',
            'password' => 'required',
            "imgidentification" => 'required'
        ]);

        $organization = Organization::where('tokenorg', $request->tokeorg)->first();
        if (!$organization) {
            return response()->json([
                'message' => 'Organization not found. Please provide a valid organizationsID.'
            ], 404);
        }

        $branch = Branch::where('tokenbranch', $request->branch)
            ->first();
        if (!$branch) {
            return response()->json([
                'message' => 'Branch not found or does not belong to the provided organization.'
            ], 404);
        }

        $doctorUser = User::create([
            'fullname' => $request->fullname,
            'address' => $request->address,
            'organizationalvalue' => $request->typeusers,
            'phone' => $request->phone,
            'imgidentification' => '',
            'cccd' => $request->cccd,
            'tokenuser' => $request->tokenuser,
            'branch' => $request->branch,
            'tokenorg' => $request->tokeorg,
            'License' => $request->License,
        ]);

        // $login = Login::create([
        //     'cccd' => $doctorUser->cccd,
        //     'typeusers' => 'doctor',
        //     'password' => Hash::make($request->password),
        // ]);

        return response()->json([
            'message' => 'Doctor user and login created successfully!',
            'doctorUser' => $doctorUser,
        ], 201);
    }

    public function updateDoctor(Request $request, $cccd)
    {
        $request->validate([
            'fullname' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
        ]);

        $doctorUser = User::where('cccd', $cccd)->first();
        if (!$doctorUser) {
            return response()->json([
                'message' => 'Doctor not found.'
            ], 404);
        }

        $doctorUser->update($request->only(['fullname', 'address', 'phone']));

        return response()->json([
            'message' => 'Doctor information updated successfully!',
            'doctorUser' => $doctorUser,
        ]);
    }

    public function changePassword(Request $request, $cccd)
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $login = Login::where('cccd', $cccd)->first();
        if (!$login) {
            return response()->json([
                'message' => 'Login not found for this doctor.'
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

    public function getDoctorsByOrganizationToken($tokenorg)
    {
        $organization = Organization::where('tokenorg', $tokenorg)->first();
        if (!$organization) {
            return response()->json([
                'message' => 'Organization not found.'
            ], 404);
        }

        $doctors = User::where('organizationalvalue', 'doctor')
            ->where('tokenorg', $tokenorg)
            ->get();

        return response()->json([
            'message' => 'Doctors retrieved successfully.',
            'doctors' => $doctors,
        ], 200);
    }

    public function getDoctorsByBranchToken($tokenbranch)
    {
        $branch = Branch::where('tokenbranch', $tokenbranch)->first();
        if (!$branch) {
            return response()->json([
                'message' => 'Branch not found.'
            ], 404);
        }

        $doctors = User::where('organizationalvalue', 'doctor')
            ->where('branch', $tokenbranch)
            ->get();

        return response()->json([
            'message' => 'Doctors retrieved successfully.',
            'doctors' => $doctors,
        ], 200);
    }
}
