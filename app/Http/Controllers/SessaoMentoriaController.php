<?php

namespace App\Http\Controllers;

use App\Models\SessaoMentoria;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SessaoMentoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       return response()->json(SessaoMentoria::all(),Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $valid = $request->validate([
            'data_hora_inicio' => 'required|date_format:H:i',
            'data_hora_termino' => 'required|date_format:H:i',
            'mentoria_id' => 'required',
        ]);
        $sessaoMentoria = new SessaoMentoria();
        $sessaoMentoria->fill($valid);
        $sessaoMentoria->save();
        return response()->json($sessaoMentoria,Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(SessaoMentoria $sessaoMentoria)
    {
        return response()->json($sessaoMentoria,Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SessaoMentoria $sessaoMentoria)
    {
        $valid = $request->validate([
            'data_hora_inicio' => 'required|date_format:H:i',
            'data_hora_termino' => 'required|date_format:H:i',
        ]);
        $sessaoMentoria->fill($valid);
        $sessaoMentoria->save();
        return response()->json($sessaoMentoria,Response::HTTP_OK);
    }

    public function avaliar(Request $request, SessaoMentoria $sessaoMentoria)
    {
        $valid = $request->validate(['avaliacao' => 'required|float']);
        $sessaoMentoria->avaliacao = $valid['avaliacao'];
        $sessaoMentoria->save();
        return response()->json($sessaoMentoria,Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SessaoMentoria $sessaoMentoria)
    {
        $sessaoMentoria->delete();
        return response()->json(null,Response::HTTP_NO_CONTENT);
    }
}
