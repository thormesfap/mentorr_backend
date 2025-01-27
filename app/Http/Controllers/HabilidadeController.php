<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Habilidade;
use App\Models\Mentor;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HabilidadeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Habilidade::all(),Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $valid = $request->validate([
            'nome' => 'required',
            'area_id' => 'required'
        ]);
        $area = Area::findOrFail($valid['area_id']);
        $habilidade = new Habilidade();
        $habilidade->fill($valid);
        $habilidade->save();
        return response()->json($habilidade,Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Habilidade $habilidade)
    {
        return response()->json($habilidade,Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Habilidade $habilidade)
    {
        $valid = $request->validate([
            'nome' => 'required',
            'area_id' => 'required'
        ]);
        $habilidade->fill($valid);
        $habilidade->save();
        return response()->json($habilidade,Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Habilidade $habilidade)
    {
        $habilidade->delete();
        return response()->json(null,Response::HTTP_NO_CONTENT);
    }

    public function doMentor(Request $request){
        $valid = $request->validate([
            'mentor_id' => 'required',
        ]);
        $mentor = Mentor::with('habilidades')->findOrFail($valid['mentor_id']);

        return response()->json($mentor->habilidades,Response::HTTP_OK);
    }
}
