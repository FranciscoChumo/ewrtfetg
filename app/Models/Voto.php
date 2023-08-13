<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voto extends Model
{
    use HasFactory;
    public $timestamps=false;
    protected $table = 'votos';
     
     protected $fillable = [
        'votos','idcandidato','user_id','estado'
    ];
}
