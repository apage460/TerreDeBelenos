<?php
define('RESTORED_LIFE', 2);


/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Custom Data Model v1.2 r7 ==				║
║	Represents a collection of custom data.			║
║	Serializable.						║
╚═══════════════════════════════════════════════════════════════╝
*/

class Custom
{

public $LinkedAccounts;
public $WoundedCharacters;
public $Passes;

public $SkillReceivers;
public $OutdatedCharacters;

public $NewSkillList;

public $SearchSubject;
public $SearchResults;


	//--CONSTRUCTOR--
	public function __construct( $inUserDataArray =array() )
	{

		if( isset($inUserDataArray['linkedaccount']) ) 			{ $this->LinkedAccounts = $inUserDataArray['linkedaccount']; }
		if( isset($inUserDataArray['woundedcharacters']) ) 		{ $this->WoundedCharacters = $inUserDataArray['woundedcharacters']; }
		if( isset($inUserDataArray['passes']) ) 			{ $this->Passes = $inUserDataArray['passes']; }

		if( isset($inUserDataArray['skillreceivers']) ) 		{ $this->SkillReceivers = $inUserDataArray['skillreceivers']; }
		if( isset($inUserDataArray['outdatedcharacters']) ) 		{ $this->OutdatedCharacters = $inUserDataArray['outdatedcharacters']; }

		if( isset($inUserDataArray['newskilllist']) ) 			{ $this->NewSkillList = $inUserDataArray['newskilllist']; }

		if( isset($inUserDataArray['searchsubject']) ) 			{ $this->SearchSubject = $inUserDataArray['searchsubject']; }
		if( isset($inUserDataArray['searchresults']) ) 			{ $this->SearchResults = $inUserDataArray['searchresults']; }

	}

	//--OTHERS--
	public function GetPassById( $inPassId ) { 
		foreach ($this->Passes as $i => $pass) {
			if( $pass['Id'] == $inPassId ) { return $pass; }
		}
		return False;
	}


} // END of Custom class

?>
