<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Game Controller v1.2 r0 ==				║
║	Implements game management services control logic.	║
║	Requires game management services.			║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('services/game-services.class.php');

class GameController
{

protected $Services;

public $Error;

	//--CONSTRUCTOR--
	public function __construct(&$inServices)
	{
		$this->Services = $inServices;
	}


	//--ACCEPT A MISSIVE--
	public function AcceptMissive($inMissiveID)
	{
		$this->Error = null;

		// Call type-specific service
		if( $this->Services->UpdateMissiveStatus($inMissiveID, 'ACTIF') ) { return True; }

		$this->Error = "Une erreur est survenue lors de la mise à jour de la missive!";
		return False;
	}


	//--REFUSE A MISSIVE--
	public function RefuseMissive($inMissiveID)
	{
		$this->Error = null;

		// Call type-specific service
		if( $this->Services->UpdateMissiveStatus($inMissiveID, 'REFUS') ) { return True; }

		$this->Error = "Une erreur est survenue lors de la mise à jour de la missive!";
		return False;
	}


	//--UPLOAD A NPC'S ANSWER TO A MISSIVE--
	public function UploadNPCAnswer($inMissiveID)
	{
		$this->Error = null;

		// Check if the posted information is valid
		//--ATTACHED FILE IS MANDATORY
		if( !isset($_FILES['attachedfile']) || !$_FILES['attachedfile']['name'] ) { 
			$this->Error = "Aucun fichier n'a été sélectionné ou celui-ci n'est pas valide. Vérifiez la taille et le contenu de votre fichier."; 
			return False;
		}

		// Prepare file upload
		$lMissive = $this->Services->GetManager()->GetMissiveByID($inMissiveID);
		$lActivityName = $lMissive->GetActivity()->GetName();

		$lFileName = $lMissive->GetSenderName().' - Réponse de '.$lMissive->GetRecipientName().'.pdf';
		$lFilePath = LETTERS_UPLOAD_DIR . $lActivityName;
		$lTarget = $lFilePath . '/'.$lFileName;
		$lFileType = strtolower(pathinfo(basename($_FILES['attachedfile']['name']),PATHINFO_EXTENSION));

		// Uploaded file checks
		// FILES ALREADY EXISTS
		if( file_exists($lTarget) ) {
			# Allowed. We replace the old file.
		}
		// FILE TYPE
		if( $_FILES['attachedfile']['name'] && $lFileType != "pdf" ) {
			$this->Error = "Seuls les fichiers PDF sont permis. Imprimez votre fichier en PDF ou utilisez les fonctionnalités gratuites de Google Docs au besoin.";
			return False;
		}
		// FILE SIZE
		if( $_FILES["attachedfile"]["size"] > UPLOAD_MAX_SIZE_IN_BYTES ) {
			$this->Error = "Votre fichier est trop volumineux. Vérifiez que sa taille en octets ne dépassent pas la limite indiquée dans les consignes.";
			return False;
		}

		// Call type-specific service
		$r = $this->Services->UploadMissiveFileToServer( $lFilePath, $lFileName );
		if($r) {
			$lAnswerID = $this->Services->RegisterNPCAnswer( $lMissive, $lFileName );
			$this->Services->UpdateMissiveAnswerID( $lMissive->GetID(), $lAnswerID );
			
			if( !$lAnswerID ) { $this->Error = "Une erreur est survenue lors de l'enregistrement de la missive!"; return False; }
			return True; 
		} 

		$this->Error = "Une erreur est survenue lors du téléchargement la missive!";
		return False;
	}


	//--ASSIGN CURRENT USER AS GHOST WRITER--
	public function AssignCurrentWriter()
	{
		$this->Error = null;

		// Check if the posted informations are valid
		//-- NAME IS MANDATORY
		if( !isset($_POST['assign-writer']) )  { 
			$this->Error = "L'identifiant de la missive n'a pas été fourni!"; 
			return False; 
		}

		// Call type-specific service
		if( $this->Services->UpdateGhostWriter( $_POST['assign-writer'], $this->Services->GetUser()->GetID() ) ) { return True; }

		$this->Error = "Une erreur est survenue lors de la mise à jour de la missive!";
		return False;
	}


} // END of GameController class

?>
