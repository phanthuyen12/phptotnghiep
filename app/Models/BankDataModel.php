<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class BankDataModel extends Model
{
    use HasFactory;
    protected $collection = 'transactions'; // Tên collection trong MongoDB

    protected $fillable = [
        'transactionID',
        'amount',
        'balance',
        'description',
        'transactionDate',
        'type',
    ];
}
