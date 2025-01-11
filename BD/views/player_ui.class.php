<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== User Views v1.2 r7 ==				║
║	Display a user's gameplay related UIs.			║
║	Requires user model.					║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('models/player.class.php');
include_once('views/user_ui.class.php');

class PlayerUI extends UserUI
{

	//--CONSTRUCTOR--
	public function __construct($inPlayer)
	{
		parent::__construct($inPlayer);
	}


	//--DISPLAY PLAYER INFORMATION--
	public function DisplayPlayerInfo()
	{
		// Check if there's a user...
		if( $this->User == null ) { $this->Error = "No user defined!"; return; }
		if( !($this->User instanceof Player) ) { $this->Error = "User is no player!"; return False; }


		// Prepare data for the form
		$lAttendance = $this->User->GetLastAttendance();
			if( !$lAttendance ) { $lAttendance = $this->User->GetOldAttendance(); }

		$lFreeActivities = $this->User->GetFreeActivityVouchers();
			if( !$lFreeActivities ) { $lFreeActivities = 0; }

		$lFreeKids = $this->User->GetFreeKidVouchers();
			if( !$lFreeKids ) { $lFreeKids = 0; }

		$lVPoints = $this->User->GetVolunteeringPointCount();
			if( !$lVPoints ) { $lVPoints = 0; }

		$lPassList = $this->User->GetPasses();
		$lFormattedPassList = "";
		foreach($lPassList as $i => $pass) { 
			$now = new DateTime("now");
			$pass_end = new DateTime($pass['endingdate']);
			if( $pass_end > $now ) { $lFormattedPassList .= $pass['name'] . "<br />"; } 
		}

		$lDebtLabel = "Crédit";	
		$lAbsoluteDebt = $this->User->GetTotalDebt();
			if($lAbsoluteDebt < 0) {$lDebtLabel = "Dette"; $lAbsoluteDebt = $lAbsoluteDebt * -1;}	// !Zero means credit, don't test TotalDebts after taking absolute value!


		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Profil de joueur</span>';
		echo '<hr width=70% />';

		// Display data
		echo '<table>';		
		echo '<tr><td class="labelname">Âge :</td> 			<td class="labelvalue">' . $this->User->GetAge() . ' ans</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Participations au GN :</td>	<td class="labelvalue">' . $this->User->GetMainActivityCount() . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Autres évènements :</td>	<td class="labelvalue">' . $this->User->GetOtherActivityCount() . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Dernière présence :</td> 	<td class="labelvalue">' . $lAttendance . '</td></tr>';
		if(USER_EXPERIENCE_ENABLED){
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Expérience de joueur :</td> 	<td class="labelvalue">' . $this->User->GetTotalExperience() . '</td></tr>';
		} 
		echo '<tr><td colspan="2"><hr style="margin-top: 10px;" /></td></tr>';
		if(USER_WARNINGS_ENABLED){
		echo '<tr><td class="labelname">Avertissements :</td>	 	<td class="labelvalue">' . $this->User->GetWarningCount() . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Blâmes :</td>	 		<td class="labelvalue">' . $this->User->GetBlameCount() . '</td></tr>';

		echo '<tr><td colspan="2"><hr style="margin-top: 10px;" /></td></tr>';
		} if(USER_VOUCHERS_ENABLED){		
		echo '<tr><td class="labelname">GN gratuits :</td>	 	<td class="labelvalue">' . $lFreeActivities . '</td></tr>';
		} if(KID_VOUCHERS_ENABLED){		
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Enfants gratuits :</td>	 	<td class="labelvalue">' . $lFreeKids . '</td></tr>';
		} if(USER_PASSES_ENABLED){		
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Passes de saison :</td> 	<td class="labelvalue">' . $lFormattedPassList . '</td></tr>';
		} if(USER_VOLUNTEERING_ENABLED){
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Bénévolat :</td> 		<td class="labelvalue">' . $lVPoints . ' point(s)</td></tr>';
		} if(USER_DEBTS_ENABLED){
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">' . $lDebtLabel . ' :</td> 	<td class="labelvalue">' . $lAbsoluteDebt . ' $</td></tr>';
		}
		echo '</table>';

		echo '<hr width=70% />';

		echo '</div>';
	}


	//--DISPLAY PLAYER'S NOTES--
	public function DisplayPlayerNotes()
	{
		// Check if there's a user...
		if( $this->User == null ) { $this->Error = "No user defined!"; return; }


		// Prepare data for the form
		$lNoteList = $this->User->GetNotes();


		// Display skills
		echo '<div>';
		echo '<span class="section-title">Journal de compte</span>';
		echo '<hr width=70% />';

		echo '<table style="text-align: left;">';
		echo '<tr><th class="black-cell" style="width:500px;">Évènement</th> <th class="black-cell" style="width:80px;">Date</th></tr>';

		foreach($lNoteList as $note) {
			echo '<tr>';
			echo '<td class="white-cell" style="width:500px;">' . $note['message'] . '</td>';
			echo '<td class="white-cell" style="width:80px;">' . substr($note['date'], 0, 10) . '</td>';
			echo '</tr>';
		}

		echo '</table>';

		echo '<hr width=70% />';

		echo '</div>';
	}


	//--DISPLAY PLAYER'S ATTENDANCE INFORMATION--
	public function DisplayAttendances()
	{
		// Check if there's a user...
		if( $this->User == null ) { $this->Error = "No user defined!"; return; }

		// Get data
		$lAttendanceList = $this->User->GetActivities();

		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Présences aux activités</span>';
		echo '<hr width=70% />';

		// Display data table
		echo '<table>';
		echo '<tr>
			<th class="black-cell" style="width:10px;">#</th> 
			<th class="black-cell" style="width:300px;">Activité</th>
			<th class="black-cell" style="width:100px;">Type</th>
			<th class="black-cell" style="width:100px;">Date</th>
		      </tr>';

		foreach($lAttendanceList['name'] as $i => $activity) { 
			$line = $i+1;
			$type = 'Autre';
				    if( $lAttendanceList['type'][$i] == 'GN' ) 	{ $type = 'GN'; }
				elseif( $lAttendanceList['type'][$i] == 'BANQUET' ) { $type = 'Banquet'; }
				elseif( $lAttendanceList['type'][$i] == 'CONTRACT') { $type = 'Con-tract'; }
				elseif( $lAttendanceList['type'][$i] == 'CHRONIQ' ) { $type = 'Chronique'; }
				elseif( $lAttendanceList['type'][$i] == 'ACTEDEG') { $type = 'Acte de guerre'; }
				elseif( $lAttendanceList['type'][$i] == 'JOURNEE') { $type = 'Journée spéciale'; }

			echo '<tr>';
			echo '<td class="grey-cell" style="width:10px;">' 	.$line.		'</td>';
			echo '<td class="white-cell" style="width:300px;">' 	.$activity.	'</td>';
			echo '<td class="white-cell" style="width:100px;">' 	.$type.		'</td>';
			echo '<td class="white-cell" style="width:100px;">' 	.str_replace(":", "h", substr($lAttendanceList['date'][$i], 0, 10)).	'</td>';
			echo '</tr>';
		}

		echo '</table>';

		echo '<hr width=70% />';
		echo '</div>';
	}


	//--DISPLAY PLAYER'S EXPERIENCE GAINS AND LOSS--
	public function DisplayExperienceDetails()
	{
		// Check if there's a user...
		if( $this->User == null ) { $this->Error = "No user defined!"; return; }

		// Get data
		$lExperienceList = $this->User->GetExperience();
		$lTotalGains = 0;
		$lTotalUsed = 0;

		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Expérience de joueur</span>';
		echo '<hr width=70% />';

		// Display data table
		echo '<table>';
		echo '<tr>
			<th class="black-cell" style="width:10px;">#</th> 
			<th class="black-cell" style="width:400px;">Raison</th>
			<th class="black-cell" style="width:60px;">XP</th>
		      </tr>';

		foreach($lExperienceList['reason'] as $i => $reason) { 
			$line = $i+1;
			$amount = $lExperienceList['xp'][$i]; 
				if( $amount > 0 ) { $lTotalGains += $amount; $amount = "+".$amount; }
				else { $lTotalUsed += $amount; }

			echo '<tr>';
			echo '<td class="grey-cell" style="width:10px;">' 	.$line.		'</td>';
			echo '<td class="white-cell" style="width:400px;text-align:left">' 	.$reason.	'</td>';
			echo '<td class="white-cell" style="width:60px;">' 	.$amount.	'</td>';
			echo '</tr>';
		}

		echo '</table>';

		echo '<hr width=61% />';

		echo '<table>';

		echo '<tr>';
			echo '<td class="black-cell" style="width:10px;">+</td>';
			echo '<td class="white-cell" style="width:200px;text-align:left">Total des gains</td>';
			echo '<td class="white-cell" style="width:60px;">' 	.$lTotalGains.	'</td>';
		echo '</tr>';

		echo '<tr>';
			echo '<td class="black-cell" style="width:10px;">-</td>';
			echo '<td class="white-cell" style="width:200px;text-align:left">Total des dépenses</td>';
			echo '<td class="white-cell" style="width:60px;">' 	.$lTotalUsed.	'</td>';
		echo '</tr>';

		echo '<tr>';
			echo '<td class="black-cell" style="width:10px;">=</td>';
			echo '<td class="grey-cell" style="width:200px;text-align:left">XP restant</td>';
			echo '<td class="grey-cell" style="width:60px;">' 	.($lTotalGains + $lTotalUsed).	'</td>';
		echo '</tr>';

		echo '</table>';
		echo '<hr width=70% />';
		echo '</div>';
	}


	//--DISPLAY PLAYER'S WARNINGS AND BLAMES--
	public function DisplayWarningsAndBlames()
	{
		// Check if there's a user...
		if( $this->User == null ) { $this->Error = "No user defined!"; return; }

		// Get data
		$lWarningList = $this->User->GetWarnings();
		$lBlameList = $this->User->GetBlames();


		// Display warnings
		echo '<div>';
		echo '<span class="section-title">Liste des avertissements</span>';
		echo '<hr width=70% />';

		echo '<table>';
		echo '<tr>
			<th class="black-cell" style="width:10px;">#</th>
			<th class="black-cell" style="width:320px;">Raison</th> 
			<th class="black-cell" style="width:160px;">Date</th>
		      </tr>';

		foreach($lWarningList as $i => $warning) {
			$line = $i+1;
			echo '<tr>';
			echo '<td class="grey-cell" style="width:10px;">' 	.$line.			'</td>';
			echo '<td class="white-cell" style="width:320px;">' 	.$warning['reason'].	'</td>';
			echo '<td class="white-cell" style="width:160px;">' 	.str_replace(":", "h", substr($warning['date'], 0, 16)).	'</td>';
			echo '</tr>';
		}

		echo '</table>';

		echo '<hr width=70% />';
		echo '</div><br />';


		// Display blames
		echo '<div>';
		echo '<span class="section-title">Liste des blâmes</span>';
		echo '<hr width=70% />';

		echo '<table>';
		echo '<tr>
			<th class="black-cell" style="width:10px;">#</th>
			<th class="black-cell" style="width:320px;">Raison</th> 
			<th class="black-cell" style="width:160px;">Date</th>
		      </tr>';

		$line = 0;
		foreach($lBlameList as $i => $blame) {
			$line = $i+1;
			echo '<tr>';
			echo '<td class="grey-cell" style="width:10px;">' 	.$line.			'</td>';
			echo '<td class="white-cell" style="width:250px;">' 	.$blame['reason'].	'</td>';
			echo '<td class="white-cell" style="width:160px;">' 	.str_replace(":", "h", substr($blame['date'], 0, 16)).	'</td>';
			echo '</tr>';
		}

		echo '</table>';

		echo '<hr width=70% />';
		echo '</div><br />';
	}


	//--DISPLAY PLAYER'S CREDITS AND DEBTS INFORMATION--
	public function DisplayCredsAndBebts()
	{
		$this->DisplayFinancials();
		//$this->DisplayPatronage();		# Patronage project has been canceled until further notice.
	}


	//--DISPLAY PLAYER'S OWED MONEY INFORMATION--
	public function DisplayFinancials()
	{
		// Check if there's a user...
		if( $this->User == null ) { $this->Error = "No user defined!"; return; }

		// Get data
		$lDebtList = $this->User->GetDebts();


		// Display credits.
		echo '<div>';
		echo '<span class="section-title">Liste des crédits</span>';
		echo '<hr width=70% />';

		echo '<table>';
		echo '<tr>
			<th class="black-cell" style="width:10px;">#</th>
			<th class="black-cell" style="width:350px;">Raison</th> 
			<th class="black-cell" style="width:80px;">Montant</th> 
			<th class="black-cell" style="width:80px;">Date</th>
		     </tr>';

		$line = 0;
		foreach($lDebtList['amount'] as $i => $credit) {
			if( $credit > 0 ) {
				$line = $line+1;
				echo '<tr>';
				echo '<td class="grey-cell" style="width:10px;">' 	.$line.				'</td>';
				echo '<td class="white-cell" style="width:350px;">' 	.$lDebtList['reason'][$i].	'</td>';
				echo '<td class="white-cell" style="width:80px;">' 	.$credit. 			' $</td>';
				echo '<td class="white-cell" style="width:80px;">' 	.substr($lDebtList['date'][$i], 0, 10).'</td>';
				echo '</tr>';
			}
		}

		echo '</table>';

		echo '<hr width=70% />';
		echo '</div><br />';


		// Display debts.
		echo '<div>';
		echo '<span class="section-title">Liste des dettes</span>';
		echo '<hr width=70% />';

		echo '<table>';
		echo '<tr><th class="black-cell" style="width:10px;">#</th> <th class="black-cell" style="width:350px;">Raison</th> <th class="black-cell" style="width:80px;">Montant</th> <th class="black-cell" style="width:80px;">Date</th></tr>';

		$line = 0;
		foreach($lDebtList['amount'] as $i => $debt) {
			if( $debt <= 0 ) {
				$line = $line+1;
				echo '<tr>';
				echo '<td class="grey-cell" style="width:10px;">' 	.$line.				'</td>';
				echo '<td class="white-cell" style="width:350px;">' 	.$lDebtList['reason'][$i]. 	'</td>';
				echo '<td class="white-cell" style="width:80px;">' 	.abs($debt). 			' $</td>';
				echo '<td class="white-cell" style="width:80px;">' 	.substr($lDebtList['date'][$i], 0, 10).'</td>';
				echo '</tr>';
			}
		}

		echo '</table>';

		echo '<hr width=70% />';
		echo '</div><br />';
	}


	//--DISPLAY PLAYER'S VOLUNTEERING INFORMATION--
	public function DisplayVolunteeringRewards()
	{
		// Check if there's a user...
		if( $this->User == null ) { $this->Error = "No user defined!"; return; }

		// Get data
		$lPoints = $this->User->GetVolunteeringPointCount();

		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Bénévolat</span>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-account"/>';


		//Display credits
		echo '<div style="margin:auto; border:0px;">';
		echo '<table style="text-align: left;">';

		echo '<tr>';
		echo '<td class="grey-cell" style="width:175px;">Points de bénévolat</td>';
		echo '<td class="white-cell" style="width:75px;">' .$lPoints. ' points</td>';
		echo '</tr>';

		echo '</table>';
		echo '</div>';

		// Display options table
		echo '<table>';
		echo '<tr>	<th class="black-cell" style="width:300px;">Récompense</th> 
				<th class="black-cell" style="width:70px;">Coût</th> 
				<th class="black-cell" style="width:80px;">Obtenir</th></tr>';

		// OPTIONS
		// Free activity
		echo '<tr>';
		echo '<td class="white-cell" style="width:300px;text-align:left;">GN gratuit</td>';
		echo '<td class="white-cell" style="width:70px;">2 points</td>';
		echo '<td class="white-cell" style="width:80px;"><button type="submit" name="option" value="Obtenir GN gratuit" class="icon-button" style="background-color: white;" ><img src="images/icon_plus.png" class="icon-button-image"></button></td>';
		echo '</tr>';

		// Free kid entries
		if(KID_VOUCHERS_ENABLED) {
		echo '<tr>';
		echo '<td class="white-cell" style="width:300px;text-align:left;">2 entrées pour enfant de 11 ans et moins</td>';
		echo '<td class="white-cell" style="width:70px;">1 points</td>';
		echo '<td class="white-cell" style="width:80px;"><button type="submit" name="option" value="Obtenir entrées enfant" class="icon-button" style="background-color: white;" ><img src="images/icon_plus.png" class="icon-button-image"></button></td>';
		echo '</tr>';
		}

		// Other activities' price reduction
		echo '<tr>';
		echo '<td class="white-cell" style="width:300px;text-align:left;">20$ de réduction sur le prix d\'une activité</td>';
		echo '<td class="white-cell" style="width:70px;">1 points</td>';
		echo '<td class="white-cell" style="width:80px;">Sur place</td>';
		echo '</tr>';

		if(ACTIVITY_SERVICES_ENABLED) {
		// Free water
		echo '<tr>';
		echo '<td class="white-cell" style="width:300px;text-align:left;">Un 18 gallons d\'eau</td>';
		echo '<td class="white-cell" style="width:70px;">1 points</td>';
		echo '<td class="white-cell" style="width:80px;">Sur place</td>';
		echo '</tr>';

		// Free wood
		echo '<tr>';
		echo '<td class="white-cell" style="width:300px;text-align:left;">Bois de chauffage supplémentaire</td>';
		echo '<td class="white-cell" style="width:70px;">1 points</td>';
		echo '<td class="white-cell" style="width:80px;">Sur place</td>';
		echo '</tr>';

		// Free transport
		echo '<tr>';
		echo '<td class="white-cell" style="width:300px;text-align:left;">Transport </td>';
		echo '<td class="white-cell" style="width:70px;">1 points</td>';
		echo '<td class="white-cell" style="width:80px;">Sur place</td>';
		echo '</tr>';
		}

		// Color rule book
		if(WORLD == 'BELE') {
		echo '<tr>';
		echo '<td class="white-cell" style="width:300px;text-align:left;">Livre de règles en couleurs</td>';
		echo '<td class="white-cell" style="width:70px;">1 points</td>';
		echo '<td class="white-cell" style="width:80px;">À venir</td>';
		echo '</tr>';
		}

		echo '</table>';

		echo '<span class="note">Chaque point de bénévolat a une valeur approximative de 20$, exception faite lorsqu\'il est utilisé pour obtenir un avantage ou une ressource que Bélénos possède en quantité limitée (ex.: bois ou barils d\'eau). Dans ce cas, le besoin de l\'Organisation de garder ces ressources disponibles aux non-bénévoles réduit la valeur d\'un point utilisé à cet effet.</span>';

		echo '<span class="note"><b>Entrées gratuites : </b>Vos entrées gratuites peuvent être consultées sous l\'encadré de votre fiche de joueur (option « Ma fiche de joueur » dans le menu de gauche).</span>';

		echo '<hr width=70% />';
		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY PLAYER'S SQUIRING FORM--
	public function DisplayTutoringGroupForm()
	{
		// Check if there's a user...
		if( $this->User == null ) { $this->Error = "No user defined!"; return; }


		// Display!
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-player"/>';

		echo '<span class="section-title">S\'inscrire au groupe cadre</span>';
		echo '<hr width=70% />';

		if( $this->User->GetTutor() == 'GroupeCadre' ) {
			echo '<span class="note">Vous faites déjà partie du groupe cadre dirigé par Miakim L\'Heureux! Contactez un Organisateur pour être retiré du groupe. Les joueurs de moins de 16 ans devront alors fournir le compte de leur nouveau tuteur.</span>';
		}
		else {
			echo '<span class="note">Dirigé par Miakim L\'Heureux et son équipe, le groupe cadre est un groupe fait pour les joueurs âgés de 12 à 15 ans. Il vise à fournir l\'encadrement nécessaire à ses membres afin que ceux-ci puissent apprendre les rudiments du jeu sous le tutorat d\'animateurs habitués. Le groupe cadre possède un fort bien à lui pouvant accueillir une quarantaine de personnes.</span>';

			echo '<button type="submit" name="option" value="Ajouter encadrement" class="submit-button" />';
			echo 'S\'inscrire';
			echo '</button>';
		}

		echo '<hr width=70% />';

		echo '</form>';
		echo '</div>';
	}


} // END of PlayerUI class

?>
