<?php
define('TIMEZONE', 'America/Montreal');


/*
╔══SERVICE CLASS════════════════════════════════════════════════╗
║	== Authenticator Services v1.2 r5 ==			║
║	Manages credential authentication and access logging.	║
║	Requires DAL and generates Users. Uses MySQL queries.	║
╚═══════════════════════════════════════════════════════════════╝
*/

require_once('includes/swiftmailer/lib/swift_required.php');	// For mailing

include_once('services/database.class.php'); 		// Data Access Layer
include_once('services/user-services.class.php'); 	// User services
include_once('models/user.class.php'); 			// User model

class Authenticator
{

const MAX_FAILED_ATTEMPTS = 5;
const LIFESPAN = 7200; 				// 2 hours in seconds

private $DAL;
private $UserServices;

private $LogTime;
private $Authenticated;				// True or False
private $Attempts;
private $TempLock;				// Note: The goal of this lock is not to disable the account. It only serves to block bots.

public $Error;

	//--CONSTRUCTOR--
	public function __construct($inDataAccessLayer)
	{
		$this->DAL = $inDataAccessLayer;
		$this->UserServices = new UserServices($inDataAccessLayer);

		$this->Authenticated = False;
		$this->Attempts = 0;
		$this->TempLock = False;

		date_default_timezone_set(TIMEZONE);
	}


	//--AUTHENTICATE NEW USER--
	public function Authenticate($inAccount, $inPassword)
	{
		// Stop if the temporary lock is on
		if($this->TempLock) { $this->Error = 'Authentication is locked!'; return False; }


		// Verify user's credentials
		$this->Authenticated = $this->UserServices->Authenticate($inAccount, $inPassword);

		// Log attempt
		$this->LogAccess($inAccount, $this->Authenticated);

		return $this->Authenticated;
	}


	//--RE-AUTHENTICATE CONNECTED USER--
	public function Reauthenticate($inPassword)
	{
		// Stop if the temporary lock is on
		if($this->TempLock) { $this->Error = 'Authentication is locked!'; return False; }


		// Verify user's credentials
		$this->Authenticated = $this->UserServices->Reauthenticate($inPassword);


		// Log attempt
		$this->LogAccess($inAccount, $this->Authenticated);

		return $this->Authenticated;
	}


	//--LOG CONNECTION ATTEMPT--
	private function LogAccess($inAccount, $inResult)
	{
		// Process tries-n'-fails
		if($inResult) 
		{ 
			$this->LogTime = new DateTime("now"); 
			$this->Attempts = 0;
		}
		else
		{
			$this->LogTime = null; 
			$this->Attempts++;
			if($this->Attempts >= self::MAX_FAILED_ATTEMPTS) { $this->TempLock = True; }
		}


		// Write it in the database
		$lQuery = 	"INSERT INTO db_indiv.journal_acces (IP, Compte, DateAcces, Resultat) 
				 VALUES (:ip, :account, sysdate(), :result )";
		$lResult = ($inResult ? "Succes" : "Echec");

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":ip", $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
			$this->DAL->Bind(":account", $inAccount, PDO::PARAM_STR);
			$this->DAL->Bind(":result", $lResult, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		if($r) { return True; }
		
		$this->Error = "Erreur de journalisation de l'accès!"; 
		return False;
	}


	//--GET ACCOUNT NAME FROM MAIL ADDRESS
	public function GetAccountFromMail( $inMail )
	{

		// Ask the database for corresponding user.
		$lQuery = 	"SELECT ind.Compte, ind.Prenom, ind.Nom, ind.Tuteur 
				 FROM db_indiv.individus ind 
				 WHERE ind.Courriel = :mail";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":mail", $inMail, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		// If there's only one user, set attributes and return true.
		if( $this->DAL->GetRowCount() == 1 ) {
			$account = [
				'name'		=> 		$r[0]['Compte'],
				'username'	=> 		$r[0]['Prenom']." ".$r[0]['Nom'],
				'tutor'		=> 		$r[0]['Tuteur']
			];

			return $account;
		}
		
		return False;
	}


	//--GET TUTOR'S MAIL ADDRESS
	public function GetTutorMail( $inTutorAccount )
	{

		// Ask the database for corresponding user.
		$lQuery = 	"SELECT ind.Courriel 
				 FROM db_indiv.individus ind 
				 WHERE ind.Compte = :account";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":account", $inTutorAccount, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		// If there's only one user, set attributes and return true.
		if( $this->DAL->GetRowCount() == 1 ) { return $r[0]['Courriel']; }
		
		return False;
	}


	//--SEND
	public function SendTemporaryPassword( $inAccount, $inPassword, $inUserMail, $inTutorMail = NULL )
	{
		// Define mail transport
		$transport = Swift_SmtpTransport::newInstance("smtp.gmail.com", 465, "ssl")
			->setUsername(GMAIL_USERNAME)
			->setPassword(GMAIL_PASSWORD);

		$mailer = Swift_Mailer::newInstance($transport);

		$message = Swift_Message::newInstance('Récupération de votre compte')
		  ->setFrom(array('TI@Terres-de-Belenos.com' => 'BD bélénoise'))
		  ->setTo(array($inUserMail => $inAccount['username']))
		  ->setBody('Bonjour '.$inAccount['username'].',<br />
		  	<br />
		  	Vous recevez ce courriel parce que vous avez demandé la récupération de vos informations de connexion à la Base de données bélénoise. Voici donc votre identifiant de compte, ainsi que votre nouveau mot de passe.<br />
		  	<br />
		  	<b>Compte : </b>'.$inAccount['name'].'<br />
		  	<b>Nouveau mot de passe : </b>'.$inPassword.'<br />
		  	<br />
		  	Merci et bonne journée!<br />
		  	<br />
		  	<i>- L\'équipe informatique des Terres de Bélénos</i>', 'text/html');

		if( $inTutorMail ) { $message->setCC(array($inTutorMail)); }

		return $mailer->send($message);	
	}


	//--SAVE NEW TEMPORARY PASSWORD FOR ACCOUNT
	public function SaveTemporaryPassword( $inAccount, $inPassword )
	{
		// Encrypt password using SHA-256
		$lSalted = hash("sha256" , $inPassword.SALT);

		// Ask the database for corresponding user.
		$lQuery = 	"UPDATE db_indiv.individus
				 SET MotDePasse = :pw
				 WHERE Compte = :account;";


		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":account", $inAccount, PDO::PARAM_STR);
			$this->DAL->Bind(":pw", $lSalted, PDO::PARAM_STR);
		return $this->DAL->FetchResult();
	}


	//--GENERATE A RANDOM STRING
	public function GenerateRandomString( $inStringLength = 10 ) 
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
 		$randomString = '';

		for ($i = 0; $i < $inStringLength; $i++) {
        		$randomString .= $characters[rand(0, $charactersLength - 1)];
		}

		return $randomString;
	}


	//--GET USER OBJECT--
	public function GetUser()
	{
		return $this->UserServices->GetUser();
	}


	//--PRINT OBJECT FOR DEBUG PURPOSES--
	public function PrintHTML()
	{
		echo '<DIV id="debug">';
		echo '<b><u>Authenticator</u></b><br />';
		if(isset($this->DAL)) {echo 'DAL is set...<br />';}
		if(isset($this->User)) {echo 'User is set...<br />';}
		echo '-------<br />';
		echo 'Log Time: ' . $this->LogTime->format('Y-m-d H:i:s') . '<br />';
		echo 'Logging Result: ' . ($this->Authenticated ? 'Success' : 'Failure') . '<br />';
		echo 'Attempts: ' . $this->Attempts . '<br />';
		echo 'Locked?: ' . ($this->TempLock ? 'Oui' : 'Non') . '<br />';
		echo 'Remaining Life Time: ' . $this->GetRemainingLife() . '<br />';
		echo '-------<br />';
		echo 'Last error: ' . $this->Error . '<br />';
		echo '</DIV>';
	}
} // END of Authenticator class

?>
