<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cure_type extends Model
{
    use HasFactory;

    protected $table = "cure_type";

    protected $fillable =[
        'type',
        'period'
    ];

    public $timestamps = false;

    public function cures() {
        return $this->hasMany(Cure::class);
    }
}
