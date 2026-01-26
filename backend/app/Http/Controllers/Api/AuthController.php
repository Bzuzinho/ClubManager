<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required','email'],
            'password' => ['required','string'],
        ]);

        if (!Auth::attempt($data)) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        $user = User::where('email', $data['email'])->firstOrFail();

        // Carregar os clubes do utilizador
        $user->load(['clubUsers.club']);

        // Obter o primeiro clube ativo do usuário para retornar ao frontend
        $firstActiveClub = $user->clubUsers()
            ->whereRaw('ativo = true')
            ->first();

        // Definir clube ativo na sessão automaticamente
        if ($firstActiveClub) {
            Session::put('active_club_id', $firstActiveClub->club_id);
        }

        // Formatar lista de clubes para o frontend
        $clubs = $user->clubUsers->map(function ($clubUser) {
            return [
                'id' => $clubUser->club->id,
                'nome' => $clubUser->club->nome,
                'role' => $clubUser->role,
            ];
        });

        return response()->json([
            'token' => $user->createToken('auth')->plainTextToken,
            'user' => array_merge($user->toArray(), ['clubs' => $clubs]),
            'active_club_id' => $firstActiveClub?->club_id,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
