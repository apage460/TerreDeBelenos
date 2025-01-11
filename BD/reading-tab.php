<?php

include_once('configs/config.cfg');
if( isset($_POST['world']) ) { include_once('configs/'.$_POST['world'].'/config.cfg'); }

// ═════ HEADER ═════
require_once('includes/header.php');



// ═════ BODY ═════
echo '<body>';

# Interface wrapper
echo '<DIV id="wrapper">';



// ═════ WORLD TITLE ═════
echo '<DIV id="header">';
if( isset($_POST['world']) && $_POST['world'] == 'BELE' ){
	echo '<div class="logo"><img src="images/logo_belenos.png" height="150" width="100" alt="Terres de Bélénos" /></div>';
	echo '<div class="main-title">Les Terres de Bélénos</div>';
}
elseif( isset($_POST['world']) && $_POST['world'] == 'BELEJR' ){
	echo '<div class="logo"><img src="images/logo_belejunior.png" height="150" width="112" alt="Terres de Bélénos" /></div>';
	echo '<div class="main-title-BJ">Bélé-Junior</div>';
	echo '<div class="logo"><img src="images/banniere_belenos.png" height="75" width="50" alt="Terres de Bélénos" /></div>';
}
elseif( isset($_POST['world']) && $_POST['world'] == 'TERNOC' ){
	echo '<div class="logo"><img src="images/logo_terreur_nocturne.png" height="100" width="100" alt="Terreur nocturne" /></div>';
	echo '<div class="main-title-TN">Terreur nocturne</div>';
	echo '<div class="logo"><img src="images/banniere_belenos.png" height="75" width="50" alt="Terres de Bélénos" /></div>';
}
else {
	echo '<div class="logo"><img src="images/logo_belenos.png" height="150" width="100" alt="Terres de Bélénos" /></div>';
	echo '<div class="main-title">Base de données bélénoise</div>';
}
echo '</DIV>'; #header



// ═════ MAIN ═════
echo '<DIV id="main">';

echo '<span class="section-title">'.$_POST['title'].'</span>';

echo '<hr width=70%>';

echo '<div>';
	$lText = nl2br( base64_decode($_POST['text']) );
	echo '<span class="note" style="text-align: left;">'.$lText.'</span>';
echo '</div>';

echo '</DIV>'; #main



// ═════ FOOTER ═════
#No footer for this page

echo '</DIV>'; #wrapper
echo '</body>';
echo '</html>';

?>