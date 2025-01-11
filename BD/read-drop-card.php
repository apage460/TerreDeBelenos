<?php

// ═════ INCLUDES ═════
include_once('configs/config.cfg');

include_once('models/masterlist.class.php');
include_once('models/player.class.php');
include_once('models/gamemanager.class.php');

include_once('services/master-services.class.php');
include_once('services/game-services.class.php');

include_once('views/game_ui.class.php');

include_once('controllers/game-controller.class.php');



// ═════ SESSION HANDLING ═════
session_start();

    if( !isset($_SESSION['authenticated']) ) { 
	session_destroy();
	header('Location: login.php');
	exit; }
elseif( !isset($_SESSION['world']) ) {
	header('Location: worlds.php');
	exit; }
else {
	include_once('configs/'.$_SESSION['world'].'/config.cfg'); 
	include_once('configs/'.$_SESSION['world'].'/alternate_names.cfg');
}



// ═════ INITIALIZATION ═════
$_redirect = null;

$_message = ""; $_msg_class = "";

$_player = null;
	if( isset($_SESSION['player']) ) 		{$_player = $_SESSION['player'];}
	else 						{$_player = new Player( $_SESSION['authenticated']->ToArray() );}
$_masterlist = null;
	if( isset($_SESSION['masterlist']) ) 		{$_masterlist = $_SESSION['masterlist'];}
$_gamemanager = null;
	if( isset($_SESSION['gamemanager']) ) 		{$_gamemanager = $_SESSION['gamemanager'];}


$_db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS);
$_master_svc = new MasterServices($_db, $_masterlist);
$_game_svc = new GameServices($_db, $_player, $_gamemanager);
$_game_ctrl = new GameController($_game_svc);



// ═════ ACCESS HANDLING ═════
    if( SITE_CLOSED || $_player->GetAccessLevel() < 6 ) { 
		  session_destroy();
		  header('Location: closed.php');
		  exit; }


// ═════ CONTROL ═════
	// GAME SERVICES MANAGEMENT ACTIONS
	
if( isset($_GET['IdPerso']) && $_GET['IdPerso'] ) {
	$_message = "Carte lue avec succès. Veuillez remplir le formulaire et cliquer sur Enregistrer."; $_msg_class = "success";
} else {
	$_message = "Aucune lecture. Veuillez fournir l'identifiant du personnage manuellement."; $_msg_class = "error";
}	


// Actions
if( isset($_POST['action']) ) {
	if( $_POST['action'] == 'Enregistrer objets') {
		foreach ($_POST['itemquantity'] as $code => $value) {
			if( $value > 0 ) { 
				$_game_svc->SaveCharacterItemEntry($_GET['IdPerso'], $_POST['activityid'], $code, $value, $_POST['itemprecision'][$code], $_POST['itemnotes'][$code]);
				$_message = "Enregistrement fait avec succès!"; 
				$_msg_class = "success"; 
			}
		}
	}
	elseif( $_POST['action'] == 'Supprimer objets') {
		$_game_svc->DeleteCharacterItemEntry($_POST['itemid']);
		$_message = "Suppression effectuée avec succès!"; 
		$_msg_class = "success"; 
	}
}


// ═════ DATA COLLECTION ═════

	// Master List
if (!$_masterlist) {
	$_masterlist = new MasterList();
}
$_master_svc->SetMasterList($_masterlist);
$_master_svc->Build();
$_masterlist = $_master_svc->GetMasterList();

	// Game services
if(!$_gamemanager && $_msg_class == "success") {
	$_gamemanager = new GameManager();
	$_game_svc->SetManager($_gamemanager);
	$_game_svc->Update();
	$_game_svc->GetCharacterItemList($_GET['IdPerso']);
	$_gamemanager = $_game_svc->GetManager();
}
elseif($_msg_class == "success") {
	$_game_svc->Update();
	$_game_svc->GetCharacterItemList($_GET['IdPerso']);
	$_gamemanager = $_game_svc->GetManager();
}


	// SESSION
$_SESSION['user_id'] = $_SESSION['authenticated']->GetID();
$_SESSION['gamemanager'] = $_gamemanager;
$_SESSION['masterlist'] = $_masterlist;



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
if(WORLD == 'BELE'){
	echo '<div class="logo"><img src="images/logo_belenos.png" height="150" width="100" alt="Terres de Bélénos" /></div>';
	echo '<div class="main-title">Les Terres de Bélénos</div>';
}
elseif(WORLD == 'BELEJR'){
	echo '<div class="logo"><img src="images/logo_belejunior.png" height="150" width="112" alt="Terres de Bélénos" /></div>';
	echo '<div class="main-title-BJ">Bélé-Junior</div>';
	echo '<div class="logo"><img src="images/banniere_belenos.png" height="75" width="50" alt="Terres de Bélénos" /></div>';
}
elseif(WORLD == 'TERNOC'){
	echo '<div class="logo"><img src="images/logo_terreur_nocturne.png" height="100" width="100" alt="Terreur nocturne" /></div>';
	echo '<div class="main-title-TN">Terreur nocturne</div>';
	echo '<div class="logo"><img src="images/banniere_belenos.png" height="75" width="50" alt="Terres de Bélénos" /></div>';
}
else {
	echo '<div class="logo"><img src="images/logo_belenos.png" height="150" width="100" alt="Terres de Bélénos" /></div>';
	echo '<div class="main-title">Base de données bélénoise</div>';
}
echo '</DIV>';

echo '<DIV id="content">';


// ═════ NAVIGATION ═════


// ═════ MESSAGES ═════
echo '<DIV id="messages">';
	echo '<span class="' . $_msg_class . '">' . $_message . '</span>';
echo '</DIV>';



// ═════ MAIN ═════
$_game_ui = new GameUI( $_gamemanager );

echo '<DIV id="main">';

echo '<DIV id="left-panel">';
	if($_SERVER['HTTP_HOST'] == 'localhost') {
		$UTC = new DateTime();
		$EST = new DateTime("now", new DateTimeZone("America/Montreal"));
		echo 'NOW (UTC) : '.$UTC->format('d-m-Y, H:i:s').'<br/>';
		echo 'NOW (EST) : '.$EST->format('d-m-Y, H:i:s').'<br/>';
		echo '<div style="width: 100%; overflow: auto;"><pre>POSTS : ';
		print_r($_SESSION['authenticated']);
		print_r($_POST);
		echo '</pre></div>';
	}
echo '</DIV>';

echo '<DIV id="right-panel">';
if($_msg_class == "success") {
	$_game_ui->DisplayCharacterItemDropForm();
}

echo '</DIV>'; # Right panel

echo '</DIV>'; # Main

echo '</DIV>'; # Content


// ═════ FOOTER ═════
$footnote = "Pour tout besoin de support ou tout commentaires, contactez l'équipe informatique : <u>TI@Terres-de-Belenos.com</u> ";
require_once('includes/footer.php');

?>