<?php
// ═════ INCLUDES ═════
include_once('configs/config.cfg');

include_once('models/player.class.php');
include_once('models/gamemanager.class.php');

include_once('services/game-services.class.php');

include_once('views/game_ui.class.php');

include_once('controllers/game-controller.class.php');



// ═════ SESSION HANDLING ═════
session_start();

    if( !isset($_SESSION['authenticated']) ) { 
	session_destroy();
	header('Location: login.php');
	exit; }
#elseif( !isset($_SESSION['world']) ) {
#	header('Location: worlds.php');
#	exit; }
#else {
#	include_once('configs/'.$_SESSION['world'].'/config.cfg'); 
#	include_once('configs/'.$_SESSION['world'].'/alternate_names.cfg');
#}



// ═════ INITIALIZATION ═════
$_redirect = null;

$_message = ""; $_msg_class = "";

$_player = null;
	if( isset($_SESSION['player']) ) 		{$_player = $_SESSION['player'];}
	else 						{$_player = new Player( $_SESSION['authenticated']->ToArray() );}
$_gamemanager = null;
	if( isset($_SESSION['gamemanager']) ) 		{$_gamemanager = $_SESSION['gamemanager'];}


$_db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS);
$_game_svc = new GameServices($_db, $_player, $_gamemanager);

#$_game_ctrl = new GameController($_game_svc);



// ═════ ACCESS HANDLING ═════
    if( SITE_CLOSED || $_player->GetAccessLevel() < MINIMAL_ACCESS_REQUIRED ) { 
		  session_destroy();
		  header('Location: closed.php');
		  exit; }


// ═════ CONTROL ═════
	// GAME SERVICES MANAGEMENT ACTIONS
if( isset($_GET['IdPerso']) && $_GET['IdPerso'] ) {
	if( $_game_svc->SaveReadWorkCard($_GET['IdPerso']) ) {
		$_message = "Carte lue avec succès"; $_msg_class = "success";
	}
	else {
		$_message = "Lecture échouée"; $_msg_class = "error";
	}
}
else {
		$_message = "Aucune lecture"; $_msg_class = "error";	
}	



// ═════ DATA COLLECTION ═════

	// SESSION
$_SESSION['user_id'] = $_SESSION['authenticated']->GetID();
#$_SESSION['gamemanager'] = $_gamemanager;



// ═════ REDIRECTION ═════
$_db->CloseConnection();

if( isset($_redirect) ) { header('Location: ' . $_redirect); }



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


// ═════ MESSAGES ═════
echo '<DIV id="messages">';
	echo '<span class="' . $_msg_class . '">' . $_message . '</span>';
echo '</DIV>';



// ═════ MAIN ═════
echo '<DIV id="main">';

echo '</DIV>'; # Main

echo '</DIV>'; # Content


// ═════ FOOTER ═════
$footnote = "Pour tout besoin de support ou tout commentaires, contactez l'équipe informatique : <u>TI@Terres-de-Belenos.com</u> ";
require_once('includes/footer.php');

?>