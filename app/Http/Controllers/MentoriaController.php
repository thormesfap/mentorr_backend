<?php

namespace App\Http\Controllers;

use App\Models\Mentoria;
use App\Http\Requests\StoreMentoriaRequest;
use App\Http\Requests\UpdateMentoriaRequest;
use Symfony\Component\HttpFoundation\Response;

class MentoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Mentoria::all(), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMentoriaRequest $request)
    {
        $mentoria = new Mentoria();
        $mentoria->fill($request->all());
        $mentoria->save();
        return response()->json($mentoria, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Mentoria $mentoria)
    {
        return response()->json($mentoria, Response::HTTP_OK);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMentoriaRequest $request, Mentoria $mentoria)
    {
        $mentoria->fill($request->all());
        $mentoria->save();
        return response()->json($mentoria, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mentoria $mentoria)
    {
        $mentoria->delete();
        return response()->json($mentoria, Response::HTTP_NO_CONTENT);
    }
}
