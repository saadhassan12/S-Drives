<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavAddress extends Model
{
    use HasFactory;
    protected $table = 'favorite_addresses';
    protected $fillable = ['user_id', 'title', 'address', 'name', 'lat', 'long'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
