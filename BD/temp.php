<?php

include_once('configs/config.cfg');

// ═════ INCLUDES ═════
session_start();



// ═════ INITIALIZATION ═════
$_title = "Sans titre";

if( isset($_GET['title']) ) {$_title = $_GET['title'];}



// ═════ CONTROL ═════



// ═════ SESSION HANDLING ═════



// ═════ HEADER ═════
require_once('includes/header.php');



// ═════ NAVIGATION ═════
echo '<DIV id="navigation">';
echo '<a href="index.php">Retour</a><br />';
echo '</DIV>';
echo '<br />';



// ═════ MAIN ═════
echo '<DIV id="main">';

echo 'Titre de la page temporaire : '.$_title.'<br />';

echo '</DIV>';



// ═════ FOOTER ═════
$footnote = "Vous êtes dans la page temporaire servant temporairement de lien temporaire pour retourner au comportement non terminé d\'une page, elle, non-temporaire.";
require_once('includes/footer.php');

?>