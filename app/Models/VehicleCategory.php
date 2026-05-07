<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vehicle_categories';
    
    protected $fillable = ['id','name', 'created_at', 'updated_at'];

    public function getNameAttribute()
    {
        return ucfirst($this->attributes['name']);
    }
    protected $hidden = ['deleted_at', 'created_at', 'updated_at'];

}
