<?php

include_once('configs/config.cfg');

// ═════ INCLUDES ═════
include_once('models/masterlist.class.php');
include_once('models/player.class.php');
include_once('models/character.class.php');
include_once('models/newcharacter.class.php');
include_once('models/skilltree.class.php');
include_once('models/coursemanager.class.php');
include_once('models/letter.class.php');
include_once('models/groupmanager.class.php');
include_once('models/registrationmanager.class.php');
include_once('models/questmanager.class.php');

include_once('services/master-services.class.php');
include_once('services/search-services.class.php');
include_once('services/player-services.class.php');
include_once('services/character-services.class.php');
include_once('services/skilltree-services.class.php');
include_once('services/course-services.class.php');
include_once('services/group-services.class.php');
include_once('services/registration-services.class.php');
include_once('services/quest-services.class.php');

include_once('views/menu_ui.class.php');
include_once('views/player_ui.class.php');
include_once('views/character_ui.class.php');
include_once('views/newcharacter_ui.class.php');
include_once('views/skilltree_ui.class.php');
include_once('views/course_ui.class.php');
include_once('views/group_ui.class.php');
include_once('views/registration_ui.class.php');
include_once('views/quest_ui.class.php');

include_once('controllers/search-controller.class.php');
include_once('controllers/player-controller.class.php');
include_once('controllers/character-controller.class.php');
include_once('controllers/skilltree-controller.class.php');
include_once('controllers/course-controller.class.php');
include_once('controllers/group-controller.class.php');
include_once('controllers/registration-controller.class.php');
include_once('controllers/quest-controller.class.php');




// ═════ INITIALIZATION ═════
session_start();

if( isset($_SESSION['world']) ) {
	include_once('configs/'.$_SESSION['world'].'/config.cfg'); 
	include_once('configs/'.$_SESSION['world'].'/alternate_names.cfg');
}

$_redirect = null;
$_nav_option = null;
$_menu_option = null;
$_change_char = False; 
	$_char_index = null;
$_new_char = False;
$_update_skill_list = False;
$_update_course_manager = False;
$_change_group = False; 
	$_group_id = null;
	$_group_manager_mode = False;
$_update_group_data = False;
$_update_reg_data = False;
$_update_quest_data = False;

$_message = ""; $_msg_class = "";

$_masterlist = null;
	if( isset($_SESSION['masterlist']) ) 		{$_masterlist = $_SESSION['masterlist'];}
$_player = null;
	if( isset($_SESSION['player']) ) 		{$_player = $_SESSION['player'];}
	elseif( isset($_SESSION['authenticated']) ) 	{$_player = new Player( $_SESSION['authenticated']->ToArray() );}
$_character = null;
	if( isset($_SESSION['character']) ) 		{$_character = $_SESSION['character'];}
$_skilltree = null;
	if( isset($_SESSION['skilltree']) ) 		{$_skilltree = $_SESSION['skilltree'];}
$_coursemanager = null;
	if( isset($_SESSION['coursemanager']) ) 	{$_coursemanager = $_SESSION['coursemanager'];}
$_groupmanager = null;
	if( isset($_SESSION['groupmanager']) ) 		{$_groupmanager = $_SESSION['groupmanager'];}
$_regmanager = null;
	if( isset($_SESSION['regmanager']) ) 		{$_regmanager = $_SESSION['regmanager'];}
$_questmanager = null;
	if( isset($_SESSION['questmanager']) ) 		{$_questmanager = $_SESSION['questmanager'];}

$_db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS);
$_master_svc = new MasterServices($_db, $_masterlist);
$_search_svc = new SearchServices($_db);
$_player_svc = new PlayerServices($_db, $_player);
$_character_svc = new CharacterServices($_db, $_player, $_character);
$_skilltree_svc = new SkillTreeServices($_db, $_character, $_skilltree);
$_course_svc = new CourseServices($_db, $_character, $_coursemanager);
$_group_svc = new GroupServices($_db, $_player, $_groupmanager);
$_registration_svc = new RegistrationServices($_db, $_player, $_regmanager);
$_quest_svc = new QuestServices($_db, $_player, $_questmanager);

$_search_ctrl = new SearchController($_search_svc);
$_player_ctrl = new PlayerController($_player_svc);
$_character_ctrl = new CharacterController($_character_svc);
$_skilltree_ctrl = new SkillTreeController($_skilltree_svc);
$_course_ctrl = new CourseController($_course_svc);
$_group_ctrl = new GroupController($_group_svc);
$_registration_ctrl = new RegistrationController($_registration_svc);
$_quest_ctrl = new QuestController($_quest_svc);



// ═════ SESSION HANDLING ═════
    if( !isset($_SESSION['authenticated']) ) { 
    		  session_destroy();
		  header('Location: login.php');
		  exit; }
elseif( !$_player ) { 
		  session_destroy();
		  header('Location: login.php');
		  exit; }
elseif( !isset($_SESSION['world']) ) {
		  $_redirect = 'worlds.php'; }
elseif( SITE_CLOSED || $_player->GetAccessLevel() < MINIMAL_ACCESS_REQUIRED ) { 
		  session_destroy();
		  header('Location: closed.php');
		  exit; }
elseif( !$_player_ctrl->CheckAge(AGE_MIN) ) { 
		  header('Location: agemin.php');
		  exit; }
else {	$_SESSION['user_id'] = $_SESSION['authenticated']->GetID(); }



// ═════ CONTROL ═════
if( isset($_POST['action']) ) {

	// **MENU MANAGEMENT**
	// NAV MENU
	if( $_POST['action'] == 'select-nav-option') { 
		    if( $_POST['option'] == 'Compte bélénois') 			{ $_nav_option = $_POST['option']; $_menu_option = 'Fiche joueur';}
		elseif( $_POST['option'] == 'Personnages') 			{ $_nav_option = $_POST['option']; }
		elseif( $_POST['option'] == 'Groupes') 				{ $_nav_option = $_POST['option']; }
		elseif( $_POST['option'] == 'Activités') 			{ $_nav_option = $_POST['option']; }
		elseif( $_POST['option'] == 'Scripteurs') 			{ $_nav_option = $_POST['option']; }

		elseif( $_POST['option'] == 'Outils gestion') 			{ $_redirect = "/bdmvc/?id=" . $_player->GetId()."&univers=".WORLD; }
		elseif( $_POST['option'] == 'Outils statistiques') 		{ $_redirect = "area-admin\index.php"; }
		elseif( $_POST['option'] == 'Pilotage') 			{ $_redirect = "area-pilot\index.php"; }
			elseif( $_POST['option'] == 'Préinscriptions') 		{ $_nav_option = "Activités"; $_menu_option = $_POST['option']; $_update_reg_data = True;}
		elseif( $_POST['option'] == 'Déconnexion') 			{ $_redirect = "login.php"; }
	}

	// OPTION MENU
	elseif( $_POST['action'] == 'select-menu-option') { 
		// Account options
		    if( $_POST['option'] == 'Identification compte') 		{ $_nav_option = "Compte bélénois"; $_menu_option = $_POST['option']; } 
		elseif( $_POST['option'] == 'Modifier compte')			{ $_nav_option = "Compte bélénois"; $_menu_option = $_POST['option']; } 
		elseif( $_POST['option'] == 'Modifier mot de passe') 		{ $_nav_option = "Compte bélénois"; $_menu_option = $_POST['option']; } 
		elseif( $_POST['option'] == 'Demander accès') 			{ $_nav_option = "Compte bélénois"; $_menu_option = $_POST['option']; } 
		elseif( $_POST['option'] == 'Fiche joueur') 			{ $_nav_option = "Compte bélénois"; $_menu_option = $_POST['option']; }
		elseif( $_POST['option'] == 'Journal joueur') 			{ $_nav_option = "Compte bélénois"; $_menu_option = $_POST['option']; }
		elseif( $_POST['option'] == 'Présences activités')		{ $_nav_option = "Compte bélénois"; $_menu_option = $_POST['option']; }
		elseif( $_POST['option'] == 'Expérience joueur')		{ $_nav_option = "Compte bélénois"; $_menu_option = $_POST['option']; }
		elseif( $_POST['option'] == 'Bénévolat') 			{ $_nav_option = "Compte bélénois"; $_menu_option = $_POST['option']; }
		elseif( $_POST['option'] == 'Crédits et dettes') 		{ $_nav_option = "Compte bélénois"; $_menu_option = $_POST['option']; }
		elseif( $_POST['option'] == 'Avertissements') 			{ $_nav_option = "Compte bélénois"; $_menu_option = $_POST['option']; }
		elseif( $_POST['option'] == 'Groupe cadre') 			{ $_nav_option = "Compte bélénois"; $_menu_option = $_POST['option']; }
		elseif( $_POST['option'] == 'Communications') 			{ $_nav_option = "Compte bélénois"; $_menu_option = $_POST['option']; }
		elseif( $_POST['option'] == 'Plaintes') 			{ $_nav_option = "Compte bélénois"; $_menu_option = $_POST['option']; }

		// Character options
		elseif( $_POST['option'] == 'Nouveau personnage')		{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; $_new_char = True; } 
		elseif( $_POST['option'] == 'Voir fiche personnage')		{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
		elseif( $_POST['option'] == 'Compétences personnage')		{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; $_update_skill_list = True; } 
			elseif( $_POST['option'] == 'Ajouter compétences régulières')	{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
			elseif( $_POST['option'] == 'Retirer compétences régulières')	{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
			elseif( $_POST['option'] == 'Ajouter compétences spéciales')	{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
			elseif( $_POST['option'] == 'Retirer compétences spéciales')	{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
			elseif( $_POST['option'] == 'Retour compétences')		{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
		elseif( $_POST['option'] == 'Enseignements personnage')		{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
			elseif( $_POST['option'] == 'Nouvel enseignement')		{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; $_update_course_manager = True;} 
			elseif( $_POST['option'] == 'Nouveau plan de cours')		{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; $_update_course_manager = True;} 
		elseif( $_POST['option'] == 'Histoire personnage')		{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
			elseif( $_POST['option'] == 'Éditer histoire personnage')	{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
			elseif( $_POST['option'] == 'Soumettre histoire personnage')	{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
			elseif( $_POST['option'] == 'Commentaires histoire personnage')	{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
		elseif( $_POST['option'] == 'Titres personnage')		{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
		elseif( $_POST['option'] == 'Quêtes personnage')		{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
			elseif( $_POST['option'] == 'Nouvelle quête personnelle')	{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
		elseif( $_POST['option'] == 'Résumés personnage')		{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
			elseif( $_POST['option'] == 'Nouveau résumé personnage')	{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
		elseif( $_POST['option'] == 'Vie personnage')			{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
			elseif( $_POST['option'] == 'Résurrection personnage')		{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
		elseif( $_POST['option'] == 'Expérience personnage')		{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
			elseif( $_POST['option'] == 'Détail expérience personnage')	{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
		elseif( $_POST['option'] == 'Missives personnage')		{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
			elseif( $_POST['option'] == 'Nouvelle missive PNJ')		{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
			elseif( $_POST['option'] == 'Nouvelle missive PJ')		{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
			elseif( $_POST['option'] == 'Archives missives personnage')	{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
		elseif( $_POST['option'] == 'Autres options personnage')	{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
			elseif( $_POST['option'] == 'Statut personnage')		{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
				elseif( $_POST['option'] == 'Mort personnage')			{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
				elseif( $_POST['option'] == 'Déportation personnage')		{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
				elseif( $_POST['option'] == 'Exil personnage')			{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
				elseif( $_POST['option'] == 'Retrait personnage')		{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
			elseif( $_POST['option'] == 'Renommer personnage')		{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
			elseif( $_POST['option'] == 'Provenance personnage')		{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
			elseif( $_POST['option'] == 'Journal personnage')		{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
			elseif( $_POST['option'] == 'Supprimer personnage')		{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 
		elseif( $_POST['option'] == 'Possessions personnage')		{ $_nav_option = "Personnages"; $_menu_option = $_POST['option']; } 

		// Group options
		elseif( $_POST['option'] == 'Invitations') 			{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; $_update_group_data = True;} 
		elseif( $_POST['option'] == 'Allégeances') 			{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; $_update_group_data = True;} 
		elseif( $_POST['option'] == 'Créer groupe') 			{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; } 
		elseif( $_POST['option'] == 'Voir fiche groupe') 		{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; $_update_group_data = True;} 
		elseif( $_POST['option'] == 'Actions groupe') 			{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; $_update_group_data = True;} 
		elseif( $_POST['option'] == 'Influence groupe') 		{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; $_update_group_data = True;} 
		elseif( $_POST['option'] == 'Institutions groupe') 		{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; } 
			elseif( $_POST['option'] == 'Nouvelle institution') 		{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; }
		elseif( $_POST['option'] == 'Avantages groupe') 		{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; } 
		elseif( $_POST['option'] == 'Quêtes groupe') 			{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; } 
			elseif( $_POST['option'] == 'Nouvelle quête groupe')		{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; } 
		elseif( $_POST['option'] == 'Résumés groupe') 			{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; } 
			elseif( $_POST['option'] == 'Nouveau résumé groupe')		{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; } 
		elseif( $_POST['option'] == 'Objectifs groupe')			{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; }
			elseif( $_POST['option'] == 'Ajouter objectif') 		{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; }
			elseif( $_POST['option'] == 'Dévoiler objectifs') 		{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; }
			elseif( $_POST['option'] == 'Retirer objectifs') 		{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; }
		elseif( $_POST['option'] == 'Membres groupe') 			{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; }
			elseif( $_POST['option'] == 'Inviter membres') 			{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; }
			elseif( $_POST['option'] == 'Expulser membres') 		{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; }
		elseif( $_POST['option'] == 'Définition groupe') 		{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; } 
			elseif( $_POST['option'] == 'Description groupe') 		{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; } 
				elseif( $_POST['option'] == 'Éditer description groupe')	{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; } 
			elseif( $_POST['option'] == 'Histoire groupe') 			{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; }
				elseif( $_POST['option'] == 'Éditer histoire') 			{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; }
			elseif( $_POST['option'] == 'Campement groupe') 		{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; }
				elseif( $_POST['option'] == 'Éditer campement')			{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; }
		elseif( $_POST['option'] == 'Autres options groupe')		{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; } 
			elseif( $_POST['option'] == 'Renommer groupe') 			{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; } 
			elseif( $_POST['option'] == 'Statut groupe') 			{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; } 
			elseif( $_POST['option'] == 'Nommer responsables') 		{ $_nav_option = "Groupes"; $_menu_option = $_POST['option']; } 

		// Activity options
		elseif( $_POST['option'] == 'Préinscriptions') 		{ $_nav_option = "Activités"; $_menu_option = $_POST['option']; $_update_reg_data = True;} 
		elseif( $_POST['option'] == 'Achat passe') 		{ $_nav_option = "Activités"; $_menu_option = $_POST['option']; $_update_reg_data = True;} 
		elseif( $_POST['option'] == 'Achat passeport') 		{ $_nav_option = "Activités"; $_menu_option = $_POST['option']; $_update_reg_data = True;} 
		elseif( $_POST['option'] == 'Services terrain')		{ $_nav_option = "Activités"; $_menu_option = $_POST['option']; $_update_reg_data = True;} 
		elseif( $_POST['option'] == 'Article Feuillet')		{ $_nav_option = "Activités"; $_menu_option = $_POST['option']; } 

		// Scriptors options
		elseif( $_POST['option'] == 'Quêtes prochain GN') 	{ $_nav_option = "Scripteurs"; $_menu_option = $_POST['option']; $_update_quest_data = True;} 
		elseif( $_POST['option'] == 'Mes quêtes - Scripteur') 	{ $_nav_option = "Scripteurs"; $_menu_option = $_POST['option']; $_update_quest_data = True;} 
		elseif( $_POST['option'] == 'Quêtes non assignées') 	{ $_nav_option = "Scripteurs"; $_menu_option = $_POST['option']; $_update_quest_data = True;} 
		elseif( $_POST['option'] == 'Recherche quêtes')		{ $_nav_option = "Scripteurs"; $_menu_option = $_POST['option']; } 
		elseif( $_POST['option'] == 'Demandes quêtes')		{ $_nav_option = "Scripteurs"; $_menu_option = $_POST['option']; $_update_quest_data = True;} 
		elseif( $_POST['option'] == 'Quêtes actives')		{ $_nav_option = "Scripteurs"; $_menu_option = $_POST['option']; $_update_quest_data = True;} 
		elseif( $_POST['option'] == 'Quêtes terminées')		{ $_nav_option = "Scripteurs"; $_menu_option = $_POST['option']; $_update_quest_data = True;} 
		elseif( $_POST['option'] == 'Récompenses quêtes')	{ $_nav_option = "Scripteurs"; $_menu_option = $_POST['option']; $_update_quest_data = True;} 
		elseif( $_POST['option'] == 'Pouvoirs légendaires')	{ $_nav_option = "Scripteurs"; $_menu_option = $_POST['option']; } 
		elseif( $_POST['option'] == 'Quêtes mythiques')		{ $_nav_option = "Scripteurs"; $_menu_option = $_POST['option']; $_update_quest_data = True;} 
	}

	// CHARACTER SELECTION
	elseif( $_POST['action'] == 'select-character') { $_nav_option = "Personnages"; $_menu_option = 'Voir fiche personnage'; $_change_char = True; $_char_index = $_POST['selection']; }

	// GROUP SELECTION
	elseif( $_POST['action'] == 'select-group') { $_nav_option = "Groupes"; $_menu_option = 'Voir fiche groupe'; $_change_group = True; $_group_id = $_POST['selection']; $_group_manager_mode = $_player->IsManagedGroup($_group_id); }

	// By default, nav and menu options are persisted.
	else { $_nav_option = $_SESSION['navoption']; $_menu_option = $_SESSION['menuoption']; }



	// **ACTION MANAGEMENT**
	// SEARCH ACTIONS
	    if( $_POST['action'] == 'search') { 
		    if( $_POST['option'] == 'Joueurs' )			{ if( $_search_ctrl->SearchForPlayers() ) { /* POST['search_results'] is set */ }
									  else { $_message = $_search_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Personnages' )		{ if( $_search_ctrl->SearchForCharacters() ) { /* POST['search_results'] is set */ }
									  else { $_message = $_search_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['submit'] == 'Groupes' )			{ if( $_search_ctrl->SearchForGroups() ) { /* POST['search_results'] is set */ }
									  else { $_message = $_search_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['submit'] == 'Quêtes' )			{ if( $_search_ctrl->SearchForQuests() ) { /* POST['search_results'] is set */ }
									  else { $_message = $_search_ctrl->Error; $_msg_class = "error"; } }
	}

	// PLAYER MANAGEMENT ACTIONS
	elseif( $_POST['action'] == 'manage-account') { 
		    if( $_POST['option'] == 'Informations compte')	{ if( $_player_ctrl->ChangeAccountInfo() ) { $_message = "Vos informations de compte ont été modifiés avec succès!"; $_msg_class = "success"; $_menu_option = 'Identification compte'; }
									  else { $_message = $_player_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Mot de passe') 		{ if( $_player_ctrl->ChangePassword() ) { $_message = "Votre mot de passe a été modifié avec succès!"; $_msg_class = "success"; }
									  else { $_message = $_player_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Demande accès') 		{ if( $_player_ctrl->RequestAccessLevel() ) { $_message = "Votre demande d'accès a été envoyée avec succès!"; $_msg_class = "success"; }
									  else { $_message = $_player_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Préférences courriel')	{ if( $_player_ctrl->SaveCommPreferences() ) { $_message = "Vos préférences ont été sauvegardées avec succès!"; $_msg_class = "success"; }
									  else { $_message = $_player_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Enregistrer plainte')	{ if( $_player_ctrl->SaveAndSendComplaint() ) { $_message = "Votre plainte a été envoyée avec succès au Comité d'éthique! Un suivi sera fait avec vous sous peu par courriel."; $_msg_class = "success"; }
									  else { $_message = $_player_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Obtenir GN gratuit')	{ if( $_player_ctrl->AddVolunteerFreeActivity() ) { $_message = "Votre GN gratuit a été ajouté à votre compte."; $_msg_class = "success"; }
									  else { $_message = $_player_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Obtenir entrées enfant')	{ if( $_player_ctrl->AddVolunteerFreeKidEntries() ) { $_message = "Vos entrées pour enfant ont été ajoutées à votre compte."; $_msg_class = "success"; }
									  else { $_message = $_player_ctrl->Error; $_msg_class = "error"; } }
	}
	elseif( $_POST['action'] == 'manage-player') { 
		    if( $_POST['option'] == 'Ajouter encadrement')	{ if( $_player_ctrl->RegisterTutoringGroupMember() ) { $_message = "Votre inscription au groupe cadre a été enregistrée avec succès!"; $_msg_class = "success"; $_menu_option = 'Journal joueur';}
									  else { $_message = $_player_ctrl->Error; $_msg_class = "error"; } }
	}

	// CHARACTER MANAGEMENT ACTIONS
	elseif( $_POST['action'] == 'manage-character') { 
		    if( isset($_POST['accept-invite']) )		{ if( $_group_ctrl->AcceptInvitation() ) { $_message = "Invitation acceptée!"; $_msg_class = "success"; }
									  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
		elseif( isset($_POST['refuse-invite']) )		{ if( $_group_ctrl->RefuseInvitation() ) { $_message = "Invitation refusée!"; $_msg_class = "success"; }
									  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Précision compétence')	{ if( $_character_ctrl->SpecifyCharacterSkill() ) { $_message = "Compétence précisée avec succès!"; $_msg_class = "success"; $_update_skill_list = True; $_menu_option = 'Compétences personnage'; }
									  else { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Choix maître PNJ')		{ if( $_course_ctrl->PrepareNPCMaster() ) { $_menu_option = 'Ajouter enseignement'; }
									  else { $_message = $_course_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Choix maître PJ')		{ if( $_course_ctrl->PreparePCMaster() ) { $_menu_option = 'Ajouter enseignement'; }
									  else { $_message = $_course_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Enseignement')		{ if( $_character_ctrl->RegisterTeaching() ) { $_message = "Votre enseignement a été enregistré avec succès!"; $_msg_class = "success"; $_update_skill_list = True; $_menu_option = 'Enseignements personnage';}
									  else { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Plan de cours')		{ if( $_character_ctrl->SubmitSyllabus() ) { $_message = "Votre plan de cours a été soumis avec succès!"; $_msg_class = "success"; $_menu_option = 'Enseignements personnage';}
									  else { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Histoire')			{ if( $_character_ctrl->ChangeBackground() ) { $_message = "L'histoire de votre personnage a été mis à jour avec succès!"; $_msg_class = "success"; $_menu_option = 'Histoire personnage';}
									  else { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Approbation histoire')	{ if( $_character_ctrl->RequestStoryApproval() ) { $_message = "Votre histoire a été soumise pour approbation avec succès!"; $_msg_class = "success"; $_menu_option = 'Histoire personnage';}
									  else { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Quête demandée')		{ if( $_character_ctrl->CannotAskQuests() ) { $_message = "Vous avez déjà une quête active ou en demande. Terminez ou annulez d'abord cette quête avant d'en demander une autre."; $_msg_class = "error"; $_menu_option = 'Quêtes personnage'; }
									  elseif( $_character_ctrl->RequestQuest() ) { $_message = "Votre demande a été enregistrée avec succès!"; $_msg_class = "success"; $_menu_option = 'Quêtes personnage'; }
									  else { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Créer résumé')		{ if( $_character_ctrl->RegisterResume() ) { $_message = "Votre résumé a été enregistré avec succès!"; $_msg_class = "success"; $_menu_option = 'Résumés personnage';}
									  else { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Modifier résumé')		{ if( $_character_ctrl->UpdateResume() ) { $_message = "Votre résumé a été mis à jour avec succès!"; $_msg_class = "success"; $_menu_option = 'Résumés personnage';}
									  else { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Envoyer missive PNJ')	{ if( $_character_ctrl->SaveNPCLetter() ) { $_message = "Votre missive a été envoyée avec succès!"; $_msg_class = "success"; $_menu_option = 'Missives personnage';}
									  else { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Envoyer missive PJ')	{ if( $_character_ctrl->SavePCLetter() ) { $_message = "Votre missive a été envoyée avec succès!"; $_msg_class = "success"; $_menu_option = 'Missives personnage';}
									  else { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Mort définitive')		{ if( $_character_ctrl->RegisterPermanentDeath() ) { $_message = "Votre déclaration a été enregistrée avec succès!"; $_msg_class = "success"; $_menu_option = 'Compétences personnage';}
									  else { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Déportation')		{ if( $_character_ctrl->RegisterDeportation() ) { $_message = "Votre déclaration a été enregistrée avec succès!"; $_msg_class = "success"; $_menu_option = 'Compétences personnage';}
									  else { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Exil')			{ if( $_character_ctrl->RegisterExile() ) { $_message = "Votre déclaration a été enregistrée avec succès!"; $_msg_class = "success"; $_menu_option = 'Compétences personnage';}
									  else { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Retrait')			{ if( $_character_ctrl->RegisterRetirement() ) { $_message = "Votre déclaration a été enregistrée avec succès!"; $_msg_class = "success"; $_menu_option = 'Compétences personnage';}
									  else { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Transfert XP')		{ if( $_character_ctrl->TransferExperience($_player->GetTotalExperience()) ) { $_message = "Le transfert s'est déroulé avec succès!"; $_msg_class = "success"; $_update_skill_list = True; $_menu_option = 'Expérience personnage';}
									  else { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Nom')			{ if( $_character_ctrl->RenameCharacter() ) { $_message = "Personnage renommé avec succès!"; $_msg_class = "success";}
									  else { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Provenance')		{ if( $_character_ctrl->UpdateOrigin() ) { $_message = "Provenance modifiée avec succès!"; $_msg_class = "success";}
									  else { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Supprimer')		{ if( $_character_ctrl->DeleteCharacter() ) { $_message = "Personnage supprimer avec succès!"; $_msg_class = "success"; $_character = null; $_menu_option = null;}
									  else { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Questionnaire')		{ if( $_character_ctrl->SaveSurveyAnswers() ) { $_message = "Réponses enregistrées avec succès!"; $_msg_class = "success"; $_menu_option = 'Voir fiche personnage';}
									  else { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Retour enseignement')	{ $_menu_option = 'Enseignements personnage'; }
		elseif( $_POST['option'] == 'Retour histoire')		{ $_menu_option = 'Histoire personnage'; }
		elseif( $_POST['option'] == 'Retour résumés')		{ $_menu_option = 'Résumés personnage'; }
		elseif( $_POST['option'] == 'Retour vie')		{ $_menu_option = 'Vie personnage'; }
		elseif( $_POST['option'] == 'Retour missives')		{ $_menu_option = 'Missives personnage'; }
		elseif( $_POST['option'] == 'Retour statut')		{ $_menu_option = 'Statut personnage'; }
	}
	elseif( $_POST['action'] == 'manage-newcharacter') { 
		    if( $_POST['option'] == 'Suivant'		
			|| $_POST['option'] == 'Précédant')		{ if( !$_character_ctrl->CollectNewCharacterData() ) { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Enregistrer')		{ if( $_character_ctrl->SaveNewCharacter() ) { $_message = "Votre nouveau personnage a été créé avec succès!"; $_msg_class = "success"; $_menu_option = null; }
									  else { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Enregistrer base')		{ if( $_character_ctrl->SaveNewBaseCharacter() ) { $_message = "Votre nouveau personnage a été créé avec succès!"; $_msg_class = "success"; $_menu_option = null; }
									  else { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Annuler création')		{ $_menu_option = null; }
	}
	elseif( $_POST['action'] == 'manage-character-lists') { 
		    if( isset($_POST['select-questpart']) )		{ $_menu_option = 'Voir partie quête personnelle'; }
		elseif( isset($_POST['select-quest']) )			{ $_menu_option = 'Détail quête personnelle'; }
		elseif( isset($_POST['cancel-quest']) )			{ if( $_character_ctrl->CancelQuest() ) { $_message = "L'annulation de votre quête s'est fait avec succès!"; $_msg_class = "success"; }
									  else { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
		elseif( isset($_POST['restore-quest']) )		{ if( $_character_ctrl->RestoreQuest() ) { $_message = "Votre quête a été restaurée avec succès!"; $_msg_class = "success"; }
									  else { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
		elseif( isset($_POST['select-resume']) )		{ $_menu_option = 'Détail résumé personnage'; }
		elseif( isset($_POST['edit-resume']) )			{ $_menu_option = 'Modifier résumé personnage'; }
		elseif( isset($_POST['select-letter']) )		{ $_menu_option = 'Détail missive personnage'; }
		elseif( isset($_POST['delete-letter']) )		{ if( $_character_ctrl->DeleteInGameLetter() ) { $_message = "Missive supprimée avec succès!"; $_msg_class = "success"; }
									  else { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
		elseif( isset($_POST['archive-letter']) )		{ if( $_character_ctrl->ArchiveLetter() ) { $_message = "Missive archivée avec succès!"; $_msg_class = "success"; }
									  else { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
		elseif( isset($_POST['select-teaching']) )		{ $_menu_option = 'Détail enseignement'; }
		elseif( isset($_POST['cancel-teaching']) )		{ if( $_character_ctrl->CancelTeaching() ) { $_message = "L'annulation de l'enseignement s'est fait avec succès!"; $_msg_class = "success"; }
									  else { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
		elseif( isset($_POST['redeem-teaching']) )		{ if( $_character_ctrl->RedeemTeaching() ) { $_message = "L'enseignement a été échangé pour des XP!"; $_msg_class = "success"; }
									  else { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
		elseif( isset($_POST['teach-student']) )		{ if( $_course_ctrl->PreparePCStudent() ) { $_menu_option = 'Ajouter enseignement'; }
									  else { $_message = $_course_ctrl->Error; $_msg_class = "error"; } }
		elseif( isset($_POST['buy-skill']) ) 			{ if( $_skilltree_ctrl->BuySkill() ) { $_message = "Compétence ajoutée avec succès!"; $_msg_class = "success"; $_update_skill_list = True; }
									  else { $_message = $_skilltree_ctrl->Error; $_msg_class = "error"; } }
		elseif( isset($_POST['refund-skill']) )			{ if( $_skilltree_ctrl->CancelSkill() ) { $_message = "Compétence retirée avec succès!"; $_msg_class = "success"; $_update_skill_list = True; }
									  else { $_message = $_skilltree_ctrl->Error; $_msg_class = "error"; } }
		elseif( isset($_POST['buy-talent']) ) 			{ if( $_skilltree_ctrl->BuyTalent() ) { $_message = "Compétence ajoutée avec succès!"; $_msg_class = "success"; $_update_skill_list = True; }
									  else { $_message = $_skilltree_ctrl->Error; $_msg_class = "error"; } }
		elseif( isset($_POST['specify-skill']) )		{ $_menu_option = 'Préciser compétence'; }
	}
	elseif( $_POST['action'] == 'manage-character-reset') { 
		    if( $_POST['option'] == 'Réinitialiser')		{ if( $_character_ctrl->ManageCharacterSoftReset() ) { $_message = "Réinialisation réussie!"; $_msg_class = "success"; $_update_skill_list = True; }
									  else { $_message = $_character_ctrl->Error; $_msg_class = "error"; } }
	}


	// GROUP MANAGEMENT ACTIONS
	elseif( $_POST['action'] == 'manage-group') { 
		    if( $_POST['option'] == 'Rejoindre')		{ if( $_group_ctrl->JoinGroup() ) { $_message = "Vous avez rejoint votre groupe avec succès!"; $_msg_class = "success"; $_menu_option = 'Allégeances'; $_update_group_data = True; }
								  	  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Créer')			{ if( $_group_ctrl->RegisterNewGroup() ) { $_message = "Votre groupe a été créé avec succès!"; $_msg_class = "success"; $_menu_option = null; $_update_group_data = True; }
								  	  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Description') 		{ if( $_group_ctrl->ChangeDescription() ) { $_message = "La mise à jour de la description s'est faite avec succès!"; $_msg_class = "success"; $_update_group_data = True; $_menu_option = 'Description groupe'; }
								  	  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Historique') 		{ if( $_group_ctrl->ChangeBackground() ) { $_message = "La mise à jour de l'historique s'est faite avec succès!"; $_msg_class = "success"; $_update_group_data = True; $_menu_option = 'Histoire groupe'; }
								  	  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Nom')			{ if( $_group_ctrl->RenameGroup() ) { $_message = "Groupe renommé avec succès!"; $_msg_class = "success"; $_update_group_data = True; }
									  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Créer institution')	{ if( $_group_ctrl->CreateNewInstitution() ) { $_message = "Votre Institution a été enregistré avec succès!"; $_msg_class = "success"; $_menu_option = 'Institutions groupe'; $_update_group_data = True; }
									  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Quête demandée')		{ if( $_group_ctrl->CannotAskQuests() ) { $_message = "Vous avez déjà une quête active ou en demande. Terminez ou annulez d'abord cette quête avant d'en demander une autre."; $_msg_class = "error"; $_menu_option = 'Quêtes groupe'; }
									  elseif( $_group_ctrl->RequestQuest() ) { $_message = "Votre demande a été enregistrée avec succès!"; $_msg_class = "success"; $_menu_option = 'Quêtes groupe'; $_update_group_data = True; }
									  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Créer résumé')		{ if( $_group_ctrl->RegisterResume() ) { $_message = "Votre résumé a été enregistré avec succès!"; $_msg_class = "success"; $_menu_option = 'Résumés groupe'; $_update_group_data = True; }
									  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Modifier résumé')		{ if( $_group_ctrl->UpdateResume() ) { $_message = "Votre résumé a été mis à jour avec succès!"; $_msg_class = "success"; $_menu_option = 'Résumés groupe'; $_update_group_data = True; }
									  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Statut')			{ if( $_group_ctrl->ChangeGroupStatus() ) { $_message = "Statut modifié avec succès!"; $_msg_class = "success"; $_update_group_data = True; }
									  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Campement')		{ if( $_group_ctrl->ChangeBaseCampInformation() ) { $_message = "Informations relatives au campement mise à jour avec succès!"; $_msg_class = "success"; $_menu_option = 'Campement groupe'; $_update_group_data = True; }
									  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Retour campement')		{ $_menu_option = 'Campement groupe'; }
		elseif( $_POST['option'] == 'Ajouter responsable')	{ if( $_group_ctrl->AddPIC() ) { $_message = "Responsable ajouté avec succès!"; $_msg_class = "success"; $_update_group_data = True; }
									  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Retirer responsable')	{ if( $_group_ctrl->RemovePIC() ) { $_message = "Vous n'êtes plus responsable!"; $_msg_class = "success"; $_update_group_data = True; $_menu_option = null; }
									  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Ajouter objectif')		{ if( $_group_ctrl->AddObjective() ) { $_message = "Objectif ajouté avec succès!"; $_msg_class = "success"; $_menu_option = 'Objectifs groupe'; $_update_group_data = True; }
									  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Actions')			{ if( $_group_ctrl->SaveNewActions() ) { $_message = "Actions mise à jour avec succès!"; $_msg_class = "success"; $_menu_option = 'Actions groupe'; $_update_group_data = True; }
									  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['option'] == 'Retour institutions')	{ $_menu_option = 'Institutions groupe'; }
		elseif( $_POST['option'] == 'Retour actions')		{ $_menu_option = 'Voir fiche groupe'; }
		elseif( $_POST['option'] == 'Retour influence')		{ $_menu_option = 'Voir fiche groupe'; }
		elseif( $_POST['option'] == 'Retour résumés')		{ $_menu_option = 'Résumés groupe'; }
	}
	elseif( $_POST['action'] == 'manage-group-lists') { 
		    if( isset($_POST['accept-invite']) )		{ if( $_group_ctrl->AcceptInvitation() ) { $_message = "Invitation acceptée!"; $_msg_class = "success"; $_update_group_data = True; }
									  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
		elseif( isset($_POST['refuse-invite']) )		{ if( $_group_ctrl->RefuseInvitation() ) { $_message = "Invitation refusée!"; $_msg_class = "success"; $_update_group_data = True; }
									  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
		elseif( isset($_POST['invite-member']) )		{ if( $_group_ctrl->InviteMember() ) { $_message = "Membre invité avec succès!"; $_msg_class = "success"; $_update_group_data = True; }
									  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
		elseif( isset($_POST['remove-member']) )		{ if( $_group_ctrl->RemoveMember() ) { $_message = "Membre retiré avec succès!"; $_msg_class = "success"; $_update_group_data = True; }
									  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
		elseif( isset($_POST['join-group']) )			{ if( $_group_ctrl->PrepareJoinedGroupSelection() ) { $_menu_option = 'Rejoindre groupe'; }
									  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
		elseif( isset($_POST['quit-group']) )			{ if( $_group_ctrl->QuitGroup() ) { $_message = "Allégeance retirée avec succès!"; $_msg_class = "success"; $_update_group_data = True; }
									  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
		elseif( isset($_POST['select-institution']) )		{ $_menu_option = 'Détail institution groupe'; }
		elseif( isset($_POST['retire-institution']) )		{ if( $_group_ctrl->RetireInstitution() ) { $_message = "La retraite de votre Institution s'est fait avec succès!"; $_msg_class = "success"; $_update_group_data = True; }
									  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
		elseif( isset($_POST['delete-institution']) )		{ if( $_group_ctrl->DeleteInstitution() ) { $_message = "La suppression de votre Institution s'est fait avec succès!"; $_msg_class = "success"; $_update_group_data = True; }
									  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
		elseif( isset($_POST['change-actions']) )		{ $_menu_option = 'Éditer actions groupe'; $_update_group_data = True; }
		elseif( isset($_POST['view-actions']) )			{ $_menu_option = 'Consulter actions groupe'; $_update_group_data = True; }
		elseif( isset($_POST['select-quest']) )			{ $_menu_option = 'Détail quête groupe'; }
		elseif( isset($_POST['cancel-quest']) )			{ if( $_group_ctrl->CancelQuest() ) { $_message = "L'annulation de votre quête s'est fait avec succès!"; $_msg_class = "success"; $_update_group_data = True; }
									  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
		elseif( isset($_POST['restore-quest']) )		{ if( $_group_ctrl->RestoreQuest() ) { $_message = "Votre quête a été restaurée avec succès!"; $_msg_class = "success"; $_update_group_data = True; }
									  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
		elseif( isset($_POST['select-resume']) )		{ $_menu_option = 'Détail résumé groupe'; }
		elseif( isset($_POST['edit-resume']) )			{ $_menu_option = 'Modifier résumé groupe'; }
		elseif( isset($_POST['reveal-objective']) )		{ if( $_group_ctrl->RevealObjective() ) { $_message = "Objectif dévoilé avec succès!"; $_msg_class = "success"; $_update_group_data = True; }
									  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
		elseif( isset($_POST['remove-objective']) )		{ if( $_group_ctrl->RemoveObjective() ) { $_message = "Objectif retiré avec succès!"; $_msg_class = "success"; $_update_group_data = True; }
									  else { $_message = $_group_ctrl->Error; $_msg_class = "error"; } }
	}

	// ACTIVITY MANAGEMENT ACTIONS
	elseif( $_POST['action'] == 'manage-activities') { 
		    if( $_POST['request'] == 'Payer sur place')		{ if( !$_registration_ctrl->Preregister(False) ) { $_message = $_registration_ctrl->Error; $_msg_class = "error"; }
								  	  else { $_message = "Votre préinscription s'est faite avec succès!"; $_msg_class = "success"; $_menu_option = 'Préinscriptions'; $_update_reg_data = True; $_update_group_data = True; } } 
		elseif( $_POST['request'] == 'Payer maintenant')	{ if( !$_registration_ctrl->Preregister(True) ) { $_message = $_registration_ctrl->Error; $_msg_class = "error"; }
								  	  else { $_message = "Votre préinscription s'est faite avec succès!"; $_msg_class = "success"; $_menu_option = 'Préinscriptions'; $_update_reg_data = True; $_update_group_data = True; } } 
		elseif( $_POST['request'] == 'Calculer paiement')	{ if( !$_registration_ctrl->ValidateRegistration() ) { $_message = $_registration_ctrl->Error; $_msg_class = "error"; }
									  else { $_menu_option = 'Payer préinscription'; } } 
		elseif( $_POST['request'] == 'Se désinscrire')		{ if( !$_registration_ctrl->Unregister() ) { $_message = $_registration_ctrl->Error; $_msg_class = "error"; }
								  	  else { $_message = "Votre désinscription s'est faite avec succès!"; $_msg_class = "success"; $_update_reg_data = True; $_update_group_data = True; } }
		elseif( $_POST['request'] == 'Acheter passe sur place') { if( $_registration_ctrl->BuyPass(False) ) { $_message = "L'acquisition de la passe s'est fait avec succès! Celle-ci sera payable à l'Accueil lors de votre prochain GN."; $_msg_class = "success"; $_update_reg_data = True;}
								  	  else { $_message = $_registration_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['request'] == 'Acheter passe maintenant') { if( $_registration_ctrl->BuyPass(True) ) { $_message = "L'acquisition de la passe s'est fait avec succès!"; $_msg_class = "success"; $_update_reg_data = True;}
								  	  else { $_message = $_registration_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['request'] == 'Acheter passeport')	{ if( $_registration_ctrl->BuyPassport() ) { $_message = "L'achat d'un passeport s'est fait avec succès!"; $_msg_class = "success"; $_update_reg_data = True;}
								  	  else { $_message = $_registration_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['request'] == 'Commander service')	{ if( $_registration_ctrl->RequestFieldService() ) { $_message = "Votre commande a été acheminée avec succès!"; $_msg_class = "success"; $_update_reg_data = True;}
								  	  else { $_message = $_registration_ctrl->Error; $_msg_class = "error"; } }
		elseif( $_POST['request'] == 'Soumettre article')	{ if( $_registration_ctrl->SubmitNewspaperArticle() ) { $_message = "Votre article a été soumis avec succès!"; $_msg_class = "success"; }
								  	  else { $_message = $_registration_ctrl->Error; $_msg_class = "error"; } }
	}


} // End ISSET(ACTION)



// ═════ DATA COLLECTION ═════

	// Master List
if (!$_masterlist) {
	$_masterlist = new MasterList();
}
$_master_svc->SetMasterList($_masterlist);
$_master_svc->Build();
$_masterlist = $_master_svc->GetMasterList();

	// Player
$_player_svc->SetUser($_player);
$_player_svc->GetPlayerInfo();
$_player_svc->GetPermissions();
$_player = $_player_svc->GetUser();

	// Character
$_character_svc->SetUser($_player);
if($_new_char) { 
	$_character = new NewCharacter( array( 'userid' => $_player->GetID(), 'step' => 1 ) ); 
	$_character_svc->SetCharacter($_character);
	$_character_svc->GetPossibleRaces();
}
elseif($_change_char) { 
	$_character = $_player->GetCharacters()[$_char_index]; 
	$_character_svc->SetCharacter($_character);
	$_character_svc->GetCharacterInfo();
	if( $_character_svc->GetPendingSurvey() ) { $_menu_option = 'Remplir questionnaire'; }
}
elseif( !($_character instanceof NewCharacter) ) { 
	$_character_svc->GetCharacterInfo(); 
}
$_character = $_character_svc->GetCharacter();

	// Skill Tree
if($_new_char) { $_skilltree = new SkillTree(); }
elseif($_change_char || $_update_skill_list) {
	$_skilltree = new SkillTree();
	$_skilltree_svc->SetCharacter($_character);
	$_skilltree_svc->SetSkillTree($_skilltree);
	$_skilltree_svc->BuildTrees();
	$_skilltree = $_skilltree_svc->GetSkillTree();
}

	// Teachings
if(!$_coursemanager) { $_coursemanager = new CourseManager( array( 'character' => $_character ) ); }
elseif($_update_course_manager) { 
	$_coursemanager->SetCharacter($_character);
	$_course_svc->SetManager($_coursemanager);
	$_course_svc->UpdateManager();
	$_coursemanager = $_course_svc->GetManager();
}

	// Groups
if(!$_groupmanager) { 
	$_groupmanager = new GroupManager();
	$_group_svc->SetManager($_groupmanager);
	$_group_svc->GetLists();
	$_group_svc->UpdateManager();
	$_groupmanager = $_group_svc->GetManager();
}
if($_change_group) {
	$_groupmanager->SetActiveGroup( new Group( array( 'id' => $_group_id ) ) );
	$_groupmanager->SetGroupManagerMode( $_group_manager_mode );	
	$_group_svc->SetUser($_player);
	$_group_svc->SetManager($_groupmanager);
	$_group_svc->UpdateManager();
	$_groupmanager = $_group_svc->GetManager();
}
elseif($_update_group_data) {
	$_group_svc->SetUser($_player);
	$_group_svc->SetManager($_groupmanager);
	$_group_svc->UpdateManager();
	$_groupmanager = $_group_svc->GetManager();
}

	// Registration Data
if(!$_regmanager) { 
	$_regmanager = new RegistrationManager();
	$_update_reg_data = True;
}
if($_update_reg_data) {
	$_registration_svc->SetUser($_player);
	$_registration_svc->SetManager($_regmanager);
	$_registration_svc->UpdateManager();
	$_regmanager = $_registration_svc->GetManager();
}

	// Quest
if(!$_questmanager) {
	$_questmanager = new QuestManager();
	$_quest_svc->SetManager($_questmanager);
}
elseif($_update_quest_data) {
	$_quest_svc->UpdateManager();
	if( $_questmanager->GetSelectedQuest() ) { 
		$type = $_questmanager->GetSelectedQuest()->GetType();
		$questid = $_questmanager->GetSelectedQuest()->GetID();
		$_quest_ctrl->SelectQuest($type, $questid); # Update logic if necessary
	}
	$_questmanager = $_quest_svc->GetManager();
}
	// SESSION
$_SESSION['masterlist'] = $_masterlist;
$_SESSION['player'] = $_player;
$_SESSION['character'] = $_character;
$_SESSION['skilltree'] = $_skilltree;
$_SESSION['coursemanager'] = $_coursemanager;
$_SESSION['groupmanager'] = $_groupmanager;
$_SESSION['regmanager'] = $_regmanager;
$_SESSION['questmanager'] = $_questmanager;
$_SESSION['navoption'] = $_nav_option;
$_SESSION['menuoption'] = $_menu_option;



// ═════ REDIRECTION ═════
$_db->CloseConnection();

if( isset($_redirect) ) { header('Location: ' . $_redirect); }



// ═════ HEADER ═════
require_once('includes/header.php');


// ═════ BODY ═════
echo '<body>';

# Paypal JS
if(PAYPAL_ENABLED) {
	echo '<script src="https://www.paypal.com/sdk/js?client-id=AXZXASyC2B2e9FQ9GjZoN7IX2wS5GhdBrZqcmFToOwyhhPTUwy1WaG3DukMfVAp4bmIJvW88PP4zqZ2x&enable-funding=venmo&currency=CAD&locale=fr_CA&vault=false" data-sdk-integration-source="button-factory"></script>';
}

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
$_player_ui = new PlayerUI( $_player );
$_character_ui = new CharacterUI( $_character );
$_newcharacter_ui = new NewCharacterUI( $_character );
$_skilltree_ui = new SkillTreeUI( $_skilltree );
$_course_ui = new CourseUI( $_coursemanager );
$_group_ui = new GroupUI( $_groupmanager );
$_registration_ui = new RegistrationUI( $_regmanager );
$_quest_ui = new QuestUI( $_questmanager );

echo '<DIV id="main">';

echo '<DIV id="left-panel">';
	$_menu_ui->DisplayMenu($_nav_option);
echo '</DIV>';

echo '<DIV id="right-panel">';
	    if( !$_menu_option ) 					{ echo "...";}

	// Account views
	elseif( $_menu_option == 'Identification compte' ) 		{ $_player_ui->DisplayUser(); }
	elseif( $_menu_option == 'Modifier compte' ) 			{ $_player_ui->DisplayAccountModifForm(); }
	elseif( $_menu_option == 'Modifier mot de passe' ) 		{ $_player_ui->DisplayPasswordModif(); }
	elseif( $_menu_option == 'Demander accès' ) 			{ $_player_ui->DisplayAccessRequestForm(); }

	elseif( $_menu_option == 'Fiche joueur' ) 			{ $_player_ui->DisplayPlayerInfo(); }
	elseif( $_menu_option == 'Journal joueur' ) 			{ $_player_ui->DisplayPlayerNotes(); }
	elseif( $_menu_option == 'Présences activités' ) 		{ $_player_ui->DisplayAttendances(); }
	elseif( $_menu_option == 'Expérience joueur' ) 			{ $_player_ui->DisplayExperienceDetails(); }
	elseif( $_menu_option == 'Bénévolat' ) 				{ $_player_ui->DisplayVolunteeringRewards(); }
	elseif( $_menu_option == 'Crédits et dettes' ) 			{ $_player_ui->DisplayCredsAndBebts(); }
	elseif( $_menu_option == 'Avertissements' ) 			{ $_player_ui->DisplayWarningsAndBlames(); }
	elseif( $_menu_option == 'Groupe cadre' ) 			{ $_player_ui->DisplayTutoringGroupForm(); }
	elseif( $_menu_option == 'Communications' ) 			{ $_player_ui->DisplayCommPreferences(); }
	elseif( $_menu_option == 'Plaintes' ) 				{ $_player_ui->DisplayComplaintForm(); }

	// Character views
	elseif( $_menu_option == 'Nouveau personnage' ) 		{ $_newcharacter_ui->DisplayNewCharacterForm(); }
	elseif( $_menu_option == 'Voir fiche personnage' ) 		{ $_character_ui->DisplayCharacterInfo(); }
	elseif( $_menu_option == 'Compétences personnage' ) 		{ $_character_ui->DisplayCharacterInfo('SKILLS'); }
		elseif( $_menu_option == 'Ajouter compétences régulières' ) 	{ $_skilltree_ui->DisplayBuyableSkills(); }
		elseif( $_menu_option == 'Retirer compétences régulières' ) 	{ $_skilltree_ui->DisplayRefundableSkills(); }
		elseif( $_menu_option == 'Ajouter compétences spéciales' ) 	{ $_skilltree_ui->DisplayBuyableTalents(); }
		elseif( $_menu_option == 'Retirer compétences spéciales' ) 	{ $_skilltree_ui->DisplayRefundableTalents(); }
		elseif( $_menu_option == 'Préciser compétence' ) 		{ $_character_ui->DisplayCharacterInfo('SKILL PRECISION'); }
	elseif( $_menu_option == 'Enseignements personnage' ) 		{ $_character_ui->DisplayCharacterInfo('TEACHINGS'); }
		elseif( $_menu_option == 'Nouvel enseignement' )		{ $_course_ui->DisplayNewTeachingOptions(); }
		elseif( $_menu_option == 'Ajouter enseignement' )		{ $_course_ui->DisplayTeachingRegForm(); }
		elseif( $_menu_option == 'Détail enseignement' )		{ $_character_ui->DisplayCharacterInfo('SELECTED TEACHING'); }
		elseif( $_menu_option == 'Nouveau plan de cours' )		{ $_course_ui->DisplaySyllabusRegForm(); }
	elseif( $_menu_option == 'Histoire personnage' ) 		{ $_character_ui->DisplayCharacterInfo('BACKGROUND'); }
		elseif( $_menu_option == 'Éditer histoire personnage' ) 	{ $_character_ui->DisplayCharacterInfo('EDIT BACKGROUND'); }
		elseif( $_menu_option == 'Soumettre histoire personnage' ) 	{ $_character_ui->DisplayCharacterInfo('SUBMIT BACKGROUND'); }
		elseif( $_menu_option == 'Commentaires histoire personnage' ) 	{ $_character_ui->DisplayCharacterInfo('BACKGROUND COMMENTS'); }
	elseif( $_menu_option == 'Titres personnage' ) 			{ $_character_ui->DisplayCharacterInfo('TITLES'); }
	elseif( $_menu_option == 'Quêtes personnage' ) 			{ $_character_ui->DisplayCharacterInfo('QUESTS'); }
		elseif( $_menu_option == 'Nouvelle quête personnelle' ) 	{ $_character_ui->DisplayCharacterInfo('NEW QUEST'); }
		elseif( $_menu_option == 'Détail quête personnelle' ) 		{ $_character_ui->DisplaySelectedQuest(); }
		elseif( $_menu_option == 'Voir partie quête personnelle' ) 	{ $_character_ui->DisplaySelectedQuestPart(); }
	elseif( $_menu_option == 'Résumés personnage' ) 		{ $_character_ui->DisplayCharacterInfo('RESUMES'); }
		elseif( $_menu_option == 'Nouveau résumé personnage' ) 		{ $_character_ui->DisplayCharacterInfo('NEW RESUME'); }
		elseif( $_menu_option == 'Détail résumé personnage' ) 		{ $_character_ui->DisplaySelectedResume(); }
		elseif( $_menu_option == 'Modifier résumé personnage' ) 	{ $_character_ui->DisplayResumeModificationForm(); }
	elseif( $_menu_option == 'Vie personnage' ) 			{ $_character_ui->DisplayCharacterInfo('LIFE'); }
		elseif( $_menu_option == 'Résurrection personnage' ) 		{ $_character_ui->DisplayCharacterInfo('RESURRECTION'); }
	elseif( $_menu_option == 'Expérience personnage' ) 		{ $_character_ui->DisplayCharacterInfo('XP'); }
		elseif( $_menu_option == 'Détail expérience personnage' ) 	{ $_character_ui->DisplayCharacterInfo('XP DETAIL'); }
	elseif( $_menu_option == 'Missives personnage' ) 		{ $_character_ui->DisplayCharacterInfo('LETTERS'); }
		elseif( $_menu_option == 'Nouvelle missive PNJ' ) 		{ $_character_ui->DisplayCharacterInfo('NEW NPC LETTER'); }
		elseif( $_menu_option == 'Nouvelle missive PJ' ) 		{ $_character_ui->DisplayCharacterInfo('NEW PC LETTER'); }
		elseif( $_menu_option == 'Détail missive personnage' ) 		{ $_character_ui->DisplayCharacterInfo('VIEW LETTER'); }
		elseif( $_menu_option == 'Archives missives personnage' ) 	{ $_character_ui->DisplayCharacterInfo('LETTER ARCHIVES'); }
	elseif( $_menu_option == 'Autres options personnage' ) 		{ $_character_ui->DisplayCharacterInfo('OTHERS'); }
		elseif( $_menu_option == 'Statut personnage' ) 			{ $_character_ui->DisplayCharacterInfo('STATUS'); }
			elseif( $_menu_option == 'Mort personnage' ) 			{ $_character_ui->DisplayCharacterInfo('PERMANENT DEATH'); }
			elseif( $_menu_option == 'Déportation personnage' ) 		{ $_character_ui->DisplayCharacterInfo('DEPORTATION'); }
			elseif( $_menu_option == 'Exil personnage' ) 			{ $_character_ui->DisplayCharacterInfo('EXILE'); }
			elseif( $_menu_option == 'Retrait personnage' ) 		{ $_character_ui->DisplayCharacterInfo('RETIREMENT'); }
		elseif( $_menu_option == 'Renommer personnage' ) 		{ $_character_ui->DisplayCharacterInfo('RENAME'); }
		elseif( $_menu_option == 'Provenance personnage' ) 		{ $_character_ui->DisplayCharacterInfo('ORIGIN'); }
		elseif( $_menu_option == 'Journal personnage' ) 		{ $_character_ui->DisplayCharacterInfo('NOTES'); }
		elseif( $_menu_option == 'Supprimer personnage' ) 		{ $_character_ui->DisplayCharacterInfo('DELETE'); }
	elseif( $_menu_option == 'Remplir questionnaire' ) 		{ $_character_ui->DisplayPendingSurvey(); }
	elseif( $_menu_option == 'Possessions personnage' ) 		{ $_character_ui->DisplayCharacterInfo('POSSESSIONS'); }

	// Group views
	elseif( $_menu_option == 'Invitations' ) 			{ $_group_ui->DisplayInvitations(); }
	elseif( $_menu_option == 'Allégeances' ) 			{ $_group_ui->DisplayAllegiances(); }
	elseif( $_menu_option == 'Rejoindre groupe' ) 			{ $_group_ui->DisplayJoinGroupForm(); }
	elseif( $_menu_option == 'Créer groupe' ) 			{ $_group_ui->DisplayGroupRegForm(); }
	elseif( $_menu_option == 'Voir fiche groupe' ) 			{ $_group_ui->DisplayGroupInfo(); }
	elseif( $_menu_option == 'Actions groupe' ) 			{ $_group_ui->DisplayGroupActions(); }
		elseif( $_menu_option == 'Éditer actions groupe') 		{ $_group_ui->DisplayActionsRegForm(); } 
		elseif( $_menu_option == 'Consulter actions groupe') 		{ $_group_ui->DisplayActivityActions(); } 
	elseif( $_menu_option == 'Influence groupe' ) 			{ $_group_ui->DisplayGroupInfluence(); }
	elseif( $_menu_option == 'Institutions groupe') 		{ $_group_ui->DisplayGroupInfo('INSTITUTIONS'); }
		elseif( $_menu_option == 'Nouvelle institution') 		{ $_group_ui->DisplayGroupInfo('NEW INSTITUTION'); } 
		elseif( $_menu_option == 'Détail institution groupe' ) 		{ $_group_ui->DisplaySelectedInstitution(); }
	elseif( $_menu_option == 'Avantages groupe') 			{ $_group_ui->DisplayGroupInfo('ADVANTAGES'); } 
	elseif( $_menu_option == 'Quêtes groupe') 			{ $_group_ui->DisplayGroupInfo('QUESTS'); } 
		elseif( $_menu_option == 'Nouvelle quête groupe' ) 		{ $_group_ui->DisplayGroupInfo('NEW QUEST'); }
		elseif( $_menu_option == 'Détail quête groupe' ) 		{ $_group_ui->DisplaySelectedQuest(); }
		elseif( $_menu_option == 'Voir partie quête groupe' ) 		{ $_group_ui->DisplaySelectedQuestPart(); }
	elseif( $_menu_option == 'Résumés groupe') 			{ $_group_ui->DisplayGroupInfo('RESUMES'); } 
		elseif( $_menu_option == 'Nouveau résumé groupe' ) 		{ $_group_ui->DisplayGroupInfo('NEW RESUME'); }
		elseif( $_menu_option == 'Détail résumé groupe' ) 		{ $_group_ui->DisplaySelectedResume(); }
		elseif( $_menu_option == 'Modifier résumé groupe' ) 	{ $_group_ui->DisplayResumeModificationForm(); }
	elseif( $_menu_option == 'Objectifs groupe' ) 			{ $_group_ui->DisplayGroupInfo('OBJECTIVES'); }
		elseif( $_menu_option == 'Ajouter objectif' ) 			{ $_group_ui->DisplayGroupInfo('ADD OBJECTIVES'); }
		elseif( $_menu_option == 'Dévoiler objectifs' ) 		{ $_group_ui->DisplayGroupInfo('REVEAL OBJECTIVES'); }
		elseif( $_menu_option == 'Retirer objectifs' ) 			{ $_group_ui->DisplayGroupInfo('REMOVE OBJECTIVES'); }
	elseif( $_menu_option == 'Membres groupe' ) 			{ $_group_ui->DisplayGroupInfo('MEMBERS'); }
		elseif( $_menu_option == 'Inviter membres' ) 			{ $_group_ui->DisplayGroupInfo('INVITE MEMBERS'); }
		elseif( $_menu_option == 'Expulser membres' ) 			{ $_group_ui->DisplayGroupInfo('REMOVE MEMBERS'); }
	elseif( $_menu_option == 'Définition groupe') 			{ $_group_ui->DisplayGroupInfo('DEFINITION'); }
		elseif( $_menu_option == 'Description groupe') 			{ $_group_ui->DisplayGroupInfo('DESCRIPTION'); } 
		elseif( $_menu_option == 'Éditer description groupe' )	 	{ $_group_ui->DisplayGroupInfo('EDIT DESCRIPTION'); }
		elseif( $_menu_option == 'Histoire groupe' ) 			{ $_group_ui->DisplayGroupInfo('BACKGROUND'); } 
		elseif( $_menu_option == 'Éditer histoire' )	 		{ $_group_ui->DisplayGroupInfo('EDIT BACKGROUND'); }
		elseif( $_menu_option == 'Campement groupe') 			{ $_group_ui->DisplayGroupInfo('BASECAMP'); } 
		elseif( $_menu_option == 'Éditer campement' ) 			{ $_group_ui->DisplayGroupInfo('EDIT BASECAMP'); }
	elseif( $_menu_option == 'Autres options groupe')		{ $_group_ui->DisplayGroupInfo('OTHERS'); } 
		elseif( $_menu_option == 'Renommer groupe' ) 			{ $_group_ui->DisplayGroupInfo('RENAME'); }
		elseif( $_menu_option == 'Statut groupe') 			{ $_group_ui->DisplayGroupInfo('STATUS'); } 
		elseif( $_menu_option == 'Nommer responsables' )		{ $_group_ui->DisplayGroupInfo('PEOPLE IN CHARGE'); }

	// Activity views
	elseif( $_menu_option == 'Préinscriptions' ) 			{ $_registration_ui->DisplayPreregistrations(); }
		elseif( $_menu_option == 'Payer préinscription' ) 		{ $_registration_ui->DisplayMainPaymentForm(); }
	elseif( $_menu_option == 'Achat passe' ) 			{ $_registration_ui->DisplayPassPurchaseForm(); }
	elseif( $_menu_option == 'Achat passeport' ) 			{ $_registration_ui->DisplayPassportPurchaseForm(); }
	elseif( $_menu_option == 'Services terrain' ) 			{ $_registration_ui->DisplayFieldServiceRequestForm(); }
	elseif( $_menu_option == 'Article Feuillet' ) 			{ $_registration_ui->DisplayNewspaperArticleSubmitForm(); }

	// Scriptor views
	elseif( $_menu_option == 'Quêtes prochain GN' ) 		{ $_quest_ui->DisplayNextActivityQuests(); }
	elseif( $_menu_option == 'Mes quêtes - Scripteur' ) 		{ $_quest_ui->DisplayMyQuests(); }
	elseif( $_menu_option == 'Quêtes non assignées' ) 		{ $_quest_ui->DisplayUnassignedQuests(); }


	// elseif( $_menu_option == 'Quêtes personnelles actives' ) 		{ $_quest_ui->DisplayActivePersonalQuests(); }
	// 	elseif( $_menu_option == 'Détail quête personnelle' ) 			{ $_quest_ui->DisplaySelectedPersonalQuest('QUEST'); }
	// 	elseif( $_menu_option == 'Rédaction quête personnelle' ) 		{ $_quest_ui->DisplaySelectedPersonalQuest('WRITING'); }
	// 	elseif( $_menu_option == 'Résumés personnels scripteur' ) 		{ $_quest_ui->DisplaySelectedPersonalQuest('RESUMES'); }
	// 		elseif( $_menu_option == 'Détail résumé personnel Scripteurs' )	 	{ $_quest_ui->DisplaySelectedPersonalQuest('RESUME'); }
	// 	elseif( $_menu_option == 'Personnage scripteurs' ) 			{ $_quest_ui->DisplaySelectedPersonalQuest('CHARACTER'); }
	// elseif( $_menu_option == 'Exemples quête personnelle' ) 		{ $_quest_ui->DisplayPersonalQuestsExamples(); }

	// 	elseif( $_menu_option == 'Groupe scripteur' ) 					{ $_quest_ui->DisplaySelectedGroupQuest('GROUP'); }
	// 		elseif( $_menu_option == 'Histoire groupe' ) 				{ $_quest_ui->DisplaySelectedGroupQuest('GROUP BACKGROUND'); }
	// 		elseif( $_menu_option == 'Avantages groupe' ) 				{ $_quest_ui->DisplaySelectedGroupQuest('GROUP ADVANTAGES'); }

	// elseif( $_menu_option == 'Quêtes mythiques' ) 			{ $_quest_ui->DisplayAllMythicQuests(); }

	// // Quests Manager views
	// elseif( $_menu_option == 'Demandes' ) 					{ $_quest_ui->DisplayFilteredRequests('DEM'); }
	// elseif( $_menu_option == 'Reprises' ) 					{ $_quest_ui->DisplayFilteredRequests('REPR'); }
	// elseif( $_menu_option == 'Suites' ) 					{ $_quest_ui->DisplayFilteredRequests('SUITE'); }
	// elseif( $_menu_option == 'Quêtes actives' ) 				{ $_quest_ui->DisplayActiveQuests(); }
	// 	elseif( $_menu_option == 'Gérer quête personnelle' ) 			{ $_quest_ui->DisplayManagedPersonalQuest('QUEST'); }
	// 	elseif( $_menu_option == 'Gérer rédaction quête personnelle' ) 		{ $_quest_ui->DisplayManagedPersonalQuest('WRITING'); }
	// 	elseif( $_menu_option == 'Résumés personnels Responsable' ) 		{ $_quest_ui->DisplayManagedPersonalQuest('RESUMES'); }
	// 		elseif( $_menu_option == 'Détail résumé personnel Responsable' )	 { $_quest_ui->DisplayManagedPersonalQuest('RESUME'); }
	// 	elseif( $_menu_option == 'Personnage Responsable' ) 			{ $_quest_ui->DisplayManagedPersonalQuest('CHARACTER'); }
	// 		elseif( $_menu_option == 'Histoire personnage' ) 			{ $_quest_ui->DisplayManagedPersonalQuest('CHARACTER BACKGROUND'); }
	// 		elseif( $_menu_option == 'Compétences personnage' ) 			{ $_quest_ui->DisplayManagedPersonalQuest('CHARACTER SKILLS'); }

	// 	elseif( $_menu_option == 'Gérer quête de groupe' ) 			{ $_quest_ui->DisplayManagedGroupQuest('QUEST'); }
	// 	elseif( $_menu_option == 'Gérer rédaction quête de groupe' ) 		{ $_quest_ui->DisplayManagedGroupQuest('WRITING'); }
	// 	elseif( $_menu_option == 'Résumés de groupe Responsable' ) 		{ $_quest_ui->DisplayManagedGroupQuest('RESUMES'); }
	// 		elseif( $_menu_option == 'Détail résumé de groupe Responsable')	{ $_quest_ui->DisplayManagedGroupQuest('RESUME'); }
	// 	elseif( $_menu_option == 'Groupe Responsable' ) 			{ $_quest_ui->DisplayManagedGroupQuest('GROUP'); }
	// elseif( $_menu_option == 'Rechercher quêtes personnelles' ) 		{ $_quest_ui->DisplayPersonalQuestsSearch(); }
	// elseif( $_menu_option == 'Récompenses' ) 				{ $_quest_ui->DisplayOwedRewards(); }
	// elseif( $_menu_option == 'Titres de prestige' ) 			{ $_quest_ui->DisplayTitleManagementUI(); }

	else { echo $_menu_option; } 

echo '</DIV>'; # Right panel

echo '</DIV>'; # Main

echo '</DIV>'; # Content


// ═════ FOOTER ═════
$footnote = "Pour tout besoin de support ou tout commentaires, contactez l'équipe informatique : <u>TI@Terres-de-Belenos.com</u> ";
require_once('includes/footer.php');

?>