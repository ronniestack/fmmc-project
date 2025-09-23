<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Setting;
use App\Events\StatusUpdated;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the dashboard with all relevant appointments.
     */
    public function index()
    {
        // Get system-wide settings (throws 404 if not found)
        $setting = Setting::firstOrFail();

        // Get logged-in user
        $user = auth()->user();

        // Start with base query including relationships
        $query = Appointment::query()->with(['doctor.user', 'service', 'user']);

        // Restrict access: only admins see all data
        if (!$user->hasRole('admin')) {
            $query->where(function($q) use ($user) {
                if ($user->doctor) {
                    $q->where('doctor_id', $user->doctor->id);
                }

                // Also show appointments the user booked
                $q->orWhere('user_id', $user->id);
            });
        }

        // Fetch and transform appointments into calendar-friendly format
        $appointments = $query->get()->map(function ($appointment) {
            try {
                // Ensure booking_time has a start-end format like "10:00 AM - 11:00 AM"
                if (!str_contains($appointment->booking_time ?? '', '-')) {
                    throw new \Exception("Invalid time format");
                }

                // Parse booking date
                $bookingDate = Carbon::parse($appointment->booking_date);

                // Split start and end times
                [$startTime, $endTime] = array_map('trim', explode('-', $appointment->booking_time));

                // Convert times into Carbon datetime with the same booking date
                $startDateTime = Carbon::createFromFormat('h:i A', $startTime)
                    ->setDate($bookingDate->year, $bookingDate->month, $bookingDate->day);

                $endDateTime = Carbon::createFromFormat('h:i A', $endTime)
                    ->setDate($bookingDate->year, $bookingDate->month, $bookingDate->day);
      
                // If end time is earlier than start (e.g., overnight appointments), push to next day
                if ($endDateTime->lt($startDateTime)) {
                    $endDateTime->addDay();
                }

                // Return formatted appointment
                return [
                    'id'            => $appointment->id, 
                    'title'         => sprintf('%s - %s',
                                        $appointment->name,
                                        $appointment->service->title ?? 'Service'),
                    'start'         => $startDateTime->toIso8601String(),
                    'end'           => $endDateTime->toIso8601String(),
                    'description'   => $appointment->notes,
                    'email'         => $appointment->email,
                    'phone'         => $appointment->phone,
                    'amount'        => $appointment->amount,
                    'status'        => $appointment->status,
                    'doctor'        => $appointment->doctor->user->name ?? 'Unassigned',
                    'color'         => $this->getStatusColor($appointment->status),
                    'service_title' => $appointment->service->title ?? 'Service', 
                    'name'          => $appointment->name, 
                    'notes'         => $appointment->notes, 
                ];
            } catch (\Exception $e) {
                // Log formatting issues instead of breaking the page
                \Log::error("Format error for appointment {$appointment->id}: {$e->getMessage()}");
                return null;
            }
        })->filter();

        return view('backend.dashboard.index', compact('appointments'));
    }

    /**
     * Return color code for appointment status.
     */
    private function getStatusColor($status)
    {
        $colors = [
            'Booked'    => '#3498db',
            'Rendered'  => '#2ecc71',
            'Cancelled' => '#ff0000',
        ];

        // Default gray if status not found
        return $colors[$status] ?? '#7f8c8d';
    }

    /**
     * Update appointment status (called from AppointmentController ideally).
     */
    public function updateStatus(Request $request)
    {
        // Validate request
        $request->validate([
            'appointment_id'    => 'required|exists:appointments,id',
            'status'            => 'required|in:Booked,Rendered,Cancelled'
        ]);

        // Find appointment
        $appointment = Appointment::findOrFail($request->appointment_id);

        // Update status
        $appointment->status = $request->status;
        $appointment->save();

        // Fire event for listeners (e.g., notify doctor/patient)
        event(new StatusUpdated($appointment));

        return back()->with('success', 'Status updated successfully');
    }
}
