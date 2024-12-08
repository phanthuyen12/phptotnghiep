<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class ServiceBranch extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'services';

    protected $primaryKey = '_id';

    protected $fillable = [
        'serviceCode',
        'serviceName',
        'serviceType',
        'tokenbranch',
        'serviceFees',
    ];

    public $timestamps = false;

    // Mối quan hệ 1-n: Một dịch vụ có thể có nhiều phòng khám
    public function clinics()
    {
        return $this->hasMany(ClinicsModel::class, 'selectedService', '_id');
    }
}
