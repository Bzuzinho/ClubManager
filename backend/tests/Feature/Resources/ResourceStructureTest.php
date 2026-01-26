<?php

namespace Tests\Feature\Resources;

use App\Http\Resources\MembroResource;
use App\Http\Resources\FaturaResource;
use App\Models\Atleta;
use App\Models\Club;
use App\Models\DadosFinanceiros;
use App\Models\DadosPessoais;
use App\Models\Fatura;
use App\Models\FaturaItem;
use App\Models\Membro;
use App\Models\Pagamento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResourceStructureTest extends TestCase
{
    use RefreshDatabase;

    protected Club $club;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->club = $this->createClub();
        $this->user = User::factory()->create(['club_id' => $this->club->id]);
    }

    /** @test */
    public function membro_resource_has_correct_structure()
    {
        $membro = Membro::factory()->create(['club_id' => $this->club->id]);
        
        $resource = new MembroResource($membro);
        $data = $resource->toArray(request());
        
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('numero_socio', $data);
        $this->assertArrayHasKey('estado', $data);
        $this->assertArrayHasKey('data_inicio', $data);
        $this->assertArrayHasKey('data_fim', $data);
        $this->assertArrayHasKey('observacoes', $data);
        
        $this->assertEquals($membro->id, $data['id']);
        $this->assertEquals($membro->numero_socio, $data['numero_socio']);
    }

    /** @test */
    public function membro_resource_includes_user_when_loaded()
    {
        $user = User::factory()->create(['club_id' => $this->club->id]);
        $membro = Membro::factory()->create([
            'club_id' => $this->club->id,
            'user_id' => $user->id,
        ]);
        
        $membroWithUser = Membro::with('user')->find($membro->id);
        
        $resource = new MembroResource($membroWithUser);
        $data = $resource->toArray(request());
        
        $this->assertArrayHasKey('user', $data);
        $this->assertIsArray($data['user']);
        $this->assertEquals($user->id, $data['user']['id']);
        $this->assertEquals($user->name, $data['user']['name']);
    }

    /** @test */
    public function membro_resource_includes_dados_pessoais_when_loaded()
    {
        $user = User::factory()->create(['club_id' => $this->club->id]);
        $dadosPessoais = DadosPessoais::factory()->create(['user_id' => $user->id]);
        $membro = Membro::factory()->create([
            'club_id' => $this->club->id,
            'user_id' => $user->id,
        ]);
        
        $membroWithDados = Membro::with('user.dadosPessoais')->find($membro->id);
        
        $resource = new MembroResource($membroWithDados);
        $data = $resource->toArray(request());
        
        $this->assertArrayHasKey('user', $data);
        $this->assertArrayHasKey('dados_pessoais', $data['user']);
    }

    /** @test */
    public function membro_resource_includes_atleta_when_loaded()
    {
        $membro = Membro::factory()->create(['club_id' => $this->club->id]);
        $atleta = Atleta::factory()->create([
            'club_id' => $this->club->id,
            'membro_id' => $membro->id,
        ]);
        
        $membroWithAtleta = Membro::with('atleta')->find($membro->id);
        
        $resource = new MembroResource($membroWithAtleta);
        $data = $resource->toArray(request());
        
        $this->assertArrayHasKey('atleta', $data);
        $this->assertIsArray($data['atleta']);
        $this->assertEquals($atleta->id, $data['atleta']['id']);
    }

    /** @test */
    public function membro_resource_includes_dados_financeiros_when_loaded()
    {
        $membro = Membro::factory()->create(['club_id' => $this->club->id]);
        $dadosFinanceiros = DadosFinanceiros::factory()->create([
            'club_id' => $this->club->id,
            'membro_id' => $membro->id,
        ]);
        
        $membroWithDados = Membro::with('dadosFinanceiros')->find($membro->id);
        
        $resource = new MembroResource($membroWithDados);
        $data = $resource->toArray(request());
        
        $this->assertArrayHasKey('dados_financeiros', $data);
        $this->assertIsArray($data['dados_financeiros']);
    }

    /** @test */
    public function fatura_resource_has_correct_structure()
    {
        $membro = Membro::factory()->create(['club_id' => $this->club->id]);
        $fatura = Fatura::factory()->create([
            'club_id' => $this->club->id,
            'membro_id' => $membro->id,
        ]);
        
        $resource = new FaturaResource($fatura);
        $data = $resource->toArray(request());
        
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('numero_fatura', $data);
        $this->assertArrayHasKey('data_emissao', $data);
        $this->assertArrayHasKey('data_vencimento', $data);
        $this->assertArrayHasKey('mes', $data);
        $this->assertArrayHasKey('valor_total', $data);
        $this->assertArrayHasKey('valor_pago', $data);
        $this->assertArrayHasKey('saldo', $data);
        $this->assertArrayHasKey('status_cache', $data);
        
        $this->assertEquals($fatura->id, $data['id']);
        $this->assertEquals($fatura->numero_fatura, $data['numero_fatura']);
    }

    /** @test */
    public function fatura_resource_includes_membro_when_loaded()
    {
        $membro = Membro::factory()->create(['club_id' => $this->club->id]);
        $fatura = Fatura::factory()->create([
            'club_id' => $this->club->id,
            'membro_id' => $membro->id,
        ]);
        
        $faturaWithMembro = Fatura::with('membro')->find($fatura->id);
        
        $resource = new FaturaResource($faturaWithMembro);
        $data = $resource->toArray(request());
        
        $this->assertArrayHasKey('membro', $data);
        $this->assertIsArray($data['membro']);
        $this->assertEquals($membro->id, $data['membro']['id']);
    }

    /** @test */
    public function fatura_resource_includes_itens_when_loaded()
    {
        $membro = Membro::factory()->create(['club_id' => $this->club->id]);
        $fatura = Fatura::factory()->create([
            'club_id' => $this->club->id,
            'membro_id' => $membro->id,
        ]);
        
        FaturaItem::factory()->count(3)->create([
            'club_id' => $this->club->id,
            'fatura_id' => $fatura->id,
        ]);
        
        $faturaWithItens = Fatura::with('itens')->find($fatura->id);
        
        $resource = new FaturaResource($faturaWithItens);
        $data = $resource->toArray(request());
        
        $this->assertArrayHasKey('itens', $data);
        $this->assertIsArray($data['itens']);
        $this->assertCount(3, $data['itens']);
    }

    /** @test */
    public function fatura_resource_includes_pagamentos_when_loaded()
    {
        $membro = Membro::factory()->create(['club_id' => $this->club->id]);
        $fatura = Fatura::factory()->create([
            'club_id' => $this->club->id,
            'membro_id' => $membro->id,
        ]);
        
        Pagamento::factory()->count(2)->create([
            'club_id' => $this->club->id,
            'fatura_id' => $fatura->id,
        ]);
        
        $faturaWithPagamentos = Fatura::with('pagamentos')->find($fatura->id);
        
        $resource = new FaturaResource($faturaWithPagamentos);
        $data = $resource->toArray(request());
        
        $this->assertArrayHasKey('pagamentos', $data);
        $this->assertIsArray($data['pagamentos']);
        $this->assertCount(2, $data['pagamentos']);
    }

    /** @test */
    public function resource_collection_has_correct_structure()
    {
        Membro::factory()->count(3)->create(['club_id' => $this->club->id]);
        
        $membros = Membro::all();
        $collection = MembroResource::collection($membros);
        
        $data = $collection->toArray(request());
        
        $this->assertCount(3, $data);
        
        foreach ($data as $item) {
            $this->assertArrayHasKey('id', $item);
            $this->assertArrayHasKey('numero_socio', $item);
            $this->assertArrayHasKey('estado', $item);
        }
    }

    /** @test */
    public function resources_do_not_include_sensitive_fields()
    {
        $user = User::factory()->create([
            'club_id' => $this->club->id,
            'password' => bcrypt('secret'),
            'remember_token' => 'test-token',
        ]);
        
        $membro = Membro::factory()->create([
            'club_id' => $this->club->id,
            'user_id' => $user->id,
        ]);
        
        $membroWithUser = Membro::with('user')->find($membro->id);
        
        $resource = new MembroResource($membroWithUser);
        $data = $resource->toArray(request());
        
        $this->assertArrayNotHasKey('password', $data['user']);
        $this->assertArrayNotHasKey('remember_token', $data['user']);
    }

    /** @test */
    public function resources_format_dates_correctly()
    {
        $fatura = Fatura::factory()->create([
            'club_id' => $this->club->id,
            'membro_id' => Membro::factory()->create(['club_id' => $this->club->id])->id,
            'data_emissao' => '2026-01-22',
        ]);
        
        $resource = new FaturaResource($fatura);
        $data = $resource->toArray(request());
        
        $this->assertArrayHasKey('data_emissao', $data);
        $this->assertIsString($data['data_emissao']);
    }

    /** @test */
    public function resources_handle_null_relationships_gracefully()
    {
        $membro = Membro::factory()->create(['club_id' => $this->club->id]);
        // Não criar atleta relacionado
        
        $resource = new MembroResource($membro);
        $data = $resource->toArray(request());
        
        // Quando não carregado com with(), não deve incluir a chave
        $this->assertArrayNotHasKey('atleta', $data);
    }
}
