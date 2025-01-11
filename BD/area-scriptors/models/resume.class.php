<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Resume Model v1.2 r0 ==				║
║	Represents the resumé of an activity.			║
║	Serializable.						║
╚═══════════════════════════════════════════════════════════════╝
*/


class Resume
{

protected $ID;

protected $Activity;
protected $Quest;
protected $QuestPart;

protected $CreationDate;

protected $Text;


	//--CONSTRUCTOR--
	public function __construct( $inDataArray =array() )
	{

		if( isset($inDataArray['id']) ) 		{ $this->ID = $inDataArray['id']; }

		if( isset($inDataArray['activity']) ) 		{ $this->Activity = $inDataArray['activity']; }
		if( isset($inDataArray['quest']) ) 		{ $this->Quest = $inDataArray['quest']; }
		if( isset($inDataArray['questpart']) ) 		{ $this->QuestPart = $inDataArray['questpart']; }

		if( isset($inDataArray['creationdate']) ) 	{ $this->CreationDate = $inDataArray['creationdate']; }

		if( isset($inDataArray['text']) ) 		{ $this->Text = $inDataArray['text']; }
	}


	//--GET FUNCTIONS--
	public function GetID() { return $this->ID; }

	public function GetActivity() { return $this->Activity; }
	public function GetQuest() { return $this->Quest; }
	public function GetQuestPart() { return $this->QuestPart; }

	public function GetCreationDate() { return $this->CreationDate; }

	public function GetText() { return $this->Text; }


	//--SET FUNCTIONS--
	public function SetID($inID) { $this->ID = $inID; }
	
	public function SetActivity($inActivity) { $this->Activity = $inActivity; }
	public function SetQuest($inQuest) { $this->Quest = $inQuest; }
	public function SetQuestPart($inQuestPart) { $this->QuestPart = $inQuestPart; }

	public function SetCreationDate($inDate) { $this->CreationDate = $inDate; }

	public function SetText($inText) { $this->Text = $inText; }


	//--GET LIST COUNTS--


	//--OTHERS--


	//--PRINT OBJECT FOR DEBUG PURPOSES--
	public function PrintHTML()
	{
		echo '<DIV class="debug">';
		echo '<b><u>Resumé</u></b><br />';
		echo 'Id: ' . $this->ID . '<br />';
		echo '-------<br />';
		echo '</DIV>';
	}


} // END of Resume class

?>
