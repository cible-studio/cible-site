{{-- Rapport des demandes du formulaire de contact (2026-11-25).
     Chiffres de la période, répartitions, puis le détail de chaque demande.
     Données : App\Support\Demandes (volume persistant, 12 mois). --}}
@extends('admin._layout', ['titre' => 'Demandes reçues', 'connecte' => true, 'onglet' => 'demandes'])
@use('App\Support\Demandes')

@section('contenu')
@php
    $max = fn (array $r) => max(1, ...array_values($r ?: [0]));
    $date = fn (string $iso) => \Illuminate\Support\Carbon::parse($iso)->timezone('Africa/Abidjan');
@endphp

<style>
    .dm-periodes{display:flex;flex-wrap:wrap;gap:8px;margin-top:18px}
    .dm-periodes a{display:inline-flex;align-items:center;min-height:40px;padding:0 16px;border-radius:999px;border:1.5px solid var(--gris);
        background:#fff;font-weight:700;font-size:13.5px;text-decoration:none;color:var(--noir)}
    .dm-periodes a[aria-current="page"]{background:var(--noir);border-color:var(--noir);color:#fff}
    .dm-chiffres{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-top:22px}
    .dm-chiffre{background:#fff;border:1px solid var(--gris);border-radius:14px;padding:18px 20px}
    .dm-chiffre b{display:block;font-size:34px;font-weight:800;line-height:1}
    .dm-chiffre span{display:block;margin-top:8px;font-size:13px;color:#666}
    .dm-grille{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:14px}
    .dm-barre{display:grid;grid-template-columns:minmax(0,1fr) 34px;gap:4px 10px;align-items:center;margin-top:10px;font-size:13.5px}
    .dm-barre .lib{overflow-wrap:anywhere}
    .dm-barre .n{text-align:right;font-weight:800}
    .dm-barre .piste{grid-column:1/-1;height:7px;border-radius:7px;background:#F0F0EE;overflow:hidden}
    .dm-barre .piste i{display:block;height:100%;border-radius:7px;background:var(--rouge)}
    .dm-demande{background:#fff;border:1px solid var(--gris);border-radius:12px;margin-top:10px}
    .dm-demande summary{display:flex;flex-wrap:wrap;align-items:center;gap:6px 14px;padding:14px 18px;cursor:pointer;list-style:none}
    .dm-demande summary::-webkit-details-marker{display:none}
    .dm-demande summary::after{content:"▾";margin-left:auto;color:#999}
    .dm-demande[open] summary::after{content:"▴"}
    .dm-demande .quand{font-size:13px;color:#777;min-width:120px}
    .dm-demande .qui{font-weight:800}
    .dm-badge{display:inline-flex;padding:3px 10px;border-radius:999px;font-size:12px;font-weight:700;background:#F0F0EE}
    .dm-badge.ko{background:#fee2e2;color:#991b1b}
    .dm-corps{padding:4px 18px 18px;border-top:1px solid var(--gris)}
    .dm-corps dl{display:grid;grid-template-columns:150px minmax(0,1fr);gap:8px 16px;margin-top:14px;font-size:14px}
    .dm-corps dt{color:#777;font-weight:700}
    .dm-corps dd{margin:0;overflow-wrap:anywhere}
    .dm-corps .message{white-space:pre-line}
    @media(max-width:640px){.dm-corps dl{grid-template-columns:1fr}.dm-corps dt{margin-top:6px}}
</style>

<h1>Demandes reçues</h1>
<p class="intro">Demandes de recommandation média envoyées par le formulaire de contact du site. Chaque demande est conservée {{ Demandes::MOIS_CONSERVES }} mois ; les pièces jointes ne sont jamais conservées.</p>

<nav class="dm-periodes" aria-label="Période">
    @foreach($periodes as $cle => $nom)
        <a href="{{ route('admin.demandes', ['periode' => $cle]) }}" @if($cle === $periode) aria-current="page" @endif>{{ $nom }}</a>
    @endforeach
    <a href="{{ route('admin.demandes.export', ['periode' => $periode]) }}" style="margin-left:auto;background:var(--rouge);border-color:var(--rouge);color:#fff">Exporter (Excel)</a>
</nav>

@if($stats['mails_non_partis'] > 0)
    <div class="alerte a-ko" style="margin-top:20px">⚠ {{ $stats['mails_non_partis'] }} demande(s) de cette période n'ont pas pu être envoyées par e-mail : elles ne sont qu'ici. Pensez à recontacter ces prospects.</div>
@endif

<div class="dm-chiffres">
    <div class="dm-chiffre"><b>{{ $stats['total'] }}</b><span>Demande(s) — {{ $libelle }}</span></div>
    @php $parMois = $stats['par_mois']; @endphp
    @if(count($parMois) > 1)
        <div class="dm-chiffre"><b>{{ number_format($stats['total'] / count($parMois), 1, ',', ' ') }}</b><span>Demandes par mois en moyenne</span></div>
    @endif
    @php $topBudget = array_key_first(array_diff_key($stats['repartitions']['budget'], ['Non précisé' => 1])); @endphp
    @if($topBudget)
        <div class="dm-chiffre"><b style="font-size:20px;line-height:1.25">{{ $topBudget }}</b><span>Budget le plus demandé</span></div>
    @endif
</div>

@if(count($parMois) > 1)
<div class="carte">
    <h2>Demandes par mois</h2>
    @php $m = $max($parMois); @endphp
    @foreach($parMois as $mois => $n)
        <div class="dm-barre">
            <span class="lib">{{ Demandes::libelleMois($mois) }}</span><span class="n">{{ $n }}</span>
            <span class="piste"><i style="width:{{ round($n / $m * 100) }}%"></i></span>
        </div>
    @endforeach
</div>
@endif

@if($stats['total'] > 0)
<div class="dm-grille" style="margin-top:22px">
    @foreach(Demandes::MULTIPLES + Demandes::UNIQUES as $cle => $titre)
        @php $r = $stats['repartitions'][$cle] ?? []; @endphp
        @continue(empty($r))
        <div class="carte" style="margin-top:0">
            <h2>{{ $titre }}</h2>
            @if(isset(Demandes::MULTIPLES[$cle]))<p class="aide" style="margin-bottom:4px">Plusieurs choix possibles par demande.</p>@endif
            @php $m = $max($r); @endphp
            @foreach($r as $valeur => $n)
                <div class="dm-barre">
                    <span class="lib">{{ $valeur }}</span><span class="n">{{ $n }}</span>
                    <span class="piste"><i style="width:{{ round($n / $m * 100) }}%"></i></span>
                </div>
            @endforeach
        </div>
    @endforeach
</div>
@endif

<div class="carte">
    <h2>Détail des demandes ({{ count($demandes) }})</h2>
    @if(empty($demandes))
        <p class="aide" style="margin:8px 0 0">Aucune demande sur cette période.</p>
    @endif
    @foreach($demandes as $d)
        <details class="dm-demande">
            <summary>
                <span class="quand">{{ $date($d['recue_le'])->format('d/m/Y à H:i') }}</span>
                <span class="qui">{{ $d['entreprise'] }}</span>
                <span>{{ $d['nom'] }}</span>
                @if($d['budget'])<span class="dm-badge">{{ $d['budget'] }}</span>@endif
                @if($d['mail'] === false)<span class="dm-badge ko">Mail non parti</span>@endif
            </summary>
            <div class="dm-corps">
                <dl>
                    <dt>Contact</dt>
                    <dd>{{ $d['nom'] }}@if($d['poste']) · {{ $d['poste'] }}@endif<br>
                        <a href="mailto:{{ $d['email'] }}">{{ $d['email'] }}</a> · <a href="tel:{{ preg_replace('#[^\d+]#', '', (string) $d['tel']) }}">{{ $d['tel'] }}</a></dd>
                    @foreach(Demandes::MULTIPLES as $cle => $titre)
                        @if(!empty($d[$cle]))<dt>{{ $titre }}</dt><dd>{{ implode(', ', $d[$cle]) }}</dd>@endif
                    @endforeach
                    @foreach(Demandes::UNIQUES as $cle => $titre)
                        @if($d[$cle])<dt>{{ $titre }}</dt><dd>{{ $d[$cle] }}</dd>@endif
                    @endforeach
                    @if($d['message'])<dt>Message</dt><dd class="message">{{ $d['message'] }}</dd>@endif
                    @if(!empty($d['documents']))<dt>Documents joints</dt><dd>{{ implode(', ', $d['documents']) }} <span style="color:#999">(envoyés par e-mail, non conservés ici)</span></dd>@endif
                </dl>
                <form method="POST" action="{{ route('admin.demandes.supprimer', $d['id']) }}" style="margin-top:16px"
                      onsubmit="return confirm('Supprimer définitivement cette demande ? Elle restera dans les boîtes e-mail.')">
                    @csrf
                    <button type="submit" class="bt bt-lien" style="padding:6px 0">Supprimer cette demande</button>
                </form>
            </div>
        </details>
    @endforeach
</div>

@endsection
