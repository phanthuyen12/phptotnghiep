<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\BankDataModel;

class ApiController extends Controller
{
    public function getApiHistory()
    {
        $url = 'https://api.sieuthicode.net/historyapimbbankv2/54d24ec60eedc663618e6697e074cc7e';

            $response = Http::get($url);

            if ($response->successful() && isset($response['data']['transactions'])) {
                $transactions = $response['data']['transactions'];
                $newTransactions = [];

                foreach ($transactions as $transaction) {
                    if (!BankDataModel::where('transactionID', $transaction['transactionID'])->exists()) {
                        $newTransactions[] = BankDataModel::create($transaction);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'New transactions saved',
                    'newTransactions' => $newTransactions,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No transactions found or invalid API response',
                'response' =>$response
            ]);
        
    }
}
