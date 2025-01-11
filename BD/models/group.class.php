<?php
define('EXTENDED_RULES_START_DATE', '2021-08-01 00:00:00');
define('GROUP_MINIMUM_SIZE', 5);
define('MAX_INSTITUTIONS', 2);
define('MAX_ACTIONS', 20);
define('MAX_ADVANTAGES', 2);
define('INSTITUTION_COMMANDS_DELAY', 3); #days Default : 14

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Group Model v1.2 r9 ==				║
║	Represents a group made of users.			║
║	Serializable.						║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('institution.class.php'); 		// Institution definition

class Group
{

protected $ID;
protected $Name;
protected $Status;

protected $Description;
protected $Background;
protected $Leadership;
protected $AffiliatedNobles;
protected $MoreInformation;
protected $BaseCamp;

protected $Values;
protected $Objectives;

protected $Members;
protected $PeopleInCharge;
protected $Influence;
protected $MaxInfluence;

protected $Specialization;
protected $Institutions;
protected $Actions;
protected $Advantages;

protected $Quests;
protected $Resumes;

protected $Logs;


	//--CONSTRUCTOR--
	public function __construct( $inDataArray =array() )
	{

		if( isset($inDataArray['id']) ) 			{ $this->ID = $inDataArray['id']; }
		if( isset($inDataArray['name']) )			{ $this->Name = $inDataArray['name']; }
		if( isset($inDataArray['status']) ) 			{ $this->Status = $inDataArray['status']; }

		if( isset($inDataArray['description']) ) 		{ $this->Description = $inDataArray['description']; }
		if( isset($inDataArray['background']) ) 		{ $this->Background = $inDataArray['background']; }
		if( isset($inDataArray['leadership']) ) 		{ $this->Leadership = $inDataArray['leadership']; }
		if( isset($inDataArray['affiliatednobles']) ) 		{ $this->AffiliatedNobles = $inDataArray['affiliatednobles']; }
		if( isset($inDataArray['moreinfo']) ) 			{ $this->MoreInformation = $inDataArray['moreinfo']; }
		if( isset($inDataArray['basecamp']) ) 			{ $this->BaseCamp = $inDataArray['basecamp']; }

		if( isset($inDataArray['values']) ) 			{ $this->Values = $inDataArray['values']; }
		if( isset($inDataArray['objectives']) ) 		{ $this->Objectives = $inDataArray['objectives']; }

		if( isset($inDataArray['members']) )			{ $this->Members = $inDataArray['members']; }
		if( isset($inDataArray['peopleincharge']) ) 		{ $this->PeopleInCharge = $inDataArray['peopleincharge']; }
		if( isset($inDataArray['influence']) )			{ $this->Influence = $inDataArray['influence']; }
		if( isset($inDataArray['maxinfluence']) ) 		{ $this->MaxInfluence = $inDataArray['maxinfluence']; }

		if( isset($inDataArray['specialization']) ) 		{ $this->Specialization = $inDataArray['specialization']; }
		if( isset($inDataArray['institutions']) ) 		{ $this->Institutions = $inDataArray['institutions']; }
		if( isset($inDataArray['actions']) ) 			{ $this->Actions = $inDataArray['actions']; }
		if( isset($inDataArray['advantages']) ) 		{ $this->Advantages = $inDataArray['advantages']; }

		if( isset($inDataArray['quests']) ) 			{ $this->Quests = $inDataArray['quests']; }
		if( isset($inDataArray['resumes']) ) 			{ $this->Resumes = $inDataArray['resumes']; }

		if( isset($inDataArray['logs']) ) 			{ $this->Logs = $inDataArray['logs']; }
	}


	//--GET FUNCTIONS--
	public function GetID() { return $this->ID; }
	public function GetName() { return $this->Name; }
	public function GetStatus() { return $this->Status; }

	public function GetDescription() { return $this->Description; }
	public function GetBackground() { return $this->Background; }
	public function GetLeadership() { return $this->Leadership; }
	public function GetAffiliatedNobles() { return $this->AffiliatedNobles; }
	public function GetMoreInformation() { return $this->MoreInformation; }
	public function GetBaseCamp() { return $this->BaseCamp; }

	public function GetValues() { return $this->Values; }
	public function GetObjectives() { return $this->Objectives; }

	public function GetMembers() { return $this->Members; }
	public function GetPeopleInCharge() { return $this->PeopleInCharge; }
	public function GetInfluence() { return $this->Influence; }
	public function GetMaxInfluence() { return $this->MaxInfluence; }

	public function GetSpecialization() { return $this->Specialization; }
	public function GetInstitutions() { return $this->Institutions; }
	public function GetActions() { return $this->Actions; }
	public function GetAdvantages() { return $this->Advantages; }

	public function GetQuests() { return $this->Quests; }
	public function GetResumes() { return $this->Resumes; }

	public function GetLogs() { return $this->Logs; }


	//--SET FUNCTIONS--
	public function SetID($inID) { $this->ID = $inID; }
	public function SetName($inName) { $this->Name = $inName; }
	public function SetStatus($inStatus) { $this->Status = $inStatus; }

	public function SetDescription($inText) { $this->Description = $inText; }
	public function SetBackground($inText) { $this->Background = $inText; }
	public function SetLeadership($inText) { $this->Leadership = $inText; }
	public function SetAffiliatedNobles($inText) { $this->AffiliatedNobles = $inText; }
	public function SetMoreInformation($inText) { $this->MoreInformation = $inText; }
	public function SetBaseCamp($inCamp) { $this->BaseCamp = $inCamp; }

	public function SetValues($inList) { $this->Values = $inList; }
	public function SetObjectives($inList) { $this->Objectives = $inList; }

	public function SetMembers($inList) { $this->Members = $inList; }
	public function SetPeopleInCharge($inList) { $this->PeopleInCharge = $inList; }
	public function SetInfluence($inAmount) { $this->Influence = $inAmount; }
	public function SetMaxInfluence($inAmount) { $this->MaxInfluence = $inAmount; }

	public function SetSpecialization($inCode) { $this->Specialization = $inCode; }
	public function SetInstitutions($inList) { $this->Institutions = $inList; }
	public function SetActions($inList) { $this->Actions = $inList; }
	public function SetAdvantages($inList) { $this->Advantages = $inList; }

	public function SetQuests($inList) { $this->Quests = $inList; }
	public function SetResumes($inList) { $this->Resumes = $inList; }

	public function SetLogs($inList) { $this->Logs = $inList; }


	//--GET COUNT FUNCTIONS--
	public function GetMemberCount() { return count($this->Members); }
	public function GetPeopleInChargeCount() { return count($this->PeopleInCharge); }
	public function GetInstitutionCount() { return count($this->Institutions); }

	public function GetActiveMemberCount() {
		$lActiveMemberList = array();
		foreach ($this->Members as $member) {
			if( $member->GetStatus() == 'LEVEL' ) { $lActiveMemberList[] = $member; }
		}
		return count($lActiveMemberList); 
	}
	public function GetOpenedQuestApplicationCount() {
		$lQuestList = array();
		foreach ($this->Quests as $quest) {
			if( in_array($quest->GetStatus(), array('DEM', 'REPR', 'ACTIF', 'SUITE')) ) { $lQuestList[] = $quest; }
		}
		return count($lQuestList); 
	}
	public function GetInfluenceCount() {
		$lTotal = 0;
		foreach ($this->Influence as $influence) { $lTotal += $influence['points']; }
		return $lTotal; 
	}
	public function GetActionCountByActivity( $inActivityID ) {
		$lActionList = array();
		foreach ($this->Actions as $action) {
			if( $action['activityid'] == $inActivityID ) { $lActionList[] = $action; }
		}
		return count($lActionList); 
	}
	public function GetActionCountByStatus( $inStatus ) {
		$lActionList = array();
		foreach ($this->Actions as $action) {
			if( $action['status'] == $inStatus ) { $lActionList[] = $action; }
		}
		return count($lActionList); 
	}
	public function GetMaximumActionCountPerActivity() { 
		$lTotalGroupActions = 0;
		foreach ($this->Institutions as $institution) {
			$lTotalGroupActions += ceil( $institution->GetLevel()/2 );
			if( $lTotalGroupActions >= MAX_ACTIONS ) { return MAX_ACTIONS; }
		}
		return $lTotalGroupActions;
	}


	//--OTHER FUNCTIONS--
	public function GetActivityActions( $inActivityID ) { 
		$lActionList = array();
		foreach ($this->Actions as $action) { 
			if($action['activityid'] == $inActivityID) { $lActionList[ $action['code'] ] = $action; } 
		}
		return $lActionList; 
	}

	public function GetInstitutionByID($inID) { 
		foreach ($this->Institutions as $institution) {
			if( $institution->GetID() == $inID ) { return $institution; }
		}
		return False; 
	}


	public function GetInstitutionsByProfile($inProfileCode) {
		$lInstitutionList = array();
		foreach ($this->Institutions as $institution) {
			if( $institution->GetProfile() == $inProfileCode ) { $lInstitutionList[] = $institution; }
		}
		return $lInstitutionList; 
	}


	//--PRINT OBJECT FOR DEBUG PURPOSES--
	public function PrintHTML()
	{
		echo '<DIV class="debug">';
		echo '<b><u>Group</u></b><br />';
		echo 'ID: ' . $this->ID . '<br />';
		echo 'Name: ' . $this->Name . '<br />';
		echo 'Status: ' . $this->Status . '<br />';
		echo '-------<br />';
		echo 'Values: ' . count($this->Values) . '<br />';
		echo 'Objectives: ' . count($this->Objectives) . '<br />';
		echo '-------<br />';
 		echo 'Members: ' . count($this->Members) . '<br />';
		echo 'Persons in charge: ' . count($this->PersonsInCharge) . '<br />';
		echo '-------<br />';
		echo 'Specialization: ' . $this->Specialization . '<br />';
		echo 'Institutions: ' . count($this->Institutions) . '<br />';
		echo '-------<br />';
		echo 'Quests: ' . count($this->Quests) . '<br />';
		echo 'Workers: ' . count($this->Resumes) . '<br />';
		echo '-------<br />';
		echo 'Logs: ' . count($this->Logs) . '<br />';
		echo '</DIV>';
	}

} // END of Group class

?>
