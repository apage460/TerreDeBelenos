<?php

/*
=SCRIPTOR FILE=
╔══CLASS════════════════════════════════════════════════════════╗
║	== Quest Model v1.2 r7 ==				║
║	Represents a quest, either for a character or a group.	║
║	Serializable.						║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('models/questpart.class.php');

class Quest
{

protected $ID;
protected $Type;
protected $Character;
protected $Group;
protected $Applicant;		// Player asking for the quest
protected $Status;

protected $Subject;
protected $OptionCode;
protected $RewardCode;
protected $Suggestions;

protected $Text;

protected $RequestDate;		// datetime
protected $ApprovalDate;	// datetime
protected $Activity;
protected $Scriptor;
protected $Comments;

protected $CountyID;
protected $CountyName;		

protected $InstitutionID;
protected $InstitutionName;		


	//--CONSTRUCTOR--
	public function __construct( $inDataArray =array() )
	{

		if( isset($inDataArray['id']) ) 		{ $this->ID = $inDataArray['id']; }
		if( isset($inDataArray['type']) )		{ $this->Type = $inDataArray['type']; }
		if( isset($inDataArray['character']) )		{ $this->Character = $inDataArray['character']; }
		if( isset($inDataArray['group']) )		{ $this->Group = $inDataArray['group']; }
		if( isset($inDataArray['applicant']) )		{ $this->Applicant = $inDataArray['applicant']; }
		if( isset($inDataArray['status']) )		{ $this->Status = $inDataArray['status']; }

		if( isset($inDataArray['subject']) ) 		{ $this->Subject = $inDataArray['subject']; }
		if( isset($inDataArray['optioncode']) ) 	{ $this->OptionCode = $inDataArray['optioncode']; }
		if( isset($inDataArray['rewardcode']) ) 	{ $this->RewardCode = $inDataArray['rewardcode']; }
		if( isset($inDataArray['suggestions']) ) 	{ $this->Suggestions = $inDataArray['suggestions']; }

		if( isset($inDataArray['text']) ) 		{ $this->Text = $inDataArray['text']; }

		if( isset($inDataArray['requestdate']) )	{ $this->RequestDate = $inDataArray['requestdate']; }
		if( isset($inDataArray['approvaldate']) ) 	{ $this->ApprovalDate = $inDataArray['approvaldate']; }
		if( isset($inDataArray['activity']) ) 		{ $this->Activity = $inDataArray['activity']; }
		if( isset($inDataArray['scriptor']) ) 		{ $this->Scriptor = $inDataArray['scriptor']; }
		if( isset($inDataArray['comments']) ) 		{ $this->Comments = $inDataArray['comments']; }

		if( isset($inDataArray['countyid']) ) 		{ $this->CountyID = $inDataArray['countyid']; }
		if( isset($inDataArray['countyname']) ) 	{ $this->CountyName = $inDataArray['countyname']; }

		if( isset($inDataArray['institutionid']) ) 	{ $this->InstitutionID = $inDataArray['institutionid']; }
		if( isset($inDataArray['institutionname']) ) 	{ $this->InstitutionName = $inDataArray['institutionname']; }
	}


	//--GET FUNCTIONS--
	public function GetID() { return $this->ID; }
	public function GetType() { return $this->Type; }
	public function GetCharacter() { return $this->Character; }
	public function GetGroup() { return $this->Group; }
	public function GetApplicant() { return $this->Applicant; }
	public function GetStatus() { return $this->Status; }

	public function GetSubject() { return $this->Subject; }
	public function GetOptionCode() { return $this->OptionCode; }
	public function GetRewardCode() { return $this->RewardCode; }
	public function GetSuggestions() { return $this->Suggestions; }

	public function GetText() { return $this->Text; }

	public function GetRequestDate() { return $this->RequestDate; }
	public function GetApprovalDate() { return $this->ApprovalDate; }
	public function GetActivity() { return $this->Activity; }
	public function GetScriptor() { return $this->Scriptor; }
	public function GetComments() { return $this->Comments; }

	public function GetCountyID() { return $this->CountyID; }
	public function GetCountyName() { return $this->CountyName; }

	public function GetInstitutionID() { return $this->InstitutionID; }
	public function GetInstitutionName() { return $this->InstitutionName; }


	//--SET FUNCTIONS--
	public function SetID($inID) { $this->ID = $inID; }
	public function SetType($inType) { $this->Type = $inType; }
	public function SetCharacter($inCharacter) { $this->Character = $inCharacter; }
	public function SetGroup($inGroup) { $this->Group = $inGroup; }
	public function SetApplicant($inApplicant) { $this->Applicant = $inApplicant; }
	public function SetStatus($inStatus) { $this->Status = $inStatus; }

	public function SetSubject($inSubject) { $this->Subject = $inSubject; }
	public function SetOptionCode($inCode) { $this->OptionCode = $inCode; }
	public function SetRewardCode($inCode) { $this->RewardCode = $inCode; }
	public function SetSuggestions($inList) { $this->Suggestions = $inList; }

	public function SetText($inText) { $this->Text = $inText; }

	public function SetRequestDate($inDatetime) { $this->RequestDate = $inDatetime; }
	public function SetApprovalDate($inDatetime) { $this->ApprovalDate = $inDatetime; }
	public function SetActivity($inActivity) { $this->Activity = $inActivity; }
	public function SetScriptor($inUser) { $this->Scriptor = $inUser; }
	public function SetComments($inText) { $this->Comments = $inText; }

	public function SetCountyID($inID) { $this->CountyID = $inID; }
	public function SetCountyName($inText) { $this->CountyName = $inText; }

	public function SetInstitutionID($inID) { $this->InstitutionID = $inID; }
	public function SetInstitutionName($inText) { $this->InstitutionName = $inText; }


	//--GET LIST COUNTS--
	#public function GetPartCount() { return count($this->Parts); }


	//--OTHERS--


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
