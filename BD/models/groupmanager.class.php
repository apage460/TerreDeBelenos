<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Group Manager Model v1.2 r8 ==			║
║	Represents a user's group management informations.	║
║	Serializable.						║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('player.class.php'); 		// Player definition
include_once('character.class.php'); 		// Character definition
include_once('group.class.php'); 		// Group definition

class GroupManager
{

protected $Invitations;
protected $Allegiances;

protected $Groups;
protected $ActiveGroup;		// Group object
protected $ActiveCharacter;	// Validate if it is still needed...
protected $GroupManagerMode;	// True if user is manager of Active Group

protected $Camps;
protected $PossibleSpecializations;
protected $PossibleProfiles;
protected $PossibleActions;
protected $Activities;		// Contains only activities since 2020 (using the current version of the group rules).



	//--CONSTRUCTOR--
	public function __construct( $inDataArray =array() )
	{

		if( isset($inDataArray['invitations']) ) 		{ $this->Invitations = $inDataArray['invitations']; }
		if( isset($inDataArray['allegiances']) ) 		{ $this->Allegiances = $inDataArray['allegiances']; }

		if( isset($inDataArray['groups']) ) 			{ $this->Groups = $inDataArray['groups']; }
		if( isset($inDataArray['activegroup']) ) 		{ $this->ActiveGroup = $inDataArray['activegroup']; }
		if( isset($inDataArray['activecharacter']) ) 		{ $this->ActiveCharacter = $inDataArray['activecharacter']; }
		if( isset($inDataArray['groupmanagermode']) ) 		{ $this->GroupManagerMode = $inDataArray['groupmanagermode']; }

		if( isset($inDataArray['camps']) ) 			{ $this->Camps = $inDataArray['camps']; }
		if( isset($inDataArray['possibleapecializations']) ) 	{ $this->PossibleSpecializations = $inDataArray['possibleapecializations']; }
		if( isset($inDataArray['possibleprofiles']) ) 		{ $this->PossibleProfiles = $inDataArray['possibleprofiles']; }
		if( isset($inDataArray['possibleactions']) ) 		{ $this->PossibleActions = $inDataArray['possibleactions']; }
		if( isset($inDataArray['activities']) ) 		{ $this->Activities = $inDataArray['activities']; }
	}


	//--GET FUNCTIONS--
	public function GetInvitations() { return $this->Invitations; }
	public function GetAllegiances() { return $this->Allegiances; }

	public function GetGroups() { return $this->Groups; }
	public function GetActiveGroup() { return $this->ActiveGroup; }
	public function GetActiveCharacter() { return $this->ActiveCharacter; }
	public function GetGroupManagerMode() { return $this->GroupManagerMode; }

	public function GetCamps() { return $this->Camps; }
	public function GetPossibleSpecializations() { return $this->PossibleSpecializations; }
	public function GetPossibleProfiles() { return $this->PossibleProfiles; }
	public function GetPossibleActions() { return $this->PossibleActions; }
	public function GetActivities() { return $this->Activities; }


	//--SET FUNCTIONS--
	public function SetInvitations($inList) { $this->Invitations = $inList; }
	public function SetAllegiances($inList) { $this->Allegiances = $inList; }

	public function SetGroups($inList) { $this->Groups = $inList; }
	public function SetActiveGroup($inGroup) { $this->ActiveGroup = $inGroup; }
	public function SetActiveCharacter($inCharacter) { $this->ActiveCharacter = $inCharacter; }
	public function SetGroupManagerMode($inBool) { $this->GroupManagerMode = $inBool; }

	public function SetCamps($inList) { $this->Camps = $inList; }
	public function SetPossibleSpecializations($inList) { $this->PossibleSpecializations = $inList; }
	public function SetPossibleProfiles($inList) { $this->PossibleProfiles = $inList; }
	public function SetPossibleActions($inList) { $this->PossibleActions = $inList; }
	public function SetActivities($inList) { $this->Activities = $inList; }


	//--OTHERS--
	public function GetCharacterByID($inID) {
		foreach ($this->Allegiances as $entry) {
			if( $entry['characterid'] == $inID ) { return new Character( ['id' => $entry['characterid'], 'firstname' => $entry['characterfirstname'], 'lastname' => $entry['characterlastname'] ] ); }
		}
	}
	public function GetActivityByID( $inID ) { 
		foreach ($this->Activities as  $activity) {	if( $activity->GetID() == $inID ) { return $activity; }		}
		return False; 
	}
	public function GetPossibleActionsForProfile( $inProfileCode ) { 
		$lActionList = array();
		foreach ($this->PossibleActions as $action) { 	if( $action['profilecode'] == $inProfileCode || $action['profilecode'] == 'P' ) { $lActionList[] = $action; } 	}
		return $lActionList; 
	}
	public function GetActionSelectionControl( $inActionCode, $inMaxPurchases, $inCost ) { 
		$lControl = ''; $lMaxPurchases = $inMaxPurchases;

		// Adjust max purchases
		if( $lMaxPurchases == NULL ) { $lMaxPurchases = floor($this->ActiveGroup->GetInfluenceCount() / $inCost); }
		else { $lMaxPurchases = min( $lMaxPurchases, floor($this->ActiveGroup->GetInfluenceCount() / $inCost) ); }
		
		if( isset($_POST[$inActionCode.'-registered']) ) { $lMaxPurchases +=  $_POST[$inActionCode.'-registered']['purchases']; }

		// Create control
		if( $lMaxPurchases ) {
			$lControl = '<select name="'.$inActionCode.'-selection" style="border:solid black 1px;"><option value="0"></option>';
			for ($i=1; $i <= $lMaxPurchases; $i++) {
				$selected = '';
					    if( isset($_POST[$inActionCode.'-selection']) && $_POST[$inActionCode.'-selection'] == $i ) { $selected = 'selected'; }
					elseif( isset($_POST[$inActionCode.'-registered']) && $_POST[$inActionCode.'-registered']['purchases'] == $i ) {  $selected = 'selected'; }
				$lControl .= '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
			}
			$lControl .= '</select>';
		}
		
		return $lControl; 
	}
	public function GetActionPrecisionControl( $inActionCode ) { 
		$lControl = ''; 
		$lPostedPrecision = '';
			    if( isset($_POST[$inActionCode.'-precision']) ) { $lPostedPrecision = $_POST[$inActionCode.'-precision']; }
			elseif( isset($_POST[$inActionCode.'-registered']) ) { $lPostedPrecision = $_POST[$inActionCode.'-registered']['moreinfo']; }

		if( $inActionCode == 'SPECGROUPE' ) {
			$lControl = '<select name="'.$inActionCode.'-precision" style="border:solid black 1px;">'; 
			foreach ($this->PossibleSpecializations as $spec) {
				$selected = '';
					if( $spec['code'] == $lPostedPrecision) { $selected = 'selected'; }
				$lControl .= '<option value="'.$spec['code'].'" '.$selected.'>'.$spec['name'].'</option>';
			}
			$lControl .= '</select>';
		}
		elseif( in_array($inActionCode, ['LIMITEGN1','RESEAU1','RESEAU2','QUETE','SOUTIEN','PVTEMP','RAVITAIL','MISSIONREL']) ) { $lControl = '<input type="hidden" name="'.$inActionCode.'-precision" value=""/>'; }
		elseif( in_array($inActionCode, ['COMMANDE','CARAVANE','CONVOI']) ) {
			// Long list
			$lControl = '<textarea name="'.$inActionCode.'-precision" cols="12" rows="5" placeholder="Marchandises et rabais">'.$lPostedPrecision.'</textarea>';		
		}
		else {
			// Normal precision control
			$lControl = '<textarea name="'.$inActionCode.'-precision" cols="12" rows="1" placeholder="Cible ou choix.">'.$lPostedPrecision.'</textarea>';		
		}
		
		return $lControl; 
	}


	//--PRINT OBJECT FOR DEBUG PURPOSES--
	public function PrintHTML()
	{
		echo '<DIV class="debug">';
		echo '<b><u>Group Manager</u></b><br />';
		echo 'Invitations: ' . count($this->Invitations) . '<br />';
		echo 'Applications: ' . count($this->Applications) . '<br />';
		echo 'Allegiances: ' . count($this->Allegiances) . '<br />';
		echo '-------<br />';
		echo 'Number of groups: ' . count($this->Groups) . '<br />';
		echo 'Active group ID: ' . $this->ActiveGroup->GetID() . '<br />';
		echo 'Active character ID: ' . $this->ActiveCharacter->GetID() . '<br />';
		echo '</DIV>';
	}

} // END of GroupManager class

?>
