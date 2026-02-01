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
        Schema::table('programas', function (Blueprint $table) {
            // 1. Creamos disk_name después de 'url' (que sí existe)
            $table->string('disk_name')->nullable()->after('url');

            // 2. Creamos file_path justo después del nuevo disk_name
            $table->string('file_path')->nullable()->after('disk_name');

            // 3. Hacemos que url sea opcional (por si usas disco local)
            $table->string('url')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->dropColumn('disk_name');
            $table->dropColumn('file_path');
            // Revertir url a no nulo sería complejo aquí, mejor lo dejamos así
        });
    }
};
