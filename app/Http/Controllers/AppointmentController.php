<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment; 
use App\Models\Dentist;     

class AppointmentController extends Controller
{
    public function index()
    {
        // Load appointments with dentist info
        $appointments = Appointment::with('dentist')->get();
        $dentists = Dentist::paginate(10);


        // Count totals
        $totalAppointments = $appointments->count();
        $totalDentists = $dentists->total(); // for paginate

        // Static card (replace with anything you like)
        $staticCard = 42;

        // Pass all variables to the view
        return view('dashboard', compact(
            'appointments',
            'dentists',
            'totalAppointments',
            'totalDentists',
            'staticCard'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'appointment_time' => 'required|date',
            'dentist_id' => 'nullable|exists:dentists,id',
        ]);

        Appointment::create($validated);

        return back()->with('success', 'Appointment created!');
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'appointment_time' => 'required|date',
            'status' => 'required|string|max:50',
            'dentist_id' => 'nullable|exists:dentists,id',
        ]);

        $appointment->update($validated);

        return back()->with('success', 'Appointment updated!');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return back()->with('success', 'Appointment deleted!');
    }
}
