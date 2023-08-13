<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoCandidato extends Model
{
    use HasFactory;
    public $timestamps=false;
    protected $table = 'tipocandidatos';
     
     protected $fillable = [
        'descripcion','icon','estado',
    ];
    //  public function candidatos()
    // {
    //     return $this->hasMany('App\Condidato', 'idtipocandidato', 'id');
     
    // }
}
