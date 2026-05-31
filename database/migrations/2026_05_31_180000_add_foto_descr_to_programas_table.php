<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->string('foto_descr1')->nullable()->after('description');
            $table->string('foto_descr2')->nullable()->after('foto_descr1');
        });
    }

    public function down(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->dropColumn(['foto_descr1', 'foto_descr2']);
        });
    }
};
