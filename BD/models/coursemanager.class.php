<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Course Manager Model v1.2 r4 ==			║
║	Identifies possible masters and courses.		║
║	Serializable.						║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('models/character.class.php');
include_once('models/activity.class.php');

class CourseManager
{

protected $Character;
protected $TeachableSkills;

protected $Masters;		// excludes NPCs for practical purposes
protected $NPCs;

protected $SelectedMaster;
protected $SelectedStudent;

protected $ValidActivities;


	//--CONSTRUCTOR--
	public function __construct( $inDataArray =array() )
	{

		if( isset($inDataArray['character']) )		{ $this->Character = $inDataArray['character']; }
		if( isset($inDataArray['teachableskills']) )	{ $this->TeachableSkills = $inDataArray['teachableskills']; }

		if( isset($inDataArray['masters']) )		{ $this->Masters = $inDataArray['masters']; }
		if( isset($inDataArray['npcs']) )		{ $this->NPCs = $inDataArray['npcs']; }

		if( isset($inDataArray['selectedmaster']) )	{ $this->SelectedMaster = $inDataArray['selectedmaster']; }
		if( isset($inDataArray['selectedstudent']) )	{ $this->SelectedStudent = $inDataArray['selectedstudent']; }

		if( isset($inDataArray['validactivities']) )	{ $this->ValidActivities = $inDataArray['validactivities']; }
	}


	//--GET FUNCTIONS--
	public function GetCharacter() { return $this->Character; }
	public function GetTeachableSkills() { return $this->TeachableSkills; }

	public function GetMasters() { return $this->Masters; }
	public function GetNPCs() { return $this->NPCs; }

	public function GetSelectedMaster() { return $this->SelectedMaster; }
	public function GetSelectedStudent() { return $this->SelectedStudent; }

	public function GetValidActivities() { return $this->ValidActivities; }


	//--SET FUNCTIONS--
	public function SetCharacter($inCharacter) { $this->Character = $inCharacter; }
	public function SetTeachableSkills($inList) { $this->TeachableSkills = $inList; }

	public function SetMasters($inList) { $this->Masters = $inList; }
	public function SetNPCs($inList) { $this->NPCs = $inList; }

	public function SetSelectedMaster($inMaster) { $this->SelectedMaster = $inMaster; }
	public function SetSelectedStudent($inCharacter) { $this->SelectedStudent = $inCharacter; }

	public function SetValidActivities($inList) { $this->ValidActivities = $inList; }


	//--OTHERS--
	public function SelectPCMaster($inIndex) { $this->SelectedMaster = $this->Masters[$inIndex]; }
	public function SelectNPCMaster($inIndex) { $this->SelectedMaster = $this->NPCs[$inIndex]; }

	public function GetMasterFromID($inID) { 
		foreach ($this->Masters as $master) {
		 	if( $master->GetID() == $inID ) { return $master; }
		}
		return False;
	}


	//--PRINT OBJECT FOR DEBUG PURPOSES--
	public function PrintHTML()
	{
		echo '<DIV class="debug">';
		echo '<b><u>Course manager</u></b><br />';
		echo 'Masters: ' . count($this->Masters) . '<br />';
		echo '-------<br />';
		echo '</DIV>';
	}


} // END of CourseManager class

?>
