<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DadosConfiguracao;
use App\Models\User;
use App\Services\Tenancy\ClubContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

/**
 * Controller para gestão de configuração de utilizadores
 */
class ConfiguracaoController extends Controller
{
    protected ClubContext $clubContext;

    public function __construct(ClubContext $clubContext)
    {
        $this->clubContext = $clubContext;
    }

    /**
     * Obter configuração de um utilizador
     */
    public function show(int $userId): JsonResponse
    {
        $clubId = $this->clubContext->getClubId();
        
        $configuracao = DadosConfiguracao::where('club_id', $clubId)
            ->where('user_id', $userId)
            ->with('user')
            ->firstOrFail();

        return response()->json([
            'data' => $configuracao,
        ]);
    }

    /**
     * Atualizar configuração de um utilizador
     */
    public function update(Request $request, int $userId): JsonResponse
    {
        $clubId = $this->clubContext->getClubId();

        $request->validate([
            'rgpd' => 'sometimes|boolean',
            'rgpd_assinado' => 'sometimes|boolean',
            'data_rgpd' => 'nullable|date',
            'arquivo_rgpd' => 'nullable|string',
            'consentimento' => 'sometimes|boolean',
            'data_consentimento' => 'nullable|date',
            'arquivo_consentimento' => 'nullable|string',
            'afiliacao' => 'sometimes|boolean',
            'data_afiliacao' => 'nullable|date',
            'arquivo_afiliacao' => 'nullable|string',
            'declaracao_transporte' => 'sometimes|boolean',
            'declaracao_transporte_arquivo' => 'nullable|string',
            'email_utilizador' => 'nullable|email',
            'perfil_id' => 'nullable|exists:roles,id',
        ]);

        DB::beginTransaction();

        try {
            // Atualizar ou criar dados de configuração
            $configuracao = DadosConfiguracao::updateOrCreate(
                [
                    'club_id' => $clubId,
                    'user_id' => $userId,
                ],
                $request->only([
                    'rgpd',
                    'rgpd_assinado',
                    'data_rgpd',
                    'arquivo_rgpd',
                    'consentimento',
                    'data_consentimento',
                    'arquivo_consentimento',
                    'afiliacao',
                    'data_afiliacao',
                    'arquivo_afiliacao',
                    'declaracao_transporte',
                    'declaracao_transporte_arquivo',
                    'email_utilizador',
                ])
            );

            // Sincronizar email_utilizador com users.email se foi alterado
            if ($request->has('email_utilizador') && $request->email_utilizador) {
                $user = User::findOrFail($userId);
                $user->update(['email' => $request->email_utilizador]);
            }

            // Atribuir perfil (role) se fornecido
            if ($request->has('perfil_id')) {
                $user = User::findOrFail($userId);
                $user->syncRoles([$request->perfil_id]);
                Log::info("Perfil atribuído ao user {$userId}: role {$request->perfil_id}");
            }

            DB::commit();

            return response()->json([
                'message' => 'Configuração atualizada com sucesso',
                'data' => $configuracao->fresh('user'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao atualizar configuração: {$e->getMessage()}");
            
            return response()->json([
                'error' => 'Erro ao atualizar configuração',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Reenviar email de recuperação de palavra-passe
     */
    public function reenviarRecuperacaoSenha(int $userId): JsonResponse
    {
        try {
            $user = User::findOrFail($userId);

            if (!$user->email) {
                return response()->json([
                    'error' => 'Utilizador não tem email configurado',
                ], 400);
            }

            // Enviar link de reset password
            $status = Password::sendResetLink(['email' => $user->email]);

            if ($status === Password::RESET_LINK_SENT) {
                Log::info("Email de recuperação de senha enviado para user {$userId}");
                
                return response()->json([
                    'message' => 'Email de recuperação enviado com sucesso',
                ]);
            }

            return response()->json([
                'error' => 'Erro ao enviar email de recuperação',
                'status' => $status,
            ], 400);

        } catch (\Exception $e) {
            Log::error("Erro ao reenviar recuperação de senha: {$e->getMessage()}");
            
            return response()->json([
                'error' => 'Erro ao reenviar email',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Alterar palavra-passe (admin ou próprio utilizador)
     */
    public function alterarSenha(Request $request, int $userId): JsonResponse
    {
        $request->validate([
            'senha_atual' => 'required_without:is_admin|string',
            'nova_senha' => 'required|string|min:8|confirmed',
            'is_admin' => 'sometimes|boolean',
        ]);

        try {
            $user = User::findOrFail($userId);
            $authUser = $request->user();

            // Se não for admin, validar senha atual
            if (!$request->is_admin || $authUser->id === $userId) {
                if (!Hash::check($request->senha_atual, $user->password)) {
                    return response()->json([
                        'error' => 'Senha atual incorreta',
                    ], 400);
                }
            }

            // Alterar senha
            $user->update(['password' => Hash::make($request->nova_senha)]);
            
            Log::info("Senha alterada para user {$userId}");

            return response()->json([
                'message' => 'Senha alterada com sucesso',
            ]);

        } catch (\Exception $e) {
            Log::error("Erro ao alterar senha: {$e->getMessage()}");
            
            return response()->json([
                'error' => 'Erro ao alterar senha',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
