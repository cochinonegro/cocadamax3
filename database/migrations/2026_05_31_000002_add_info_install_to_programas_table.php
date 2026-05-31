<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->text('info_install')->nullable()->after('installation_steps');
        });
    }

    public function down(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->dropColumn('info_install');
        });
    }
};
