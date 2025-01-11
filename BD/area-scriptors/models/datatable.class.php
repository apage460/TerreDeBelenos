<?php

/*
=SCRIPTOR FILE=
╔══CLASS════════════════════════════════════════════════════════╗
║	== Data Table Model v1.2 r0 ==				║
║	Represents a set of data that can be viewed in a table.	║
║	Serializable.						║
╚═══════════════════════════════════════════════════════════════╝
*/


class DataTable
{

protected $Name;

protected $Columns;		// First column names the rows if they are set.
protected $Data;		


	//--CONSTRUCTOR--
	public function __construct( $inDataArray =array() )
	{

		if( isset($inDataArray['name']) ) 		{ $this->Name = $inDataArray['name']; }

		if( isset($inDataArray['columns']) ) 		{ $this->Columns = $inDataArray['columns']; }
		if( isset($inDataArray['data']) ) 		{ $this->Data = $inDataArray['data']; }
	}


	//--GET FUNCTIONS--
	public function GetName() { return $this->Name; }

	public function GetColumns() { return $this->Columns; }
	public function GetData() { return $this->Data; }


	//--SET FUNCTIONS--
	public function SetName($inName) { $this->Name = $inName; }

	public function SetColumns($inColumns) { $this->Columns = $inColumns; }
	public function SetData($inData) { $this->Data = $inData; }


	//--GET LIST COUNTS--


	//--OTHERS--
	public function GetTable() { 
		$table = array(); 
		$table[0] = $this->Columns;
		foreach ($this->Data as $row) { $table[] = $row; }
		return $table;
	}


	//--PRINT OBJECT FOR DEBUG PURPOSES--
	public function PrintHTML()
	{
		echo '<DIV class="debug">';
		echo '<b><u>Data table</u></b><br />';
		echo 'Name: ' . $this->Name . '<br />';
		echo '-------<br />';
		echo '</DIV>';
	}


} // END of DataTable class

?>
