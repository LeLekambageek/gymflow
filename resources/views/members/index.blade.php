@extends('layouts.owner')
@section('title', 'Membres')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">MEMBRES</div>
        <div class="page-subtitle">{{ $members->total() }} membres</div>
    </div>
    <a href="{{ route('members.create') }}" class="btn btn-primary btn-sm">+ Nouveau</a>
</div>

<form method="GET" class="filter-bar">
    <input type="text" name="search" placeholder="Nom, email, téléphone…" value="{{ request('search') }}" style="flex:1;min-width:0">
    <select name="status" style="width:120px">
        <option value="">Tous</option>
        <option value="active" {{ request('status')==='active'?'selected':'' }}>Actif</option>
        <option value="inactive" {{ request('status')==='inactive'?'selected':'' }}>Inactif</option>
        <option value="suspended" {{ request('status')==='suspended'?'selected':'' }}>Suspendu</option>
    </select>
    <button type="submit" class="btn btn-secondary btn-sm">OK</button>
</form>

<div style="display:flex;flex-direction:column;gap:10px">
    @forelse($members as $member)
    <div style="background:var(--bg2);border:1px solid var(--border);border-radius:10px;padding:12px 14px">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:8px">
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
        <div style="font-size:12px;padding:6px 10px;background:var(--bg3);border-radius:6px;margin-bottom:8px;display:flex;justify-content:space-between">
            <span class="fw-500">{{ $member->activeSubscription->plan->name }}</span>
            <span class="text-muted">expire {{ $member->activeSubscription->end_date->format('d/m/Y') }}</span>
        </div>
        @endif
        <div style="display:flex;gap:6px">
            <a href="{{ route('members.show',$member) }}" class="btn btn-secondary btn-sm" style="flex:1;text-align:center">Voir</a>
            <a href="{{ route('members.edit',$member) }}" class="btn btn-secondary btn-sm" style="flex:1;text-align:center">Modifier</a>
        </div>
    </div>
    @empty
    <div class="empty-state">Aucun membre trouvé</div>
    @endforelse
</div>
<div style="margin-top:14px">{{ $members->links() }}</div>
@endsection
