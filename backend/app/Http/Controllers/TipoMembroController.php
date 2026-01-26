<?php

namespace App\Http\Controllers;

use App\Models\TipoUtilizador;
use Illuminate\Http\Request;

class TipoMembroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tipos = TipoUtilizador::orderBy('nome')->get();
        
        return response()->json([
            'success' => true,
            'data' => $tipos
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $tipo = TipoUtilizador::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $tipo
        ]);
    }
}
