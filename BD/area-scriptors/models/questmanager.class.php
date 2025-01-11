<?php

/*
=SCRIPTOR FILE=
╔══CLASS════════════════════════════════════════════════════════╗
║	== Quest Manager Model v1.2 r5 ==			║
║	Represents a list of quests to be managed by scriptors.	║
║	Serializable.						║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('models/quest.class.php');

class QuestManager
{

protected $PersonalQuests;
protected $GroupQuests;
protected $MythicQuests;

protected $SelectedQuest;
protected $Character;		// Selected quest's target character. NULL for group quests.
protected $Group;		// Selected quest's target group. NULL for personal and mythic quests.
protected $Resumes;

protected $Scriptors;

protected $Titles;
protected $TitlePowers;




	//--CONSTRUCTOR--
	public function __construct( $inDataArray =array() )
	{

		if( isset($inDataArray['personalquests']) ) 		{ $this->PersonalQuests = $inDataArray['personalquests']; }
		if( isset($inDataArray['groupquests']) ) 		{ $this->GroupQuests = $inDataArray['groupquests']; }
		if( isset($inDataArray['mythicquests']) ) 		{ $this->MythicQuests = $inDataArray['mythicquests']; }

		if( isset($inDataArray['selectedquest']) ) 		{ $this->SelectedQuest = $inDataArray['selectedquest']; }
		if( isset($inDataArray['character']) ) 			{ $this->Character = $inDataArray['character']; }
		if( isset($inDataArray['group']) ) 			{ $this->Group = $inDataArray['group']; }
		if( isset($inDataArray['resumes']) ) 			{ $this->Resumes = $inDataArray['resumes']; }

		if( isset($inDataArray['scriptors']) ) 			{ $this->Scriptors = $inDataArray['scriptors']; }

		if( isset($inDataArray['titles']) ) 			{ $this->Titles = $inDataArray['titles']; }
		if( isset($inDataArray['titlepowers']) ) 		{ $this->TitlePowers = $inDataArray['titlepowers']; }
	}


	//--GET FUNCTIONS--
	public function GetPersonalQuests() { return $this->PersonalQuests; }
	public function GetGroupQuests() { return $this->GroupQuests; }
	public function GeMythicQuests() { return $this->MythicQuests; }

	public function GetSelectedQuest() { return $this->SelectedQuest; }
	public function GetCharacter() { return $this->Character; }
	public function GetGroup() { return $this->Group; }
	public function GetResumes() { return $this->Resumes; }

	public function GetScriptors() { return $this->Scriptors; }
	
	public function GetTitles() { return $this->Titles; }
	public function GetTitlePowers() { return $this->TitlePowers; }


	//--SET FUNCTIONS--
	public function SetPersonalQuests($inList) { $this->PersonalQuests = $inList; }
	public function SetGroupQuests($inList) { $this->GroupQuests = $inList; }
	public function SetMythicQuests($inList) { $this->MythicQuests = $inList; }

	public function SetSelectedQuest($inQuest) { $this->SelectedQuest = $inQuest; }
	public function SetCharacter($inCharacter) { $this->Character = $inCharacter; }
	public function SetGroup($inGroup) { $this->Group = $inGroup; }
	public function SetResumes($inList) { $this->Resumes = $inList; }

	public function SetScriptors($inScriptors) { $this->Scriptors = $inScriptors; }

	public function SetTitles($inList) { $this->Titles = $inList; }
	public function SetTitlePowers($inList) { $this->TitlePowers = $inList; }


	//--GET LIST COUNTS--
	public function GetPersonalQuestCount() { return count($this->PersonalQuests); }
	public function GetGroupQuestCount() { return count($this->GroupQuests); }
	public function GetMythicQuestCount() { return count($this->MythicQuests); }

	public function GetResumeCount() { return count($this->Resumes); }


	//--OTHERS--
	public function SelectQuest($inQuestType, $inID) { 
		if( $inQuestType == 'PERSONAL' ) {
			foreach ($this->PersonalQuests as $i => $quest) {
				if( $quest->GetID() == $inID ) { $this->SelectedQuest = $quest;	return True; }
			}
		}
		elseif( $inQuestType == 'GROUP' ) {
			foreach ($this->GroupQuests as $i => $quest) {
				if( $quest->GetID() == $inID ) { $this->SelectedQuest = $quest; return True; }
			}
		}
		elseif( $inQuestType == 'MYTHIC' ) {
			foreach ($this->MythicQuests as $i => $quest) {
				if( $quest->GetID() == $inID ) { $this->SelectedQuest = $quest;	return True; }
			}
		}
		return False; 
	}
	public function GetTitle( $inTitleCode ) { 
		foreach ($this->Titles as $title) {
			if( $title['code'] == $inTitleCode ) { return $title; }
		}
		return False; 
	}


	//--PRINT OBJECT FOR DEBUG PURPOSES--
	public function PrintHTML()
	{
		echo '<DIV class="debug">';
		echo '<b><u>Quest manager</u></b><br />';
		echo '-------<br />';
		echo '</DIV>';
	}


} // END of QuestManager class

?>
