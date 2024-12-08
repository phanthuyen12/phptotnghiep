<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestMedicalModel;

class RequestMedicalController extends Controller
{
    //
    public function createRequest(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $validatedData = $request->validate([
            'branch' => 'required|string',
            'cccd' => 'required|string',
            'content' => 'required|string',
            'status' => 'required|string',
            'timerequest' => 'required|date',
            'tokeorg' => 'required|string'
        ]);

        // Tạo yêu cầu mới
        $requestMedical = RequestMedicalModel::create([
            'branch' => $validatedData['branch'],
            'cccd' => $validatedData['cccd'],
            'content' => $validatedData['content'],
            'status' => $validatedData['status'],
            'timerequest' => Carbon::parse($validatedData['timerequest']), // chuyển đổi thời gian
            'tokeorg' => $validatedData['tokeorg']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Request Medical created successfully',
            'data' => $requestMedical
        ]);
    }

}
