<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CargoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Cargo::all(),Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $valid = $request->validate([
            'nome' => 'required',
        ]);
        $cargo = new Cargo();
        $cargo->nome = $valid['nome'];
        $cargo->save();
        return response()->json($cargo, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cargo $cargo)
    {
       return response()->json($cargo,Response::HTTP_OK);
    }

     /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cargo $cargo)
    {
        $valid = $request->validate([
            'nome' => 'required',
        ]);
        $valid->nome = $valid['nome'];
        $cargo->save();
        return response()->json($cargo, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cargo $cargo)
    {
        $cargo->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
