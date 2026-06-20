<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ride extends Model
{
    //
    use HasFactory;
    protected $table = 'rides';
    protected $fillable = ['user_id', 'start_latitude', 'start_longitude', 'end_latitude', 'end_longitude', 'start','destination' ,'notified_driver_id' ,'promo_code'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
    public function user_pe()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function vehicles()
    {
        return $this->hasOne(Vehicles::class, 'user_id', 'driver_id');
    }
    public function ratings()
    {
        return $this->hasMany(Rating::class, 'ride_id');
    }
    public function vehicleCategory()
{
    return $this->belongsTo(VehicleCategory::class, 'vehicle_category_id');
}

    public function chatRoom()
    {
        return $this->hasOne(ChatRoom::class);
    }
}
