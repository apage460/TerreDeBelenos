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
║	== User Model v1.2 r16 ==				║
║	Represents an individual, authenticated or not.		║
║	Serializable.						║
╚═══════════════════════════════════════════════════════════════╝
*/

class User
{

protected $ID;
protected $Account;
protected $AccessLevel;
protected $Status;

protected $FirstName;
protected $LastName;
protected $Gender;
protected $DateOfBirth;
protected $Mail;

protected $Tutor;


	//--CONSTRUCTOR--
	public function __construct( $inUserDataArray =array() )
	{

		if( isset($inUserDataArray['id']) ) 			{ $this->ID = $inUserDataArray['id']; }
		if( isset($inUserDataArray['account']) ) 		{ $this->Account = $inUserDataArray['account']; }
		if( isset($inUserDataArray['accesslevel']) ) 		{ $this->AccessLevel = $inUserDataArray['accesslevel']; }
		if( isset($inUserDataArray['status']) ) 		{ $this->Status = $inUserDataArray['status']; }

		if( isset($inUserDataArray['firstname']) ) 		{ $this->FirstName = $inUserDataArray['firstname']; }
		if( isset($inUserDataArray['lastname']) ) 		{ $this->LastName = $inUserDataArray['lastname']; }
		if( isset($inUserDataArray['gender']) ) 		{ $this->Gender = $inUserDataArray['gender']; }
		if( isset($inUserDataArray['dateofbirth']) ) 		{ $this->DateOfBirth = $inUserDataArray['dateofbirth']; }
		if( isset($inUserDataArray['mail']) ) 			{ $this->Mail = $inUserDataArray['mail']; }

		if( isset($inUserDataArray['tutor']) ) 			{ $this->Tutor = $inUserDataArray['tutor']; }

	}


	//--GET FUNCTIONS--
	public function GetID() { return $this->ID; }
	public function GetAccountName() { return $this->Account; }
	public function GetAccessLevel() { return $this->AccessLevel; }
	public function GetStatus() { return $this->Status; }

	public function GetFirstName() { return $this->FirstName; }
	public function GetLastName() { return $this->LastName; }
	public function GetGender() { return $this->Gender; }
	public function GetBirthDate() { return $this->DateOfBirth; }
	public function GetMailAddress() { return $this->Mail; }

	public function GetTutor() { return $this->Tutor; }


	//--SET FUNCTIONS--
	public function SetID($inID) { $this->ID = $inID; }
	public function SetAccountName($inAccount) { $this->Account = $inAccount; }
	public function SetStatus($inStatus) { $this->Status = $inStatus; }

	public function SetFirstName($inFirstName) { $this->FirstName = $inFirstName; }
	public function SetLastName($inLastName) { $this->LastName = $inLastName; }
	public function SetGender($inGender) { $this->Gender = $inGender; }
	public function SetBirthDate($inDateOfBirth) { $this->DateOfBirth = $inDateOfBirth; }
	public function SetMailAddress($inMail) { $this->Mail = $inMail; }

	public function SetTutor($inTutor) { $this->Tutor = $inTutor; }


	//--PRIVILEGES CHECKS--
	public function IsAdmin() { return ($this->AccessLevel >= ADMIN_LEVEL); }
	public function IsReferee() { return ($this->AccessLevel >= REFEREE_LEVEL); }
	public function IsScriptor() { return ($this->AccessLevel >= SCRIPTOR_LEVEL); }
	public function IsManager() { return ($this->AccessLevel >= MANAGER_LEVEL); }


	//--OTHERS--
	public function GetFullName() { $lFullName = $this->FirstName . ' ' . $this->LastName; return $lFullName; }
	public function GetAge() { $lBirth = new DateTime($this->DateOfBirth); $lNow = new DateTime(); $lAge = $lNow->diff($lBirth); return $lAge->y; }
	public function IsUntutoredKid() { if($this->GetAge() < 16 && $this->Tutor <> 'GroupeCadre') {return True;} return False; }


	//--DATA TRANSFERT--
	public function ToArray() {
		return [
			'id'		=>	$this->ID,
			'account'	=>	$this->Account,
			'accesslevel' 	=>	$this->AccessLevel,
			'status' 	=>	$this->Status,
			'firstname' 	=>	$this->FirstName,
			'lastname' 	=>	$this->LastName,
			'gender' 	=>	$this->Gender,
			'dateofbirth' 	=>	$this->DateOfBirth,
			'mail'		=>	$this->Mail,
			'tutor'	=>	$this->Tutor
		];
		
	}	

	//--PRINT OBJECT FOR DEBUG PURPOSES--
	public function PrintHTML()
	{
		echo '<DIV id="debug">';
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
		echo '</DIV>';
	}

} // END of User class

?>
