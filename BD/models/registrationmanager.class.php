<?php
define('MAX_CHILDREN', 2);
define('NEWS_UPLOAD_DIR', 'uploads/Feuillet/');
define('NEWSPAPER_MAIL', 'gaelletourneur2@hotmail.com');

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Registration Manager Model v1.2 r8 ==		║
║	Represents a registration process manager.		║
║	Serializable.						║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('models/activity.class.php');
include_once('models/character.class.php');
include_once('models/pass.class.php');

class RegistrationManager
{

protected $Activities;
protected $Passes;

protected $UserRegistrations;	// array
protected $UserAttendances;	// array
protected $UserCharacters;

protected $NewPlayer;		// bool
protected $KidStatus;		// bool

protected $FreeActivityVouchers;
protected $FreeKidVouchers;

	//--CONSTRUCTOR--
	public function __construct( $inDataArray =array() )
	{

		if( isset($inDataArray['activities']) )			{ $this->Activities = $inDataArray['activities']; }
		if( isset($inDataArray['passes']) ) 			{ $this->Passes = $inDataArray['passes']; }

		if( isset($inDataArray['userregistrations']) )		{ $this->UserRegistrations = $inDataArray['userregistrations']; }
		if( isset($inDataArray['userattendances']) )		{ $this->UserAttendances = $inDataArray['userattendances']; }
		if( isset($inDataArray['usercharacters']) )		{ $this->UserCharacters = $inDataArray['usercharacters']; }

		if( isset($inDataArray['newplayer']) )			{ $this->NewPlayer = $inDataArray['newplayer']; }
		if( isset($inDataArray['kid']) )			{ $this->KidStatus = $inDataArray['kid']; }

		if( isset($inDataArray['freeactivityvouchers']) )	{ $this->FreeActivityVouchers = $inDataArray['freeactivityvouchers']; }
		if( isset($inDataArray['freekidyvouchers']) )		{ $this->FreeKidVouchers = $inDataArray['freekidyvouchers']; }
	}


	//--GET FUNCTIONS--
	public function GetActivities() { return $this->Activities; }
	public function GetPasses() { return $this->Passes; }

	public function GetRegistrations() { return $this->UserRegistrations; }
	public function GetAttendances() { return $this->UserAttendances; }
	public function GetCharacters() { return $this->UserCharacters; }

	public function IsNewPlayer() { return $this->NewPlayer; }
	public function IsKid() { return $this->KidStatus; }

	public function GetFreeActivityVouchers() { if(!USER_VOUCHERS_ENABLED) { return 0; } return $this->FreeActivityVouchers; }
	public function GetFreeKidVouchers() { if(!KID_VOUCHERS_ENABLED) { return 0; } return $this->FreeKidVouchers; }


	//--SET FUNCTIONS--
	public function SetActivities($inList) { $this->Activities = $inList; }
	public function SetPasses($inList) { $this->Passes = $inList; }

	public function SetRegistrations($inList) { $this->UserRegistrations = $inList; }
	public function SetAttendances($inList) { $this->UserAttendances = $inList; }
	public function SetCharacters($inList) { $this->UserCharacters = $inList; }

	public function SetNewPlayer($inBool) { $this->NewPlayer = $inBool; }
	public function SetKidStatus($inBool) { $this->KidStatus = $inBool; }

	public function SetFreeActivityVouchers($inNumber) { $this->FreeActivityVouchers = $inNumber; }
	public function SetFreeKidVouchers($inNumber) { $this->FreeKidVouchers = $inNumber; }


	//--SPECIAL GETS--
	public function GetNextActivity() { 
		if( !isset($this->Activities) ) { return False; }
		foreach( $this->Activities as $activity) {
			$now = new DateTime();
			$end = new DateTime($activity->GetEndingDate());			// Compare to ending date so we get the current activity if it started.
			if( $now->diff($end)->format("%R") == '+' ) { return $activity; } 	// Activities are sorted in order of ascending dates. First that's not over is the correct one.
		}
		return False;
	}

	public function GetLastRegistration() { 
		if( !isset($this->UserRegistrations) || !count($this->UserRegistrations) ) { return False; }
		return $this->UserRegistrations[0];
	}

	public function GetLastAttendance() { 
		if( !isset($this->UserAttendances) || !count($this->UserAttendances) ) { return False; }
		return $this->UserAttendances[0];
	}

	public function GetCharacterByIndex($inIndex) { 
		if( !isset($this->UserCharacters) || !count($this->UserCharacters) ) { return False; }
		return $this->UserCharacters[$inIndex];
	}


	//--OTHERS--
	public function AdjustActivityPrices() {
		if( !isset($this->Activities) || !isset($this->Passes) ) { return False; }
		foreach( $this->Activities as $activity ) {
			// Adjust main activities' price for kids and new player
			if( $activity->GetType() == 'GN' ) {
				if( $this->KidStatus ) { $activity->SetRegularPrice( 30 ); $activity->SetLatePrice( 30 ); }
				elseif( $this->NewPlayer ) { $activity->SetRegularPrice( 35 ); $activity->SetLatePrice( 35 ); }
			}

			// Adjust for owed passes
			foreach( $this->Passes as $pass ) {
				if( $pass->IsAcquired() ) {
					foreach( $pass->GetFreeActivities() as $id ) {
						if( $activity->GetID() == $id ) { $activity->SetRegularPrice(0); $activity->SetLatePrice(0); }
					}
				}
			}

		}
	}


	//--PRINT OBJECT FOR DEBUG PURPOSES--
	public function PrintHTML()
	{
		echo '<DIV class="debug">';
		echo '<b><u>Registration manager</u></b><br />';
		echo 'Activities: ' . count($this->Activities) . '<br />';
		echo 'Passes: ' . count($this->Passes) . '<br />';
		echo 'User registrations: ' . count($this->UserRegistrations) . '<br />';
		echo 'User attendances: ' . count($this->UserAttendances) . '<br />';
		echo 'User characters: ' . count($this->UserCharacters) . '<br />';
		echo '-------<br />';
		echo '</DIV>';
	}


} // END of RegistrationManager class

?>
