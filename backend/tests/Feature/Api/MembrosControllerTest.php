<?php

namespace Tests\Feature\Api;

use App\Models\Club;
use App\Models\Membro;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembrosControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Club $club;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->club = $this->createClub();
        $this->user = $this->createAuthenticatedUser('admin', $this->club);
    }

    /** @test */
    public function it_can_list_membros_from_authenticated_users_club()
    {
        // Criar membros do clube do utilizador autenticado
        Membro::factory()->count(3)->create(['club_id' => $this->club->id]);
        
        // Criar membros de outro clube (não devem aparecer)
        $otherClub = $this->createClub();
        Membro::factory()->count(2)->create(['club_id' => $otherClub->id]);

        $response = $this->getJson('/api/v2/membros');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'numero_socio', 'estado', 'user']
                ]
            ]);
    }

    /** @test */
    public function it_cannot_list_membros_without_permission()
    {
        // Criar utilizador sem permissão
        $userWithoutPermission = User::factory()->create([
            'club_id' => $this->club->id,
        ]);
        
        $this->actingAs($userWithoutPermission, 'sanctum');

        $response = $this->getJson('/api/v2/membros');

        $response->assertStatus(403);
    }

    /** @test */
    public function it_can_create_a_membro()
    {
        $data = [
            'user' => [
                'name' => 'João Silva',
                'email' => 'joao@example.com',
            ],
            'dados_pessoais' => [
                'nome_completo' => 'João Pedro Silva',
                'data_nascimento' => '1990-05-15',
            ],
            'tipos_utilizador' => [1], // Assumindo que existe
        ];

        $response = $this->postJson('/api/v2/membros', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'numero_socio', 'user']
            ]);

        $this->assertDatabaseHas('membros', [
            'club_id' => $this->club->id,
        ]);
    }

    /** @test */
    public function it_can_show_a_specific_membro()
    {
        $membro = Membro::factory()->create(['club_id' => $this->club->id]);

        $response = $this->getJson("/api/v2/membros/{$membro->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $membro->id,
                    'numero_socio' => $membro->numero_socio,
                ]
            ]);
    }

    /** @test */
    public function it_cannot_show_membro_from_different_club()
    {
        $otherClub = $this->createClub();
        $membroFromOtherClub = Membro::factory()->create(['club_id' => $otherClub->id]);

        $response = $this->getJson("/api/v2/membros/{$membroFromOtherClub->id}");

        // ClubScope previne acesso - retorna 404 (não encontrado no scope)
        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_update_a_membro()
    {
        $membro = Membro::factory()->create(['club_id' => $this->club->id]);

        $data = [
            'user' => [
                'name' => 'João Updated',
            ],
            'estado' => 'ativo',
        ];

        $response = $this->putJson("/api/v2/membros/{$membro->id}", $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $membro->user_id,
            'name' => 'João Updated',
        ]);
    }

    /** @test */
    public function it_cannot_update_membro_from_different_club()
    {
        $otherClub = $this->createClub();
        $membroFromOtherClub = Membro::factory()->create(['club_id' => $otherClub->id]);

        $data = ['estado' => 'inativo'];

        $response = $this->putJson("/api/v2/membros/{$membroFromOtherClub->id}", $data);

        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_deactivate_a_membro()
    {
        $membro = Membro::factory()->create([
            'club_id' => $this->club->id,
            'estado' => 'ativo',
        ]);

        $response = $this->deleteJson("/api/v2/membros/{$membro->id}");

        $response->assertStatus(200);

        $this->assertDatabaseHas('membros', [
            'id' => $membro->id,
            'estado' => 'inativo',
        ]);
    }

    /** @test */
    public function it_can_filter_membros_by_estado()
    {
        Membro::factory()->create(['club_id' => $this->club->id, 'estado' => 'ativo']);
        Membro::factory()->create(['club_id' => $this->club->id, 'estado' => 'inativo']);

        $response = $this->getJson('/api/v2/membros?estado=ativo');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function it_can_search_membros_by_name()
    {
        $user1 = User::factory()->create(['name' => 'João Silva']);
        $user2 = User::factory()->create(['name' => 'Maria Santos']);
        
        Membro::factory()->create(['club_id' => $this->club->id, 'user_id' => $user1->id]);
        Membro::factory()->create(['club_id' => $this->club->id, 'user_id' => $user2->id]);

        $response = $this->getJson('/api/v2/membros?search=João');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function it_validates_required_fields_when_creating_membro()
    {
        $response = $this->postJson('/api/v2/membros', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['user.name', 'dados_pessoais.nome_completo', 'tipos_utilizador']);
    }

    /** @test */
    public function it_validates_email_uniqueness_when_creating_membro()
    {
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);

        $data = [
            'user' => [
                'name' => 'João Silva',
                'email' => 'existing@example.com',
            ],
            'dados_pessoais' => [
                'nome_completo' => 'João Silva',
            ],
            'tipos_utilizador' => [1],
        ];

        $response = $this->postJson('/api/v2/membros', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['user.email']);
    }
}
