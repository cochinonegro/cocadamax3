<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('catalogos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('Producto');
            $table->enum('estado', ['activo', 'inactivo']);
            $table->string('Size');
            $table->year('Año');
            $table->string('Picture');
            $table->string('Description');
            $table->string('Reference_official');
            $table->string('Op_system');
            $table->string('Infocrack');
            $table->string('Date_add');
            $table->string('Link');
            $table->string('Proved');
            $table->string('Minimal_requirement');
            $table->string('Problems_installing');
            $table->string('ProductoAdditional_info');
            $table->string('Procedures_Installation');
            $table->string('Brand');
            $table->string('Compatible_with');
            $table->string('Additional_info');
            $table->string('Fuente');
            $table->string('Enlace_fuente');
            $table->string('Price');
            $table->string('Price_official');
            $table->string('Demo_on_youtube');
            $table->string('Categoria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogos');
    }
};
