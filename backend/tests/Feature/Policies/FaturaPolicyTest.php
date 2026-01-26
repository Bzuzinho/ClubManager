<?php

namespace Tests\Feature\Policies;

use App\Models\Club;
use App\Models\Fatura;
use App\Models\Membro;
use App\Models\User;
use App\Policies\FaturaPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaturaPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected FaturaPolicy $policy;
    protected Club $club;
    protected User $admin;
    protected User $userWithPermission;
    protected User $userWithoutPermission;
    protected Fatura $fatura;
    protected Membro $membro;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->policy = new FaturaPolicy();
        $this->club = $this->createClub();
        
        // Admin
        $this->admin = User::factory()->create(['club_id' => $this->club->id]);
        $this->admin->assignRole('admin');
        
        // User com permissões financeiras
        $this->userWithPermission = User::factory()->create(['club_id' => $this->club->id]);
        $this->userWithPermission->givePermissionTo('financeiro.view');
        $this->userWithPermission->givePermissionTo('financeiro.create');
        $this->userWithPermission->givePermissionTo('financeiro.update');
        
        // User sem permissões
        $this->userWithoutPermission = User::factory()->create(['club_id' => $this->club->id]);
        
        // Membro e fatura
        $this->membro = Membro::factory()->create(['club_id' => $this->club->id]);
        $this->fatura = Fatura::factory()->create([
            'club_id' => $this->club->id,
            'membro_id' => $this->membro->id,
            'status_cache' => 'pendente',
        ]);
    }

    /** @test */
    public function admin_can_view_any_faturas()
    {
        $this->assertTrue($this->policy->viewAny($this->admin));
    }

    /** @test */
    public function user_with_permission_can_view_any_faturas()
    {
        $this->assertTrue($this->policy->viewAny($this->userWithPermission));
    }

    /** @test */
    public function user_without_permission_cannot_view_any_faturas()
    {
        $this->assertFalse($this->policy->viewAny($this->userWithoutPermission));
    }

    /** @test */
    public function admin_can_view_specific_fatura()
    {
        $this->assertTrue($this->policy->view($this->admin, $this->fatura));
    }

    /** @test */
    public function user_with_permission_can_view_fatura_from_same_club()
    {
        $this->assertTrue($this->policy->view($this->userWithPermission, $this->fatura));
    }

    /** @test */
    public function user_cannot_view_fatura_from_different_club()
    {
        $otherClub = $this->createClub();
        $otherMembro = Membro::factory()->create(['club_id' => $otherClub->id]);
        $faturaFromOtherClub = Fatura::factory()->create([
            'club_id' => $otherClub->id,
            'membro_id' => $otherMembro->id,
        ]);
        
        $this->assertFalse($this->policy->view($this->userWithPermission, $faturaFromOtherClub));
    }

    /** @test */
    public function admin_can_create_faturas()
    {
        $this->assertTrue($this->policy->create($this->admin));
    }

    /** @test */
    public function user_with_permission_can_create_faturas()
    {
        $this->assertTrue($this->policy->create($this->userWithPermission));
    }

    /** @test */
    public function user_without_permission_cannot_create_faturas()
    {
        $this->assertFalse($this->policy->create($this->userWithoutPermission));
    }

    /** @test */
    public function admin_can_update_fatura_pendente()
    {
        $this->assertTrue($this->policy->update($this->admin, $this->fatura));
    }

    /** @test */
    public function user_with_permission_can_update_fatura_pendente_from_same_club()
    {
        $this->assertTrue($this->policy->update($this->userWithPermission, $this->fatura));
    }

    /** @test */
    public function user_cannot_update_fatura_paga()
    {
        $faturaPaga = Fatura::factory()->create([
            'club_id' => $this->club->id,
            'membro_id' => $this->membro->id,
            'status_cache' => 'paga',
        ]);
        
        $this->assertFalse($this->policy->update($this->userWithPermission, $faturaPaga));
    }

    /** @test */
    public function user_cannot_update_fatura_from_different_club()
    {
        $otherClub = $this->createClub();
        $otherMembro = Membro::factory()->create(['club_id' => $otherClub->id]);
        $faturaFromOtherClub = Fatura::factory()->create([
            'club_id' => $otherClub->id,
            'membro_id' => $otherMembro->id,
        ]);
        
        $this->assertFalse($this->policy->update($this->userWithPermission, $faturaFromOtherClub));
    }

    /** @test */
    public function admin_can_delete_fatura_pendente()
    {
        $this->assertTrue($this->policy->delete($this->admin, $this->fatura));
    }

    /** @test */
    public function user_with_permission_can_delete_fatura_pendente()
    {
        $this->userWithPermission->givePermissionTo('financeiro.delete');
        
        $this->assertTrue($this->policy->delete($this->userWithPermission, $this->fatura));
    }

    /** @test */
    public function user_cannot_delete_fatura_paga()
    {
        $faturaPaga = Fatura::factory()->create([
            'club_id' => $this->club->id,
            'membro_id' => $this->membro->id,
            'status_cache' => 'paga',
        ]);
        
        $this->userWithPermission->givePermissionTo('financeiro.delete');
        
        $this->assertFalse($this->policy->delete($this->userWithPermission, $faturaPaga));
    }

    /** @test */
    public function user_cannot_delete_fatura_from_different_club()
    {
        $otherClub = $this->createClub();
        $otherMembro = Membro::factory()->create(['club_id' => $otherClub->id]);
        $faturaFromOtherClub = Fatura::factory()->create([
            'club_id' => $otherClub->id,
            'membro_id' => $otherMembro->id,
        ]);
        
        $this->userWithPermission->givePermissionTo('financeiro.delete');
        
        $this->assertFalse($this->policy->delete($this->userWithPermission, $faturaFromOtherClub));
    }

    /** @test */
    public function admin_can_generate_mensalidades()
    {
        $this->assertTrue($this->policy->generateMensalidades($this->admin));
    }

    /** @test */
    public function user_with_permission_can_generate_mensalidades()
    {
        $this->userWithPermission->givePermissionTo('financeiro.generate');
        
        $this->assertTrue($this->policy->generateMensalidades($this->userWithPermission));
    }

    /** @test */
    public function user_without_permission_cannot_generate_mensalidades()
    {
        $this->assertFalse($this->policy->generateMensalidades($this->userWithoutPermission));
    }

    /** @test */
    public function admin_can_cancel_fatura()
    {
        $this->assertTrue($this->policy->cancel($this->admin, $this->fatura));
    }

    /** @test */
    public function user_with_permission_can_cancel_fatura_from_same_club()
    {
        $this->userWithPermission->givePermissionTo('financeiro.cancel');
        
        $this->assertTrue($this->policy->cancel($this->userWithPermission, $this->fatura));
    }

    /** @test */
    public function user_cannot_cancel_fatura_from_different_club()
    {
        $otherClub = $this->createClub();
        $otherMembro = Membro::factory()->create(['club_id' => $otherClub->id]);
        $faturaFromOtherClub = Fatura::factory()->create([
            'club_id' => $otherClub->id,
            'membro_id' => $otherMembro->id,
        ]);
        
        $this->userWithPermission->givePermissionTo('financeiro.cancel');
        
        $this->assertFalse($this->policy->cancel($this->userWithPermission, $faturaFromOtherClub));
    }

    /** @test */
    public function user_without_permission_cannot_cancel_fatura()
    {
        $this->assertFalse($this->policy->cancel($this->userWithoutPermission, $this->fatura));
    }
}
