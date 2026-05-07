<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    //
    use HasFactory;

    // Allow mass assignment for the specified columns
    protected $fillable = [
        'ride_id',
        'rated_by',
        'rated_to',
        'rating',
        'comment',
    ];
}
