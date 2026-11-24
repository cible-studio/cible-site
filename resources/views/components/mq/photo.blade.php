{{-- Photo du catalogue de la maquette (config/marquette.php).
     <x-mq.photo cle="axe" sizes="(min-width: 960px) 46vw, 100vw" prioritaire />
     · prioritaire : image du premier écran (pas de lazy, fetchpriority haute).
     · largeur/hauteur intrinsèques : le navigateur réserve la place, pas de saut. --}}
@props(['cle', 'sizes' => '100vw', 'prioritaire' => false, 'alt' => null])
@php
    $p = \App\Support\Marquette::photo($cle);
    // ?capture=1 : chargement immédiat, pour les captures d'écran de contrôle
    // (Chrome sans interface ne déclenche pas toujours le chargement différé).
    $prioritaire = $prioritaire || request()->boolean('capture');
@endphp
<img src="{{ $p['src'] }}" srcset="{{ $p['srcset'] }}" sizes="{{ $sizes }}"
     width="{{ $p['width'] }}" height="{{ $p['height'] }}" alt="{{ $alt ?? $p['alt'] }}"
     @if($prioritaire) fetchpriority="high" @else loading="lazy" decoding="async" @endif
     {{ $attributes }}>
