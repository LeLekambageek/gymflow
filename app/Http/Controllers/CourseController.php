<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSession;
use App\Models\Booking;
use App\Models\Coach;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['coach', 'sessions' => fn($q) => $q->where('start_time', '>=', now())])
            ->latest()->get();
        $coaches = Coach::where('status', 'active')->get();

        return view('courses.index', compact('courses', 'coaches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:100',
            'description'      => 'nullable|string',
            'coach_id'         => 'nullable|exists:coaches,id',
            'category'         => 'required|string',
            'capacity'         => 'required|integer|min:1',
            'duration_minutes' => 'required|integer|min:15',
            'price'            => 'required|numeric|min:0',
            'room'             => 'nullable|string|max:50',
        ]);

        Course::create($validated);
        return back()->with('success', 'Cours créé.');
    }

    public function schedule()
    {
        $sessions = CourseSession::with(['course.coach'])
            ->where('start_time', '>=', now()->startOfWeek())
            ->where('start_time', '<=', now()->endOfWeek()->addWeeks(2))
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn($s) => $s->start_time->toDateString());

        $courses = Course::where('status', 'active')->get();

        return view('courses.schedule', compact('sessions', 'courses'));
    }

    public function storeSession(Request $request)
    {
        $validated = $request->validate([
            'course_id'  => 'required|exists:courses,id',
            'start_time' => 'required|date|after:now',
            'notes'      => 'nullable|string',
        ]);

        $course = Course::findOrFail($validated['course_id']);

        CourseSession::create(array_merge($validated, [
            'end_time' => \Carbon\Carbon::parse($validated['start_time'])->addMinutes($course->duration_minutes),
            'status'   => 'scheduled',
        ]));

        return back()->with('success', 'Séance planifiée.');
    }

    public function sessionAttendance(CourseSession $session)
    {
        $session->load(['course', 'bookings.member']);
        return view('courses.attendance', compact('session'));
    }

    public function markAttendance(Request $request, Booking $booking)
    {
        $booking->update(['status' => $request->status]);
        return back()->with('success', 'Présence enregistrée.');
    }
}
