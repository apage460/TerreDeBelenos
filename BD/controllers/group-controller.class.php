<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Group Controller v1.2 r7 ==				║
║	Implements group management control logic.		║
║	Requires group services.				║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('services/group-services.class.php');

class GroupController
{

Protected $Services;

public $Error;

	//--CONSTRUCTOR--
	public function __construct(&$inServices)
	{
		$this->Services = $inServices;
	}


	//--ACCEPT INVITATION LOGIC--
	public function AcceptInvitation()
	{
		$this->Error = null;

		// Separate key
		$key = explode('-', $_POST['accept-invite']);
		$lCharacterID = $key[0];
		$lGroupID = $key[1];

		// Call the validation function
		$r = $this->Services->AddCharacterToGroup( $lCharacterID, $lGroupID );
		if($r) { $r = $this->Services->RemoveInvitation( $lCharacterID, $lGroupID ); }

		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors de l'acceptation de l'invitation."; 
		return False;
	}


	//--REFUSE INVITATION LOGIC--
	public function RefuseInvitation()
	{
		$this->Error = null;

		// Separate key
		$key = explode('-', $_POST['refuse-invite']);
		$lCharacterID = $key[0];
		$lGroupID = $key[1];

		// Call the validation function
		$r = $this->Services->RefuseInvitation( $lCharacterID, $lGroupID );
		if($r) { $r = $this->Services->RemoveInvitation( $lCharacterID, $lGroupID ); }

		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors de la suppression de l'invitation."; 
		return False;
	}


	//--GROUP CHOICE LOGIC--
	public function PrepareJoinedGroupSelection()
	{
		$this->Error = null;

		// Get Character
		$lCharacter = $this->Services->GetManager()->GetCharacterByID( $_POST['join-group'] );

		// Set it as the active one
		if($lCharacter) {
			$this->Services->GetManager()->SetActiveCharacter( $lCharacter );
			return True;
		}

		$this->Error = "Une erreur est survenue lors de la mise à jour de vos données!"; 
		return False;
	}


	//--JOIN GROUP LOGIC--
	public function JoinGroup()
	{
		$this->Error = null;

		// Separate key
		$lCharacterID = $_POST['characterid'];
		$lGroupID = $_POST['groupid'];

		// Call the validation function
		$r = $this->Services->AddCharacterToGroup( $lCharacterID, $lGroupID );

		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors de la mise à jour de vos données!"; 
		return False;
	}


	//--QUIT GROUP LOGIC--
	public function QuitGroup()
	{
		$this->Error = null;

		// Separate key
		$key = explode('-', $_POST['quit-group']);
		$lCharacterID = $key[0];
		$lGroupID = $key[1];

		// Call the validation function
		$r = $this->Services->RemoveCharacterToGroup( $lCharacterID, $lGroupID );

		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors de la mise à jour de vos données!"; 
		return False;
	}


	//--REGISTRATION LOGIC--
	public function RegisterNewGroup()
	{
		$this->Error = null;

		// Check if the posted informations are valid
		//-- NAME IS MANDATORY
		if(!$_POST['name']) { 
			$this->Error = "Le nom du groupe est obligatoire!"; 
			return False; 
		}
		//-- DESCRIPTION IS MANDATORY
		if(!$_POST['description']) { 
			$this->Error = "La description du groupe est obligatoire!"; 
			return False; 
		} 


		// Check if group is unique
		if( $this->Services->GroupNameExists( $_POST['name'] ) ) { 
			$this->Error = "Le nom de groupe que vous avez choisi est déjà utilisé!"; 
			return False; 
		}

		// Secure free text!
		$lDescription = strip_tags($_POST['description'], '<p><b><i><u>');
		$lBackground = strip_tags($_POST['background'], '<p><b><i><u>');
		$lMoreInfo = strip_tags($_POST['moreinfo'], '<p><b><i><u>');
		


		// Call the registration function
		$r = $this->Services->RegisterGroup( $_POST['name'], $lDescription, $lBackground, $_POST['basecampcode'], $lMoreInfo );
		
		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors de la création du groupe!";
		return False;
	}


	//--DESCRIPTION UPDATE LOGIC--
	public function ChangeDescription()
	{
		$this->Error = null;

		// There is no mandatory input


		// Remove potentially harmful tags
		$lDescription = strip_tags($_POST['description'], '<p><b><i><u>');

		// Call the registration function
		$r = $this->Services->SaveGroupDescription( $lDescription );
		
		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors de la mise à jour de votre description de groupe!";
		return False;
	}


	//--BACKGROUND UPDATE LOGIC--
	public function ChangeBackground()
	{
		$this->Error = null;

		// There is no mandatory input


		// Remove potentially harmful tags
		$lBackground = strip_tags($_POST['background'], '<p><b><i><u>');

		// Call the registration function
		$r = $this->Services->SaveGroupBackground( $lBackground );
		
		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors de la mise à jour de votre historique de groupe!";
		return False;
	}


	//--RENAME ACTIVE GROUP--
	public function AddPIC()
	{
		$this->Error = null;

		// Check if the posted informations are valid
		//-- PERSON-IN-CHARGE IS MANDATORY
		if(!isset($_POST['newpic'])) { 
			$this->Error = "Le compte du nouveau Responsable est obligatoire!"; 
			return False;
		}
		//-- USER MUST NOT ALREADY BE IN CHARGE OF A GROUP
		if( $this->Services->AlreadyInCharge( $_POST['newpic'] ) ) { 
			$this->Error = "Le Responsable que vous tentez d'ajouter gère déjà un autre groupe! Un individu ne peut être Responsable que d'un seul groupe."; 
			return False;
		}


		// Call the insert function
		$r = $this->Services->NamePersonInCharge( $_POST['newpic'] );

		if($r) { return True; }

		$this->Error = "Une erreur imprévue est survenue lors de la nomimation du nouveau Responsable.";
		return False;
	}


	//--RENAME ACTIVE GROUP--
	public function RemovePIC()
	{
		$this->Error = null;

		// No validation needed


		// Call the insert function
		$r = $this->Services->RemovePersonInCharge();

		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors du retrait...";
		return False;
	}


	//--RENAME ACTIVE GROUP--
	public function RenameGroup()
	{
		$this->Error = null;

		// Check if the posted informations are valid
		//-- NAME IS MANDATORY
		if(!isset($_POST['name'])) { 
			$this->Error = "Le nouveau nom du groupe est obligatoire!!"; 
			return False;
		}
		//-- NAME MUST BE UNIQUE
		if( $this->Services->GroupNameExists( $_POST['name'] ) ) { 
			$this->Error = "Le nom de groupe que vous avez choisi est déjà utilisé ou est identique à votre ancien nom!"; 
			return False;
		}


		// Call the rename function
		$r = $this->Services->RenameActiveGroup( $_POST['name'] );

		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors de la mise à jour du nom du groupe.";
		return False;
	}


	//--CHANGE ACTIVE GROUP STATUS--
	public function ChangeGroupStatus()
	{
		$this->Error = null;

		// Check if the posted informations are valid
		//-- NEW STATUS IS MANDATORY
		if(!isset($_POST['status'])) { 
			$this->Error = "Le nouveau statut du groupe est obligatoire!!"; 
			return False;
		}


		// Call the rename function
		$r = $this->Services->UpdateGroupStatus( $_POST['status'] );

		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors de la mise à jour du statut du groupe.";
		return False;
	}


	//--REQUEST APPROVAL FOR SOME CHARACTER TEXT--
	public function RequestApproval( $inSubject )
	{
		$this->Error = null;

		// There is no mandatory input


		// Call the registration function
		$r = $this->Services->RegisterApproval( $inSubject );

		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors de la demande d'approbation...";
		return False;
	}


	//--CREATE A NEW INSTITUTION FOR GROUP--
	public function CreateNewInstitution()
	{
		$this->Error = null;

		// Check if the posted informations are valid
		//--NAME IS MANDATORY
		if(!$_POST['name'] || trim($_POST['name']) == "") { 
			$this->Error = "Choisir un nom est obligatoire!"; 
			return False; 
		}
		//--COUNTY IS MANDATORY
		if(!$_POST['countyid']){ 
			$this->Error = "Choisir un comté est obligatoire!"; 
			return False; 
		}
		//--PROFILE IS MANDATORY
		if(!$_POST['profilecode']){ 
			$this->Error = "Choisir un profil est obligatoire!"; 
			return False; 
		}
		//--LEADER IS MANDATORY
		if(!$_POST['leader'] || trim($_POST['leader']) == "") {
			$this->Error = "Votre Institution a besoin d'un chef."; 
			return False; 
		}
		//--DESCRIPTION IS MANDATORY
		if(!$_POST['description'] || trim($_POST['description']) == "") {
			$this->Error = "Vous devez fournir une description de votre Institution."; 
			return False; 
		}

		// Check if group has too many Institutions
		$lActiveInstitutions = $this->Services->GetManager()->GetActiveGroup()->GetInstitutionCount();
		if( $lActiveInstitutions >= MAX_INSTITUTIONS ) {
			$this->Error = "Vous avez déjà le maximum d'Institutions possible pour un groupe. Contactez l'Organisation si vous souhaitez vous départir de certaines d'entre elles."; 
			return False; 
		}
		

		// Prepare data
		// Remove potentially harmful tags
		$lLeader = strip_tags($_POST['leader'], '<p><b><i><u>');				
		$lDescription = strip_tags($_POST['description'], '<p><b><i><u>');				
		$lHiddenAgenda = strip_tags($_POST['hiddenagenda'], '<p><b><i><u>');				

		
		// Call the registration or reservation function
		$r = $this->Services->RegisterNewInstitution( $_POST['name'], $_POST['countyid'], $_POST['profilecode'], $lLeader, $lDescription, $lHiddenAgenda );

		if($r) {
			$this->Error = null;
			return True;
		}

		$this->Error = "Une erreur inattendue est survenue lors de l'enregistrement de votre Institution!";
		return False;
	}


	//--DELETE A LEVEL-0 INSTITUTION--
	public function DeleteInstitution()
	{
		// Prepare data
		$lInstitutionID = $_POST['delete-institution'];


		// Call the remove function
		if( $this->Services->DeleteInstitution( $lInstitutionID ) ) 
			{ return True; }

		$this->Error = "Une erreur est survenue lors de la suppression de votre Institution."; 
		return False;
	}


	//--RETIRE A NON-LEVEL-0 INSTITUTION--
	public function RetireInstitution()
	{
		// Prepare data
		$lInstitutionID = $_POST['retire-institution'];


		// Call the remove function
		if( $this->Services->UpdateInstitution( $lInstitutionID, 'RETIR' ) ) 
			{ return True; }

		$this->Error = "Une erreur est survenue lors de la retraite de votre Institution."; 
		return False;
	}


	//--VALIDATE GROUP HAS NO ACTIVE OR REQUESTED QUEST--
	public function CannotAskQuests()
	{
		$this->Error = null;

		// No input validation required


		// Call service
		$lApplicationCount = $this->Services->GetManager()->GetActiveGroup()->GetOpenedQuestApplicationCount();

		if( $lApplicationCount >= 2) { return True; }

		return False;
	}


	//--SAVE A NEW QUEST REQUEST FOR GROUP--
	public function RequestQuest()
	{
		$this->Error = null;

		// Check if the posted informations are valid
		//--QUEST SUBJECT IS MANDATORY
		if(!$_POST['quest'] || $_POST['quest'] == "ND|S.O.|0") { 
			$this->Error = "Choisir une quête est obligatoire!"; 
			return False; 
		}
		//--COUNTY IS MANDATORY
		if(!$_POST['county']){ 
			$this->Error = "Choisir un comté est obligatoire!"; 
			return False; 
		}
		//--SUGGESTIONS ARE MANDATORY
		if(!$_POST['suggestions']) {
			$this->Error = "Vous devez obligatoirement fournir les détails de votre quête de groupe. Autrement, elle sera refusée."; 
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
		//--THIRD ITEM IN COUNTY DATA MUST BE PRESENT SINCE IT IS THE ID
		if(!isset($lCountyData[3])) {
			$this->Error = "Vous devez choisir un comté!";
			return False;
		}
		

		// Prepare data
		$lOptionCode = $lQuestData[0];
		$lRewardCode = $lQuestData[1];
		$lQuestSubject = $lQuestData[2];
		$lCountyID = $lCountyData[3];
		$lActiveApplication = $this->Services->GetManager()->GetActiveGroup()->GetOpenedQuestApplicationCount();
		$lNextActivity = $_SESSION['masterlist']->GetNextMainActivity()->GetID();
		$lAskingPlayer = $_SESSION['authenticated']->GetID();
				
		// Remove potentially harmful tags
		$lSuggestions = strip_tags($_POST['suggestions'], '<p><b><i><u>');				

		
		// Call the registration or reservation function
		if( $lActiveApplication == 0) {
			$r = $this->Services->RegisterQuestRequest( $lQuestSubject, $lOptionCode, $lRewardCode, $lCountyID, $lSuggestions, 'DEM', $lNextActivity, $lAskingPlayer );
		}
		else {
			$r = $this->Services->RegisterQuestRequest( $lQuestSubject, $lOptionCode, $lRewardCode, $lCountyID, $lSuggestions, 'SUITE', $lNextActivity, $lAskingPlayer );
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
		$lQuestID = NULL;
			if( isset($_POST['questid']) ) { $lQuestID = $_POST['questid'];}


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
			$this->Error = "L'identifiant du résumé a été perdu dans la transaction! Contactez un Administrateurs."; 
			return False; 
		}
		//--TEXT IS MANDATORY
		if(!$_POST['text']) { 
			$this->Error = "Le texte du résumé est obligatoire! Si vous désirez supprimer un résumé, envoyez un courriel aux Administrateurs."; 
			return False; 
		}


		// Prepare data
		$lQuestID = NULL;
			if( isset($_POST['questid']) ) { $lQuestID = $_POST['questid'];}

		// Remove potentially harmful tags
		$lText = strip_tags($_POST['text'], '<p><b><i><u>');


		// Call update function.
		$r = $this->Services->UpdateResume( $_POST['resumeid'], $lText, $lQuestID );
		
		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors de la mise à jour de votre résumé!";
		return False;
	}


	//--INVITE CHARACTER TO ACTIVE GROUP--
	public function InviteMember()
	{
		// Check if the posted informations are valid
		//-- ACCOUNT NAME IS MANDATORY
		if(!isset($_POST['invite-member'])) { 
			$this->Error = "Le choix d'un personnage est obligatoire!"; 
			return False;
		}


		// Prepare data
		$lCharacterID = $_POST['invite-member'];


		// Call the validation functions
		if( !$this->Services->CharacterExists( $lCharacterID ) ) { $this->Error = "Le personnage que vous avez sélectionné n'existe plus!"; return False; }
		if( $this->Services->AlreadyAMember( $lCharacterID ) ) { $this->Error = "Le personnage sélectionné est déjà membre de votre groupe ou y est déjà invité!"; return False; }

		// Call registration function
		$r = $this->Services->InviteMember( $lCharacterID );

		if($r) { return True; }

		$this->Error = "Une erreur imprévue est survenue lors de l'enregistrement de l'invitation!"; 
		return False;
	}


	//--REMOVE GROUP MEMBER--
	public function RemoveMember()
	{
		// Prepare data
		$lCharacterIndex = $_POST['remove-member'];


		// Call the remove function
		$r = $this->Services->RemoveMember( $lCharacterIndex );

		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors du retrait du personnage..."; 
		return False;
	}


	//--ADD OBJECTIVE TO ACTIVE GROUP--
	public function AddObjective()
	{
		// Check if the posted informations are valid
		//-- OBJECTIVE NAME IS MANDATORY
		if(!isset($_POST['objective']) || !$_POST['objective']) { 
			$this->Error = "Le nom de l'objectif est obligatoire!"; 
			return False;
		}
		//-- OBJECTIVE TYPE IS MANDATORY
		if(!isset($_POST['type']) || !$_POST['type']) { 
			$this->Error = "Le type d'objectif est obligatoire!"; 
			return False;
		}
		//-- OBJECTIVE DESCRIPTION IS MANDATORY
		if(!isset($_POST['description']) || !$_POST['description']) { 
			$this->Error = "La description de l'objectif est obligatoire!"; 
			return False;
		}

		// Prepare data
		$lDescription = strip_tags($_POST['description'], '<p><b><i><u>');


		// Call registration function
		$r = $this->Services->RegisterObjective( $_POST['objective'], $_POST['type'], $lDescription );

		if($r) { return True; }

		$this->Error = "Une erreur imprévue est survenue lors de l'enregistrement..."; 
		return False;
	}


	//--REVEAL GROUP OBJECTIVE--
	public function RevealObjective()
	{
		// Prepare data
		$lObjectiveID = $_POST['reveal-objective'];


		// Call the remove function
		$r = $this->Services->RevealObjective( $lObjectiveID );

		if($r) { return True; }

		$this->Error = "Une erreur imprévue est survenue lors de la mise à jour..."; 
		return False;
	}


	//--REMOVE GROUP OBJECTIVE--
	public function RemoveObjective()
	{
		// Prepare data
		$lObjectiveID = $_POST['remove-objective'];


		// Call the remove function
		$r = $this->Services->RemoveObjective( $lObjectiveID );

		if($r) { return True; }

		$this->Error = "Une erreur imprévue est survenue lors de la suppression..."; 
		return False;
	}


	//--SAVE NEW ACTIONS FOR THE COMING ACTIVITY--
	public function SaveNewActions()
	{
		// Check mandatory information
		//-- ACTIVITY IS MANDATORY
		if(!isset($_POST['activityid'])) { 
			$this->Error = "Nous avons perdu l'activité pendant le traitement... Désolé."; 
			return False;
		}


		// Prepare data
		$lGroup = $this->Services->GetManager()->GetActiveGroup();
		$lPossibleActionList = $this->Services->GetManager()->GetPossibleActions();
		$lRegisteredActionList = $lGroup->GetActivityActions( $_POST['activityid'] );

		// Control actions
		foreach ($lPossibleActionList as $action) {
			if( !isset($_POST[$action['code'].'-selection']) ) { continue; }
			elseif( isset($lRegisteredActionList[$action['code']]) && !$_POST[$action['code'].'-selection'] ) {
				// Delete action
				$lRegainedInfluence = ($lRegisteredActionList[$action['code']]['purchases']*$action['cost']);
				if( $lGroup->GetMaxInfluence() < ($lGroup->GetInfluenceCount()+$lRegainedInfluence) ) {
					$this->Error = "Vos limites d'Influence nous empêche de rembourser l'action « ".$action['name']." ». Le retrait n'a pas été enregistré.";
					return False;					
				}
				else {
					$this->Services->DeleteAction( $lRegisteredActionList[$action['code']]['id'] );
					$this->Services->AddInfluence( $lGroup->GetID(), $_POST['activityid'], 'Retrait '.$action['name'], $lRegainedInfluence );
				}
			}
			elseif( isset($lRegisteredActionList[$action['code']]) && $_POST[$action['code'].'-selection'] ) {
				// Update action
				$lTotalCost = $_POST[$action['code'].'-selection'] * $action['cost'];
				$lCostDifference = $lTotalCost - ($lRegisteredActionList[$action['code']]['purchases']*$action['cost']);
				if( $lGroup->GetMaxInfluence() < $lCostDifference || $lGroup->GetMaxInfluence() < ($lGroup->GetInfluenceCount()-$lCostDifference) ) {
					$this->Error = "Vos limites d'Influence nous empêche de faire la mise à jour pour l'action « ".$action['name']." ». Celle-ci n'a pas été enregistrée."; 
					return False;					
				}
				else {
					$this->Services->UpdateAction( $lRegisteredActionList[$action['code']]['id'], $_POST[$action['code'].'-selection'], $_POST[$action['code'].'-precision'], $lTotalCost );
					$this->Services->AddInfluence( $lGroup->GetID(), $_POST['activityid'], 'Modification '.$action['name'], -$lCostDifference );
				}
			}
			elseif ( !isset($lRegisteredActionList[$action['code']]) && $_POST[$action['code'].'-selection'] ) {
				// Add an action
				$lTotalCost = $_POST[$action['code'].'-selection'] * $action['cost'];
				if( $lGroup->GetInfluenceCount() < $lTotalCost ) { 
					$this->Error = "Vous n'avez plus assez d'Influence pour l'action « ".$action['name']." ». Celle-ci n'a pas été enregistrée."; 
					return False;
				}
				else {
					$this->Services->RegisterAction( $lGroup->GetID(), $_POST['activityid'], $action['code'], $_POST[$action['code'].'-selection'], $_POST[$action['code'].'-precision'], $lTotalCost );
					$this->Services->AddInfluence( $lGroup->GetID(), $_POST['activityid'], $action['name'], -$lTotalCost );
				}
			}
		}

		return True;
	}


	//--INFORMATION UPDATE LOGIC--
	public function ChangeBaseCampInformation()
	{
		$this->Error = null;

		// There is no mandatory input


		// Remove potentially harmful tags
		$lInformation = strip_tags($_POST['information'], '<p><b><i><u>');

		// Call the registration function
		if( $this->Services->SaveNewBaseCamp( $_POST['basecampcode'] ) 
			&& $this->Services->SaveMoreInformation( $lInformation )
		) { return True; }

		$this->Error = "Une erreur imprévue est survenue lors de la mise à jour...";
		return False;
	}


} // END of GroupController class

?>
