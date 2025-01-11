<?php
define('MAX_XP_TRANSFER', 120);

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Character Model v1.2 r22 ==				║
║	Represents a player's character.			║
║	Serializable.						║
╚═══════════════════════════════════════════════════════════════╝
*/


class Character
{

protected $ID;
protected $Status;
protected $Notes;

protected $FirstName;
protected $LastName;
protected $Race;
protected $RaceCode;
protected $Class;
protected $ClassCode;
protected $Archetype;
protected $ArchetypeCode;
protected $Level;
protected $Religion;
protected $ReligionCode;

protected $Origin;
protected $Background;
protected $QuickNote;

protected $XP;
protected $TransferedXP;
protected $QuestCredits;
protected $Life;

protected $Skills;
protected $Talents;
protected $Teachings;
protected $Titles;
protected $Quests;
protected $Resumes;
protected $Letters;
protected $Approvals;

protected $UserRecentActivities;
protected $CharacterAttendances;

protected $UserID;
protected $UserAccount;
protected $UserName;

protected $Group;

protected $PendingSurvey;		
protected $AnsweredSurveys;		


	//--CONSTRUCTOR--
	public function __construct( $inDataArray =array() )
	{

		if( isset($inDataArray['id']) ) 		{ $this->ID = $inDataArray['id']; }
		if( isset($inDataArray['status']) ) 		{ $this->Status = $inDataArray['status']; }
		if( isset($inDataArray['notes']) ) 		{ $this->Notes = $inDataArray['notes']; }

		if( isset($inDataArray['firstname']) )		{ $this->FirstName = $inDataArray['firstname']; }
		if( isset($inDataArray['lastname']) )		{ $this->LastName = $inDataArray['lastname']; }
		if( isset($inDataArray['race']) )		{ $this->Race = $inDataArray['race']; }
		if( isset($inDataArray['racecode']) )		{ $this->RaceCode = $inDataArray['racecode']; }
		if( isset($inDataArray['class']) )		{ $this->Class = $inDataArray['class']; }
		if( isset($inDataArray['classcode']) )		{ $this->ClassCode = $inDataArray['classcode']; }
		if( isset($inDataArray['archetype']) )		{ $this->Archetype = $inDataArray['archetype']; }
		if( isset($inDataArray['archetypecode']) )	{ $this->ArchetypeCode = $inDataArray['archetypecode']; }
		if( isset($inDataArray['level']) ) 		{ $this->Level = $inDataArray['level']; }
		if( isset($inDataArray['religion']) )		{ $this->Religion = $inDataArray['religion']; }
		if( isset($inDataArray['religioncode']) )	{ $this->ReligionCode = $inDataArray['religioncode']; }

		if( isset($inDataArray['origin']) ) 		{ $this->Origin = $inDataArray['origin']; }
		if( isset($inDataArray['background']) ) 	{ $this->Background = $inDataArray['background']; }
		if( isset($inDataArray['quicknote']) ) 		{ $this->QuickNote = $inDataArray['quicknote']; }

		if( isset($inDataArray['xp']) ) 		{ $this->XP = $inDataArray['xp']; }
		if( isset($inDataArray['transferedxp']) ) 	{ $this->TransferedXP = $inDataArray['transferedxp']; }
		if( isset($inDataArray['questcredits']) ) 	{ $this->QuestCredits = $inDataArray['questcredits']; }
		if( isset($inDataArray['life']) ) 		{ $this->Life = $inDataArray['life']; }

		if( isset($inDataArray['skills']) ) 		{ $this->Skills = $inDataArray['skills']; }
		if( isset($inDataArray['talents']) ) 		{ $this->Talents = $inDataArray['talents']; }
		if( isset($inDataArray['teachings']) ) 		{ $this->Teachings = $inDataArray['teachings']; }
		if( isset($inDataArray['titles']) ) 		{ $this->Titles = $inDataArray['titles']; }
		if( isset($inDataArray['quests']) ) 		{ $this->Quests = $inDataArray['quests']; }
		if( isset($inDataArray['resumes']) ) 		{ $this->Resumes = $inDataArray['resumes']; }
		if( isset($inDataArray['letters']) ) 		{ $this->Letters = $inDataArray['letters']; }
		if( isset($inDataArray['approvals']) ) 		{ $this->Approvals = $inDataArray['approvals']; }

		if( isset($inDataArray['userrecentactivities']) ) 	{ $this->UserRecentActivities = $inDataArray['userrecentactivities']; }
		if( isset($inDataArray['characterattendances']) ) 	{ $this->CharacterAttendances = $inDataArray['characterattendances']; }

		if( isset($inDataArray['userid']) )		{ $this->UserID = $inDataArray['userid']; }
		if( isset($inDataArray['useraccount']) ) 	{ $this->UserAccount = $inDataArray['useraccount']; }
		if( isset($inDataArray['username']) ) 		{ $this->UserName = $inDataArray['username']; }

		if( isset($inDataArray['group']) ) 		{ $this->Group = $inDataArray['group']; }

		if( isset($inDataArray['pendingsurvey']) ) 	{ $this->PendingSurvey = $inDataArray['pendingsurvey']; }
		if( isset($inDataArray['answeredsurveys']) ) 	{ $this->AnsweredSurveys = $inDataArray['answeredsurveys']; }
	}


	//--GET FUNCTIONS--
	public function GetID() { return $this->ID; }
	public function GetStatus() { return $this->Status; }
	public function GetNotes() { return $this->Notes; }

	public function GetFirstName() { return $this->FirstName; }
	public function GetLastName() { return $this->LastName; }
	public function GetRace() { return $this->Race; }
	public function GetRaceCode() { return $this->RaceCode; }
	public function GetClass() { return $this->Class; }
	public function GetClassCode() { return $this->ClassCode; }
	public function GetArchetype() { return $this->Archetype; }
	public function GetArchetypeCode() { return $this->ArchetypeCode; }
	public function GetLevel() { return $this->Level; }
	public function GetReligion() { return $this->Religion; }
	public function GetReligionCode() { return $this->ReligionCode; }

	public function GetOrigin() { return $this->Origin; }
	public function GetBackground() { return $this->Background; }
	public function GetQuickNote() { return $this->QuickNote; }

	public function GetExperience() { return $this->XP; }
	public function GetTransferedExperience() { return $this->TransferedXP; }
	public function GetQuestCredits( $inCode ='UNIV') { if( isset($this->QuestCredits[$inCode]) ) { return $this->QuestCredits[$inCode]; } else { return 0; } }
	public function GetLife() { return $this->Life; }

	public function GetSkills() { return $this->Skills; }
	public function GetTalents() { return $this->Talents; }
	public function GetTeachings() { return $this->Teachings; }
	public function GetTitles() { return $this->Titles; }
	public function GetQuests() { return $this->Quests; }
	public function GetResumes() { return $this->Resumes; }
	public function GetLetters() { return $this->Letters; }
	public function GetApprovals() { return $this->Approvals; }

	public function GetUserRecentActivities() { return $this->UserRecentActivities; }
	public function GetCharacterAttendances() { return $this->CharacterAttendances; }

	public function GetUserID() { return $this->UserID; }
	public function GetUserAccount() { return $this->UserAccount; }
	public function GetUserName() { return $this->UserName; }

	public function GetGroup() { return $this->Group; }

	public function GetPendingSurvey() { return $this->PendingSurvey; }
	public function GetAnsweredSurveys() { return $this->AnsweredSurveys; }
	

	//--SET FUNCTIONS--
	public function SetID($inID) { $this->ID = $inID; }
	public function SetStatus($inStatus) { $this->Status = $inStatus; }
	public function SetNotes($inNotes) { $this->Notes = $inNotes; }

	public function SetFirstName($inFirstName) { $this->FirstName = $inFirstName; }
	public function SetLastName($inLastName) { $this->LastName = $inLastName; }
	public function SetRace($inRace) { $this->Race = $inRace; }
	public function SetRaceCode($inCode) { $this->RaceCode = $inCode; }
	public function SetClass($inClass) { $this->Class = $inClass; }
	public function SetClassCode($inCode) { $this->ClassCode = $inCode; }
	public function SetArchetype($inArchetype) { $this->Archetype = $inArchetype; }
	public function SetArchetypeCode($inCode) { $this->ArchetypeCode = $inCode; }
	public function SetLevel($inLevel) { $this->Level = $inLevel; }
	public function SetReligion($inReligion) { $this->Religion = $inReligion; }
	public function SetReligionCode($inCode) { $this->ReligionCode = $inCode; }

	public function SetOrigin($inOrigin) { $this->Origin = $inOrigin; }
	public function SetBackground($inBackground) { $this->Background = $inBackground; }
	public function SetQuickNote($inNote) { $this->QuickNote = $inNote; }

	public function SetExperience($inXP) { $this->XP = $inXP; }
	public function SetTransferedExperience($inXP) { $this->TransferedXP = $inXP; }
	public function SetQuestCredits($inCode, $inNumber) { $this->QuestCredits[$inCode] = $inNumber; }
	public function SetLife($inLife) { $this->Life = $inLife; }

	public function SetSkills($inSkills) { $this->Skills = $inSkills; }
	public function SetTalents($inTalents) { $this->Talents = $inTalents; }
	public function SetTeachings($inTeachings) { $this->Teachings = $inTeachings; }
	public function SetTitles($inTitles) { $this->Titles = $inTitles; }
	public function SetQuests($inQuests) { $this->Quests = $inQuests; }
	public function SetResumes($inList) { $this->Resumes = $inList; }
	public function SetLetters($inList) { $this->Letters = $inList; }
	public function SetApprovals($inApprovals) { $this->Approvals = $inApprovals; }

	public function SetUserRecentActivities($inActivities) { $this->UserRecentActivities = $inActivities; }
	public function SetCharacterAttendances($inActivities) { $this->CharacterAttendances = $inActivities; }

	public function SetUserID($inUserID) { $this->UserID = $inUserID; }
	public function SetUserAccount($inAccounName) { $this->UserAccount = $inAccounName; }
	public function SetUserName($inUserName) { $this->UserName = $inUserName; }

	public function SetGroup($inGroup) { $this->Group = $inGroup; }

	public function SetPendingSurvey($inSurvey) { $this->PendingSurvey = $inSurvey; }
	public function SetAnsweredSurveys($inList) { $this->AnsweredSurveys = $inList; }


	//--GET LIST COUNTS--
	public function GetTotalExperience() { 	if( isset($this->XP) ) { return array_sum( array_column($this->XP, 'xp') ); }
						return 0; }
	public function GetGainedExperience() { 
		if( !isset($this->XP) ) { return 0; }
		$lGainedXP = 0; 
		foreach ($this->XP as $xp) {
			if( $xp['xp'] > 0 ) { $lGainedXP += $xp['xp']; }
		}
		return $lGainedXP; 
	}
	public function GetTotalLife() { 	if( isset($this->Life) ) { return array_sum( array_column($this->Life, 'life') ); } 
						return 0; }
	public function GetTotalMana() {  
		$lMana = 0; 
		foreach ($this->Skills as $skill) {
			    if( substr($skill['code'], 0, 4) == 'MAGI' && $skill['status'] != 'INACT') { $lMana += $skill['quantity']; }
			elseif( substr($skill['code'], 0, 6) == 'ELMMAG' && $skill['status'] != 'INACT') { $lMana += $skill['quantity']; }
		}
		foreach ($this->Talents as $talent) {
			    if( $talent['code'] == 'RESERVE' && $talent['status'] != 'INACT') { $lMana += 15; }
			elseif( $talent['code'] == 'PROPHET' && $talent['status'] != 'INACT') { $lMana += 5; }
			elseif( $talent['code'] == 'ARCHIM'  && $talent['status'] != 'INACT') { $lMana += 5; }
		}
		return $lMana; 
	}

	public function GetOpenedQuestApplicationCount() {
		$lQuestList = array();
		foreach ($this->Quests as $quest) {
			if( in_array($quest->GetStatus(), array('DEM', 'REPR', 'ACTIF', 'SUITE')) ) { $lQuestList[] = $quest; }
		}
		return count($lQuestList); 
	}

	public function GetSkillCount() { return count($this->Skills); }
	public function GetTalentCount() { return count($this->Talents); }
	public function GetTeachingCount() { return count($this->Teachings); }
	public function GetTitleCount() { return count($this->Titles); }
	public function GetQuestCount() { return count($this->Quests); }
	public function GetResumeCount() { return count($this->Resumes); }
	public function GetLetterCount() { return count($this->Letters); }
	public function GetGroupCount() { return count($this->Groups); }
	public function GetSurveyCount() { return count($this->AnsweredSurveys); }


	//--OTHERS--
	public function GetFullName() { $lFullName = $this->FirstName . ' ' . $this->LastName; return $lFullName; }

	public function GetMaxLife() { 
		$lMaxLife = 0; 
		if( isset($this->Life) ) {
			foreach($this->Life as $life) { if( 	$life['reason'] == 'PV de départ' 
								|| $life['reason'] == 'Ajustement racial' 
								|| $life['reason'] == 'Achat de PV' ) {$lMaxLife += $life['life'];} }
		}
		return $lMaxLife; 
	}

	public function AddSkill($inSkill) { $this->Skills[] = $inSkill; }

	public function GetMergedSkills() { 
		if( !isset($this->Skills) ) { return False; }

		$lMergedSkills = array();
		foreach ($this->GetSkills() as $skill) {
			if( $skill['precisable'] ) { 		// Don't merge precisable skills, like spells
				$lMergedSkills[] = $skill;
			}
			else {
				$lMerged = False;
				foreach ($lMergedSkills as $i => $merged) {
					if( $skill['code'] == $merged['code'] ) {
						$lMergedSkills[$i]['quantity'] += $skill['quantity']; 
						if( $skill['status'] == 'LEVEL' ) { $lMergedSkills[$i]['status'] = 'LEVEL'; }	// Keep 'leveled' status
						$lMerged = True; 
					}
				}
				if( !$lMerged ) { $lMergedSkills[] = $skill; }
			}
 		}

		$skillcode = array_column($lMergedSkills, 'code');
		array_multisort($skillcode, SORT_ASC, $lMergedSkills);

		return $lMergedSkills;
	}	

	public function GetSkillByID( $inID ) {
		if( !isset($this->Skills) || !$this->GetSkillCount() ) { return False; }

		foreach ($this->Skills as $i => $skill) {
		 	if( $skill['id'] == $inID ) { 
		 		return $skill; 
		 	}
		}
		return False;
	}

	public function HasSkill( $inSkillCode ) {
		if( !isset($this->Skills) || !$this->GetSkillCount() ) { return False; }

		foreach ($this->Skills as $i => $skill) {
		 	if( $skill['code'] == $inSkillCode ) { return True; }
		}
		return False;
	}

	public function GetLetterByID( $inID ) {
		if( !isset($this->Letters) || !$this->GetLetterCount() ) { return False; }

		foreach ($this->Letters as $letter) {
		 	if( $letter->GetID() == $inID ) { 
		 		return $letter; 
		 	}
		}
		return False;
	}

	public function GetLetterByIndexes( $inIndex, $inSubIndex =0 ) {
		if( !isset($this->Letters) || !$this->GetLetterCount() ) { return False; }

		foreach ($this->Letters as $letter) {
		 	if( $letter->GetIndex() == $inIndex && $letter->GetSubIndex() == $inSubIndex ) { 
		 		return $letter; 
		 	}
		}
		return False;
	}

	public function GetQuestCreditsByType( $inType ) {
		if( !isset($this->QuestCredits[$inType]) ) { return False; }

		return $this->QuestCredits[$inType]; 
	}

	public function CheckRaceApproval( $inRaceName ) {
		foreach ($this->Approvals as $approval) {
		 	if( $approval->GetSubject() == $inRaceName && $approval->GetStatus() == 'ACCEP' ) { return True; }
		}
		return False;
	}

	public function HasAttendance( $inActivityID ) {
		foreach ($this->CharacterAttendances as $attendance) {
		 	if( $attendance->GetID() == $inActivityID ) { return True; }
		}
		return False;
	}


	//--PRINT OBJECT FOR DEBUG PURPOSES--
	public function PrintHTML()
	{
		echo '<DIV class="debug">';
		echo '<b><u>Character</u></b><br />';
		echo 'ID: ' . $this->ID . '<br />';
		echo 'Status: ' . $this->Status . '<br />';
		echo '-------<br />';
		echo 'Name: ' . $this->GetFullName() . '<br />';
		echo 'Race: ' . $this->Race . '<br />';
		echo 'Class: ' . $this->Class . '<br />';
		echo 'Level: ' . $this->Level . '<br />';
		echo 'Religion: ' . $this->Religion . '<br />';
		echo '-------<br />';
		echo 'Origin: ' . $this->GetFullName() . '<br />';
		echo 'BG: ' . $this->Background . '<br />';
		echo 'Quick Note: ' . $this->QuickNote . '<br />';
		echo '-------<br />';
		echo 'XP mods: ' . count($this->XP['id']) . '<br />';
		echo 'Life mods: ' . count($this->Life['id']) . '<br />';
		echo '-------<br />';
		echo 'Skills: ' . count($this->Skills) . '<br />';
		echo 'Talents: ' . count($this->Talents) . '<br />';
		echo 'Titles: ' . count($this->Titles) . '<br />';
		echo 'Quests: ' . count($this->Quests) . '<br />';
		echo '-------<br />';
		echo 'User ID: ' . $this->UserID . '<br />';
		echo 'User Name: ' . $this->UserName . '<br />';
		echo 'Group ID: ' . $this->Group->GetID() . '<br />';
		echo 'Group Name: ' . $this->Group->GetName() . '<br />';
		echo '</DIV>';
	}


} // END of Character class

?>
