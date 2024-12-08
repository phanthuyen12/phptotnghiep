<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $collection = 'branches';
    protected $fillable = [
        'tokenbranch',
        'branchname',
        'branchaddress',
        'branchphone',
        'branchbusinesslicense',
        'tokeorg',
        'branchemail'
    ];

    // Định nghĩa mối quan hệ với Department (Khoa) theo tokenbranch
    public function departments()
    {
        return $this->hasMany(DepartmentModel::class, 'tokenbranch', 'tokenbranch'); // Một bệnh viện có nhiều khoa
    }
}
