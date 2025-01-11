<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Menu Views v1.2 r3 ==				║
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
		echo '<hr width=250px />';

		echo '<button type="submit" name="option" value="Présences activités" class="text-button" />';
		echo 'Activités';
		echo '</button>';
		echo '<br />';

		echo '<button type="submit" name="option" value="Passes" class="text-button" />';
		echo "Passes";
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
