<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('category')->nullable()->after('name');

            $table->string('phone', 20)->nullable()->after('email');

            $table->string('progname')->nullable()->after('phone');

            $table->string('sistema')
                ->default('default')
                ->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'category',
                'phone',
                'progname',
                'sistema',
            ]);
        });
    }
};
