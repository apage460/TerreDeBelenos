<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Custom Data Model v1.2 r1 ==				║
║	Represents a collection of custom data.			║
║	Serializable.						║
╚═══════════════════════════════════════════════════════════════╝
*/

class Custom
{

public $Activities;
public $Passes;


	//--CONSTRUCTOR--
	public function __construct( $inUserDataArray =array() )
	{

		if( isset($inUserDataArray['activities']) ) 			{ $this->Activities = $inUserDataArray['activities']; }
		if( isset($inUserDataArray['passes']) ) 			{ $this->Passes = $inUserDataArray['passes']; }

	}


} // END of Custom class

?>
