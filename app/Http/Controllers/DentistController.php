<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dentist;

class DentistController extends Controller
{
    public function index()
    {
        $dentists = Dentist::withCount('appointments')->paginate(10);
        return view('dentists', compact('dentists'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:dentists,name',
            'specialization' => 'nullable|string|max:255'
        ]);

        Dentist::create($validated);

        return back()->with('success', 'Dentist added!');
    }

    public function update(Request $request, Dentist $dentist)
    {
        $validated = $request->validate([
            'name' => 'required|unique:dentists,name,' . $dentist->id,
            'specialization' => 'nullable|string|max:255'
        ]);

        $dentist->update($validated);

        $dentist->refresh();

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'dentist' => $dentist,
            ]);
        }

        return back()->with('success', 'Dentist updated!');
    }

    public function destroy(Dentist $dentist)
    {
        $dentist->delete();
        return back()->with('success', 'Dentist deleted!');
    }
}
