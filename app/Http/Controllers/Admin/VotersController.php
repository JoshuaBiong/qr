<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VotersModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;


class VotersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $voters = VotersModel::all();
        
        return Inertia::render('Admin/Voters/VotersList', [
            'voters' => $voters
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){
    return Inertia::render('Admin/Voters/VotersCreate');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'required|string|max:255',
        ]);

        $voter = VotersModel::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'middle_name' => $validated['middle_name'],
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
        ]);

        return redirect()->route('voters.index')
            ->with('success', 'Voter created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        //
    }

   
}
