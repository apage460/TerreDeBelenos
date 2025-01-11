<?php

include_once('configs/config.cfg');

// ═════ INITIALIZATION ═════
session_start();



// ═════ CONTROL ═════
if( defined('WORLD') ) { header('Location: index.php'); exit; }

elseif( isset($_POST['world-choice']) && $_POST['world-choice'] ) {
	$_SESSION['world'] = $_POST['world-choice'];
	include_once('configs/'.$_SESSION['world'].'/config.cfg'); 
	include_once('configs/'.$_SESSION['world'].'/alternate_names.cfg');

	header('Location: index.php'); exit;
}



// ═════ HEADER ═════
require_once('includes/header.php');



// ═════ BODY ═════
echo '<body>';

# Interface wrapper
echo '<DIV id="wrapper">';



// ═════ TITLE ═════
echo '<DIV id="header">';
 	echo '<div class="logo"><img src="images/logo_belenos.png" height="150" width="100" alt="Terres de Bélénos" /></div>';
	echo '<div class="main-title">Base de données bélénoise</div>';
echo '</DIV>';

echo '<DIV id="content">';



// ═════ NAVIGATION ═════



// ═════ MAIN ═════
echo '<DIV id="main">';

echo '<span class="instructions">Pour quel GN voulez-vous gérer votre compte ? </span>';

echo '<table class="logo-list">';
echo '<tr>';
	echo '<td class="logo-list"><form method="post">';
		echo '<input type="hidden" name="world-choice" value="BELE" />';
		echo '<input type="image" src="images/banniere_belenos.png" alt="Bélénos" width="133px" height="225px" style="padding:0px 46px;" />';
	echo '</form></td>';
	echo '<td class="logo-list"><form method="post">';
		echo '<input type="hidden" name="world-choice" value="BELEJR" />';
		echo '<input type="image" src="images/banniere_belejunior.png" alt="Bélénos" width="225px" height="185px" style="padding:20px 0px;" />';
	echo '</form></td>';
	echo '<td class="logo-list"><form method="post">';
		echo '<input type="hidden" name="world-choice" value="TERNOC" />';
		echo '<input type="image" src="images/banniere_terreur_nocturne.png" alt="Terreur nocturne" width="225px" height="225px" style="" />';
	echo '</form></td>';
echo '</tr>';
echo '</table>';

echo '</DIV>'; # Main
echo '</div>'; # Content


// ═════ FOOTER ═════
echo '</div>'; # Wrapper
echo '</body>';
echo '</html>';

?>