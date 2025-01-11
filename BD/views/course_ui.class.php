<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Course Views v1.2 r3 ==				║
║	Display courses' related UIs.				║
║	Requires character model and course manager.		║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('models/character.class.php');
include_once('models/coursemanager.class.php');

class CourseUI
{

protected $Manager;

public $Error;

	//--CONSTRUCTOR--
	public function __construct($inManager)
	{
		$this->Manager = $inManager;
	}


	//--DISPLAY MORE OPTIONS FOR THE CHARACTER SHEET--
	public function DisplayNewTeachingOptions()
	{
		// Check if there's a character...
		if( $this->Manager == null ) { $this->Error = "No character defined!"; return; }


		// Prepare data
		$lNPCList = $this->Manager->GetNPCs();
		$lPCList = $this->Manager->GetMasters();

		$lNPCInput = '<select name="npc-index">';
		foreach ($lNPCList as $i => $npc) {
			$lNPCInput .= '<option value="'.$i.'">'.$npc->GetFullName().'</option>';
		}
		$lNPCInput .= '</select>';

		$lPCInput = '<select name="pc-index">';
		foreach ($lPCList as $i => $pc) {
			$lPCInput .= '<option value="'.$i.'">'.$pc->GetFullName().'</option>';
		}
		$lPCInput .= '</select>';

		$lAccount = ""; if( isset($_POST['account']) ) { $lAccount = $_POST['account']; }
		$lFirstName = ""; if( isset($_POST['firstname']) ) { $lFirstName = $_POST['firstname']; }
		$lLastName = ""; if( isset($_POST['lastname']) ) { $lLastName = $_POST['lastname']; }


		// Display!
		echo '<div>';
		echo '<span class="section-title">OPTIONS D\'ENREGISTREMENT D\'UN COURS</span>';

		// Instructions
		echo   '<div style="margin:auto; margin-bottom:15px; margin-top:15px; padding: 5px; width:620px; border:1px solid black; font-size:0.8em;">
			<span><b><u>INSTRUCTIONS</b></u><br />
				Cette interface vous permet d\'enregistrer les cours que votre personnage a donnés ou reçus, que ce soit d\'un autre joueur ou d\'un PNJ. 
				Votre personnage recevra immédiatement les avantages de ce cours (XP ou rabais). Ceux-ci seront valider par un responsable par la suite.
			</span>
			</div>';

		// "NPC-received" option
		echo '<span class="section-title">Enseignement par un PNJ</span>';
		echo '<span class="note">Si vous avez reçu un cours d\'un Personnage Non Joueur, sélectionnez votre instructeur et cliquez sur le bouton ci-dessous.</span>';
	
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character"/>';

		echo '<table>';
		echo '<tr><td class="inputname">Instructeur</td>		<td class="inputbox" style="width: 250px;">' . $lNPCInput . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td colspan="2">
			<button type="submit" name="option" value="Choix maître PNJ" class="submit-button" />Continuer</button>
		      </td></tr>';
		echo '</table>';

		echo '</form>';

		echo '<hr width=50% />';

		// "PC-received" option
		echo '<span class="section-title">Enseignement reçu d\'un joueur</span>';
		echo '<span class="note">Si vous avez reçu un cours d\'un Personnage Joueur, sélectionnez votre instructeur et cliquez sur le bouton ci-dessous.</span>';
	
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character"/>';

		echo '<table>';
		echo '<tr><td class="inputname">Instructeur</td>		<td class="inputbox" style="width: 250px;">' . $lPCInput . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td colspan="2">
			<button type="submit" name="option" value="Choix maître PJ" class="submit-button" />Continuer</button>
		      </td></tr>';
		echo '</table>';

		echo '</form>';

		echo '<hr width=50% />';

		// "PC-given" option
		echo '<form method="post" style="margin-bottom:10px;">';
		echo '<input type="hidden" name="action" value="search"/>';
		echo '<span class="section-title">Enseignement donné à un autre joueur</span>';

		echo '<span class="note">Si vous donné un cours à un autre joueur, utilisez l\'outil de recherche ci-dessous afin de trouver votre élève.</span>';

		echo '<table>';
		echo '<tr><td class="inputname">Compte joueur</td>		<td class="inputbox"><input name="account" type="text" value="' .$lAccount. '" maxlength="32"/></td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Prénom du personnage</td>	<td class="inputbox"><input name="firstname" type="text" value="' .$lFirstName. '" maxlength="50"/></td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Nom du personnage</td>		<td class="inputbox"><input name="lastname" type="text" value="' .$lLastName. '" maxlength="50"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td colspan="2">';
			echo '<button type="submit" name="option" value="Personnages" class="submit-button" />';
			echo 'Rechercher';
			echo '</button>';
		echo '</td></tr>';
		echo '</table>';

		echo '</form>';

		// Results
		if( isset($_POST['search_results']) ) {
			$lCharacters = $_POST['search_results'];

			echo '<form method="post" style="margin-bottom:15px;">';
			echo '<input type="hidden" name="action" value="manage-character-lists"/>';
			echo '<span class="section-title">Choisir un personnage</span>';

			echo '<table>';

			echo '	<tr>
				<th class="black-cell" style="width:10px;">#</th> 
				<th class="black-cell" style="width:250px;">Personnage</th> 
				<th class="black-cell" style="width:250px;">Joueur</th> 
				<th class="black-cell" style="width:16px;"></th>
				</tr>';

			foreach($lCharacters as $i => $character) {
				$line = $i + 1;
				$lButtons = '<button type="submit" name="teach-student" value="' .$character['characterid']. '" class="icon-button"/><img src="images/icon_plus.png" class="icon-button-image"></button>';

				echo '<tr>';
				echo '<td class="grey-cell" style="width:10px;">' 	.$line.							'</td>';
				echo '<td class="white-cell" style="width:160px;">' 	.$character['charactername'].				'</td>';
				echo '<td class="white-cell" style="width:160px;">' 	.$character['username'].' ('.$character['account'].	')</td>';
				echo '<td class="white-cell" style="width:16px;">' 	.$lButtons.						'</td>';
				echo '</tr>';
			}

			echo '</table>';

			echo '</form>';
		}

		echo '<hr width=70% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Enseignements personnage" class="submit-button" />';
		echo 'Retour';
		echo '</button>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY REACHING REGISTRATION FORM--
	public function DisplayTeachingRegForm()
	{
		// Check if there's a character...
		if( $this->Manager == null ) { $this->Error = "No character defined!"; return; }


		// Prepare data
		$lMasterID = $this->Manager->GetSelectedMaster()->GetID();
		$lMasterName = $this->Manager->GetSelectedMaster()->GetFullName();
		$lMasterPlayer = $this->Manager->GetSelectedMaster()->GetUserName();

		$lStudentID = $this->Manager->GetSelectedStudent()->GetID();
		$lStudentName = $this->Manager->GetSelectedStudent()->GetFullName();
		$lStudentPlayer = $this->Manager->GetSelectedStudent()->GetUserName();

		$lSkillList = $this->Manager->GetSelectedMaster()->GetSkills();
		$lSkillInput = '<select name="skillcode">';
		foreach ($lSkillList as $skill) {
			$lSkillInput .= '<option value="'.$skill['code'].'">'.$skill['name'].'</option>';
		}
		$lSkillInput .= '</select>';

		$lActivityList = $this->Manager->GetValidActivities();
		$lActivityInput = '<select name="activityid">';
		foreach ($lActivityList as $activity) {
			$lActivityInput .= '<option value="'.$activity->GetID().'">'.$activity->GetName().'</option>';
		}
		$lActivityInput .= '</select>';

		$lPlace = "";
			if( isset( $_POST['place'] ) ) { $lPlace = $_POST['place']; }

		$lMomentInput = '<select name="moment">';
			$lMomentInput .= '<option value="Vendredi soir">Vendredi soir</option>';
			$lMomentInput .= '<option value="Samedi matin">Samedi matin</option>';
			$lMomentInput .= '<option value="Samedi midi">Samedi midi</option>';
			$lMomentInput .= '<option value="Samedi après-midi">Samedi après-midi</option>';
			$lMomentInput .= '<option value="Samedi soir">Samedi soir</option>';
			$lMomentInput .= '<option value="Dimanche matin">Dimanche matin</option>';
			$lMomentInput .= '<option value="Maître absent du jeu">Maître absent du jeu</option>';
		$lMomentInput .= '</select>';

		// Display!
		echo '<div>';
		echo '<span class="section-title">Enregistrer un nouvel enseignement</span>';
		echo '<hr width=70% />';

		// Registration form
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character"/>';
		echo '<input type="hidden" name="masterid" value="'.$lMasterID.'"/>';
		echo '<input type="hidden" name="studentid" value="'.$lStudentID.'"/>';

		echo '<table>';
		echo '<tr><td class="inputname">Maître</td>			<td class="labelvalue" style="width: 300px;">' .$lMasterName.' ('.$lMasterPlayer.')'. '</td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Élève</td>			<td class="labelvalue" style="width: 300px;">' .$lStudentName.' ('.$lStudentPlayer.')'. '</td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Compétence</td>			<td class="inputbox" style="width: 300px;">' . $lSkillInput . '</td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Activité</td>			<td class="inputbox" style="width: 300px;">' . $lActivityInput . '</td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Lieu</td>			<td class="inputbox"><input name="place" type="text" value="' . $lPlace . '" maxlength="50" style="width: 300px;"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Moment</td>			<td class="inputbox" style="width: 300px;">' . $lMomentInput . '</td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td colspan="2">';
			echo '<button type="submit" name="option" value="Enseignement" class="submit-button" style="margin: 5px;" />';
			echo 'Enregistrer';
			echo '</button>';

			echo '<button type="submit" name="option" value="Retour enseignement" class="submit-button" style="margin: 5px;" />';
			echo 'Annuler';
			echo '</button>';
		echo '</td></tr>';
		echo '</table>';

		echo '</form>';

		echo '<hr width=70% />';
		echo '</div>';
	}


	//--DISPLAY REACHING REGISTRATION FORM--
	public function DisplaySyllabusRegForm()
	{
		// Check if there's a character...
		if( $this->Manager == null ) { $this->Error = "DisplaySyllabusRegForm : No manager defined!"; return; }
		if( $this->Manager->GetCharacter() == null ) { $this->Error = "DisplaySyllabusRegForm : No character defined!"; return; }

		// Prepare data
		$lCharacterID = $this->Manager->GetCharacter()->GetID();

		$lSkillList = $this->Manager->GetTeachableSkills();
		$lSkillInput = '<select name="skillcode">';
		foreach ($lSkillList as $skill) {
			$lSkillInput .= '<option value="'.$skill['code'].'">'.$skill['name'].'</option>';
		}
		$lSkillInput .= '</select>';

		$lFileInput = '<input type="file" name="attachedfile" id="attachedfile" />';


		// Display!
		echo '<div>';
		echo '<span class="section-title">Soumettre un nouveau plan de cours</span>';
		echo '<hr width=70% />';

		// Instructions
		echo '<div style="width: 70%; margin: auto; text-align: left;"><table>
				<tr><td style="width: 25%;" valign="top"><b>Délais :</b></td>
					<td>Vous devez avoir soumis vos plans de cours au plus tard le mercredi précédent le premier GN où vous souhaitez donner votre cours.</td></tr>
				<tr><td style="width: 25%;" valign="top"><b>Fichiers :</b></td>
					<td>Seuls les fichiers PDF de moins de 5 Mo sont acceptés. Imprimez vos documents en PDF au besoin.</td></tr>
		      </table></div>';

		echo '<hr width=70% />';

		// Registration form
		echo '<form method="post" enctype="multipart/form-data">';
		echo '<input type="hidden" name="action" value="manage-character"/>';
		echo '<input type="hidden" name="characterid" value="'.$lCharacterID.'"/>';

		echo '<table>';
		echo '<tr><td class="inputname">Compétence</td>			<td class="inputbox" style="width: 300px;">' . $lSkillInput . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Fichier</td>			<td class="inputbox" style="width: 300px;">' . $lFileInput . '</td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td colspan="2">';
			echo '<button type="submit" name="option" value="Plan de cours" class="submit-button" style="margin: 5px;" />';
			echo 'Enregistrer';
			echo '</button>';

			echo '<button type="submit" name="option" value="Retour enseignement" class="submit-button" style="margin: 5px;" />';
			echo 'Annuler';
			echo '</button>';
		echo '</td></tr>';
		echo '</table>';

		echo '</form>';

		echo '<hr width=70% />';
		echo '</div>';
	}


} // END of CourseUI class

?>
