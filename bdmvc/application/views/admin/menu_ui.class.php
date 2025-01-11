<?php

/*
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


		// Everyone's options
		echo '<button type="submit" name="option" value="Compte bélénois" class="image-button" />';
		echo '<img src="images/menu_principal/comptebelenois.png"  height=50>';
		echo '</button>';

		echo '<img src="images/menu_principal/blanc.png">';

		echo '<button type="submit" name="option" value="Personnages" class="image-button" />';
		echo '<img src="images/menu_principal/personnages.png" height=50 >';
		echo '</button>';

		echo '<img src="images/menu_principal/blanc.png">';

		echo '<button type="submit" name="option" value="Groupe" class="image-button" />';
		echo '<img src="images/menu_principal/groupe.png" height=50 >';
		echo '</button>';

		echo '<img src="images/menu_principal/blanc.png">';

		/*echo '<button type="submit" name="option" value="Noblesse" class="image-button" />';
		echo '<img src="images/menu_principal/noblesse.png" height=50 >';
		echo '</button>';

		echo '<img src="images/menu_principal/blanc.png">';*/

		echo '<button type="submit" name="option" value="Activités" class="image-button" />';
		echo '<img src="images/menu_principal/activites.png" height=50 >';
		echo '</button>';

		echo '<img src="images/menu_principal/blanc.png">';

		// Scriptors' menu options
		if( $this->User->IsScriptor() ) {

			echo '<button type="submit" name="option" value="Gestion quêtes" class="image-button" />';
			echo '<img src="images/menu_principal/scripteurs.png" height=50 >';
			echo '</button>';

			echo '<img src="images/menu_principal/blanc.png">';

		}

		// Administrators' menu options
		if( $this->User->IsManager() ) {

			//echo '<button type="submit" name="option" value="Outils gestion" class="image-button" />';
			echo '<a href="http://www.terres-de-belenos.com/bdmvc/?id=' .$this->User->GetID()  .'"><img src="images/menu_principal/outilsdegestion.png" height=50 ></a>';
			//echo '</button>';

			//echo '<img src="images/menu_principal/blanc.png">';

		}

		// Administrators' menu options
		if( $this->User->IsAdmin() ) {

			echo '<button type="submit" name="option" value="Outils statistiques" class="image-button" />';
			echo '<img src="images/menu_principal/statistiques.png" height=50 >';
			echo '</button>';

			echo '<img src="images/menu_principal/blanc.png">';

		}

		// DBA menu options
		/*if( $this->User->IsDBA() ) {

			echo '<button type="submit" name="option" value="Pilotage" class="image-button" />';
			echo '<img src="images/menu_principal/pilotage.png" height=50 >';
			echo '</button>';

			echo '<img src="images/menu_principal/blanc.png">';

		}*/

		echo '<button type="submit" name="option" value="Déconnexion"  class="image-button" style="float: right; padding-top: 25px;" />';
		echo '<img src="images/menu_principal/deconnexion.png" height=25 >';
		echo '</button>';

		echo '</form>';
	}


	//--DISPLAY CORRECT SUBMENU--
	public function DisplayMenu( $inNavOption )
	{
		if( !$inNavOption ) { return; }

		elseif( $inNavOption == 'Compte bélénois' ) 	{ $this->DisplayAccountMenu(); }
		elseif( $inNavOption == 'Personnages' ) 	{ $this->DisplayCharacterMenu(); }
		elseif( $inNavOption == 'Groupe' ) 		{ $this->DisplayGroupMenu(); }
		//elseif( $inNavOption == 'Noblesse' ) 		{ $this->DisplayNobilityMenu(); }
		elseif( $inNavOption == 'Activités' )		{ $this->DisplayActivityMenu(); }

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

		echo '<button type="submit" name="option" value="Demander accès" class="text-button" />';
		echo 'Demander un accès';
		echo '</button>';
		echo '<br />';

		echo '<hr width=225px />';

		// Player information's options
		echo '<button type="submit" name="option" value="Fiche joueur" class="text-button" />';
		echo 'Ma fiche de joueur';
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Journal joueur" class="text-button" />';
		echo 'Journal';
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Présences activités" class="text-button" />';
		echo 'Présences aux activités';
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Avertissements" class="text-button" />';
		echo 'Avertissements';
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Crédits et dettes" class="text-button" />';
		echo 'Crédits et dettes';
		echo '</button>';
		echo '<br />';

		if( $this->User->GetAge() < 16 ) {
			echo '<button type="submit" name="option" value="Groupe cadre" class="text-button" />';
			echo 'Groupe cadre';
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
		echo '<button type="submit" name="option" value="Invitations" class="text-button" />';
		echo 'Invitations';
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Allégeances" class="text-button" />';
		echo 'Allégeances';
		echo '</button>';
		echo '<br />';

		echo '<hr width=225px />';

		// Group managers' menu options
		if( $this->User->GetManagedGroupID() ) {

			echo '<button type="submit" name="option" value="Voir fiche groupe" class="text-button" />';
			echo 'Mon groupe';
			echo '</button>';
			echo '<br />';

			echo '<button type="submit" name="option" value="Voir membres" class="text-button" />';
			echo 'Membres';
			echo '</button>';
			echo '<br />';
	
			echo '<button type="submit" name="option" value="Objectifs groupe" class="text-button" />';
			echo 'Objectifs';
			echo '</button>';
			echo '<br />';
	
			echo '<button type="submit" name="option" value="Missives" class="text-button" />';
			echo 'Missives';
			echo '</button>';
			echo '<br />';
	
			echo '<button type="submit" name="option" value="Actions" class="text-button" />';
			echo 'Actions';
			echo '</button>';
			echo '<br />';
	
			echo '<button type="submit" name="option" value="Résumés groupe" class="text-button" />';
			echo 'Résumés des activités';
			echo '</button>';
			echo '<br />';

			echo '<button type="submit" name="option" value="Bâtiments groupe" class="text-button" />';
			echo 'Bâtiments';
			echo '</button>';
			echo '<br />';

		}
		else {
			echo '<button type="submit" name="option" value="Créer groupe" class="text-button" />';
			echo 'Créer un groupe';
			echo '</button>';
			echo '<br />';
		}

		echo '<hr width=250px />';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY ACCOUNT MENU--
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
		$lCharacterList = $this->User->GetCharacters();
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


	//--DISPLAY GROUP MENU--
	public function DisplayActivityMenu()
	{
		// Display the title.
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<span class="section-title">MENU</span>';
		echo '<hr width=250px />';

		echo '<button type="submit" name="option" value="Préinscriptions" class="text-button" />';
		echo 'Préinscriptions';
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Achat passe" class="text-button" />';
		echo "Achat d'une passe";
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
