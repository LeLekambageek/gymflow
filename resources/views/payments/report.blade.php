@extends('layouts.owner')
@section('title', 'Rapport financier')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">RAPPORT</div>
        <div class="page-subtitle">12 derniers mois</div>
    </div>
    <a href="{{ route('payments.index') }}" class="btn btn-secondary btn-sm">← Paiements</a>
</div>

<div class="card">
    <div class="card-title">Revenus 12 mois</div>
    <div style="position:relative;height:200px"><canvas id="chart12"></canvas></div>
</div>

<div class="card">
    <div class="card-title">Répartition par type</div>
    <div style="position:relative;height:180px"><canvas id="chartType"></canvas></div>
</div>

<div class="card">
    <div class="card-title">Top membres</div>
    @foreach($topMembers as $i=>$m)
    <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid var(--border)">
        <div style="font-family:var(--font-num);font-size:18px;color:var(--muted);min-width:24px">{{ $i+1 }}</div>
        <div class="avatar" style="width:28px;height:28px;font-size:10px;flex-shrink:0">{{ strtoupper(substr($m->first_name,0,1).substr($m->last_name,0,1)) }}</div>
        <div style="flex:1;min-width:0">
            <div class="fw-500" style="font-size:13px">{{ $m->full_name }}</div>
            <div class="progress" style="margin-top:4px"><div class="progress-bar" style="width:{{ ($m->total_paid/($topMembers->first()->total_paid??1))*100 }}%"></div></div>
        </div>
        <div class="fw-500 text-orange mono" style="font-size:12px;flex-shrink:0">{{ number_format($m->total_paid,0,',',' ') }} F</div>
    </div>
    @endforeach
</div>

<div class="card">
    <div class="card-title">Détail mensuel</div>
    @foreach(array_reverse($data) as $row)
    @php $total=$row['subscription']+$row['course']+$row['other']; @endphp
    <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border)">
        <div class="fw-500" style="font-size:13px;min-width:80px">{{ $row['month'] }}</div>
        <div style="text-align:right">
            <div class="fw-500 text-orange mono">{{ number_format($total,0,',',' ') }} F</div>
            <div class="text-sm text-muted">Abo: {{ number_format($row['subscription'],0,',',' ') }} F</div>
        </div>
    </div>
    @endforeach
    <div style="display:flex;justify-content:space-between;padding:10px 0;font-size:14px;font-weight:600">
        <span>TOTAL</span>
        <span class="text-orange mono">{{ number_format(array_sum(array_column($data,'subscription'))+array_sum(array_column($data,'course'))+array_sum(array_column($data,'other')),0,',',' ') }} F</span>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
var data=@json($data);
var opts={responsive:true,maintainAspectRatio:false,plugins:{legend:{labels:{color:'#6b7a99',font:{size:11}}}},scales:{x:{stacked:true,grid:{color:'#2a3347'},ticks:{color:'#6b7a99',font:{size:10}}},y:{stacked:true,grid:{color:'#2a3347'},ticks:{color:'#6b7a99',font:{size:10}}}}};
new Chart(document.getElementById('chart12').getContext('2d'),{type:'bar',data:{labels:data.map(function(d){return d.month;}),datasets:[{label:'Abonnements',data:data.map(function(d){return d.subscription;}),backgroundColor:'rgba(249,115,22,0.8)',borderRadius:4,borderSkipped:false},{label:'Cours',data:data.map(function(d){return d.course;}),backgroundColor:'rgba(59,130,246,0.7)',borderRadius:4,borderSkipped:false}]},options:opts});
var ts=data.reduce(function(s,d){return s+d.subscription;},0);
var tc=data.reduce(function(s,d){return s+d.course;},0);
var to=data.reduce(function(s,d){return s+d.other;},0);
new Chart(document.getElementById('chartType').getContext('2d'),{type:'doughnut',data:{labels:['Abonnements','Cours','Autre'],datasets:[{data:[ts,tc,to],backgroundColor:['rgba(249,115,22,0.8)','rgba(59,130,246,0.7)','rgba(107,122,153,0.5)'],borderWidth:0}]},options:{responsive:true,maintainAspectRatio:false,cutout:'65%',plugins:{legend:{position:'bottom',labels:{color:'#6b7a99',padding:10,font:{size:11}}}}}});
</script>
@endpush
@endsection
