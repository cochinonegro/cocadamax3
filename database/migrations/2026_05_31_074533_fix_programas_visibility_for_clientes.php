<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $visibleUntil = now()->addYear();

        DB::table('programas')
            ->where('show', true)
            ->whereNull('show_until')
            ->update([
                'show_until' => $visibleUntil,
                'updated_at' => now(),
            ]);

        DB::table('programas')
            ->where('show', false)
            ->update([
                'show' => true,
                'show_until' => $visibleUntil,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // No revert: visibility flags cannot be restored reliably.
    }
};
