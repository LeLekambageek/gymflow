<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use Illuminate\Http\Request;

class CoachController extends Controller
{
    public function index()
    {
        $coaches = Coach::withCount('courses')->latest()->get();
        return view('coaches.index', compact('coaches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'  => 'required|string|max:100',
            'last_name'   => 'required|string|max:100',
            'email'       => 'required|email|unique:coaches',
            'phone'       => 'nullable|string|max:20',
            'speciality'  => 'nullable|string|max:100',
            'bio'         => 'nullable|string',
            'hourly_rate' => 'nullable|numeric|min:0',
        ]);

        Coach::create(array_merge($validated, ['status' => 'active']));
        return back()->with('success', 'Coach ajouté.');
    }

    public function update(Request $request, Coach $coach)
    {
        $validated = $request->validate([
            'first_name'  => 'required|string|max:100',
            'last_name'   => 'required|string|max:100',
            'email'       => "required|email|unique:coaches,email,{$coach->id}",
            'phone'       => 'nullable|string|max:20',
            'speciality'  => 'nullable|string|max:100',
            'bio'         => 'nullable|string',
            'hourly_rate' => 'nullable|numeric|min:0',
            'status'      => 'required|in:active,inactive',
        ]);

        $coach->update($validated);
        return back()->with('success', 'Coach mis à jour.');
    }

    public function destroy(Coach $coach)
    {
        $coach->delete();
        return back()->with('success', 'Coach supprimé.');
    }
}
