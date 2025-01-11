<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Pass Model v1.2 r0 ==				║
║	Represents a special pass for activities.		║
║	Serializable.						║
╚═══════════════════════════════════════════════════════════════╝
*/


class Pass
{

protected $ID;

protected $Name;
protected $Description;
protected $Price;
protected $Acquired;

protected $StartingDate;	// datetime
protected $EndingDate;		// datetime

protected $FreeActivities;	

	//--CONSTRUCTOR--
	public function __construct( $inPassDataArray =array() )
	{

		if( isset($inPassDataArray['id']) ) 		{ $this->ID = $inPassDataArray['id']; }

		if( isset($inPassDataArray['name']) ) 		{ $this->Name = $inPassDataArray['name']; }
		if( isset($inPassDataArray['description']) )	{ $this->Description = $inPassDataArray['description']; }
		if( isset($inPassDataArray['price']) ) 		{ $this->Price = $inPassDataArray['price']; }
		if( isset($inPassDataArray['acquired']) ) 	{ $this->Acquired = $inPassDataArray['acquired']; }

		if( isset($inPassDataArray['startingdate']) )	{ $this->StartingDate = $inPassDataArray['startingdate']; }
		if( isset($inPassDataArray['endingdate']) ) 	{ $this->EndingDate = $inPassDataArray['endingdate']; }

		if( isset($inPassDataArray['freeactivites']) ) 	{ $this->FreeActivities = $inPassDataArray['freeactivites']; }
	}


	//--GET FUNCTIONS--
	public function GetID() { return $this->ID; }

	public function GetName() { return $this->Name; }
	public function GetDescription() { return $this->Description; }
	public function GetPrice() { return $this->Price; }
	public function IsAcquired() { return $this->Acquired; }

	public function GetStartingDate() { return $this->StartingDate; }
	public function GetEndingDate() { return $this->EndingDate; }

	public function GetFreeActivities() { return $this->FreeActivities; }


	//--SET FUNCTIONS--
	public function SetID($inID) { $this->ID = $inID; }

	public function SetName($inName) { $this->Name = $inName; }
	public function SetDescription($inDescription) { $this->Description = $inDescription; }
	public function SetPrice($inPrice) { $this->Price = $inPrice; }
	public function SetAcquired($inStatus =True) { $this->Acquired = $inStatus; }

	public function SetStartingDate($inDatetime) { $this->StartingDate = $inDatetime; }
	public function SetEndingDate($inDatetime) { $this->EndingDate = $inDatetime; }

	public function SetFreeActivities($inList) { $this->FreeActivities = $inList; }


	//--GET LIST COUNTS--


	//--OTHERS--


	//--PRINT OBJECT FOR DEBUG PURPOSES--
	public function PrintHTML()
	{
		echo '<DIV class="debug">';
		echo '<b><u>Pass</u></b><br />';
		echo 'ID: ' . $this->ID . '<br />';
		echo 'Name: ' . $this->Name . '<br />';
		echo 'Description: ' . $this->Description . '<br />';
		echo 'Price: ' . $this->Price . '<br />';
		echo '-------<br />';
		echo '</DIV>';
	}


} // END of Pass class

?>
