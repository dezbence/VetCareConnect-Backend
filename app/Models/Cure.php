<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cure extends Model
{
    use HasFactory;

    protected $table = "cure";

    protected $fillable =[
        'date',
        'pet_id',
        'cure_type_id',
        'vet_id'
    ];

    public $timestamps = false;
    
    public function pet() {
        return $this->belongsTo(Pet::class, 'pet_id', 'id');
    }

    public function vet() {
        return $this->belongsTo(Vet::class, 'vet_id', 'id');
    }

    public function cure_type() {
        return $this->belongsTo(Cure_type::class, 'cure_type_id', 'id');
    }
}
