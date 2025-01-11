<?php

/*
=SCRIPTOR FILE=
╔══CLASS════════════════════════════════════════════════════════╗
║	== Statistics Views v1.2 r1 ==				║
║	Display compiled statistics.				║
║	Requires user model.					║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('models/statistics.class.php');

class StatisticsUI
{

protected $Statistics;

protected $Error;

	//--CONSTRUCTOR--
	public function __construct($inStatistics)
	{
		$this->Statistics = $inStatistics;
	}


	//--DISPLAY CHARACTERS VERSUS RACES STATISTICS--
	public function DisplayRaceStatistics()
	{
		// Check if there's a user...
		if( $this->Statistics == null ) { $this->Error = "No statistics defined!"; return; }


		// Prepare data for the form
		$lDisplayedTable = array();
		$lDataTable = $this->Statistics->GetTableByName( 'Races VS Personnages actifs' );
			if( $lDataTable ) { $lDisplayedTable = $lDataTable->GetTable(); }


		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Statistiques sur la race des personnages considérés "actifs"</span>';
		echo '<hr width=70% />';

		// Display data table
		echo '<table>';

		foreach( $lDisplayedTable as $i => $row ) {
			echo '<tr>';
			if($i == 0) { foreach($row as $data) { echo '<th class="black-cell" style="width:100px">' .$data. '</th>'; } }
			else {
				foreach($row as $j => $data) { 
					if($j == 0) { echo '<td class="grey-cell">' .$data. '</td>'; }
					else { echo '<td class="white-cell" style="width:100px">' .$data. '</td>'; }
				}
			}
			echo '</tr>';
		}

		echo '</table>';

		echo '<hr width=70% />';
		echo '</div>';
	}


	//--DISPLAY CHARACTERS VERSUS CLASSES STATISTICS--
	public function DisplayClassStatistics()
	{
		// Check if there's a user...
		if( $this->Statistics == null ) { $this->Error = "No statistics defined!"; return; }


		// Prepare data for the form
		$lDisplayedTable = array();
		$lDataTable = $this->Statistics->GetTableByName( 'Classes VS Personnages actifs' );
			if( $lDataTable ) { $lDisplayedTable = $lDataTable->GetTable(); }


		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Statistiques sur la classe des personnages considérés "actifs"</span>';
		echo '<hr width=70% />';

		// Display data table
		echo '<table>';

		foreach( $lDisplayedTable as $i => $row ) {
			echo '<tr>';
			if($i == 0) { foreach($row as $data) { echo '<th class="black-cell" style="width:100px">' .$data. '</th>'; } }
			else {
				foreach($row as $j => $data) { 
					if($j == 0) { echo '<td class="grey-cell">' .$data. '</td>'; }
					else { echo '<td class="white-cell" style="width:100px">' .$data. '</td>'; }
				}
			}
			echo '</tr>';
		}

		echo '</table>';

		echo '<hr width=70% />';
		echo '</div>';
	}


	//--DISPLAY CHARACTERS VERSUS RELIGIONS STATISTICS--
	public function DisplayReligionStatistics()
	{
		// Check if there's a user...
		if( $this->Statistics == null ) { $this->Error = "No statistics defined!"; return; }


		// Prepare data for the form
		$lDisplayedTable = array();
		$lDataTable = $this->Statistics->GetTableByName( 'Religions VS Personnages actifs' );
			if( $lDataTable ) { $lDisplayedTable = $lDataTable->GetTable(); }


		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Statistiques sur la religion des personnages considérés "actifs"</span>';
		echo '<hr width=70% />';

		// Display data table
		echo '<table>';

		foreach( $lDisplayedTable as $i => $row ) {
			echo '<tr>';
			if($i == 0) { foreach($row as $data) { echo '<th class="black-cell" style="width:100px">' .$data. '</th>'; } }
			else {
				foreach($row as $j => $data) { 
					if($j == 0) { echo '<td class="grey-cell">' .$data. '</td>'; }
					else { echo '<td class="white-cell" style="width:100px">' .$data. '</td>'; }
				}
			}
			echo '</tr>';
		}

		echo '</table>';

		echo '<span class="note" style="font-size:0.8em;">« Prêtres » inclut les clercs (ou acolytes), les shamans et les moines, selon la religion.</span>';

		echo '<hr width=70% />';
		echo '</div>';
	}


} // END of StatisticsUI class

?>
