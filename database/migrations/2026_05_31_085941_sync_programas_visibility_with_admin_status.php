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
            ->where(function ($query): void {
                $query
                    ->whereNull('show_until')
                    ->orWhere('show_until', '<', now());
            })
            ->update([
                'show_until' => $visibleUntil,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        //
    }
};
