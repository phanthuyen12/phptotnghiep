<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use App\Models\BankDataModel;
use App\Models\PatientBillModel;
use Carbon\Carbon;

class ApiController extends Controller
{
    public function getApiHistory()
    {
        // URL API
        $url = 'https://api.sieuthicode.net/historyapimbbankv2/54d24ec60eedc663618e6697e074cc7e';
    
        try {
            // Gửi request GET
            $response = Http::get($url);
            $data = $response['status'];
            // Kiểm tra nếu request thành công
            if ($data === "success") {
                $transactions = $response['transactions'];
                $newTransactions = [];
    
                foreach ($transactions as $transaction) {
                    // Kiểm tra nếu giao dịch đã tồn tại
                    if (!BankDataModel::where('transactionID', $transaction['transactionID'])->exists()) {
                        // Chuyển trường 'description' thành chữ thường
                        if (isset($transaction['description'])) {
                            $transaction['description'] = strtolower($transaction['description']);
                        }
    
                        // Tạo bản ghi mới
                        $newTransactions[] = BankDataModel::create($transaction);
                    }
                }
    
                return response()->json([
                    'success' => true,
                    'newTransactions' => $newTransactions,
                ]);
            } else {
                // Nếu request thất bại
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch data from API',
                    'status' => $response->status(),
                ], $response->status());
            }
        } catch (\Exception $e) {
            // Xử lý lỗi ngoại lệ
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function checkTransaction(Request $request)
    {
        // Validate dữ liệu đầu vào
        $data = $request->validate([
            'description' => 'required', // Có thể là chuỗi hoặc mảng
        ]);
        
        // Kiểm tra nếu description là mảng
        if ($data['description']) {
            // Lọc các giao dịch có mô tả chứa description
            $transactions = BankDataModel::where('description', 'like', "%{$data['description']}%")->get();
        }
        
        // Nếu có giao dịch, trả về dữ liệu
        if ($transactions->isNotEmpty()) {
            // Duyệt qua các giao dịch và cập nhật status
            foreach ($transactions as $transaction) {
                $amount = floatval($transaction->amount); // Chuyển đổi amount thành số thập phân
        
                // Tìm hóa đơn tương ứng với mô tả và số tiền
                $bill = PatientBillModel::where('medicalRecordCode', $data['description'])
                    ->where('totalsum', $amount)
                    ->first();
        
                // Nếu tìm thấy hóa đơn, cập nhật status
                if ($bill) {
                    $bill->status = true;
                    $bill->save();
                }
            }
    
            // Trả về dữ liệu giao dịch và hóa đơn
            return response()->json([
                'success' => true,
                'data' => $transactions, // Trả về tất cả giao dịch
                'descriptions' => $transactions->pluck('description'), // Trả về mô tả của tất cả giao dịch
                'bill' => isset($bill) ? $bill : null // Trả về hóa đơn, nếu tìm thấy
            ], 200);
        } else {
            // Nếu không tìm thấy giao dịch
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found.',
            ], 404);
        }
    }
    
    public function getMonthlyTransactions(Request $request)
    {
        // Lấy tháng và năm từ request, nếu không có thì lấy tháng hiện tại
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        // Lọc các giao dịch trong tháng và năm
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $transactions = BankDataModel::whereBetween('transactionDate', [$startDate, $endDate])
            ->orderBy('transactionDate', 'asc')  // Sắp xếp theo ngày giao dịch
            ->get();

        // Lấy danh sách giao dịch theo ngày trong tháng
        $groupedByDate = $transactions->groupBy(function($date) {
            return Carbon::parse($date->transactionDate)->format('Y-m-d');
        });

        return response()->json($groupedByDate);
    }
    public function getAllTransactions()
    {
        $transactions = BankDataModel::orderBy('transactionDate', 'asc')->get();
        return response()->json($transactions);
    }


}
