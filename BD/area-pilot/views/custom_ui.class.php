<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Custom Views v1.2 r5 ==				║
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


	//--DISPLAY ACTIVITY TRANSFER LISTS--
	public function DisplayActivityTransferUI()
	{
		// Check data
		if( !isset($this->Data) ) { $this->Error = "No data set!"; return False; }

		// Prepare data
		$lAccountList = array();
			if( $this->Data->LinkedAccounts ) { $lAccountList = $this->Data->LinkedAccounts; }


		// Display!
		echo '<div>';
		echo '<span class="section-title">Comptes appariés</span>';
		echo '<hr width=80% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-actions"/>';

		echo '<button type="submit" name="submit" value="Activités" class="smalltext-button" style="margin:0px;"/>';
		echo 'Transfert';
		echo '</button>';

		echo '</form>';
		echo '<hr width=80% />';

		// Account list
		echo '<table style="font-size:0.8em;">';
		echo '<tr>
			<th class="black-cell" style="width:10px;">ID</th> 
			<th class="black-cell" style="width:175px;">Nom complet</th>
			<th class="black-cell" style="width:80px;">Naissance</th>
			<th class="black-cell" style="width:240px;">Courriel</th>
			<th class="black-cell" style="width:65px;">Ancien ID</th>
			<th class="black-cell" style="width:25px;">Traité?</th>
		      </tr>';

		foreach($lAccountList as $account) { 
			$name = $account['Prenom'].' '.$account['Nom'];
			$processed = $account['TransfertPresences']?'Oui':'Non';

			echo '<tr>';
			echo '<td class="grey-cell" style="width:10px;">'	.$account['Id'].			'</td>';
			echo '<td class="white-cell" style="width:175px;">'	.$name.					'</td>';
			echo '<td class="white-cell" style="width:80px;">'	.$account['DateNaissance'].		'</td>';
			echo '<td class="white-cell" style="width:240px;">'	.substr($account['Courriel'], 0, 35).	'</td>';
			echo '<td class="white-cell" style="width:65px;">'	.$account['jou_uid'].			'</td>';
			echo '<td class="white-cell" style="width:25px;">'	.$processed.				'</td>';
			echo '</tr>';
		}
		echo '</table>';

		echo '<hr width=80% />';
		echo '</div>';
	}


	//--DISPLAY LAST EXPERIENCE TRANSFER LISTS--
	public function DisplayExperienceTransferUI()
	{
		// Check data
		if( !isset($this->Data) ) { $this->Error = "No data set!"; return False; }

		// Prepare data
		$lAccountList = array();
			if( $this->Data->LinkedAccounts ) { $lAccountList = $this->Data->LinkedAccounts; }


		// Display!
		echo '<div>';
		echo '<span class="section-title">Comptes appariés</span>';
		echo '<hr width=80% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-actions"/>';

		echo '<button type="submit" name="submit" value="Transfert XP" class="smalltext-button" style="margin:0px;"/>';
		echo 'Transfert';
		echo '</button>';

		echo '</form>';
		echo '<hr width=80% />';

		// Account list
		echo '<table style="font-size:0.8em;">';
		echo '<tr>
			<th class="black-cell" style="width:10px;">ID</th> 
			<th class="black-cell" style="width:175px;">Nom complet</th>
			<th class="black-cell" style="width:80px;">Naissance</th>
			<th class="black-cell" style="width:240px;">Courriel</th>
			<th class="black-cell" style="width:65px;">Ancien ID</th>
			<th class="black-cell" style="width:40px;">XP?</th>
		      </tr>';

		foreach($lAccountList as $account) { 
			$name = $account['Prenom'].' '.$account['Nom'];
			$processed = 'Non';
				if( $account['DernierTransfertXP'] != null) { $processed = $account['DernierTransfertXP']; }

			echo '<tr>';
			echo '<td class="grey-cell" style="width:10px;">'	.$account['Id'].			'</td>';
			echo '<td class="white-cell" style="width:175px;">'	.$name.					'</td>';
			echo '<td class="white-cell" style="width:80px;">'	.$account['DateNaissance'].		'</td>';
			echo '<td class="white-cell" style="width:240px;">'	.substr($account['Courriel'], 0, 35).	'</td>';
			echo '<td class="white-cell" style="width:65px;">'	.$account['jou_uid'].			'</td>';
			echo '<td class="white-cell" style="width:40px;">'	.$processed.				'</td>';
			echo '</tr>';
		}
		echo '</table>';

		echo '<hr width=80% />';
		echo '</div>';
	}


	//--DISPLAY LIST OF CHARACTER MISSING HEALTH--
	public function DisplayLifeRestorationUI()
	{
		// Check data
		if( !isset($this->Data) ) { $this->Error = "No data set!"; return False; }

		// Prepare data
		$lWoundedList = array();
			if( $this->Data->WoundedCharacters ) { $lWoundedList = $this->Data->WoundedCharacters; }


		// Display!
		echo '<div>';
		echo '<span class="section-title">Regain de PV annuel</span>';
		echo '<hr width=80% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-actions"/>';

		echo '<button type="submit" name="submit" value="PV" class="smalltext-button" style="margin:0px;"/>';
		echo 'Restitution';
		echo '</button>';

		echo '</form>';
		echo '<hr width=80% />';

		// Account list
		echo '<table style="font-size:0.8em;">';
		echo '<tr>
			<th class="black-cell" style="width:10px;">ID</th> 
			<th class="black-cell" style="width:200px;">Nom complet</th>
			<th class="black-cell" style="width:80px;">Total / Naturel</th>
			<th class="black-cell" style="width:40px;">Rest.</th>
		      </tr>';

		foreach($lWoundedList as $wounded) { 
			$name = $wounded['Prenom'].' '.$wounded['Nom'];

			echo '<tr>';
			echo '<td class="grey-cell" style="width:10px;">'	.$wounded['IdPersonnage'].			'</td>';
			echo '<td class="white-cell" style="width:200px;">'	.$name.						'</td>';
			echo '<td class="white-cell" style="width:80px;">'	.$wounded['PVTotal']." / ".$wounded['PVNaturels'].'</td>';
			echo '<td class="white-cell" style="width:40px;">'	.$wounded['PVRestitues'].			'</td>';
			echo '</tr>';
		}
		echo '</table>';

		echo '<hr width=80% />';
		echo '</div>';
	}


	//--DISPLAY PASS ELIGIBLE TO XP GIVEAWAYS--
	public function DisplayPassXPGiveAwayUI()
	{
		// Check data
		if( !isset($this->Data) ) { $this->Error = "No data set!"; return False; }

		// Prepare data
		$lPassList = array();
			if( $this->Data->Passes ) { $lPassList = $this->Data->Passes; }

		$lPassInput = '<select name="passid">';
			foreach($lPassList as $pass) { $lPassInput .= '<option value="'.$pass['Id'].'">'.$pass['Nom'].'</option>'; }
		$lPassInput .= "</select>";


		// Display!
		echo '<div>';
		echo '<span class="section-title">Don d\'XP pour une passe</span>';
		echo '<hr width=80% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-actions"/>';

		echo '<table>';
		echo '<tr><td class="inputname">Passe</td> <td class="inputbox" style="width: 250px;">' . $lPassInput . '</td></tr>';
		echo '</table>';

		echo '<br /><br />';

		echo '<button type="submit" name="submit" value="XP Passe" class="smalltext-button" style="margin:0px;"/>';
		echo 'Traiter la passe';
		echo '</button>';

		echo '</form>';
		echo '<hr width=80% />';
		echo '</div>';
	}


	//--DISPLAY LIST OF CHARACTER MISSING A PARTICULAR SKILL--
	public function DisplayMassSkillGainUI()
	{
		// Check data
		if( !isset($this->Data) ) { $this->Error = "No data set!"; return False; }

		// Prepare data
		$transfert_enabled = "disabled";

		$lCharacterList = array();
			if( $this->Data->SkillReceivers ) { $lCharacterList = $this->Data->SkillReceivers; $transfert_enabled = "enabled"; }

		$lClassCode = ""; if( isset($_POST['classcode']) ) { $lClassCode = $_POST['classcode']; }
		$lSkillCode = ""; if( isset($_POST['skillcode']) ) { $lSkillCode = $_POST['skillcode']; }

		// Display!
		echo '<div>';
		echo '<span class="section-title">Gain de compétence de masse</span>';
		echo '<hr width=80% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-actions"/>';

		echo '<table>';
		echo '<tr><td class="inputname">Code de classe</td>		<td class="inputbox"><input name="classcode" type="text" value="' . $lClassCode . '" maxlength="50"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Code de compétence</td>		<td class="inputbox"><input name="skillcode" type="text" value="' . $lSkillCode . '" maxlength="100"/></td></tr>';
		echo '<tr class="filler"></tr>';
		echo '</table><br/>';

		echo '<button type="submit" name="submit" value="Liste compétence manquante" class="smalltext-button" style="margin:0px;" />';
		echo 'Liste';
		echo '</button>';

		echo '<hr width=80% />';

		// Account list
		echo '<table style="font-size:0.8em;">';
		echo '<tr>
			<th class="black-cell" style="width:10px;">ID</th> 
			<th class="black-cell" style="width:175px;">Nom complet</th>
			<th class="black-cell" style="width:80px;">Classe</th>
		      </tr>';

		foreach($lCharacterList as $character) { 
			$name = $character['Prenom'].' '.$character['Nom'];

			echo '<tr>';
			echo '<td class="grey-cell" style="width:10px;">'	.$character['Id'].		'</td>';
			echo '<td class="white-cell" style="width:175px;">'	.$name.				'</td>';
			echo '<td class="white-cell" style="width:80px;">'	.$character['CodeClasse'].	'</td>';
			echo '</tr>';
		}
		echo '</table><br/>';

		// Submenu
		echo '<button type="submit" name="submit" value="Ajout compétence manquante" class="smalltext-button" style="margin:0px;" '.$transfert_enabled.'/>';
		echo 'Correction';
		echo '</button>';

		echo '</form>';

		echo '<hr width=80% />';
		echo '</div>';
	}


	//--DISPLAY MOST RECENT MASS SKILL UPDATE DASHBOARD--
	public function DisplayMassCharacterUpdateUI()
	{
		// Check data
		if( !isset($this->Data) ) { $this->Error = "No data set!"; return False; }

		// Prepare data
		$lCharacterList = array();
			if( $this->Data->OutdatedCharacters ) { $lCharacterList = $this->Data->OutdatedCharacters; }

		$lUpdateName = "Transformation des recettes et sorts en compétences précisables";


		// Display!
		echo '<div>';
		echo '<span class="section-title">Mise à jour massive des personnages</span>';
		echo '<hr width=80% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-actions"/>';

		echo '<table>';
		echo '<tr><td class="labelname" >Mise à jour</td> <td class="labelvalue" style="width: 250px;">' . $lUpdateName . '</td></tr>';
		echo '</table>';

		echo '<br /><br />';

		echo '<button type="submit" name="submit" value="Mise à jour personnages" class="smalltext-button" style="margin:0px;"/>';
		echo 'Mise à jour';
		echo '</button>';

		// Affected character list
		if ($lCharacterList) {
			echo '<hr width=80% />';

			echo '<table>';
			echo '<tr>
				<th class="black-cell" style="width:10px;">ID</th>
				<th class="black-cell" style="width:300px;">Personnage</th>
				<th class="black-cell" style="width:80px;">Classe</th>
			      </tr>';

			foreach ($lCharacterList as $character) {
				$name = $character['Prenom'].' '.$character['Nom'];

				echo '<tr>';
				echo '<td class="grey-cell" style="width:10px;">'	.$character['Id'].		'</td>';
				echo '<td class="white-cell" style="width:300px;">'	.$name.				'</td>';
				echo '<td class="white-cell" style="width:80px;">'	.$character['CodeClasse'].	'</td>';
				echo '</tr>';
			}
			echo '</table>';

			echo '<br /><br />';

		}

		echo '</form>';
		echo '<hr width=80% />';
		echo '</div>';
	}


	//--DISPLAY ID SEARCH FORM--
	public function DisplayIDSearchUI()
	{
		// Check data
		if( !isset($this->Data) ) { $this->Error = "No data set!"; return False; }

		// Prepare data
		$lSearchString = null; 		if( isset($_POST['searchstring']) ) { $lSearchString = $_POST['searchstring']; }

		$lSubject = null;
		$Uchecked = ''; $Cchecked = '';
			if( $this->Data->SearchSubject ) { 
				$lSubject = $this->Data->SearchSubject; 
				    if( $lSubject == 'Comptes' ) 	{ $Uchecked = 'checked'; }
				elseif( $lSubject == 'Personnages' ) 	{ $Cchecked = 'checked'; }
			}

		$lResults = array();
			if( $this->Data->SearchResults ) { $lResults = $this->Data->SearchResults; }

		// Display!
		echo '<div>';
		echo '<span class="section-title">Obtenir un ID de compte ou de personnage</span>';
		echo '<hr width=80% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-actions"/>';

		echo '<table>';
		echo '<tr><td class="inputname">Recherche</td>	<td class="inputbox"><input name="searchstring" type="text" value="' . $lSearchString . '" maxlength="30"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Sujet</td>	<td class="inputradio"><input name="searchsubject" type="radio" value="Comptes" '.$Uchecked.'> Comptes <br/><input name="searchsubject" type="radio" value="Personnages" '.$Cchecked.'> Personnages </td></tr>';
		echo '<tr class="filler"></tr>';
		echo '</table>';

		echo '<br /><br />';

		echo '<button type="submit" name="submit" value="Recherche ID" class="smalltext-button" style="margin:0px;"/>';
		echo 'Rechercher';
		echo '</button>';

		// New skill list
		if ($lResults) {
			echo '<hr width=80% />';

			echo '<span class="section-title">'. $lSubject .' trouvés</span>';

			echo '<table>';
			echo '<tr>
				<th class="black-cell" style="width:20px;">ID</th>
				<th class="black-cell" style="width:150px;">Compte</th>
				<th class="black-cell" style="width:150px;">Prénom</th>
				<th class="black-cell" style="width:150px;">Nom</th>
				<th class="black-cell" style="width:50px;">État</th>
			      </tr>';

			foreach ($lResults as $record) {
				echo '<tr>';
				echo '<td class="grey-cell" style="width:20px;">'	.$record['Id'].		'</td>';
				echo '<td class="white-cell" style="width:150px;">'	.$record['Compte'].	'</td>';
				echo '<td class="white-cell" style="width:150px;">'	.$record['Prenom'].	'</td>';
				echo '<td class="white-cell" style="width:150px;">'	.$record['Nom'].	'</td>';
				echo '<td class="white-cell" style="width:50px;">'	.$record['CodeEtat'].	'</td>';
				echo '</tr>';
			}
			echo '</table>';

			echo '<br /><br />';

		}

		echo '</form>';
		echo '<hr width=80% />';
		echo '</div>';
	}


	//--DISPLAY ACCOUNT REACTIVATION FORM--
	public function DisplayAccountReactivationUI()
	{
		// Check data
		if( !isset($this->Data) ) { $this->Error = "No data set!"; return False; }

		// Prepare data
		$lAccountID = null;	if( isset($_POST['accountid']) ) { $lAccountID = $_POST['accountid']; }
		$lPassword = null;	if( isset($_POST['password']) )  { $lPassword = $_POST['password']; }

		$lResult = array();
			if( $this->Data->SearchResults ) { $lResult  = $this->Data->SearchResults; }

		// Display!
		echo '<div>';
		echo '<span class="section-title">Réinitialiser un compte</span>';
		echo '<hr width=80% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-actions"/>';

		echo '<table>';
		echo '<tr><td class="inputname">ID Compte</td>			<td class="inputbox"><input name="accountid" type="text" value="' . $lAccountID . '" maxlength="11"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Nouveau mot de passe</td>	<td class="inputbox"><input name="password" type="text" value="' . $lPassword . '" maxlength="32"/></td></tr>';
		echo '<tr class="filler"></tr>';
		echo '</table>';

		echo '<br /><br />';

		echo '<button type="submit" name="submit" value="Réinitialiser compte" class="smalltext-button" style="margin:0px;"/>';
		echo 'Réinitialiser';
		echo '</button>';

		// New skill list
		if($lResult) {
			echo '<hr width=80% />';

			echo '<span class="section-title">Compte réinitialisé</span>';

			echo '<table>';
			echo '<tr>
				<th class="black-cell" style="width:20px;">ID</th>
				<th class="black-cell" style="width:100px;">Compte</th>
				<th class="black-cell" style="width:125px;">Prénom</th>
				<th class="black-cell" style="width:125px;">Nom</th>
				<th class="black-cell" style="width:150px;">Courriel</th>
			      </tr>';

			echo '<tr>';
				echo '<td class="grey-cell" style="width:20px;">'	.$lResult['Id'].	'</td>';
				echo '<td class="white-cell" style="width:100px;">'	.$lResult['Compte'].	'</td>';
				echo '<td class="white-cell" style="width:125px;">'	.$lResult['Prenom'].	'</td>';
				echo '<td class="white-cell" style="width:125px;">'	.$lResult['Nom'].	'</td>';
				echo '<td class="white-cell" style="width:150px;">'	.$lResult['Courriel'].	'</td>';
			echo '</tr>';
			echo '</table>';

			echo '<br /><br />';
		}

		echo '</form>';
		echo '<hr width=80% />';
		echo '</div>';
	}


	//--DISPLAY CLASS CHANGE FORM--
	public function DisplayClassChangeUI()
	{
		// Check data
		if( !isset($this->Data) ) { $this->Error = "No data set!"; return False; }

		// Prepare data
		$lCharacterID = null; 		if( isset($_POST['characterid']) ) { $lCharacterID = $_POST['characterid']; }
		$lClassCode = null;  		if( isset($_POST['classcode']) ) { $lClassCode = $_POST['classcode']; }
		$lArchChoiceCode = null;	if( isset($_POST['archchoicecode']) ) { $lArchChoiceCode = $_POST['archchoicecode']; }

		$lNewSkillList = array();
			if ( $this->Data->NewSkillList ) { $lNewSkillList = $this->Data->NewSkillList; }

		// Display!
		echo '<div>';
		echo '<span class="section-title">Changement de classe</span>';
		echo '<hr width=80% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-actions"/>';

		echo '<table>';
		echo '<tr><td class="inputname">ID Personnage</td>		<td class="inputbox"><input name="characterid" type="text" value="' . $lCharacterID . '" maxlength="11"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Nouvelle classe (code)</td>	<td class="inputbox"><input name="classcode" type="text" value="' . $lClassCode . '" maxlength="7"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Archétype (code)</td>		<td class="inputbox"><input name="archchoicecode" type="text" value="' . $lArchChoiceCode . '" maxlength="8"/></td></tr>';
		echo '<tr class="filler"></tr>';
		echo '</table>';

		echo '<br /><br />';

		echo '<button type="submit" name="submit" value="Classe personnages" class="smalltext-button" style="margin:0px;"/>';
		echo 'Appliquer';
		echo '</button>';

		// New skill list
		if ($lNewSkillList) {
			echo '<hr width=80% />';

			echo '<span class="section-title">Nouvelle liste de compétences du personnage</span>';

			echo '<table>';
			echo '<tr>
				<th class="black-cell" style="width:10px;">#</th>
				<th class="black-cell" style="width:50px;">Code</th>
				<th class="black-cell" style="width:50px;">Type</th>
				<th class="black-cell" style="width:50px;">Usages</th>
				<th class="black-cell" style="width:50px;">Coût</th>
				<th class="black-cell" style="width:60px;">Acquisition</th>
				<th class="black-cell" style="width:50px;">État</th>
			      </tr>';

			foreach ($lNewSkillList as $i => $skill) {
				echo '<tr>';
				echo '<td class="grey-cell" style="width:10px;">'	.$i.				'</td>';
				echo '<td class="white-cell" style="width:50px;">'	.$skill['CodeCompetence'].	'</td>';
				echo '<td class="white-cell" style="width:50px;">'	.$skill['Type'].		'</td>';
				echo '<td class="white-cell" style="width:50px;">'	.$skill['Usages'].		'</td>';
				echo '<td class="white-cell" style="width:50px;">'	.$skill['CoutXP'].		'</td>';
				echo '<td class="white-cell" style="width:60px;">'	.$skill['CodeAcquisition'].	'</td>';
				echo '<td class="white-cell" style="width:50px;">'	.$skill['CodeEtat'].		'</td>';
				echo '</tr>';
			}
			echo '</table>';

			echo '<br /><br />';

		}

		echo '</form>';
		echo '<hr width=80% />';
		echo '</div>';
	}


	//--DISPLAY CHARACTER DELETION FORM--
	public function DisplayCharacterDeletionUI()
	{
		// Check data
		if( !isset($this->Data) ) { $this->Error = "No data set!"; return False; }

		// Prepare data
		$lCharacterID = null; 	if( isset($_POST['characterid']) ) { $lCharacterID = $_POST['characterid']; }


		// Display!
		echo '<div>';
		echo '<span class="section-title">Suppression de personnage</span>';
		echo '<hr width=80% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-actions"/>';

		echo '<table>';
		echo '<tr><td class="inputname">ID Personnage</td>	<td class="inputbox"><input name="characterid" type="text" value="' . $lCharacterID . '" maxlength="11"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Mot de passe</td>	<td class="inputbox"><input name="dbapassword" type="password" value="" /></td></tr>';
		echo '<tr class="filler"></tr>';
		echo '</table>';

		echo '<br /><br />';

		echo '<button type="submit" name="submit" value="Suppression personnages" class="smalltext-button" style="margin:0px;"/>';
		echo 'Supprimer';
		echo '</button>';


		echo '</form>';
		echo '<hr width=80% />';
		echo '</div>';
	}


} // END of CustomUI class

?>
