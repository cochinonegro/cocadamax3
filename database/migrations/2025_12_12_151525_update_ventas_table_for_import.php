<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {

            // NUEVOS CAMPOS (para que tu INSERT funcione)
            $table->integer('sort_order')->default(0)->after('id');

            $table->string('pago', 50)->nullable()->after('packv');

            $table->string('os_required')->nullable()->after('updated_at');

            $table->boolean('impago')->default(false)->after('os_required');

            $table->date('fecha_pago')->nullable()->after('impago');

            $table->text('anotacion_vta')->nullable()->after('fecha_pago');

            $table->boolean('pendiente')->default(false)->after('anotacion_vta');

            // AJUSTES PARA CONCORDAR CON TU SCRIPT
            $table->date('fecha_venta')->default('2025-07-01')->change();

            // En tu script: informacion_adicional es text (puede ser null)
            $table->text('informacion_adicional')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {

            // revert defaults/cambios
            $table->date('fecha_venta')->default('2025-12-09')->change(); // ajusta si tu default original era otro
            $table->text('informacion_adicional')->nullable(false)->change();

            // eliminar columnas agregadas
            $table->dropColumn([
                'sort_order',
                'pago',
                'os_required',
                'impago',
                'fecha_pago',
                'anotacion_vta',
                'pendiente',
            ]);
        });
    }
};
