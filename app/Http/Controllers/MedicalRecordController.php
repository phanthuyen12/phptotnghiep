<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\MedicalRecordDetail;
use App\Models\MedicalService;
use App\Models\Prescription;
use App\Models\User;
use App\Models\Branch;
use App\Models\MedicalRecordBook;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function addRecord(Request $request)
    {
        $request->validate([
            'branchID' => 'required',
            'medicalrecordbookID' => 'required',
            'userID' => 'required',
            'date' => 'required',
            'resuft' => 'nullable',
            'unit' => 'required',

            'services' => 'required|array',
            'services.*.nameService' => 'required',
            'services.*.descService' => 'nullable',
            'services.*.details' => 'required|array',
            'services.*.details.*.examinationsection' => 'required',
            'services.*.details.*.index' => 'required',

            'prescriptions' => 'required|array',
            'prescriptions.*.namePrescription' => 'required',
            'prescriptions.*.quatity' => 'required|integer',
            'prescriptions.*.unitOfMeasurement' => 'required',
            'prescriptions.*.userManual' => 'required',
        ]);

        try {
            $branch = Branch::find($request->branchID);
            if (!$branch) {
                return response()->json(['error' => 'Branch not found.'], 404);
            }

            $medicalRecordBook = MedicalRecordBook::find($request->medicalrecordbookID);
            if (!$medicalRecordBook) {
                return response()->json(['error' => 'Medical record book not found.'], 404);
            }

            $user = User::find($request->userID);
            if (!$user) {
                return response()->json(['error' => 'User not found.'], 404);
            }

            $medicalRecord = MedicalRecord::create([
                'branchID' => $request->branchID,
                'medicalrecordbookID' => $request->medicalrecordbookID,
                'userID' => $request->userID,
                'date' => $request->date,
                'resuft' => $request->resuft,
                'unit' => $request->unit,
                'status' => 'unfinish',
                'diagnosis' => null,
            ]);

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

            foreach ($request->prescriptions as $prescription) {
                Prescription::create([
                    'medicalrecordID' => $medicalRecord->_id,
                    'namePrescription' => $prescription['namePrescription'],
                    'quatity' => $prescription['quatity'],
                    'unitOfMeasurement' => $prescription['unitOfMeasurement'],
                    'userManual' => $prescription['userManual'],
                ]);
            }

            return response()->json(['message' => 'Medical record created successfully!', 'data' => $medicalRecord], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create medical record.', 'message' => $e->getMessage()], 500);
        }
    }

    public function getUnfinishedMedicalRecords()
    {
        $unfinishedRecords = MedicalRecord::where('status', 'unfinish')->get();
        return response()->json([
            'message' => 'Danh sách bệnh án chưa hoàn thành',
            'data' => $unfinishedRecords
        ], 200);
    }

    public function getFinishedMedicalRecords()
    {
        $unfinishedRecords = MedicalRecord::where('status', 'finish')->get();
        return response()->json([
            'message' => 'Danh sách bệnh án đã hoàn thành',
            'data' => $unfinishedRecords
        ], 200);
    }

    public function updateMedicalRecordStatus(Request $request, $id)
    {
        $medicalRecord = MedicalRecord::find($id);

        if (!$medicalRecord) {
            return response()->json(['error' => 'Medical record not found.'], 404);
        }

        $medicalRecord->status = 'finished';
        $medicalRecord->save();

        return response()->json([
            'message' => 'Medical record status updated successfully!',
            'data' => $medicalRecord
        ], 200);
    }

    public function updateMedicalRecordDiagnosis(Request $request, $id)
    {
        $request->validate([
            'diagnosis' => 'required|string'
        ]);

        $medicalRecord = MedicalRecord::find($id);

        if (!$medicalRecord) {
            return response()->json(['error' => 'Medical record not found.'], 404);
        }

        $medicalRecord->diagnosis = $request->diagnosis;
        $medicalRecord->save();

        return response()->json([
            'message' => 'Medical record diagnosis updated successfully!',
            'data' => $medicalRecord
        ], 200);
    }

    public function getAllMedicalRecordsWithPatientInfo()
    {
        $records = MedicalRecord::with('medicalRecordBook')->get();

        return response()->json($records, 200);
    }
}
