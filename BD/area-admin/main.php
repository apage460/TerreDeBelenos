<?php

include_once('configs/config.cfg');

// ═════ INCLUDES ═════
include_once('models/player.class.php');
include_once('models/custom.class.php');

include_once('services/custom-services.class.php');

include_once('views/menu_ui.class.php');
include_once('views/custom_ui.class.php');

/*include_once('controllers/search-controller.class.php');*/



// ═════ INITIALIZATION ═════
session_start();

$_redirect = null;
$_menu_option = null;

$_update_activities = null;
$_update_passes = null;

$_message = ""; $_msg_class = "";

$_player = null;
	if( isset($_SESSION['player']) ) 		{$_player = $_SESSION['player'];}
	elseif( isset($_SESSION['authenticated']) ) 	{$_player = new Player( $_SESSION['authenticated']->ToArray() );}
$_data = null;
	if( isset($_SESSION['data']) ) 			{$_data = $_SESSION['data']; }
	else 						{$_data = new Custom(); }

$_db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS);
$_custom_svc = new CustomServices($_db, $_data);



// ═════ SESSION HANDLING ═════
if (isset($_SESSION['last_time_renewed']) && (time() - $_SESSION['last_time_renewed'] > SESSION_TIMEOUT)) {
	$_POST = array();
	session_unset();
	$_redirect = "../login.php";
}
else if( !isset($_SESSION['authenticated']) ) {$_redirect = "../login.php";}
else {	$_SESSION['last_time_renewed'] = time(); }



// ═════ CONTROL ═════
if( isset($_POST['action']) ) {

	// **MENU MANAGEMENT**
	if( $_POST['action'] == 'select-nav-option') { 
		if( $_POST['option'] == 'Retour') 			{ $_redirect = "../index.php"; }
	}

	elseif( $_POST['action'] == 'select-menu-option') { 

		    if( $_POST['option'] == 'Présences activités') 		{ $_menu_option = $_POST['option']; $_update_activities = True; } 
		elseif( $_POST['option'] == 'Passes')				{ $_menu_option = $_POST['option']; $_update_passes = True; } 
	}

	// By default, nav and menu options are persisted.
	else { $_menu_option = $_SESSION['menuoption']; }


	// **ACTION MANAGEMENT**
	    if( $_POST['action'] == 'update-data') { 

		    if( $_POST['submit'] == 'Activités') 		{ $_custom_svc->GetActivityData(); } 
		elseif( $_POST['submit'] == 'Passes')			{ $_custom_svc->GetPassData(); } 
	}


} // End ISSET(ACTION)



// ═════ DATA COLLECTION ═════

	// Custom Data
if ($_update_activities) { $_custom_svc->GetActivityData(); }
elseif ($_update_passes) { $_custom_svc->GetPassData(); }
$_data = $_custom_svc->Data;

	// SESSION
$_SESSION['player'] = $_player;
$_SESSION['data'] = $_data;
$_SESSION['menuoption'] = $_menu_option;



// ═════ REDIRECTION ═════
$_db->CloseConnection();

if( isset($_redirect) ) { header('Location: ' . $_redirect); }



// ═════ HEADER ═════
require_once('includes/header.php');



// ═════ NAVIGATION ═════
$_menu_ui = new MenuUI( $_player );

echo '<DIV id="navigation">';
	$_menu_ui->DisplayNavMenu();
echo '</DIV>';



// ═════ MESSAGES ═════
echo '<DIV id="messages">';
	echo '<span class="' . $_msg_class . '">' . $_message . '</span>';
echo '</DIV>';



// ═════ MAIN ═════
$_custom_ui = new CustomUI( $_data );

echo '<DIV id="main">';

echo '<DIV id="left-panel">';
	$_menu_ui->DisplayMenu();
echo '</DIV>';

echo '<DIV id="right-panel">';
	    if( !$_menu_option ) {}
	elseif( $_menu_option == 'Présences activités' ) 		{ $_custom_ui->DisplayActivityData(); }
	elseif( $_menu_option == 'Passes' ) 				{ $_custom_ui->DisplayPassData(); }

	else { echo $_menu_option; } 

echo '</DIV>';

echo '</DIV>';



// ═════ FOOTER ═════
$footnote = "Ce site est actuellement en développement. Pour tout besoin de support ou tout commentaires, contactez l'équipe informatique : <u>BD@Terres-de-Belenos.com</u> ";
require_once('includes/footer.php');

?>