<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Area::all(),Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $valid = $request->validate([
            'nome' => 'required',
        ]);
        $area = new Area();
        $area->nome = $valid['nome'];
        $area->save();
        return response()->json($area,Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Area $area)
    {
        return response()->json($area, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Area $area)
    {
        $valid = $request->validate([
            'nome' => 'required',
        ]);
        $area->nome = $valid['nome'];
        $area->save();
        return response()->json($area,Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Area $area)
    {
        $area->delete();
        return response()->json(null,Response::HTTP_NO_CONTENT);
    }
}
