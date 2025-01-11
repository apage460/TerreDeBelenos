<?php

/*
=SCRIPTOR FILE=
╔══CLASS════════════════════════════════════════════════════════╗
║	== Statistics Model v1.2 r0 ==				║
║	Represents different kind of compiled statistics.	║
║	Serializable.						║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('models/datatable.class.php'); 		// Statistics definition

class Statistics
{

protected $DataTables;


	//--CONSTRUCTOR--
	public function __construct( $inDataArray =array() )
	{

		if( isset($inDataArray['datatables']) ) 	{ $this->DataTables = $inDataArray['datatables']; }

	}


	//--GET FUNCTIONS--
	public function GetDataTables() { return $this->DataTables; }


	//--SET FUNCTIONS--
	public function SetDataTables($inTables) { $this->DataTables = $inTables; }


	//--GET LIST COUNTS--
	public function GetTableCount() { return count($this->DataTables); }


	//--OTHERS--
	public function AddDataTable($inTable) { $this->DataTables[] = $inTable; }

	public function GetTableByName( $inName ) { 
		if( !isset($this->DataTables) || !$this->DataTables) { return False; }

		foreach( $this->DataTables as $table ) { if( $table->GetName() == $inName ) { return $table; } }

		return False;
	}


	//--PRINT OBJECT FOR DEBUG PURPOSES--
	public function PrintHTML()
	{
		echo '<DIV class="debug">';
		echo '<b><u>Statistics</u></b><br />';
		echo '-------<br />';
		echo '</DIV>';
	}


} // END of Statistics class

?>
