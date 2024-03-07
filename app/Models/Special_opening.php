<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Special_opening extends Model
{
    use HasFactory;

    protected $table = "special_opening";

    protected $fillable =[
        'working_hours',
        'date',
        'vet_id'
    ];

    public $timestamps = false;

    public function vet() {
        return $this->belongsTo(Vet::class, 'vet_id', 'id');
    }
}
