<?php

/*
=SCRIPTOR FILE=
╔══CLASS════════════════════════════════════════════════════════╗
║	== Quest Controller v1.2 r2 ==				║
║	Implements quest management control logic.		║
║	Requires quest services.				║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('services/quest-services.class.php');

class QuestController
{

protected $Services;

public $Error;

	//--CONSTRUCTOR--
	public function __construct(&$inServices)
	{
		$this->Services = $inServices;
	}


	//--SELECT A QUEST--
	public function SelectQuest($inQuestType, $inQuestID)
	{
		$this->Error = null;

		// Check if the posted information is valid
		# Not necessary for the moment. Already checked in main.php.


		// Call type-specific service
		if( $inQuestType == 'PERSONAL' ) { 
		    	$this->Services->GetSelectedPersonalQuest($inQuestID);
		    	$this->Services->GetCharacterDetails($inQuestID);
		    	$this->Services->GetPersonalResumes($inQuestID);
		    	return True; 
		}
		elseif( $inQuestType == 'GROUP' ) { 
			$this->Services->GetSelectedGroupQuest($inQuestID); 
		    	$this->Services->GetGroupDetails($inQuestID);
		    	$this->Services->GetGroupResumes($inQuestID);
			return True; }
		elseif( $inQuestType == 'MYTHIC' ) { /*Not implemented yet...*/ }
		

		$this->Error = "Une erreur est survenue lors de la sélection de la quête!";
		return False;
	}


	//--UPDATE A QUEST'S BASE DATA--
	public function UpdateQuest()
	{
		$this->Error = null;

		// Check if the posted information is valid
		//--STATUS IS MANDATORY--
		if( !isset($_POST['status']) || !$_POST['status'] ) { 
			$this->Error = "Le choix d'un état estobligatoire!"; 
			return False; 
		}


		// Prepare data


		// Call the update function
		if(  isset($_POST['scriptor']) && !$this->Services->UpdateQuestScriptor($_POST['scriptor']) ) 
			{ $this->Error = "Une erreur est survenue lors de l'assignation!"; return False; }
		if( !$this->Services->UpdateQuestStatus($_POST['status']) ) 	
			{ $this->Error = "Une erreur est survenue lors du changement d'état!"; return Flase; }
	
		return True;
	}


	//--UPDATE A QUEST'S PART--
	public function UpdateQuestTexts()
	{
		$this->Error = null;

		// Check if the posted information is valid


		// Prepare data
		$lText = strip_tags($_POST['text'], '<p><b><i><u>');
		$lComments = strip_tags($_POST['comments'], '<p><b><i><u>');

		// Call the update function
		$r = $this->Services->UpdateQuestTexts( $lText, $lComments );
		
		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors de la mise à jour!";
		return False;
	}


	//--GIVE A QUEST'S REWARD TO THE CHARACTER--
	public function GivePersonalQuestReward()
	{
		$this->Error = null;

		// No validation on input required


		// Prepare data
		$lRewardStage = 0;
		if(isset($_POST['rewardstage'])) {
			$lRewardStage = $_POST['rewardstage'];
		}
	

		// Call the update function
		$r = $this->Services->GivePersonalQuestReward( $lRewardStage );
		
		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors du traitement de la récompense!";
		return False;
	}


} // END of QuestController class

?>
