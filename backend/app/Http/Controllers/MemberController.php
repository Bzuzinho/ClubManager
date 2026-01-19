<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        return User::query()
            ->select('id', 'name', 'email', 'numero_socio', 'estado')
            ->with('roles') // se usares roles
            ->orderBy('name')
            ->get();
    }
}
