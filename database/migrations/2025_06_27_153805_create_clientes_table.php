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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('name');
            $table->date('date');
            $table->string('required_prog');
            $table->string('category')->nullable();
            $table->string('os_required');
            $table->string('email')->nullable();
            $table->string('publicidad')->nullable();
            $table->string('result_client')->nullable;
            $table->string('observaciones')->nullable();
            $table->unsignedInteger('phone'); // ← corregido
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};

