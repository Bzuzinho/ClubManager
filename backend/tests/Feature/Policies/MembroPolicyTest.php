<?php

namespace Tests\Feature\Policies;

use App\Models\Club;
use App\Models\Membro;
use App\Models\User;
use App\Policies\MembroPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembroPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected MembroPolicy $policy;
    protected Club $club;
    protected User $admin;
    protected User $userWithPermission;
    protected User $userWithoutPermission;
    protected Membro $membro;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->policy = new MembroPolicy();
        $this->club = $this->createClub();
        
        // Admin - bypass todas as permissões
        $this->admin = User::factory()->create(['club_id' => $this->club->id]);
        $this->admin->assignRole('admin');
        
        // User com permissões específicas
        $this->userWithPermission = User::factory()->create(['club_id' => $this->club->id]);
        $this->userWithPermission->givePermissionTo('membros.view');
        $this->userWithPermission->givePermissionTo('membros.create');
        $this->userWithPermission->givePermissionTo('membros.update');
        
        // User sem permissões
        $this->userWithoutPermission = User::factory()->create(['club_id' => $this->club->id]);
        
        // Membro do clube
        $this->membro = Membro::factory()->create(['club_id' => $this->club->id]);
    }

    /** @test */
    public function admin_can_view_any_membros()
    {
        $this->assertTrue($this->policy->viewAny($this->admin));
    }

    /** @test */
    public function user_with_permission_can_view_any_membros()
    {
        $this->assertTrue($this->policy->viewAny($this->userWithPermission));
    }

    /** @test */
    public function user_without_permission_cannot_view_any_membros()
    {
        $this->assertFalse($this->policy->viewAny($this->userWithoutPermission));
    }

    /** @test */
    public function admin_can_view_specific_membro()
    {
        $this->assertTrue($this->policy->view($this->admin, $this->membro));
    }

    /** @test */
    public function user_with_permission_can_view_membro_from_same_club()
    {
        $this->assertTrue($this->policy->view($this->userWithPermission, $this->membro));
    }

    /** @test */
    public function user_cannot_view_membro_from_different_club()
    {
        $otherClub = $this->createClub();
        $membroFromOtherClub = Membro::factory()->create(['club_id' => $otherClub->id]);
        
        $this->assertFalse($this->policy->view($this->userWithPermission, $membroFromOtherClub));
    }

    /** @test */
    public function user_without_permission_cannot_view_membro()
    {
        $this->assertFalse($this->policy->view($this->userWithoutPermission, $this->membro));
    }

    /** @test */
    public function admin_can_create_membros()
    {
        $this->assertTrue($this->policy->create($this->admin));
    }

    /** @test */
    public function user_with_permission_can_create_membros()
    {
        $this->assertTrue($this->policy->create($this->userWithPermission));
    }

    /** @test */
    public function user_without_permission_cannot_create_membros()
    {
        $this->assertFalse($this->policy->create($this->userWithoutPermission));
    }

    /** @test */
    public function admin_can_update_membro()
    {
        $this->assertTrue($this->policy->update($this->admin, $this->membro));
    }

    /** @test */
    public function user_with_permission_can_update_membro_from_same_club()
    {
        $this->assertTrue($this->policy->update($this->userWithPermission, $this->membro));
    }

    /** @test */
    public function user_cannot_update_membro_from_different_club()
    {
        $otherClub = $this->createClub();
        $membroFromOtherClub = Membro::factory()->create(['club_id' => $otherClub->id]);
        
        $this->assertFalse($this->policy->update($this->userWithPermission, $membroFromOtherClub));
    }

    /** @test */
    public function user_without_permission_cannot_update_membro()
    {
        $this->assertFalse($this->policy->update($this->userWithoutPermission, $this->membro));
    }

    /** @test */
    public function admin_can_delete_membro()
    {
        $this->assertTrue($this->policy->delete($this->admin, $this->membro));
    }

    /** @test */
    public function user_with_delete_permission_can_delete_membro_from_same_club()
    {
        $this->userWithPermission->givePermissionTo('membros.delete');
        
        $this->assertTrue($this->policy->delete($this->userWithPermission, $this->membro));
    }

    /** @test */
    public function user_cannot_delete_membro_from_different_club()
    {
        $otherClub = $this->createClub();
        $membroFromOtherClub = Membro::factory()->create(['club_id' => $otherClub->id]);
        
        $this->userWithPermission->givePermissionTo('membros.delete');
        
        $this->assertFalse($this->policy->delete($this->userWithPermission, $membroFromOtherClub));
    }

    /** @test */
    public function user_without_permission_cannot_delete_membro()
    {
        $this->assertFalse($this->policy->delete($this->userWithoutPermission, $this->membro));
    }

    /** @test */
    public function user_with_manage_documents_permission_can_manage_documents()
    {
        $this->userWithPermission->givePermissionTo('membros.manage_documents');
        
        $this->assertTrue($this->policy->manageDocuments($this->userWithPermission, $this->membro));
    }

    /** @test */
    public function user_cannot_manage_documents_from_different_club()
    {
        $otherClub = $this->createClub();
        $membroFromOtherClub = Membro::factory()->create(['club_id' => $otherClub->id]);
        
        $this->userWithPermission->givePermissionTo('membros.manage_documents');
        
        $this->assertFalse($this->policy->manageDocuments($this->userWithPermission, $membroFromOtherClub));
    }

    /** @test */
    public function user_with_financeiro_permission_can_view_financial_data()
    {
        $this->userWithPermission->givePermissionTo('financeiro.view');
        
        $this->assertTrue($this->policy->viewFinancial($this->userWithPermission, $this->membro));
    }

    /** @test */
    public function user_cannot_view_financial_data_from_different_club()
    {
        $otherClub = $this->createClub();
        $membroFromOtherClub = Membro::factory()->create(['club_id' => $otherClub->id]);
        
        $this->userWithPermission->givePermissionTo('financeiro.view');
        
        $this->assertFalse($this->policy->viewFinancial($this->userWithPermission, $membroFromOtherClub));
    }
}
