<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BankBranchModel;

class BankBranchController extends Controller
{
    /**
     * Display a listing of all bank branches.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $branches = BankBranchModel::all();
        return response()->json($branches, 200);
    }

    /**
     * Store a newly created bank branch.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validate incoming request
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'accountNumber' => 'required|string|max:255',
                'accountHolder' => 'required|string|max:255',
                'status' => 'required|string|in:Hiển thị,Ẩn',
                'logo' => 'required|string|',
                'token' => 'required|string',
                'branch' => 'required|string',
            ]);
    
            // Create a new bank branch
            $branch = BankBranchModel::create($validatedData);
    
            return response()->json([
                'message' => 'Bank branch created successfully.',
                'branch' => $branch,
                'status' => true,
            ], 201);
    
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json([
                'message' => 'Failed to create bank branch.',
                'error' => $e->getMessage(),
                'status' => false,
            ], 500);
        }
    }
    public function showdataByBranh(Request $request)
    {
        try {
            // Validate the incoming request
            $validatedData = $request->validate([
                'branch' => 'required|string',
            ]);
    
            // Fetch data for the specified branch
            $branchData = BankBranchModel::where('branch', $request->branch)->get();
    
            if ($branchData->isEmpty()) {
                return response()->json([
                    'message' => 'No data found for the specified branch.',
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
    
    public function showIdAndNameByBranch(Request $request)
{
    try {
        // Validate the incoming request
        $validatedData = $request->validate([
            'branch' => 'required|string',
        ]);

        // Fetch only _id and name for the specified branch
        $branchData = BankBranchModel::where('branch', $request->branch)
            ->get(['_id', 'name']); // Select only _id and name fields

        if ($branchData->isEmpty()) {
            return response()->json([
                'message' => 'No data found for the specified branch.',
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
public function showClientByBranch(Request $request)
{
    try {
        // Validate the incoming request
        $validatedData = $request->validate([
            'branch' => 'required|string',
            'id' => 'required|string', // Validate 'id'
        ]);

        // Fetch branch data with specified fields and conditions
        $branchData = BankBranchModel::where('branch', $request->branch)
            ->where('_id', $request->id) // Thêm điều kiện id
            ->get(['_id', 'id', 'name', 'accountHolder', 'accountNumber']); // Select fields

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

    /**
     * Display the specified bank branch.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $branch = BankBranchModel::find($id);

        if (!$branch) {
            return response()->json(['message' => 'Bank branch not found.'], 404);
        }

        return response()->json($branch, 200);
    }

    /**
     * Update the specified bank branch.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $branch = BankBranchModel::find($id);

        if (!$branch) {
            return response()->json(['message' => 'Bank branch not found.'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'account_holder' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:Hiển thị,Ẩn',
            'logo' => 'nullable|string|max:255',
            'token' => 'nullable|string|max:255',
        ]);

        $branch->update($validatedData);

        return response()->json([
            'message' => 'Bank branch updated successfully.',
            'branch' => $branch,
        ], 200);
    }

    /**
     * Remove the specified bank branch.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $branch = BankBranchModel::find($id);

        if (!$branch) {
            return response()->json(['message' => 'Bank branch not found.'], 404);
        }

        $branch->delete();

        return response()->json(['message' => 'Bank branch deleted successfully.'], 200);
    }
}
