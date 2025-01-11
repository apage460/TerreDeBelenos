<?php


/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== RepairBot Data Model v1.2 r0 ==			║
║	Required data for database cleaning.			║
║	Serializable.						║
╚═══════════════════════════════════════════════════════════════╝
*/

class RepairBot
{

public $Processes;


	//--CONSTRUCTOR--
	public function __construct( $inUserDataArray =array() )
	{

		if( isset($inUserDataArray['processes']) ) 	{ $this->Processes = $inUserDataArray['processes']; }

	}

	//--OTHERS--


} // END of RepairBot class

?>
