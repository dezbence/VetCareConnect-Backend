<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opening extends Model
{
    use HasFactory;

    protected $table = "opening";

    protected $fillable =[
        'working_hours',
        'day',
        'vet_id',
    ];

    public $timestamps = false;

    public function vet() {
        return $this->belongsTo(Vet::class, 'vet_id', 'id');
    }
}
