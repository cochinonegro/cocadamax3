<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->json('gallery_images')->nullable()->after('foto');
            $table->json('installation_steps')->nullable()->after('gallery_images');
        });
    }

    public function down(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->dropColumn(['gallery_images', 'installation_steps']);
        });
    }
};
