<?php

namespace App\Http\Controllers;

use App\Models\TipoMembro;
use Illuminate\Http\Request;

class TipoMembroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tipos = TipoMembro::orderBy('nome')->get();
        
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
        $tipo = TipoMembro::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $tipo
        ]);
    }
}
