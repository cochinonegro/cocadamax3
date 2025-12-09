<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

public function up(): void
{
    Schema::table('programas', function (Blueprint $table) {
        $table->string('enlace')->nullable();
    });
}

public function down(): void
{
    Schema::table('programas', function (Blueprint $table) {
        $table->dropColumn('enlace');
    });
}

    /**
     * Reverse the migrations.
     */
    /**public function down(): void
    {
        Schema::table('notas', function (Blueprint $table) {
            //
        });
    }**/
};
