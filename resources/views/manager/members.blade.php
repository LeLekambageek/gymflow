@extends('layouts.manager')
@section('title', 'Membres')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">MEMBRES</div>
        <div class="page-subtitle">{{ $members->total() }} membres enregistrés</div>
    </div>
    <a href="{{ route('manager.dashboard') }}" class="btn btn-secondary btn-sm">← Retour</a>
</div>

<form method="GET" action="{{ route('manager.members') }}" class="filter-bar">
    <input type="text" name="search" placeholder="Nom ou téléphone…" value="{{ request('search') }}" style="flex:1;min-width:0">
    <select name="status" style="width:130px">
        <option value="">Tous</option>
        <option value="active" {{ request('status')==='active'?'selected':'' }}>Actif</option>
        <option value="inactive" {{ request('status')==='inactive'?'selected':'' }}>Inactif</option>
        <option value="suspended" {{ request('status')==='suspended'?'selected':'' }}>Suspendu</option>
    </select>
    <button type="submit" class="btn btn-secondary btn-sm">Filtrer</button>
</form>

{{-- MOBILE : cartes --}}
<div style="display:flex;flex-direction:column;gap:10px">
    @forelse($members as $member)
    <div style="background:var(--bg2);border:1px solid var(--border);border-radius:10px;padding:14px">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:10px">
            <div style="display:flex;align-items:center;gap:10px">
                <div class="avatar">{{ strtoupper(substr($member->first_name,0,1).substr($member->last_name,0,1)) }}</div>
                <div>
                    <div class="member-name">{{ $member->full_name }}</div>
                    <div class="member-sub">{{ $member->phone ?: $member->email }}</div>
                </div>
            </div>
            <span class="badge badge-{{ $member->status }}">{{ $member->status }}</span>
        </div>

        @if($member->activeSubscription && $member->activeSubscription->plan)
        <div style="background:var(--bg3);border-radius:8px;padding:8px 10px;margin-bottom:10px">
            <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px">
                <span class="fw-500">{{ $member->activeSubscription->plan->name }}</span>
                @php $days=$member->activeSubscription->days_remaining; @endphp
                <span class="badge {{ $days<=3?'badge-suspended':($days<=7?'badge-pending':'badge-active') }}">{{ (int)$days }}j restants</span>
            </div>
            <div class="progress"><div class="progress-bar {{ $days<=3?'red':($days<=7?'orange':'') }}" style="width:{{ min(100,max(0,($days/$member->activeSubscription->plan->duration_days)*100)) }}%"></div></div>
            <div style="font-size:11px;color:var(--muted);margin-top:4px">Expire le {{ $member->activeSubscription->end_date->format('d/m/Y') }}</div>
        </div>
        @else
        <div style="font-size:12px;color:var(--muted);margin-bottom:10px;padding:6px 10px;background:var(--bg3);border-radius:6px">Aucun abonnement actif</div>
        @endif

        <button onclick="prefillRenew({{ $member->id }},'{{ addslashes($member->full_name) }}','{{ $member->phone }}','{{ $member->activeSubscription?->plan->name ?? 'Aucun' }}','{{ $member->activeSubscription?->end_date->format('d/m/Y') ?? '—' }}')"
            class="btn btn-secondary btn-full" style="font-size:12px;min-height:36px">
            ↻ Renouveler l'abonnement
        </button>
    </div>
    @empty
    <div class="empty-state">Aucun membre trouvé</div>
    @endforelse
</div>

<div style="margin-top:14px">{{ $members->links() }}</div>

@push('scripts')
<script>
function prefillRenew(id,name,phone,plan,expires){
    window.location.href='{{ route('manager.dashboard') }}#renouvellement';
}
</script>
@endpush
@endsection
