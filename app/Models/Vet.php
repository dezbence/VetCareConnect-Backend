<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Vet extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = "vet";

    protected $fillable =[
        'name',
        'email',
        'password',
        'address',
        'postal_code',
        'stamp_number',
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

    public function cures() {
        return $this->hasMany(Cure::class);
    }

    public function special_openings() {
        return $this->hasMany(Special_opening::class);
    }

    public function openings() {
        return $this->hasMany(Opening::class);
    }

    public function password_reset() {
        return $this->belongsTo(Password_reset::class, 'email', 'email');
    }

}
