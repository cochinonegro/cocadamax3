<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('company')->nullable()->after('category');
            $table->string('referencia')->nullable()->after('company');
            $table->string('nombre_whatsapp')->nullable()->after('referencia');
            $table->string('ciudad', 100)->nullable()->after('nombre_whatsapp');
            $table->text('comentario_info_cliente')->nullable()->after('ciudad');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn([
                'company',
                'referencia',
                'nombre_whatsapp',
                'ciudad',
                'comentario_info_cliente'
            ]);
        });
    }
};
