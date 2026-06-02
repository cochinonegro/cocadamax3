<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programa_solicitudes', function (Blueprint $table) {
            $table->decimal('precio_acordado', 10, 2)->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('programa_solicitudes', function (Blueprint $table) {
            $table->dropColumn('precio_acordado');
        });
    }
};
