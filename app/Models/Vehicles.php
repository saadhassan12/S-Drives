<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicles extends Model
{
    //
    use HasFactory;
    protected $table = 'vehicles';
    protected $fillable = ['user_id', 'vehicle_category_id', 'engine', 'manufacture_year', 'manufacture_model', 'manufacture_company','courier_servies','registration_number','vehicle_front_pic','vehicle_back_pic','vehicle_dashboard','vehicle_certificate_front','vehicle_certificate_back','interior'];
    protected $casts = [
    'courier_servies' => 'integer',
];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function vehicle()
    {
        return $this->belongsTo(VehicleCategory::class, 'vehicle_category_id');
    }
}
