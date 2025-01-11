<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Master List Model v1.2 r11 ==			║
║	Centralize data lists crucial for site management.	║
║	Serializable.						║
╚═══════════════════════════════════════════════════════════════╝
*/

class MasterList
{

protected $Activities;

protected $QuestOptions;
protected $Archetypes;

protected $MinorTalents;
protected $MajorTalents;
protected $PrestigeTitles;
protected $MythicBeings;

protected $Spells;		// 'name','skillcode','religioncode'
protected $Curses;		// 'name','skillcode'
protected $Recipes;		// 'name','skillcode'
protected $Jobs;		// 'name','skillcode'

protected $ResurrectionMethods;	// 'name'

protected $Counties;
protected $Kingdoms;

protected $NPCs;

	//--CONSTRUCTOR--
	public function __construct( $inUserDataArray =array() )
	{

		if( isset($inUserDataArray['activities']) )		{ $this->Activities = $inUserDataArray['activities']; }

		if( isset($inUserDataArray['questoptions']) ) 		{ $this->QuestOptions = $inUserDataArray['questoptions']; }
		if( isset($inUserDataArray['archetypes']) ) 		{ $this->Archetypes = $inUserDataArray['archetypes']; }

		if( isset($inUserDataArray['minortalents']) )		{ $this->MinorTalents = $inUserDataArray['minortalents']; }
		if( isset($inUserDataArray['majortalents']) ) 		{ $this->MajorTalents = $inUserDataArray['majortalents']; }
		if( isset($inUserDataArray['prestigetitles']) ) 	{ $this->PrestigeTitles = $inUserDataArray['prestigetitles']; }
		if( isset($inUserDataArray['mythicbeings']) ) 		{ $this->MythicBeings = $inUserDataArray['mythicbeings']; }
		
		if( isset($inUserDataArray['spells']) )			{ $this->Spells = $inUserDataArray['spells']; }
		if( isset($inUserDataArray['curses']) )			{ $this->Curses = $inUserDataArray['curses']; }
		if( isset($inUserDataArray['recipes']) ) 		{ $this->Recipes = $inUserDataArray['recipes']; }
		if( isset($inUserDataArray['jobs']) ) 			{ $this->Jobs = $inUserDataArray['jobs']; }
		
		if( isset($inUserDataArray['resurrectionmethods']) )	{ $this->ResurrectionMethods = $inUserDataArray['resurrectionmethods']; }

		if( isset($inUserDataArray['comtes']) )			{ $this->Comtes = $inUserDataArray['comtes']; }
		if( isset($inUserDataArray['royaumes']) ) 		{ $this->Royaumes = $inUserDataArray['royaumes']; }

		if( isset($inUserDataArray['pnj']) ) 			{ $this->NPCs = $inUserDataArray['pnj']; }
	}


	//--GET FUNCTIONS--
	public function GetActivities() { return $this->Activities; }

	public function GetQuestOptions() { return $this->QuestOptions; }
	public function GetArchetypes() { return $this->Archetypes; }

	public function GetMinorTalents() { return $this->MinorTalents; }
	public function GetMajorTalents() { return $this->MajorTalents; }
	public function GetPrestigeTitles() { return $this->PrestigeTitles; }
	public function GetMythicBeings() { return $this->MythicBeings; }

	public function GetSpells() { return $this->Spells; }
	public function GetCurses() { return $this->Curses; }
	public function GetRecipes() { return $this->Recipes; }
	public function GetJobs() { return $this->Jobs; }

	public function GetActions() { return $this->Jobs; }

	public function GetResurrectionMethods() { return $this->ResurrectionMethods; }

	public function GetCounties() { return $this->Counties; }
	public function GetKingdoms() { return $this->Kingdoms; }

	public function GetNPCs() { return $this->NPCs; }

	//--SET FUNCTIONS--
	public function SetActivities($inList) { $this->Activities = $inList; }

	public function SetQuestOptions($inList) { $this->QuestOptions = $inList; }
	public function SetArchetypes($inList) { $this->Archetypes = $inList; }

	public function SetMinorTalents($inList) { $this->MinorTalents = $inList; }
	public function SetMajorTalents($inList) { $this->MajorTalents = $inList; }
	public function SetPrestigeTitles($inList) { $this->PrestigeTitles = $inList; }
	public function SetMythicBeings($inList) { $this->MythicBeings = $inList; }

	public function SetSpells($inList) { $this->Spells = $inList; }
	public function SetCurses($inList) { $this->Curses = $inList; }
	public function SetRecipes($inList) { $this->Recipes = $inList; }
	public function SetJobs($inList) { $this->Jobs = $inList; }

	public function SetResurrectionMethods($inList) { $this->ResurrectionMethods = $inList; }

	public function SetCounties($inList) { $this->Counties = $inList; }
	public function SetKingdoms($inList) { $this->Kingdoms = $inList; }

	public function SetNPCs($inList) { $this->NPCs = $inList; }

	//--OTHERS--
	public function IsMinorTalent($inTalentCode) { 
		foreach( $this->MinorTalents as $talent ) {
			if( $talent['code'] == $inTalentCode ) { return True; }
		}; 
		return False;
	}
	public function IsMajorTalent($inTalentCode) { 
		foreach( $this->MajorTalents as $talent ) {
			if( $talent['code'] == $inTalentCode ) { return True; }
		}; 
		return False;
	}
	public function IsPrestigeTitle($inTalentCode) { 
		foreach( $this->PrestigeTitles as $title ) {
			if( $title['code'] == $inTalentCode ) { return True; }
		}; 
		return False;
	}
	public function GetCountyByID($inID) { 
		foreach( $this->Counties as $county ) {
			if($county['id'] == $inID) { return $county; }
		}
		return array(); 
	}
	public function GetActivityByID($inID) { 
		foreach( $this->Activities as $activity ) {
			if( $activity->GetID() == $inID ) { return $activity; } 
		}
		return False; 
	}
	public function GetLastMainActivity() { 
		foreach( $this->Activities as $activity ) {
			# Positive number of days means it's in the past. Activities are ordered from most to least recent.
			if( $activity->GetType() == 'GN' && $activity->GetDaysSinceBeginning() >= 0 ) { return $activity; } 
		}
		return False; 
	}
	public function GetNextMainActivity() { 
		$lActivityList = array_reverse($this->Activities);
		$now = new DateTime();
		foreach( $lActivityList as $activity ) {
			$end = new DateTime($activity->GetEndingDate());			// Compare to ending date so we get the current activity if it started.
			if( $now->diff($end)->format("%R") == '+' ) { return $activity; } 	// Activities are sorted in order of ascending dates. First that's not over is the correct one.
		}
		return False;
	}
	public function GetArchetypesByClass($inClassCode) { 
		$lArchetypeList = array();
		foreach( $this->Archetypes as $archetype ) {
			if( $archetype['classcode'] == $inClassCode ) { $lArchetypeList[] = $archetype; } 
		}
		return $lArchetypeList; 
	}
	public function GetArchetypeName($inArchetypeCode) { 
		foreach ($this->Archetypes as $archetype) {
			if( $archetype['code'] == $inArchetypeCode ) { return $archetype['name']; } 
		}
		return False; 
	}
	public function GetPermittedSpells($inSkillCode, $inReligionCode, $inIncludeCurses =False) { 
		$lSpellList = array();
		foreach ($this->Spells as $spell) {
			if( $spell['skillcode'] == $inSkillCode && $spell['religioncode'] == $inReligionCode ) { $lSpellList[] = $spell; }	
		}
		if( $inIncludeCurses ) {
			foreach ($this->Curses as $spell) {
				if( $spell['skillcode'] == $inSkillCode ) { $lSpellList[] = $spell; }
			}			
		}
		return $lSpellList; 
	}
	public function GetPermittedCurses($inSkillCode) { 
		$lSpellList = array();
		foreach ($this->Curses as $spell) {
			if( $spell['skillcode'] == $inSkillCode ) { $lSpellList[] = $spell; }
		}			
		return $lSpellList; 
	}
	public function GetFreeMagicLevelSpells($inMaxLevel, $inReligionCode, $inIncludeCurses =False, $inIncludeHighMagic =False) { 
		$lMaxLevel = $inMaxLevel;
			if(!$inIncludeHighMagic) { $lMaxLevel = 4; }
		$lSpellList = array();
		for ($i=1; $i <= $lMaxLevel; $i++) { 
			$lSpellList = array_merge( $lSpellList, $this->GetPermittedSpells('SORTN'.$i, $inReligionCode, $inIncludeCurses) );
		}
		return $lSpellList;
	}		
	public function GetRecipesBySkill($inSkillCode) { 
		$lRecipeList = array();
		foreach ($this->Recipes as $recipe) {
			if( $recipe['skillcode'] == $inSkillCode ) { $lRecipeList[] = $recipe; }
		}
		return $lRecipeList; 
	}
	public function GetFreeAlchemicLevelRecipes($inMaxLevel) { 
		$lRecipeList = array();
		for ($i=1; $i <= $inMaxLevel; $i++) { 
			$lRecipeList = array_merge( $lRecipeList, $this->GetRecipesBySkill('RECETA'.$i) );
		}

		$recipename = array_column($lRecipeList, 'name');
		array_multisort($recipename, SORT_ASC, $lRecipeList);

		return $lRecipeList;
	}		
	public function GetFreeBotanicLevelRecipes($inMaxLevel) { 
		$lRecipeList = array();
		for ($i=1; $i <= $inMaxLevel; $i++) { 
			$lRecipeList = array_merge( $lRecipeList, $this->GetRecipesBySkill('RECETH'.$i) );
		}

		$recipename = array_column($lRecipeList, 'name');
		array_multisort($recipename, SORT_ASC, $lRecipeList);

		return $lRecipeList;
	}		


	//--PRINT OBJECT FOR DEBUG PURPOSES--
	public function PrintHTML()
	{
		echo '<DIV class="debug">';
		echo '<b><u>Master List</u></b><br />';
		echo 'Activities: <br />' . print_r($this->Activities) . '<br />';
		echo '-------<br />';
		echo 'Assistants: <br />' . print_r($this->Assistants) . '<br />';
		echo '-------<br />';
		echo 'Minor Talents: <br />' . print_r($this->MinorTalents) . '<br />';
		echo '-------<br />';
		echo 'Major Talents: <br />' . print_r($this->MajorTalents) . '<br />';
		echo '-------<br />';
		echo 'Prestige Titles: <br />' . print_r($this->PrestigeTitles) . '<br />';
		echo '-------<br />';
		echo '</DIV>';
	}

} // END of Master List class

?>
