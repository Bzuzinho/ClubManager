<?php

namespace Tests\Feature\Scopes;

use App\Models\Atleta;
use App\Models\Club;
use App\Models\Evento;
use App\Models\Fatura;
use App\Models\Grupo;
use App\Models\Membro;
use App\Models\Pagamento;
use App\Models\Presenca;
use App\Models\Treino;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClubScopeTest extends TestCase
{
    use RefreshDatabase;

    protected Club $club1;
    protected Club $club2;
    protected User $userClub1;
    protected User $userClub2;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar dois clubes diferentes
        $this->club1 = $this->createClub();
        $this->club2 = $this->createClub();
        
        // Criar utilizadores de cada clube
        $this->userClub1 = User::factory()->create(['club_id' => $this->club1->id]);
        $this->userClub2 = User::factory()->create(['club_id' => $this->club2->id]);
    }

    /** @test */
    public function membro_scope_filters_by_club_id()
    {
        // Criar membros em ambos os clubes
        $membroClub1 = Membro::factory()->create(['club_id' => $this->club1->id]);
        $membroClub2 = Membro::factory()->create(['club_id' => $this->club2->id]);
        
        // Autenticar como user do club1
        $this->actingAs($this->userClub1, 'sanctum');
        
        // Deve retornar apenas membros do club1
        $membros = Membro::all();
        
        $this->assertCount(1, $membros);
        $this->assertEquals($membroClub1->id, $membros->first()->id);
        $this->assertNotContains($membroClub2->id, $membros->pluck('id'));
    }

    /** @test */
    public function fatura_scope_filters_by_club_id()
    {
        $membroClub1 = Membro::factory()->create(['club_id' => $this->club1->id]);
        $membroClub2 = Membro::factory()->create(['club_id' => $this->club2->id]);
        
        $faturaClub1 = Fatura::factory()->create([
            'club_id' => $this->club1->id,
            'membro_id' => $membroClub1->id,
        ]);
        
        $faturaClub2 = Fatura::factory()->create([
            'club_id' => $this->club2->id,
            'membro_id' => $membroClub2->id,
        ]);
        
        $this->actingAs($this->userClub1, 'sanctum');
        
        $faturas = Fatura::all();
        
        $this->assertCount(1, $faturas);
        $this->assertEquals($faturaClub1->id, $faturas->first()->id);
    }

    /** @test */
    public function atleta_scope_filters_by_club_id()
    {
        $membroClub1 = Membro::factory()->create(['club_id' => $this->club1->id]);
        $membroClub2 = Membro::factory()->create(['club_id' => $this->club2->id]);
        
        $atletaClub1 = Atleta::factory()->create([
            'club_id' => $this->club1->id,
            'membro_id' => $membroClub1->id,
        ]);
        
        $atletaClub2 = Atleta::factory()->create([
            'club_id' => $this->club2->id,
            'membro_id' => $membroClub2->id,
        ]);
        
        $this->actingAs($this->userClub1, 'sanctum');
        
        $atletas = Atleta::all();
        
        $this->assertCount(1, $atletas);
        $this->assertEquals($atletaClub1->id, $atletas->first()->id);
    }

    /** @test */
    public function grupo_scope_filters_by_club_id()
    {
        $grupoClub1 = Grupo::factory()->create(['club_id' => $this->club1->id]);
        $grupoClub2 = Grupo::factory()->create(['club_id' => $this->club2->id]);
        
        $this->actingAs($this->userClub1, 'sanctum');
        
        $grupos = Grupo::all();
        
        $this->assertCount(1, $grupos);
        $this->assertEquals($grupoClub1->id, $grupos->first()->id);
    }

    /** @test */
    public function evento_scope_filters_by_club_id()
    {
        $eventoClub1 = Evento::factory()->create(['club_id' => $this->club1->id]);
        $eventoClub2 = Evento::factory()->create(['club_id' => $this->club2->id]);
        
        $this->actingAs($this->userClub1, 'sanctum');
        
        $eventos = Evento::all();
        
        $this->assertCount(1, $eventos);
        $this->assertEquals($eventoClub1->id, $eventos->first()->id);
    }

    /** @test */
    public function treino_scope_filters_by_club_id()
    {
        $treinoClub1 = Treino::factory()->create(['club_id' => $this->club1->id]);
        $treinoClub2 = Treino::factory()->create(['club_id' => $this->club2->id]);
        
        $this->actingAs($this->userClub1, 'sanctum');
        
        $treinos = Treino::all();
        
        $this->assertCount(1, $treinos);
        $this->assertEquals($treinoClub1->id, $treinos->first()->id);
    }

    /** @test */
    public function presenca_scope_filters_by_club_id()
    {
        $membroClub1 = Membro::factory()->create(['club_id' => $this->club1->id]);
        $treinoClub1 = Treino::factory()->create(['club_id' => $this->club1->id]);
        
        $membroClub2 = Membro::factory()->create(['club_id' => $this->club2->id]);
        $treinoClub2 = Treino::factory()->create(['club_id' => $this->club2->id]);
        
        $presencaClub1 = Presenca::factory()->create([
            'club_id' => $this->club1->id,
            'treino_id' => $treinoClub1->id,
            'membro_id' => $membroClub1->id,
        ]);
        
        $presencaClub2 = Presenca::factory()->create([
            'club_id' => $this->club2->id,
            'treino_id' => $treinoClub2->id,
            'membro_id' => $membroClub2->id,
        ]);
        
        $this->actingAs($this->userClub1, 'sanctum');
        
        $presencas = Presenca::all();
        
        $this->assertCount(1, $presencas);
        $this->assertEquals($presencaClub1->id, $presencas->first()->id);
    }

    /** @test */
    public function pagamento_scope_filters_by_club_id()
    {
        $membroClub1 = Membro::factory()->create(['club_id' => $this->club1->id]);
        $faturaClub1 = Fatura::factory()->create([
            'club_id' => $this->club1->id,
            'membro_id' => $membroClub1->id,
        ]);
        
        $membroClub2 = Membro::factory()->create(['club_id' => $this->club2->id]);
        $faturaClub2 = Fatura::factory()->create([
            'club_id' => $this->club2->id,
            'membro_id' => $membroClub2->id,
        ]);
        
        $pagamentoClub1 = Pagamento::factory()->create([
            'club_id' => $this->club1->id,
            'fatura_id' => $faturaClub1->id,
        ]);
        
        $pagamentoClub2 = Pagamento::factory()->create([
            'club_id' => $this->club2->id,
            'fatura_id' => $faturaClub2->id,
        ]);
        
        $this->actingAs($this->userClub1, 'sanctum');
        
        $pagamentos = Pagamento::all();
        
        $this->assertCount(1, $pagamentos);
        $this->assertEquals($pagamentoClub1->id, $pagamentos->first()->id);
    }

    /** @test */
    public function scope_works_with_where_clauses()
    {
        $membroAtivo = Membro::factory()->create([
            'club_id' => $this->club1->id,
            'estado' => 'ativo',
        ]);
        
        $membroInativo = Membro::factory()->create([
            'club_id' => $this->club1->id,
            'estado' => 'inativo',
        ]);
        
        $membroClub2 = Membro::factory()->create([
            'club_id' => $this->club2->id,
            'estado' => 'ativo',
        ]);
        
        $this->actingAs($this->userClub1, 'sanctum');
        
        $membrosAtivos = Membro::where('estado', 'ativo')->get();
        
        $this->assertCount(1, $membrosAtivos);
        $this->assertEquals($membroAtivo->id, $membrosAtivos->first()->id);
    }

    /** @test */
    public function scope_works_with_relationships()
    {
        $membroClub1 = Membro::factory()->create(['club_id' => $this->club1->id]);
        $membroClub2 = Membro::factory()->create(['club_id' => $this->club2->id]);
        
        Fatura::factory()->count(2)->create([
            'club_id' => $this->club1->id,
            'membro_id' => $membroClub1->id,
        ]);
        
        Fatura::factory()->create([
            'club_id' => $this->club2->id,
            'membro_id' => $membroClub2->id,
        ]);
        
        $this->actingAs($this->userClub1, 'sanctum');
        
        $membro = Membro::with('faturas')->first();
        
        $this->assertCount(2, $membro->faturas);
    }

    /** @test */
    public function find_or_fail_respects_club_scope()
    {
        $membroClub2 = Membro::factory()->create(['club_id' => $this->club2->id]);
        
        $this->actingAs($this->userClub1, 'sanctum');
        
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        
        Membro::findOrFail($membroClub2->id);
    }

    /** @test */
    public function scope_can_be_bypassed_with_withoutGlobalScope()
    {
        Membro::factory()->create(['club_id' => $this->club1->id]);
        Membro::factory()->create(['club_id' => $this->club2->id]);
        
        $this->actingAs($this->userClub1, 'sanctum');
        
        // Com scope (deve retornar 1)
        $this->assertCount(1, Membro::all());
        
        // Sem scope (deve retornar 2)
        $this->assertCount(2, Membro::withoutGlobalScope('club')->get());
    }

    /** @test */
    public function new_models_automatically_get_club_id()
    {
        $this->actingAs($this->userClub1, 'sanctum');
        
        $membro = Membro::factory()->create([
            // Não especificamos club_id
        ]);
        
        // HasClubScope deve ter adicionado automaticamente
        $this->assertEquals($this->club1->id, $membro->club_id);
    }
}
