@extends('layouts.manager')
@section('title', 'Dashboard Gérant')

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">MON DASHBOARD</div>
        <div class="page-subtitle">{{ now()->isoFormat('ddd D MMM Y') }}</div>
    </div>
</div>

{{-- STATS --}}
<div class="stats-grid">
    <div class="stat-card" style="--c:var(--green)">
        <div class="stat-label">Inscrits auj.</div>
        <div class="stat-value">{{ $newToday }}</div>
        <div class="stat-note">Nouveaux clients</div>
    </div>
    <div class="stat-card" style="--c:var(--blue)">
        <div class="stat-label">Renouvellements</div>
        <div class="stat-value">{{ $renewalsToday }}</div>
        <div class="stat-note">Aujourd'hui</div>
    </div>
    <div class="stat-card" style="--c:var(--orange)">
        <div class="stat-label">Abonnements actifs</div>
        <div class="stat-value">{{ $activeCount }}</div>
        @if($expiringCount > 0)
        <div class="stat-note" style="color:var(--orange)">⚠ {{ $expiringCount }} expirent dans 7j</div>
        @else
        <div class="stat-note">Aucune expiration imminente</div>
        @endif
    </div>
    <div class="stat-card" style="--c:var(--green)">
        <div class="stat-label">Encaissé auj.</div>
        <div class="stat-value" style="font-size:22px">{{ number_format($revenueToday,0,',',' ') }}</div>
        <div class="stat-note">FCFA</div>
    </div>
</div>

{{-- EXPIRATIONS URGENTES --}}
@if($expiringSoon->count() > 0)
<div class="card" style="border-color:rgba(239,68,68,0.3)">
    <div class="card-title" style="color:var(--red)">⚠ Expirent dans 7 jours ({{ $expiringSoon->count() }})</div>
    <div style="display:flex;flex-direction:column;gap:8px">
        @foreach($expiringSoon as $sub)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:var(--bg3);border-radius:8px;border-left:3px solid var(--red)">
            <div>
                <div class="fw-600" style="font-size:13px">{{ $sub->member->full_name }}</div>
                <div class="text-sm text-muted">{{ $sub->plan?->name ?? 'Plan supprimé' }} · {{ $sub->member->phone }}</div>
            </div>
            <div style="text-align:right;flex-shrink:0;margin-left:8px">
                <div style="color:var(--red);font-weight:600;font-size:13px">{{ $sub->days_remaining }}j</div>
                <button onclick="prefillRenew({{ $sub->member->id }},'{{ addslashes($sub->member->full_name) }}','{{ $sub->member->phone }}','{{ $sub->plan?->name ?? 'N/A' }}','{{ $sub->end_date->format('d/m/Y') }}')"
                    class="btn btn-orange btn-sm" style="margin-top:4px;font-size:11px;padding:4px 10px;min-height:28px">
                    Renouveler
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- INSCRIPTION NOUVEAU CLIENT --}}
<div class="card" id="inscription">
    <div class="card-title" style="color:var(--green)">＋ Inscrire un nouveau client</div>
    <form method="POST" action="{{ route('manager.register') }}">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label>Prénom *</label>
                <input type="text" name="first_name" placeholder="Prénom" required>
            </div>
            <div class="form-group">
                <label>Nom *</label>
                <input type="text" name="last_name" placeholder="Nom" required>
            </div>
        </div>
        <div class="form-group">
            <label>Téléphone *</label>
            <input type="tel" name="phone" placeholder="+221 77 000 00 00" required>
        </div>
        <div class="form-group">
            <label>Email <span style="font-weight:400;text-transform:none;font-size:11px;color:var(--muted)">(optionnel)</span></label>
            <input type="email" name="email" placeholder="client@email.com">
        </div>
        <div class="form-group">
            <label>Type d'abonnement *</label>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:6px" id="planGridReg">
                @foreach($plans as $plan)
                <label style="padding:10px 12px;background:var(--bg3);border:2px solid var(--border);border-radius:10px;cursor:pointer;transition:all 0.15s" class="plan-card-reg">
                    <input type="radio" name="subscription_plan_id" value="{{ $plan->id }}" style="display:none" required>
                    <div style="font-size:18px;font-weight:700;color:var(--accent);line-height:1">{{ number_format($plan->price,0,',',' ') }}<span style="font-size:10px;color:var(--muted);font-weight:400"> F</span></div>
                    <div style="font-size:11px;font-weight:500;margin-top:3px">{{ $plan->name }}</div>
                    <div style="font-size:10px;color:var(--muted)">{{ $plan->duration_days }}j</div>
                </label>
                @endforeach
            </div>
        </div>
        <div class="form-group">
            <label>Mode de paiement *</label>
            <select name="payment_method" required>
                <option value="cash">💵 Espèces</option>
                <option value="mobile">📱 Mobile Money (Wave/Orange)</option>
                <option value="card">💳 Carte bancaire</option>
                <option value="transfer">🏦 Virement</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-full" style="font-size:15px;padding:13px">
            Enregistrer le client
        </button>
    </form>
</div>

{{-- RENOUVELLEMENT --}}
<div class="card" id="renouvellement">
    <div class="card-title" style="color:var(--orange)">↻ Renouveler un abonnement</div>

    <div class="form-group">
        <label>Rechercher le client</label>
        <input type="text" id="memberSearch" placeholder="Nom ou numéro de téléphone…" autocomplete="off" style="border-color:var(--orange)">
        <div id="searchResults" style="display:none;background:var(--bg3);border:1px solid var(--border);border-radius:8px;margin-top:4px;overflow:hidden;max-height:200px;overflow-y:auto"></div>
    </div>

    <div id="selectedMember" style="display:none;padding:12px;background:rgba(249,115,22,0.08);border:1px solid rgba(249,115,22,0.2);border-radius:8px;margin-bottom:14px">
        <div style="display:flex;align-items:flex-start;justify-content:space-between">
            <div>
                <div class="fw-600" id="sel-name"></div>
                <div class="text-sm text-muted" id="sel-phone"></div>
            </div>
            <button type="button" onclick="clearMember()" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:18px;padding:0">✕</button>
        </div>
        <div class="divider" style="margin:8px 0"></div>
        <div style="display:flex;justify-content:space-between;font-size:12px">
            <span class="text-muted">Plan actuel</span><span class="fw-500" id="sel-plan"></span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:12px;margin-top:4px">
            <span class="text-muted">Expire le</span><span id="sel-expires" class="fw-500"></span>
        </div>
    </div>

    <form method="POST" action="{{ route('manager.renew') }}" id="renewForm">
        @csrf
        <input type="hidden" name="member_id" id="renewMemberId">
        <div class="form-group">
            <label>Nouveau plan *</label>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:6px" id="renewPlanGrid">
                @foreach($plans as $plan)
                <label style="padding:10px 12px;background:var(--bg3);border:2px solid var(--border);border-radius:10px;cursor:pointer;transition:all 0.15s" class="plan-card-ren">
                    <input type="radio" name="subscription_plan_id" value="{{ $plan->id }}" style="display:none" required>
                    <div style="font-size:18px;font-weight:700;color:var(--orange);line-height:1">{{ number_format($plan->price,0,',',' ') }}<span style="font-size:10px;color:var(--muted);font-weight:400"> F</span></div>
                    <div style="font-size:11px;font-weight:500;margin-top:3px">{{ $plan->name }}</div>
                    <div style="font-size:10px;color:var(--muted)">{{ $plan->duration_days }}j</div>
                </label>
                @endforeach
            </div>
        </div>
        <div class="form-group">
            <label>Mode de paiement *</label>
            <select name="payment_method" required>
                <option value="cash">💵 Espèces</option>
                <option value="mobile">📱 Mobile Money</option>
                <option value="card">💳 Carte bancaire</option>
                <option value="transfer">🏦 Virement</option>
            </select>
        </div>
        <button type="submit" class="btn btn-orange btn-full" id="renewBtn" disabled style="font-size:15px;padding:13px;opacity:0.5">
            Renouveler l'abonnement
        </button>
    </form>
</div>

{{-- LISTE ABONNEMENTS ACTIFS --}}
<div class="card" id="abonnements">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
        <div class="card-title" style="margin:0">Abonnements actifs · {{ $activeSubscriptions->total() }}</div>
        <a href="{{ route('manager.members') }}" class="btn btn-secondary btn-sm">Voir tout →</a>
    </div>

    {{-- MOBILE : cartes --}}
    <div class="mobile-cards">
        @forelse($activeSubscriptions as $sub)
        <div style="padding:12px;background:var(--bg3);border-radius:10px;margin-bottom:8px;border-left:3px solid {{ $sub->days_remaining <= 3 ? 'var(--red)' : ($sub->days_remaining <= 7 ? 'var(--orange)' : 'var(--accent)') }}">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:8px">
                <div style="display:flex;align-items:center;gap:9px">
                    <div class="avatar">{{ strtoupper(substr($sub->member->first_name,0,1).substr($sub->member->last_name,0,1)) }}</div>
                    <div>
                        <div class="fw-600" style="font-size:13px">{{ $sub->member->full_name }}</div>
                        <div class="text-sm text-muted">{{ $sub->member->phone ?: '—' }}</div>
                    </div>
                </div>
                @php $days=$sub->days_remaining; @endphp
                <span class="badge {{ $days<=3?'badge-suspended':($days<=7?'badge-pending':'badge-active') }}">{{ $days }}j</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:6px">
                <span class="text-muted">{{ $sub->plan?->name ?? 'Plan supprimé' }}</span>
                <span class="fw-500">{{ number_format($sub->amount_paid,0,',',' ') }} FCFA</span>
            </div>
            @if($sub->plan)
            <div class="progress"><div class="progress-bar {{ $days<=3?'red':($days<=7?'orange':'') }}" style="width:{{ min(100,max(0,($days/$sub->plan->duration_days)*100)) }}%"></div></div>
            @endif
            <div style="margin-top:8px;text-align:right">
                <button onclick="prefillRenew({{ $sub->member->id }},'{{ addslashes($sub->member->full_name) }}','{{ $sub->member->phone }}','{{ $sub->plan?->name ?? 'N/A' }}','{{ $sub->end_date->format('d/m/Y') }}')"
                    class="btn btn-secondary btn-sm" style="font-size:11px">
                    Renouveler
                </button>
            </div>
        </div>
        @empty
        <div class="empty-state">Aucun abonnement actif</div>
        @endforelse
    </div>

    <div style="margin-top:12px">{{ $activeSubscriptions->links() }}</div>
</div>

@push('scripts')
<script>
// Plan cards inscription
document.querySelectorAll('.plan-card-reg').forEach(function(lbl){
    lbl.addEventListener('click',function(){
        document.querySelectorAll('.plan-card-reg').forEach(function(l){
            l.style.borderColor='var(--border)';l.style.background='var(--bg3)';
        });
        this.style.borderColor='var(--accent)';this.style.background='rgba(34,197,94,0.06)';
    });
});
// Plan cards renouvellement
document.querySelectorAll('.plan-card-ren').forEach(function(lbl){
    lbl.addEventListener('click',function(){
        document.querySelectorAll('.plan-card-ren').forEach(function(l){
            l.style.borderColor='var(--border)';l.style.background='var(--bg3)';
        });
        this.style.borderColor='var(--orange)';this.style.background='rgba(249,115,22,0.06)';
    });
});

// Recherche membre
var searchTimeout;
var searchInput=document.getElementById('memberSearch');
var results=document.getElementById('searchResults');
searchInput.addEventListener('input',function(){
    clearTimeout(searchTimeout);
    var q=this.value.trim();
    if(q.length<2){results.style.display='none';return;}
    searchTimeout=setTimeout(async function(){
        var res=await fetch('{{ route('manager.search') }}?q='+encodeURIComponent(q));
        var members=await res.json();
        if(!members.length){
            results.innerHTML='<div style="padding:12px 14px;font-size:13px;color:var(--muted)">Aucun résultat</div>';
        }else{
            results.innerHTML=members.map(function(m){
                return '<div onclick="selectMember('+m.id+',\''+m.name+'\',\''+m.phone+'\',\''+m.plan+'\',\''+m.expires+'\','+m.days_left+')" style="padding:12px 14px;cursor:pointer;border-bottom:1px solid var(--border)">'
                +'<div style="font-weight:500;font-size:14px">'+m.name+'</div>'
                +'<div style="font-size:11px;color:var(--muted)">'+m.phone+' · '+m.plan+'</div>'
                +'</div>';
            }).join('');
        }
        results.style.display='block';
    },300);
});
document.addEventListener('click',function(e){
    if(!results.contains(e.target)&&e.target!==searchInput)results.style.display='none';
});
function selectMember(id,name,phone,plan,expires,daysLeft){
    document.getElementById('renewMemberId').value=id;
    document.getElementById('sel-name').textContent=name;
    document.getElementById('sel-phone').textContent=phone;
    document.getElementById('sel-plan').textContent=plan;
    document.getElementById('sel-expires').textContent=expires;
    document.getElementById('sel-expires').style.color=daysLeft<=7?'var(--red)':'var(--text)';
    document.getElementById('selectedMember').style.display='block';
    var btn=document.getElementById('renewBtn');
    btn.disabled=false;btn.style.opacity='1';
    searchInput.value=name;
    results.style.display='none';
}
function clearMember(){
    document.getElementById('renewMemberId').value='';
    document.getElementById('selectedMember').style.display='none';
    var btn=document.getElementById('renewBtn');
    btn.disabled=true;btn.style.opacity='0.5';
    searchInput.value='';
}
function prefillRenew(id,name,phone,plan,expires){
    selectMember(id,name,phone,plan,expires,999);
    document.getElementById('renouvellement').scrollIntoView({behavior:'smooth',block:'start'});
}
</script>
@endpush
@endsection
