<?php

include_once('configs/config.cfg');

// ═════ INCLUDES ═════
include_once('models/player.class.php');
include_once('models/questmanager.class.php');
include_once('models/statistics.class.php');

include_once('services/search-services.class.php');
include_once('services/quest-services.class.php');
include_once('services/statistics-services.class.php');

include_once('views/menu_ui.class.php');
include_once('views/quest_ui.class.php');
include_once('views/statistics_ui.class.php');

include_once('controllers/search-controller.class.php');
include_once('controllers/quest-controller.class.php');



// ═════ INITIALIZATION ═════
session_start();

$_redirect = null;
$_nav_option = null;
$_menu_option = null;
$_update_quests = False;
#$_update_statistics = False;

$_message = ""; $_msg_class = "";

$_player = null;
	if( isset($_SESSION['player']) ) 		{$_player = $_SESSION['player'];}
	elseif( isset($_SESSION['authenticated']) ) 	{$_player = new Player( $_SESSION['authenticated']->ToArray() );}
$_quests = null;
	if( isset($_SESSION['quests']) ) 		{$_quests = $_SESSION['quests'];}
#$_statistics = null;
#	if( isset($_SESSION['statistics']) ) 		{$_statistics = $_SESSION['statistics'];}

$_db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS);
$_search_svc = new SearchServices($_db);
$_quests_svc = new QuestServices($_db, $_player, $_quests);

$_search_ctrl = new SearchController($_search_svc);
$_quest_ctrl = new QuestController($_quests_svc);



// ═════ SESSION HANDLING ═════
# Disabling session timeout
/*if (isset($_SESSION['last_time_renewed']) && (time() - $_SESSION['last_time_renewed'] > SESSION_TIMEOUT)) {
	$_POST = array();
	session_unset();
	$_redirect = "../login.php";
}
else*/ if( !isset($_SESSION['authenticated']) ) {$_redirect = "../login.php";}
else {	$_SESSION['last_time_renewed'] = time(); }



// ═════ CONTROL ═════
if( isset($_POST['action']) ) {

	// **MENU MANAGEMENT**
	// NAV MENU
	if( $_POST['action'] == 'select-nav-option') { 
		    if( $_POST['option'] == 'Quêtes personnelles') 	{ $_nav_option = $_POST['option']; $_menu_option = null; }
		elseif( $_POST['option'] == 'Quêtes de groupe')		{ $_nav_option = $_POST['option']; $_menu_option = null; }
		elseif( $_POST['option'] == 'Quêtes mythiques')		{ $_nav_option = $_POST['option']; $_menu_option = null; }
		elseif( $_POST['option'] == 'Responsable des quêtes')	{ $_nav_option = $_POST['option']; $_menu_option = null; }

		elseif( $_POST['option'] == 'Pilotage Quêtes')		{ $_nav_option = $_POST['option']; $_menu_option = null; }

		elseif( $_POST['option'] == 'Retour') 		{ $_redirect = "../index.php"; }
	}

	// OPTION MENU
	elseif( $_POST['action'] == 'select-menu-option') { 
		// Personal quest options
		    if( $_POST['option'] == 'Mes quêtes personnelles')			{ $_nav_option = "Quêtes personnelles"; $_menu_option = $_POST['option']; $_update_quests = True; }
		elseif( $_POST['option'] == 'Quêtes personnelles non assignées') 	{ $_nav_option = "Quêtes personnelles"; $_menu_option = $_POST['option']; $_update_quests = True; }
		elseif( $_POST['option'] == 'Quêtes personnelles actives') 		{ $_nav_option = "Quêtes personnelles"; $_menu_option = $_POST['option']; $_update_quests = True; }
			elseif( $_POST['option'] == 'Détail quête personnelle')			{ $_nav_option = "Quêtes personnelles"; $_menu_option = $_POST['option']; }
			elseif( $_POST['option'] == 'Rédaction quête personnelle')		{ $_nav_option = "Quêtes personnelles"; $_menu_option = $_POST['option']; }
			elseif( $_POST['option'] == 'Résumés personnels Scripteurs')		{ $_nav_option = "Quêtes personnelles"; $_menu_option = $_POST['option']; }
				elseif( $_POST['option'] == 'Détail résumé personnel Scripteurs')	{ $_nav_option = "Quêtes personnelles"; $_menu_option = $_POST['option']; }
			elseif( $_POST['option'] == 'Personnage Scripteurs')			{ $_nav_option = "Quêtes personnelles"; $_menu_option = $_POST['option']; }
		elseif( $_POST['option'] == 'Exemples quête personnelle') 		{ $_nav_option = "Quêtes personnelles"; $_menu_option = $_POST['option']; $_update_quests = True; }

		// Group quest options
		elseif( $_POST['option'] == 'Mes quêtes de groupe')			{ $_nav_option = "Quêtes de groupe"; $_menu_option = $_POST['option']; $_update_quests = True; }
		elseif( $_POST['option'] == 'Quêtes de groupe actives') 		{ $_nav_option = "Quêtes de groupe"; $_menu_option = $_POST['option']; $_update_quests = True; }
		elseif( $_POST['option'] == 'Exemples quête de groupe') 		{ $_nav_option = "Quêtes de groupe"; $_menu_option = $_POST['option']; $_update_quests = True; }
			elseif( $_POST['option'] == 'Détail quête de groupe')			{ $_nav_option = "Quêtes de groupe"; $_menu_option = $_POST['option']; }
			elseif( $_POST['option'] == 'Rédaction quête de groupe')		{ $_nav_option = "Quêtes de groupe"; $_menu_option = $_POST['option']; }
			elseif( $_POST['option'] == 'Résumés de groupe')			{ $_nav_option = "Quêtes de groupe"; $_menu_option = $_POST['option']; }
			elseif( $_POST['option'] == 'Groupe')					{ $_nav_option = "Quêtes de groupe"; $_menu_option = $_POST['option']; }
				elseif( $_POST['option'] == 'Histoire groupe')			{ $_nav_option = "Quêtes de groupe"; $_menu_option = $_POST['option']; }

		// Mythic quest option
		elseif( $_POST['option'] == 'Liste quêtes mythiques')			{ $_nav_option = "Quêtes mythiques"; $_menu_option = $_POST['option']; $_update_quests = True; }
		elseif( $_POST['option'] == 'Exemples quête mythique') 			{ $_nav_option = "Quêtes mythiques"; $_menu_option = $_POST['option']; $_update_quests = True; }
			elseif( $_POST['option'] == 'Détail quête mythique')			{ $_nav_option = "Quêtes mythiques"; $_menu_option = $_POST['option']; }
			elseif( $_POST['option'] == 'Parties quête mythique')			{ $_nav_option = "Quêtes mythiques"; $_menu_option = $_POST['option']; }

		// Managing options
		elseif( $_POST['option'] == 'Demandes') 				{ $_nav_option = "Responsable des quêtes"; $_menu_option = $_POST['option']; $_update_quests = True; }
		elseif( $_POST['option'] == 'Reprises') 				{ $_nav_option = "Responsable des quêtes"; $_menu_option = $_POST['option']; $_update_quests = True; }
		elseif( $_POST['option'] == 'Suites') 					{ $_nav_option = "Responsable des quêtes"; $_menu_option = $_POST['option']; $_update_quests = True; }
		elseif( $_POST['option'] == 'Quêtes actives') 				{ $_nav_option = "Responsable des quêtes"; $_menu_option = $_POST['option']; $_update_quests = True; }
			elseif( $_POST['option'] == 'Gérer quête personnelle' ) 		{ $_nav_option = "Responsable des quêtes"; $_menu_option = $_POST['option']; $_update_quests = True; }
			elseif( $_POST['option'] == 'Gérer rédaction quête personnelle' ) 	{ $_nav_option = "Responsable des quêtes"; $_menu_option = $_POST['option']; $_update_quests = True; }
			elseif( $_POST['option'] == 'Résumés personnels Responsable')		{ $_nav_option = "Responsable des quêtes"; $_menu_option = $_POST['option']; }
				elseif( $_POST['option'] == 'Détail résumé personnel Responable')	{ $_nav_option = "Quêtes personnelles"; $_menu_option = $_POST['option']; }
			elseif( $_POST['option'] == 'Personnage Responsable')			{ $_nav_option = "Responsable des quêtes"; $_menu_option = $_POST['option']; }
				elseif( $_POST['option'] == 'Histoire personnage')			{ $_nav_option = "Responsable des quêtes"; $_menu_option = $_POST['option']; }
				elseif( $_POST['option'] == 'Compétences personnage')			{ $_nav_option = "Responsable des quêtes"; $_menu_option = $_POST['option']; }

			elseif( $_POST['option'] == 'Gérer quête de groupe' ) 			{ $_nav_option = "Responsable des quêtes"; $_menu_option = $_POST['option']; $_update_quests = True; }
			elseif( $_POST['option'] == 'Gérer rédaction quête de groupe' ) 	{ $_nav_option = "Responsable des quêtes"; $_menu_option = $_POST['option']; $_update_quests = True; }
			elseif( $_POST['option'] == 'Résumés de groupe Responsable' )		{ $_nav_option = "Responsable des quêtes"; $_menu_option = $_POST['option']; }
				elseif( $_POST['option'] == 'Détail résumé de groupe Responable')	{ $_nav_option = "Quêtes de groupe"; $_menu_option = $_POST['option']; }
			elseif( $_POST['option'] == 'Groupe Responsable')			{ $_nav_option = "Responsable des quêtes"; $_menu_option = $_POST['option']; }
		elseif( $_POST['option'] == 'Rechercher quêtes personnelles')		{ $_nav_option = "Responsable des quêtes"; $_menu_option = $_POST['option']; $_update_quests = True; }
		elseif( $_POST['option'] == 'Rechercher quêtes groupe')			{ $_nav_option = "Responsable des quêtes"; $_menu_option = $_POST['option']; $_update_quests = True; }

		elseif( $_POST['option'] == 'Valider parties')				{ $_nav_option = "Responsable des quêtes"; $_menu_option = $_POST['option']; $_update_quests = True; }
		elseif( $_POST['option'] == 'Imprimer quêtes')				{ $_nav_option = "Responsable des quêtes"; $_menu_option = $_POST['option']; $_update_quests = True; }

		elseif( $_POST['option'] == 'Récompenses')				{ $_nav_option = "Responsable des quêtes"; $_menu_option = $_POST['option']; $_update_quests = True; }
		elseif( $_POST['option'] == 'Titres de prestige')			{ $_nav_option = "Responsable des quêtes"; $_menu_option = $_POST['option']; $_update_quests = True; }

	}

	// By default, nav and menu options are persisted.
	else { $_nav_option = $_SESSION['navoption']; $_menu_option = $_SESSION['menuoption']; }


	// **ACTION MANAGEMENT**
	// SEARCH ACTIONS
	    if( $_POST['action'] == 'search') { 
		    if( $_POST['submit'] == 'Joueurs' )			{ if( $_search_ctrl->SearchForPlayers() ) { /* POST['search_results'] is set */ }
								  	  else { $_message = "La recherche n'a produit aucun résultat!"; $_msg_class = "error"; } }
		elseif( $_POST['submit'] == 'Personnages' )		{ if( $_search_ctrl->SearchForCharacters() ) { /* POST['search_results'] is set */ }
								  	  else { $_message = "La recherche n'a produit aucun résultat!"; $_msg_class = "error"; } }
		elseif( $_POST['submit'] == 'Groupes' )			{ if( $_search_ctrl->SearchForGroups() ) { /* POST['search_results'] is set */ }
								  	  else { $_message = "La recherche n'a produit aucun résultat!"; $_msg_class = "error"; } }
		elseif( $_POST['submit'] == 'Quêtes' )			{ if( $_search_ctrl->SearchForQuests() ) { /* POST['search_results'] is set */ }
								  	  else { $_message = "La recherche n'a produit aucun résultat!"; $_msg_class = "error"; } }
	}

	// QUEST MANAGEMENT ACTIONS
	elseif( $_POST['action'] == 'manage-quest') { 
		    if( $_POST['submit'] == 'Modifier quête' )		{ if( $_quest_ctrl->UpdateQuest() ) { $_message = "La mise à jour réussie!"; $_msg_class = "success"; $_update_quests = True; }
								  	  else { $_message = $_quest_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['submit'] == 'Modifier rédaction' )	{ if( $_quest_ctrl->UpdateQuestTexts() ) { $_message = "La mise à jour réussie!"; $_msg_class = "success"; $_update_quests = True; }
								  	  else { $_message = $_quest_ctrl->Error; $_msg_class = "error"; } }
	}
	elseif( $_POST['action'] == 'manage-quest-lists') { 
		    if( isset($_POST['select-personal-quest']) )	{ $_quest_ctrl->SelectQuest('PERSONAL', $_POST['select-personal-quest']); $_menu_option = 'Détail quête personnelle'; $_update_quests = True; }
		elseif( isset($_POST['select-group-quest']) )		{ $_quest_ctrl->SelectQuest('GROUP', $_POST['select-group-quest']); $_menu_option = 'Détail quête de groupe'; $_update_quests = True; }
		elseif( isset($_POST['select-personal-resume']) && 
			$_nav_option == 'Quêtes personnelles' )		{ $_menu_option = 'Détail résumé personnel Scripteurs'; }
		elseif( isset($_POST['select-personal-resume']) && 
			$_nav_option == 'Responsable des quêtes' )	{ $_menu_option = 'Détail résumé personnel Responsable'; }
		elseif( isset($_POST['select-group-resume']) && 
			$_nav_option == 'Quêtes personnelles' )		{ $_menu_option = 'Détail résumé de groupe Scripteurs'; }
		elseif( isset($_POST['select-group-resume']) && 
			$_nav_option == 'Responsable des quêtes' )	{ $_menu_option = 'Détail résumé de groupe Responsable'; }
		elseif( isset($_POST['manage-personal-quest']) )	{ $_quest_ctrl->SelectQuest('PERSONAL', $_POST['manage-personal-quest']); $_menu_option = 'Gérer quête personnelle'; $_update_quests = True; }
		elseif( isset($_POST['manage-group-quest']) )		{ $_quest_ctrl->SelectQuest('GROUP', $_POST['manage-group-quest']); $_menu_option = 'Gérer quête de groupe'; $_update_quests = True; }
		elseif( isset($_POST['give-personal-reward']) )		{ $_quests->SelectQuest('PERSONAL', $_POST['give-personal-reward']);
									  if( $_quest_ctrl->GivePersonalQuestReward() ) { $_message = "La récompense a été correctement enregistrée!"; $_msg_class = "success"; $_update_quests = True; }
								  	  else { $_message = $_quest_ctrl->Error; $_msg_class = "error"; } }
	}

} // End ISSET(ACTION)



// ═════ DATA COLLECTION ═════

	// Quest
if(!$_quests) {
	$_quests = new QuestManager();
	$_quests_svc->SetManager($_quests);
	$_quests_svc->GetAllQuestLists();
	$_quests_svc->GetScriptorList();
	$_quests_svc->GetTitleList();
	$_quests = $_quests_svc->GetManager();
}
elseif($_update_quests) {
	$_quests_svc->GetAllQuestLists();
	if( $_quests->GetSelectedQuest() ) { 
		$type = $_quests->GetSelectedQuest()->GetType();
		$questid = $_quests->GetSelectedQuest()->GetID();
		$_quest_ctrl->SelectQuest($type, $questid); # Update logic if necessary
	}
	$_quests_svc->GetScriptorList();
	$_quests_svc->GetTitleList();
	$_quests = $_quests_svc->GetManager();
}

	// SESSION
$_SESSION['player'] = $_player;
$_SESSION['quests'] = $_quests;
#$_SESSION['statistics'] = $_statistics;
$_SESSION['navoption'] = $_nav_option;
$_SESSION['menuoption'] = $_menu_option;



// ═════ REDIRECTION ═════
$_db->CloseConnection();

if( isset($_redirect) ) { header('Location: ' . $_redirect); }



// ═════ HEADER ═════
require_once('includes/header.php');



// ═════ NAVIGATION ═════
$_menu_ui = new MenuUI( $_player );

echo '<DIV id="navigation-menu">';
	$_menu_ui->DisplayNavMenu();
echo '</DIV>';
echo '<DIV id="navigation-underlay">';
	$_menu_ui->DisplayNavUnderlay();
echo '</DIV>';



// ═════ MESSAGES ═════
echo '<DIV id="messages">';
	echo '<span class="' . $_msg_class . '">' . $_message . '</span>';
echo '</DIV>';



// ═════ MAIN ═════
$_quest_ui = new QuestUI( $_quests );
#$_statistics_ui = new StatisticsUI( $_statistics );

echo '<DIV id="main">';

echo '<DIV id="left-panel">';
	$_menu_ui->DisplayMenu($_nav_option);
echo '</DIV>';

echo '<DIV id="right-panel">';
	    if( !$_menu_option ) {}

	// Personal Quests views
	elseif( $_menu_option == 'Mes quêtes personnelles' ) 			{ $_quest_ui->DisplayMyPersonalQuests(); }
	elseif( $_menu_option == 'Quêtes personnelles non assignées' ) 		{ $_quest_ui->DisplayUnassignedPersonalQuests(); }
	elseif( $_menu_option == 'Quêtes personnelles actives' ) 		{ $_quest_ui->DisplayActivePersonalQuests(); }
		elseif( $_menu_option == 'Détail quête personnelle' ) 			{ $_quest_ui->DisplaySelectedPersonalQuest('QUEST'); }
		elseif( $_menu_option == 'Rédaction quête personnelle' ) 		{ $_quest_ui->DisplaySelectedPersonalQuest('WRITING'); }
		elseif( $_menu_option == 'Résumés personnels Scripteurs' ) 		{ $_quest_ui->DisplaySelectedPersonalQuest('RESUMES'); }
			elseif( $_menu_option == 'Détail résumé personnel Scripteurs' )	 	{ $_quest_ui->DisplaySelectedPersonalQuest('RESUME'); }
		elseif( $_menu_option == 'Personnage Scripteurs' ) 			{ $_quest_ui->DisplaySelectedPersonalQuest('CHARACTER'); }
	elseif( $_menu_option == 'Exemples quête personnelle' ) 		{ $_quest_ui->DisplayPersonalQuestsExamples(); }

	// Group Quests views
	elseif( $_menu_option == 'Mes quêtes de groupe' ) 			{ $_quest_ui->DisplayMyGroupQuests(); }
	elseif( $_menu_option == 'Quêtes de groupe actives' ) 			{ $_quest_ui->DisplayActiveGroupQuests(); }
		elseif( $_menu_option == 'Détail quête de groupe' ) 			{ $_quest_ui->DisplaySelectedGroupQuest('QUEST'); }
		elseif( $_menu_option == 'Rédaction quête de groupe' ) 			{ $_quest_ui->DisplaySelectedGroupQuest('WRITING'); }
		elseif( $_menu_option == 'Résumés de groupe' ) 				{ $_quest_ui->DisplaySelectedGroupQuest('RESUMES'); }
			elseif( $_menu_option == 'Détail résumé de groupe' ) 			{ $_quest_ui->DisplaySelectedGroupQuest('RESUME'); }
		elseif( $_menu_option == 'Groupe' ) 					{ $_quest_ui->DisplaySelectedGroupQuest('GROUP'); }
			elseif( $_menu_option == 'Histoire groupe' ) 				{ $_quest_ui->DisplaySelectedGroupQuest('GROUP BACKGROUND'); }
			elseif( $_menu_option == 'Avantages groupe' ) 				{ $_quest_ui->DisplaySelectedGroupQuest('GROUP ADVANTAGES'); }
	elseif( $_menu_option == 'Exemples quête de groupe' ) 			{ $_quest_ui->DisplayGroupQuestsExamples(); }

	// Mythic Quests views
	elseif( $_menu_option == 'Liste quêtes mythiques' ) 			{ $_quest_ui->DisplayAllMythicQuests(); }
	elseif( $_menu_option == 'Exemples quête mythique' ) 			{ $_quest_ui->DisplayMythicQuestsExamples(); }

	// Quests Manager views
	elseif( $_menu_option == 'Demandes' ) 					{ $_quest_ui->DisplayFilteredRequests('DEM'); }
	elseif( $_menu_option == 'Reprises' ) 					{ $_quest_ui->DisplayFilteredRequests('REPR'); }
	elseif( $_menu_option == 'Suites' ) 					{ $_quest_ui->DisplayFilteredRequests('SUITE'); }
	elseif( $_menu_option == 'Quêtes actives' ) 				{ $_quest_ui->DisplayActiveQuests(); }
		elseif( $_menu_option == 'Gérer quête personnelle' ) 			{ $_quest_ui->DisplayManagedPersonalQuest('QUEST'); }
		elseif( $_menu_option == 'Gérer rédaction quête personnelle' ) 		{ $_quest_ui->DisplayManagedPersonalQuest('WRITING'); }
		elseif( $_menu_option == 'Résumés personnels Responsable' ) 		{ $_quest_ui->DisplayManagedPersonalQuest('RESUMES'); }
			elseif( $_menu_option == 'Détail résumé personnel Responsable' )	 { $_quest_ui->DisplayManagedPersonalQuest('RESUME'); }
		elseif( $_menu_option == 'Personnage Responsable' ) 			{ $_quest_ui->DisplayManagedPersonalQuest('CHARACTER'); }
			elseif( $_menu_option == 'Histoire personnage' ) 			{ $_quest_ui->DisplayManagedPersonalQuest('CHARACTER BACKGROUND'); }
			elseif( $_menu_option == 'Compétences personnage' ) 			{ $_quest_ui->DisplayManagedPersonalQuest('CHARACTER SKILLS'); }

		elseif( $_menu_option == 'Gérer quête de groupe' ) 			{ $_quest_ui->DisplayManagedGroupQuest('QUEST'); }
		elseif( $_menu_option == 'Gérer rédaction quête de groupe' ) 		{ $_quest_ui->DisplayManagedGroupQuest('WRITING'); }
		elseif( $_menu_option == 'Résumés de groupe Responsable' ) 		{ $_quest_ui->DisplayManagedGroupQuest('RESUMES'); }
			elseif( $_menu_option == 'Détail résumé de groupe Responsable')	{ $_quest_ui->DisplayManagedGroupQuest('RESUME'); }
		elseif( $_menu_option == 'Groupe Responsable' ) 			{ $_quest_ui->DisplayManagedGroupQuest('GROUP'); }
	elseif( $_menu_option == 'Rechercher quêtes personnelles' ) 		{ $_quest_ui->DisplayPersonalQuestsSearch(); }
	elseif( $_menu_option == 'Valider parties' ) 				{ $_quest_ui->DisplayQuestPartValidationUI(); }
	elseif( $_menu_option == 'Imprimer quêtes' ) 				{ $_quest_ui->DisplayPrintingUI(); }
	elseif( $_menu_option == 'Récompenses' ) 				{ $_quest_ui->DisplayOwedRewards(); }
	elseif( $_menu_option == 'Titres de prestige' ) 			{ $_quest_ui->DisplayTitleManagementUI(); }

	// Stats views	
#	elseif( $_menu_option == 'Statistiques races' ) 		{ $_statistics_ui->DisplayRaceStatistics(); }
#	elseif( $_menu_option == 'Statistiques classes' ) 		{ $_statistics_ui->DisplayClassStatistics(); }
#	elseif( $_menu_option == 'Statistiques religions' ) 		{ $_statistics_ui->DisplayReligionStatistics(); } 
	
	else { echo $_menu_option; } 

echo '</DIV>';

echo '</DIV>';



// ═════ FOOTER ═════
$footnote = "Pour tout besoin de support ou tout commentaires, contactez l'équipe informatique : <u>TI@Terres-de-Belenos.com</u> ";
require_once('includes/footer.php');

?>