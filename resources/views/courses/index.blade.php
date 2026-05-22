@extends('layouts.owner')
@section('title', 'Cours')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">COURS</div>
        <div class="page-subtitle">{{ $courses->count() }} cours enregistrés</div>
    </div>
    <div style="display:flex;gap:8px">
        <a href="{{ route('courses.schedule') }}" class="btn btn-secondary btn-sm">Planning</a>
        <button onclick="openModal('addCourseModal')" class="btn btn-primary btn-sm">+ Nouveau</button>
    </div>
</div>

<div style="display:flex;flex-direction:column;gap:10px">
    @forelse($courses as $course)
    <div class="card">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:10px">
            <div>
                <div class="fw-600" style="font-size:15px">{{ $course->name }}</div>
                <div class="text-sm text-muted">{{ $course->category }}</div>
            </div>
            <span class="badge badge-{{ $course->status }}">{{ $course->status }}</span>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;font-size:12px;margin-bottom:10px">
            <div style="background:var(--bg3);border-radius:6px;padding:7px 10px">
                <div class="text-muted">Capacité</div>
                <div class="fw-500">{{ $course->capacity }} pers.</div>
            </div>
            <div style="background:var(--bg3);border-radius:6px;padding:7px 10px">
                <div class="text-muted">Durée</div>
                <div class="fw-500">{{ $course->duration_minutes }} min</div>
            </div>
            <div style="background:var(--bg3);border-radius:6px;padding:7px 10px">
                <div class="text-muted">Prix</div>
                <div class="fw-500">{{ $course->price>0?number_format($course->price,0,',',' ').' F':'Inclus' }}</div>
            </div>
            <div style="background:var(--bg3);border-radius:6px;padding:7px 10px">
                <div class="text-muted">Salle</div>
                <div class="fw-500">{{ $course->room??'—' }}</div>
            </div>
        </div>

        @if($course->coach)
        <div style="display:flex;align-items:center;gap:8px;padding-top:10px;border-top:1px solid var(--border)">
            <div class="avatar" style="width:26px;height:26px;font-size:10px">
                {{ strtoupper(substr($course->coach->first_name,0,1).substr($course->coach->last_name,0,1)) }}
            </div>
            <div class="text-sm fw-500">{{ $course->coach->full_name }}</div>
        </div>
        @endif
    </div>
    @empty
    <div class="empty-state">Aucun cours créé</div>
    @endforelse
</div>

<div id="addCourseModal" class="modal-backdrop">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">NOUVEAU COURS</div>
            <button class="modal-close" onclick="closeModal('addCourseModal')">✕</button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('courses.store') }}">
                @csrf
                <div class="form-grid">
                    <div class="form-group"><label>Nom *</label><input type="text" name="name" required placeholder="CrossFit, Yoga…"></div>
                    <div class="form-group"><label>Catégorie *</label><input type="text" name="category" required placeholder="Cardio, Muscu…"></div>
                </div>
                <div class="form-group"><label>Description</label><textarea name="description" rows="2"></textarea></div>
                <div class="form-grid">
                    <div class="form-group"><label>Capacité *</label><input type="number" name="capacity" min="1" value="20" required></div>
                    <div class="form-group"><label>Durée (min) *</label><input type="number" name="duration_minutes" min="15" value="60" required></div>
                </div>
                <div class="form-grid">
                    <div class="form-group"><label>Prix (FCFA)</label><input type="number" name="price" min="0" value="0"></div>
                    <div class="form-group"><label>Salle</label><input type="text" name="room" placeholder="Salle A…"></div>
                </div>
                <div class="form-group"><label>Coach</label>
                    <select name="coach_id">
                        <option value="">— Sans coach</option>
                        @foreach($coaches as $coach)
                        <option value="{{ $coach->id }}">{{ $coach->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-full">Créer le cours</button>
            </form>
        </div>
    </div>
</div>
@endsection
