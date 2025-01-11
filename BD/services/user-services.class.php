<?php
define('SALT', 'Chad4Ever');

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== User Services v1.2 r19 ==				║
║	Manages User's Account and Game Data.			║
║	Non-serializable. Requires DAL. Uses MySQL queries.	║
╚═══════════════════════════════════════════════════════════════╝
*/

require_once('includes/swiftmailer/lib/swift_required.php');	// For mailing

include_once('services/database.class.php'); 		// Data Access Layer
include_once('models/user.class.php'); 			// User definition

class UserServices
{

protected $DAL;
protected $User;

public $Error;

	//--CONSTRUCTOR--
	public function __construct($inDataAccessLayer, $inUser =NULL)
	{
		$this->DAL = $inDataAccessLayer;

		if( isset($inUser) ) {$this->User = $inUser;}
	}


	//--GET/SET FUNCTIONS--
	public function GetUser() { return $this->User; }
	public function SetUser($inUser) { $this->User = $inUser; }


	//--AUTHENTICATE USER--
	public function Authenticate($inAccount, $inPassword)
	{
		// Encrypt password using SHA-256
		$lSalted = hash("sha256" , $inPassword.SALT);

		
		// Ask the database for corresponding user.
		$lQuery = 	"SELECT ind.Id, ind.Compte, ind.NiveauAcces, ind.CodeEtat, ind.Sexe, ind.DateNaissance, ind.Pseudo, ind.Prenom, ind.Nom, ind.Courriel, ind.Tuteur, ind.CodeContact
				 FROM db_indiv.individus ind 
				 WHERE ind.Compte = :account AND ind.MotDePasse = :pw";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":account", $inAccount, PDO::PARAM_STR);
			$this->DAL->Bind(":pw", $lSalted, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		// If there's only one user, set attributes and return true.
		if( $this->DAL->GetRowCount() == 1 ) {
			$attributes = [
				'id'			=> 	$r[0]['Id'],
				'account'		=> 	$r[0]['Compte'],
				'accesslevel' 		=> 	$r[0]['NiveauAcces'],
				'status'		=> 	$r[0]['CodeEtat'],
				'altname' 		=> 	$r[0]['Pseudo'],
				'firstname' 		=> 	$r[0]['Prenom'],
				'lastname' 		=> 	$r[0]['Nom'],
				'gender' 		=> 	$r[0]['Sexe'],
				'dateofbirth' 		=> 	$r[0]['DateNaissance'],
				'mail' 			=> 	$r[0]['Courriel'],
				'tutor'			=> 	$r[0]['Tuteur'],
				'contactcode'		=>	$r[0]['CodeContact']
			];

			$this->User = new User($attributes);
			$this->GetVolunteeringPoints();

			return True;
		}
		
		return False;
	}


	//--GET USER VOLUNTEERING POINTS--
	public function GetPermissions()
	{
		// User must be defined
		if( !isset($this->User) ) { $this->Error = "GetPermissions : no user!"; return False; }

		
		// Ask the database ...
		$lQuery = 	"SELECT Acces
				 FROM db_indiv.acces 
				 WHERE IdIndividu = :accountid
				   AND CodeUnivers = :universecode ;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":accountid", $this->User->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		// Convert to list
		$lPermissionList = array(); # Array must start at index 1.
		foreach ($r as $i => $access) { $lPermissionList[$i+1] = $access['Acces']; }

		$this->User->SetPermissions($lPermissionList);
		return True;
	}


	//--GET USER VOLUNTEERING POINTS--
	public function GetVolunteeringPoints()
	{
		// User must be defined
		if( !isset($this->User) ) { $this->Error = "GetVolunteeringPoints : no user!"; return False; }

		
		// Ask the database ...
		$lQuery = 	"SELECT ben.Id, ben.Raison, ben.Points
				 FROM db_indiv.benevolat ben 
				 WHERE ben.IdIndividu = :accountid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":accountid", $this->User->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();

		// Convert to point list
		$lPointList = array();
		foreach ($r as $points) {
			$lPointList[] = [
				'id'		=>	$points['Id'],
				'reason'	=>	$points['Raison'],
				'points'	=>	$points['Points']
			];
		}

		$this->User->SetVolunteeringPoints($lPointList);
		return True;
	}


	//--RE-AUTHENTICATE USER--
	public function Reauthenticate($inPassword)
	{
		if( !isset($this->User) ) { $this->Error = "Technical error : Trying to reauthenticate an unknown user!"; return False; }

		// Encrypt password using SHA-256
		$lSalted = hash("sha256" , $inPassword.SALT);

		
		// Ask the database for corresponding user.
		$lQuery = 	"SELECT ind.Id 
				 FROM db_indiv.individus ind 
				 WHERE ind.Compte = :account AND ind.MotDePasse = :pw";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":account", $this->User->GetAccountName(), PDO::PARAM_STR);
			$this->DAL->Bind(":pw", $lSalted, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		// If there's only one user found, return true.
		if( $this->DAL->GetRowCount() == 1 ) { return True; }
		
		return False;
	}


	//--CHECK IF USERNAME EXISTS--
	public function AccountNameExists($inAccount)
	{
		// If it exists in the databse, then it's taken.
		$lQuery = "SELECT ind.Compte FROM db_indiv.individus ind WHERE ind.Compte = :account";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":account", $inAccount, PDO::PARAM_STR);
		$this->DAL->FetchResult();


		// Return True if the account exists
		if ($this->DAL->GetRowCount() > 0) { return True; }
		return False;
	}


	//--CHECK IF ALT NAME EXISTS--
	public function AltNameExists($inName)
	{
		// Don't check if there is no name.
		if( !$inName ) { return False; }

		// If it exists in the databse, then it's taken.
		$lQuery = "SELECT ind.Compte FROM db_indiv.individus ind WHERE ind.Pseudo = :altname";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":altname", $inName, PDO::PARAM_STR);
		$this->DAL->FetchResult();


		// Return True if the account exists
		if ($this->DAL->GetRowCount() > 0) { return True; }
		return False;
	}


	//--CHECK IF USERNAME EXISTS--
	public function AlreadyHasAccount($inFirstName, $inLastName, $inDateOfBirth)
	{
		// If it exists in the databse, then it's taken.
		$lQuery = "SELECT ind.Compte FROM db_indiv.individus ind 
			   WHERE ind.Prenom = :firstname
			     AND ind.Nom = :lastname
			     AND ind.DateNaissance = :dateofbirth";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":firstname", $inFirstName, PDO::PARAM_STR);
			$this->DAL->Bind(":lastname", $inLastName, PDO::PARAM_STR);
			$this->DAL->Bind(":dateofbirth", $inDateOfBirth, PDO::PARAM_STR);
		$this->DAL->FetchResult();


		// Return True if the account exists
		if ($this->DAL->GetRowCount() > 0) { return True; }
		return False;
	}


	//--CHECK IF USERNAME EXISTS--
	public function MailAddressExists($inMailAddress)
	{
		// If it exists in the databse, then it's taken.
		$lQuery = "SELECT ind.Compte FROM db_indiv.individus ind WHERE ind.Courriel = :mail";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":mail", $inMailAddress, PDO::PARAM_STR);
		$this->DAL->FetchResult();


		// Return True if the account exists
		if ($this->DAL->GetRowCount() > 0) { return True; }
		return False;
	}


	//--REGISTER A NEW USER--
	public function Register($inAccount, $inPassword, $inAltName, $inFirstName, $inLastName, $inGender, $inDateOfBirth, $inMail, $inTutor =NULL)
	{
		if(!$inTutor) { $inTutor =NULL;}	# No blanks in the database please. ;)
		$inMail = trim($inMail);
		if(!$inMail)  { $inMail =NULL;}		# No blanks in the database please. ;)

		// Encrypt password using SHA-256
		$lSalted = hash("sha256" , $inPassword.SALT);

		
		// Insert new user in the database.
		$lQuery = 	"INSERT INTO db_indiv.individus (Compte, MotDePasse, NiveauAcces, CodeEtat, DateCreation, Prenom, Nom, Sexe, DateNaissance, Courriel, Tuteur) 
				 VALUES (:account, :pw, 1, 'ACTIF', sysdate(), :firstname, :lastname, :gender, str_to_date(:birthdate, '%Y-%m-%d'), :mail, :tutor )";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":account", trim($inAccount), PDO::PARAM_STR);
			$this->DAL->Bind(":pw", $lSalted, PDO::PARAM_STR);
			$this->DAL->Bind(":firstname", trim($inFirstName), PDO::PARAM_STR);
			$this->DAL->Bind(":lastname", trim($inLastName), PDO::PARAM_STR);
			$this->DAL->Bind(":gender", $inGender, PDO::PARAM_STR);
			$this->DAL->Bind(":birthdate", $inDateOfBirth, PDO::PARAM_STR);
			$this->DAL->Bind(":mail", $inMail, PDO::PARAM_STR);
			$this->DAL->Bind(":tutor", $inTutor, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		$s = True;
		if( $inAltName ) {
			$lQuery = 	"UPDATE db_indiv.individus
					 SET Pseudo = :altname
					 WHERE Compte = :account";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":account", trim($inAccount), PDO::PARAM_STR);
				$this->DAL->Bind(":altname", trim($inAltName), PDO::PARAM_STR);
			$s = $this->DAL->FetchResult();
		}

		if($r && $s) {
			$attributes = [
				'id'		=> 		0,
				'account'	=> 		trim($inAccount),
				'accesslevel' 	=> 		1,
				'status'	=> 		'AC',
				'altname' 	=> 		trim($inAltName),
				'firstname' 	=> 		$inFirstName,
				'lastname' 	=> 		$inLastName,
				'gender' 	=> 		$inGender,
				'dateofbirth' 	=> 		$inDateOfBirth,
				'mail' 		=> 		$inMail,
				'tutor'		=> 		$inTutor
			];

			$this->User = new User($attributes);
			return True;
		}
		
		return False;
	}


	//--MODIFFY THE USER'S PASSWORD--
	public function ChangePassword($inOldPassword, $inNewPassword)
	{
		//Verify there's a user
		if( !isset($this->User) ) { $this->Error = "Technical error : No user!"; return False; }

		//Verify that the old password fits the current account
		if ( !$this->Reauthenticate($inOldPassword) ) { return False; }


		// Update the hashed password in the database
		$lSalted = hash("sha256" , $inNewPassword.SALT);
		$lQuery = 	"UPDATE db_indiv.individus ind 
				 SET ind.MotDePasse = :pw
				 WHERE ind.Id = :id";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":id", $this->User->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":pw", $lSalted, PDO::PARAM_STR);
		return $this->DAL->FetchResult();
	}


	//--WRITE A NOTE IN USER'S JOURNAL--
	public function WriteNote($inNote)
	{
		//Verify there's a user
		if( !isset($this->User) ) { $this->Error = "Technical error : No user!"; return False; }

		$lQuery = 	"INSERT INTO db_indiv.remarques (IdIndividu, Message, Type, DateCreation)
				 VALUES ( :userid, :message, 'MAJ', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":message", $inNote, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		return True;
	}


	//--CHANGE THE ACCOUNT'S NAME--
	public function ChangeAccountName($inAccount)
	{
		// Verify there's a user
		if( !isset($this->User) ) { $this->Error = "Technical error : No user!"; return False; }

		// Skip if account name hasn't changed
		if( $inAccount == $this->User->GetAccountName() ) { return True; }

		// Check if the new account name can be used
		if( $this->AccountNameExists($inAccount) ) { $this->Error = "Ce nom de compte est déjà utilisé..."; return False; }
		

		// Update the hashed password in the database
		$lQuery = 	"UPDATE db_indiv.individus ind 
				 SET ind.Compte = :account
				 WHERE ind.Id = :id ;

				 UPDATE db_indiv.individus ind 
				 SET ind.Tuteur = :account
				 WHERE ind.Tuteur = :oldaccount ;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":id", $this->User->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":account", $inAccount, PDO::PARAM_STR);
			$this->DAL->Bind(":oldaccount", $this->User->GetAccountName(), PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		// Update User if it worked.
		if($r) {
			$this->User->SetAccountName($inAccount);
			return True;
		}

		return False;
	}


	//--CHANGE THE ACCOUNT'S GENDER--
	public function ChangeGender($inGender)
	{
		//Verify there's a user
		if( !isset($this->User) ) { $this->Error = "Technical error : No user!"; return False; }

		// Skip if account name hasn't changed
		if( $inGender == $this->User->GetGender() ) { return True; }

		// Commit to database
		$lQuery = 	"UPDATE db_indiv.individus ind 
				 SET ind.Sexe = :gender
				 WHERE ind.Id = :id";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":id", $this->User->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":gender", $inGender, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		// Update User if it worked.
		if($r) {
			$this->User->SetGender($inGender);
			return True;
		}

		return False;
	}


	//--CHANGE THE ACCOUNT'S MAIL--
	public function ChangeMailAddress($inMail)
	{
		//Verify there's a user
		if( !isset($this->User) ) { $this->Error = "Technical error : No user!"; return False; }

		// Skip if account name hasn't changed
		if( $inMail == $this->User->GetMailAddress() ) { return True; }

		// Check if the new mail can be used
		if( $this->MailAddressExists($inMail) ) { $this->Error = "Mail address already exists!"; return False; }
		
		// Commit to database
		$lQuery = 	"UPDATE db_indiv.individus ind 
				 SET ind.Courriel = :mail
				 WHERE ind.Id = :id";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":id", $this->User->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":mail", $inMail, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		// Update User if it worked.
		if($r) {
			$this->User->SetMailAddress($inMail);
			return True;
		}

		return False;
	}


	//--CHANGE THE ACCOUNT'S NOMINATIVES--
	public function ChangeNominatives($inAltName, $inFirstName, $inLastName, $inDateOfBirth)
	{
		//Verify there's a user
		if( !isset($this->User) ) { $this->Error = "Technical error : No user!"; return False; }

		// Commit to database
		$lQuery = 	"UPDATE db_indiv.individus ind 
				 SET ind.Pseudo = :altname, ind.Prenom = :firstname, ind.Nom = :lastname, ind.DateNaissance = str_to_date(:birthdate, '%Y-%m-%d')
				 WHERE ind.Id = :id";
		if( !$inAltName ) {
			$lQuery = "UPDATE db_indiv.individus ind 
				   SET ind.Pseudo = NULL, ind.Prenom = :firstname, ind.Nom = :lastname, ind.DateNaissance = str_to_date(:birthdate, '%Y-%m-%d')
				   WHERE ind.Id = :id";
		}

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":id", $this->User->GetID(), PDO::PARAM_INT);
			if( $inAltName ) { $this->DAL->Bind(":altname", $inAltName, PDO::PARAM_STR); }
			$this->DAL->Bind(":firstname", $inFirstName, PDO::PARAM_STR);
			$this->DAL->Bind(":lastname", $inLastName, PDO::PARAM_STR);
			$this->DAL->Bind(":birthdate", $inDateOfBirth, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		// Update User and write note if it worked.
		if($r) {
			$lName = $this->User->GetFirstName()." ".$this->User->GetLastName()." (".$this->User->GetBirthDate().")";

			$this->WriteNote("Mise à jour du compte de ".$lName);

			$this->User->SetAltName($inAltName);
			$this->User->SetFirstName($inFirstName);
			$this->User->SetLastName($inLastName);
			$this->User->SetBirthDate($inDateOfBirth);

			return True;
		}

		return False;
	}


	//--UPDATE USER'S CONTACT CODE--
	public function UpdateContactCode($inContactCode)
	{
		try {
			//Verify there's a user
			if( !isset($this->User) ) { $this->Error = "Technical error : No user!"; return False; }

			// Skip if account already has the same level
			if( $inContactCode == $this->User->GetContactCode() ) { return True; }

			// Commit to database
			$lQuery = 	"UPDATE db_indiv.individus 
					 SET CodeContact = :contactcode
					 WHERE Id = :userid ;";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
				$this->DAL->Bind(":contactcode", $inContactCode, PDO::PARAM_STR);
			$r = $this->DAL->FetchResult();

			if($r) { return True; }

			return False;

		} catch (PDOException $e) {
			$this->Error = $e->getMessage();
			return False;
		}
	}


	//--ADD COMPLAINT TO DATABASE--
	public function RegisterComplaint($inCategory, $inDate, $inLocation, $inEvents, $inWitnesses, $inPhone =NULL)
	{
		try {
			//Verify there's a user
			if( !isset($this->User) ) { $this->Error = "Technical error : No user!"; return False; }


			// Commit to database
			$lQuery = 	"INSERT INTO db_indiv.plaintes (IdIndividu, Motif, DateEvenements, Lieu, Evenements, Temoins, Telephone, DateCreation)
					 VALUES (:userid, :category, :eventsdate, :location, :events, :witnesses, :phone, sysdate() )";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
				$this->DAL->Bind(":category", $inCategory, PDO::PARAM_STR);
				$this->DAL->Bind(":eventsdate", $inDate, PDO::PARAM_STR);
				$this->DAL->Bind(":location", $inLocation, PDO::PARAM_STR);
				$this->DAL->Bind(":events", $inEvents, PDO::PARAM_STR);
				$this->DAL->Bind(":witnesses", $inWitnesses, PDO::PARAM_STR);
				$this->DAL->Bind(":phone", $inPhone, PDO::PARAM_STR);
			$r = $this->DAL->FetchResult();

			// Mail is NOT sent here, so both processes stay independents.
			if($r) { return True; }

			return False;

		} catch (PDOException $e) {
			$this->Error = $e->getMessage();
			return False;
		}
	}


	//--SEND COMPLAINT MAIL
	public function SendComplaint($inCategory, $inDate, $inLocation, $inEvents, $inWitnesses, $inPhone =NULL)
	{
		// Prepare data
		$lUserName = $this->User->GetFullName();
		$lAltName = $this->User->GetAltName();
			if( $lAltName ) { $lAltName = ' ('.$lAltName.')'; }
		$lAccount = $this->User->GetAccountName();
		$lMail = $this->User->GetMailAddress();
		$lPhone = "Aucun numéro fourni.";
			if($inPhone) { $lPhone = $inPhone; };

		$lCategory = "Indéterminé";
			    if( $inCategory == "AUTORITE" ) 	{ $lCategory = "Abus d'autorité"; }
			elseif( $inCategory == "DISCRIM" ) 	{ $lCategory = "Discrimination"; }
			elseif( $inCategory == "HPSYCHO" ) 	{ $lCategory = "Harcèlement psychologique"; }
			elseif( $inCategory == "HSEXUEL" ) 	{ $lCategory = "Harcèlement sexuel"; }
			elseif( $inCategory == "INTIMID" ) 	{ $lCategory = "Intimidation"; }
			elseif( $inCategory == "TRICHE" ) 	{ $lCategory = "Triche"; }
			elseif( $inCategory == "VIOLENCE" ) 	{ $lCategory = "Violence"; }
			elseif( $inCategory == "AUTRE" ) 	{ $lCategory = "Autre motif"; }

		$lLocation = "Indéterminé";
			    if( $lLocation == "TERRAIN" ) 	{ $lLocation = "Sur le terrain de Bélénos"; }
			elseif( $lLocation == "HORSBELE" ) 	{ $lLocation = "Hors du terrain<"; }
			elseif( $lLocation == "FACEBOOK" ) 	{ $lLocation = "Sur Facebook"; }
			elseif( $lLocation == "DISCORD" ) 	{ $lLocation = "Sur Discord"; }
			elseif( $lLocation == "COURRIEL" ) 	{ $lLocation = "Par courriel"; }
			elseif( $lLocation == "INTERNET" ) 	{ $lLocation = "Ailleurs sur le Net"; }


		// Define mail transport
		$transport = Swift_SmtpTransport::newInstance("smtp.gmail.com", 465, "ssl")
			->setUsername(GMAIL_USERNAME)
			->setPassword(GMAIL_PASSWORD);

		$mailer = Swift_Mailer::newInstance($transport);

		$message = Swift_Message::newInstance('Plainte officielle - '.$lUserName.' - '.$lCategory)
		  ->setFrom(array('BD@Terres-de-Belenos.com' => 'BD bélénoise'))
		  ->setTo(array('Ethique@Terres-de-Belenos.com' => 'Comité d\'éthique'))
		  ->setCC(array( $lMail => $lUserName ), true)
		  ->setBody('<b><u>PLAINTE OFFICIELLE</u></b><br />
		  	<br />
		  	<b>Plaignant</b><br/>
		  	'.$lUserName.$lAltName.'<br/>
		  	'.$lMail.'<br>
		  	'.$lPhone.'<br/>
		  	<br />
		  	<b>Compte bélénois: </b>'.$lAccount.'<br />
		  	<br />
		  	<b>Motif : </b>'.$lCategory.'<br />
		  	<b>Date de début des évènements : </b>'.$inDate.'<br />
		  	<b>Lieu principal </b>: '.$lLocation.'<br />
		  	<br/>
		  	<b>Récit des évènements</b><br />
		  	'.nl2br($inEvents).'<br />
		  	<br />
		  	<b>Témoins</b><br/>
		  	'.nl2br($inWitnesses).'<br />
		  	<br/>
		  	Merci de prendre contact avec le ou la plaignante le plus tôt possible!<br />
		  	<br />
		  	<b>Message automatisé des <i>Terres de Bélénos</i>.</b>', 'text/html');

		#$message->setCC(NULL);

		return $mailer->send($message);	
	}


} // END of UserServices class

?>
