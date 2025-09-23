<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Doctor $doctor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Doctor $doctor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Doctor $doctor)
    {
        // Validate the incoming request
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'sometimes|string|max:255|email:rfc,dns',
            'bio'       => 'nullable|string',
            'social'    => 'nullable|string',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doctor $doctor)
    {
        //
    }

    public function updateBio(Request $request, Doctor $doctor)
    {
        // Validate input specifically for bio and social
        $data = $request->validate([
            'bio'       => 'nullable|string|max:2000',
            'social'    => 'nullable'
        ]);

        // Apply updates
        $doctor->update($data);
        
        return back()->withSuccess('Profile has been updated successfullly!');
    }
}
