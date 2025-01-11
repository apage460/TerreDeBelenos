<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Registration Controller v1.2 r5 ==			║
║	Implements activity registration control logic.		║
║	Requires registration services.				║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('services/registration-services.class.php');

class RegistrationController
{

protected $Services;

public $Error;

	//--CONSTRUCTOR--
	public function __construct(&$inServices)
	{
		$this->Services = $inServices;
	}


	//--VALIDATE REGISTRATION--
	public function ValidateRegistration()
	{
		$this->Error = null;

		// Check if the posted information is valid
		//-- CHARACTER ID IS MANDATORY
		if(!isset($_POST['characterindex']) || $_POST['characterindex'] == null) { 
			$this->Error = "La sélection d'un personnage est obligatoire!"; 
			return False;
		}

		return True;
	}


	//--PREREGISTER LOGIC--
	public function Preregister( $inAlreadyPaid =False )
	{
		$this->Error = null;
	
		// Check if the posted information is valid
		//-- CHARACTER ID IS MANDATORY	
		if(!isset($_POST['characterindex']) || $_POST['characterindex'] == null) { 
			$this->Error = "La sélection d'un personnage est obligatoire!"; 
			return False;
		}


		// Prepare data
		$lUserID = $this->Services->GetUser()->GetID();
		$lCharacter = $this->Services->GetManager()->GetCharacterByIndex($_POST['characterindex']); 
			if(!$lCharacter) { 	$this->Error = "Aucun personnage pour le choix fait!"; 
						return False;	}
		$lActivity = $this->Services->GetManager()->GetNextActivity();
		$lType = 'REGULIER';
			if( $this->Services->GetUser()->GetAge() < 16 && $this->Services->GetUser()->GetAge() > 11 ) { $lType = 'ENFANT'; }
			elseif( !$this->Services->GetUser()->GetMainActivityCount() ) { $lType = 'NOUVEAU'; }
		$lPrice = $_POST['price'];
		$lYoungChildren = $_POST['youngchildren'];
		$lUsedVoucher = $_POST['usedvoucher'];
		$lUsedKidVoucher = $_POST['usedkidvoucher'];


		// Call registration function
		$r = $this->Services->PreregisterUser( $lUserID, $lActivity, $lCharacter, $lType, $lPrice, $inAlreadyPaid, $lYoungChildren, $lUsedVoucher, $lUsedKidVoucher );

		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors de la préinscription!";
		return False;
	}


	//--UNREGISTER LOGIC--
	public function Unregister()
	{
		$this->Error = null;

		// No validation of input


		// Prepare data
		$lRegistration = $this->Services->GetManager()->GetLastRegistration();

		// Call registration function
		$r = $this->Services->UnregisterUser( $lRegistration );

		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors de la désinscription!";
		return False;
	}


	//--REGISTRATION TRANSFER LOGIC--
	public function Transfer()
	{
		$this->Error = null;

		// No post necessary


		// Prepare data
		$lUserID = $this->Services->GetUser()->GetID();
		$lOldActivityID = $this->Services->GetManager()->GetLastRegistration()['activityid'];
		$lNewActivityID = $this->Services->GetManager()->GetNextActivity()->GetID();


		// Call registration function
		$r = $this->Services->TransferRegistration( $lUserID, $lOldActivityID, $lNewActivityID );

		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors du transfert!";
		return False;
	}


	//--PASS PURCHASE LOGIC--
	public function BuyPass($inPrepaid =False)
	{
		$this->Error = null;

		// Check if the posted informations are valid
		//-- PASS ID IS MANDATORY
		if( !isset($_POST['passindex']) || $_POST['passindex'] == null ) { 
			$this->Error = "Le choix d'une passe est obligatoire!"; 
			return False;
		}


		// call services
		$r = $this->Services->BuyPass( $_POST['passindex'], $inPrepaid );
		
		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors de la transaction!";
		return False;
	}


	//--PASS PURCHASE LOGIC--
	public function BuyPassport()
	{
		$this->Error = null;

		// call services
		$r = $this->Services->BuyPassport();
		
		if($r) { 
			$this->Services->GetUser()->AddFreeActivityVouchers(5);		// This is a temporary solution to avoid reloading the User.
			return True; 
		}

		$this->Error = "Une erreur est survenue lors de la transaction!";
		return False;
	}


	//--REQUEST FIELD SERVICE--
	public function RequestFieldService()
	{
		$this->Error = null;

		// Input checks
		// ACTIVITY IS MANDATORY
		if( !isset($_POST['activityid']) || !isset($_POST['activityname']) ) { 
			$this->Error = "L'activité n'a pas pu être déterminée!"; 
			return False;
		}
		// REQUESTED SERVICE IS MANDATORY
		if( !isset($_POST['service']) ) { 
			$this->Error = "L'objet de la demande n'a pas été sélectionné!"; 
			return False;
		}
		// SERVICE DETAILS ARE MANDATORY
		if( !isset($_POST['text']) || $_POST['text'] == null ) { 
			$this->Error = "Les détails de votre demande n'ont pas été fournis!"; 
			return False;
		}

		// Remove potentially harmful tags
		$lDetails = strip_tags($_POST['text']);

		// send mail
		$r = $this->Services->SaveServiceRequest( $_POST['activityid'], $_POST['service'], $lDetails );
		$s = $this->Services->SendServiceRequestMail( $_POST['activityname'], $_POST['service'], $lDetails );
		
		if($r && $s) { 
			unset($_POST['text']);
			return True; 
		}

		$this->Error = "Une erreur est survenue lors de la commande!";
		return False;
	}


	//--REQUEST FIELD SERVICE--
	public function SubmitNewspaperArticle()
	{
		$this->Error = null;

		// Input checks
		// ACTIVITY IS MANDATORY
		if( !isset($_POST['activityid']) || !isset($_POST['activityname']) ) { 
			$this->Error = "L'activité n'a pas pu être déterminée!"; 
			return False;
		}
		// TITLE IS MANDATORY
		if( !isset($_POST['title']) || !$_POST['title'] ) { 
			$this->Error = "Votre article doit posséder un titre, même s'il est déjà inclus dans le fichier."; 
			return False;
		}
		// SIGNATURE IS MANDATORY
		if( !isset($_POST['signature']) || !$_POST['signature'] ) { 
			$this->Error = "Votre article doit posséder une signature, même si elle est déjà incluse dans le fichier."; 
			return False;
		}

		// ATTACHED FILE CHECK
		if( 	( !isset($_FILES['attachedfile']) || !$_FILES['attachedfile']['name'] ) && 
			( !isset($_POST['okwithnofile']) || $_POST['okwithnofile'] != 1 ) ) { 
			$this->Error = "Aucun fichier n'a été sélectionné! Si vous avez mis votre article dans les instructions supplémentaires, cochez la case « Je n'ai pas de fichier. »"; 
			return False;
		}

		// NO FILE CHECK
		if( isset($_POST['okwithnofile']) && ( !isset($_POST['text']) || !$_POST['text'] ) )  { 
			$this->Error = "Vous avez choisi l'option sans fichier, mais n'avez pas fourni votre texte dans les instructions supplémentaires."; 
			return False;
		}

		// Remove potentially harmful tags
		$lDetails = strip_tags($_POST['text']);

		// Prepare file upload
		$lTarget = NEWS_UPLOAD_DIR . $this->Services->GetUser()->GetID() . '/'. basename($_FILES['attachedfile']['name']);
		$lFileType = strtolower(pathinfo($lTarget,PATHINFO_EXTENSION));

		// Uploaded file checks
		// FILES ALREADY EXISTS
		if( file_exists($lTarget) ) {
			# Allowed. We replace the old file.
		}

		// FILE TYPE
		if( $_FILES['attachedfile']['name'] && $lFileType != "pdf" && $lFileType != "docx" && $lFileType != "odt" && $lFileType != "txt" && $lFileType != "jpg" && $lFileType != "png") {
			$this->Error = "Seuls les fichiers PDF, DOCX, ODT, TXT, JPG et PNG sont permis. Imprimez votre fichier en PDF ou utilisez les fonctionnalités gratuites de Google Docs au besoin.";
			return False;
		}

		// FAKE FILES -- Soon...

		// FILE SIZE
		if( $_FILES["attachedfile"]["size"] > UPLOAD_MAX_SIZE_IN_BYTES ) {
			$this->Error = "Votre fichier est trop volumineux. Vérifiez que sa taille en octets ne dépassent pas la limite indiquée dans les consignes.";
			return False;
		}

		// upload and send mail
		$attachment = False; $uploaded = False; $mailed = False;

		if( $_FILES['attachedfile']['name'] ) { $uploaded = $this->Services->UploadFileToServer(); $attachment = True; }
		else { $uploaded = True; }
		
		if($uploaded) { $mailed = $this->Services->SendArticleSubmissionMail( $_POST['activityname'], $_POST['title'], $_POST['category'], $_POST['signature'], 
			$attachment, $lDetails, $_POST['revisionapproved'] );}
		
		if($uploaded && $mailed) { 
			unset($_POST['title']);
			unset($_POST['category']);
			unset($_POST['signature']);
			unset($_FILES['attachedfile']);
			unset($_POST['okwithnofile']);
			unset($_POST['revisionapproved']);
			unset($_POST['text']);
			return True; 
		}

		$this->Error = "Une erreur est survenue lors de la soumission!";
		return False;
	}


} // END of RegistrationController class

?>
