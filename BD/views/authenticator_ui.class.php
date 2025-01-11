<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Authenticator Views v1.2 r2 ==			║
║	Display authentication forms and results.		║
║	Requires authentication services.			║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('services/authenticator.class.php');

class AuthenticatorUI
{

private $Authenticator;

public $Error;

	//--CONSTRUCTOR--
	public function __construct($inAuthenticator)
	{
		$this->Authenticator = $inAuthenticator;
	}

	//--DISPLAY AHTHENTIFICATION FORM--
	public function Display()
	{
		// If account name already defined, fill the account name
		$account = "";
		if( $this->Authenticator->GetUser() ) { $account = $this->Authenticator->GetUser()->GetAccountName(); }


		// Display! 
		echo '<div style="margin-bottom:15px">';
		echo '<form id="authhentication" method="post">';

		echo '<table>';
		echo '<tr><td class="inputname">Identifiant</td><td class="inputbox"><input name="account" type="text" value="' . $account . '"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Mot de passe</td><td class="inputbox"><input name="password" type="password" value=""/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td colspan="2"><button type="submit" name="action" value="login" class="submit-button">Connexion</button></td></tr>';
		echo '</table>';

		echo '</form>';

		// Forgot your account information ?
		echo '<a href="get-password.php">J\'ai oublié mon identifiant ou mon mot de passe...</a>';

		echo '</div>';
	}

} // END of AuthenticatorUI class

?>
