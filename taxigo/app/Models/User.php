<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Type;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    protected $with = ['getCountryDetails', 'getStateDetails'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'gender',
        'country_id',
        'state_id',
        'device',
        'status',
        'password',
        'role_id',
        'type',
        'image',
        'social_login'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function getCountryDetails()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function getStateDetails()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function getUserImageAttribute()
    {
        $image = asset('build/images/user/avatar-1.jpg');
        if ($this->image != null) {
            $filePath = asset('storage/customer') . '/' . $this->image;
            // if (!Storage::exists($this->image)) {
            $image = $filePath;
            // }
        }
        return $image;
    }

    public static function store($data)
    {
        try {
            $data['status'] = Type::ActiveUser;
            $data['type'] = Type::CUSTOMER;
            $user = self::create($data);
            return $user;
        } catch (\Throwable $th) {
            Log::error('Getting error of store user details in user model :' . $th->getMessage());
            return false;
        }
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }

    // Custom accessor for updated_at
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }

    // Optional: Custom accessor for deleted_at if using SoftDeletes
    public function getDeletedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d-m-Y') : null;
    }

    public function advertisementUserClick()
    {
        return $this->hasMany(AdvertisementUserClick::class,'user_id','id');
    }
}
