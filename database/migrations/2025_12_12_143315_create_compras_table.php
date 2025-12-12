<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto_increment

            $table->unsignedBigInteger('cliente_id');

            $table->string('producto');
            $table->string('os_required');

            $table->date('fecha_venta');

            $table->decimal('packv', 10, 2);

            $table->text('informacion_adicional')->nullable();

            $table->string('dia')->nullable();

            $table->timestamps();

            // FK
            $table->foreign('cliente_id')
                ->references('id')
                ->on('clientes')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
