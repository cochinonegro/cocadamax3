<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CocadaMaxSqlSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CocadaMaxClientesSeeder::class,
            CocadaMaxProgramasSeeder::class,
        ]);
    }
}
