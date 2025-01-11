<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== New Character Views v1.2 r3 ==			║
║	Display new characters' related UIs.			║
║	Requires character model.				║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('models/newcharacter.class.php');
include_once('views/character_ui.class.php');

class NewCharacterUI extends CharacterUI
{

	//--CONSTRUCTOR--
	public function __construct($inCharacter)
	{
		parent::__construct($inCharacter);
	}


	//--DISPLAY CHARACTER REGISTRATION FORM--
	public function DisplayNewCharacterForm()
	{
		// Check if active character is a new character
		if( !($this->Character instanceof NewCharacter) ) { $this->Error = "Character is not new!"; return False; }

		// Cases where we need the simplest character creation form
		if( WORLD == 'TERNOC' ) { $this->DisplaySimplestCharacterCreationForm(); return; }


		// Build inputs
		$lStep = $this->Character->GetCreationStep();
		if($lStep == 4) { $this->DisplayNewCharacterConfirm(); return; }

		$lFirstNameInput = '<td class="inputvalue">'.$this->Character->GetFirstName().'</td>';
		if($lStep == 1) { $lFirstNameInput = '<td class="inputbox"><input name="firstname" type="text" value="'.$this->Character->GetFirstName().'" autocomplete="off" maxlength="50"/></td>'; }

		$lLastNameInput = '<td class="inputvalue">'.$this->Character->GetLastName().'</td>';
		if($lStep == 1) { $lLastNameInput = '<td class="inputbox"><input name="lastname" type="text" value="'.$this->Character->GetLastName().'" autocomplete="off" maxlength="100" placeholder="Facultatif"/></td>'; }

		$lRaceInput = '<td class="inputvalue">'.$this->Character->GetRace().'</td>';
		if($lStep == 1) { 
			$lPossibilities = $this->Character->GetRaceList();
			$lRaceInput = '<td class="inputbox"><select name="racecode">';
			foreach($lPossibilities as $race) {
				$selected = ''; if( $race['code'] == $this->Character->GetRaceCode() ) { $selected = 'selected'; }
				$lRaceInput .= '<option value="'.$race['code'].'" '.$selected.'>' .$race['name']. '</option>';
			}
			$lRaceInput .= '</select></td>';
		}

		$lClassInput = '<td class="inputvalue">'.$this->Character->GetClass().'</td>';
		if($lStep == 2) { 
			$lPossibilities = $this->Character->GetPossibleClasses();
			$lClassInput = '<td class="inputbox"><select name="class">';
			foreach($lPossibilities as $class) {
				$selected = ''; if( $class['code'] == $this->Character->GetClassCode() ) { $selected = 'selected'; }
				$lClassInput .= '<option value="'. $class['code'] .'" '.$selected.'>'. $class['name'] .'</option>';
			}
			$lClassInput .= '</select></td>';
		}

		$lArchetypeInput = '<td class="inputvalue">'.$this->Character->GetArchetype().'</td>';
		if($lStep == 3) { 
			$lPossibilities = $this->Character->GetPossibleArchetypes();
			$lArchetypeInput = '<td class="inputbox"><select name="archetype">';
			foreach($lPossibilities as $archetype) {
				$selected = ''; if( $archetype['code'] == $this->Character->GetArchetypeCode() ) { $selected = 'selected'; }
				$lArchetypeInput .= '<option value="'. $archetype['code'] .'" '.$selected.'>'. $archetype['name'] .'</option>';
			}
			$lArchetypeInput .= '</select></td>';
		}

		$lReligionInput = '<td class="inputvalue">'.$this->Character->GetReligion().'</td>';
		if($lStep == 3) { 
			$lPossibilities = $this->Character->GetPossibleReligions();
			$lReligionInput = '<td class="inputbox"><select name="religion">';
			foreach($lPossibilities as $religion) {
				$selected = ''; if( $religion['code'] == $this->Character->GetReligionCode() ) { $selected = 'selected'; }
				$lReligionInput .= '<option value="'. $religion['code'] .'" '.$selected.'>'. $religion['name'] .'</option>';
			}
			$lReligionInput .= '</select></td>';
		}

		$lOriginInput = '<td class="inputvalue">'.$this->Character->GetOrigin().'</td>';
		if($lStep == 3) { 
			$lPossibleOrigins = $_SESSION['masterlist']->GetKingdoms();
			$lOriginInput = '<td class="inputbox"><select name="origin">';
				foreach($lPossibleOrigins as $origin) {
					$selected = ''; if( isset($_POST['origin']) && $origin['name'] == $_POST['origin'] ) { $selected = 'selected'; }
					$lOriginInput .= '<option value="'. $origin['name'] .'" '.$selected.'>'. $origin['name'] .'</option>';
				}
			$lOriginInput .= '</select></td>';
		}


		// Display!
		echo '<div>';
		echo '<span class="section-title">Nouveau personnage - Étape '.$lStep.' de 4</span>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-newcharacter"/>';

		echo '<table>';
		echo '<tr><td class="inputname">Prénom</td>			' . $lFirstNameInput . '</tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Nom</td>			' . $lLastNameInput . '</tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Race</td>			' . $lRaceInput . '</tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Classe</td>			' . $lClassInput . '</tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Archétype</td>			' . $lArchetypeInput . '</tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Religion</td>			' . $lReligionInput . '</tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Provenance</td>			' . $lOriginInput . '</tr>';
		echo '<tr class="filler"></tr>';
		echo '</table>';

		echo '<hr width=70% />';

		// Submenu
		if($lStep > 1) { 
			echo '<button type="submit" name="option" value="Précédant" class="submit-button" style="margin: 5px;"/>';
			echo 'Précédent';
			echo '</button>';
		}
		if($lStep < 4) { 
			echo '<button type="submit" name="option" value="Suivant" class="submit-button" style="margin: 5px;"/>';
			echo 'Suivant';
			echo '</button>';
		}

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY CHARACTER REGISTRATION FORM--
	public function DisplayNewCharacterConfirm()
	{
		// Check if active character is a new character
		if( !($this->Character instanceof NewCharacter) ) { $this->Error = "Character is not new!"; return False; }


		// Build inputs
		$lSkillList = $this->Character->GetMergedSkills();
		$lTalentList = $this->Character->GetTalents();
		$lLife = $this->Character->GetLife();
			$lTotalLife = 0;
			foreach( $lLife as $lifemod ) { $lTotalLife += $lifemod['life']; }

		// Display title
		echo '<div>';
		echo '<span class="section-title">Nouveau personnage - Étape 4 de 4</span>';
		echo '<hr width=70% />';

		// Display base data
		echo '<table>';		

		echo '<tr><td class="labelname-small">Nom :</td>'; 		echo '<td class="labelvalue" colspan="3">' . $this->Character->GetFullName() . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">Race :</td>'; 		echo '<td class="labelvalue">' . $this->Character->GetRace() . '</td>
			  <td class="labelname-small">Religion :</td>'; 	echo '<td class="labelvalue">' . $this->Character->GetReligion() . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">Classe :</td>'; 		echo '<td class="labelvalue">' . $this->Character->GetClass() . '</td>
			  <td class="labelname-small">Provenance :</td>';	echo '<td class="labelvalue">' . $this->Character->GetOrigin() . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">Option :</td>';		echo '<td class="labelvalue">' . $this->Character->GetArchetype() . '</td>
			  <td class="labelname-small">PV :</td>'; 		echo '<td class="labelvalue">' . $lTotalLife . '</td></tr>';

		echo '</table>';
		echo '<hr width=70% />';

		// Display skills

		echo '<table style="text-align: left;">';
		echo '<tr><th class="black-cell" style="width:400px;">Compétence</th> <th class="black-cell" style="width:25px;">Qté</th> <th class="black-cell" style="width:80px;">Type</th></tr>';

		foreach($lSkillList as $skill) {
			$style = "color: black"; 	if($skill['status'] <> 'ACTIF') { $style = "color: grey"; }
			$name = $skill['name']; 	if($skill['precision']) 	{ $name .= " - <i>".$skill['precision']."</i>"; }
			$type = 'Régulière'; 

			echo '<tr style="'.$style.'">';
			echo '<td class="white-cell" style="width:400px;'.$style.'">' . $name . '</td>';
			echo '<td class="white-cell" style="width:25px; '.$style.'">' . $skill['quantity'] .'</td>';
			echo '<td class="white-cell" style="width:80px; '.$style.'">' . $type . '</td>';
			echo '</tr>';
		}

		foreach($lTalentList as $talent) {
			$style = "color: black"; 	if($talent['status'] <> 'ACTIF') { $style = "color: grey"; }
			$type = 'À valider'; 
				if( $talent['type'] == 'MINEURE' ) { $type = 'Mineure'; }
				if( $talent['type'] == 'MAJEURE' ) { $type = 'Majeure'; }
				if( $talent['type'] == 'RACIALE' ) { $type = 'Raciale'; }
				if( $talent['type'] == 'CLASSE' ) { $type = 'De classe'; }

			echo '<tr>';
			echo '<td class="white-cell" style="width:400px;'.$style.'">' . $talent['name'] . '</td>';
			echo '<td class="white-cell" style="width:25px; '.$style.'">' . $talent['quantity'] .'</td>';
			echo '<td class="white-cell" style="width:80px; '.$style.'">' . $type . '</td>';
			echo '</tr>';
		}

		echo '</table>';
		echo '<hr width=70% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-newcharacter"/>';

		echo '<button type="submit" name="option" value="Précédant" class="submit-button" style="margin: 5px;"/>';
		echo 'Précédent';
		echo '</button>';

		echo '<button type="submit" name="option" value="Enregistrer" class="submit-button" style="margin: 5px;"/>';
		echo 'Enregistrer';
		echo '</button>';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY CHARACTER REGISTRATION FORM--
	public function DisplaySimplestCharacterCreationForm()
	{
		// Build inputs
		$lFirstName = $this->Character->GetFirstName();
		if( isset($_POST['firstname']) ) { $lFirstName = $_POST['firstname']; }

		$lLastName = $this->Character->GetLastName();
		if( isset($_POST['lastname']) ) { $lLastName = $_POST['lastname']; }


		$lRaceList = $this->Character->GetRaceList();

		$lRaceInput = '<select name="racecode">';
			foreach($lRaceList as $race) {
				$selected = ''; if( $race['code'] == $this->Character->GetRaceCode() ) { $selected = 'selected'; }
				$lRaceInput .= '<option value="'.$race['code'].'" '.$selected.'>' .$race['name']. '</option>';
			}
			$lRaceInput .= '</select>';


		$lStateList = array('Éveillé', 'En fuite');
		$lStateInput = '<select name="origin">';
			foreach($lStateList as $origin) {
				$selected = ''; if( isset($_POST['origin']) && $origin == $_POST['origin'] ) { $selected = 'selected'; }
				$lStateInput .= '<option value="'. $origin .'" '.$selected.'>'. $origin .'</option>';
			}
			$lStateInput .= '</select>';


		// Display!
		echo '<div>';
		echo '<span class="section-title">Nouveau personnage</span>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-newcharacter"/>';

		echo '<table>';
		echo '<tr><td class="inputname">Prénom</td>
			  <td class="inputbox"><input name="firstname" type="text" value="'.$lFirstName.'" autocomplete="off" maxlength="50"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Nom</td>
			  <td class="inputbox"><input name="lastname" type="text" value="'.$lLastName.'" autocomplete="off" maxlength="100" placeholder="Facultatif"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Race</td>
			  <td class="inputbox">' . $lRaceInput . '</td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Situation</td>
			  <td class="inputbox">' . $lStateInput . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '</table>';

		echo '<hr width=70% />';


		// Submenu
		echo '<button type="submit" name="option" value="Annuler création" class="submit-button" style="margin: 5px;"/>';
		echo 'Annuler';
		echo '</button>';

		echo '<button type="submit" name="option" value="Enregistrer base" class="submit-button" style="margin: 5px;"/>';
		echo 'Enregistrer';
		echo '</button>';

		echo '</form>';
		echo '</div>';
	}


} // END of NewCharacterUI class

?>
