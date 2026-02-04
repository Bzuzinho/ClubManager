<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@admin.pt'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );

        // Assign admin role
        $user->assignRole('admin');

        // Associar ao clube BSCN
        $club = \App\Models\Club::where('abreviatura', 'BSCN')->first();
        if ($club) {
            \App\Models\ClubUser::firstOrCreate([
                'club_id' => $club->id,
                'user_id' => $user->id,
            ], [
                'role_no_clube' => 'admin',
                'ativo' => true,
            ]);
        }
    }
}
