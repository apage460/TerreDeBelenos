<?php
define('DBA_LEVEL', 7);
define('ADMIN_LEVEL', 6);
define('MANAGER_LEVEL', 5);
define('REFEREE_LEVEL', 4);
define('SCRIPTOR_LEVEL', 3);
define('ASSIST_LEVEL', 2);
define('PLAYER_LEVEL', 1);


/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== User Model v1.2 r24 ==				║
║	Represents an individual, authenticated or not.		║
║	Serializable.						║
╚═══════════════════════════════════════════════════════════════╝
*/

class User
{

protected $ID;
protected $Account;
protected $AccessLevel;
protected $Permissions;
protected $Status;

protected $FirstName;
protected $LastName;
protected $AltName;
protected $Gender;
protected $DateOfBirth;
protected $Mail;

protected $Tutor;
protected $ContactCode;
protected $VolunteeringPoints = array();


	//--CONSTRUCTOR--
	public function __construct( $inDataArray =array() )
	{

		if( isset($inDataArray['id']) ) 			{ $this->ID = $inDataArray['id']; }
		if( isset($inDataArray['account']) ) 			{ $this->Account = $inDataArray['account']; }
		if( isset($inDataArray['accesslevel']) ) 		{ $this->AccessLevel = $inDataArray['accesslevel']; }
		if( isset($inDataArray['permissions']) ) 		{ $this->Permissions = $inDataArray['permissions']; }
		if( isset($inDataArray['status']) ) 			{ $this->Status = $inDataArray['status']; }

		if( isset($inDataArray['firstname']) ) 			{ $this->FirstName = $inDataArray['firstname']; }
		if( isset($inDataArray['lastname']) ) 			{ $this->LastName = $inDataArray['lastname']; }
		if( isset($inDataArray['altname']) ) 			{ $this->AltName = $inDataArray['altname']; }
		if( isset($inDataArray['gender']) ) 			{ $this->Gender = $inDataArray['gender']; }
		if( isset($inDataArray['dateofbirth']) ) 		{ $this->DateOfBirth = $inDataArray['dateofbirth']; }
		if( isset($inDataArray['mail']) ) 			{ $this->Mail = $inDataArray['mail']; }

		if( isset($inDataArray['tutor']) ) 			{ $this->Tutor = $inDataArray['tutor']; }
		if( isset($inDataArray['contactcode']) )		{ $this->ContactCode = $inDataArray['contactcode']; }
		if( isset($inDataArray['volunteeringpoints']) ) 	{ $this->VolunteeringPoints = $inDataArray['volunteeringpoints']; }
	}


	//--GET FUNCTIONS--
	public function GetID() { return $this->ID; }
	public function GetAccountName() { return $this->Account; }
	public function GetAccessLevel() { return $this->AccessLevel; }
	public function GetPermissions() { return $this->Permissions; }
	public function GetStatus() { return $this->Status; }

	public function GetFirstName() { return $this->FirstName; }
	public function GetLastName() { return $this->LastName; }
	public function GetAltName() { return $this->AltName; }
	public function GetGender() { return $this->Gender; }
	public function GetBirthDate() { return $this->DateOfBirth; }
	public function GetMailAddress() { return $this->Mail; }

	public function GetTutor() { return $this->Tutor; }
	public function GetContactCode() { return $this->ContactCode; }
	public function GetVolunteeringPoints() { return $this->VolunteeringPoints; }


	//--SET FUNCTIONS--
	public function SetID($inID) { $this->ID = $inID; }
	public function SetAccountName($inAccount) { $this->Account = $inAccount; }
	public function SetAccessLevel($inLevel) { $this->AccessLevel = $inLevel; }
	public function SetPermissions($inList) { $this->Permissions = $inList; }
	public function SetStatus($inStatus) { $this->Status = $inStatus; }

	public function SetFirstName($inName) { $this->FirstName = $inName; }
	public function SetLastName($inName) { $this->LastName = $inName; }
	public function SetAltName($inName) { $this->AltName = $inName; }
	public function SetGender($inGender) { $this->Gender = $inGender; }
	public function SetBirthDate($inDate) { $this->DateOfBirth = $inDate; }
	public function SetMailAddress($inMail) { $this->Mail = $inMail; }

	public function SetTutor($inTutor) { $this->Tutor = $inTutor; }
	public function SetContactCode($inCode) { $this->ContactCode = $inCode; }
	public function SetVolunteeringPoints($inPoints) { $this->VolunteeringPoints = $inPoints; }			


	//--PRIVILEGES CHECKS--
	public function IsDBA() { return ($this->AccessLevel >= DBA_LEVEL); }
	public function IsAdmin() { return ($this->AccessLevel >= ADMIN_LEVEL); }
	public function IsManager() { return ($this->AccessLevel >= MANAGER_LEVEL); }		// This function must change name throughout the code
	public function IsReferee() { return ($this->AccessLevel >= REFEREE_LEVEL); }
	public function IsScriptor() { return ($this->AccessLevel >= SCRIPTOR_LEVEL); }
	public function IsAssist() { return ($this->AccessLevel >= ASSIST_LEVEL); }		// This function must change name throughout the code

	public function HasAccess($inPermission) { return array_search($inPermission, $this->Permissions) || $this->IsDBA(); }		


	//--OTHERS--
	public function GetFullName() { $lFullName = $this->FirstName . ' ' . $this->LastName; return $lFullName; }
	public function GetAge() { $lBirth = new DateTime($this->DateOfBirth); $lNow = new DateTime(); $lAge = $lNow->diff($lBirth); return $lAge->y; }
	public function IsContactCode($inCode) { if($this->ContactCode == $inCode) {return True;} return False; }
	public function GetVolunteeringPointCount() { $total = 0; foreach($this->VolunteeringPoints as $points) { $total += $points['points']; } return $total; }


	//--DATA TRANSFERT--
	public function ToArray() {
		return [
			'id'			=>	$this->ID,
			'account'		=>	$this->Account,
			'accesslevel' 		=>	$this->AccessLevel,
			'status' 		=>	$this->Status,
			'firstname' 		=>	$this->FirstName,
			'lastname' 		=>	$this->LastName,
			'altname' 		=>	$this->AltName,
			'gender' 		=>	$this->Gender,
			'dateofbirth' 		=>	$this->DateOfBirth,
			'mail'			=>	$this->Mail,
			'tutor'			=>	$this->Tutor,
			'contactcode'		=>	$this->ContactCode,
			'permissions'		=>	$this->Permissions,
			'volunteeringpoints'	=>	$this->VolunteeringPoints
		];
		
	}	

	//--PRINT OBJECT FOR DEBUG PURPOSES--
	public function PrintHTML()
	{
		echo '<DIV class="debug">';
		echo '<b><u>User</u></b><br />';
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
		echo 'Tutor: ' . $this->Tutor . '<br />';
		echo 'Free Vouchers: ' . $this->FreeActivityVouchers . '<br />';
		echo '</DIV>';
	}

} // END of User class

?>
