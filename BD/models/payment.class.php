<?php

/*   NOT CURRENTLY USED !!!
╔══CLASS════════════════════════════════════════════════════════╗
║	== Payment Model v1.2 r0 ==				║
║	Represents a payment made. PayPal-friendly.		║
║	Serializable.						║
╚═══════════════════════════════════════════════════════════════╝
*/


class Payment
{

protected $ID;
protected $Status;

protected $Business = 'user@paypal.com';	// PayPal email address
protected $Currency = 'CAD';			
protected $CurrencySymbol = '$';
protected $Location = 'CA';
protected $CancelURL = 'http://www.terres-de-belenos.com/BD/main.php';
protected $ReturnURL = 'http://www.terres-de-belenos.com/BD/main.php';
protected $ReturnText = 'Retour à la BD bélénoise';
protected $Shipping = 0;
protected $Custom = '';

protected $ItemId;
protected $ItemName;
protected $ItemCost;


	//--CONSTRUCTOR--
	public function __construct( $inDataArray =array() )
	{

 
		if( isset($inDataArray['id']) ) 			{ $this->ID = $inDataArray['id']; }
		if( isset($inDataArray['status']) ) 			{ $this->Status = $inDataArray['status']; }

		if( isset($inDataArray['business']) ) 			{ $this->Business = $inDataArray['business']; }
		if( isset($inDataArray['currency']) ) 			{ $this->Currency = $inDataArray['currency']; }
		if( isset($inDataArray['currencysymbol']) ) 		{ $this->CurrencySymbol = $inDataArray['currencysymbol']; }
		if( isset($inDataArray['location']) ) 			{ $this->Location = $inDataArray['location']; }
		if( isset($inDataArray['cancelurl']) ) 			{ $this->CancelURL = $inDataArray['cancelurl']; }
		if( isset($inDataArray['returnurl']) ) 			{ $this->ReturnURL = $inDataArray['returnurl']; }
		if( isset($inDataArray['returntext']) ) 		{ $this->ReturnText = $inDataArray['returntext']; }
		if( isset($inDataArray['shipping']) ) 			{ $this->Shipping = $inDataArray['shipping']; }
		if( isset($inDataArray['custom']) ) 			{ $this->Custom = $inDataArray['custom']; }

		if( isset($inDataArray['itemid']) ) 			{ $this->ItemId = $inDataArray['itemid']; }
		if( isset($inDataArray['itemname']) )			{ $this->ItemName = $inDataArray['itemname']; }
		if( isset($inDataArray['itemcost']) )			{ $this->ItemCost = $inDataArray['itemcost']; }
	}


	//--GET FUNCTIONS--
	public function GetID() { return $this->ID; }
	public function GetStatus() { return $this->Status; }

	public function GetBusiness() { return $this->Business; }
	public function GetCurrency() { return $this->Currency; }
	public function GetCurrencySymbol() { return $this->CurrencySymbol; }
	public function GetLocation() { return $this->Location; }
	public function GetCancelURL() { return $this->CancelURL; }
	public function GetReturnURL() { return $this->ReturnURL; }
	public function GetReturnText() { return $this->ReturnText; }
	public function GetShipping() { return $this->Shipping; }
	public function GetCustom() { return $this->Custom; }

	public function GetItemId() { return $this->ItemId; }
	public function GetItemName() { return $this->ItemName; }
	public function GetItemCost() { return $this->ItemCost; }


	//--SET FUNCTIONS--
	public function SetID($inID) { $this->ID = $inID; }
	public function SetStatus($inStatus) { $this->Status = $inStatus; }

	public function SetBusiness($inText) { $this->Business = $inText; }
	public function SetCurrency($inText) { $this->Currency = $inText; }
	public function SetCurrencySymbol($inText) { $this->CurrencySymbol = $inText; }
	public function SetLocation($inText) { $this->Location = $inText; }
	public function SetCancelURL($inURL) { $this->CancelURL = $inURL; }
	public function SetReturnURL($inURL) { $this->ReturnURL = $inURL; }
	public function SetReturnText($inText) { $this->ReturnText = $inText; }
	public function SetShipping($inAmount) { $this->Shipping = $inAmount; }
	public function SetCustom($inList) { $this->Custom = $inList; }

	public function SetItemId($inID) { $this->ItemId = $inID; }
	public function SetItemName($inName) { $this->ItemName = $inName; }
	public function SetItemCost($inAmount) { $this->ItemCost = $inAmount; }


	//--OTHER FUNCTIONS--
	public function GetPayPalString() { 
		$lString = "";

		return $lString; 
	}


	//--PRINT OBJECT FOR DEBUG PURPOSES--
	public function PrintHTML()
	{
		echo '<DIV class="debug">';
		echo '<b><u>Payment</u></b><br />';
		echo 'ID: ' . $this->ID . '<br />';
		echo 'Name: ' . $this->Name . '<br />';
		echo '-------<br />';
		echo 'Business: ' . $this->Business . '<br />';
		echo '-------<br />';
		echo 'Item ID: ' . $this->ItemId . '<br />';
		echo 'Item Name: ' . $this->ItemName . '<br />';
		echo 'Item Cost: ' . $this->ItemCost . '<br />';
		echo '</DIV>';
	}

} // END of Payment class

?>