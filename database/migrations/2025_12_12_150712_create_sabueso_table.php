<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sabueso', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto_increment

            $table->unsignedInteger('cliente_id')->nullable();

            $table->string('telefono_cliente', 50)->nullable();

            $table->string('sabu_programa');

            $table->year('ano_sabu_programa')->nullable();

            $table->string('sabu_marca', 150)->nullable();

            $table->text('sabu_informacion')->nullable();
            $table->text('sabu_011')->nullable();
            $table->text('sabu_012')->nullable();
            $table->text('sabu_013')->nullable();
            $table->text('sabu_014')->nullable();

            $table->boolean('sabu_find')->default(false);
            $table->dateTime('fecha_sabu_find')->nullable();

            $table->boolean('sabu_vendido')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sabueso');
    }
};
