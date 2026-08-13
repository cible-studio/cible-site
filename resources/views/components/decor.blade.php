{{-- ═══════════════════════════════════════════════════════════════════
     Décor ambiant — formes de marque flottantes (flèche / plume)

     ⚠ Ne PAS écrire de balise x-decor littérale dans ce commentaire :
       Blade compile les composants avant de retirer les commentaires,
       l'exemple serait donc compilé comme un vrai appel et le composant
       s'invoquerait lui-même (ParseError à la compilation).

     Usage — passer un tableau `formes`, chaque entrée valant
     [classe de forme, style inline] :

         formes="[
             ['f-plume',  '--c:var(--violet);--op:.10;top:8%;right:6%;width:150px'],
             ['f-fleche', '--c:var(--jaune);--op:.14;bottom:4%;left:2%;width:110px'],
         ]"

     Le style porte position, taille et variables d'animation :
        --c   couleur (palette ou valeur libre, ex. #fff sur fond sombre)
        --op  opacité finale       --r   rotation
        --dur durée de dérive      --del délai d'apparition
        --dx/--dy amplitude        --sx  -1 pour retourner la forme

     La section parente doit porter la classe `a-decor` : position
     relative, overflow masqué, et contenu remonté au-dessus du décor.
════════════════════════════════════════════════════════════════════ --}}
@props(['formes' => []])

<div class="couche" aria-hidden="true">
    @foreach($formes as $forme)
        <span class="fleche dessin {{ $forme[0] }}" style="{{ $forme[1] }}"></span>
    @endforeach
</div>
