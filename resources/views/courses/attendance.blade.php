@extends('layouts.owner')
@section('title', 'Présences')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">PRÉSENCES</div>
        <div class="page-subtitle">{{ $session->course->name }} · {{ $session->start_time->format('d/m/Y H:i') }}</div>
    </div>
    <a href="{{ route('courses.schedule') }}" class="btn btn-secondary btn-sm">← Planning</a>
</div>

{{-- STAT --}}
<div class="card" style="margin-bottom:14px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
        <div class="card-title" style="margin:0">{{ $session->bookings->count() }} inscrit(s) / {{ $session->course->capacity }} places</div>
        <span class="badge badge-{{ $session->status }}">{{ $session->status }}</span>
    </div>
    <div class="progress">
        <div class="progress-bar" style="width:{{ $session->course->capacity?($session->bookings->count()/$session->course->capacity*100):0 }}%"></div>
    </div>
</div>

{{-- LISTE PRÉSENCES --}}
@if($session->bookings->count() > 0)
<div style="display:flex;flex-direction:column;gap:8px">
    @foreach($session->bookings as $booking)
    <div style="background:var(--bg2);border:1px solid var(--border);border-radius:10px;padding:12px 14px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
            <div style="display:flex;align-items:center;gap:9px">
                <div class="avatar">{{ strtoupper(substr($booking->member->first_name,0,1).substr($booking->member->last_name,0,1)) }}</div>
                <div>
                    <div class="fw-500" style="font-size:13px">{{ $booking->member->full_name }}</div>
                    <div class="text-sm text-muted">Réservé {{ $booking->booked_at->format('d/m H:i') }}</div>
                </div>
            </div>
            <span class="badge badge-{{ $booking->status }}">{{ $booking->status }}</span>
        </div>
        <form method="POST" action="{{ route('courses.bookings.attendance',$booking) }}" style="display:flex;gap:8px">
            @csrf
            <button type="submit" name="status" value="attended"
                class="btn btn-sm" style="flex:1;background:rgba(34,197,94,0.15);color:var(--green);border:1px solid rgba(34,197,94,0.3)">
                ✓ Présent
            </button>
            <button type="submit" name="status" value="no_show"
                class="btn btn-sm" style="flex:1;background:rgba(239,68,68,0.15);color:var(--red);border:1px solid rgba(239,68,68,0.3)">
                ✗ Absent
            </button>
        </form>
    </div>
    @endforeach
</div>
@else
<div class="card">
    <div class="empty-state">Aucune réservation pour cette séance.</div>
</div>
@endif
@endsection
