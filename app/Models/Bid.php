<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'ride_id',
        'driver_id',
        'amount',
        'status',
        'time',
        'user_id'
    ];
     public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
       public function vehicles()
{
    return $this->hasOne(Vehicles::class, 'user_id', 'driver_id');
}
}
