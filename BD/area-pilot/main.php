<?php

include_once('configs/config.cfg');

// ═════ INCLUDES ═════
include_once('models/player.class.php');
include_once('models/repairbot.class.php');
include_once('models/custom.class.php');

include_once('services/repair-services.class.php');
include_once('services/custom-services.class.php');

include_once('views/menu_ui.class.php');
include_once('views/repair_ui.class.php');
include_once('views/custom_ui.class.php');

/*include_once('controllers/search-controller.class.php');*/



// ═════ INITIALIZATION ═════
session_start();

$_redirect = null;
$_menu_option = null;

$_update_linked_account = null;
$_update_incomplete_characters = null;
$_update_outdated_characters = null;
$_update_wounded_characters = null;
$_update_pass_list = null;

$_reset_search_data = False;

$_message = ""; $_msg_class = "";

$_player = null;
	if( isset($_SESSION['player']) ) 		{$_player = $_SESSION['player'];}
	elseif( isset($_SESSION['authenticated']) ) 	{$_player = new Player( $_SESSION['authenticated']->ToArray() );}
$_data = null;
	if( isset($_SESSION['data']) ) 			{$_data = $_SESSION['data']; }
	else 						{$_data = new Custom(); }
$_repairbot = null;
	if( isset($_SESSION['repairbot']) ) 		{$_repairbot = $_SESSION['repairbot']; }
	else 						{$_repairbot = new RepairBot(); }

$_db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS);
$_custom_svc = new CustomServices($_db, $_data);
$_repair_svc = new RepairServices($_db, $_repairbot);



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

		    if( $_POST['option'] == 'Nettoyage BD') 			{ $_menu_option = $_POST['option']; } 

		elseif( $_POST['option'] == 'Transfert activités') 		{ $_menu_option = $_POST['option']; $_update_linked_account = True; } 
		elseif( $_POST['option'] == 'Transfert XP')			{ $_menu_option = $_POST['option']; $_update_linked_account = True; } 
		elseif( $_POST['option'] == 'Restitution PV')			{ $_menu_option = $_POST['option']; $_update_wounded_characters = True; } 
		elseif( $_POST['option'] == 'XP Passe')				{ $_menu_option = $_POST['option']; $_update_pass_list = True; } 

		elseif( $_POST['option'] == 'Compétence-Ajout de masse')	{ $_menu_option = $_POST['option']; } 
		elseif( $_POST['option'] == 'Personnages-MAJ massive')		{ $_menu_option = $_POST['option']; $_update_outdated_characters = True; } 

		elseif( $_POST['option'] == 'Trouver ID')			{ $_menu_option = $_POST['option']; $_reset_search_data = True; } 
		elseif( $_POST['option'] == 'Réinitialiser compte')		{ $_menu_option = $_POST['option']; $_reset_search_data = True; } 
		elseif( $_POST['option'] == 'Changement classe')		{ $_menu_option = $_POST['option']; } 
		elseif( $_POST['option'] == 'Suppression personnage')		{ $_menu_option = $_POST['option']; } 
	}

	// By default, nav and menu options are persisted.
	else { $_menu_option = $_SESSION['menuoption']; }


	// **ACTION MANAGEMENT**
	    if( $_POST['action'] == 'manage-actions') { 

		    if( $_POST['submit'] == 'Nettoyage BD') 			{ $_repair_svc->CleanUpDatabase(); } 

		elseif( $_POST['submit'] == 'Activités') 			{ $_custom_svc->TransferActivitiesForLinkedAccounts(); $_update_linked_account = True; } 
		elseif( $_POST['submit'] == 'Transfert XP')			{ $_custom_svc->TransferAllExperienceForLinkedAccounts(); $_update_linked_account = True; } 
		elseif( $_POST['submit'] == 'PV')				{ $_custom_svc->HealWoundedCharacters(); $_update_wounded_characters = True; } 
		elseif( $_POST['submit'] == 'XP Passe')				{ $_custom_svc->GivePassXP(); } 

		elseif( $_POST['submit'] == 'Liste compétence manquante')	{ $_update_incomplete_characters = True; } 
		elseif( $_POST['submit'] == 'Ajout compétence manquante')	{ $_custom_svc->GiveSkillToClassMembers($_POST['skillcode']); $_update_incomplete_characters = True; } 
		elseif( $_POST['submit'] == 'Mise à jour personnages')		{ $_custom_svc->UpdateAllOutdatedCharacters(); $_update_outdated_characters = True; } 

		elseif( $_POST['submit'] == 'Recherche ID')			{ $_custom_svc->FindUserOrCharacter($_POST['searchsubject'], $_POST['searchstring']); } 
		elseif( $_POST['submit'] == 'Réinitialiser compte')		{ $_custom_svc->ResetAccount($_POST['accountid'], $_POST['password']); } 
		elseif( $_POST['submit'] == 'Classe personnages')		{ $_custom_svc->ChangeCharacterClass($_POST['characterid'], $_POST['classcode'], $_POST['archchoicecode']); } 
		elseif( $_POST['submit'] == 'Suppression personnages')		{ $_custom_svc->DeleteCharacter($_POST['characterid'], $_POST['dbapassword']); } 
	}


} // End ISSET(ACTION)



// ═════ DATA COLLECTION ═════

	// LOCAL
if ($_update_linked_account) { $_custom_svc->GetLinkedAccounts(); }
elseif ($_update_incomplete_characters) { if( $_POST['classcode'] == "" ) { $_custom_svc->GetIncompleteCharacters($_POST['skillcode']); } 
					  else { $_custom_svc->GetIncompleteCharacters($_POST['skillcode'], $_POST['classcode']); } }
elseif ($_update_outdated_characters) { $_custom_svc->GetOutdatedCharacters(); }
elseif ($_update_wounded_characters) { $_custom_svc->GetWoundedCharacters(); }
elseif ($_update_pass_list) { $_custom_svc->GetPassList(); }

if ($_reset_search_data) { $_custom_svc->ResetSearchData(); }

$_data = $_custom_svc->Data;
$_repairbot = $_repair_svc->Data;

	// SESSION
$_SESSION['player'] = $_player;
$_SESSION['data'] = $_data;
$_SESSION['repairbot'] = $_repairbot;
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
if( $_custom_svc->Error ) { $_msg_class = "error"; $_message = $_custom_svc->Error; }

echo '<DIV id="messages">';
	echo '<span class="' . $_msg_class . '">' . $_message . '</span>';
echo '</DIV>';



// ═════ MAIN ═════
$_custom_ui = new CustomUI( $_data );
$_repair_ui = new RepairUI( $_repairbot );

echo '<DIV id="main">';

echo '<DIV id="left-panel">';
	$_menu_ui->DisplayMenu();
echo '</DIV>';

echo '<DIV id="right-panel">';
	    if( !$_menu_option ) 					{ /* Display nothing*/}
	elseif( $_menu_option == 'Nettoyage BD' ) 			{ $_repair_ui->DisplayDatabaseCleanUpUI(); }

	elseif( $_menu_option == 'Transfert activités' ) 		{ $_custom_ui->DisplayActivityTransferUI(); }
	elseif( $_menu_option == 'Transfert XP' ) 			{ $_custom_ui->DisplayExperienceTransferUI(); }
	elseif( $_menu_option == 'Restitution PV' ) 			{ $_custom_ui->DisplayLifeRestorationUI(); }
	elseif( $_menu_option == 'XP Passe' ) 				{ $_custom_ui->DisplayPassXPGiveAwayUI(); }

	elseif( $_menu_option == 'Compétence-Ajout de masse' ) 		{ $_custom_ui->DisplayMassSkillGainUI(); }
	elseif( $_menu_option == 'Personnages-MAJ massive' ) 		{ $_custom_ui->DisplayMassCharacterUpdateUI(); }

	elseif( $_menu_option == 'Trouver ID' ) 			{ $_custom_ui->DisplayIDSearchUI(); }
	elseif( $_menu_option == 'Réinitialiser compte' )		{ $_custom_ui->DisplayAccountReactivationUI(); }
	elseif( $_menu_option == 'Changement classe' ) 			{ $_custom_ui->DisplayClassChangeUI(); }
	elseif( $_menu_option == 'Suppression personnage' ) 		{ $_custom_ui->DisplayCharacterDeletionUI(); }

	else { echo $_menu_option; } 

echo '</DIV>';

echo '</DIV>';



// ═════ FOOTER ═════
$footnote = "Pour tout besoin de support ou tout commentaires, contactez l'équipe informatique : <u>BD@Terres-de-Belenos.com</u> ";
require_once('includes/footer.php');

?>