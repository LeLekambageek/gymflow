@extends('layouts.owner')
@section('title', 'Planning')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">PLANNING</div>
        <div class="page-subtitle">2 prochaines semaines</div>
    </div>
    <button onclick="openModal('addSessionModal')" class="btn btn-primary btn-sm">+ Planifier</button>
</div>

@forelse($sessions as $date => $daySessions)
<div style="margin-bottom:16px">
    <div style="font-size:10px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);margin-bottom:8px;padding:4px 0;border-bottom:1px solid var(--border)">
        {{ \Carbon\Carbon::parse($date)->isoFormat('dddd D MMMM') }}
    </div>
    <div style="display:flex;flex-direction:column;gap:8px">
        @foreach($daySessions as $session)
        <div style="display:flex;align-items:center;gap:12px;padding:12px 14px;background:var(--bg2);border:1px solid var(--border);border-left:3px solid var(--orange);border-radius:10px">
            <div style="font-family:var(--font-num);font-size:22px;color:var(--orange);min-width:50px;flex-shrink:0">
                {{ $session->start_time->format('H:i') }}
            </div>
            <div style="flex:1;min-width:0">
                <div class="fw-600" style="font-size:13px">{{ $session->course->name }}</div>
                <div class="text-sm text-muted">
                    {{ $session->course->duration_minutes }}min
                    @if($session->course->coach) · {{ $session->course->coach->full_name }}@endif
                    @if($session->course->room) · {{ $session->course->room }}@endif
                </div>
                <div style="display:flex;align-items:center;gap:8px;margin-top:4px">
                    <div class="progress" style="flex:1;max-width:80px">
                        <div class="progress-bar" style="width:{{ $session->course->capacity>0?($session->registered_count/$session->course->capacity*100):0 }}%"></div>
                    </div>
                    <div class="text-sm text-muted">{{ $session->registered_count }}/{{ $session->course->capacity }}</div>
                </div>
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;flex-shrink:0">
                <span class="badge badge-{{ $session->status }}" style="font-size:9px">{{ $session->status }}</span>
                <a href="{{ route('courses.sessions.attendance',$session) }}" class="btn btn-secondary btn-sm" style="font-size:10px;padding:4px 8px;min-height:26px">Présences</a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@empty
<div class="card">
    <div class="empty-state">Aucune séance planifiée pour les 2 prochaines semaines.</div>
</div>
@endforelse

<div id="addSessionModal" class="modal-backdrop">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">NOUVELLE SÉANCE</div>
            <button class="modal-close" onclick="closeModal('addSessionModal')">✕</button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('courses.sessions.store') }}">
                @csrf
                <div class="form-group">
                    <label>Cours *</label>
                    <select name="course_id" required>
                        <option value="">Choisir un cours…</option>
                        @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->name }} ({{ $course->capacity }} places)</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Date et heure *</label>
                    <input type="datetime-local" name="start_time" required>
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" rows="2" placeholder="Informations particulières…"></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-full">Planifier la séance</button>
            </form>
        </div>
    </div>
</div>
@endsection
