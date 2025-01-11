<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Repair Views v1.2 r0 ==				║
║	Display Database Clean-Up UIs.				║
║	Requires RepairBot model.				║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('models/repairbot.class.php');

class RepairUI
{

protected $Data;

protected $Error;

	//--CONSTRUCTOR--
	public function __construct($inData)
	{
		$this->Data = $inData;
	}


	//--DISPLAY DATABASE CLEAN-UP INTERFACE--
	public function DisplayDatabaseCleanUpUI()
	{
		// Check data
		if( !isset($this->Data) ) { $this->Error = "No data set!"; return False; }

		// Prepare data
		$lProcessList = array();
			if( $this->Data->Processes ) { $lProcessList = $this->Data->Processes; }

		// Display!
		echo '<div>';
		echo '<span class="section-title">Nettoyage des données</span>';
		echo '<hr width=80% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-actions"/>';

		echo '<button type="submit" name="submit" value="Nettoyage BD" class="smalltext-button" style="margin:0px;"/>';
		echo 'Démarrer';
		echo '</button>';

		echo '</form>';
		echo '<hr width=80% />';

		// Account list
		echo '<table style="font-size:0.8em;">';
		echo '<tr>
			<th class="black-cell" style="width:300px;">Processus</th>
			<th class="black-cell" style="width:100px;">Résultat</th>
		      </tr>';

		foreach ($lProcessList as $process) {
			echo '<tr>';
			echo '<td class="white-cell" style="width:300px;">'	.$process['Name'].	'</td>';
			echo '<td class="grey-cell" style="width:100px;">'	.$process['Result'].	'</td>';
			echo '</tr>';
		}

		echo '</table>';

		echo '<hr width=80% />';
		echo '</div>';
	}


} // END of RepairUI class

?>
