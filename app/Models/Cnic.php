<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cnic extends Model
{
    //
    use HasFactory;
    protected $table = 'driver_cnic';
    protected $fillable = ['user_id', 'cnic_no', 'exp_date', 'front_pic', 'back_pic', 'selfie_with_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
