<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Approval Model v1.2 r0 ==				║
║	Represents an approval process.				║
║	Serializable.						║
╚═══════════════════════════════════════════════════════════════╝
*/


class Approval
{

protected $ID;

protected $Subject;
protected $Status;

protected $RequestDate;		// datetime
protected $ApprovalDate;	// datetime

protected $Comments;	

	//--CONSTRUCTOR--
	public function __construct( $inDataArray =array() )
	{

		if( isset($inDataArray['id']) ) 		{ $this->ID = $inDataArray['id']; }

		if( isset($inDataArray['subject']) ) 		{ $this->Subject = $inDataArray['subject']; }
		if( isset($inDataArray['status']) ) 		{ $this->Status = $inDataArray['status']; }

		if( isset($inDataArray['requestdate']) )	{ $this->RequestDate = $inDataArray['requestdate']; }
		if( isset($inDataArray['approvaldate']) ) 	{ $this->ApprovalDate = $inDataArray['approvaldate']; }

		if( isset($inDataArray['comments']) ) 	{ $this->Comments = $inDataArray['comments']; }
	}


	//--GET FUNCTIONS--
	public function GetID() { return $this->ID; }

	public function GetSubject() { return $this->Subject; }
	public function GetStatus() { return $this->Status; }

	public function GetRequestDate() { return $this->RequestDate; }
	public function GetApprovalDate() { return $this->ApprovalDate; }

	public function GetComments() { return $this->Comments; }


	//--SET FUNCTIONS--
	public function SetID($inID) { $this->ID = $inID; }

	public function SetSubject($inSubject) { $this->Subject = $inSubject; }
	public function SetStatus($inStatus) { $this->Status = $inStatus; }

	public function SetRequestDate($inDatetime) { $this->RequestDate = $inDatetime; }
	public function SetApprovalDate($inDatetime) { $this->ApprovalDate = $inDatetime; }

	public function SetComments($inText) { $this->Comments = $inText; }


	//--GET LIST COUNTS--


	//--OTHERS--


	//--PRINT OBJECT FOR DEBUG PURPOSES--
	public function PrintHTML()
	{
		echo '<DIV class="debug">';
		echo '<b><u>Approval</u></b><br />';
		echo 'ID: ' . $this->ID . '<br />';
		echo 'Subject: ' . $this->Subject . '<br />';
		echo 'Status: ' . $this->Status . '<br />';
		echo '-------<br />';
		echo '</DIV>';
	}


} // END of Approval class

?>
