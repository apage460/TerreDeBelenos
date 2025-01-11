<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== New Character Model v1.2 r4 ==			║
║	Represents a character in the creation process.		║
║	Serializable.						║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('models/character.class.php');

class NewCharacter extends Character
{

protected $CreationStep;
protected $RaceList;

protected $RaceCode;
protected $PossibleClasses;

protected $ClassCode;
protected $PossibleArchetypes;
protected $PossibleReligions;

protected $ReligionCode;
protected $ArchetypeCode;
protected $Archetype;

protected $StartingSkills;
protected $StartingTalents;


	//--CONSTRUCTOR--
	public function __construct( $inDataArray =array() )
	{
		parent::__construct( $inDataArray );

		if( isset($inDataArray['step']) )			{ $this->CreationStep = $inDataArray['step']; }
		if( isset($inDataArray['racelist']) )			{ $this->CreationStep = $inDataArray['racelist']; }

		if( isset($inDataArray['racecode']) )			{ $this->RaceCode = $inDataArray['racecode']; }
		if( isset($inDataArray['classchoices']) )		{ $this->PossibleClasses = $inDataArray['classchoices']; }

		if( isset($inDataArray['classcode']) )			{ $this->ClassCode = $inDataArray['classcode']; }
		if( isset($inDataArray['archetypechoices']) ) 		{ $this->PossibleArchetypes = $inDataArray['archetypechoices']; }
		if( isset($inDataArray['religionchoices']) ) 		{ $this->PossibleReligions = $inDataArray['religionchoices']; }

		if( isset($inDataArray['religioncode']) )		{ $this->ReligionCode = $inDataArray['religioncode']; }
		if( isset($inDataArray['archetypecode']) )		{ $this->ArchetypeCode = $inDataArray['archetypecode']; }
		if( isset($inDataArray['archetype']) )			{ $this->Archetype = $inDataArray['archetype']; }

		if( isset($inDataArray['startingskills']) ) 		{ $this->StartingSkills = $inDataArray['startingskills']; }
		if( isset($inDataArray['startingtalents']) ) 		{ $this->StartingTalents = $inDataArray['startingtalents']; }
	}


	//--GET FUNCTIONS--
	public function GetCreationStep() { return $this->CreationStep; }
	public function GetRaceList() { return $this->RaceList; }

	public function GetRaceCode() { return $this->RaceCode; }
	public function GetPossibleClasses() { return $this->PossibleClasses; }

	public function GetClassCode() { return $this->ClassCode; }
	public function GetPossibleArchetypes() { return $this->PossibleArchetypes; }
	public function GetPossibleReligions() { return $this->PossibleReligions; }

	public function GetReligionCode() { return $this->ReligionCode; }
	public function GetArchetypeCode() { return $this->ArchetypeCode; }
	public function GetArchetype() { return $this->Archetype; }

	public function GetStartingSkills() { return $this->StartingSkills; }
	public function GetStartingTalents() { return $this->StartingTalents; }


	//--SET FUNCTIONS--
	public function SetCreationStep($inStep) { $this->CreationStep = $inStep; }
	public function SetRaceList($inList) { $this->RaceList = $inList; }

	public function SetRaceCode($inCode) { $this->RaceCode = $inCode; }
	public function SetPossibleClasses($inList) { $this->PossibleClasses = $inList; }

	public function SetClassCode($inCode) { $this->ClassCode = $inCode; }
	public function SetPossibleArchetypes($inList) { $this->PossibleArchetypes = $inList; }
	public function SetPossibleReligions($inList) { $this->PossibleReligions = $inList; }

	public function SetReligionCode($inCode) { $this->ReligionCode = $inCode; }
	public function SetArchetypeCode($inCode) { $this->ArchetypeCode = $inCode; }
	public function SetArchetype($inName) { $this->Archetype = $inName; }

	public function SetStartingSkills($inList) { $this->StartingSkills = $inList; }
	public function SetStartingTalents($inList) { $this->StartingTalents = $inList; }


	//--GET LIST COUNTS--


	//--OTHERS--
	public function GetMergedSkills() {
		$lMergedSkills = array();
		foreach ($this->GetSkills() as $skill) {
			$lMerged = False;
			foreach ($lMergedSkills as $i => $merged) {
				if( $skill['code'] == $merged['code'] && $skill['quantity'] ) {$lMergedSkills[$i]['quantity'] += $skill['quantity']; $lMerged = True; }
			}
			if( !$lMerged ) { $lMergedSkills[] = $skill; }
		}

		$skillname = array_column($lMergedSkills, 'name');
		array_multisort($skillname, SORT_ASC, $lMergedSkills);

		return $lMergedSkills;
	}

	//--PRINT OBJECT FOR DEBUG PURPOSES--
	public function PrintHTML()
	{
		echo '<DIV class="debug">';
		echo '<b><u>NewCharacter</u></b><br />';
		echo 'ID: ' . $this->ID . '<br />';
		echo 'Status: ' . $this->Status . '<br />';
		echo 'User ID: ' . $this->UserID . '<br />';
		echo '-------<br />';
		echo 'Creation step: ' . $this->CreationStep . '<br />';
		echo 'Race choices: ' . count($this->RaceList) . '<br />';
		echo '-------<br />';
		echo 'Name: ' . $this->GetFullName() . '<br />';
		echo 'Race: ' . $this->RaceCode . '<br />';
		echo 'Class choices: ' . count($this->PossibleClasses) . '<br />';
		echo '-------<br />';
		echo 'Class: ' . $this->ClassCode . '<br />';
		echo '-------<br />';
		echo 'Skill choices: ' . count($this->PossibleArchetypes) . '<br />';
		echo 'Religion choices: ' . count($this->PossibleReligions) . '<br />';
		echo 'Origin: ' . $this->GetFullName() . '<br />';
		echo '-------<br />';
		echo 'Skillset: ' . $this->Archetype . '<br />';
		echo 'Religion: ' . $this->Religion . '<br />';
		echo 'Life mods: ' . count($this->Life['id']) . '<br />';
		echo 'Skills: ' . count($this->Skills['id']) . '<br />';
		echo 'Talents: ' . count($this->Talents['id']) . '<br />';
		echo '</DIV>';
	}


} // END of NewCharacter class

?>
