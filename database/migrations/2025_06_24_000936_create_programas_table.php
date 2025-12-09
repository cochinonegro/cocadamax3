<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programas', function (Blueprint $table) {
            $table->id();
            $table->string("progname");
            $table->string("year_prog");
            $table->string("size");
            $table->string("os_required");
            $table->string("level_inst");
            $table->text("description");
            $table->string("category");
            $table->string("working");
            $table->date("date_add");
            $table->unsignedBigInteger("program_id");
            $table->timestamps();
            //$table->text("content");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programas');
    }
};
