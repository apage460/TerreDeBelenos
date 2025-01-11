<?php

include_once('configs/config.cfg');

// ═════ HEADER ═════
require_once('includes/header.php');


// ═════ MAIN ═════
echo '<DIV id="main">';

echo '<span class="section-title">Vous n\'avez pas l\'âge requise pour utiliser les fonctionnalités de ce site.</span>';

echo '<hr width=50%>';

echo '<div>';
echo '<span>L\'âge minimal pour avoir des personnages et gérer ses informations personnelles en lien avec l\'activité choisie (Bélénos, Bélé Jr, Terreur nocture, etc.) est de '.AGE_MIN.' an(s). En-dessous de cet âge, vos parents ou votre tuteur désigné sont responsable de votre inscription et celle-ci doit être incluse dans leur propre inscription. Si aucun champs de saisie « Enfants » n\'est inclut dans le formulaire, c\'est que l\'activité en question n\'est pas conçue pour les accueillir.<br/>
	<br/> 
Notez que bien que votre compte bélénois fonctionne pour toutes les activités auxquelles <i>Les Terres de Bélénos</i> s\'associe directement (excluant donc les locations du terrain), il existe un site « Base de données... » pour chaque univers de jeu. Par conséquent, vérifiez d\'abord que vous êtes au bon endroit. Les sites ont tous une adresse « Terres-de-Belenos.com/[SITE]/ » et vous trouverez donc le site de Bélénos au « /BD/ », celui de Bélé Junior au « /BELEJR/ » et celui de Terreur nocturne au « /TN/ ».</span>';
echo '</div>';

echo '</DIV>';



// ═════ FOOTER ═════
echo '</div>';

echo '</div>';
echo '</body>';
echo '</html>';

?>