<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            // Campo del error
            $table->timestamp('show_until')->nullable()->after('show');

            // Campos que también faltan en tu INSERT
            $table->string('required')->nullable()->after('company');
            $table->string('idioma')->nullable()->after('required');
        });
    }

    public function down(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->dropColumn(['show_until', 'required', 'idioma']);
        });
    }
};
