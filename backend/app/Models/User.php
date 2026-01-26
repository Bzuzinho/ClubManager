<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * Guard padrão para Spatie Permission
     */
    protected string $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'telefone',
        'ativo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'ativo' => 'boolean',
        ];
    }

    /**
     * Relação com dados pessoais
     */
    public function dadosPessoais()
    {
        return $this->hasOne(DadosPessoais::class);
    }

    /**
     * Relação com clubes (através de club_users)
     */
    public function clubUsers()
    {
        return $this->hasMany(ClubUser::class);
    }

    /**
     * Relação muitos-para-muitos com clubes
     */
    public function clubs()
    {
        return $this->belongsToMany(Club::class, 'club_users')
            ->withPivot(['role_no_clube', 'data_inicio', 'data_fim', 'ativo'])
            ->whereRaw('club_users.ativo = TRUE');
    }

    /**
     * Relação com membros (diferentes perfis em diferentes clubes)
     */
    public function membros()
    {
        return $this->hasMany(Membro::class);
    }

    /**
     * Relações do utilizador (encarregado/educando)
     */
    public function relacoes()
    {
        return $this->hasMany(RelacaoUser::class, 'user_origem_id');
    }

    /**
     * Relações inversas (quando user é destino)
     */
    public function relacoesInversas()
    {
        return $this->hasMany(RelacaoUser::class, 'user_destino_id');
    }

    /**
     * Obter membro no clube ativo
     */
    public function getMembroAtivo(int $clubId)
    {
        return $this->membros()->where('club_id', $clubId)->first();
    }

    /**
     * Relação com encarregados de educação (user é menor)
     */
    public function encarregadosEducacao()
    {
        return $this->belongsToMany(
            User::class,
            'relacoes_users',
            'user_destino_id',
            'user_origem_id'
        )
        ->wherePivot('tipo_relacao', 'encarregado_educacao')
        ->whereRaw('relacoes_users.ativo = TRUE')
        ->withPivot(['club_id', 'data_inicio', 'data_fim', 'ativo']);
    }

    /**
     * Relação com educandos (user é encarregado de educação)
     */
    public function educandos()
    {
        return $this->belongsToMany(
            User::class,
            'relacoes_users',
            'user_origem_id',
            'user_destino_id'
        )
        ->wherePivot('tipo_relacao', 'encarregado_educacao')
        ->whereRaw('relacoes_users.ativo = TRUE')
        ->withPivot(['club_id', 'data_inicio', 'data_fim', 'ativo']);
    }
}
