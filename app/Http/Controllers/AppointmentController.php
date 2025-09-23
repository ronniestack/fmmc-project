<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Events\BookingCreated;
use App\Events\StatusUpdated;

class AppointmentController extends Controller
{
    /**
     * Display all appointments, latest first.
     */
    public function index()
    {
        $appointments = Appointment::latest()->get();

        return view('backend.appointment.index', compact('appointments'));
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
        // Validate input with specific rules
        $validated = $request->validate([
            'user_id'       => 'nullable|exists:users,id',
            'doctor_id'     => 'required|exists:doctors,id',
            'service_id'    => 'required|exists:services,id',
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'phone'         => 'required|string|max:20',
            'notes'         => 'nullable|string',
            'amount'        => 'required|numeric',
            'booking_date'  => 'required|date',
            'booking_time'  => 'required',
            'status'        => 'required|string',
        ]);

        // Determine if authenticated user is an admin/staff/doctor
        $isPrivilegedRole = auth()->check() && (
            auth()->user()->hasRole('admin') ||
            auth()->user()->hasRole('staff') ||
            auth()->user()->hasRole('doctor')
        );

        // If privileged (admin/staff/doctor), appointment has no linked user_id
        if ($isPrivilegedRole) {
            $validated['user_id'] = null;
        } elseif (auth()->check() && !$request->has('user_id')) {
            // Otherwise, assign the authenticated user as owner of the appointment
            $validated['user_id'] = auth()->id();
        }

        // Generate unique booking ID for tracking
        $validated['booking_id'] = 'BK-' . strtoupper(uniqid());

        // Save appointment in database
        $appointment = Appointment::create($validated);

        // Fire event (useful for sending notifications, emails, etc.)
        event(new BookingCreated($appointment));

        // Return JSON response for frontend/API
        return response()->json([
            'success'       => true,
            'message'       => 'Appointment booked successfully!',
            'booking_id'    => $appointment->booking_id,
            'appointment'   => $appointment
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        //
    }

    public function updateStatus(Request $request)
    {
        // Validate that appointment_id exists and status is provided
        $request->validate([
            'appointment_id'    => 'required|exists:appointments,id',
            'status'            => 'required|string',
        ]);

        // Fetch the appointment and update status
        $appointment = Appointment::findOrFail($request->appointment_id);
        $appointment->status = $request->status;
        $appointment->save();

        // Fire event to notify other parts of system
        event(new StatusUpdated($appointment));

        // Redirect back with success message
        return redirect()->back()->with('success', 'Appointment status updated successfully.');
    }
}
