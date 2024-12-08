<?php

namespace App\Http\Controllers;

use App\Models\ServiceBranch;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    // Tạo dịch vụ mới
    public function create(Request $request)
    {
        $request->validate([
            'serviceCode' => 'required|string|unique:services',
            'serviceName' => 'required|string',
            'serviceType' => 'required|string',
            'model' => 'required|string',
            'serviceFees'=>'required|string',
        ]);

        $service = ServiceBranch::create([
            'serviceCode' => $request->serviceCode,
            'serviceName' => $request->serviceName,
            'serviceType' => $request->serviceType,
            'tokenbranch' => $request->model,
            'serviceFees' => $request->serviceFees,
        ]);

        return response()->json(['message' => 'Service created successfully', 'data' => $service], 201);
    }

    // Lấy danh sách dịch vụ theo chi nhánh
    public function getByBranch(Request $request)
    {
        $id = $request->branchId;
        $services = ServiceBranch::where('tokenbranch', $id)->get();

        return response()->json(['data' => $services], 200);
    }

    // Kiểm tra thông tin dịch vụ theo tokenuser
    public function checkServiceByToken(Request $request)
    {
        $request->validate([
            'serviceName' => 'required|string',
        ]);

        $services = ServiceBranch::where('serviceName', $request->serviceName)->get();

        if ($services->isEmpty()) {
            return response()->json(['message' => 'No services found for this token'], 404);
        }

        return response()->json(['data' => $services], 200);
    }
    public function getById(Request $request){
        $request->validate([
            'id' => 'required|string',
        ]);
        $services = ServiceBranch::where('_id', $request->id)->get();

        if ($services->isEmpty()) {
            return response()->json(['message' => 'No services found for this type'], 404);
        }

        return response()->json(['data' => $services], 200);
    }
    // Lấy danh sách dịch vụ theo loại dịch vụ
    public function getByServiceType(Request $request)
    {
        $request->validate([
            'serviceType' => 'required|string',
        ]);

        $services = ServiceBranch::where('serviceType', $request->serviceType)->get();

        if ($services->isEmpty()) {
            return response()->json(['message' => 'No services found for this type'], 404);
        }

        return response()->json(['data' => $services], 200);
    }

    // Xóa dịch vụ theo ID
    public function delete($id)
    {
        $service = ServiceBranch::find($id);

        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        $service->delete();

        return response()->json(['message' => 'Service deleted successfully'], 200);
    }

    // Lấy toàn bộ dịch vụ
    public function getAllServices()
    {
        $services = ServiceBranch::all();
        
        return response()->json(['data' => $services], 200);
    }
    
}
