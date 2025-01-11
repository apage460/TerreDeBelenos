<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== User Controller v1.2 r7 ==				║
║	Implements user management control logic.		║
║	Requires user services.					║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('services/user-services.class.php');

class UserController
{

protected $Services;

public $Error;

	//--CONSTRUCTOR--
	public function __construct(&$inServices)
	{
		$this->Services = $inServices;
	}


	//--INPUT VALIDATION LOGIC--
	public function CheckInput()
	{
		$this->Error = null;

		// Check if the posted information is valid and keep all valid data.
		//-- NAME IS MANDATORY
		if(!$_POST['account']) { 
			$this->Error = "Tous les champs sont obligatoires!"; 
		}
		else { $this->Services->GetUser()->SetAccountName( $_POST['account'] ); }

		//-- PASSWORD IS MANDATORY
		if(!$_POST['password']) { 
			$this->Error = "Tous les champs sont obligatoires!"; 
		} 
		//-- PW CONFIRMATION IS MANDATORY
		if(!$_POST['pw-confirm']) { 
			$this->Error = "Tous les champs sont obligatoires!"; 
		} 
		//-- PASSWORD MUST BE EQUAL TO CONFIRMATION
		if($_POST['password'] != $_POST['pw-confirm']) { 
			$this->Error = "Il y a une différence entre le mot de passe saisi et sa confirmation."; 
		} 

		//-- FIRSTNAME IS MANDATORY
		if(!$_POST['firstname']) { 
			$this->Error = "Tous les champs sont obligatoires!"; 
		} 
		else { $this->Services->GetUser()->SetFirstName( $_POST['firstname'] ); }

		//-- LASTNAME IS MANDATORY
		if(!$_POST['lastname']) { 
			$this->Error = "Tous les champs sont obligatoires!"; 
		} 
		else { $this->Services->GetUser()->SetLastName( $_POST['lastname'] ); }

		//-- GENDER IS MANDATORY
		if(!isset($_POST['gender']) || !$_POST['gender']) { 
			$this->Error = "Tous les champs sont obligatoires!"; 
		} 
		else { $this->Services->GetUser()->SetGender( $_POST['gender'] ); }

		//-- DoB IS MANDATORY
		if(!$_POST['dateofbirth']) { 
			$this->Error = "Tous les champs sont obligatoires!"; 
		} 
		elseif( !$this->CheckBirthDate($_POST['dateofbirth']) ) {
			$this->Error = "La date de naissance doit suivre le format indiqué et être valide!"; 
		}
		else { $this->Services->GetUser()->SetBirthDate( $_POST['dateofbirth'] ); }

		//-- ACCOUNT NAME MUST NOT BE TAKEN
		if( $this->Services->AccountNameExists( trim($_POST['account'] )) ) { 
			$this->Error = "Le nom de compte que vous avez saisi existe déjà."; 
		}

		//-- ALTERNATIVE NAME MUST NOT BE TAKEN
		$this->Services->GetUser()->SetAltName( $_POST['altname'] );
		if( $this->Services->AltNameExists( trim($_POST['altname'] )) ) { 
			$this->Error = "Le nom d'usage que vous avez saisi existe déjà."; 
		}

		//-- ACCOUNT NAME MUST NOT BE TAKEN
		if( $this->Services->AlreadyHasAccount( $_POST['firstname'], $_POST['lastname'], $_POST['dateofbirth'] ) ) { 
			$this->Error = "Vous avez déjà un compte. Il est interdit d'en créer un second. Utiliser l'outil de récupération de compte ou contacter l'équipe TI pour obtenir vos informations de connexion."; 
		}

		if( $this->Error ) { return False; }
		return True;
	}


	//--BIRTHDATE VALIDATION LOGIC--
	public function CheckBirthDate( $inDate )
	{
		// Separate date pieces
		$date_parts = explode('-', $inDate);


		// Check list
		if( count($date_parts) != 3 ) 		{ return False; }	// Date must have 3 parts

		if( strlen($date_parts[0]) != 4 )  	{ return False; }	// First part is Year
		if( !is_numeric($date_parts[0]) )  	{ return False; }	
		if( $date_parts[0] < 1 )  		{ return False; }	

		if( strlen($date_parts[1]) != 2 )  	{ return False; }	// Second part is Month
		if( !is_numeric($date_parts[1]) )  	{ return False; }	
		if( $date_parts[1] < 1 )  		{ return False; }	
		if( $date_parts[1] > 12 )  		{ return False; }	

		if( strlen($date_parts[2]) != 2 )  	{ return False; }	// Third part is Day
		if( !is_numeric($date_parts[2]) )  	{ return False; }	
		if( $date_parts[2] < 1 )  		{ return False; }	
		if( $date_parts[2] > 31 )  		{ return False; }	


		return True;
	}


	//--MAIL VALIDATION LOGIC--
	public function CheckMail()
	{
		$this->Error = null;

		// Check if the posted information is valid
		//-- MAIL ADDRESS IS MANDATORY

		if(!$_POST['mail']) { 
			$this->Error = "Le courriel est obligatoire si vous avez ".AGE_NO_TUTOR." ans et plus!"; 
			return False;
		} 
		else { $this->Services->GetUser()->SetMailAddress( $_POST['mail'] ); }

		//-- MAIL ADRESS MUST NOT BE TAKEN
		if( $this->Services->MailAddressExists( $_POST['mail'] ) ) { 
			$this->Error = "L'adresse courriel que vous avez saisi est déjà utilisée."; 
			return False; 
		}

		return True;
	}


	//--TUTOR VALIDATION LOGIC--
	public function CheckTutor()
	{
		$this->Error = null;

		// Check if the posted information is valid
		//-- TUTOR IS MANDATORY
		if( !isset($_POST['tutor']) || !$_POST['tutor']) { 
			$this->Error = "Vous avez moins de ".AGE_NO_TUTOR." ans. Identifiez s.v.p. le compte utilisateur de votre tuteur."; 
		}
		else { $this->Services->GetUser()->SetTutor( $_POST['tutor'] ); }

		if( $this->Error ) { return False; }


		// If account exists in the database, all is good. Else, we have a problem.
		if( $this->Services->AccountNameExists( $_POST['tutor'] ) ) { return True; }

		$this->Error = "Le compte de votre tuteur n'existe pas!";
		return False;
	}


	//--AGE VALIDATION LOGIC--
	public function CheckAge($inReachedAge)
	{
		$this->Error = null;

		// Get user's age
		$lAge = $this->Services->GetUser()->GetAge();

		if( $lAge < $inReachedAge ) { Return False; }
		return True;
	}


	//--REGISTRATION LOGIC--
	public function RegisterUser()
	{
		$this->Error = null;

		// Prepare data
		$lTutor = null;
			if ( isset($_POST['tutor']) ) { $lTutor = $_POST['tutor']; }
		// Call the registration function
		$r = $this->Services->Register($_POST['account'], $_POST['password'], $_POST['altname'], $_POST['firstname'], $_POST['lastname'], $_POST['gender'], $_POST['dateofbirth'], $_POST['mail'], $lTutor);
		
		if($r) {
			$this->Error = null;
			return True;
		}

		$this->Error = "L'enregistrement a échoué!";
		return False;
	}


	//--ACCOUNT MODIFICATION LOGIC--
	public function ChangeAccountInfo()
	{
		$this->Error = null;

		// Check if the posted information is valid
		//-- ACCOUNT NAME IS MANDATORY
		//-- Note : Account unicity is validated on update.
		if(!$_POST['account']) { 
			$this->Error = "L'identifiant du compte est obligatoire!"; 
			return False; 
		}
		//-- FIRST NAME IS MANDATORY
		if(!$_POST['firstname']) { 
			$this->Error = "Le prénom est obligatoire!"; 
			return False; 
		}
		//-- LAST NAME IS MANDATORY
		if(!$_POST['lastname']) { 
			$this->Error = "Le nom de famille est obligatoire!"; 
			return False; 
		}
		//-- GENDER IS MANDATORY
		if(!$_POST['gender']) { 
			$this->Error = "Êtes-vous de sexe masculin ou féminin!"; 
			return False; 
		} 
		//-- BIRTHDATE IS MANDATORY
		if(!$_POST['birthdate']) { 
			$this->Error = "La date de naissance est obligatoire!"; 
			return False; 
		}
		//-- BIRTHDATE IS VALID
		if(!$this->CheckBirthDate( $_POST['birthdate'] )) { 
			$this->Error = "La date de naissance est invalide ou ne suit pas le format AAAA-MM-JJ !"; 
			return False; 
		}
		//-- MAIL ADDRESS IS MANDATORY
		//-- Note : Mail unicity is validated on update.
		if(!$_POST['mail']) { 
			$this->Error = "Le courriel est obligatoire, doit être valide et unique à vous!"; 
			return False; 
		} 


		// Prepare data
		$lAccount = trim($_POST['account']);
		$lMail = trim($_POST['mail']);
		$lAltName = trim($_POST['altname']);
		$lFirstName = trim($_POST['firstname']);
		$lLastName = trim($_POST['lastname']);


		// Account name modification
		if( !$this->Services->ChangeAccountName($lAccount) ) { $this->Error = "Ce nom de compte est déjà utilisé..."; return False; }

		// Gender modification
		if( !$this->Services->ChangeGender($_POST['gender']) ) { $this->Error = "Ça ne va pas! La mise à jour du code de sexe a échouée."; return False; }

		// Mail modification
		if( !$this->Services->ChangeMailAddress($lMail) ) { $this->Error = "Ce courriel est déjà utilisé..."; return False; }

		// Nominatives modification
		if( !$this->Services->ChangeNominatives($lAltName, $lFirstName, $lLastName, $_POST['birthdate']) ) { $this->Error = "Ça ne va pas! La mise à jour de vos données nominatives a échouée."; return False; }

		
		$this->Error = "Une erreur est survenue lors de la modification de vos informations de compte!";
		return True;
	}


	//--PASSWORD MODIFICATION LOGIC--
	public function ChangePassword()
	{
		$this->Error = null;

		// Check if the posted information is valid
		//-- OLD PASSWORD IS MANDATORY
		if(!$_POST['old_password']) { 
			$this->Error = "L'ancien mot de passe est obligatoire!"; 
			return False; 
		}
		//-- NEW PASSWORD IS MANDATORY
		if(!$_POST['new_password']) { 
			$this->Error = "Le nouveau mot de passe est obligatoire!"; 
			return False; 
		}
		//-- PW CONFIRMATION IS MANDATORY
		if(!$_POST['pw-confirm']) { 
			$this->Error = "La confirmation est obligatoire!"; 
			return False; 
		}
		//-- PASSWORD MUST BE EQUAL TO CONFIRMATION
		if($_POST['new_password'] != $_POST['pw-confirm']) { 
			$this->Error = "La confirmation diffère du mot de passe saisi."; 
			return False; 
		} 


		// Call the password modification function
		$r = $this->Services->ChangePassword($_POST['old_password'], $_POST['new_password']);
		
		if($r) {
			$this->Error = null;
			return True;
		}

		$this->Error = "Une erreur est survenue lors de la modification du mot de passe!";
		return False;
	}


	//--COMM PREFS SAVING LOGIC--
	public function SaveCommPreferences()
	{
		$this->Error = null;

		// Check if the posted information is valid
		//-- MAIL USAGE IS MANDATORY
		if(!$_POST['mailusage']) { 
			$this->Error = "Erreur! La sélection de vos préférences courriel ne s'est pas rendu au serveur! Contactez un DBA..."; 
			return False; 
		}
		//-- MAIL USAGE CANNOT BE "NEVER" IF USER IS A GROUP MANAGER
		if($this->Services->GetUser() instanceof Player && $_POST['mailusage'] == "AUCUN" && $this->Services->GetUser()->GetManagedGroupCount()) { 
			$this->Error = "Les responsables de groupe ne peuvent choisir la préférence 'Jamais' pour l'utilisation du courriel."; 
			return False; 
		}
		//-- MAIL USAGE CANNOT BE "NEVER" IF USER HAS SPECIAL ACCESS
		if($_POST['mailusage'] == "AUCUN" && $this->Services->GetUser()->IsAssist()) { 
			$this->Error = "Les personnes possédant des accès privilégiés ne peuvent choisir la préférence 'Jamais' pour l'utilisation du courriel."; 
			return False; 
		}


		// Call the request registration function
		$r = $this->Services->UpdateContactCode($_POST['mailusage']);
		
		if($r) {
			$this->Services->GetUser()->SetContactCode($_POST['mailusage']);
			$this->Error = null;
			return True;
		}

		$this->Error = "La mise à jour des préférences de contact a échouée!";
		return False;
	}


	//--COMPLAINT SAVING AND SENDING LOGIC--
	public function SaveAndSendComplaint()
	{
		$this->Error = null;

		// Check if the posted information is valid
		//-- CATEGORY IS MANDATORY
		if(!$_POST['category']) { 
			$this->Error = "Erreur! La sélection d'un motif est obligatoire."; 
			return False; 
		}
		if(!$_POST['location']) { 
			$this->Error = "Erreur! Nous aurions besoin que vous précisiez le lieu (physique ou virtuel) où se sont produit les évènements principaux."; 
			return False; 
		}
		if(!$_POST['events']) { 
			$this->Error = "Erreur! Votre plainte ne contient aucun évènements."; 
			return False; 
		}


		// Remove potentially harmful tags
		$lDate = strip_tags($_POST['date']);
		$lEvents = strip_tags($_POST['events']);
		$lWitnesses = strip_tags($_POST['witness1'])."\r\n".strip_tags($_POST['witness2'])."\r\n".strip_tags($_POST['witness3'])."\r\n"
							  .strip_tags($_POST['witness4'])."\r\n".strip_tags($_POST['witness5']);
		$lPhone = trim( strip_tags($_POST['phone']) );


		// Call the save n' send functions
		if( !$this->Services->RegisterComplaint($_POST['category'], $lDate, $_POST['location'], $lEvents, $lWitnesses, $lPhone) )
		{
 			$this->Error = "Erreur! Votre plainte n'a pas pu être enregistrée. Contactez le Comité d'éthique par courriel à <b>Ethique@Terres-de-Belenos.com</b>."; 
			return False; 
		};
		
		if( !$this->Services->SendComplaint($_POST['category'], $lDate, $_POST['location'], $lEvents, $lWitnesses, $lPhone) ) 
		{
 			$this->Error = "Erreur! Votre plainte a été enregistrée, mais l'envoi du courriel au Comité d'éthique a échoué. Contactez le Comité d'éthique par courriel à <b>Ethique@Terres-de-Belenos.com.</b>."; 
			return False;
		}

		return True;
	}


} // END of UserController class

?>
