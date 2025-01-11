<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Player Model v1.2 r2 ==				║
║	Represents a player individual.				║
║	Serializable.						║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('models/user.class.php');


class Player extends User
{

protected $Notes;

protected $UserXP;
protected $Characters;
protected $ManagedGroupID;

protected $Activities;
protected $Warnings;
protected $Blames;
protected $Passes;
protected $Debts;
protected $Patronage;


	//--CONSTRUCTOR--
	public function __construct( $inPlayerDataArray =array() )
	{
		parent::__construct( $inPlayerDataArray );

		if( isset($inPlayerDataArray['notes']) ) 		{ $this->Notes = $inPlayerDataArray['notes']; }

		if( isset($inPlayerDataArray['xp']) ) 			{ $this->UserXP = $inPlayerDataArray['xp']; }
		if( isset($inPlayerDataArray['characters']) ) 		{ $this->Characters = $inPlayerDataArray['characters']; }
		if( isset($inPlayerDataArray['managedgroupid']) ) 	{ $this->ManagedGroupID = $inPlayerDataArray['managedgroupid']; }

		if( isset($inPlayerDataArray['activities']) ) 		{ $this->Activities = $inPlayerDataArray['activities']; }
		if( isset($inPlayerDataArray['warnings']) ) 		{ $this->Warnings = $inPlayerDataArray['warnings']; }
		if( isset($inPlayerDataArray['blames']) ) 		{ $this->Blames = $inPlayerDataArray['blames']; }
		if( isset($inPlayerDataArray['passes']) ) 		{ $this->Passes = $inPlayerDataArray['passes']; }
		if( isset($inPlayerDataArray['debts']) ) 		{ $this->Debts = $inPlayerDataArray['debts']; }
		if( isset($inPlayerDataArray['patronage']) ) 		{ $this->Patronage = $inPlayerDataArray['patronage']; }
	}


	//--GET FUNCTIONS--
	public function GetNotes() { return $this->Notes; }

	public function GetExperience() { return $this->UserXP; }
	public function GetCharacters() { return $this->Characters; }
	public function GetManagedGroupID() { return $this->ManagedGroupID; }

	public function GetActivities() { return $this->Activities; }
	public function GetWarnings() { return $this->Warnings; }
	public function GetBlames() { return $this->Blames; }
	public function GetPasses() { return $this->Passes; }
	public function GetDebts() { return $this->Debts; }
	public function GetPatronage() { return $this->Patronage; }


	//--SET FUNCTIONS--
	public function SetNotes($inNotes) { $this->Notes = $inNotes; }

	public function SetExperience($inXP) { $this->UserXP = $inXP; }
	public function SetCharacters($inCharacters) { $this->Characters = $inCharacters; }
	public function SetManagedGroupID($inGroupID) { $this->ManagedGroupID = $inGroupID; }

	public function SetActivities($inActivities) { $this->Activities = $inActivities; }
	public function SetWarnings($inWarnings) { $this->Warnings = $inWarnings; }		
	public function SetBlames($inBlames) { $this->Blames = $inBlames; }			
	public function SetPasses($inPasses) { $this->Passes = $inPasses; }			
	public function SetDebts($inDebts) { $this->Debts = $inDebts; }			
	public function SetPatronage($inPatronage) { $this->Patronage = $inPatronage; }			


	//--GET LIST COUNTS--
	public function GetTotalExperience() { return array_sum($this->UserXP['xp']); }
	public function GetCharacterCount() { return count($this->Characters); }

	public function GetActivityCount() { return count($this->Activities['id']); }
	public function GetWarningCount() { return count($this->Warnings); }
	public function GetBlameCount() { return count($this->Blames); }
	public function GetTotalDebt() { return array_sum($this->Debts['amount']); }
	public function GetTotalPatronage() { return array_sum($this->Patronage['amount']); }

	public function GetMainActivityCount() { 
		$lValueCounts = array_count_values($this->Activities['type']); 

		if( isset($lValueCounts['GN']) ) { return $lValueCounts['GN']; } 
		return 0; 
	}
	public function GetOtherActivityCount() { 
		$lValueCounts = array_count_values($this->Activities['type']); 

		if( isset($lValueCounts['GN']) ) { return count($this->Activities['type']) - $lValueCounts['GN']; }
		return count($this->Activities['type']); 
	}


	//--OTHERS--
	public function GetLastAttendance() { 

		// Find last activity with the right type
		$lLastAttendance = null; $lActivityCount = count($this->Activities['type']);
		for ($i = 0; $i < $lActivityCount; $i++) {
			if ($this->Activities['type'][$i] == "GN") {$lLastAttendance = $this->Activities['name'][$i]; break;}
		}
		
		return $lLastAttendance;
	}
	public function GetOldAttendance() { 

		// If the player's last attendance is before 2016-01-01, returns it
		$lOldAttendance = null; $lActivityCount = count($this->Activities['type']);
		if( $lActivityCount == 0 ) { return $lOldAttendance; }
		if ($this->Activities['type'][0] == "OLDGN") {$lOldAttendance = $this->Activities['name'][0]; }
		
		return $lOldAttendance;
	}
	

	//--PRINT OBJECT FOR DEBUG PURPOSES--
	public function PrintHTML()
	{
		echo '<DIV id="debug">';
		echo '<b><u>Player</u></b><br />';
		echo 'ID: ' . $this->ID . '<br />';
		echo 'Account: ' . $this->Account . '<br />';
		echo 'Access Level: ' . $this->AccessLevel . '<br />';
		echo 'Status: ' . $this->Status . '<br />';
		echo '-------<br />';
		echo 'Name: ' . $this->FirstName . ' ' . $this->LastName . '<br />';
		echo 'Date of birth: ' . $this->DateOfBirth . '<br />';
		echo 'Gender: ' . $this->Gender . '<br />';
		echo 'Mail address: ' . $this->Mail . '<br />';
		echo '-------<br />';
		echo 'XP: ' . count($this->UserXP['id']) . '<br />';
		echo 'Characters: ' . count($this->Characters) . '<br />';
		echo 'Managed Group ID: ' . $this->ManagedGroupID . '<br />';
		echo '-------<br />';
		echo 'Activities: ' . count($this->Activities['id']) . '<br />';
		echo 'Warnings: ' . count($this->Warnings) . '<br />';
		echo 'Blames: ' . count($this->Blames) . '<br />';
		echo 'Passes: ' . count($this->Passes) . '<br />';
		echo 'Debts: ' . count($this->Debts) . '<br />';
		echo 'Philanthropy: ' . count($this->PhilanthropyActs) . '<br />';
		echo '-------<br />';
		echo '</DIV>';
	}

} // END of User class

?>
