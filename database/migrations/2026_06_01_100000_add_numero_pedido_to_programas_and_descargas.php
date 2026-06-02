<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->unsignedBigInteger('numero_pedido')->nullable()->after('pedidos_visible_until');
            $table->index('numero_pedido');
        });

        Schema::table('descargas', function (Blueprint $table) {
            $table->unsignedBigInteger('numero_pedido')->nullable()->after('programas_id');
            $table->index('numero_pedido');
        });
    }

    public function down(): void
    {
        Schema::table('descargas', function (Blueprint $table) {
            $table->dropIndex(['numero_pedido']);
            $table->dropColumn('numero_pedido');
        });

        Schema::table('programas', function (Blueprint $table) {
            $table->dropIndex(['numero_pedido']);
            $table->dropColumn('numero_pedido');
        });
    }
};
