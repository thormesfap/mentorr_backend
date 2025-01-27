<?php

namespace App\Http\Controllers;

use App\Models\Mentor;
use App\Http\Requests\StoreMentorRequest;
use App\Http\Requests\UpdateMentorRequest;
use Symfony\Component\HttpFoundation\Response;

class MentorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Mentor::all(), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMentorRequest $request)
    {
        $mentor = new Mentor();
        $mentor->fill($request->all());
        $mentor->save();
        return response()->json($mentor, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Mentor $mentor)
    {
        return response()->json($mentor,Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMentorRequest $request, Mentor $mentor)
    {
        $mentor->fill($request->all());
        $mentor->save();
        return response()->json($mentor, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mentor $mentor)
    {
        $mentor->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
