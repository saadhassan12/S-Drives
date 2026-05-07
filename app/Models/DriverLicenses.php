<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverLicenses extends Model
{
    //
    use HasFactory;
    protected $table = 'driver_licenses';
    protected $fillable = ['user_id', 'license_no', 'expiration_date', 'licenses_front_pic', 'licenses_back_pic', 'selfie_with_licenses'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
