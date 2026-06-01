<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programa_solicitudes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('programas_id')->constrained('programas')->cascadeOnDelete();
            $table->string('status', 20)->default('pending');
            $table->string('telegram_chat_id')->nullable();
            $table->unsignedBigInteger('telegram_message_id')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['programas_id', 'status']);
            $table->index(['user_id', 'programas_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programa_solicitudes');
    }
};
