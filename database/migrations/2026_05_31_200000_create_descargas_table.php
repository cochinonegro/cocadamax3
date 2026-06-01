<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('descargas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('programas_id')->constrained('programas')->cascadeOnDelete();
            $table->timestamp('downloaded_at');
            $table->timestamps();

            $table->index('downloaded_at');
            $table->index(['user_id', 'downloaded_at']);
            $table->index(['programas_id', 'downloaded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('descargas');
    }
};
