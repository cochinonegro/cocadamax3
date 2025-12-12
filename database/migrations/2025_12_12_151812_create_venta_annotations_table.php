<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venta_annotations', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto_increment

            $table->unsignedBigInteger('venta_id');

            $table->text('contenido');

            $table->timestamps();

            // FK opcional (recomendada)
            $table->foreign('venta_id')
                ->references('id')
                ->on('ventas')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venta_annotations');
    }
};
