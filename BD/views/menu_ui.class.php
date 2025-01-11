<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Menu Views v1.2 r6 ==				║
║	Display all the menu UIs in the application.		║
║	Requires user model.					║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('models/player.class.php');

class MenuUI
{

private $Player;

public $Error;

	//--CONSTRUCTOR--
	public function __construct($inPlayer)
	{
		$this->Player = $inPlayer;
	}


	//--DISPLAY NAVIGATION MENU--
	public function DisplayNavMenu()
	{
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-nav-option"/>';

		// If Player is NULL, then display error and stop.
		if( !$this->Player || !($this->Player instanceof Player) ) {
			echo "Vous n'êtes pas authentifié! Retournez à « Terres-de-Belenos.com/BD » et recommencez.";
			return;
		}


		// Everyone's options
		echo '<div class="menu-box"><button type="submit" name="option" value="Compte bélénois" class="menu-button"/>';
		echo 'Compte bélénois';
		echo '</button></div>';

		echo '<div class="menu-box"><button type="submit" name="option" value="Personnages" class="menu-button"/>';
		echo 'Personnages';
		echo '</button></div>';

		if(GROUP_MENU_ENABLED){
		echo '<div class="menu-box"><button type="submit" name="option" value="Groupes" class="menu-button"/>';
		echo 'Groupes';
		echo '</button></div>';
		}

		if(ACTIVITY_MENU_ENABLED){
		echo '<div class="menu-box"><button type="submit" name="option" value="Activités" class="menu-button"/>';
		echo 'Activités';
		echo '</button></div>';
		}

		// Scriptors' menu options
		if(QUEST_MENU_ENABLED){
		if( $this->Player->HasAccess('Scripteur') ) {
			echo '<div class="menu-box"><button type="submit" name="option" value="Scripteurs" class="menu-button"/>';
			echo 'Quêtes';
			echo '</button></div>';
		}
		}

		if(STATS_MENU_ENABLED){
		// Administrators' stats menu options
		if( $this->Player->HasAccess('Statistiques') ) {
			echo '<div class="menu-box"><button type="submit" name="option" value="Outils statistiques" class="menu-button"/>';
			echo 'Statistiques';
			echo '</button></div>';
		}
		}

		if(ADMIN_MENU_ENABLED){
		// Administrators' management menu options
		if( $this->Player->HasAccess('Admin') || $this->Player->HasAccess('Arbitre') || $this->Player->HasAccess('Inscripteur') ) {
			echo '<div class="menu-box"><button type="submit" name="option" value="Outils gestion" class="menu-button"/>';
			echo 'Outils de gestion';
			echo '</button></div>';
		}
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
		echo '<div class="underlay-box"><button type="submit" name="option" value="Déconnexion" class="underlay-button"/>';
		echo 'Déconnexion';
		echo '</button></div>';

		// DBA menu options
		if( $this->Player->IsDBA() ) {

		if(PILOT_MENU_ENABLED){
			echo '<div class="underlay-box"><button type="submit" name="option" value="Pilotage" class="underlay-button"/>';
			echo 'Pilotage';
			echo '</button></div>';
		}

			#echo '<div class="underlay-box"><button type="submit" name="option" value="Préinscriptions" class="underlay-button"/>';
			#echo 'Inscription';
			#echo '</button></div>';
		}

		echo '</form>';
	}


	//--DISPLAY CORRECT SUBMENU--
	public function DisplayMenu( $inNavOption )
	{
		if( !$inNavOption ) { return; }

		elseif( $inNavOption == 'Compte bélénois' ) 	{ $this->DisplayAccountMenu(); }
		elseif( $inNavOption == 'Personnages' ) 	{ $this->DisplayCharacterMenu(); }
		elseif( $inNavOption == 'Groupes' ) 		{ $this->DisplayGroupMenu(); }
		elseif( $inNavOption == 'Activités' )		{ $this->DisplayActivityMenu(); }
		elseif( $inNavOption == 'Scripteurs' )		{ $this->DisplayScriptorMenu(); }

		else { $this->DisplayDefaultMenu(); }
	}


	//--DISPLAY ACCOUNT MENU--
	public function DisplayAccountMenu()
	{
		// Display the title.
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<span class="section-title">MENU</span>';
		echo '<hr width=250px />';


		// User account's options
		echo '<button type="submit" name="option" value="Identification compte" class="text-button" />';
		echo 'Identification du compte';
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Modifier mot de passe" class="text-button" />';
		echo 'Modifier mon mot de passe';
		echo '</button>';
		echo '<br />';

		echo '<hr width=225px />';

		// Player information's options
		echo '<button type="submit" name="option" value="Fiche joueur" class="text-button" />';
		echo 'Ma fiche de joueur';
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Présences activités" class="text-button" />';
		echo 'Présences aux activités';
		echo '</button>';
		echo '<br />';

		if(USER_EXPERIENCE_ENABLED){
		echo '<button type="submit" name="option" value="Expérience joueur" class="text-button" />';
		echo 'Expérience';
		echo '</button>';
		echo '<br />';
		}

		if(USER_VOLUNTEERING_ENABLED){
		echo '<button type="submit" name="option" value="Bénévolat" class="text-button" />';
		echo 'Bénévolat';
		echo '</button>';
		echo '<br />';
		}

		if(USER_DEBTS_ENABLED){
		echo '<button type="submit" name="option" value="Crédits et dettes" class="text-button" />';
		echo 'Crédits et dettes';
		echo '</button>';
		echo '<br />';
		}

		if(USER_WARNINGS_ENABLED){
		echo '<button type="submit" name="option" value="Avertissements" class="text-button" />';
		echo 'Avertissements';
		echo '</button>';
		echo '<br />';
		}

		if(USER_JOURNAL_ENABLED){
		echo '<button type="submit" name="option" value="Journal joueur" class="text-button" />';
		echo 'Journal';
		echo '</button>';
		echo '<br />';
		}

		if(USER_MENTORING_ENABLED){
		if( $this->Player->GetAge() < 16 ) {
			echo '<button type="submit" name="option" value="Groupe cadre" class="text-button" />';
			echo 'Groupe cadre';
			echo '</button>';
			echo '<br />';
		}
		}

		echo '<hr width=225px />';

		// Communication options
		if(USER_COMMS_ENABLED){
		echo '<button type="submit" name="option" value="Communications" class="text-button" />';
		echo 'Communications';
		echo '</button>';
		echo '<br />';
		}

		if(USER_COMPLAINT_ENABLED){
		echo '<button type="submit" name="option" value="Plaintes" class="text-button" />';
		echo 'Plaintes';
		echo '</button>';
		echo '<br />';
		}

		echo '<hr width=250px />';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY GROUP MENU--
	public function DisplayGroupMenu()
	{
		// Display the title.
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<span class="section-title">MENU</span>';
		echo '<hr width=250px />';


		// All player's options
		echo '<button type="submit" name="option" value="Allégeances" class="text-button" />';
		echo 'Allégeances';
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Invitations" class="text-button" />';
		echo 'Invitations';
		echo '</button>';
		echo '<br />';

		echo '<hr width=225px />';

		echo '<button type="submit" name="option" value="Créer groupe" class="text-button" />';
		echo '*Créer un groupe*';
		echo '</button>';
		echo '<br />';

		echo '</form>';
		echo '<hr width=225px />';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-group"/>';


		// Group managers' menu options
		echo '<span><b>Responsable de :</b></span>';
		
		if( $this->Player->GetManagedGroupCount() ) {

			$lManagedGroupList = $this->Player->GetManagedGroups();
			foreach ($lManagedGroupList as $group) {
				echo '<button type="submit" name="selection" value="'.$group->GetID().'" class="text-button" />';
				echo $group->GetName();
				echo '</button>';
				echo '<br />';
			}

		}
		else {
			echo '<i><font color="grey">Aucun groupe.</font></i><br />';
		}
		echo '<hr width=225px />';


		// Members' menu options
		echo '<span><b>Membre de :</b></span>';

		$lMemberGroupList = $this->Player->GetMemberGroups();
		$lNoGroupShown = True;
		foreach ($lMemberGroupList as $group) {
			if( !$this->Player->IsManagedGroup($group->GetID()) ) {
				echo '<button type="submit" name="selection" value="'.$group->GetID().'" class="text-button" />';
				echo $group->GetName();
				echo '</button>';
				echo '<br />';

				$lNoGroupShown = False;
			}
		}
		if( $lNoGroupShown ) {
			echo '<i><font color="grey">Aucun groupe.</font></i><br />';
		}

		echo '<hr width=250px />';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY CHARACTER MENU--
	public function DisplayCharacterMenu()
	{
		// Display the title.
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<span class="section-title">MENU</span>';
		echo '<hr width=250px />';


		// New Character Option
		echo '<button type="submit" name="option" value="Nouveau personnage" class="text-button" />';
		echo '*Nouveau*';
		echo '</button>';
		echo '<br />';

		echo '</form>';
		echo '<hr width=225px />';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-character"/>';


		// Character selection
		$lCharacterList = $this->Player->GetCharacters();
		foreach($lCharacterList as $i => $character) {
			echo '<button type="submit" name="selection" value="'.$i.'" class="text-button" />';
			echo $character->GetFirstName();
			echo '</button>';
			echo '<br />';
		}


		echo '<hr width=250px />';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY ACTIVITY MENU--
	public function DisplayActivityMenu()
	{
		// Display the title.
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option" />';

		echo '<span class="section-title">MENU</span>';
		echo '<hr width=250px />';

		if(ACTIVITY_REGISTRATION_ENABLED){
		echo '<button type="submit" name="option" value="Préinscriptions" class="text-button" '.REGISTRATIONS.'/>';
		echo 'Préinscriptions';
		echo '</button>';
		echo '<br />';
		} 

		if(ACTIVITY_PASS_ENABLED){
		echo '<button type="submit" name="option" value="Achat passe" class="text-button" '.REGISTRATIONS.'/>';
		echo "Achat d'une passe";
		echo '</button>';
		echo '<br />';
		} 

		if(ACTIVITY_SERVICES_ENABLED){
		echo '<button type="submit" name="option" value="Services terrain" class="text-button" />';
		echo "Services";
		echo '</button>';
		echo '<br />';
		}

		echo '<hr width=250px />';

		if(ACTIVITY_NEWSPAPER_ENABLED){
		echo '<button type="submit" name="option" value="Article Feuillet" class="text-button" />';
		echo "Feuillet d'Hyden";
		echo '</button>';
		echo '<br />';
		}

		echo '<hr width=250px />';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY ACTIVITY MENU--
	public function DisplayScriptorMenu()
	{
		if( $this->Player->HasAccess('Scripteur') ) {

		// Display the title.
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option" />';

		echo '<span class="section-title">MENU</span>';
		echo '<hr width=250px />';

		echo '<button type="submit" name="option" value="Quêtes prochain GN" class="text-button" />';
		echo "Prochain GN";
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Mes quêtes - Scripteur" class="text-button" />';
		echo 'Mes quêtes';
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Quêtes non assignées" class="text-button" />';
		echo "Quêtes non assignées";
		echo '</button>';
		echo '<br />';

		echo '<hr width=250px />';

		if( $this->Player->IsManager() ){
		echo '<button type="submit" name="option" value="Demandes quêtes" class="text-button" />';
		echo "Demandes";
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Quêtes actives" class="text-button" />';
		echo "Quêtes actives";
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Quêtes terminées" class="text-button" />';
		echo "Quêtes terminées";
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Récompenses quêtes" class="text-button" />';
		echo "Récompenses";
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Pouvoirs légendaires" class="text-button" />';
		echo "Pouvoirs légendaires";
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Quêtes mythiques" class="text-button" />';
		echo "Êtres mythiques";
		echo '</button>';
		echo '<br />';
		}

		echo '<hr width=250px />';

		echo '</form>';
		echo '</div>';

		} else { $this->DisplayDefaultMenu(); }
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
