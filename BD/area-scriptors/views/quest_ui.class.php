<?php

/*
=SCRIPTOR FILE=
╔══CLASS════════════════════════════════════════════════════════╗
║	== Quest Views v1.2 r2 ==				║
║	Display quest management UIs.				║
║	Requires quest manager model.				║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('models/questmanager.class.php');

class QuestUI
{

protected $Manager;

protected $Error;

	//--CONSTRUCTOR--
	public function __construct($inManager)
	{
		$this->Manager = $inManager;
	}

//=================================================================== PERSONAL QUESTS UIs ===================================================================

	//--DISPLAY MY ASSIGNED QUESTS--
	public function DisplayMyPersonalQuests()
	{
		// Check if there's a quest manager...
		if( $this->Manager == null ) { $this->Error = "No quest manager defined!"; return; }


		// Prepare data for the form
		$lQuestList = $this->Manager->GetPersonalQuests();


		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Mes quêtes personnelles</span>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-quest-lists"/>';

		// Display data table
		echo '<table>';

		echo '<tr>
			<th class="black-cell" style="width:20px;">ID</th> 
			<th class="black-cell" style="width:150px;">Personnage</th>
			<th class="black-cell" style="width:180px;">Quête</th>
			<th class="black-cell" style="width:150px;">Comté choisi</th>
			<th class="black-cell" style="width:100px;">État</th>
			<th class="black-cell" style="width:20px;"></th>
		      </tr>';


		foreach( $lQuestList as $i => $quest ) {
			if( !$quest->GetScriptor() ) { /*Do nothing. Quests without scriptors can't be yours.*/ }
			elseif( $quest->GetScriptor()->GetID() == $_SESSION['player']->GetID() ){
				// Gather quest data
				$lCharacterName = $quest->GetCharacter()->GetFullName();

				$lStatus = "Indéterminé";
					    if( $quest->GetStatus() == 'DEM' )   { $lStatus = "Demande"; }
					elseif( $quest->GetStatus() == 'REPR' )  { $lStatus = "Reprise"; }
					elseif( $quest->GetStatus() == 'SUITE' ) { $lStatus = "Suite"; }
					elseif( $quest->GetStatus() == 'ACTIF' ) { $lStatus = "Active"; }
					elseif( $quest->GetStatus() == 'TERM'  ) { $lStatus = "Terminée"; }
					elseif( $quest->GetStatus() == 'RECOM' ) { $lStatus = "Récompensée"; }
					elseif( $quest->GetStatus() == 'VEROU' ) { $lStatus = "Verrouillée"; }
					elseif( $quest->GetStatus() == 'ANNUL' ) { $lStatus = "Annulée"; }
					elseif( $quest->GetStatus() == 'REFUS' ) { $lStatus = "Refusée"; }

				$lButton = '<button type="submit" name="select-personal-quest" value="' .$quest->GetID(). '" class="icon-button"/><img src="images/icon_see.png" width="16" heigth="16"></button>';

				$lCountyName = $quest->GetCountyName();
					if( $lCountyName == "" ) { $lCountyName = "Aucun"; }
 
 				// Display quest entry
				echo '<tr>';
				echo '<td class="grey-cell" style="width:20px;">'	.$quest->GetID().	'</td>'; 
				echo '<td class="white-cell" style="width:150px;">'	.$lCharacterName.	'</td>';
				echo '<td class="white-cell" style="width:180px;">'	.$quest->GetSubject().	'</td>';
				echo '<td class="white-cell" style="width:150px;">'	.$lCountyName.		'</td>';
				echo '<td class="white-cell" style="width:100px;">'	.$lStatus.		'</td>';
				echo '<td class="white-cell" style="width:20px;">'	.$lButton.		'</td>';
				echo '</tr>';
			}
		}

		echo '</table>';
		echo '</form>';

		echo '<hr width=70% />';
		echo '</div>';
	}


	//--DISPLAY UNASSIGNED PERSONAL QUESTS--
	public function DisplayUnassignedPersonalQuests()
	{
		// Check if there's a quest manager...
		if( $this->Manager == null ) { $this->Error = "No quest manager defined!"; return; }


		// Prepare data for the form
		$lQuestList = $this->Manager->GetPersonalQuests();


		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Quêtes personnelles en cours</span>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-quest-lists"/>';

		// Display data table
		echo '<table>';

		echo '<tr>
			<th class="black-cell" style="width:20px;">ID</th> 
			<th class="black-cell" style="width:150px;">Personnage</th>
			<th class="black-cell" style="width:180px;">Quête</th>
			<th class="black-cell" style="width:120px;">Comté choisi</th>
			<th class="black-cell" style="width:100px;">Approbation</th>
			<th class="black-cell" style="width:20px;"></th>
		      </tr>';


		foreach( $lQuestList as $i => $quest ) {
			if( $quest->GetStatus() == 'ACTIF' && !$quest->GetScriptor()->GetID() ) {
				// Gather quest data
				$lCharacterName = $quest->GetCharacter()->GetFullName();
				$lDate = substr($quest->GetApprovalDate(), 0, 10);

				$lButton =	'<button type="submit" name="select-personal-quest" value="' .$quest->GetID(). '" class="icon-button"/><img src="images/icon_see.png" width="16" heigth="16"></button>';

				$lCountyName = $quest->GetCountyName();
					if( $lCountyName == "" ) { $lCountyName = "Aucun"; }

				// Display quest entry
				echo '<tr>';
				echo '<td class="grey-cell" style="width:20px;">'	.$quest->GetID().	'</td>'; 
				echo '<td class="white-cell" style="width:150px;">'	.$lCharacterName.	'</td>';
				echo '<td class="white-cell" style="width:180px;">'	.$quest->GetSubject().	'</td>';
				echo '<td class="white-cell" style="width:120px;">'	.$lCountyName.		'</td>';
				echo '<td class="white-cell" style="width:100px;">'	.$lDate.		'</td>';
				echo '<td class="white-cell" style="width:20px;">'	.$lButton.		'</td>';
				echo '</tr>';
			}
		}

		echo '</table>';
		echo '</form>';

		echo '<hr width=70% />';
		echo '</div>';
	}


	//--DISPLAY ALL ACTIVE PERSONAL QUESTS--
	public function DisplayActivePersonalQuests()
	{
		// Check if there's a quest manager...
		if( $this->Manager == null ) { $this->Error = "No quest manager defined!"; return; }


		// Prepare data for the form
		$lQuestList = $this->Manager->GetPersonalQuests();


		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Quêtes personnelles en cours</span>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-quest-lists"/>';

		// Display data table
		echo '<table>';

		echo '<tr>
			<th class="black-cell" style="width:20px;">ID</th> 
			<th class="black-cell" style="width:150px;">Personnage</th>
			<th class="black-cell" style="width:180px;">Quête</th>
			<th class="black-cell" style="width:150px;">Comté choisi</th>
			<th class="black-cell" style="width:100px;">Responsable</th>
			<th class="black-cell" style="width:20px;"></th>
		      </tr>';


		foreach( $lQuestList as $i => $quest ) {
			if( $quest->GetStatus() == 'ACTIF' ) {
				// Gather quest data
				$lCharacterName = $quest->GetCharacter()->GetFirstName();
				$lScriptorName = $quest->GetScriptor()->GetFirstName();

				$lButton = '<button type="submit" name="select-personal-quest" value="' .$quest->GetID(). '" class="icon-button"/><img src="images/icon_see.png" width="16" heigth="16"></button>';

				$lCountyName = $quest->GetCountyName();
					if( $lCountyName == "" ) { $lCountyName = "Aucun"; }

				// Display quest entry
				echo '<tr>';
				echo '<td class="grey-cell" style="width:20px;">'	.$quest->GetID().	'</td>'; 
				echo '<td class="white-cell" style="width:150px;">'	.$lCharacterName.	'</td>';
				echo '<td class="white-cell" style="width:180px;">'	.$quest->GetSubject().	'</td>';
				echo '<td class="white-cell" style="width:150px;">'	.$lCountyName.		'</td>';
				echo '<td class="white-cell" style="width:100px;">'	.$lScriptorName.	'</td>';
				echo '<td class="white-cell" style="width:20px;">'	.$lButton.		'</td>';
				echo '</tr>';
			}
		}

		echo '</table>';
		echo '</form>';

		echo '<hr width=70% />';
		echo '</div>';
	}


	//--DISPLAY SELECTED PERSONAL QUEST--
	public function DisplaySelectedPersonalQuest( $inSubUI =NULL )
	{
		// Check if there's a quest manager...
		if( $this->Manager == null ) { $this->Error = "No quest manager defined!"; return; }


		// Prepare data for the form
		$lQuest = $this->Manager->GetSelectedQuest();


		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Quête personnelle #' .$lQuest->GetID(). '</span>';
		echo '<hr width=70% style="margin-bottom:5px;" />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Détail quête personnelle" class="smalltext-button" style="margin:0px;"/>';
		echo 'Quête';
		echo '</button>';

		echo '<button type="submit" name="option" value="Rédaction quête personnelle" class="smalltext-button" style="margin:0px;"/>';
		echo 'Rédaction';
		echo '</button>';

		echo '<button type="submit" name="option" value="Résumés personnels Scripteurs" class="smalltext-button" style="margin:0px;"/>';
		echo 'Résumés';
		echo '</button>';

		echo '<button type="submit" name="option" value="Personnage Scripteurs" class="smalltext-button" style="margin:0px;"/>';
		echo 'Personnage';
		echo '</button>';

		echo '</form>';

		echo '<hr width=70% />';
		echo '</div>';


		// Display SubUI if not null
		    if( $inSubUI == 'QUEST' ) { $this->DisplaySelectedPersonalQuestDetail(); }
		elseif( $inSubUI == 'WRITING' ) { $this->DisplaySelectedPersonalQuestWriting(); }
		elseif( $inSubUI == 'CHARACTER' ) { $this->DisplaySelectedPersonalQuestCharacter(); }
		elseif( $inSubUI == 'RESUMES' ) { $this->DisplaySelectedPersonalQuestResumes(); }
			elseif( $inSubUI == 'RESUME' ) { $this->DisplaySelectedPersonalResume(); }
	}


	//--DISPLAY SELECTED PERSONAL QUEST'S DETAIL--
	public function DisplaySelectedPersonalQuestDetail()
	{
		// Prepare data for the form
		$lQuest = $this->Manager->GetSelectedQuest();

		$lCurrentScriptor = $lQuest->GetScriptor();
			if(!$lCurrentScriptor) { $lCurrentScriptor = new User(); }

		$lStatus = "Indéterminé";
			    if( $lQuest->GetStatus() == 'DEM' ) { $lStatus = "Demande"; }
			elseif( $lQuest->GetStatus() == 'REPR' ) { $lStatus = "Reprise"; }
			elseif( $lQuest->GetStatus() == 'SUITE' ) { $lStatus = "Suite"; }
			elseif( $lQuest->GetStatus() == 'ACTIF' ) { $lStatus = "Active"; }
			elseif( $lQuest->GetStatus() == 'TERM'  ) { $lStatus = "Terminée"; }
			elseif( $lQuest->GetStatus() == 'RECOM' ) { $lStatus = "Terminée (R)"; }
			elseif( $lQuest->GetStatus() == 'ANNUL' ) { $lStatus = "Annulée"; }
			elseif( $lQuest->GetStatus() == 'REFUS' ) { $lStatus = "Refusée"; }

		
		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Détail</span>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-quest"/>';

		// Display data table
		$lApplicant = $lQuest->GetApplicant()->GetFullName() . ' ('.$lQuest->GetApplicant()->GetMailAddress().')';
		$lScriptor = $lCurrentScriptor->GetFullName();
			if(!$lCurrentScriptor->GetID()) { $lScriptor = '<button type="submit" name="submit" value="Modifier quête" style=""/>M\'assigner cette quête</button>'; }

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-quest"/>';
		echo '<input type="hidden" name="scriptor" value="'.$_SESSION['player']->GetID().'"/>';
		echo '<input type="hidden" name="status" value="'.$lQuest->GetStatus().'"/>';
		echo '<input type="hidden" name="comments" value="'.$lQuest->GetComments().'"/>';

		echo '<table>';

		echo '<tr><td class="labelname-small">Quête :</td> 		<td class="labelvalue" colspan="3">' .$lQuest->GetSubject(). '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">Comté choisi :</td> 	<td class="labelvalue" colspan="3">' .$lQuest->GetCountyName(). '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">Demandée par :</td> 	<td class="labelvalue" colspan="3">' .$lApplicant. '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">Responsable :</td> 	<td class="labelvalue" colspan="3">' .$lScriptor.'</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">État :</td>		<td class="labelvalue">' .$lStatus. '</td>
			  <td class="labelname-small">Demandée le :</td>	<td class="labelvalue">' .str_replace(":", "h", substr($lQuest->GetRequestDate(), 0, 16)). '</td></tr>';
		if( $lQuest->GetStatus() != 'DEM' ){
			echo '<tr class="filler"></tr>';
			echo '<tr><td class="labelname-small">Pour quel GN :</td>	<td class="labelvalue">' .$lQuest->GetActivity()->GetName(). '</td>
				  <td class="labelname-small">Approuvée le :</td> 	<td class="labelvalue">' .str_replace(":", "h", substr($lQuest->GetApprovalDate(), 0, 16)). '</td></tr>';
		}
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small" colspan="4">Suggestions offertes par le joueur :</td>';
		echo '<tr><td style="width:600px; text-align:left;" colspan="4">' .nl2br($lQuest->GetSuggestions()). '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small" colspan="4">Commentaires de l\'approbateur :</td>';
		echo '<tr><td style="width:600px; text-align:left;" colspan="4">' .nl2br($lQuest->GetComments()). '</td></tr>';			

		echo '</table>';
		echo '</form>';

		echo '</form>';
		echo '<hr width=70% />';
		echo '</div>';
	}


	//--DISPLAY SELECTED PERSONAL QUEST'S PARTS--
	public function DisplaySelectedPersonalQuestWriting()
	{
		// Prepare data for the form
		$lQuest = $this->Manager->GetSelectedQuest();

		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Rédaction</span>';

		// Display text
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-quest"/>';

		echo '<span class="note"><b>Note : </b>Afin de ne pas perdre un texte, il est toujours conseillé de l\'écrire d\'abord dans un fichier.</span>';

		echo '<table>';
		echo '<tr><td class="inputname" style="width: 100px;">Description</td>  <td></td></tr>';
		echo '<tr><td class="inputbox" colspan="3">	<textarea name="text" cols="72" rows="16" style="width:600px;" placeholder="Le texte de la quête elle-même...">'.$lQuest->GetText().'</textarea></td></tr>';
		echo '<tr><td colspan="3"><hr style="margin-top: 10px;"/></td></tr>';

		echo '<tr><td class="inputname" style="width: 100px;">Commentaires</td>  <td></td></tr>';
		echo '<tr><td class="inputbox" colspan="3">	<textarea name="comments" cols="72" rows="8" style="width:600px;" placeholder="Commentaires donnés au joueur concernant sa quête.">'.$lQuest->GetComments().'</textarea></td></tr>';

		echo '<tr class="filler"></tr>';
		echo '<tr><td colspan="3">';
			echo '<button type="submit" name="submit" value="Modifier rédaction" class="submit-button" />';
			echo 'Mettre à jour';
			echo '</button>';
		echo '</td></tr>';
		echo '</table>';

		echo '</form>';
		echo '<hr width=70% />';
		echo '</div>';
	
		echo '</div>';
	}


	//--DISPLAY SELECTED QUEST'S CHARACTER--
	public function DisplaySelectedPersonalQuestCharacter()
	{
		// Check if there's a quest manager...
		if( $this->Manager == null ) { $this->Error = "No quest manager defined!"; return; }


		// Prepare data for the form
		$lQuest = $this->Manager->GetSelectedQuest();
		$lCharacter = $lQuest->GetCharacter();
		$lStatus = $lCharacter->GetStatus();
		    if( $lStatus == 'NOUVO' ) 	{ $lStatus = 'Nouveau'; }
		elseif( $lStatus == 'ACTIF' ) 	{ $lStatus = 'Actif'; }
		elseif( $lStatus == 'LEVEL' ) 	{ $lStatus = 'Actuellement joué'; }
		elseif( $lStatus == 'DEPOR' ) 	{ $lStatus = 'Déporté'; }
		elseif( $lStatus == 'RETIR' ) 	{ $lStatus = 'Retraité'; }
		elseif( $lStatus == 'MORT' ) 	{ $lStatus = 'Décédé'; }


		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Personnage</span>';

		// Display data table
		echo '<table>';

		echo '<tr><td class="labelname-small">Personnage :</td> 	<td class="labelvalue" colspan="3">' .$lCharacter->GetFullName(). '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">Race :</td> 		<td class="labelvalue">' . $lCharacter->GetRace() . '</td>
			  <td class="labelname-small">Religion :</td>	 	<td class="labelvalue">' . $lCharacter->GetReligion() . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">Classe :</td> 		<td class="labelvalue">' . $lCharacter->GetClass() . '</td>
			  <td class="labelname-small">Provenance :</td>		<td class="labelvalue">' . $lCharacter->GetOrigin() . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">Niveau :</td>		<td class="labelvalue">' . $lCharacter->GetLevel() . '</td>
			  <td class="labelname-small">Status :</td> 		<td class="labelvalue">' . $lStatus . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">Groupe :</td>		<td class="labelvalue" colspan="3">' . $lCharacter->GetGroup()->GetName() . '</td>';

		echo '</table>';

		echo '<hr width=70% style="margin-bottom:5px;" />';
		echo '</div>';


		// Display background!
		echo '<div>';

		echo '<table>';
		echo '<tr><td class="labelname-small">Histoire du personnage :</td>';
		echo '<tr><td style="width:600px; text-align:left;">' .nl2br( $lCharacter->GetBackground() ). '</td></tr>';
		echo '</table>';

		echo '<hr width=70% style="margin-bottom:5px;" />';
		echo '</div>';


		// Display groupe description!
		echo '<div>';

		echo '<table>';
		echo '<tr><td class="labelname-small">Description du groupe :</td>';
		echo '<tr><td style="width:600px; text-align:left;">' .nl2br( $lCharacter->GetGroup()->GetDescription() ). '</td></tr>';
		echo '</table>';

		echo '<hr width=70% />';
		echo '</div>';

	}


	//--DISPLAY SELECTED QUEST'S RESUMES--
	public function DisplaySelectedPersonalQuestResumes()
	{
		// Prepare data for the form
		$lQuest = $this->Manager->GetSelectedQuest();
		$lResumeList = $this->Manager->GetResumes();


		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Résumés d\'activité</span>';

		echo '<hr width=70% style="margin-bottom:5px;" />';
		echo '</div>';

		echo '</div>';

		// Display!
		echo '<div>';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-quest-lists"/>';

		echo '<table style="text-align: left;">';
		echo '<tr><th class="black-cell" style="width:100px;">Activité</th>
			  <th class="black-cell" style="width:260px;">Quête</th> 
			  <th class="black-cell" style="width:120px;">Création</th>
			  <th class="black-cell" style="width:40px;"></th></tr>';

		foreach($lResumeList as $i => $resume) {

			$lQuest = "";
				if( $resume->GetQuest()->GetID() ) { 
					$lQuest .= $resume->GetQuest()->GetSubject(); 
					# if( $resume->GetQuestPart()->GetID() ) { $lQuest .= " - Partie " . $resume->GetQuestPart()->GetNumber(); }
				}

			$lButton = '<button type="submit" name="select-personal-resume" value="' .$i. '" class="icon-button"/><img src="images/icon_see.png" width="16" heigth="16"></button>';


			echo '<tr>';
			echo '<td class="grey-cell" style="width:100px;">' 	.$resume->GetActivity()->GetName().					'</td>';
			echo '<td class="white-cell" style="width:260px;">' 	.$lQuest.								'</td>';
			echo '<td class="white-cell" style="width:120px;">'	.str_replace(":", "h", substr($resume->GetCreationDate(), 0, 16)).	'</td>';
			echo '<td class="white-cell" style="width:40px;">'	.$lButton.								'</td>';
			echo '</tr>';
		}
		echo '</table>';

		echo '</form>';
		echo '<hr width=70% />';

		echo '</div>';
	}


	//--DISPLAY SELECTED RESUMÉ'S DETAIL--
	public function DisplaySelectedPersonalResume()
	{
		// Prepare data
		$index = $_POST['select-personal-resume'];
		$lResume = $this->Manager->GetResumes()[$index];


		// Display!
		echo '<div>';
		echo '<span class="section-title">Résumé pour '.$lResume->GetActivity()->GetName().'</span>';

		// Resumé
		echo '<div style="margin: auto; margin-top: 10px; margin-bottom: 10px; width: 620px; text-align: left;"><span>' .nl2br( $lResume->GetText() ). '</span></div>';	

		echo '<hr width=70% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Résumés personnelles" class="smalltext-button"/>';
		echo 'Retour';
		echo '</button>';

		echo '</form>';

		echo '</div>';
	}

//=================================================================== GROUP QUESTS UIs ===================================================================

	//--DISPLAY MY ASSIGNED QUESTS--
	public function DisplayMyGroupQuests()
	{
		// Check if there's a quest manager...
		if( $this->Manager == null ) { $this->Error = "No quest manager defined!"; return; }


		// Prepare data for the form
		$lQuestList = $this->Manager->GetGroupQuests();


		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Mes quêtes de groupe</span>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-quest-lists"/>';

		// Display data table
		echo '<table>';

		echo '<tr>
			<th class="black-cell" style="width:20px;">ID</th> 
			<th class="black-cell" style="width:150px;">Groupe</th>
			<th class="black-cell" style="width:180px;">Quête</th>
			<th class="black-cell" style="width:150px;">Comté choisi</th>
			<th class="black-cell" style="width:100px;">État</th>
			<th class="black-cell" style="width:20px;"></th>
		      </tr>';


		foreach( $lQuestList as $i => $quest ) {
			if( !$quest->GetScriptor() ) { /*Do nothing. Quests without scriptors can't be yours.*/ }
			if( $quest->GetScriptor()->GetID() == $_SESSION['player']->GetID() ){
				// Gather quest data
				$lGroupName = $quest->GetGroup()->GetName();

				$lStatus = "Indéterminé";
					    if( $quest->GetStatus() == 'DEM' )   { $lStatus = "Demande"; }
					elseif( $quest->GetStatus() == 'REPR' )  { $lStatus = "Reprise"; }
					elseif( $quest->GetStatus() == 'SUITE' ) { $lStatus = "Suite"; }
					elseif( $quest->GetStatus() == 'ACTIF' ) { $lStatus = "Active - " .$quest->GetProgress()." / ".$quest->GetRequiredParts(); }
					elseif( $quest->GetStatus() == 'TERM'  ) { $lStatus = "Terminée"; }
					elseif( $quest->GetStatus() == 'RECOM' ) { $lStatus = "Terminée (R)"; }
					elseif( $quest->GetStatus() == 'VEROU' ) { $lStatus = "Terminée (V)"; }
					elseif( $quest->GetStatus() == 'ANNUL' ) { $lStatus = "Annulée"; }
					elseif( $quest->GetStatus() == 'REFUS' ) { $lStatus = "Refusée"; }

				$lButton = '<button type="submit" name="select-group-quest" value="' .$quest->GetID(). '" class="icon-button"/><img src="images/icon_see.png" width="16" heigth="16"></button>';

				$lCountyName = $quest->GetCountyName();
					if( $lCountyName == "" ) { $lCountyName = "Aucun"; }
 
				// Display quest entry
				echo '<tr>';
				echo '<td class="grey-cell" style="width:20px;">'	.$quest->GetID().	'</td>'; 
				echo '<td class="white-cell" style="width:180px;">'	.$lGroupName.	'</td>';
				echo '<td class="white-cell" style="width:180px;">'	.$quest->GetSubject().	'</td>';
				echo '<td class="white-cell" style="width:150px;">'	.$lCountyName.		'</td>';
				echo '<td class="white-cell" style="width:100px;">'	.$lStatus.		'</td>';
				echo '<td class="white-cell" style="width:20px;">'	.$lButton.		'</td>';
				echo '</tr>';
			}
		}

		echo '</table>';
		echo '</form>';

		echo '<hr width=70% />';
		echo '</div>';
	}


	//--DISPLAY ALL ACTIVE GROUP QUESTS--
	public function DisplayActiveGroupQuests()
	{
		// Check if there's a quest manager...
		if( $this->Manager == null ) { $this->Error = "No quest manager defined!"; return; }


		// Prepare data for the form
		$lQuestList = $this->Manager->GetGroupQuests();


		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Toutes les quêtes actives</span>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-quest-lists"/>';

		// Display data table
		echo '<table>';

		echo '<tr>
			<th class="black-cell" style="width:20px;">ID</th> 
			<th class="black-cell" style="width:150px;">Groupe</th>
			<th class="black-cell" style="width:180px;">Quête</th>
			<th class="black-cell" style="width:150px;">Comté choisi</th>
			<th class="black-cell" style="width:100px;">Responsable</th>
			<th class="black-cell" style="width:20px;"></th>
		      </tr>';


		foreach( $lQuestList as $i => $quest ) {
			if( $quest->GetStatus() == 'ACTIF' ) {
				// Gather quest data
				$lGroupName = $quest->GetGroup()->GetName();
				$lScriptorName = $quest->GetScriptor()->GetFirstName();

				$lButton = '<button type="submit" name="select-group-quest" value="' .$quest->GetID(). '" class="icon-button"/><img src="images/icon_see.png" width="16" heigth="16"></button>';

				$lCountyName = $quest->GetCountyName();
					if( $lCountyName == "" ) { $lCountyName = "Aucun"; }

				// Display quest entry 
				echo '<tr>';
				echo '<td class="grey-cell" style="width:20px;">'	.$quest->GetID().	'</td>'; 
				echo '<td class="white-cell" style="width:130px;">'	.$lGroupName.		'</td>';
				echo '<td class="white-cell" style="width:180px;">'	.$quest->GetSubject().	'</td>';
				echo '<td class="white-cell" style="width:150px;">'	.$lCountyName.		'</td>';
				echo '<td class="white-cell" style="width:100px;">'	.$lScriptorName.	'</td>';
				echo '<td class="white-cell" style="width:20px;">'	.$lButton.		'</td>';
				echo '</tr>';
			}
		}

		echo '</table>';
		echo '</form>';

		echo '<hr width=70% />';
		echo '</div>';
	}


	//--DISPLAY SELECTED GROUP QUEST--
	public function DisplaySelectedGroupQuest( $inSubUI =NULL )
	{
		// Check if there's a quest manager...
		if( $this->Manager == null ) { $this->Error = "No quest manager defined!"; return; }


		// Prepare data for the form
		$lQuest = $this->Manager->GetSelectedQuest();


		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Quête de groupe #' .$lQuest->GetID(). '</span>';
		echo '<hr width=70% style="margin-bottom:5px;" />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Détail quête" class="smalltext-button" style="margin:0px;"/>';
		echo 'Quête';
		echo '</button>';

		echo '<button type="submit" name="option" value="Rédaction quête de groupe" class="smalltext-button" style="margin:0px;"/>';
		echo 'Rédaction';
		echo '</button>';

		if( $lQuest->GetGroup()->GetResumeCount() ) {
			echo '<button type="submit" name="option" value="Résumés de groupe" class="smalltext-button" style="margin:0px;"/>';
			echo 'Résumés';
			echo '</button>';
		}

		echo '<button type="submit" name="option" value="Groupe" class="smalltext-button" style="margin:0px;"/>';
		echo 'Groupe';
		echo '</button>';

		echo '</form>';

		echo '<hr width=70% />';
		echo '</div>';


		// Display SubUI if not null
		    if( $inSubUI == 'QUEST' ) { $this->DisplaySelectedQuestDetail(); }
		elseif( $inSubUI == 'CHARACTER' ) { $this->DisplaySelectedQuestCharacter('BACKGROUND'); }
			elseif( $inSubUI == 'BACKGROUND' ) { $this->DisplaySelectedQuestCharacter('BACKGROUND'); }
			elseif( $inSubUI == 'SKILLS' ) { $this->DisplaySelectedQuestCharacter('SKILLS'); }
			elseif( $inSubUI == 'TITLES' ) { $this->DisplaySelectedQuestCharacter('TITLES'); }
			elseif( $inSubUI == 'SURVEY' ) { $this->DisplaySelectedQuestCharacter('SURVEY'); }
		elseif( $inSubUI == 'GROUPS' ) { $this->DisplaySelectedQuestGroups(); }
		elseif( $inSubUI == 'WRITING' ) { $this->DisplaySelectedQuestWriting(); }
		elseif( $inSubUI == 'RESUMES' ) { $this->DisplaySelectedQuestResumes(); }
			elseif( $inSubUI == 'ALL RESUMES' ) { $this->DisplaySelectedQuestResumes('ALL'); }
			elseif( $inSubUI == 'QUEST RESUMES' ) { $this->DisplaySelectedQuestResumes('QUEST'); }
	}


	//--DISPLAY CHARACTER'S GROUPS FOR THE SELECTED QUEST--
	public function DisplaySelectedQuestGroups()
	{
		// Prepare data for the form
		$lQuest = $this->Manager->GetSelectedQuest();
		$lNumberOfGroups = $lQuest->GetCharacter()->GetGroupCount();
		$lGroupList = $lQuest->GetCharacter()->GetGroups();


		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Allégeances du personnage</span>';

		// Display groups
		if( $lNumberOfGroups == 0) { echo '<span class="note">Le personnage ne fait partie d\'aucun groupe pour le moment.</span>'; }
		else {
			if( $lNumberOfGroups > 1 ) {
				// Create one button per group
				echo '<div>';
				echo '<hr width=70% style="margin-bottom:5px;" />';

				echo '<form method="post">';
				echo '<input type="hidden" name="action" value="manage-quest-lists"/>';

				foreach ($lGroupList as $i => $group) {
					if( $i && ($i % 3) == 0 ) { echo '<br />';}	// Insert new line each 3 names except the first
				
					echo '<button type="submit" name="select-group" value="' .$i. '" class="groupname-button" style="margin:0px;"/>';
					echo substr($group->GetName(), 0, 30);
					echo '</button>';
				}
			
				echo '</form>';
				echo '</div>';
			}

			echo '<hr width=70% />';

			// Display selected group
			if( !isset($_POST['select-group']) ) { $_POST['select-group'] = 0; }
			$lDisplayedGroup = $lGroupList[ $_POST['select-group'] ];

			echo '<div>';
			echo '<span class="section-title">' .$lDisplayedGroup->GetName(). '</span>';

			echo '<table>';
			echo '<tr><td class="labelname-small">Description :</td>';
			echo '<tr><td style="width:600px; heigth:400px; overflow:auto; text-align:left;">' .nl2br($lDisplayedGroup->GetDescription()). '</td></tr>';
			echo '<tr class="filler"></tr>';
			echo '<tr class="filler"></tr>';
			echo '<tr><td class="labelname-small">Valeur #1 : « ' .$lDisplayedGroup->GetValues()[0]['title']. ' »</td>';
			echo '<tr><td style="width:600px; heigth:250px; overflow:auto; text-align:left;">' .nl2br($lDisplayedGroup->GetValues()[0]['description']). '</td></tr>';
			echo '<tr class="filler"></tr>';
			echo '<tr class="filler"></tr>';
			echo '<tr><td class="labelname-small">Valeur #2 : « ' .$lDisplayedGroup->GetValues()[1]['title']. ' »</td>';
			echo '<tr><td style="width:600px; heigth:250px; overflow:auto; text-align:left;">' .nl2br($lDisplayedGroup->GetValues()[1]['description']). '</td></tr>';
			echo '<tr class="filler"></tr>';
			echo '<tr class="filler"></tr>';
			echo '<tr><td class="labelname-small">Valeur #3 : « ' .$lDisplayedGroup->GetValues()[2]['title']. ' »</td>';
			echo '<tr><td style="width:600px; heigth:250px; overflow:auto; text-align:left;">' .nl2br($lDisplayedGroup->GetValues()[2]['description']). '</td></tr>';
			echo '<tr class="filler"></tr>';
			echo '<tr class="filler"></tr>';
			echo '<tr><td class="labelname-small">Hiérarchie</td>';
			echo '<tr><td style="width:600px; heigth:300px; overflow:auto; text-align:left;">' .nl2br($lDisplayedGroup->GetLeadership()). '</td></tr>';
			echo '<tr class="filler"></tr>';
			echo '<tr class="filler"></tr>';
			echo '<tr><td class="labelname-small">Historique</td>';
			echo '<tr><td style="width:600px; heigth:400px; overflow:auto; text-align:left;">' .nl2br($lDisplayedGroup->GetBackground()). '</td></tr>';
			echo '</table>';

			echo '<hr width=70% />';

			echo '</div>';
		}

		echo '</div>';
	}

//=================================================================== MANAGEMENT UIs ===================================================================

	//--DISPLAY REQUESTS FOR ALL QUEST TYPES--
	public function DisplayFilteredRequests($inStatusCode)
	{
		// Check if there's a quest manager...
		if( $this->Manager == null ) { $this->Error = "No quest manager defined!"; return; }


		// Prepare data for the form
		$lPersonalQuestList = $this->Manager->GetPersonalQuests();
		$lGroupQuestList = $this->Manager->GetGroupQuests();

		$lFilteredStatus = "Indéterminés";
			    if( $inStatusCode == 'DEM' )  { $lFilteredStatus = "Demandes"; }
			elseif( $inStatusCode == 'REPR')  { $lFilteredStatus = "Reprises"; }
			elseif( $inStatusCode == 'SUITE') { $lFilteredStatus = "Suites"; }


		// PERSONAL REQUESTS	
		// Display the title.
		echo '<div>';
		echo '<span class="section-title">'.$lFilteredStatus.' de quête personnelle</span>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-quest-lists"/>';

		// Display data table
		echo '<table>';

		echo '<tr>
			<th class="black-cell" style="width:20px;">ID</th> 
			<th class="black-cell" style="width:150px;">Personnage</th>
			<th class="black-cell" style="width:150px;">Quête</th>
			<th class="black-cell" style="width:250px;">Comté choisi</th>
			<th class="black-cell" style="width:120px;">Demandée le</th>
			<th class="black-cell" style="width:20px;"></th>
		      </tr>';


		foreach( $lPersonalQuestList as $quest ) {
			if( $quest->GetStatus() == $inStatusCode) {
				$lCharacterName = $quest->GetCharacter()->GetFirstName();
				$lRequestDate = str_replace(":", "h", substr($quest->GetRequestDate(), 0, 16));
				$lButton = '<button type="submit" name="manage-personal-quest" value="' .$quest->GetID(). '" class="icon-button"/><img src="images/icon_see.png" width="16" heigth="16"></button>';
				$lCountyName = $quest->GetCountyName();
					if( $lCountyName == "" ) { $lCountyName = "Aucun"; }
								
				echo '<tr>';
				echo '<td class="grey-cell" style="width:20px;">'	.$quest->GetID().	'</td>'; 
				echo '<td class="white-cell" style="width:180px;">'	.$lCharacterName.	'</td>';
				echo '<td class="white-cell" style="width:180px;">'	.$quest->GetSubject().	'</td>';
				echo '<td class="white-cell" style="width:150px;">'	.$lCountyName.		'</td>';
				echo '<td class="white-cell" style="width:120px;">'	.$lRequestDate.	'</td>';
				echo '<td class="white-cell" style="width:20px;">'	.$lButton.	'</td>';
				echo '</tr>';
			}
		}

		echo '</table>';
		echo '</form>';

		echo '<hr width=70% />';

		// GROUP REQUESTS	
		// Display the title.
		echo '<span class="section-title">'.$lFilteredStatus.' de quête de groupe</span>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-quest-lists"/>';

		// Display data table
		echo '<table>';

		echo '<tr>
			<th class="black-cell" style="width:20px;">ID</th> 
			<th class="black-cell" style="width:150px;">Groupe</th>
			<th class="black-cell" style="width:150px;">Quête</th>
			<th class="black-cell" style="width:250px;">Comté choisi</th>
			<th class="black-cell" style="width:120px;">Demandée le</th>
			<th class="black-cell" style="width:20px;"></th>
		      </tr>';


		foreach( $lGroupQuestList as $quest ) {
			if( $quest->GetStatus() == $inStatusCode) {
				$lGroupName = $quest->GetGroup()->GetName();
				$lRequestDate = str_replace(":", "h", substr($quest->GetRequestDate(), 0, 16));
				$lButton = '<button type="submit" name="manage-group-quest" value="' .$quest->GetID(). '" class="icon-button"/><img src="images/icon_see.png" width="16" heigth="16"></button>';
				$lCountyName = $quest->GetCountyName();
					if( $lCountyName == "" ) { $lCountyName = "Aucun"; }
								
				echo '<tr>';
				echo '<td class="grey-cell" style="width:20px;">'	.$quest->GetID().	'</td>'; 
				echo '<td class="white-cell" style="width:180px;">'	.$lGroupName.	'</td>';
				echo '<td class="white-cell" style="width:180px;">'	.$quest->GetSubject().	'</td>';
				echo '<td class="white-cell" style="width:150px;">'	.$lCountyName.		'</td>';
				echo '<td class="white-cell" style="width:120px;">'	.$lRequestDate.	'</td>';
				echo '<td class="white-cell" style="width:20px;">'	.$lButton.	'</td>';
				echo '</tr>';
			}
		}

		echo '</table>';
		echo '</form>';

		echo '<hr width=70% />';
		echo '</div>';
	}


	//--DISPLAY ALL ACTIVE QUESTS--
	public function DisplayActiveQuests()
	{
		// Check if there's a quest manager...
		if( $this->Manager == null ) { $this->Error = "No quest manager defined!"; return; }


		// Prepare data for the form
		$lPersonalQuestList = $this->Manager->GetPersonalQuests();
		$lGroupQuestList = $this->Manager->GetGroupQuests();


		// PERSONAL QUESTS
		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Quêtes personnelles en cours</span>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-quest-lists"/>';

		// Display data table
		echo '<table>';

		echo '<tr>
			<th class="black-cell" style="width:32px;">ID</th> 
			<th class="black-cell" style="width:150px;">Personnage</th>
			<th class="black-cell" style="width:180px;">Quête</th>
			<th class="black-cell" style="width:150px;">Comté choisi</th>
			<th class="black-cell" style="width:100px;">Responsable</th>
			<th class="black-cell" style="width:20px;"></th>
		      </tr>';


		foreach( $lPersonalQuestList as $quest ) {
			if( $quest->GetStatus() == 'ACTIF' ) {
				$lCharacterName = $quest->GetCharacter()->GetFirstName();
				$lScriptorName = $quest->GetScriptor()->GetFirstName();
				$lButton = '<button type="submit" name="manage-personal-quest" value="' .$quest->GetID(). '" class="icon-button"/><img src="images/icon_see.png" width="16" heigth="16"></button>';
				$lCountyName = $quest->GetCountyName();
					if( $lCountyName == "" ) { $lCountyName = "Aucun"; }

				echo '<tr>';
				echo '<td class="grey-cell" style="width:32px;">'	.$quest->GetID().	'</td>'; 
				echo '<td class="white-cell" style="width:150px;">'	.$lCharacterName.	'</td>';
				echo '<td class="white-cell" style="width:180px;">'	.$quest->GetSubject().	'</td>';
				echo '<td class="white-cell" style="width:150px;">'	.$lCountyName.		'</td>';
				echo '<td class="white-cell" style="width:100px;">'	.$lScriptorName.	'</td>';
				echo '<td class="white-cell" style="width:20px;">'	.$lButton.		'</td>';
				echo '</tr>';
			}
		}

		echo '</table>';
		echo '</form>';

		echo '<hr width=70% />';

		// GROUP QUESTS	
		// Display the title.
		echo '<span class="section-title">Quêtes de groupe en cours</span>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-quest-lists"/>';

		// Display data table
		echo '<table>';

		echo '<tr>
			<th class="black-cell" style="width:32px;">ID</th> 
			<th class="black-cell" style="width:150px;">Groupe</th>
			<th class="black-cell" style="width:180px;">Quête</th>
			<th class="black-cell" style="width:150px;">Comté choisi</th>
			<th class="black-cell" style="width:100px;">Responsable</th>
			<th class="black-cell" style="width:20px;"></th>
		      </tr>';


		foreach( $lGroupQuestList as $quest ) {
			if( $quest->GetStatus() == 'ACTIF') {
				$lGroupName = $quest->GetGroup()->GetName();
				$lScriptorName = $quest->GetScriptor()->GetFirstName();
				$lButton = '<button type="submit" name="manage-group-quest" value="' .$quest->GetID(). '" class="icon-button"/><img src="images/icon_see.png" width="16" heigth="16"></button>';
				$lCountyName = $quest->GetCountyName();
					if( $lCountyName == "" ) { $lCountyName = "Aucun"; }
								
				echo '<tr>';
				echo '<td class="grey-cell" style="width:32px;">'	.$quest->GetID().	'</td>'; 
				echo '<td class="white-cell" style="width:150px;">'	.$lGroupName.	'</td>';
				echo '<td class="white-cell" style="width:180px;">'	.$quest->GetSubject().	'</td>';
				echo '<td class="white-cell" style="width:150px;">'	.$lCountyName.		'</td>';
				echo '<td class="white-cell" style="width:100px;">'	.$lScriptorName.	'</td>';
				echo '<td class="white-cell" style="width:20px;">'	.$lButton.	'</td>';
				echo '</tr>';
			}
		}

		echo '</table>';
		echo '</form>';

		echo '<hr width=70% />';
		echo '</div>';
	}


	//--DISPLAY SELECTED PERSONAL QUEST--
	public function DisplayManagedPersonalQuest( $inSubUI =NULL )
	{
		// Check if there's a quest manager...
		if( $this->Manager == null ) { $this->Error = "No quest manager defined!"; return; }


		// Prepare data for the form
		$lQuest = $this->Manager->GetSelectedQuest();


		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Quête personnelle #' .$lQuest->GetID(). '</span>';
		echo '<hr width=70% style="margin-bottom:5px;" />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Gérer quête personnelle" class="smalltext-button" style="margin:0px;"/>';
		echo 'Quête';
		echo '</button>';

		echo '<button type="submit" name="option" value="Gérer rédaction quête personnelle" class="smalltext-button" style="margin:0px;"/>';
		echo 'Rédaction';
		echo '</button>';

		echo '<button type="submit" name="option" value="Résumés personnels Responsable" class="smalltext-button" style="margin:0px;"/>';
		echo 'Résumés';
		echo '</button>';

		echo '<button type="submit" name="option" value="Personnage Responsable" class="smalltext-button" style="margin:0px;"/>';
		echo 'Personnage';
		echo '</button>';

		echo '</form>';

		echo '<hr width=70% />';
		echo '</div>';


		// Display SubUI if not null
		    if( $inSubUI == 'QUEST' ) { $this->DisplayManagedPersonalQuestDetail(); }
		elseif( $inSubUI == 'WRITING' ) { $this->DisplayManagedPersonalQuestWriting(); }
		elseif( $inSubUI == 'CHARACTER' ) { $this->DisplayManagedPersonalQuestCharacter('BACKGROUND'); }
			elseif( $inSubUI == 'CHARACTER BACKGROUND' ) { $this->DisplayManagedPersonalQuestCharacter('BACKGROUND'); }
			elseif( $inSubUI == 'CHARACTER SKILLS' ) { $this->DisplayManagedPersonalQuestCharacter('SKILLS'); }
		elseif( $inSubUI == 'RESUMES' ) { $this->DisplayManagedPersonalQuestResumes(); }
			elseif( $inSubUI == 'RESUME' ) { $this->DisplayManagedPersonalResume(); }
	}


	//--DISPLAY SELECTED PERSONAL QUEST'S DETAIL FOR MANAGERS--
	public function DisplayManagedPersonalQuestDetail()
	{
		// Prepare data for the form
		$lQuest = $this->Manager->GetSelectedQuest();
		$lScriptorList = $this->Manager->GetScriptors();
		$lStatusList = array(	0 => [ 'code' => 'DEM', 'name' => 'Demande'],
					1 => [ 'code' => 'REPR', 'name' => 'Reprise'],
					2 => [ 'code' => 'SUITE', 'name' => 'Suite'],
					3 => [ 'code' => 'ACTIF', 'name' => 'Active'],
					4 => [ 'code' => 'TERM', 'name' => 'Terminée'],
					5 => [ 'code' => 'ANNUL', 'name' => 'Annulée'],
					6 => [ 'code' => 'REFUS', 'name' => 'Refusée']
				);

		$lCurrentScriptor = $lQuest->GetScriptor();
			if(!$lCurrentScriptor) { $lCurrentScriptor = new User(); }

		$lScriptorInput = '<select name="scriptor"><option value="0"></option>';
		foreach($lScriptorList as $scriptor) {
			$title = 'scripteur'; 
				if( $scriptor->IsAdmin() ) { $title = 'organisateur'; }
				elseif( $scriptor->IsManager() ) { $title = 'responsable'; }
			$selected = ''; if( $scriptor->GetID() == $lCurrentScriptor->GetID() ) { $selected = 'selected'; }
			$lScriptorInput .= '<option value="'. $scriptor->GetID() .'" '.$selected.'>'. $scriptor->GetFullName() .' ('.$title.')</option>';
		}
		$lScriptorInput .= '</select>';

		$lStatusInput = '<select name="status">';
		foreach($lStatusList as $status) {
			$selected = ''; if( $status['code'] == $lQuest->GetStatus() ) { $selected = 'selected'; }
			$lStatusInput .= '<option value="' .$status['code']. '" '.$selected.'>' .$status['name']. '</option>';
		}
		$lStatusInput .= '</select>';

		$lButtons = '';

		
		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Détail</span>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-quest"/>';

		// Display data table
		echo '<table>';

		echo '<tr><td class="labelname-small">Quête :</td> 		<td class="labelvalue" colspan="3">' .$lQuest->GetSubject(). '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">Comté choisi :</td> 	<td class="labelvalue" colspan="3">' .$lQuest->GetCountyName(). '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">Demandée par :</td> 	<td class="labelvalue" colspan="3">' .$lQuest->GetApplicant()->GetFullName(). '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">Courriel :</td> 		<td class="labelvalue" colspan="3">' .$lQuest->GetApplicant()->GetMailAddress(). '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">Responsable :</td> 	<td colspan="2" class="inputbox">' .$lScriptorInput. '</td><td></td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">État :</td>		<td class="inputbox">' .$lStatusInput. '</td>
			  <td class="labelname-small">Demandée le :</td>	<td class="labelvalue">' .str_replace(":", "h", substr($lQuest->GetRequestDate(), 0, 16)). '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">Pour quel GN :</td>	<td class="labelvalue">' .$lQuest->GetActivity()->GetName(). '</td>
			  <td class="labelname-small">Approuvée le :</td> 	<td class="labelvalue">' .str_replace(":", "h", substr($lQuest->GetApprovalDate(), 0, 16)). '</td></tr>';

		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small" colspan="4">Suggestions offertes :</td>';
		echo '<tr><td style="width:600px; text-align:left;" colspan="4">' .nl2br($lQuest->GetSuggestions()). '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small" colspan="4">Commentaires de l\'approbateur :</td>';
		echo '<tr><td style="width:600px; text-align:left;" colspan="4">' .nl2br($lQuest->GetComments()). '</td></tr>';			
		echo '<tr class="filler"></tr>';
		echo '<tr><td colspan="4"><button type="submit" name="submit" value="Modifier quête" class="smalltext-button" style="margin-bottom:5px;"/>';
			echo 'Mettre à jour';
		echo '</button><br /></td></tr>';

		echo '</table>';

		echo '</form>';
		echo '<hr width=70% />';
		echo '</div>';
	}


	//--DISPLAY SELECTED PERSONAL QUEST'S PARTS--
	public function DisplayManagedPersonalQuestWriting()
	{
		// Prepare data for the form
		$lQuest = $this->Manager->GetSelectedQuest();


		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Rédaction</span>';

		// Display selected part
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-quest"/>';

		echo '<span class="note"><b>Note : </b>Afin de ne pas perdre un texte, il est toujours conseillé de l\'écrire d\'abord dans un fichier.</span>';

		echo '<table>';
		echo '<tr><td class="inputname" style="width: 100px;">Missive</td>  <td></td></tr>';
		echo '<tr><td class="inputbox" colspan="3">	<textarea name="text" cols="72" rows="16" style="width:600px;" placeholder="Le texte de la quête elle-même...">'.$lQuest->GetText().'</textarea></td></tr>';

		echo '<tr><td colspan="3"><hr style="margin-top: 10px;"/></td></tr>';

		echo '<tr><td class="inputname" style="width: 100px;">Commentaires</td>  <td></td></tr>';
		echo '<tr><td class="inputbox" colspan="3">	<textarea name="comments" cols="72" rows="8" style="width:600px;" placeholder="Commentaires donnés au joueur concernant sa quête.">'.$lQuest->GetComments().'</textarea></td></tr>';

		echo '<tr class="filler"></tr>';
		echo '<tr><td colspan="3">';
			echo '<button type="submit" name="submit" value="Modifier rédaction" class="submit-button" />';
			echo 'Mettre à jour';
			echo '</button>';
		echo '</td></tr>';
		echo '</table>';

		echo '</form>';
		echo '<hr width=70% />';
		echo '</div>';
		
		echo '</div>';
	}


	//--DISPLAY SELECTED QUEST'S RESUMES--
	public function DisplayManagedPersonalQuestResumes()
	{
		// Prepare data for the form
		$lQuest = $this->Manager->GetSelectedQuest();
		$lResumeList = $this->Manager->GetResumes();


		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Résumés d\'activité</span>';

		echo '<hr width=70% style="margin-bottom:5px;" />';
		echo '</div>';

		echo '</div>';

		// Display!
		echo '<div>';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-quest-lists"/>';

		echo '<table style="text-align: left;">';
		echo '<tr><th class="black-cell" style="width:100px;">Activité</th>
			  <th class="black-cell" style="width:260px;">Quête</th> 
			  <th class="black-cell" style="width:120px;">Création</th>
			  <th class="black-cell" style="width:40px;"></th></tr>';

		foreach($lResumeList as $i => $resume) {

			$lQuest = "";
				if( $resume->GetQuest()->GetID() ) { 
					$lQuest .= $resume->GetQuest()->GetSubject(); 
					# if( $resume->GetQuestPart()->GetID() ) { $lQuest .= " - Partie " . $resume->GetQuestPart()->GetNumber(); }
				}

			$lButton = '<button type="submit" name="select-personal-resume" value="' .$i. '" class="icon-button"/><img src="images/icon_see.png" width="16" heigth="16"></button>';


			echo '<tr>';
			echo '<td class="grey-cell" style="width:100px;">' 	.$resume->GetActivity()->GetName().					'</td>';
			echo '<td class="white-cell" style="width:260px;">' 	.$lQuest.								'</td>';
			echo '<td class="white-cell" style="width:120px;">'	.str_replace(":", "h", substr($resume->GetCreationDate(), 0, 16)).	'</td>';
			echo '<td class="white-cell" style="width:40px;">'	.$lButton.								'</td>';
			echo '</tr>';
		}
		echo '</table>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY SELECTED RESUMÉ'S DETAIL--
	public function DisplayManagedPersonalResume()
	{
		// Prepare data
		$index = $_POST['select-personal-resume'];
		$lResume = $this->Manager->GetResumes()[$index];


		// Display!
		echo '<div>';
		echo '<span class="section-title">Résumé pour '.$lResume->GetActivity()->GetName().'</span>';

		// Resumé
		echo '<div style="margin: auto; margin-top: 10px; margin-bottom: 10px; width: 620px; text-align: left;"><span>' .nl2br( $lResume->GetText() ). '</span></div>';	

		echo '<hr width=70% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Résumés personnels Responsable" class="smalltext-button"/>';
		echo 'Retour';
		echo '</button>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY SELECTED QUEST'S CHARACTER--
	public function DisplayManagedPersonalQuestCharacter( $inSubUI =NULL )
	{
		// Check if there's a quest manager...
		if( $this->Manager == null ) { $this->Error = "No quest manager defined!"; return; }


		// Prepare data for the form
		$lQuest = $this->Manager->GetSelectedQuest();
		$lCharacter = $this->Manager->GetCharacter();
		$lStatus = $lCharacter->GetStatus();
		    if( $lStatus == 'NOUVO' ) 	{ $lStatus = 'Nouveau'; }
		elseif( $lStatus == 'ACTIF' ) 	{ $lStatus = 'Actif'; }
		elseif( $lStatus == 'LEVEL' ) 	{ $lStatus = 'Actuellement joué'; }
		elseif( $lStatus == 'DEPOR' ) 	{ $lStatus = 'Déporté'; }
		elseif( $lStatus == 'RETIR' ) 	{ $lStatus = 'Retraité'; }
		elseif( $lStatus == 'MORT' ) 	{ $lStatus = 'Décédé'; }


		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Personnage</span>';

		// Display data table
		echo '<table>';

		echo '<tr><td class="labelname-small">Personnage :</td> 	<td class="labelvalue" colspan="3">' .$lCharacter->GetFullName(). '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">Race :</td> 		<td class="labelvalue">' . $lCharacter->GetRace() . '</td>
			  <td class="labelname-small">Religion :</td>	 	<td class="labelvalue">' . $lCharacter->GetReligion() . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">Classe :</td> 		<td class="labelvalue">' . $lCharacter->GetClass() . '</td>
			  <td class="labelname-small">Provenance :</td>		<td class="labelvalue">' . $lCharacter->GetOrigin() . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">Niveau :</td>		<td class="labelvalue">' . $lCharacter->GetLevel() . '</td>
			  <td class="labelname-small">Status :</td> 		<td class="labelvalue">' . $lStatus . '</td></tr>';

		echo '</table>';

		echo '<hr width=70% style="margin-bottom:5px;" />';
		echo '</div>';


		// Submenu
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Histoire personnage" class="smalltext-button" style="margin:0px;"/>';
		echo 'Histoire';
		echo '</button>';

		echo '<button type="submit" name="option" value="Compétences personnage" class="smalltext-button" style="margin:0px;"/>';
		echo 'Compétences';
		echo '</button>';

		echo '</form>';
		echo '<hr width=70% />';
		echo '</div>';


		// Display SubUI if not null
		    if( $inSubUI == 'BACKGROUND' ) { $this->DisplayCharacterBackground(); }
		elseif( $inSubUI == 'SKILLS' ) { $this->DisplayCharacterSkills(); }
	}


	//--DISPLAY CHARACTER'S STORY--
	public function DisplayCharacterBackground()
	{
		// Prepare data
		$lQuest = $this->Manager->GetSelectedQuest();
		$lCharacter = $lQuest->GetCharacter();

		// Display!
		echo '<div>';

		echo '<table>';
		echo '<tr><td class="labelname-small">Histoire du personnage :</td>';
		echo '<tr><td style="width:600px; text-align:left;">' .nl2br( $lCharacter->GetBackground() ). '</td></tr>';
		echo '</table>';

		echo '<hr width=70% />';
		echo '</div>';
	}


	//--DISPLAY CHARACTER'S SKILLS--
	public function DisplayCharacterSkills()
	{
		// Prepare data for the form
		$lQuest = $this->Manager->GetSelectedQuest();
		$lCharacter = $lQuest->GetCharacter();
		$lSkillList = $lCharacter->GetMergedSkills();
			if( !$lSkillList ) { $lSkillList['name'] = array(); }
		$lTalentList = $lCharacter->GetTalents();
			if( !$lTalentList ) { $lTalentList['name'] = array(); }


		// Display!
		echo '<div>';

		echo '<table style="text-align: left;">';
		echo   '<tr>
			<th class="black-cell" style="width:400px;">Compétence</th>
			<th class="black-cell" style="width:25px; font-size: 0.8em;">UEC*</th>
			<th class="black-cell" style="width:80px;">Type</th>
			</tr>';

		foreach($lSkillList as $skill) {
			echo '<tr>';
			echo '<td class="white-cell" style="width:400px;">' 			.$skill['name'].		'</td>';
			echo '<td class="white-cell" style="width:25px; text-align:center;">'	.$skill['quantity'].		'</td>';
			echo '<td class="white-cell" style="width:80px;">			Régulière			 </td>';
			echo '</tr>';
		}
		foreach($lTalentList as $talent) {
			$type = 'Inconnue'; 
				    if( $talent['type'] == 'MINEURE' ) { $type = 'Mineure'; }
				elseif( $talent['type'] == 'MAJEURE' ) { $type = 'Majeure'; }
				elseif( $talent['type'] == 'SPECIAL' ) { $type = 'Spécial'; }
				elseif( $talent['type'] == 'LEGEND' )  { $type = 'Légendaire'; }
				elseif( $talent['type'] == 'RACIALE' ) { $type = 'Raciale'; }

			echo '<tr>';
			echo '<td class="white-cell" style="width:400px;">'	.$talent['name'].	'</td>';
			echo '<td class="white-cell" style="width:25px;">			 	</td>';
			echo '<td class="white-cell" style="width:80px;">'	.$type.			'</td>';
			echo '</tr>';
		}
		echo '<tr><td colspan="3" style="font-size: 0.8em;"><i><b>* UEC :</b> Usages / Éléments / Chads</i></td></tr>';
		echo '</table>';

		echo '<hr width=70% />';
		echo '</div>';
	}


	//--DISPLAY SEARCH ENGINE AND RESULTS FOR QUESTS--
	public function DisplayPersonalQuestsSearch()
	{
		// Check if there's a quest manager...
		if( $this->Manager == null ) { $this->Error = "No quest manager defined!"; return; }


		// Prepare data for the form
		$lPersonalQuestList = $this->Manager->GetPersonalQuests();

		$lStatusInput = '<select name="status">';
			$lStatusInput .= '<option value="%%">Tous</option>';
			$lStatusInput .= '<option value="ANNUL">Annulée</option>';
			$lStatusInput .= '<option value="REFUS">Refusée</option>';
			$lStatusInput .= '<option value="TERM">Terminée</option>';
			$lStatusInput .= '<option value="RECOM">Récompensée</option>';
		$lStatusInput .= '</select>';

		$lScriptorList = $this->Manager->GetScriptors();
		$lScriptorInput = '<select name="scriptor">';
			$lScriptorInput .= '<option value="0"></option>';
			foreach($lScriptorList as $scriptor) {
				$title = 'scripteur'; 
					    if( $scriptor->IsDBA() ) { $title = 'administrateur'; }
					elseif( $scriptor->IsAdmin() ) { $title = 'organisateur'; }
					elseif( $scriptor->IsManager() ) { $title = 'responsable'; }

				$lScriptorInput .= '<option value="'. $scriptor->GetID() .'">'. $scriptor->GetFullName() .' ('.$title.')</option>';
			}
		$lScriptorInput .= '</select>';

		// PERSONAL QUESTS
		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Critères de recherche</span>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="search"/>';

		// Display search inputs
		echo '<table>';
		echo '<tr><td class="inputname">Nom de la cible</td>
								<td class="inputbox"><input name="targetname" type="text" value="" maxlength="50"/></td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Objet de la quête</td>
								<td class="inputbox"><input name="questsubject" type="text" value="" maxlength="50"/></td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">État de la quête</td>
								<td class="inputbox">' .$lStatusInput. '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Responsable</td>
								<td class="inputbox">' .$lScriptorInput. '</td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td colspan="2">';
			echo '<button type="submit" name="submit" value="Quêtes" class="submit-button" />';
			echo 'Rechercher';
			echo '</button>';
		echo '</td></tr>';
		echo '</table>';
		echo '</form>';

		echo '<hr width=70% />';

		// FOUND QUESTS	
		// Display the title.
		echo '<span class="section-title">Résultat de la recherche</span>';
		echo '<hr width=70% />';

		if( isset($_POST['search_results']) ) {
			$lPersonalQuestList = $_POST['search_results'];


			echo '<form method="post">';
			echo '<input type="hidden" name="action" value="manage-quest-lists"/>';

			// Display data table
			echo '<table>';
			echo '<tr>
				<th class="black-cell" style="width:20px;">ID</th> 
				<th class="black-cell" style="width:150px;">Cible</th>
				<th class="black-cell" style="width:150px;">Quête</th>
				<th class="black-cell" style="width:50px;">État</th>
				<th class="black-cell" style="width:125px;">Demandée le</th>
				<th class="black-cell" style="width:20px;"></th>
			      </tr>';


			foreach( $lPersonalQuestList as $quest ) {
				$lRequestDate = str_replace(":", "h", substr($quest['requestdate'], 0, 16));
				$lButton = '<button type="submit" name="manage-personal-quest" value="' .$quest['id']. '" class="icon-button"/><img src="images/icon_see.png" width="16" heigth="16"></button>';
									
				echo '<tr>';
				echo '<td class="grey-cell" style="width:20px;">'	.$quest['id'].		'</td>'; 
				echo '<td class="white-cell" style="width:150px;">'	.$quest['target'].	'</td>';
				echo '<td class="white-cell" style="width:150px;">'	.$quest['subject'].	'</td>';
				echo '<td class="white-cell" style="width:50px;">'	.$quest['status'].	'</td>';
				echo '<td class="white-cell" style="width:125px;">'	.$lRequestDate.		'</td>';
				echo '<td class="white-cell" style="width:20px;">'	.$lButton.		'</td>';
				echo '</tr>';
			}

			echo '</table>';
			echo '</form>';
		}

		echo '<hr width=70% />';
		echo '</div>';		
	}


	//--DISPLAY OWED REWARDS--
	public function DisplayOwedRewards()
	{
		// Check if there's a quest manager...
		if( $this->Manager == null ) { $this->Error = "No quest manager defined!"; return; }


		// Prepare data for the form
		$lQuestList = $this->Manager->GetPersonalQuests();


		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Récompenses dues</span>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-quest-lists"/>';

		// Display data table
		echo '<table>';

		echo '<tr>
			<th class="black-cell" style="width:20px;">ID</th> 
			<th class="black-cell" style="width:200px;">Personnage</th>
			<th class="black-cell" style="width:200px;">Quête</th>
			<th class="black-cell" style="width:80px;"></th>
		      </tr>';


		foreach( $lQuestList as $quest ) {
			if( $quest->GetStatus() == 'TERM' ) {
				$lCharacterName = $quest->GetCharacter()->GetFirstName();
				$lButtons = '<button type="submit" name="give-personal-reward" value="' .$quest->GetID(). '" class="icon-button"/><img src="images/icon_accept.png" width="16" heigth="16"></button>';

				if( $quest->GetOptionCode() == 'GAINXP' ) { 
					$lButtons = '<select name="rewardstage" style="width:60px; border: 1px solid black; margin-bottom:3px">
					<option value="3" selected>30 XP</option>
					<option value="2">20 XP</option>
					<option value="1">10 XP</option>
					</select> ' . $lButtons;
				}

				echo '<tr>';
				echo '<td class="grey-cell" style="width:20px;">'	.$quest->GetID().	'</td>'; 
				echo '<td class="white-cell" style="width:200px;">'	.$lCharacterName.	'</td>';
				echo '<td class="white-cell" style="width:200px;">'	.$quest->GetSubject().	'</td>';
				echo '<td class="white-cell" style="width:80px;">'	.$lButtons.		'</td>';
				echo '</tr>';
			}
		}

		echo '</table>';
		echo '</form>';

		echo '<hr width=70% />';
		echo '</div>';
	}


	//--DISPLAY SELECTED GROUP QUEST--
	public function DisplayManagedGroupQuest( $inSubUI =NULL )
	{
		// Check if there's a quest manager...
		if( $this->Manager == null ) { $this->Error = "No quest manager defined!"; return; }


		// Prepare data for the form
		$lQuest = $this->Manager->GetSelectedQuest();


		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Quête de groupe #' .$lQuest->GetID(). '</span>';
		echo '<hr width=70% style="margin-bottom:5px;" />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Gérer quête de groupe" class="smalltext-button" style="margin:0px;"/>';
		echo 'Quête';
		echo '</button>';

		echo '<button type="submit" name="option" value="Gérer rédaction quête de groupe" class="smalltext-button" style="margin:0px;"/>';
		echo 'Rédaction';
		echo '</button>';

		echo '<button type="submit" name="option" value="Résumés de groupe Responsable" class="smalltext-button" style="margin:0px;"/>';
		echo 'Résumés';
		echo '</button>';

		echo '<button type="submit" name="option" value="Groupe Responsable" class="smalltext-button" style="margin:0px;"/>';
		echo 'Groupe';
		echo '</button>';

		echo '</form>';

		echo '<hr width=70% />';
		echo '</div>';


		// Display SubUI if not null
		    if( $inSubUI == 'QUEST' ) { $this->DisplayManagedGroupQuestDetail(); }
		elseif( $inSubUI == 'WRITING' ) { $this->DisplayManagedGroupQuestWriting(); }
		elseif( $inSubUI == 'GROUP' ) { $this->DisplayManagedGroupQuestGroup(); }
		elseif( $inSubUI == 'RESUMES' ) { $this->DisplayManagedGroupQuestResumes(); }
			elseif( $inSubUI == 'RESUME' ) { $this->DisplayManagedGroupResume(); }
	}


	//--DISPLAY SELECTED GROUP QUEST'S DETAIL FOR MANAGERS--
	public function DisplayManagedGroupQuestDetail()
	{
		// Prepare data for the form
		$lQuest = $this->Manager->GetSelectedQuest();
		$lScriptorList = $this->Manager->GetScriptors();
		$lStatusList = array(	0 => [ 'code' => 'DEM', 'name' => 'Demande'],
					1 => [ 'code' => 'REPR', 'name' => 'Reprise'],
					2 => [ 'code' => 'SUITE', 'name' => 'Suite'],
					3 => [ 'code' => 'ACTIF', 'name' => 'Active'],
					4 => [ 'code' => 'TERM', 'name' => 'Terminée'],
					5 => [ 'code' => 'ANNUL', 'name' => 'Annulée'],
					6 => [ 'code' => 'REFUS', 'name' => 'Refusée']
				);

		$lCurrentScriptor = $lQuest->GetScriptor();
			if(!$lCurrentScriptor) { $lCurrentScriptor = new User(); }

		$lScriptorInput = '<select name="scriptor"><option value="0"></option>';
		foreach($lScriptorList as $scriptor) {
			$title = 'scripteur'; 
				if( $scriptor->IsAdmin() ) { $title = 'organisateur'; }
				elseif( $scriptor->IsManager() ) { $title = 'responsable'; }
			$selected = ''; if( $scriptor->GetID() == $lCurrentScriptor->GetID() ) { $selected = 'selected'; }
			$lScriptorInput .= '<option value="'. $scriptor->GetID() .'" '.$selected.'>'. $scriptor->GetFullName() .' ('.$title.')</option>';
		}
		$lScriptorInput .= '</select>';

		$lStatusInput = '<select name="status">';
		foreach($lStatusList as $status) {
			$selected = ''; if( $status['code'] == $lQuest->GetStatus() ) { $selected = 'selected'; }
			$lStatusInput .= '<option value="' .$status['code']. '" '.$selected.'>' .$status['name']. '</option>';
		}
		$lStatusInput .= '</select>';

		$lButtons = '';

		
		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Détail</span>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-quest"/>';

		// Display data table
		echo '<table>';

		echo '<tr><td class="labelname-small">Quête :</td> 		<td class="labelvalue" colspan="3">' .$lQuest->GetSubject(). '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">Comté choisi :</td> 	<td class="labelvalue" colspan="3">' .$lQuest->GetCountyName(). '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">Demandée par :</td> 	<td class="labelvalue" colspan="3">' .$lQuest->GetApplicant()->GetFullName(). '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">Courriel :</td> 		<td class="labelvalue" colspan="3">' .$lQuest->GetApplicant()->GetMailAddress(). '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">Responsable :</td> 	<td colspan="2" class="inputbox">' .$lScriptorInput. '</td><td></td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">État :</td>		<td class="inputbox">' .$lStatusInput. '</td>
			  <td class="labelname-small">Demandée le :</td>	<td class="labelvalue">' .str_replace(":", "h", substr($lQuest->GetRequestDate(), 0, 16)). '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">Pour quel GN :</td>	<td class="labelvalue">' .$lQuest->GetActivity()->GetName(). '</td>
			  <td class="labelname-small">Approuvée le :</td> 	<td class="labelvalue">' .str_replace(":", "h", substr($lQuest->GetApprovalDate(), 0, 16)). '</td></tr>';

		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small" colspan="4">Suggestions offertes :</td>';
		echo '<tr><td style="width:600px; text-align:left;" colspan="4">' .nl2br($lQuest->GetSuggestions()). '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small" colspan="4">Commentaires de l\'approbateur :</td>';
		echo '<tr><td style="width:600px; text-align:left;" colspan="4">' .nl2br($lQuest->GetComments()). '</td></tr>';			
		echo '<tr class="filler"></tr>';
		echo '<tr><td colspan="4"><button type="submit" name="submit" value="Modifier quête" class="smalltext-button" style="margin-bottom:5px;"/>';
			echo 'Mettre à jour';
		echo '</button><br /></td></tr>';

		echo '</table>';

		echo '</form>';
		echo '<hr width=70% />';
		echo '</div>';
	}


	//--DISPLAY SELECTED GROUP QUEST'S WRITING--
	public function DisplayManagedGroupQuestWriting()
	{
		// Prepare data for the form
		$lQuest = $this->Manager->GetSelectedQuest();


		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Rédaction</span>';

		// Display selected part
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-quest"/>';

		echo '<span class="note"><b>Note : </b>Afin de ne pas perdre un texte, il est toujours conseillé de l\'écrire d\'abord dans un fichier.</span>';

		echo '<table>';
		echo '<tr><td class="inputname" style="width: 100px;">Missive</td>  <td></td></tr>';
		echo '<tr><td class="inputbox" colspan="3">	<textarea name="text" cols="72" rows="16" style="width:600px;" placeholder="Le texte de la quête elle-même...">'.$lQuest->GetText().'</textarea></td></tr>';

		echo '<tr><td colspan="3"><hr style="margin-top: 10px;"/></td></tr>';

		echo '<tr><td class="inputname" style="width: 100px;">Commentaires</td>  <td></td></tr>';
		echo '<tr><td class="inputbox" colspan="3">	<textarea name="comments" cols="72" rows="8" style="width:600px;" placeholder="Commentaires donnés au joueur concernant sa quête.">'.$lQuest->GetComments().'</textarea></td></tr>';

		echo '<tr class="filler"></tr>';
		echo '<tr><td colspan="3">';
			echo '<button type="submit" name="submit" value="Modifier rédaction" class="submit-button" />';
			echo 'Mettre à jour';
			echo '</button>';
		echo '</td></tr>';
		echo '</table>';

		echo '</form>';
		echo '<hr width=70% />';
		echo '</div>';
		
		echo '</div>';
	}


	//--DISPLAY SELECTED QUEST'S RESUMES--
	public function DisplayManagedGroupQuestResumes()
	{
		// Prepare data for the form
		$lQuest = $this->Manager->GetSelectedQuest();
		$lResumeList = $this->Manager->GetResumes();


		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Résumés d\'activité</span>';

		echo '<hr width=70% style="margin-bottom:5px;" />';
		echo '</div>';

		echo '</div>';

		// Display!
		echo '<div>';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-quest-lists"/>';

		echo '<table style="text-align: left;">';
		echo '<tr><th class="black-cell" style="width:100px;">Activité</th>
			  <th class="black-cell" style="width:260px;">Quête</th> 
			  <th class="black-cell" style="width:120px;">Création</th>
			  <th class="black-cell" style="width:40px;"></th></tr>';

		foreach($lResumeList as $i => $resume) {

			$lQuest = "";
				if( $resume->GetQuest()->GetID() ) { 
					$lQuest .= $resume->GetQuest()->GetSubject(); 
					# if( $resume->GetQuestPart()->GetID() ) { $lQuest .= " - Partie " . $resume->GetQuestPart()->GetNumber(); }
				}

			$lButton = '<button type="submit" name="select-group-resume" value="' .$i. '" class="icon-button"/><img src="images/icon_see.png" width="16" heigth="16"></button>';


			echo '<tr>';
			echo '<td class="grey-cell" style="width:100px;">' 	.$resume->GetActivity()->GetName().					'</td>';
			echo '<td class="white-cell" style="width:260px;">' 	.$lQuest.								'</td>';
			echo '<td class="white-cell" style="width:120px;">'	.str_replace(":", "h", substr($resume->GetCreationDate(), 0, 16)).	'</td>';
			echo '<td class="white-cell" style="width:40px;">'	.$lButton.								'</td>';
			echo '</tr>';
		}
		echo '</table>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY SELECTED RESUMÉ'S DETAIL--
	public function DisplayManagedGroupResume()
	{
		// Prepare data
		$index = $_POST['select-group-resume'];
		$lResume = $this->Manager->GetResumes()[$index];


		// Display!
		echo '<div>';
		echo '<span class="section-title">Résumé pour '.$lResume->GetActivity()->GetName().'</span>';

		// Resumé
		echo '<div style="margin: auto; margin-top: 10px; margin-bottom: 10px; width: 620px; text-align: left;"><span>' .nl2br( $lResume->GetText() ). '</span></div>';	

		echo '<hr width=70% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Résumés de groupe Responsable" class="smalltext-button"/>';
		echo 'Retour';
		echo '</button>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY SELECTED QUEST'S CHARACTER--
	public function DisplayManagedGroupQuestGroup()
	{
		// Check if there's a quest manager...
		if( $this->Manager == null ) { $this->Error = "No quest manager defined!"; return; }


		// Prepare data for the form
		$lQuest = $this->Manager->GetSelectedQuest();
		$lGroup = $this->Manager->GetGroup();
		$lStatus = $lGroup->GetStatus();
		    if( $lStatus == 'NOUV' ) 	{ $lStatus = 'Nouveau'; }
		elseif( $lStatus == 'ACTIF' ) 	{ $lStatus = 'Actif'; }
		elseif( $lStatus == 'INACT' ) 	{ $lStatus = 'Inactif'; }
		elseif( $lStatus == 'DISSO' ) 	{ $lStatus = 'Dissoud'; }
		elseif( $lStatus == 'RETIR' ) 	{ $lStatus = 'Retraité'; }
		elseif( $lStatus == 'ERAD' ) 	{ $lStatus = 'Éradiqué'; }

		$lPeopleInCharge = "";
			foreach($lGroup->GetPeopleInCharge() as $i => $person) {
				if($i) { $lPeopleInCharge .= '<br />'; }
				$lPeopleInCharge .= $person->GetFullName(). ' ('.$person->GetAccountName().')';
			}

		$lInstitutions = "";
			foreach($lGroup->GetInstitutions() as $i => $institution) {
				if($i) { $lInstitutions .= ' & '; }
				$lInstitutions .= $institution->GetProfile(). '(niv.'.$institution->GetLevel().')';
			}

		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Groupe</span>';

		// Display data table
		echo '<table>';

		echo '<tr><td class="labelname-small">Nom :</td> 		<td class="labelvalue" colspan="3">' .$lGroup->GetName(). '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">Membres :</td> 		<td class="labelvalue">' . $lGroup->GetMemberCount() . ' ( '. $lGroup->GetActiveMemberCount() .' actifs)</td>
			  <td class="labelname-small">Campement :</td>	 	<td class="labelvalue">' . $lGroup->GetBaseCamp() . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">Status :</td>		<td class="labelvalue">' . $lStatus . '</td>
			  <td class="labelname-small">Insitutions :</td> 	<td class="labelvalue">' . $lInstitutions . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">Responsables :</td> 	<td class="labelvalue" colspan="3">' .$lPeopleInCharge. '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small" colspan="4">Description :</td></tr>';
		echo '<tr><td colspan="4" style="text-align: left;">' .nl2br( $lGroup->GetDescription() ). '</td></tr>';

		echo '</table>';

		echo '<hr width=70% style="margin-bottom:5px;" />';
		echo '</div>';

	}


} // END of QuestUI class

?>