<?php

namespace App\Http\Controllers;

use App\Models\MedicalService;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordDetail;
use Illuminate\Http\Request;

class MedicalServiceController extends Controller
{
    public function addService(Request $request, $medicalRecordID)
    {
        $request->validate([
            'services' => 'required|array',
            'services.*.nameService' => 'required|string',
            'services.*.descService' => 'nullable|string',
            'services.*.details' => 'required|array',
            'services.*.details.*.examinationsection' => 'required|string',
            'services.*.details.*.index' => 'required|string',
        ]);

        $medicalRecord = MedicalRecord::find($medicalRecordID);
        if (!$medicalRecord) {
            return response()->json(['error' => 'Medical record not found.'], 404);
        }

        try {
            foreach ($request->services as $service) {
                $medicalService = MedicalService::create([
                    'medicalrecordID' => $medicalRecord->_id,
                    'nameService' => $service['nameService'],
                    'descService' => $service['descService'] ?? null,
                ]);

                foreach ($service['details'] as $detail) {
                    MedicalRecordDetail::create([
                        'medicalserviceID' => $medicalService->_id,
                        'examinationsection' => $detail['examinationsection'],
                        'index' => $detail['index'],
                    ]);
                }
            }

            return response()->json(['message' => 'Medical services and details added successfully!'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to add medical services.', 'message' => $e->getMessage()], 500);
        }
    }

    public function addServiceKN(Request $request, $medicalRecordID)
    {
        $request->validate([
            'services.*.details' => 'required|array',
            'services.*.details.*.examinationsection' => 'required|string',
            'services.*.details.*.index' => 'required|string',
        ]);

        $medicalRecord = MedicalRecord::find($medicalRecordID);
        if (!$medicalRecord) {
            return response()->json(['error' => 'Medical record not found.'], 404);
        }

        try {
            foreach ($request->services as $service) {
                $nameService = 'Khám ngoại';
                $descService = 'Khám ngoại khoa';

                $medicalService = MedicalService::create([
                    'medicalrecordID' => $medicalRecord->_id,
                    'nameService' => $nameService,
                    'descService' => $descService,
                    'diagnosis' => $request->diagnosis
                ]);

                foreach ($service['details'] as $detail) {
                    MedicalRecordDetail::create([
                        'medicalserviceID' => $medicalService->_id,
                        'examinationsection' => $detail['examinationsection'],
                        'index' => $detail['index'],
                    ]);
                }
            }

            return response()->json(['message' => 'Medical services and details added successfully!'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to add medical services.', 'message' => $e->getMessage()], 500);
        }
    }

    public function addServiceKNO(Request $request, $medicalRecordID)
    {
        $request->validate([
            'services.*.details' => 'required|array',
            'services.*.details.*.examinationsection' => 'required|string',
            'services.*.details.*.index' => 'required|string',
        ]);

        $medicalRecord = MedicalRecord::find($medicalRecordID);
        if (!$medicalRecord) {
            return response()->json(['error' => 'Medical record not found.'], 404);
        }

        try {
            foreach ($request->services as $service) {
                $nameService = 'Khám nội';
                $descService = 'Khám nội khoa';

                $medicalService = MedicalService::create([
                    'medicalrecordID' => $medicalRecord->_id,
                    'nameService' => $nameService,
                    'descService' => $descService,
                    'diagnosis' => $request->diagnosis
                ]);

                foreach ($service['details'] as $detail) {
                    MedicalRecordDetail::create([
                        'medicalserviceID' => $medicalService->_id,
                        'examinationsection' => $detail['examinationsection'],
                        'index' => $detail['index'],
                    ]);
                }
            }

            return response()->json(['message' => 'Medical services and details added successfully!'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to add medical services.', 'message' => $e->getMessage()], 500);
        }
    }

    public function addServiceXNM(Request $request, $medicalRecordID)
    {
        $request->validate([
            'services.*.details' => 'required|array',
            'services.*.details.*.examinationsection' => 'required|string',
            'services.*.details.*.index' => 'required|string',
        ]);

        $medicalRecord = MedicalRecord::find($medicalRecordID);
        if (!$medicalRecord) {
            return response()->json(['error' => 'Medical record not found.'], 404);
        }

        try {
            foreach ($request->services as $service) {
                $nameService = 'Xét nghiệm máu';
                $descService = 'Xét nghiệm máu';

                $medicalService = MedicalService::create([
                    'medicalrecordID' => $medicalRecord->_id,
                    'nameService' => $nameService,
                    'descService' => $descService,
                    'diagnosis' => $request->diagnosis
                ]);

                foreach ($service['details'] as $detail) {
                    MedicalRecordDetail::create([
                        'medicalserviceID' => $medicalService->_id,
                        'examinationsection' => $detail['examinationsection'],
                        'index' => $detail['index'],
                    ]);
                }
            }

            return response()->json(['message' => 'Medical services and details added successfully!'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to add medical services.', 'message' => $e->getMessage()], 500);
        }
    }

    public function addServiceXNNT(Request $request, $medicalRecordID)
    {
        $request->validate([
            'services.*.details' => 'required|array',
            'services.*.details.*.examinationsection' => 'required|string',
            'services.*.details.*.index' => 'required|string',
        ]);

        $medicalRecord = MedicalRecord::find($medicalRecordID);
        if (!$medicalRecord) {
            return response()->json(['error' => 'Medical record not found.'], 404);
        }

        try {
            foreach ($request->services as $service) {
                $nameService = 'Xét nghiệm nước tiểu';
                $descService = 'Xét nghiệm nước tiểu';

                $medicalService = MedicalService::create([
                    'medicalrecordID' => $medicalRecord->_id,
                    'nameService' => $nameService,
                    'descService' => $descService,
                    'diagnosis' => $request->diagnosis
                ]);

                foreach ($service['details'] as $detail) {
                    MedicalRecordDetail::create([
                        'medicalserviceID' => $medicalService->_id,
                        'examinationsection' => $detail['examinationsection'],
                        'index' => $detail['index'],
                    ]);
                }
            }

            return response()->json(['message' => 'Medical services and details added successfully!'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to add medical services.', 'message' => $e->getMessage()], 500);
        }
    }

    public function addServiceXQ(Request $request, $medicalRecordID)
    {
        $request->validate([
            'services.*.details' => 'required|array',
            'services.*.details.*.examinationsection' => 'required|string',
            'services.*.details.*.index' => 'required|string',
        ]);

        $medicalRecord = MedicalRecord::find($medicalRecordID);
        if (!$medicalRecord) {
            return response()->json(['error' => 'Medical record not found.'], 404);
        }

        try {
            foreach ($request->services as $service) {
                $nameService = 'X-Quang';
                $descService = 'X-Quang';

                $medicalService = MedicalService::create([
                    'medicalrecordID' => $medicalRecord->_id,
                    'nameService' => $nameService,
                    'descService' => $descService,
                    'diagnosis' => $request->diagnosis
                ]);

                foreach ($service['details'] as $detail) {
                    MedicalRecordDetail::create([
                        'medicalserviceID' => $medicalService->_id,
                        'examinationsection' => $detail['examinationsection'],
                        'index' => $detail['index'],
                    ]);
                }
            }

            return response()->json(['message' => 'Medical services and details added successfully!'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to add medical services.', 'message' => $e->getMessage()], 500);
        }
    }    

    public function addRecordDetails(Request $request, $medicalServiceID)
    {
        $request->validate([
            'details' => 'required|array',
            'details.*.examinationsection' => 'required|string',
            'details.*.index' => 'required|string',
        ]);

        $medicalService = MedicalService::find($medicalServiceID);
        if (!$medicalService) {
            return response()->json(['error' => 'Medical service not found.'], 404);
        }

        try {
            foreach ($request->details as $detail) {
                MedicalRecordDetail::create([
                    'medicalserviceID' => $medicalService->_id,
                    'examinationsection' => $detail['examinationsection'],
                    'index' => $detail['index'],
                ]);
            }

            return response()->json(['message' => 'Medical record details added successfully!'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to add medical record details.', 'message' => $e->getMessage()], 500);
        }
    }
}
