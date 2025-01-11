<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Player Model v1.2 r5 ==				║
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
protected $ManagedGroups;

protected $Activities;

protected $Warnings;
protected $Blames;

protected $Passes;
protected $Debts;
protected $FreeActivityVouchers;
protected $FreeKidVouchers;


	//--CONSTRUCTOR--
	public function __construct( $inDataArray =array() )
	{
		parent::__construct( $inDataArray );

		if( isset($inDataArray['notes']) ) 			{ $this->Notes = $inDataArray['notes']; }

		if( isset($inDataArray['xp']) ) 			{ $this->UserXP = $inDataArray['xp']; }
		if( isset($inDataArray['characters']) ) 		{ $this->Characters = $inDataArray['characters']; }
		if( isset($inDataArray['managedgroups']) ) 		{ $this->ManagedGroupsID = $inDataArray['managedgroups']; }

		if( isset($inDataArray['activities']) ) 		{ $this->Activities = $inDataArray['activities']; }

		if( isset($inDataArray['warnings']) ) 			{ $this->Warnings = $inDataArray['warnings']; }
		if( isset($inDataArray['blames']) ) 			{ $this->Blames = $inDataArray['blames']; }

		if( isset($inDataArray['passes']) ) 			{ $this->Passes = $inDataArray['passes']; }
		if( isset($inDataArray['debts']) ) 			{ $this->Debts = $inDataArray['debts']; }
		if( isset($inUserDataArray['freeactivityvouchers']) )	{ $this->FreeActivityVouchers = $inUserDataArray['freeactivityvouchers']; }
		if( isset($inUserDataArray['freekidvouchers']) )	{ $this->FreeKidVouchers = $inUserDataArray['freekidvouchers']; }
	}


	//--GET FUNCTIONS--
	public function GetNotes() { return $this->Notes; }

	public function GetExperience() { return $this->UserXP; }
	public function GetCharacters() { return $this->Characters; }
	public function GetManagedGroups() { return $this->ManagedGroups; }

	public function GetActivities() { return $this->Activities; }

	public function GetWarnings() { return $this->Warnings; }
	public function GetBlames() { return $this->Blames; }

	public function GetPasses() { return $this->Passes; }
	public function GetDebts() { return $this->Debts; }
	public function GetFreeActivityVouchers() { return $this->FreeActivityVouchers; }
	public function GetFreeKidVouchers() { return $this->FreeKidVouchers; }


	//--SET FUNCTIONS--
	public function SetNotes($inNotes) { $this->Notes = $inNotes; }

	public function SetExperience($inXP) { $this->UserXP = $inXP; }
	public function SetCharacters($inCharacters) { $this->Characters = $inCharacters; }
	public function SetManagedGroups($inList) { $this->ManagedGroups = $inList; }

	public function SetActivities($inActivities) { $this->Activities = $inActivities; }

	public function SetWarnings($inWarnings) { $this->Warnings = $inWarnings; }		
	public function SetBlames($inBlames) { $this->Blames = $inBlames; }			

	public function SetPasses($inPasses) { $this->Passes = $inPasses; }			
	public function SetDebts($inDebts) { $this->Debts = $inDebts; }			
	public function SetFreeActivityVouchers($inNumber) { $this->FreeActivityVouchers = $inNumber; }
	public function SetFreeKidVouchers($inNumber) { $this->FreeKidVouchers = $inNumber; }


	//--GET LIST COUNTS--
	public function GetTotalExperience() { return array_sum($this->UserXP['xp']); }
	public function GetCharacterCount() { return count($this->Characters); }
	public function GetManagedGroupCount() { return count($this->ManagedGroups); }

	public function GetActivityCount() { return count($this->Activities['id']); }
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

	public function GetWarningCount() { return count($this->Warnings); }
	public function GetBlameCount() { return count($this->Blames); }

	public function GetTotalDebt() { return array_sum($this->Debts['amount']); }


	//--OTHERS--
	public function AddFreeActivityVouchers($inNumber) { $this->FreeActivityVouchers = $this->FreeActivityVouchers + $inNumber; }
	public function AddFreeKidVouchers($inNumber) { $this->FreeKidVouchers = $this->FreeKidVouchers + $inNumber; }
	public function GetLastAttendance() { 

		// Find last activity with the right type
		$lLastAttendance = null; $lActivityCount = count($this->Activities['type']);
		for ($i = 0; $i < $lActivityCount; $i++) {
			if ($this->Activities['type'][$i] == "GN") {$lLastAttendance = $this->Activities['name'][$i]; break;}
		}
		
		return $lLastAttendance;
	}
	public function GetOldAttendance() { 	# Still used to verify if player is a new or returning player.

		// If the player's last attendance is before 2016-01-01...
		$lOldAttendance = null; $lActivityCount = count($this->Activities['type']);
		if( $lActivityCount == 0 ) { return $lOldAttendance; }
		if ($this->Activities['type'][0] == "OLDGN") {$lOldAttendance = $this->Activities['name'][0]; }
		
		return $lOldAttendance;
	}
	public function IsManagedGroup( $inGroupId ) {
		foreach ($this->ManagedGroups as $group) { 
			if( $group->GetID() == $inGroupId ) { return True; } 
		}
		return False;
	}
	public function GetMemberGroups() { 

		$lGroupList = array(); $lGroupValidationList = array();
		foreach ($this->Characters as $character) { 
			if( $character->GetGroup()->GetID() <> NULL && !isset($lGroupValidationList[$character->GetGroup()->GetID()]) ) { 
				$lGroupValidationList[$character->GetGroup()->GetID()] = True;
				$lGroupList[] = $character->GetGroup(); 
			} 
		}
		return $lGroupList;
	}
	

	//--PRINT OBJECT FOR DEBUG PURPOSES--
	public function PrintHTML()
	{
		echo '<DIV class="debug">';
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
		echo 'Managed Groups: ' . count($this->ManagedGroups) . '<br />';
		echo '-------<br />';
		echo 'Activities: ' . count($this->Activities['id']) . '<br />';
		echo 'Warnings: ' . count($this->Warnings) . '<br />';
		echo 'Blames: ' . count($this->Blames) . '<br />';
		echo 'Passes: ' . count($this->Passes) . '<br />';
		echo 'Debts: ' . count($this->Debts) . '<br />';
		echo '-------<br />';
		echo '</DIV>';
	}

} // END of Player class

?>
