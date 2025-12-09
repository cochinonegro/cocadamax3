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
        Schema::table('clientes', function (Blueprint $table) {
        $table->string('category')->nullable()->change();
        $table->string('email')->nullable()->change();
        $table->string('os_required')->nullable()->change();
        $table->string('result_client')->nullable()->change();
        $table->text('observaciones')->nullable()->change();
        $table->date('date')->nullable()->change();
        $table->string('publicidad')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            //
        });
    }
};
