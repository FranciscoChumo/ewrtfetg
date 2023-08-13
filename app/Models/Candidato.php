<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidato extends Model
{
    use HasFactory;
    public $timestamps=false;
    protected $table = 'candidatos';
     
     protected $fillable = [
        'descripcion','foto','idlista','idtipocandidato','estado'
    ];
}
