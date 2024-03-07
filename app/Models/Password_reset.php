<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Password_reset extends Model
{
    use HasFactory;

    protected $table = "password_reset_tokens";

    protected $fillable =[
        'email',
        'token',
        'created_at'
    ];

    protected $primaryKey = 'email';

    public $timestamps = false;

    public function owner() {
        return $this->belongsTo(Owner::class, 'email', 'email');
    }

    public function vet() {
        return $this->belongsTo(Vet::class, 'email', 'email');
    }
}
