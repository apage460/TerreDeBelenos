<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Custom Views v1.2 r0 ==				║
║	Display custom UIs meant for temporary jobs.		║
║	Requires custom model.					║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('models/custom.class.php');

class CustomUI
{

protected $Data;

protected $Error;

	//--CONSTRUCTOR--
	public function __construct($inData)
	{
		$this->Data = $inData;
	}


	//--DISPLAY ACTIVITY DATAS--
	public function DisplayActivityData()
	{
		// Check data
		if( !isset($this->Data) ) { $this->Error = "No data set!"; return False; }

		// Prepare data
		$lActivityList = array();
			if( $this->Data->Activities ) { $lActivityList = $this->Data->Activities; }


		// Display!
		echo '<div>';
		echo '<span class="section-title">Inscriptions et présences aux activités</span>';
		echo '<hr width=80% />';

		// Instructions
		echo '<span class="note">Les données suivantes sont celles contenues dans la nouvelles Base de données bélénoise. Dans le cas des activités datant de 2015 et avant, il faut donc les lire en gardant en tête qu\'elles ne prennent en compte que les joueurs qui sont revenus participer aux activités en 2016 et après. Les données pour les autres joueurs existent toujours, mais ne sont pas disponibles via cette interface.</span>';

		// Account list
		echo '<table style="font-size:0.8em;">';
		echo '<tr>
			<th class="black-cell" style="width:220px;">Activité</th>
			<th class="black-cell" style="width:80px;">Type</th>
			<th class="black-cell" style="width:100px;">Date de début</th>
			<th class="black-cell" style="width:90px;">Préinscriptions</th>
			<th class="black-cell" style="width:90px;">Présences</th>
		      </tr>';

		foreach($lActivityList as $i => $activity) { 
			$type = $activity['Type'];
				    if( $type == 'CONTRACT' )	{ $type = "Con-Tract"; }
				elseif( $type == 'BANQUET' )	{ $type = "Banquet"; }
				elseif( $type == 'GALA' )	{ $type = "Gala"; }

			$style = "background:white;";
				if( $i%2 ) { $style = "background:lightgrey;"; }

			echo '<tr>';
			echo '<td class="white-cell" style="text-align:left;width:220px; '.$style.'">'		.$activity['Nom'].			'</td>';
			echo '<td class="white-cell" style="width:80px; '.$style.'">'				.$type.					'</td>';
			echo '<td class="white-cell" style="width:100px; '.$style.'">'				.substr($activity['DateDebut'], 0, 10).	'</td>';
			echo '<td class="white-cell" style="width:90px; '.$style.'">'				.$activity['Inscriptions'].		'</td>';
			echo '<td class="white-cell" style="width:90px; '.$style.'">'				.$activity['Presences'].		'</td>';
			echo '</tr>';
		}
		echo '</table>';

		echo '<hr width=80% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="update-data"/>';

		echo '<button type="submit" name="submit" value="Activités" class="smalltext-button" style="margin:0px;"/>';
		echo 'Actualiser';
		echo '</button>';

		echo '</form>';
		echo '<hr width=80% />';
		echo '</div>';
	}


	//--DISPLAY PASS DATAS--
	public function DisplayPassData()
	{
		// Check data
		if( !isset($this->Data) ) { $this->Error = "No data set!"; return False; }

		// Prepare data
		$lPassList = array();
			if( $this->Data->Passes ) { $lPassList = $this->Data->Passes; }


		// Display!
		echo '<div>';
		echo '<span class="section-title">Acquisition de passes</span>';
		echo '<hr width=80% />';

		// Instructions
		echo '<span class="note">Voici le nombre d\'acquisition des différentes passes à partir de 2016</span>';

		// Account list
		echo '<table style="font-size:0.8em;">';
		echo '<tr>
			<th class="black-cell" style="width:200px;">Passe</th>
			<th class="black-cell" style="width:100px;">Nb. Détenteurs</th>
		      </tr>';

		foreach($lPassList as $pass) { 
			echo '<tr>';
			echo '<td class="white-cell" style="width:200px;">'	.$pass['Nom'].			'</td>';
			echo '<td class="white-cell" style="width:100px;">'	.$pass['Detenteurs'].		'</td>';
			echo '</tr>';
		}
		echo '</table>';

		echo '<hr width=80% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="update-data"/>';

		echo '<button type="submit" name="submit" value="Passes" class="smalltext-button" style="margin:0px;"/>';
		echo 'Actualiser';
		echo '</button>';

		echo '</form>';
		echo '<hr width=80% />';
		echo '</div>';
	}


} // END of CustomUI class

?>
