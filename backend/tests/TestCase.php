<?php

namespace Tests;

use App\Models\Club;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Criar e autenticar utilizador com clube
     */
    protected function createAuthenticatedUser(string $role = 'admin', ?Club $club = null): User
    {
        $club = $club ?? Club::factory()->create();
        
        $user = User::factory()->create([
            'club_id' => $club->id,
        ]);
        
        $user->assignRole($role);
        
        $this->actingAs($user, 'sanctum');
        
        return $user;
    }
    
    /**
     * Criar clube para testes
     */
    protected function createClub(): Club
    {
        return Club::factory()->create();
    }
    
    /**
     * Criar utilizador de outro clube (para testar isolamento)
     */
    protected function createUserFromDifferentClub(): User
    {
        $differentClub = Club::factory()->create();
        
        return User::factory()->create([
            'club_id' => $differentClub->id,
        ]);
    }
}

