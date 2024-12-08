<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    // thêm đơn thuốc
    public function addPrescription(Request $request, $medicalRecordID)
    {
        $request->validate([
            'prescriptions' => 'required|array',
            'prescriptions.*.namePrescription' => 'required|string',
            'prescriptions.*.quantity' => 'required|integer',
            'prescriptions.*.unitOfMeasurement' => 'required|string',
            'prescriptions.*.userManual' => 'required|string',
        ]);

        $medicalRecord = MedicalRecord::find($medicalRecordID);
        if (!$medicalRecord) {
            return response()->json(['error' => 'Medical record not found.'], 404);
        }

        try {
            foreach ($request->prescriptions as $prescriptionData) {
                Prescription::create([
                    'medicalrecordID' => $medicalRecord->_id,
                    'namePrescription' => $prescriptionData['namePrescription'],
                    'quantity' => $prescriptionData['quantity'],
                    'unitOfMeasurement' => $prescriptionData['unitOfMeasurement'],
                    'userManual' => $prescriptionData['userManual'],
                ]);
            }

            return response()->json(['message' => 'Prescriptions added successfully!'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to add prescriptions.', 'message' => $e->getMessage()], 500);
        }
    }
}
