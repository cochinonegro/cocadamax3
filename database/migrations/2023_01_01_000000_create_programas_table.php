<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // SEGURIDAD: Solo creamos la tabla si no existe ya
        if (!Schema::hasTable('programas')) {
            Schema::create('programas', function (Blueprint $table) {
                $table->id();
                // Definimos las columnas básicas que necesita tu sistema
                $table->string('progname')->nullable(); 
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('programas');
    }
};