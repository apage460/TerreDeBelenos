<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Letter Model v1.2 r2 ==				║
║	Represents a letter sent.				║	
║	Serializable.						║
╚═══════════════════════════════════════════════════════════════╝
*/


class Letter
{

protected $ID;
protected $Type;
protected $Status;

protected $Index;
protected $SubIndex =0;

protected $SenderID;
protected $SenderName;
protected $RecipientID;
protected $RecipientName;
protected $Subject;
protected $Body;
protected $File;

protected $DateSent;
protected $OriginalPost =NULL;	# Change for Reply's Letter object in next versions. 


	//--CONSTRUCTOR--
	public function __construct( $inDataArray =array() )
	{

		if( isset($inDataArray['id']) ) 		{ $this->ID = $inDataArray['id']; }
		if( isset($inDataArray['type']) ) 		{ $this->Type = $inDataArray['type']; }
		if( isset($inDataArray['status']) ) 		{ $this->Status = $inDataArray['status']; }

		if( isset($inDataArray['index']) ) 		{ $this->Index = $inDataArray['index']; }
		if( isset($inDataArray['subindex']) ) 		{ $this->SubIndex = $inDataArray['subindex']; }

		if( isset($inDataArray['senderid']) )		{ $this->SenderID = $inDataArray['senderid']; }
		if( isset($inDataArray['sendername']) )		{ $this->SenderName = $inDataArray['sendername']; }
		if( isset($inDataArray['recipientid']) )	{ $this->RecipientID = $inDataArray['recipientid']; }
		if( isset($inDataArray['recipientname']) )	{ $this->RecipientName = $inDataArray['recipientname']; }
		if( isset($inDataArray['subject']) )		{ $this->Subject = $inDataArray['subject']; }
		if( isset($inDataArray['body']) )		{ $this->Body = $inDataArray['body']; }
		if( isset($inDataArray['file']) )		{ $this->File = $inDataArray['file']; }

		if( isset($inDataArray['datesent']) ) 		{ $this->DateSent = $inDataArray['datesent']; }
		if( isset($inDataArray['originalpost']) )	{ $this->OriginalPost = $inDataArray['originalpost']; }
	}


	//--GET FUNCTIONS--
	public function GetID() { return $this->ID; }
	public function GetType() { return $this->Type; }
	public function GetStatus() { return $this->Status; }

	public function GetIndex() { return $this->Index; }
	public function GetSubIndex() { return $this->SubIndex; }

	public function GetSenderID() { return $this->SenderID; }
	public function GetSenderName() { return $this->SenderName; }
	public function GetRecipientID() { return $this->RecipientID; }
	public function GetRecipientName() { return $this->RecipientName; }
	public function GetSubject() { return $this->Subject; }
	public function GetBody() { return $this->Body; }
	public function GetFile() { return $this->File; }

	public function GetDateSent() { return $this->DateSent; }
	public function GetOriginalPost() { return $this->OriginalPost; }
	

	//--SET FUNCTIONS--
	public function SetID($inID) { $this->ID = $inID; }
	public function SetType($inType) { $this->Type = $inType; }
	public function SetStatus($inStatus) { $this->Status = $inStatus; }

	public function SetIndex($inIndex) { $this->Index = $inIndex; }
	public function SetSubIndex($inIndex) { $this->SubIndex = $inIndex; }

	public function SetSenderID($inID) { $this->SenderID = $inID; }
	public function SetSenderName($inName) { $this->SenderName = $inName; }
	public function SetRecipientID($inID) { $this->RecipientID = $inID; }
	public function SetRecipientName($inName) { $this->RecipientName = $inName; }
	public function SetSubject($inSubject) { $this->Subject = $inSubject; }
	public function SetBody($inText) { $this->Body = $inText; }
	public function SetFile($inPath) { $this->File = $inPath; }

	public function SetDateSent($inDate) { $this->DateSent = $inDate; }
	public function SetOriginalPost($inID) { $this->OriginalPost = $inID; }


	//--GET LIST COUNTS--
	public function GetReplyCount() { 	if( $this->OriginalPost ) { return 1; } #return Reply->GetReplyCount() +1;
						return 0; } 


	//--OTHERS--
	// public function GetRecipientName() { 	if( $this->RecipientID == 0 ) { return ""; }
	// 					else {  $lRecipient = $_SESSION['masterlist']->GetNPCs()[$this->RecipientID];
	// 						return $lRecipient['firstname']." ".$lRecipient['lastname']; } } 


	//--PRINT OBJECT FOR DEBUG PURPOSES--
	public function PrintHTML()
	{
		echo '<DIV class="debug">';
		echo '<b><u>Letter</u></b><br />';
		echo 'ID: ' . $this->ID . '<br />';
		echo 'Type: ' . $this->Type . '<br />';
		echo 'Status: ' . $this->Status . '<br />';
		echo '-------<br />';
		echo 'Destinataire: ' . $this->GetRecipientID() . '<br />';
		echo 'Objet: ' . $this->GetSubject() . '<br />';
		echo '-------<br />';
		echo 'Envoi: ' . $this->Group->GetDateSent() . '<br />';
		echo '</DIV>';
	}


} // END of Letter class

?>
