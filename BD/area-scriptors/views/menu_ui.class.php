<?php

/*
=SCRIPTOR FILE=
╔══CLASS════════════════════════════════════════════════════════╗
║	== Menu Views v1.2 r2 ==				║
║	Display all the menu UIs in the application.		║
║	Requires user model.					║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('models/user.class.php');

class MenuUI
{

private $User;

public $Error;

	//--CONSTRUCTOR--
	public function __construct($inUser)
	{
		$this->User = $inUser;
	}


	//--DISPLAY NAVIGATION MENU--
	public function DisplayNavMenu()
	{
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-nav-option"/>';

		if( !$this->User->HasAccess('Scripteur') ) {
			echo "Vous n'êtes pas authorisé à accéder à cette section du site! Retournez à « Terres-de-Belenos.com » et recommencez.";
			return;
		}

		// Scriptor's options
		if(PERSONAL_QUEST_MENU_ENABLED){
		echo '<div class="menu-box"><button type="submit" name="option" value="Quêtes personnelles" class="menu-button"/>';
		echo 'Personnelles';
		echo '</button></div>';
		}

		if(GROUP_QUEST_MENU_ENABLED){
		echo '<div class="menu-box"><button type="submit" name="option" value="Quêtes de groupe" class="menu-button"/>';
		echo 'Groupes';
		echo '</button></div>';
		}

		// Manager's menu options
		if( $this->User->IsManager() ) {
			if(MYTHIC_QUEST_MENU_ENABLED){
			echo '<div class="menu-box"><button type="submit" name="option" value="Quêtes mythiques" class="menu-button"/>';
			echo 'Mythiques';
			echo '</button></div>';
			}

			echo '<div class="menu-box"><button type="submit" name="option" value="Responsable des quêtes" class="menu-button"/>';
			echo 'Responsable';
			echo '</button></div>';
		}

		echo '</form>';
	}


	//--DISPLAY NAVIGATION UNDERLAY MENU--
	public function DisplayNavUnderlay()
	{
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-nav-option"/>';


		// Indicators
		//echo '<img src="images/indicateurs/missivesnonlues.png"  height=25>';

		// Disconnect
		echo '<div class="underlay-box"><button type="submit" name="option" value="Retour" class="underlay-button"/>';
		echo 'Retour';
		echo '</button></div>';

		// DBA menu options
		if( $this->User->IsDBA() ) {

			echo '<div class="underlay-box"><button type="submit" name="option" value="Pilotage Quêtes" class="underlay-button"/>';
			echo 'Pilotage';
			echo '</button></div>';

		}

		echo '</form>';
	}


	//--DISPLAY CORRECT SUBMENU--
	public function DisplayMenu( $inNavOption )
	{
		if( !$inNavOption ) { return; }

		elseif( $inNavOption == 'Quêtes personnelles' ) 	{ $this->DisplayPersonalQuestsMenu(); }
		elseif( $inNavOption == 'Quêtes de groupe' ) 		{ $this->DisplayGroupQuestsMenu(); }
		elseif( $inNavOption == 'Quêtes mythiques' ) 		{ $this->DisplayMythicQuestsMenu(); }
		elseif( $inNavOption == 'Responsable des quêtes' ) 	{ $this->DisplayQuestManagerMenu(); }

		elseif( $inNavOption == 'Pilotage Quêtes' ) 		{ $this->DisplayQuestAdministrationMenu(); }

		else { $this->DisplayDefaultMenu(); }
	}


	//--DISPLAY PERSONAL QUESTS MENU--
	public function DisplayPersonalQuestsMenu()
	{
		// Display the title.
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<span class="section-title">MENU</span>';
		echo '<hr width=250px />';

		// Default option
		echo '<button type="submit" name="option" value="Mes quêtes personnelles" class="text-button" />';
		echo 'Mes quêtes';
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Quêtes personnelles non assignées" class="text-button" />';
		echo 'Quêtes non assignées';
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Quêtes personnelles actives" class="text-button" />';
		echo 'Quêtes en cours';
		echo '</button>';
		echo '<br />';

		echo '<hr width=225px />';

		// Scriptors' ressources options
		echo '<button type="submit" name="option" value="Exemples quête personnelle" class="text-button" disabled />';
		echo 'Exemples';
		echo '</button>';
		echo '<br />';

		echo '<hr width=250px />';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY GROUP QUESTS MENU--
	public function DisplayGroupQuestsMenu()
	{
		// Display the title.
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<span class="section-title">MENU</span>';
		echo '<hr width=250px />';

		// Default option
		echo '<button type="submit" name="option" value="Mes quêtes de groupe" class="text-button" />';
		echo 'Mes quêtes';
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Quêtes de groupe actives" class="text-button" />';
		echo 'Quêtes en cours';
		echo '</button>';
		echo '<br />';

		echo '<hr width=225px />';

		// Scriptors' ressources options
		echo '<button type="submit" name="option" value="Exemples quête de groupe" class="text-button" disabled />';
		echo 'Exemples';
		echo '</button>';
		echo '<br />';

		echo '<hr width=250px />';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY MYTHIC QUESTS MENU--
	public function DisplayMythicQuestsMenu()
	{
		// Display the title.
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<span class="section-title">MENU</span>';
		echo '<hr width=250px />';

		// Default option
		echo '<button type="submit" name="option" value="Liste quêtes mythiques" class="text-button" disabled />';
		echo 'Quêtes';
		echo '</button>';
		echo '<br />';

		echo '<hr width=225px />';

		// Scriptors' ressources options
		echo '<button type="submit" name="option" value="Exemples quête mythique" class="text-button" disabled />';
		echo 'Exemples';
		echo '</button>';
		echo '<br />';

		echo '<hr width=250px />';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY QUEST MANAGER MENU--
	public function DisplayQuestManagerMenu()
	{
		// Display the title.
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<span class="section-title">MENU</span>';
		echo '<hr width=250px />';

		// Quests and Requests menu options
		echo '<button type="submit" name="option" value="Demandes" class="text-button" />';
		echo 'Demandes';
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Reprises" class="text-button" />';
		echo 'Reprises';
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Suites" class="text-button" />';
		echo 'Suites';
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Quêtes actives" class="text-button" />';
		echo 'Quêtes en cours';
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Rechercher quêtes personnelles" class="text-button" />';
		echo 'Recherche';
		echo '</button>';
		echo '<br />';

		echo '<hr width=225px />';

		echo '<button type="submit" name="option" value="Valider parties" class="text-button" disabled/>';
		echo 'Validation';
		echo '</button>';
		echo '<br />';
		
		echo '<button type="submit" name="option" value="Imprimer quêtes" class="text-button" disabled/>';
		echo 'Impression';
		echo '</button>';
		echo '<br />';
		
		echo '<hr width=225px />';

		echo '<button type="submit" name="option" value="Récompenses" class="text-button" />';
		echo 'Récompenses';
		echo '</button>';
		echo '<br />';
		
		echo '<button type="submit" name="option" value="Titres de prestige" class="text-button" />';
		echo 'Titres de prestige';
		echo '</button>';
		echo '<br />';
		
		echo '<hr width=250px />';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY ADMIN MENU--
	public function DisplayQuestAdministrationMenu()
	{
		// Display the title.
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<span class="section-title">MENU</span>';
		echo '<hr width=250px />';


		// All user's options
		echo '<button type="submit" name="option" value="Statistiques races" class="text-button" />';
		echo 'Stats sur les races';
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Statistiques classes" class="text-button" />';
		echo 'Stats sur les classes';
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Statistiques religions" class="text-button" />';
		echo 'Stats sur les religions';
		echo '</button>';
		echo '<br />';

		echo '<hr width=225px />';
		echo '<button type="submit" name="option" value="Liste des PNJ" class="text-button" />';
		echo 'Liste des PNJ';
		echo '</button>';
		echo '<br />';

		echo '<hr width=250px />';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY DEFAULT MENU--
	public function DisplayDefaultMenu()
	{
		// Display the title.
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<span class="section-title">MENU</span>';
		echo '<hr width=250px />';
		echo '<i><font color="grey">Aucune option disponible</font></i><br />';
		echo '<hr width=250px style="margin-top: 10px" />';

		echo '</form>';
		echo '</div>';
	}

} // END of MenuUI class

?>
