<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CancelRide extends Model
{
    use HasFactory;
    protected $table = 'cancel_ride';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ride_id',
        'user_id',
        'reason',
        'canceled_by',
    ];

    /**
     * Relationship with the Ride model.
     */
    public function ride()
    {
        return $this->belongsTo(Ride::class);
    }

    /**
     * Relationship with the User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

