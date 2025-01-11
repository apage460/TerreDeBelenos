<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Activity Model v1.2 r2 ==				║
║	Represents an activity.					║
║	Serializable.						║
╚═══════════════════════════════════════════════════════════════╝
*/


class Activity
{

protected $ID;

protected $Name;
protected $Type;
protected $Description;

protected $RegularPrice;
protected $LatePrice;
protected $DelayBeforeLate;	// in days

protected $StartingDate;	// datetime
protected $EndingDate;		// datetime

protected $Inscriptions;	

	//--CONSTRUCTOR--
	public function __construct( $inDataArray =array() )
	{

		if( isset($inDataArray['id']) ) 		{ $this->ID = $inDataArray['id']; }

		if( isset($inDataArray['name']) ) 		{ $this->Name = $inDataArray['name']; }
		if( isset($inDataArray['type']) ) 		{ $this->Type = $inDataArray['type']; }
		if( isset($inDataArray['description']) )	{ $this->Description = $inDataArray['description']; }

		if( isset($inDataArray['regularprice']) )	{ $this->RegularPrice = $inDataArray['regularprice']; }
		if( isset($inDataArray['lateprice']) ) 		{ $this->LatePrice = $inDataArray['lateprice']; }
		if( isset($inDataArray['delaybeforelate']) )	{ $this->DelayBeforeLate = $inDataArray['delaybeforelate']; }

		if( isset($inDataArray['startingdate']) )	{ $this->StartingDate = $inDataArray['startingdate']; }
		if( isset($inDataArray['endingdate']) ) 	{ $this->EndingDate = $inDataArray['endingdate']; }

		if( isset($inDataArray['inscriptions']) ) 	{ $this->Inscriptions = $inDataArray['inscriptions']; }
	}


	//--GET FUNCTIONS--
	public function GetID() { return $this->ID; }

	public function GetName() { return $this->Name; }
	public function GetType() { return $this->Type; }
	public function GetDescription() { return $this->Description; }

	public function GetRegularPrice() { return $this->RegularPrice; }
	public function GetLatePrice() { return $this->LatePrice; }
	public function GetDelayBeforeLate() { return $this->DelayBeforeLate; }

	public function GetStartingDate() { return $this->StartingDate; }
	public function GetEndingDate() { return $this->EndingDate; }

	public function GetInscriptions() { return $this->Inscriptions; }


	//--SET FUNCTIONS--
	public function SetID($inID) { $this->ID = $inID; }

	public function SetName($inName) { $this->Name = $inName; }
	public function SetType($inType) { $this->Type = $inType; }
	public function SetDescription($inDescription) { $this->Description = $inDescription; }

	public function SetRegularPrice($inPrice) { $this->RegularPrice = $inPrice; }
	public function SetLatePrice($inPrice) { $this->LatePrice = $inPrice; }
	public function SetDelayBeforeLate($inDelay) { $this->DelayBeforeLate = $inDelay; }

	public function SetStartingDate($inDatetime) { $this->StartingDate = $inDatetime; }
	public function SetEndingDate($inDatetime) { $this->EndingDate = $inDatetime; }

	public function SetInscriptions($inList) { $this->Inscriptions = $inList; }


	//--GET LIST COUNTS--


	//--OTHERS--
	public function GetEffectivePrice() {
		if( FIXED_PRICE) { return FIXED_PRICE; }

		$now = new DateTime();
		$lTargetDate = new DateTime( substr($this->StartingDate, 0, 10) );
		$lDaysBeforeEvent = $now->diff($lTargetDate)->format("%r%a");
		if( $lDaysBeforeEvent < $this->DelayBeforeLate ) { return $this->LatePrice; }
		return $this->RegularPrice;
	}
	public function GetDaysSinceBeginning() {
		$now = new DateTime(); $lTargetDate = new DateTime($this->StartingDate);
		return $lTargetDate->diff($now)->format("%r%a");
	}
	public function GetDaysSinceEnd() {
		$now = new DateTime(); $lTargetDate = new DateTime($this->EndingDate);
		return $lTargetDate->diff($now)->format("%r%a");
	}


	//--PRINT OBJECT FOR DEBUG PURPOSES--
	public function PrintHTML()
	{
		echo '<DIV class="debug">';
		echo '<b><u>Activity</u></b><br />';
		echo 'ID: ' . $this->ID . '<br />';
		echo 'Name: ' . $this->Name . '<br />';
		echo 'Type: ' . $this->Type . '<br />';
		echo '-------<br />';
		echo '</DIV>';
	}


} // END of Activity class

?>
