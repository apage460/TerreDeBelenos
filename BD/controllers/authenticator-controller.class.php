<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Authenticator Controller v1.2 r1 ==			║
║	Implements authentication control logic.		║
║	Requires authentication services.			║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('services/authenticator.class.php');

class AuthenticatorController
{

private $Authenticator;

public $Error;

	//--CONSTRUCTOR--
	public function __construct(&$inAuthenticator)
	{
		$this->Authenticator = $inAuthenticator;
	}


	//--AUTHENTICATION LOGIC--
	public function Login()
	{
		// Check if the posted information is valid
		if(!$_POST['account']) { 
			$this->Error = "Account name is mandatory!"; 
			return False; 
		}
		if(!$_POST['password']) { 
			$this->Error = "Password is mandatory!"; 
			return False; 
		}


		// Call the authentication function
		$r = $this->Authenticator->Authenticate($_POST['account'], $_POST['password']);
		
		if($r) {
			$this->Error = null;
			return True;
		}

		$this->Error = "Authentification failed!";
		return False;
	}


	//--ACCOUNT RECOVERY LOGIC--
	public function SendRecoveryMail()
	{
		$this->Error = null;

		// Check if the posted information is valid
		if(!$_POST['mail']) { 
			$this->Error = "L'adresse courriel est obligatoire!"; 
			return False; 
		}

		// Prepare data
		$mail = $_POST['mail'];
		$tutor_mail = null;


		// Get account name
		$account = $this->Authenticator->GetAccountFromMail( $mail );
		
		if( !$account ) { 
			$this->Error = "Aucun compte n'est associé à cette adresse courriel.";
			return False;
		}

		// Check if user has a tutor. If so, get his email.
		if( $account['tutor'] ) { $tutor_mail = $this->Authenticator->GetTutorMail( $account['tutor'] ); }

		// Generate new password
		$password = "Bele*" . $this->Authenticator->GenerateRandomString( 4 );

		// Send mails & change password
		if( $this->Authenticator->SendTemporaryPassword( $account, $password, $mail, $tutor_mail ) ) {
			if( !$this->Authenticator->SaveTemporaryPassword($account['name'], $password) ) { 
				$this->Error = "La modification du mot de passe a échouée! Veuillez ignorer le courriel que vous avez reçu.";
				return False; 
			}

			return True;
		}
		else {
			$this->Error = "Une erreur est survenue lors de l'envoi du message de récupération! Votre mot de passe reste inchangé.";
			return False;
		}


		$this->Error = "Une erreur imprévue est survenue! Contactez un DBA.";
		return False;
	}


} // END of AuthenticatorController class

?>
