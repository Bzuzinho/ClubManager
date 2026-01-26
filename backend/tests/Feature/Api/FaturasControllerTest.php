<?php

namespace Tests\Feature\Api;

use App\Models\Club;
use App\Models\Fatura;
use App\Models\Membro;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaturasControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Club $club;
    protected Membro $membro;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->club = $this->createClub();
        $this->user = $this->createAuthenticatedUser('admin', $this->club);
        $this->membro = Membro::factory()->create(['club_id' => $this->club->id]);
    }

    /** @test */
    public function it_can_list_faturas_from_authenticated_users_club()
    {
        // Criar faturas do clube do utilizador autenticado
        Fatura::factory()->count(3)->create([
            'club_id' => $this->club->id,
            'membro_id' => $this->membro->id,
        ]);
        
        // Criar faturas de outro clube (não devem aparecer)
        $otherClub = $this->createClub();
        $otherMembro = Membro::factory()->create(['club_id' => $otherClub->id]);
        Fatura::factory()->count(2)->create([
            'club_id' => $otherClub->id,
            'membro_id' => $otherMembro->id,
        ]);

        $response = $this->getJson('/api/v2/faturas');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'numero_fatura', 'valor_total', 'status_cache']
                ]
            ]);
    }

    /** @test */
    public function it_cannot_list_faturas_without_permission()
    {
        $userWithoutPermission = User::factory()->create([
            'club_id' => $this->club->id,
        ]);
        
        $this->actingAs($userWithoutPermission, 'sanctum');

        $response = $this->getJson('/api/v2/faturas');

        $response->assertStatus(403);
    }

    /** @test */
    public function it_can_create_fatura_avulsa()
    {
        $data = [
            'membro_id' => $this->membro->id,
            'itens' => [
                [
                    'descricao' => 'Quota extra',
                    'valor_unitario' => 50.00,
                    'quantidade' => 1,
                ],
            ],
        ];

        $response = $this->postJson('/api/v2/faturas', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'numero_fatura', 'valor_total']
            ]);

        $this->assertDatabaseHas('faturas', [
            'club_id' => $this->club->id,
            'membro_id' => $this->membro->id,
        ]);
    }

    /** @test */
    public function it_can_show_a_specific_fatura()
    {
        $fatura = Fatura::factory()->create([
            'club_id' => $this->club->id,
            'membro_id' => $this->membro->id,
        ]);

        $response = $this->getJson("/api/v2/faturas/{$fatura->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $fatura->id,
                    'numero_fatura' => $fatura->numero_fatura,
                ]
            ]);
    }

    /** @test */
    public function it_cannot_show_fatura_from_different_club()
    {
        $otherClub = $this->createClub();
        $otherMembro = Membro::factory()->create(['club_id' => $otherClub->id]);
        $faturaFromOtherClub = Fatura::factory()->create([
            'club_id' => $otherClub->id,
            'membro_id' => $otherMembro->id,
        ]);

        $response = $this->getJson("/api/v2/faturas/{$faturaFromOtherClub->id}");

        // ClubScope previne acesso - retorna 404
        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_filter_faturas_by_membro()
    {
        $membro2 = Membro::factory()->create(['club_id' => $this->club->id]);
        
        Fatura::factory()->create([
            'club_id' => $this->club->id,
            'membro_id' => $this->membro->id,
        ]);
        
        Fatura::factory()->create([
            'club_id' => $this->club->id,
            'membro_id' => $membro2->id,
        ]);

        $response = $this->getJson("/api/v2/faturas?membro_id={$this->membro->id}");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function it_can_filter_faturas_by_mes()
    {
        Fatura::factory()->create([
            'club_id' => $this->club->id,
            'membro_id' => $this->membro->id,
            'mes' => '2026-01',
        ]);
        
        Fatura::factory()->create([
            'club_id' => $this->club->id,
            'membro_id' => $this->membro->id,
            'mes' => '2026-02',
        ]);

        $response = $this->getJson('/api/v2/faturas?mes=2026-01');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function it_can_filter_faturas_by_estado()
    {
        Fatura::factory()->create([
            'club_id' => $this->club->id,
            'membro_id' => $this->membro->id,
            'status_cache' => 'pendente',
        ]);
        
        Fatura::factory()->create([
            'club_id' => $this->club->id,
            'membro_id' => $this->membro->id,
            'status_cache' => 'paga',
        ]);

        $response = $this->getJson('/api/v2/faturas?estado=paga');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function it_can_generate_mensalidades()
    {
        $data = [
            'membro_id' => $this->membro->id,
            'mes_inicio' => '2026-01',
            'mes_fim' => '2026-03',
        ];

        $response = $this->postJson('/api/v2/faturas/gerar-mensalidades', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'numero_fatura', 'mes']
                ]
            ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_fatura()
    {
        $response = $this->postJson('/api/v2/faturas', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['membro_id', 'itens']);
    }

    /** @test */
    public function it_validates_itens_structure_when_creating_fatura()
    {
        $data = [
            'membro_id' => $this->membro->id,
            'itens' => [
                ['descricao' => 'Sem valor'], // Falta valor_unitario e quantidade
            ],
        ];

        $response = $this->postJson('/api/v2/faturas', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['itens.0.valor_unitario', 'itens.0.quantidade']);
    }

    /** @test */
    public function it_validates_required_fields_when_generating_mensalidades()
    {
        $response = $this->postJson('/api/v2/faturas/gerar-mensalidades', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['membro_id', 'mes_inicio']);
    }

    /** @test */
    public function it_validates_date_format_when_generating_mensalidades()
    {
        $data = [
            'membro_id' => $this->membro->id,
            'mes_inicio' => 'invalid-date',
        ];

        $response = $this->postJson('/api/v2/faturas/gerar-mensalidades', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['mes_inicio']);
    }

    /** @test */
    public function it_can_add_item_to_fatura()
    {
        $fatura = Fatura::factory()->create([
            'club_id' => $this->club->id,
            'membro_id' => $this->membro->id,
            'status_cache' => 'pendente',
        ]);

        $data = [
            'descricao' => 'Item adicional',
            'valor_unitario' => 25.00,
            'quantidade' => 2,
        ];

        $response = $this->postJson("/api/v2/faturas/{$fatura->id}/itens", $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('fatura_itens', [
            'fatura_id' => $fatura->id,
            'descricao' => 'Item adicional',
        ]);
    }

    /** @test */
    public function it_cannot_add_item_to_fatura_from_different_club()
    {
        $otherClub = $this->createClub();
        $otherMembro = Membro::factory()->create(['club_id' => $otherClub->id]);
        $faturaFromOtherClub = Fatura::factory()->create([
            'club_id' => $otherClub->id,
            'membro_id' => $otherMembro->id,
        ]);

        $data = [
            'descricao' => 'Item',
            'valor_unitario' => 10.00,
            'quantidade' => 1,
        ];

        $response = $this->postJson("/api/v2/faturas/{$faturaFromOtherClub->id}/itens", $data);

        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_register_pagamento()
    {
        $fatura = Fatura::factory()->create([
            'club_id' => $this->club->id,
            'membro_id' => $this->membro->id,
            'valor_total' => 100.00,
        ]);

        $data = [
            'valor' => 100.00,
            'metodo' => 'transferencia',
            'data_pagamento' => '2026-01-22',
        ];

        $response = $this->postJson("/api/v2/faturas/{$fatura->id}/pagamentos", $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('pagamentos', [
            'fatura_id' => $fatura->id,
            'valor' => 100.00,
        ]);
    }
}
