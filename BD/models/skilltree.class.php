<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Skill Tree Model v1.2 r4 ==				║
║	Represents a skill tree and/or a talent tree.		║
║	Serializable.						║
╚═══════════════════════════════════════════════════════════════╝
*/


class SkillTree
{

protected $SkillTree;
protected $TalentTree;

protected $Teachings;
protected $ObtainedSkills;
protected $ObtainedTalents;
protected $ObtainedCredits;

protected $Spells;
protected $Recipes;


	//--CONSTRUCTOR--
	public function __construct( $inTreeDataArray =array() )
	{

		if( isset($inTreeDataArray['skilltree']) ) 		{ $this->SkillTree = $inTreeDataArray['skilltree']; }
		if( isset($inTreeDataArray['talenttree']) ) 		{ $this->TalentTree = $inTreeDataArray['talenttree']; }

		if( isset($inTreeDataArray['teachings']) ) 		{ $this->Teachings = $inTreeDataArray['teachings']; }
		if( isset($inTreeDataArray['obtainedskills']) )		{ $this->ObtainedSkills = $inTreeDataArray['obtainedskills']; }
		if( isset($inTreeDataArray['obtainedtalents']) )	{ $this->ObtainedTalents = $inTreeDataArray['obtainedtalents']; }
		if( isset($inTreeDataArray['obtainedcredits']) )	{ $this->ObtainedCredits = $inTreeDataArray['obtainedcredits']; }

		if( isset($inTreeDataArray['spells']) ) 		{ $this->Spells = $inTreeDataArray['spells']; }
		if( isset($inTreeDataArray['recipes']) ) 		{ $this->Recipes = $inTreeDataArray['recipes']; }
	}


	//--GET FUNCTIONS--
	public function GetSkillTree() { return $this->SkillTree; }
	public function GetTalentTree() { return $this->TalentTree; }

	public function GetTeachings() { return $this->Teachings; }
	public function GetObtainedSkills() { return $this->ObtainedSkills; }
	public function GetObtainedTalents() { return $this->ObtainedTalents; }
	public function GetObtainedCredits() { return $this->ObtainedCredits; }

	public function GetSpells() { return $this->Spells; }
	public function GetRecipes() { return $this->Recipes; }


	//--SET FUNCTIONS--
	public function SetSkillTree($inTree) { $this->SkillTree = $inTree; }
	public function SetTalentTree($inTree) { $this->TalentTree = $inTree; }

	public function SetTeachings($inList) { $this->Teachings = $inList; }
	public function SetObtainedSkills($inList) { $this->ObtainedSkills = $inList; }
	public function SetObtainedTalents($inList) { $this->ObtainedTalents = $inList; }
	public function SetObtainedCredits($inList) { $this->ObtainedCredits = $inList; }

	public function SetSpells($inList) { $this->Spells = $inList; }
	public function SetRecipes($inList) { $this->Recipes = $inList; }


	//--OTHERS--
	public function GetSkillIndex( $inSkillCode ) 
	{ 
		if( !isset( $this->SkillTree ) ) { return False; }

		foreach($this->SkillTree as $i => $skill) {
			if( $skill['code'] == $inSkillCode ) { return $i; }
		}
		return False;
	}
	public function GetTalentIndex( $inTalentCode ) 
	{ 
		if( !isset( $this->TalentTree ) ) { return False; }

		foreach($this->TalentTree as $i => $talent) {
			if( $talent['code'] == $inTalentCode ) { return $i; }
		}
		return False;
	}
	public function GetTalentNameByCode( $inTalentCode ) 
	{ 
		if( !isset( $this->TalentTree ) ) { return False; }

		foreach($this->TalentTree as $talent) {
			if( $talent['code'] == $inTalentCode ) { return $talent['name']; }
		}
		return False;
	}

	public function GetCategorySkills( $inCategory ) 
	{ 
		if( !isset( $this->SkillTree ) ) { return False; }

		$lSkillList = array();
		foreach($this->SkillTree as $skill) {
			if( $skill['category'] == $inCategory && $skill['buyable'] ) { $lSkillList[] = $skill; }
		}
		return $lSkillList;
	}
	public function IsActivePrerequisite( $inSkillCode ) 
	{ 
		if( !isset( $this->ObtainedSkills ) ) { return False; }

		foreach($this->ObtainedSkills as $skill) {
			foreach ($skill['prerequisites'] as $prerequisite) {
		 		if( $prerequisite == $inSkillCode ) { return True; }	// Logic must be upgraded to take other prerequisites into account.
			}
		}
		return False;
	}

	//--PRINT OBJECT FOR DEBUG PURPOSES--
	public function PrintHTML()
	{
		echo '<DIV class="debug">';
		echo '<b><u>SkillTree</u></b><br />';
		echo '-------<br />';
		print_r($this->SkillTree);
		echo '</DIV>';
	}


} // END of SkillTree class

?>
