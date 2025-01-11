<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Quest Part Model v1.2 r2 ==				║
║	Represents one part of a quest.				║
║	Serializable.						║
╚═══════════════════════════════════════════════════════════════╝
*/


class QuestPart
{

protected $ID;
protected $Number;
protected $Status;

protected $Description;
protected $Activity;
protected $Scriptor;

protected $CreationDate;
protected $Comments;



	//--CONSTRUCTOR--
	public function __construct( $inDataArray =array() )
	{

		if( isset($inDataArray['id']) ) 		{ $this->ID = $inDataArray['id']; }
		if( isset($inDataArray['number']) ) 		{ $this->Number = $inDataArray['number']; }
		if( isset($inDataArray['status']) ) 		{ $this->Status = $inDataArray['status']; }

		if( isset($inDataArray['description']) ) 	{ $this->Description = $inDataArray['description']; }
		if( isset($inDataArray['activity']) ) 		{ $this->Activity = $inDataArray['activity']; }
		if( isset($inDataArray['scriptor']) ) 		{ $this->Scriptor = $inDataArray['scriptor']; }

		if( isset($inDataArray['creationdate']) ) 	{ $this->CreationDate = $inDataArray['creationdate']; }
		if( isset($inDataArray['comments']) ) 		{ $this->Comments = $inDataArray['comments']; }
	}


	//--GET FUNCTIONS--
	public function GetID() { return $this->ID; }
	public function GetNumber() { return $this->Number; }
	public function GetStatus() { return $this->Status; }

	public function GetDescription() { return $this->Description; }
	public function GetActivity() { return $this->Activity; }
	public function GetScriptor() { return $this->Scriptor; }

	public function GetCreationDate() { return $this->CreationDate; }
	public function GetComments() { return $this->Comments; }


	//--SET FUNCTIONS--
	public function SetID($inID) { $this->ID = $inID; }
	public function SetNumber($inNumber) { $this->Number = $inNumber; }
	public function SetStatus($inStatus) { $this->Status = $inStatus; }

	public function SetDescription($inDescription) { $this->Description = $inDescription; }
	public function SetActivity($inActivity) { $this->Activity = $inActivity; }
	public function SetScriptor($inUser) { $this->Scriptor = $inUser; }

	public function SetCreationDate($inDate) { $this->CreationDate = $inDate; }
	public function SetComments($inText) { $this->Comments = $inText; }


	//--GET LIST COUNTS--


	//--OTHERS--


	//--PRINT OBJECT FOR DEBUG PURPOSES--
	public function PrintHTML()
	{
		echo '<DIV class="debug">';
		echo '<b><u>Quest part</u></b><br />';
		echo 'Number: ' . $this->Number . '<br />';
		echo '-------<br />';
		echo '</DIV>';
	}


} // END of QuestPart class

?>
