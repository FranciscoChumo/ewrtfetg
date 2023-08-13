<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Condidatos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
           Schema::create('candidatos', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion',200);
            $table->integer('idlista')->unsigned();
            $table->integer('idtipocandidato')->unsigned();
            $table->string('foto',100)->nullable();
            $table->boolean('estado')->default(true);
            
            $table->foreign('idlista')
                  ->references('id')
                  ->on('listas');

             $table->foreign('idtipocandidato')
                  ->references('id')
                  ->on('tipocandidatos');

                  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('candidatos');
    }
}
