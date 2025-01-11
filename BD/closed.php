<?php

// ═════ INITIALIZATION ═════
session_start();
session_destroy();
session_unset();
session_start();


// ═════ HEADER ═════
require_once('includes/header.php');


// ═════ MAIN ═════
echo '<DIV id="main">';

echo '<span class="section-title">La Base de données est présentement innaccessible</span>';

echo '<hr width=50%>';

echo '<div>';
echo '<span>Une mise à jour ou la correction d\'une erreur est en cours et nous oblige présentement à restreindre l\'accès au site. Nous vous remercions pour votre patience et vous tiendrons informés via la page Facebook « Annonces et quetions » des Terres de Bélénos.</span>';
echo '</div>';

echo '</DIV>';



// ═════ FOOTER ═════
echo '</div>';

echo '</div>';
echo '</body>';
echo '</html>';

?>