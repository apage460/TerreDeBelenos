<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Institution Model v1.2 r0 ==				║
║	Represents a foreign organization.			║
║	Serializable.						║
╚═══════════════════════════════════════════════════════════════╝
*/


class Institution
{

protected $ID;
protected $Name;
protected $Status;

protected $Profile;
protected $CountyID;
protected $Level;

protected $Leader;
protected $Description;
protected $HiddenAgenda;

protected $GroupID;
protected $GroupName;

protected $Actions = array();
protected $GivenAdvantages = array();


	//--CONSTRUCTOR--
	public function __construct( $inDataArray =array() )
	{

		if( isset($inDataArray['id']) ) 			{ $this->ID = $inDataArray['id']; }
		if( isset($inDataArray['name']) )			{ $this->Name = $inDataArray['name']; }
		if( isset($inDataArray['status']) ) 			{ $this->Status = $inDataArray['status']; }

		if( isset($inDataArray['profile']) ) 			{ $this->Profile = $inDataArray['profile']; }
		if( isset($inDataArray['countyid']) ) 			{ $this->CountyID = $inDataArray['countyid']; }
		if( isset($inDataArray['level']) ) 			{ $this->Level = $inDataArray['level']; }

		if( isset($inDataArray['leader']) ) 			{ $this->Leader = $inDataArray['leader']; }
		if( isset($inDataArray['description']) ) 		{ $this->Description = $inDataArray['description']; }
		if( isset($inDataArray['hiddenagenda']) ) 		{ $this->HiddenAgenda = $inDataArray['hiddenagenda']; }

		if( isset($inDataArray['groupid']) ) 			{ $this->GroupID = $inDataArray['groupid']; }
		if( isset($inDataArray['groupname']) ) 			{ $this->GroupName = $inDataArray['groupname']; }

		if( isset($inDataArray['actions']) ) 			{ $this->Actions = $inDataArray['actions']; }
		if( isset($inDataArray['givenadvantages']) ) 		{ $this->GivenAdvantages = $inDataArray['givenadvantages']; }
	}


	//--GET FUNCTIONS--
	public function GetID() { return $this->ID; }
	public function GetName() { return $this->Name; }
	public function GetStatus() { return $this->Status; }

	public function GetProfile() { return $this->Profile; }
	public function GetCountyID() { return $this->CountyID; }
	public function GetLevel() { return $this->Level; }

	public function GetLeader() { return $this->Leader; }
	public function GetDescription() { return $this->Description; }
	public function GetHiddenAgenda() { return $this->HiddenAgenda; }

	public function GetGroupID() { return $this->GroupID; }
	public function GetGroupName() { return $this->GroupName; }

	public function GetActions() { return $this->Actions; }
	public function GetGivenAdvantages() { return $this->GivenAdvantages; }


	//--SET FUNCTIONS--
	public function SetID($inID) { $this->ID = $inID; }
	public function SetName($inName) { $this->Name = $inName; }
	public function SetStatus($inStatus) { $this->Status = $inStatus; }

	public function SetProfile($inProfile) { $this->Profile = $inProfile; }
	public function SetCountyID($inID) { $this->CountyID = $inID; }
	public function SetLevel($inLevel) { $this->Level = $inLevel; }

	public function SetLeader($inText) { $this->Leader = $inText; }
	public function SetDescription($inText) { $this->Description = $inText; }
	public function SetHiddenAgenda($inText) { $this->HiddenAgenda = $inText; }

	public function SetGroupID($inID) { $this->GroupID = $inID; }
	public function SetGroupName($inName) { $this->GroupName = $inName; }

	public function SetActions($inList) { $this->Actions = $inList; }
	public function SetGivenAdvantages($inList) { $this->GivenAdvantages = $inList; }


	//--GET COUNT FUNCTIONS--
	public function GetActionCount() { return count($this->Actions); }
	public function GetGivenAdvantageCount() { return count($this->GivenAdvantages); }
	public function GetTotalAdvantageCount() { 
		    if( $this->Level >= 4 ) { return 2; }
		elseif( $this->Level >= 2 ) { return 1; }
		return 0;
	}
	public function GetRemainingAdvantageCount() { 
		    if( $this->Level >= 4 ) { return 2 - count($this->GivenAdvantages); }
		elseif( $this->Level >= 2 ) { return 1 - count($this->GivenAdvantages); }
		return 0;
	}
	public function GetActionCountByActivity( $inActivityID ) {
		$lActionList = array();
		foreach ($this->Actions as $action) {
			if( $action['activityid'] == $inActivityID ) { $lActionList[] = $action; }
		}
		return count($lActionList); 
	}

	//--OTHER FUNCTIONS--
	public function GetActivityActions( $inActivityID ) {
		$lActionList = array();
		foreach ($this->Actions as $action) {
			if( $action['activityid'] == $inActivityID ) { $lActionList[] = $action; }
		}
		return $lActionList; 
	}
	public function GetProfileName() { 
		    if( $this->Profile == 'M' ) { return "Militaire"; }
		elseif( $this->Profile == 'R' ) { return "Religieuse"; }
		elseif( $this->Profile == 'C' ) { return "Commerciale"; }
		elseif( $this->Profile == 'A' ) { return "Académique"; }
		elseif( $this->Profile == 'I' ) { return "Interlope"; }
		  
		return "Politique"; 
	}

	public function AddAction($inAction) { $this->Actions[] = $inAction; }
	public function AddGivenAdvantage($inAdvantage) { $this->GivenAdvantages[] = $inAdvantage; }


	//--PRINT OBJECT FOR DEBUG PURPOSES--
	public function PrintHTML()
	{
		echo '<DIV class="debug">';
		echo '<b><u>Institution</u></b><br />';
		echo 'ID: ' . $this->ID . '<br />';
		echo 'Name: ' . $this->Name . '<br />';
		echo 'Status: ' . $this->Status . '<br />';
		echo '-------<br />';
		echo 'Profile: ' . count($this->Profile) . '<br />';
		echo 'County: ' . count($this->CountyID) . '<br />';
 		echo 'Level: ' . count($this->Members) . '<br />';
		echo '-------<br />';
		echo 'Group: ' . $this->GroupID . '<br />';
		echo '</DIV>';
	}

} // END of Institution class

?>
