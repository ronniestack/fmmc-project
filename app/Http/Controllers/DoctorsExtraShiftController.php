<?php

namespace App\Http\Controllers;

use App\Models\DoctorsExtraShift;
use Illuminate\Http\Request;

class DoctorsExtraShiftController extends Controller
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
        // Validate request input
        $data = $request->validate([
            'doctor_id'    => 'required',
            'date'         => 'required',
            'hours'        => 'nullable',
            'recurring'    => 'nullable',
        ]);

        // Create the extra shift in database
        DoctorsExtraShift::create($data);

        // Redirect back with success message
        return back()->withSuccess('Extra shift has been created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(DoctorsExtraShift $doctorsExtraShift)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DoctorsExtraShift $doctorsExtraShift)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DoctorsExtraShift $doctorsExtraShift)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DoctorsExtraShift $doctorsExtraShift)
    {
        //
    }
}
