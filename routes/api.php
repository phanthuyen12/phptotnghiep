<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MedicalRecordBookController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\MedicalServiceController;
use App\Http\Controllers\PrescriptionController;
use  App\Http\Controllers\ServiceController;
use  App\Http\Controllers\MedicalScheduleController;
use  App\Http\Controllers\MedicalDataController;
use  App\Http\Controllers\MedicalConclusionController;
use App\Http\Controllers\ClinicsController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PatientBillController;
use App\Http\Controllers\BankBranchController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\DoctorController;

// thêm tổ chứcBankBranchController
Route::post('/organizations', [OrganizationController::class, 'addOrganization']);

// chi tiết tổ chức (ghi cho cóa)
Route::get('/organizations/{id}', [OrganizationController::class, 'getOrganizationDetails']);

// đổi mật khẩu cho tài khoản admin của tổ chức (ghi cho cóa chắc là kh xài đâu)
Route::put('/organizations/{id}/change-password', [OrganizationController::class, 'changePassword']);

// cập nhập tổ chức
Route::put('/organizations/{id}', [OrganizationController::class, 'updateOrganization']);

// xuất toàn bộ tổ chức
Route::get('/organizations', [OrganizationController::class, 'getAllOrganization']);

// thêm chi nhánh
Route::post('/branches', [BranchController::class, 'addBranch']);

// cập nhập chi nhánh
Route::put('/branches/{id}', [BranchController::class, 'updateBranch']);

// xóa chi nhánh
Route::delete('/branches/{id}', [BranchController::class, 'deleteBranch']);

// xuất toàn bộ chi nhánh
Route::get('/branches', [BranchController::class, 'getAllBranches']);

// thêm tài khoản admin or doctor
Route::post('/users', [UserController::class, 'createuser']);
Route::post('/users/count', [UserController::class, 'countUsersByBranch']);
Route::get('/users', [UserController::class, 'getAvailableUsers']);
Route::post('/users/bybranch', [UserController::class, 'getAvailableUsersByBranch']);
Route::get('/userss', [ClinicsController::class, 'getUnassignedUsers']);

// cập nhập tài khoản (ghi cho cóa)
Route::put('/users/{cccd}', [UserController::class, 'updateuser']);

// đổi mật khẩu user (ghi cho cóa)
Route::post('/users/{cccd}/change-password', [UserController::class, 'changePassword']);

// xuất danh sách bác sĩ
Route::get('/doctors', [UserController::class, 'getAllDoctors']);
// Route::post('/doctors', [UserController::class, 'createDoctor']);

// đăng nhập (ghi cho cóa)
Route::post('/login', [AuthController::class, 'login']);

// thêm sổ khám
Route::get('/medicalrecordbooks', [MedicalRecordBookController::class, 'getAllMedicalRecordBook']);
Route::post('/medical/cccd', [MedicalRecordBookController::class, 'getMedicalRecordByCCCD']);
Route::post('/medical/code', [MedicalRecordBookController::class, 'getMedicalRecordByCode']);

// xuất tất cả sổ khám
Route::post('/medical-record-books', [MedicalRecordBookController::class, 'addMedicalRecordBook']);
Route::post('/medicalbook/count', [MedicalRecordBookController::class, 'countMedicalRecordsByBranch']);
Route::post('/medicalbook/count-by-organization', [MedicalRecordBookController::class, 'countMedicalRecordsByOrganization']);

// tìm bệnh nhân theo căn cước công dân và tên
Route::post('/patients/search', [MedicalRecordBookController::class, 'searchPatients']);

// show dữ liệu bệnh nhân khám bệnh tại 1 bệnh viện “mongodb”
Route::get('/medicalbook-byorg/{tokenorg}', [MedicalRecordBookController::class, 'getPatientsByHospital']);

// show dữ liệu bệnh nhân khám bệnh tại chi nhánh
Route::get('/medicalbook-bybranch/{tokenbranch}', [MedicalRecordBookController::class, 'getPatientsByBranch']);

Route::get('/doctors/organization/{tokenorg}', [DoctorController::class, 'getDoctorsByOrganizationToken']);
Route::get('/doctors/branch/{tokenbranch}', [DoctorController::class, 'getDoctorsByBranchToken']);

// thêm bệnh án
Route::post('/medical-records', [MedicalRecordController::class, 'addRecord']);

// xuất tẩ cả bệnh án chưa khám xong
Route::get('/medical-records-unfinish', [MedicalRecordController::class, 'getUnfinishedMedicalRecords']);

// xuất tất cả bệnh án đã khám
Route::get('/medical-records-finish', [MedicalRecordController::class, 'getFinishedMedicalRecords']);

// thay đổi trạng thái hệ thống
Route::put('/medical-records/{id}/status', [MedicalRecordController::class, 'updateMedicalRecordStatus']);

// thêm chuẩn đoán cho bệnh án
Route::put('/medical-records/{id}/diagnosis', [MedicalRecordController::class, 'updateMedicalRecordDiagnosis']);

// xuất tất cả bệnh án
Route::get('/all-medical-records-with-patient-info', [MedicalRecordController::class, 'getAllMedicalRecordsWithPatientInfo']);

// thêm dịch vụ khám (ghi cho form động mà xài có định ròi nên để cho cóa thôi)
Route::post('/medical-records/{id}/services', [MedicalServiceController::class, 'addService']);

// thêm chi tiết cho dịch vụ khám ngoại
Route::post('/medical-records/{id}/serviceKN', [MedicalServiceController::class, 'addServiceKN']);

// thêm chi tiết cho dịch vụ khám nội
Route::post('/medical-records/{id}/serviceKNO', [MedicalServiceController::class, 'addServiceKNO']);

// thêm chi tiết cho dịch vụ xét nghiệm máu
Route::post('/medical-records/{id}/serviceXNM', [MedicalServiceController::class, 'addServiceXNM']);

// thêm chi tiết cho dịch vụ xét nghiệm nước tiểu
Route::post('/medical-records/{id}/serviceXNNT', [MedicalServiceController::class, 'addServiceXNNT']);

// thêm chi tiết dịch vụ cho xquang
Route::post('/medical-records/{id}/serviceXQ', [MedicalServiceController::class, 'addServiceXQ']);

// thêm chi tiết cho mấy cái dịch vụ (mà dùng cho form động này bỏ để lại cho cóa thôi)
Route::post('/medical-records/{id}/details', [MedicalServiceController::class, 'addRecordDetails']);

// thêm đơn thuốc
Route::post('/medical-records/{id}/prescriptions', [PrescriptionController::class, 'addPrescription']);



Route::post('/services/create', [ServiceController::class, 'create']);
Route::post('/services', [ServiceController::class, 'getByBranch']);
Route::post('/services/id', [ServiceController::class, 'getById']);
Route::get('/services', [ServiceController::class, 'getAllServices']);
Route::post('/services-bytype', [ServiceController::class, 'getByServiceType']);

Route::post('/check-by-name', [UserController::class, 'getUsersBySpecialized']);

Route::post('/check-services', [ServiceController::class, 'checkServiceByToken']);
Route::post('/schedule-create', [MedicalScheduleController::class, 'addMedicalSchedule']);
Route::post('/schedule', [MedicalScheduleController::class, 'index']);
Route::post('/schedule/bymedical', [MedicalScheduleController::class, 'getByPatient']);
Route::post('/schedule/bydoctor', [MedicalScheduleController::class, 'getByDoctor']);
Route::post('/schedule/byclinics', [MedicalScheduleController::class, 'getByClinics']);
Route::post('/schedule/update/reception', [MedicalScheduleController::class, 'updateAcceptedByDoctor']);




Route::post('/medicaldata/add', [MedicalDataController::class, 'store']);
Route::post('/medicaldata/bycode', [MedicalDataController::class, 'getMedicalRecordByCode']);
Route::post('/medicaldata/code', [MedicalDataController::class, 'getMedicalRecordByCodeOne']);
Route::post('/medicaldata/bydoctor', [MedicalDataController::class, 'getMedicalRecordByDoctorToken']);
Route::post('/medicalconclusion/add', [MedicalConclusionController::class, 'add']);
Route::post('/medicalconclusion/getbycode', [MedicalConclusionController::class, 'getByCode']);

//clinics
Route::post('/clinics/add', [ClinicsController::class, 'StoreClinics']);
Route::post('/clinics', [ClinicsController::class, 'getClinicsAllByBranch']);
Route::post('/clinics/user', [ClinicsController::class, 'getClinicByDoctors']);
Route::post('/clinics/bydepartmenttype', [ClinicsController::class, 'getClinicsByDepartmentType']);
Route::get('/clinics/{id}', [ClinicsController::class, 'getClinicsAll']);
Route::post('/clinics/update/{id}', [ClinicsController::class, 'UpdateClinics']);
Route::post('/clinics/delete', [ClinicsController::class, 'DeleteClinics']);

// DepartMentMonel.php
Route::post('/department/add', [DepartmentController::class, 'Store']);
Route::post('/department/bybranch', [DepartmentController::class, 'getDepartmentsByBranch']);
Route::post('/department/delete', [DepartmentController::class, 'destroy']);


// PatientBillController

Route::post('/PatientBillController/add', [PatientBillController::class, 'addBill']);
Route::post('/PatientBillController/pycode', [PatientBillController::class, 'showBillByID']);
Route::post('/PatientBillController/pyid', [PatientBillController::class, 'getById']);

//BankBranchController showIdAndNameByBranch
Route::post('/brank/add', [BankBranchController::class, 'store']);
Route::post('/brank/bybranch', [BankBranchController::class, 'showdataByBranh']);
Route::post('/brank/client', [BankBranchController::class, 'showIdAndNameByBranch']);
Route::post('/brank/idclient', [BankBranchController::class, 'showClientByBranch']);



// ApiController checkTransaction
Route::get('/brank/cronjob', [ApiController::class, 'getApiHistory']);
Route::post('/brank/checktransaction', [ApiController::class, 'checkTransaction']);
Route::post('/brank/monthly', [ApiController::class, 'getMonthlyTransactions']);
Route::get('/brank/all', [ApiController::class, 'getAllTransactions']);
