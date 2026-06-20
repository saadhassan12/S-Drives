<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;



class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'mobile_number',
        'otp_verified_at',
        'email',
        'profile_image',
        'latitude',
        'longitude',
        'profile_picture',
        'role',
        'last_login_at',
        'device_token',
        'is_online',
        'last_seen_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_online' => 'boolean',
            'last_seen_at' => 'datetime',
        ];
    }

    public function driverCnic()
    {
        return $this->hasOne(Cnic::class);
    }
    public function driverliceses()
    {
        return $this->hasOne(DriverLicenses::class);
    }
    public function vehicles()
    {
        return $this->hasOne(Vehicles::class);
    }
    public function ride()
    {
        return $this->hasOne(Ride::class);
    }
    public function favaddress()
    {
        return $this->hasOne(FavAddress::class);
    }

    public function passengerChatRooms()
    {
        return $this->hasMany(ChatRoom::class, 'passenger_id');
    }

    public function driverChatRooms()
    {
        return $this->hasMany(ChatRoom::class, 'driver_id');
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }
   
}
