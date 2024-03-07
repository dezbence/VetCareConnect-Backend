<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Owner extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = "owner";

    protected $fillable =[
        'name',
        'email',
        'password',
        'postal_code',
        'phone'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public $timestamps = false;

    public function pets(){
        return $this->hasMany(Pet::class);
    }

    public function password_reset() {
        return $this->belongsTo(Password_reset::class, 'email', 'email');
    }

}
