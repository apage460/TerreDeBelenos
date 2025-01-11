<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Menu Views v1.2 r5 ==				║
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

		echo '<button type="submit" name="option" value="Retour"  class="image-button" style="float: right; padding-top: 25px;" />';
		echo '<img src="images/menu_principal/retour.png" height=25 >';
		echo '</button>';

		echo '</form>';
	}


	//--DISPLAY CORRECT SUBMENU--
	public function DisplayMenu()
	{
		// Display the title.
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<span class="section-title">MENU</span>';

		// Repair sub-menu
		echo '<hr width=250px />';

		echo '<button type="submit" name="option" value="Nettoyage BD" class="text-button" />';
		echo 'Auto-nettoyage';
		echo '</button>';
		echo '<br />';

		// Annual jobs sub-menu
		echo '<hr width=250px />';

		echo '<button type="submit" name="option" value="Transfert activités" class="text-button" />';
		echo 'Transfert de présences';
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Transfert XP" class="text-button" />';
		echo "Transfert d'XP";
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Restitution PV" class="text-button" />';
		echo "Restitution PV";
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="XP Passe" class="text-button" />';
		echo "XP Passe";
		echo '</button>';
		echo '<br />';

		// Mass-modifications sub-menu
		echo '<hr width=250px />';

		echo '<button type="submit" name="option" value="Compétence-Ajout de masse" class="text-button" />';
		echo "Compétence - Ajout de masse";
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Personnages-MAJ massive" class="text-button" />';
		echo "Personnages - MAJ massive";
		echo '</button>';
		echo '<br />';

		// Individual jobs sub-menu
		echo '<hr width=250px />';

		echo '<button type="submit" name="option" value="Trouver ID" class="text-button" />';
		echo "Trouver un ID";
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Réinitialiser compte" class="text-button" />';
		echo "Réinitialiser un compte";
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Changement classe" class="text-button" />';
		echo "Changement de classe";
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Suppression personnage" class="text-button" />';
		echo "Suppression de personnage";
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
