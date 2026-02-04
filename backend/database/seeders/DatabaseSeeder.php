<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ClubSeeder::class,
            PermissionsSeeder::class,
            NotificacoesTiposSeeder::class,
            ConfiguracaoClubSeeder::class,
            AdminUserSeeder::class,
            QuickStartSeeder::class,
        ]);
    }
}
