<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Character Controller v1.2 r15 ==			║
║	Implements character management control logic.		║
║	Requires character services.				║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('models/character.class.php');
include_once('services/character-services.class.php');

class CharacterController
{

private $Services;

public $Error;

	//--CONSTRUCTOR--
	public function __construct(&$inServices)
	{
		$this->Services = $inServices;
	}


	//--COLLECT DATA FOR NEW CHARACTER--
	public function CollectNewCharacterData()
	{
		$this->Error = null;

		// Check if active character is a new character
		if( !($this->Services->GetCharacter() instanceof NewCharacter) ) { $this->Error = "Ceci n'est pas un nouveau personnage!"; return False; }


		// STEP-BY-STEP
		$lStep = $this->Services->GetCharacter()->GetCreationStep();
		if( $lStep == 1 ) {

			// Check if the posted informations are valid
			//-- FIRST NAME IS MANDATORY
			if(!isset($_POST['firstname']) || !$_POST['firstname']) 	{ $this->Error = "Le prénom est obligatoire!"; }

			//-- RACE IS MANDATORY
			if(!isset($_POST['racecode']) || !$_POST['racecode']) 		{ $this->Error = "Le choix d'une race est obligatoire!"; }

			// Update object
			$this->Services->GetCharacter()->SetFirstName( $_POST['firstname'] );
			$this->Services->GetCharacter()->SetLastName( $_POST['lastname'] );
			$this->Services->GetCharacter()->SetRaceCode( $_POST['racecode'] );

			// Check if name is valid
			if( $this->Services->CharacterNameExists( $this->Services->GetCharacter()->GetUserID(), $_POST['firstname'], $_POST['lastname'] ) ) { 
				$this->Error = "Vous avez déjà un personnage portant ce nom!"; 
			}

			if( $this->Error ) { return False; }

			// Get race name and possible classes
			$this->Services->GetRaceName();		// *!* For one less access to DB, take the name from RaceList...
			$this->Services->GetPossibleClasses();

			// Step up!
			$this->Services->GetCharacter()->SetCreationStep( 2 );
		}

		elseif( $lStep == 2 ) {
			if($_POST['option'] == 'Précédant') {

				// reset stats other than name and race
				$this->Services->GetCharacter()->SetClassCode( NULL );
				$this->Services->GetCharacter()->SetClass( NULL );

				// Step down...
				$this->Services->GetCharacter()->SetCreationStep( 1 );
			}

			elseif($_POST['option'] == 'Suivant') {

				// Check if the posted informations are valid
				//-- CLASS IS MANDATORY
				if(!isset($_POST['class']) || !$_POST['class']) { 
					$this->Error = "Le choix d'une classe est obligatoire!"; 
					return False; 
				}

				// Update object
				$this->Services->GetCharacter()->SetClassCode( $_POST['class'] );

				// Get race name and possible classes
				$this->Services->GetClassName();		// *!* For one less access to DB, take the name from ClassList...
				$this->Services->GetPossibleArchetypes();
				$this->Services->GetPossibleReligions();

				// Step up!
				$this->Services->GetCharacter()->SetCreationStep( 3 );
			}
		}

		elseif( $lStep == 3 ) {
			if($_POST['option'] == 'Précédant') {

				// reset stats other than name and race
				$this->Services->GetCharacter()->SetArchetypeCode( NULL );
				$this->Services->GetCharacter()->SetArchetype( NULL );
				$this->Services->GetCharacter()->SetReligionCode( NULL );
				$this->Services->GetCharacter()->SetReligion( NULL );
				$this->Services->GetCharacter()->SetOrigin( NULL );

				// Step down...
				$this->Services->GetCharacter()->SetCreationStep( 2 );
			}
			elseif($_POST['option'] == 'Suivant') {

				// Check if the posted informations are valid
				//-- ARCHETYPE IS MANDATORY
				if(!isset($_POST['archetype']) || !$_POST['archetype']) { 
					$this->Error = "Le choix de votre archétype est obligatoire!"; 
				}
				//-- RELIGION IS MANDATORY
				if(!isset($_POST['religion']) || !$_POST['religion']) { 
					$this->Error = "Le choix d'une religion est obligatoire!"; 
				}
				//-- ORIGIN IS MANDATORY
				if(!isset($_POST['origin']) || !$_POST['origin']) { 
					$this->Error = "La provenance de votre personnage est obligatoire!"; 
				}
				//-- ADEPT - ARCHETYPE & RELIGION MUST MATCH
				if( 	($_POST['archetype']=='POTENT'  && $_POST['religion']<>'ADEMOS')  || 
					($_POST['archetype']=='CHANGE'  && $_POST['religion']<>'CHAOS')   || 
					($_POST['archetype']=='BALANCE' && $_POST['religion']<>'GOLGOTH') ){ 
					$this->Error = "Adepte : Votre religion et votre archétype doivent s'aligner!"; 
				}

				// Update object
				$this->Services->GetCharacter()->SetArchetypeCode( $_POST['archetype'] );
				$this->Services->GetCharacter()->SetReligionCode( $_POST['religion'] );
				$this->Services->GetCharacter()->SetOrigin( $_POST['origin'] );

				if( $this->Error ) { return False; }

				// Get race name and possible classes
				$this->Services->GetArchetypeName();		// *!* For one less access to DB, take the name from ArchetypeList...
				$this->Services->GetReligionName();		// *!* For one less access to DB, take the name from ReligionList...
				$this->Services->GetStartingSkillsAndTalents();
				$this->Services->GetStartingLife();
	
				// Step up!
				$this->Services->GetCharacter()->SetCreationStep( 4 );
			}
		}

		elseif( $lStep == 4 ) {

			// "Last" button only
			// reset stats other than name and race
			$this->Services->GetCharacter()->SetSkills( NULL );
			$this->Services->GetCharacter()->SetTalents( NULL );
			$this->Services->GetCharacter()->SetLife( NULL );

			// Step down...
			$this->Services->GetCharacter()->SetCreationStep( 3 ); 				
		}

		return True;
	}


	//--SAVE NEW CHARACTER--
	public function SaveNewCharacter()
	{
		$this->Error = null;

		// Check if active character is a new character
		if( !($this->Services->GetCharacter() instanceof NewCharacter) ) { $this->Error = "Ceci n'est pas un nouveau personnage!"; return False; }

		// Get data
		$lUserID = $this->Services->GetCharacter()->GetUserID();
		$lFirstName = $this->Services->GetCharacter()->GetFirstName();
		$lLastName = $this->Services->GetCharacter()->GetLastName();
		$lRaceCode = $this->Services->GetCharacter()->GetRaceCode();
		$lClassCode = $this->Services->GetCharacter()->GetClassCode();
		$lArchetypeCode = $this->Services->GetCharacter()->GetArchetypeCode();
		$lReligionCode = $this->Services->GetCharacter()->GetReligionCode();
		$lOrigin = $this->Services->GetCharacter()->GetOrigin();
		$lSkills = $this->Services->GetCharacter()->GetSkills();
		$lTalents = $this->Services->GetCharacter()->GetTalents();
		$lLife = $this->Services->GetCharacter()->GetLife();

		// Call the registration function
		$r = $this->Services->RegisterCharacter( $lUserID, $lFirstName, $lLastName, $lRaceCode, $lClassCode, $lArchetypeCode, $lReligionCode, $lOrigin, $lSkills, $lTalents, $lLife);
		
		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors de l'enregistrement de votre personnage!";
		return False;
	}


	//--SAVE NEW CHARACTER WITH ONLY BASE INFORMATION--
	public function SaveNewBaseCharacter()
	{
		$this->Error = null;

		// Check if active character is a new character
		if( !($this->Services->GetCharacter() instanceof NewCharacter) ) { $this->Error = "Ceci n'est pas un nouveau personnage!"; return False; }

		// Get data
		$lUserID = $this->Services->GetCharacter()->GetUserID();
		$lFirstName = $_POST['firstname'];
		$lLastName = $_POST['lastname'];
		$lRaceCode = $_POST['racecode'];
		$lClassCode = 'N.A.';
		$lArchetypeCode = 'N.A.';
		$lReligionCode = 'ADETERM';
		$lOrigin = $_POST['origin'];

		// Call the registration function
		$r = $this->Services->RegisterCharacterBase( $lUserID, $lFirstName, $lLastName, $lRaceCode, $lClassCode, $lArchetypeCode, $lReligionCode, $lOrigin);
		
		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors de l'enregistrement de votre personnage!";
		return False;
	}


	//--SAVE SKILL PRECISION
	public function SpecifyCharacterSkill()
	{
		$this->Error = null;

		// Check if the posted informations are valid
		//--SKILL ID IS MANDATORY
		if(!$_POST['skillid']) { 
			$this->Error = "Le choix de la compétence n'est plus en mémoire!"; 
			return False; 
		}
		if(!$_POST['skillprecision'] || $_POST['skillprecision'] == "") { 
			$this->Error = "Aucune précision choisie pour la compétence!"; 
			return False; 
		}


		// Prepare data


		// Call service
		$this->Services->UpdateSkillPrecision( $_POST['skillid'], $_POST['skillprecision'] );
		
		return True;
	}


	//--REGISTER NEW TEACHING
	public function RegisterTeaching()
	{
		$this->Error = null;

		// Check if the posted informations are valid
		//--MASTER IS MANDATORY
		if(!$_POST['studentid']) { 
			$this->Error = "La sélection d'un étudiant est obligatoire!"; 
			return False; 
		}
		if(!$_POST['masterid']) { 
			$this->Error = "La sélection d'un maître est obligatoire!"; 
			return False; 
		}
		//--SKILL IS MANDATORY
		if(!$_POST['skillcode']) { 
			$this->Error = "La sélection d'une compétence est obligatoire!"; 
			return False; 
		}
		//--ACTIVITY IS MANDATORY
		if(!$_POST['activityid']) { 
			$this->Error = "La sélection d'une activité est obligatoire!!"; 
			return False; 
		}
		//--PLACE IS MANDATORY
		if(!$_POST['place']) { 
			$this->Error = "L'identification du lieu du cours est obligatoire!"; 
			return False; 
		}
		//--MOMENT IS MANDATORY
		if(!$_POST['moment']) { 
			$this->Error = "L'identification du moment de la journée où le cours fut donné est obligatoire!"; 
			return False; 
		}


		// Prepare data


		// Remove potentially harmful tags
		$lPlace = strip_tags($_POST['place']);


		// Check if teaching is not already registered and call registration function if not
		if( $this->Services->TeachingExists( $_POST['masterid'], $_POST['studentid'], $_POST['skillcode'] ) ){
			$this->Error = "Cet enseignement a déjà été enregistré dans la base de données.";
			return False;
		}
		else {
			$lMasterReward = $this->Services->CalculateMasterReward( $_POST['masterid'], $_POST['skillcode'], $_POST['activityid'] );
			if($lMasterReward == NULL) { $lMasterReward = 0; }

			if(!$this->Services->AddTeaching( $_POST['masterid'], $_POST['studentid'], $_POST['skillcode'], $_POST['activityid'], $_POST['place'], $_POST['moment'] )) { 
				$this->Error = "Une erreur est survenue lors de l'enregistrement du cours.";
				return False; 
			}

			$this->Services->ApplyMasterReward( $_POST['masterid'], $_POST['studentid'], $_POST['skillcode'], $lMasterReward ); 
		}
		
		return True;
	}


	//--REGISTER NEW TEACHING
	public function SubmitSyllabus()
	{
		$this->Error = null;

		// Check if the posted informations are valid
		//--MASTER IS MANDATORY
		if(!$_POST['characterid']) { 
			$this->Error = "Un problème s'est produit à l'identification du personnage! Contactez TI@Terres-de-Belenos.com."; 
			return False; 
		}
		if(!$_POST['skillcode']) { 
			$this->Error = "Un problème s'est produit à l'identification de la compétence! Contactez TI@Terres-de-Belenos.com."; 
			return False; 
		}
		// ATTACHED FILE IS MANDATORY
		if( !isset($_FILES['attachedfile']) ) { 
			$this->Error = "Aucun fichier n'a été sélectionné!"; 
			return False;
		}

		// Prepare file upload
		$lTarget = SYLLABUS_UPLOAD_DIR . basename($_FILES['attachedfile']['name']);
		$lFileType = strtolower(pathinfo($lTarget,PATHINFO_EXTENSION));

		// Uploaded file checks
		// FILES ALREADY EXISTS
		// This is not forbidden. The old file is replaced.

		// FILE TYPE
		if( $lFileType != "pdf") {
			$this->Error = "Seuls les fichiers PDF sont permis. Imprimez votre fichier en PDF au besoin.";
			return False;
		}

		// FAKE FILES
		if( $_FILES["attachedfile"]["size"] == 0 ) {
			$this->Error = "Votre fichier est vide. Vérifiez son intégrité.";
			return False;
		}

		// FILE SIZE
		if( $_FILES["attachedfile"]["size"] > UPLOAD_MAX_SIZE_IN_BYTES ) {
			$this->Error = "Votre fichier est trop volumineux. Vérifiez que sa taille en octets ne dépassent pas la limite indiquée dans les consignes.";
			return False;
		}

		// upload and send mail
		$uploaded = $this->Services->RegisterSyllabusProposal( $_POST['skillcode'], "attachedfile" );
		
		if($uploaded) { 
			unset($_FILES['attachedfile']);
			return True; 
		}

		$this->Error = $this->Services->Error; //"Une erreur est survenue lors de la soumission!";
		return False;
	}


	//--DELETE A TEACHING--
	public function CancelTeaching()
	{
		// Prepare data
		$index = $_POST['cancel-teaching'];
		$lTeaching = $this->Services->GetCharacter()->GetTeachings()[$index];


		// Call cancelation function
		$r = $this->Services->CancelTeaching( $lTeaching['id'] );

		// Recalculate overall teachings' worths for the pertinent activity, since it can be wrong due to experience cap
		$this->Services->RecalculateTeachingsWorth( $lTeaching['masterid'], $lTeaching['activityid'] );


		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors de l'annulation du cours!"; 
		return False;
	}


	//--REDEEM A TEACHING FOR MORE XP--
	public function RedeemTeaching()
	{
		$this->Error = null;

		// Check if the posted informations are valid


		// Prepare data
		$index = $_POST['redeem-teaching'];
		$lTeaching = $this->Services->GetCharacter()->GetTeachings()[$index];


		// Call cancelation function
		if( $lTeaching['xpvalue'] ) {
			if( $this->Services->RedeemTeaching( $lTeaching) ) { return True; }
		}
		else { 
			$this->Error = "Le cours sélectionné n'a aucune valeur en XP!"; 
			return False; 
		}

		$this->Error = "Une erreur est survenue lors du traitement du cours!"; 
		return False;
	}


	//--SAVE A NEW BACKGROUND FOR CHARACTER--
	public function ChangeBackground()
	{
		$this->Error = null;

		// There is no mandatory input


		// Remove potentially harmful tags
		$lBackground = strip_tags($_POST['background'], '<p><b><i><u>');

		// Call the registration function
		$r = $this->Services->SaveCharacterBackground( $lBackground );

		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors de la mise à jour de votre histoire.";
		return False;
	}


	//--REQUEST APPROVAL FOR SOME CHARACTER TEXT--
	public function RequestStoryApproval()
	{
		$this->Error = null;

		// There is no mandatory input
		$lApproval = $this->Services->GetCharacter()->GetSubjectApproval( 'Histoire' );
		if( $lApproval && $lApproval->GetStatus() == 'ACCEP' ) {
			$this->Error = "Votre histoire a déjà été approuvée.";
			return False;			
		}

		// Call the registration function
		$r = $this->Services->RegisterApprovalRequest( 'Histoire' );

		if($r) { /*$this->Services->SendApprovalRequestMail( 'Histoire' );*/ return True; }

		$this->Error = "Une erreur est survenue lors de la demande d'approbation...";
		return False;
	}


	//--VALIDATE CHARACTER HAS NO ACTIVE OR REQUESTED QUEST--
	public function CannotAskQuests()
	{
		$this->Error = null;

		// No input validation required


		// Call service
		$lApplicationCount = $this->Services->GetCharacter()->GetOpenedQuestApplicationCount();

		if( $lApplicationCount >= 2) { return True; }

		return False;
	}


	//--SAVE A NEW QUEST REQUEST--
	public function RequestQuest()
	{
		$this->Error = null;

		// Check if the posted informations are valid
		//--QUEST SUBJECT IS MANDATORY
		if(!$_POST['quest']) { 
			$this->Error = "Choisir une quête est obligatoire!"; 
			return False; 
		}
		//--LE COMTÉ EST MANDATOIRE
		if(!$_POST['county']){ 
			$this->Error = "Choisir un comté est obligatoire!"; 
			return False; 
		}

		// Explode combined posted data
		$lQuestData = explode("|", $_POST['quest']);
		$lCountyData = explode(";", $_POST['county']);

		// Additional validations
		//--THIRD ITEM IN QUEST DATA MUST NOT BE ZERO OR NULL
		if(!$lQuestData[2]) {
			$this->Error = "Vous devez choisir une quête!";
			return False;
		}
		//--FOURTH ITEM IN COUNTY DATA MUST BE PRESENT SINCE IT IS THE ID
		if(!isset($lCountyData[3])) {
			$this->Error = "Vous devez choisir un comté!";
			return False;
		}
		

		// Prepare data
		$lOptionCode = $lQuestData[0];
		$lRewardCode = $lQuestData[1];
		$lQuestSubject = $lQuestData[2];
		$lCountyID = $lCountyData[3];
		$lActiveApplication = $this->Services->GetCharacter()->GetOpenedQuestApplicationCount();
		$lNextActivity = $_SESSION['masterlist']->GetNextMainActivity()->GetID();
				
		// Remove potentially harmful tags
		$lSuggestions = strip_tags($_POST['suggestions'], '<p><b><i><u>');				

		
		// Call the registration or reservation function
		if( $lActiveApplication == 0) {
			$r = $this->Services->RegisterQuestRequest( $lQuestSubject, $lOptionCode, $lRewardCode, $lCountyID, $lSuggestions, 'DEM', $lNextActivity );
		}
		else {
			$r = $this->Services->RegisterQuestRequest( $lQuestSubject, $lOptionCode, $lRewardCode, $lCountyID, $lSuggestions, 'SUITE', $lNextActivity );
		}

		if($r) {
			$this->Error = null;
			return True;
		}

		$this->Error = "Une erreur est survenue lors de l'enregistrement de votre quête!";
		return False;
	}


	//--CANCEL OR DELETE A QUEST--
	public function CancelQuest()
	{
		// Prepare data
		$lQuestID = $_POST['cancel-quest'];


		// Call the remove function
		$r = $this->Services->CancelQuest( $lQuestID );

		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors de l'annulation de votre quête."; 
		return False;
	}


	//--RESTORE A CANCELED QUEST--
	public function RestoreQuest()
	{
		// Prepare data
		$lQuestID = $_POST['restore-quest'];


		// Check if there's already an active quest
		if( $this->CannotAskQuests() ) { 
			$this->Error = "Vous avez déjà une quête active ou en demande. Terminez ou annulez d'abord cette quête avant d'en restaurer une autre."; 
			return False; 
		}

		// Call the remove function
		$r = $this->Services->RestoreQuest( $lQuestID );

		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors de la restauration de votre quête!"; 
		return False;
	}


	//--REGISTER NEW RESUME
	public function RegisterResume()
	{
		$this->Error = null;

		// Check if the posted informations are valid
		//--ACTIVITY IS MANDATORY
		if(!$_POST['activity']) { 
			$this->Error = "Le choix de l'activité est obligatoire!"; 
			return False; 
		}
		//--TEXT IS MANDATORY
		if(!$_POST['text']) { 
			$this->Error = "Le texte du résumé est obligatoire! Vous ne pouvez pas enregistré un résumé vide."; 
			return False; 
		}


		// Prepare data
		$lQuestID = $_POST['quest'];


		// Remove potentially harmful tags
		$lText = strip_tags($_POST['text'], '<p><b><i><u>');


		// Check if character has a resumé for this activity. If so, update instead of inserting.
		$r = False;
		if( $this->Services->ResumeExists( $_POST['activity'] ) ){
			$this->Error = "Vous ne pouvez pas créer deux résumés pour la même activité! Veuillez plutôt modifier le résumé existant pour cette activité.";
			return False;
		}
		else {
			$r = $this->Services->RegisterNewResume( $_POST['activity'], $lText, $lQuestID );
		}
		
		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors de l'enregistrement de votre résumé!";
		return False;
	}


	//--UPDATE OLD RESUME
	public function UpdateResume()
	{
		$this->Error = null;

		// Check if the posted informations are valid
		//--ACTIVITY IS MANDATORY
		if(!$_POST['resumeid']) { 
			$this->Error = "L'identifiant du résumé a été perdu dans la transaction! Contactez un DBA."; 
			return False; 
		}
		//--TEXT IS MANDATORY
		if(!$_POST['text']) { 
			$this->Error = "Le texte du résumé est obligatoire! Si vous désirez supprimer un résumé, envoyez un courriel aux DBA."; 
			return False; 
		}


		// Prepare data
		$lQuestID = $_POST['quest'];

		// Remove potentially harmful tags
		$lText = strip_tags($_POST['text'], '<p><b><i><u>');


		// Call update function.
		$r = $this->Services->UpdateResume( $_POST['resumeid'], $lText, $lQuestID );
		
		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors de la mise à jour de votre résumé!";
		return False;
	}


	//--SEND NEW LETTER TO NPC
	public function SaveNPCLetter()
	{
		$this->Error = null;
		$lFileType = null;

		// Check if the posted informations are valid
		//--RECIPIENT IS MANDATORY
		if(!$_POST['recipient']) { 
			$this->Error = "Le choix du destinataire est obligatoire!"; 
			return False; 
		}
		//--SUBJECT IS MANDATORY
		if(!$_POST['subject']) { 
			$this->Error = "Le sujet est obligatoire!"; 
			return False; 
		}
		// ATTACHED FILE CHECK
		if(!isset($_FILES['attachedfile']) || !$_FILES['attachedfile']['name'] ) { 
			$this->Error = "Aucun fichier n'a été sélectionné!"; 
			return False;
		}
		else {	$lFileType = strtolower(pathinfo($_FILES['attachedfile']['name'],PATHINFO_EXTENSION));	}
		// FILE TYPE
		if( $lFileType != "pdf" ) {
			$this->Error = "Seuls les fichiers PDF sont permis. Imprimez votre fichier en PDF ou utilisez les fonctionnalités gratuites de Google Docs au besoin.";
			return False;
		}
		// FILE SIZE
		if( $_FILES["attachedfile"]["size"] > UPLOAD_MAX_SIZE_IN_BYTES ) {
			$this->Error = "Votre fichier est trop volumineux. Vérifiez que sa taille en octets ne dépassent pas la limite indiquée dans les consignes.";
			return False;
		}


		// Remove potentially harmful tags
		$lSubject = strip_tags($_POST['subject'], '<b><i><u>');


		// Save, upload and send.
		if( $this->Services->CreateNewLetter( 5, $lSubject, $_POST['recipient'], 'PNJ' ) && 
			$this->Services->UploadNPCLetter( $_POST['recipient'], $lSubject ) && 
			$this->Services->SendNPCLetterMail( $_POST['recipient'], $lSubject) ) {
				return True;
		}
		
		$this->Error = "Une erreur imprévue est survenue lors de l'enregistrement de votre lettre!";
		return False;
	}


	//--SEND NEW LETTER TO PC
	public function SavePCLetter()
	{
		$this->Error = null; 
		$lFileType = null;

		// Check if the posted informations are valid
		//--RECIPIENT IS MANDATORY
		if(!$_POST['characterid']) { 
			$this->Error = "Le choix du destinataire est obligatoire!"; 
			return False; 
		}
		//--SUBJECT IS MANDATORY
		if(!$_POST['subject']) { 
			$this->Error = "Le sujet est obligatoire!"; 
			return False; 
		}
		// ATTACHED FILE CHECK
		if(!isset($_FILES['attachedfile']) || !$_FILES['attachedfile']['name'] ) { 
			$this->Error = "Aucun fichier n'a été sélectionné!"; 
			return False;
		}
		else {	$lFileType = strtolower(pathinfo($_FILES['attachedfile']['name'],PATHINFO_EXTENSION));	}
		// FILE TYPE
		if( $lFileType != "pdf" ) {
			$this->Error = "Seuls les fichiers PDF sont permis. Imprimez votre fichier en PDF ou utilisez les fonctionnalités gratuites de Google Docs au besoin.";
			return False;
		}
		// FILE SIZE
		if( $_FILES["attachedfile"]["size"] > UPLOAD_MAX_SIZE_IN_BYTES ) {
			$this->Error = "Votre fichier est trop volumineux. Vérifiez que sa taille en octets ne dépassent pas la limite indiquée dans les consignes.";
			return False;
		}


		// Remove potentially harmful tags
		$lSubject = strip_tags($_POST['subject'], '<b><i><u>');


		// Save and upload.
		if( $this->Services->CreateNewLetter( $_POST['characterid'], $lSubject, $_FILES['attachedfile']['name'], 'PJ' ) && 
		    	$this->Services->UploadPCLetter( $_POST['characterid'], $lSubject ) ) {
				return True;
		}
		
		$this->Error = "Une erreur imprévue est survenue lors de l'enregistrement de votre lettre!";
		return False;
	}


	//--DELETE EXISTING LETTER TO A NPC
	public function DeleteNPCLetter()
	{
		$this->Error = null;

		// Check if the posted informations are valid
		//--RECIPIENT IS MANDATORY
		if(!$_POST['delete-letter']) { 
			$this->Error = "Aucune missive sélectionnée!"; 
			return False; 
		}


		// Send.
		$r = False;
		if( $this->Services->DeleteLetter( $_POST['delete-letter']) ) {
			return True;
		}
		
		$this->Error = "Une erreur imprévue est survenue lors de la suppression!";
		return False;
	}


	//--ARCHIVE EXISTING LETTER
	public function ArchiveLetter()
	{
		$this->Error = null;

		// Check if the posted informations are valid
		//--RECIPIENT IS MANDATORY
		if(!$_POST['archive-letter']) { 
			$this->Error = "Aucune missive sélectionnée!"; 
			return False;
		}

		// Archive.
		$r = False;
		if( $this->Services->ArchiveLetter( $_POST['archive-letter']) ) {
			return True;
		}
		
		$this->Error = "Une erreur imprévue est survenue lors de l'archivage de votre lettre!";
		return False;
	}


	//--REGISTER A PERMANENT DEATH--
	public function RegisterPermanentDeath()
	{
		$this->Error = null;

		// Check if the posted informations are valid


		// Prepare data


		// Call correct function
		if( $this->Services->UpdateCharacterStatus('MORT') ) { return True; }
		

		$this->Error = "Une erreur est survenue lors de l'enregistrement de la mort...";
		return False;
	}


	//--REGISTER A TEMPORARY DEATH--
	public function RegisterTemporaryDeath()
	{
		$this->Error = null;

		// Check if the posted informations are valid
		//--REASON IS MANDATORY IF DEATH IS TEMPORARY
		if(!$_POST['resurectionmethod']) { 
			$this->Error = "Le moyen de résurrection est obligatoire!"; 
			return False; 
		}
		//--PLACE OF DEATH IS MANDATORY
		if(!$_POST['place']) { 
			$this->Error = "Le lieu de la mort est obligatoire!"; 
			return False; 
		}
		//--MOMENT OF DEATH IS MANDATORY
		if(!$_POST['moment']) { 
			$this->Error = "Le moment de la mort est obligatoire!"; 
			return False; 
		}
		//--HEALER IS MANDATORY IF RESURECTION METHOD IS GIVEN
		if($_POST['resurectionmethod'] <> 'Pacte divin/démoniaque' && $_POST['resurectionmethod'] <> 'Renaissance sauvage' && !$_POST['healer']) { 
			$this->Error = "Le nom du guérisseur est obligatoire si un moyen de résurrection a été utilisé!"; 
			return False; 
		}


		// Prepare data
		$lComments = 'Lieu: '.$_POST['place'].' - Moment: '.$_POST['moment'].' - Guérisseur: '.$_POST['healer'];
		$lComments = strip_tags($lComments, '');
		$lLostLife = -1;


		// Call correct function
		if( $this->Services->InsertLifeAdjustment($lLostLife, $_POST['resurectionmethod'], $lComments) ) { return True; }
		

		$this->Error = "Une erreur est survenue lors de l'enregistrement...";
		return False;
	}


	//--REGISTER CHARACTER'S DEPORTATION--
	public function RegisterDeportation()
	{
		$this->Error = null;

		// No mandatory data


		// Call correct function
		if( $this->Services->UpdateCharacterStatus('DEPOR') ) { return True; }
		

		$this->Error = "Une erreur est survenue lors de l'enregistrement de la déclaration...";
		return False;
	}


	//--REGISTER CHARACTER'S DEPORTATION--
	public function RegisterExile()
	{
		$this->Error = null;

		// No mandatory data


		// Call correct function
		if( $this->Services->UpdateCharacterStatus('EXIL') ) { return True; }
		

		$this->Error = "Une erreur est survenue lors de l'enregistrement de la déclaration...";
		return False;
	}


	//--REGISTER CHARACTER'S DEPORTATION--
	public function RegisterRetirement()
	{
		$this->Error = null;

		// No mandatory data


		// Call correct function
		if( $this->Services->UpdateCharacterStatus('RETIR') ) { return True; }
		

		$this->Error = "Une erreur est survenue lors de l'enregistrement de la déclaration...";
		return False;
	}


	//--TRANSFER PLAYER XP TO CURRENT CHARACTER--
	public function TransferExperience($inPlayerExperience)
	{
		$this->Error = null;

		// Check if the posted informations are valid
		//-- XP AMOUNT IS MANDATORY
		if(!isset($_POST['xp'])) { 
			$this->Error = "La quantité d'Expérience à transférer est obligatoire!"; 
			return False;
		}
		//-- XP AMOUNT CANNOT BE NEGATIVE
		if( $_POST['xp'] <= 0 ) { 
			$this->Error = "La quantité saisie est négative..."; 
			return False;
		}
		//-- XP AMOUNT CANNOT BE GREATER THAN PLAYER'S TOTAL
		if( $_POST['xp'] > $inPlayerExperience ) { 
			$this->Error = "La quantité saisie excède l'Expérience de joueur que vous possédez."; 
			return False;
		}


		// Validate that transfered XP does not exceed maximum
		$lTransferedExperience = $this->Services->GetTransferedExperience();
		if( $lTransferedExperience + $_POST['xp'] > MAX_XP_TRANSFER ) { $this->Error = "Vous excédez les ".MAX_XP_TRANSFER." XP qu'un personnage peut recevoir chaque année! Votre transfert maximum est de ". (MAX_XP_TRANSFER - $lTransferedExperience) ." XP."; return False; }


		// Call the transfer function
		$r = $this->Services->TransferExperience( $_POST['xp'] );

		if($r) {
			$this->Error = null;
			return True;
		}

		$this->Error = "Une erreur est survenue lors du transfert.";
		return False;
	}


	//--RENAME CURRENT CHARACTER--
	public function RenameCharacter()
	{
		$this->Error = null;

		// Check if the posted informations are valid
		//-- FIRSTNAME IS MANDATORY
		if(!isset($_POST['firstname'])) { 
			$this->Error = "Le prénom est obligatoire!"; 
			return False;
		}
		//-- FULLNAME MUST BE UNIQUE FOR THIS USER
		if( $this->Services->CharacterNameExists( $this->Services->GetUser()->GetID(), $_POST['firstname'], $_POST['lastname']) ) { 
			$this->Error = "Vous avez déjà un personnage portant ce nom."; 
			return False;
		}


		// Call the delete function
		$r = $this->Services->RenameCurrentCharacter( $_POST['firstname'], $_POST['lastname'] );
		if($r) {
			$this->Error = null;
			return True;
		}

		$this->Error = "Une erreur est survenue lors de la mise à jour de votre nom de personnage!";
		return False;
	}


	//--CHANGE CURRENT CHARACTER'S ORIGIN--
	public function UpdateOrigin()
	{
		$this->Error = null;

		// Check if the posted informations are valid
		//-- ORIGIN IS MANDATORY
		if(!isset($_POST['origin'])) { 
			$this->Error = "La provenance est obligatoire!"; 
			return False;
		}


		// Call the delete function
		$r = $this->Services->ChangeOrigin( $_POST['origin'] );
		if($r) {
			$this->Error = null;
			return True;
		}

		$this->Error = "Une erreur est survenue lors de la mise à jour de votre provenance!";
		return False;
	}


	//--DELETE CURRENT CHARACTER--
	public function DeleteCharacter()
	{
		$this->Error = null;

		// Check if the posted informations are valid
		//-- PASSWORD IS MANDATORY
		if(!isset($_POST['password'])) { 
			$this->Error = "Le mot de passe est obligatoire!"; 
			return False;
		}
		//-- PASSWORD CONFIRMATION IS MANDATORY
		if(!isset($_POST['pw-confirm'])) { 
			$this->Error = "La confirmation est obligatoire!"; 
			return False;
		}
		//-- PASSWORD AND CONFIRMATION MUST BE IDENTICAL
		if( $_POST['password'] != $_POST['pw-confirm'] ) { 
			$this->Error = "La confirmation diffère du mot de passe!"; 
			return False;
		}


		// Validate password
		if( !$this->Services->ValidateDeletion( $_POST['password'] ) ){
			$this->Error = "Le mot de passe est invalide!"; 
			return False;
		}

		// Call the delete function
		$r = $this->Services->DeleteCurrentCharacter();
		if($r) { return True; }
		

		$this->Error = "Une erreur est survenue lors de la suppression du personnage.";
		return False;
	}


	//--SAVE SURVEY ANSWERS--
	public function SaveSurveyAnswers()
	{
		$this->Error = null;


		// Check if the posted informations are valid
		//-- SURVEY CODE IS MANDATORY
		if(!isset($_POST['surveycode'])) { 
			$this->Error = "Une erreur s'est produite et le code du questionnaire a été perdu!"; 
			return False;
		}
		//-- ANSWERS ARE MANDATORY
		if(!isset($_POST['answers'])) { 
			$this->Error = "Une erreur s'est produite et la liste des réponses a été perdu!"; 
			return False;
		}


		// Prepare data
		$lNumberOfQuestions = 0;
			if( $_POST['surveymode'] == 'PENDING') { $lNumberOfQuestions = $this->Services->GetCharacter()->GetPendingSurvey()->GetQuestionCount(); }
			elseif( $_POST['surveymode'] == 'ANSWERED' ) { 
				$index = $_POST['surveyindex'];
				$lNumberOfQuestions = $this->Services->GetCharacter()->GetAnsweredSurveys()[$index]->GetQuestionCount();
			}

		$lNumberOfAnswers = count( $_POST['answers'] );


		// Validate that every question was answered
		if( $lNumberOfQuestions != $lNumberOfAnswers ) {
			$this->Error = "Vous n'avez pas répondu à toutes les questions!"; 
			return False;
		}

		// Call the delete function
		$r = $this->Services->SaveSurveyAnswers($_POST['surveycode'], $_POST['answers']);
		if($r) { return True; }
		

		$this->Error = "Une erreur est survenue lors de l'enregistrement des réponses.";
		return False;
	}


	//--LEVEL UP CURRENT CHARACTER--
	public function LevelUp()
	{
		$this->Error = null;

		// No validation required

		// Call the level up functions
		$this->Services->SecureLastLevel();
		$r = $this->Services->LevelUpCurrentCharacter();
		if($r) { return True; }
		

		$this->Error = "L'augmentation de niveau a échouée!";
		return False;
	}


	//--LEVEL DOWN CURRENT CHARACTER--
	public function LevelDown()
	{
		$this->Error = null;

		// No validation required

		// Call the delete function
		$r = $this->Services->LevelDownCurrentCharacter();
		if($r) { return True; }
		

		$this->Error = "Level up failed!";
		return False;
	}

	//--SOFT RESET EXISTING CHARACTER--
	public function ManageCharacterSoftReset()
	{
		$this->Error = null;

		// Check if the posted informations are valid
		//-- ARCHETYPE IS MANDATORY
		if(!isset($_POST['archetype'])) { 
			$this->Error = "L'archétype est obligatoire!"; 
			return False;
		}
		//-- ORIGIN IS MANDATORY
		if(!isset($_POST['origin'])) { 
			$this->Error = "La provenance est obligatoire!"; 
			return False;
		}


		// Call reset functions
		$this->Services->GetCharacter()->SetArchetypeCode($_POST['archetype']);
		$this->Services->GetCharacter()->SetArchetype( $_SESSION['masterlist']->GetArchetypeName($_POST['archetype']));
		$this->Services->GetCharacter()->SetOrigin($_POST['origin']);
		$this->Services->GetStartingSkillsAndTalents();

		$r = $this->Services->RegisterCharacterResetData();
		
		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors de l'enregistrement de votre personnage!";
		return False;
	}


} // END of CharacterController class

?>
