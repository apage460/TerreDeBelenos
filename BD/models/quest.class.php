<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Quest Model v1.2 r8 ==				║
║	Represents a quest, either for a character or a group.	║
║	Serializable.						║
╚═══════════════════════════════════════════════════════════════╝
*/


class Quest
{

protected $ID;
protected $Character;
protected $Group;
protected $Applicant;		// Player asking for the quest
protected $Status;

protected $Subject;
protected $OptionCode;
protected $RewardCode;
protected $Suggestions;

protected $CountyID;
protected $CountyName;
protected $KingdomCode;
protected $KingdomName;	

protected $RequestDate;		// datetime
protected $Activity;		// Activity class
protected $Options;		// List of Applicant's options : [...]

protected $Revisor;		// User class
protected $ApprovalDate;	// datetime
protected $Scriptor;		// User class
protected $File;

protected $Comments;


	//--CONSTRUCTOR--
	public function __construct( $inDataArray =array() )
	{

		if( isset($inDataArray['id']) ) 		{ $this->ID = $inDataArray['id']; }
		if( isset($inDataArray['character']) )		{ $this->Character = $inDataArray['character']; }
		if( isset($inDataArray['group']) )		{ $this->Group = $inDataArray['group']; }
		if( isset($inDataArray['applicant']) )		{ $this->Applicant = $inDataArray['applicant']; }
		if( isset($inDataArray['status']) )		{ $this->Status = $inDataArray['status']; }

		if( isset($inDataArray['subject']) ) 		{ $this->Subject = $inDataArray['subject']; }
		if( isset($inDataArray['optioncode']) ) 	{ $this->OptionCode = $inDataArray['optioncode']; }
		if( isset($inDataArray['rewardcode']) ) 	{ $this->RewardCode = $inDataArray['rewardcode']; }
		if( isset($inDataArray['suggestions']) ) 	{ $this->Suggestions = $inDataArray['suggestions']; }

		if( isset($inDataArray['countyid']) ) 		{ $this->CountyID = $inDataArray['countyid']; }
		if( isset($inDataArray['countyname']) ) 	{ $this->CountyName = $inDataArray['countyname']; }
		if( isset($inDataArray['kingdomcode']) ) 	{ $this->KingdomCode = $inDataArray['kingdomcode']; }
		if( isset($inDataArray['kingdomname']) ) 	{ $this->KingdomName = $inDataArray['kingdomname']; }

		if( isset($inDataArray['requestdate']) )	{ $this->RequestDate = $inDataArray['requestdate']; }
		if( isset($inDataArray['activity']) ) 		{ $this->Activity = $inDataArray['activity']; }
		if( isset($inDataArray['options']) ) 		{ $this->Options = $inDataArray['options']; }

		if( isset($inDataArray['revisor']) ) 		{ $this->Revisor = $inDataArray['revisor']; }
		if( isset($inDataArray['approvaldate']) ) 	{ $this->ApprovalDate = $inDataArray['approvaldate']; }
		if( isset($inDataArray['scriptor']) ) 		{ $this->Scriptor = $inDataArray['scriptor']; }
		if( isset($inDataArray['file']) ) 		{ $this->File = $inDataArray['file']; }

		if( isset($inDataArray['comments']) ) 		{ $this->Comments = $inDataArray['comments']; }
	}


	//--GET FUNCTIONS--
	public function GetID() { return $this->ID; }
	public function GetCharacter() { return $this->Character; }
	public function GetGroup() { return $this->Group; }
	public function GetApplicant() { return $this->Applicant; }
	public function GetStatus() { return $this->Status; }

	public function GetSubject() { return $this->Subject; }
	public function GetOptionCode() { return $this->OptionCode; }
	public function GetRewardCode() { return $this->RewardCode; }
	public function GetSuggestions() { return $this->Suggestions; }

	public function GetCountyID() { return $this->CountyID; }
	public function GetCountyName() { return $this->CountyName; }
	public function GetKingdomCode() { return $this->KingdomCode; }
	public function GetKingdomName() { return $this->KingdomName; }

	public function GetRequestDate() { return $this->RequestDate; }
	public function GetActivity() { return $this->Activity; }
	public function GetOptions() { return $this->Options; }

	public function GetRevisor() { return $this->Revisor; }
	public function GetApprovalDate() { return $this->ApprovalDate; }
	public function GetScriptor() { return $this->Scriptor; }
	public function GetFile() { return $this->File; }

	public function GetComments() { return $this->Comments; }


	//--SET FUNCTIONS--
	public function SetID($inID) { $this->ID = $inID; }
	public function SetCharacter($inCharacter) { $this->Character = $inCharacter; }
	public function SetGroup($inGroup) { $this->Group = $inGroup; }
	public function SetApplicant($inApplicant) { $this->Applicant = $inApplicant; }
	public function SetStatus($inStatus) { $this->Status = $inStatus; }

	public function SetSubject($inSubject) { $this->Subject = $inSubject; }
	public function SetOptionCode($inCode) { $this->OptionCode = $inCode; }
	public function SetRewardCode($inCode) { $this->RewardCode = $inCode; }
	public function SetSuggestions($inList) { $this->Suggestions = $inList; }

	public function SetCountyID($inID) { $this->CountyID = $inID; }
	public function SetCountyName($inText) { $this->CountyName = $inText; }
	public function SetKingdomCode($inCode) { $this->KingdomCode = $inCode; }
	public function SetKingdomName($inText) { $this->KingdomName = $inText; }

	public function SetRequestDate($inDatetime) { $this->RequestDate = $inDatetime; }
	public function SetActivity($inActivity) { $this->Activity = $inActivity; }
	public function SetOptions($inList) { $this->Options = $inList; }

	public function SetRevisor($inUser) { $this->Revisor = $inUser; }
	public function SetApprovalDate($inDatetime) { $this->ApprovalDate = $inDatetime; }
	public function SetScriptor($inUser) { $this->Scriptor = $inUser; }
	public function SetFile($inFileName) { $this->File = $inFileName; }

	public function SetComments($inText) { $this->Comments = $inText; }


	//--GET LIST COUNTS--


	//--OTHERS--
	public function GetOption($inOptionName) { if( defined($this->Options[$inOptionName]) ) { return $this->Options[$inOptionName]; } 
						   return NULL; }
	public function SetOption($inOptionName, $inValue) { $this->Options[$inOptionName] = $inValue; }


	//--PRINT OBJECT FOR DEBUG PURPOSES--
	public function PrintHTML()
	{
		echo '<DIV class="debug">';
		echo '<b><u>Quest</u></b><br />';
		echo 'ID: ' . $this->ID . '<br />';
		echo 'Subject: ' . $this->Subject . '<br />';
		echo 'Status: ' . $this->Status . '<br />';
		echo '-------<br />';
		echo '</DIV>';
	}


} // END of Quest class

?>
