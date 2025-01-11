<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Character Views v1.2 r17 ==				║
║	Display a character's related UIs.			║
║	Requires character model.				║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('configs/config.cfg');
include_once('models/character.class.php');
include_once('models/quest.class.php');
include_once('models/letter.class.php');
include_once('models/masterlist.class.php');

class CharacterUI
{

protected $Character;

public $Error;

	//--CONSTRUCTOR--
	public function __construct($inCharacter)
	{
		$this->Character = $inCharacter;
	}


	//--DISPLAY CHARACTER INFORMATION--
	public function DisplayCharacterInfo( $inSubUI =NULL )
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Prepare data for the form
		$class_label = 'Classe'; 	if(defined('alt_class')) 	{ $class_label = alt_class; }
		$archetype_label = 'Archétype'; if(defined('alt_archetype')) 	{ $archetype_label = alt_archetype; }
		$level_label = 'Niveau'; 	if(defined('alt_level')) 	{ $level_label = alt_level; }
		$origin_label = 'Provenance'; 	if(defined('alt_origin')) 	{ $origin_label = alt_origin; }
		$group_label = 'Groupe'; 	if(defined('alt_group')) 	{ $group_label = alt_group; }

		$lStatus = $this->Character->GetStatus();
		    if( $lStatus == 'NOUVO' ) 	{ $lStatus = 'Nouveau'; }
		elseif( $lStatus == 'ACTIF' ) 	{ $lStatus = 'Actif'; }
		elseif( $lStatus == 'LEVEL' ) 	{ $lStatus = 'Actif - Personnage inscrit'; }
		elseif( $lStatus == 'EXIL'  ) 	{ $lStatus = 'Exilé'; }
		elseif( $lStatus == 'DEPOR' ) 	{ $lStatus = 'Déporté'; }
		elseif( $lStatus == 'RETIR' ) 	{ $lStatus = 'Retraité'; }
		elseif( $lStatus == 'MORT'  ) 	{ $lStatus = 'Décédé'; }

		$lGroup = $this->Character->GetGroup();
		$lGroupName = '';
		if( $lGroup ) { $lGroupName = $lGroup->GetName(); }
		elseif( $this->Character->GetNextInvitation() ) { 
			$key = $this->Character->GetID().'-'.$this->Character->GetNextInvitation()->GetID();
			$lButtons = ' <button type="submit" name="accept-invite" value="' .$key. '" class="icon-button"/><img src="images/icon_accept.png" class="icon-button-image"></button>
				      <button type="submit" name="refuse-invite" value="' .$key. '" class="icon-button"/><img src="images/icon_delete.png" class="icon-button-image"></button>';; 
			$lGroupName = '<span style="font-weight:normal"><i>Vous avez été invité par : <b>'.$this->Character->GetNextInvitation()->GetName().'</b>. Accepter ? </i>'.$lButtons.'</span>'; 
		}


		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Fiche de personnage</span>';
		echo '<hr width=70% />';

		// Display data
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character"/>';

		echo '<table>';		

		echo '<tr><td class="labelname-small">Nom :</td> 		<td class="labelvalue" colspan="3">' . $this->Character->GetFullName() . '</td></tr>';
		echo '<tr class="filler"></tr>';
				echo '<tr>';
							echo '<td class="labelname-small">Race :</td> 		<td class="labelvalue">' . $this->Character->GetRace() . '</td>';
		if(CHARACTER_RELIGION_ENABLED){ 	echo '<td class="labelname-small">Religion :</td>	<td class="labelvalue">' . $this->Character->GetReligion() . '</td>'; } else {echo '<td></td>';}
				echo '</tr>';
				echo '<tr class="filler"></tr>';
		if(CHARACTER_CLASS_ENABLED){
				echo '<tr>';
							echo '<td class="labelname-small">'.$class_label.' :</td> 	<td class="labelvalue">' . $this->Character->GetClass() . '</td>';
		if(CHARACTER_ARCHETYPE_ENABLED){ 	echo '<td class="labelname-small">'.$archetype_label.' :</td>	<td class="labelvalue">' . $this->Character->GetArchetype() . '</td>'; } else {echo '<td></td>';}
				echo '</tr>';
				echo '<tr class="filler"></tr>'; }
				echo '<tr>';
						echo '<td class="labelname-small">'.$level_label.' :</td>	<td class="labelvalue">' . $this->Character->GetLevel() . '</td>';
		if(CHARACTER_ORIGIN_ENABLED){ 	echo '<td class="labelname-small">'.$origin_label.' :</td>	<td class="labelvalue">' . $this->Character->GetOrigin() . '</td>'; } else {echo '<td></td>';}
				echo '</tr>';
		if(CHARACTER_GROUP_ENABLED){
				echo '<tr class="filler"></tr>';
				echo '<tr><td class="labelname-small">'.$group_label.' :</td> 		<td class="labelvalue" colspan="3">' . $lGroupName . '</td></tr>'; }
				echo '<tr class="filler"></tr>';
				echo '<tr class="filler"></tr>';
		if(CHARACTERSHEET_STATS_GROUP_ENABLED){
				echo '<tr>';
		if(CHARACTERSHEET_LIFE_ENABLED){ 	echo '<td class="labelname-small">PV :</td>		<td class="labelvalue">' . $this->Character->GetTotalLife().' / '. $this->Character->GetMaxLife() . '</td>'; } else {echo '<td></td>';}
		if(CHARACTER_EXPERIENCE_ENABLED){ 	echo '<td class="labelname-small">Expérience :</td>	<td class="labelvalue">' . $this->Character->GetTotalExperience().' / '. $this->Character->GetGainedExperience() . '</td>'; } else {echo '<td></td>';}
				echo '</tr>';
				echo '<tr class="filler"></tr>';
				echo '<tr>';
		if(CHARACTERSHEET_MAGIC_ENABLED){ 	echo '<td class="labelname-small">Éléments de magie :</td>	<td class="labelvalue">' . $this->Character->GetTotalMana() . '</td>'; } else {echo '<td></td>';}
		if(CHARACTER_QUESTS_ENABLED){ 		echo '<td class="labelname-small">Crédits de quête :</td>	<td class="labelvalue">' . array_sum($this->Character->GetQuestCredits()) . '</td>'; } else {echo '<td></td>';}
				echo '</tr>';
				echo '<tr class="filler"></tr>';
				echo '<tr class="filler"></tr>'; }
		echo '<tr><td class="labelname-small">Status :</td> 		<td class="labelvalue">' . $lStatus . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname-small">Note :</td> 		<td class="labelvalue-note" colspan="3">' . $this->Character->GetQuickNote() . '</td></tr>';

		echo '</table>';
		echo '</form>';
		echo '<hr width=70% />';

		// Manage Reset only if no archetype
		if( !$this->Character->GetArchetypeCode() ) { $this->DisplayResetForm(); return; }


		// Submenu
		$lOptionCount = 0;
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		if(CHARACTER_SKILLS_ENABLED){
		echo '<button type="submit" name="option" value="Compétences personnage" class="smalltext-button" style="margin:0px;">';
		echo 'Compétences';
		echo '</button>';
			$lOptionCount++; if($lOptionCount >= OPTIONS_PER_LINE) {echo '<br />'; $lOptionCount = 0; }
		}
		if(CHARACTER_POSSESSIONS_ENABLED){
		echo '<button type="submit" name="option" value="Possessions personnage" class="smalltext-button" style="margin:0px;">';
		echo 'Possessions';
		echo '</button>';
			$lOptionCount++; if($lOptionCount >= OPTIONS_PER_LINE) {echo '<br />'; $lOptionCount = 0; }
		}
		if(CHARACTER_BACKGROUND_ENABLED){
		echo '<button type="submit" name="option" value="Histoire personnage" class="smalltext-button" style="margin:0px;">';
		echo 'Histoire';
		echo '</button>';
			$lOptionCount++; if($lOptionCount >= OPTIONS_PER_LINE) {echo '<br />'; $lOptionCount = 0; }
		}
		if(CHARACTER_QUESTS_ENABLED){
		echo '<button type="submit" name="option" value="Quêtes personnage" class="smalltext-button" style="margin:0px;">';
		echo 'Quêtes';
		echo '</button>';
			$lOptionCount++; if($lOptionCount >= OPTIONS_PER_LINE) {echo '<br />'; $lOptionCount = 0; }
		}
		if(CHARACTER_LIFE_ENABLED){
		echo '<button type="submit" name="option" value="Vie personnage" class="smalltext-button" style="margin:0px;">';
		echo 'Points de vie';
		echo '</button>';
			$lOptionCount++; if($lOptionCount >= OPTIONS_PER_LINE) {echo '<br />'; $lOptionCount = 0; }
		}
		if(CHARACTER_MISSIVES_ENABLED){
		echo '<button type="submit" name="option" value="Missives personnage" class="smalltext-button" style="margin:0px;">';
		echo 'Missives';
		echo '</button>';
			$lOptionCount++; if($lOptionCount >= OPTIONS_PER_LINE) {echo '<br />'; $lOptionCount = 0; }
		}
		if(CHARACTER_TEACHING_ENABLED){
		echo '<button type="submit" name="option" value="Enseignements personnage" class="smalltext-button" style="margin:0px;">';
		echo 'Enseignements';
		echo '</button>';
			$lOptionCount++; if($lOptionCount >= OPTIONS_PER_LINE) {echo '<br />'; $lOptionCount = 0; }
		}
		if(CHARACTER_TITLES_ENABLED){
		echo '<button type="submit" name="option" value="Titres personnage" class="smalltext-button" style="margin:0px;">';
		echo 'Titres';
		echo '</button>';
			$lOptionCount++; if($lOptionCount >= OPTIONS_PER_LINE) {echo '<br />'; $lOptionCount = 0; }
		}
		if(CHARACTER_RESUMES_ENABLED){
		echo '<button type="submit" name="option" value="Résumés personnage" class="smalltext-button" style="margin:0px;">';
		echo 'Résumés';
		echo '</button>';
			$lOptionCount++; if($lOptionCount >= OPTIONS_PER_LINE) {echo '<br />'; $lOptionCount = 0; }
		}
		if(CHARACTER_EXPERIENCE_ENABLED){
		echo '<button type="submit" name="option" value="Expérience personnage" class="smalltext-button" style="margin:0px;">';
		echo 'Expérience';
		echo '</button>';
			$lOptionCount++; if($lOptionCount >= OPTIONS_PER_LINE) {echo '<br />'; $lOptionCount = 0; }
		}
		if(CHARACTER_OTHERS_ENABLED){
		echo '<button type="submit" name="option" value="Autres options personnage" class="smalltext-button" style="margin:0px;">';
		echo 'Autres';
		echo '</button>';
			$lOptionCount++; if($lOptionCount >= OPTIONS_PER_LINE) {echo '<br />'; $lOptionCount = 0; }
		}

		echo '</form>';
		echo '</div>';


		// Display SubUI if not null
		    if( $inSubUI == 'SKILLS' ) { $this->DisplayCharacterSkills(); }
			elseif( $inSubUI == 'SKILL PRECISION' ) { $this->DisplayCharacterSkills(); $this->DisplayCharacterSkillPrecisionForm();}
		elseif( $inSubUI == 'POSSESSIONS' ) { $this->DisplayCharacterPossessions(); }
		elseif( $inSubUI == 'TEACHINGS' ) { $this->DisplayCharacterTeachings(); }
		elseif( $inSubUI == 'BACKGROUND' ) { $this->DisplayCharacterBackground(); }
			elseif( $inSubUI == 'EDIT BACKGROUND' ) { $this->DisplayBackgroundEditingForm(); }
			elseif( $inSubUI == 'SUBMIT BACKGROUND' ) { $this->DisplayBackgroundSubmitingForm(); }
			elseif( $inSubUI == 'BACKGROUND COMMENTS' ) { $this->DisplayBackgroundComments(); }
		elseif( $inSubUI == 'TITLES' ) { $this->DisplayCharacterTitles(); }
			elseif( $inSubUI == 'SELECTED TEACHING' ) { $this->DisplaySelectedTeaching(); }
		elseif( $inSubUI == 'QUESTS' ) { $this->DisplayCharacterQuests(); }
			elseif( $inSubUI == 'NEW QUEST' ) { $this->DisplayQuestRequestForm(); }
		elseif( $inSubUI == 'RESUMES' ) { $this->DisplayCharacterResumes(); }
			elseif( $inSubUI == 'NEW RESUME' ) { $this->DisplayResumeCreationForm(); }
		elseif( $inSubUI == 'LIFE' ) { $this->DisplayCharacterLife(); }
			elseif( $inSubUI == 'RESURRECTION' ) { $this->DisplayCharacterResurrectionForm(); }
		elseif( $inSubUI == 'XP' ) { $this->DisplayCharacterXPTransfer(); }
			elseif( $inSubUI == 'XP DETAIL' ) { $this->DisplayCharacterXPDetail(); }
		elseif( $inSubUI == 'LETTERS' ) { $this->DisplayCharacterLetterbox(); }
			elseif( $inSubUI == 'NEW NPC LETTER' ) { $this->DisplayCharacterNewLetterToNPCForm(); }
			elseif( $inSubUI == 'NEW PC LETTER' ) { $this->DisplayCharacterNewLetterToPCForm(); }
			elseif( $inSubUI == 'VIEW LETTER' ) { $this->DisplaySelectedLetter(); }
		elseif( $inSubUI == 'LETTER ARCHIVES' ) { $this->DisplayCharacterLetterArchives(); }
		elseif( $inSubUI == 'OTHERS' ) { $this->DisplayOtherOptions(); }
			elseif( $inSubUI == 'STATUS' ) { $this->DisplayCharacterStatusManagement(); }
				elseif( $inSubUI == 'PERMANENT DEATH' ) { $this->DisplayCharacterDeathDeclarationForm(); }
				elseif( $inSubUI == 'DEPORTATION' ) { $this->DisplayCharacterDeportationForm(); }
				elseif( $inSubUI == 'EXILE' ) { $this->DisplayCharacterExileForm(); }
				elseif( $inSubUI == 'RETIREMENT' ) { $this->DisplayCharacterRetirementForm(); }
			elseif( $inSubUI == 'RENAME' ) { $this->DisplayCharacterRename(); }
			elseif( $inSubUI == 'ORIGIN' ) { $this->DisplayCharacterChangeOrigin(); }
			elseif( $inSubUI == 'NOTES' ) { $this->DisplayCharacterNotes(); }
			elseif( $inSubUI == 'DELETE' ) { $this->DisplayCharacterDelete(); }
	}


	//--DISPLAY RESET FORM--
	public function DisplayResetForm()
	{
		// Prepare data
		$lPossibleArchetypes = $_SESSION['masterlist']->GetArchetypesByClass( $this->Character->GetClassCode() );
		$lArchetypeInput = '<td class="inputbox"><select name="archetype">';
			foreach($lPossibleArchetypes as $archetype) {
				$selected = ''; if( isset($_POST['archetype']) && $archetype['code'] == $_POST['archetype'] ) { $selected = 'selected'; }
				$lArchetypeInput .= '<option value="'. $archetype['code'] .'" '.$selected.'>'. $archetype['name'] .'</option>';
			}
		$lArchetypeInput .= '</select></td>';

		$lPossibleOrigins = $_SESSION['masterlist']->GetKingdoms();
		$lOriginInput = '<td class="inputbox"><select name="origin">';
			foreach($lPossibleOrigins as $origin) {
				$selected = ''; if( isset($_POST['origin']) && $origin['name'] == $_POST['origin'] ) { $selected = 'selected'; }
				$lOriginInput .= '<option value="'. $origin['name'] .'" '.$selected.'>'. $origin['name'] .'</option>';
			}
		$lOriginInput .= '</select></td>';


		// Display
		echo '<div>';
		echo '<span class="section-title">RÉINITIALISATION DE LA FICHE</span>';
		echo '<hr width=70% />';

		echo '<span class="note" >Faites les choix suivants si applicables, puis cliquez sur "Réinitialiser".</span>';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character-reset"/>';

		echo '<table>';

		echo '<tr><td class="inputname">Archétype</td>			' . $lArchetypeInput . '</tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Provenance</td>			' . $lOriginInput . '</tr>';
		echo '<tr class="filler"></tr>';
		echo '</table>';

		echo '<hr width=70% />';

		// Submenu
		echo '<button type="submit" name="option" value="Réinitialiser" class="submit-button" style="margin: 5px;"/>';
		echo 'Réinitialiser';
		echo '</button>';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY MORE OPTIONS FOR THE CHARACTER SHEET--
	public function DisplayOtherOptions()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Prepare data


		// Display!
		echo '<div>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Renommer personnage" class="smalltext-button" />';
		echo 'Renommer';
		echo '</button><br />';

		echo '<button type="submit" name="option" value="Statut personnage" class="smalltext-button" />';
		echo 'Statut';
		echo '</button><br />';

		if(CHARACTER_ORIGIN_ENABLED){
		echo '<button type="submit" name="option" value="Provenance personnage" class="smalltext-button" />';
		echo 'Provenance';
		echo '</button><br />'; }

		// echo '<button type="submit" name="option" value="Journal personnage" class="smalltext-button" />';
		// echo 'Journal';
		// echo '</button><br />';

		echo '<button type="submit" name="option" value="Supprimer personnage" class="smalltext-button" />';
		echo 'Supprimer';
		echo '</button><br />';

		echo '</form>';

		echo '<hr width=70% />';

		echo '</div>';
	}


	//--DISPLAY CHARACTER'S SKILLS--
	public function DisplayCharacterSkills()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Prepare data for the form
		$lSkillList = $this->Character->GetMergedSkills();
		$lTalentList = $this->Character->GetTalents();


		// Display!
		echo '<div>';
		echo '<hr width=70% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<table style="text-align: center;">';
		echo   '<tr> <th colspan="2" style="width:250px;">Compétences régulières</th> <th colspan="2" style="width:250px;">Compétences spéciales</th> </tr>';

		echo '<tr>';
		echo '<td><button type="submit" name="option" value="Ajouter compétences régulières" class="smalltext-button" style="margin: 8px; margin-right: 2px;">';
		echo 'Ajout';
		echo '</button></td>';

		echo '<td><button type="submit" name="option" value="Retirer compétences régulières" class="smalltext-button" style="margin: 8px; margin-left: 2px;">';
		echo 'Retrait';
		echo '</button></td>';

		echo '<td><button type="submit" name="option" value="Ajouter compétences spéciales" class="smalltext-button" style="margin: 8px; margin-right: 2px;">';
		echo 'Ajout';
		echo '</button></td>';

		echo '<td><button type="submit" name="option" value="Retirer compétences spéciales" class="smalltext-button" style="margin: 8px; margin-left: 2px;" disabled>';
		echo 'Retrait';
		echo '</button></td>';
		echo '</tr>';

		echo '</table>';

		echo '</form>';

		// Skills
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character-lists"/>';

		echo '<table style="text-align: left;">';
		echo   '<tr>
			<th class="black-cell" style="width:400px;">Compétence</th>
			<th class="black-cell" style="width:25px; font-size: 0.8em;">UEC*</th>
			<th class="black-cell" style="width:120px;">Type</th>
			</tr>';

		foreach($lSkillList as $skill) {
			$lSkillDescription = $skill['name'];
				if($skill['precision']) { $lSkillDescription .= " - <i>".$skill['precision']."</i>"; }
				else if($skill['precisable']) { $lSkillDescription .= '<button type="submit" name="specify-skill" value="'.$skill['id'].'" class="icon-button" style="background-color: white; float: right;" ><img src="images/icon_elaborate.png" class="icon-button-image"></button>'; }

			$lStyle = "";
			if( $skill['status'] == 'PRLVL' ) { $lStyle = "background-color: lightblue; font-weight: bold;"; }
			if( $skill['status'] == 'LEVEL' ) { $lStyle = "background-color: lightgreen; font-weight: bold;"; }

			echo '<tr>';
			echo '<td class="white-cell" style="width:400px; '.$lStyle.'">' 			. $lSkillDescription .		'</td>';
			echo '<td class="white-cell" style="width:25px; text-align: center;'.$lStyle.'">'	. $skill['quantity'] .		'</td>';
			echo '<td class="white-cell" style="width:120px;  '.$lStyle.'">				Régulière			 </td>';
			echo '</tr>';
		}
		foreach($lTalentList as $talent) {
			$type = 'Inconnue'; 
				    if( $talent['type'] == 'MINEURE' ) { $type = 'Mineure'; }
				elseif( $talent['type'] == 'MAJEURE' ) { $type = 'Majeure'; }
				elseif( $talent['type'] == 'PRESTIG' ) { $type = 'Prestige'; }
				elseif( $talent['type'] == 'LEGEND' )  { $type = 'Légendaire'; }
				elseif( $talent['type'] == 'RACIALE' ) { $type = 'Raciale'; }
				elseif( $talent['type'] == 'SPECIAL' ) { $type = 'Spéciale'; }

			echo '<tr>';
			echo '<td class="white-cell" style="width:400px;">'	.$talent['name'].		'</td>';
			echo '<td class="white-cell" style="width:25px;">'	.$talent['quantity'].		'</td>';
			echo '<td class="white-cell" style="width:120px;">'	.$type.				'</td>';
			echo '</tr>';
		}
		echo '<tr><td colspan="3" style="font-size: 0.8em;"><i><b>* UEC :</b> Usages / Éléments / Chads</i></td></tr>';
		echo '</table>';

		echo '<hr width=70% />';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY CHARACTER'S SKILL PRECISION FORM--
	public function DisplayCharacterSkillPrecisionForm()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "Aucun personnage défini!"; return False; }

		// Check for bad religion + magic type combinaisons
		$lReligionCode = $this->Character->GetReligionCode();

		if( ($lReligionCode == 'GOLGOTH' || $lReligionCode == 'CHAOS' || $lReligionCode == 'ADEMOS') && $this->Character->HasSkill('MAGIC1') ) 
			{ $this->Error = "Il n'existe aucune magie cléricale pour la religion de votre personnage!"; return False; }

		elseif( ($lReligionCode == 'AYKA' || $lReligionCode == 'GAEA' || $lReligionCode == 'GALLEON' || $lReligionCode == 'MAKUDAR' || $lReligionCode == 'SIBYL'
			|| $lReligionCode == 'SYLVA' || $lReligionCode == 'USIRE') && $this->Character->HasSkill('MAGIM1') ) { $lReligionCode = 'GOLGOTH'; }
		elseif( ($lReligionCode == 'AMAIRA' || $lReligionCode == 'DAGOTH' || $lReligionCode == 'GODTAKK' || $lReligionCode == 'KAALKH' || $lReligionCode == 'KHALII'
			|| $lReligionCode == 'NOCTAVE' || $lReligionCode == 'OTTOKOM' || $lReligionCode == 'TOYASH') && $this->Character->HasSkill('MAGIM1') ) 
			{ $lReligionCode = 'CHAOS'; }
		elseif( $this->Character->HasSkill('MAGIS1') ) { $lReligionCode = 'ESPRITS'; }

		// Prepare data for the form
		$lSkillID = null;
			    if( isset($_POST['specify-skill']) ) { $lSkillID = $_POST['specify-skill']; }
			elseif( isset($_POST['skillid']) ) { $lSkillID = $_POST['skillid']; }


		$lSkill = $this->Character->GetSkillByID( $lSkillID );
		$lCursesIncluded = False; 
			if( $this->Character->HasSkill('MALEDIC') ) { $lCursesIncluded = True; }
		$lHighMagicIncluded = False; 
			if( $this->Character->HasSkill('HMAGIC') ) { $lHighMagicIncluded = True; }
		$lSkillGroup = "compétence";
		$lPrecisions = array(); 
			    if( substr($lSkill['code'], 0, 4) == 'MAGI' ) 	{ $lPrecisions = $_SESSION['masterlist']->GetFreeMagicLevelSpells($lSkill['level'], $lReligionCode, $lCursesIncluded, $lHighMagicIncluded); $lSkillGroup = "sort"; }
			elseif( substr($lSkill['code'], 0, 4) == 'SORT' ) 	{ $lPrecisions = $_SESSION['masterlist']->GetPermittedSpells($lSkill['code'], $lReligionCode, $lCursesIncluded); $lSkillGroup = "sort"; }
			elseif( $lSkill['code'] == 'MALEDIC' ) 			{ $lPrecisions = $_SESSION['masterlist']->GetPermittedCurses('SORTN1'); $lSkillGroup = "malédiction"; }

			elseif( substr($lSkill['code'], 0, 6) == 'ALCHIM' ) 	{ $lPrecisions = $_SESSION['masterlist']->GetFreeAlchemicLevelRecipes($lSkill['level']); $lSkillGroup = "recette"; }
			elseif( substr($lSkill['code'], 0, 5) == 'HERBO' ) 	{ $lPrecisions = $_SESSION['masterlist']->GetFreeBotanicLevelRecipes($lSkill['level']); $lSkillGroup = "recette"; }
			elseif( substr($lSkill['code'], 0, 5) == 'RECET' ) 	{ $lPrecisions = $_SESSION['masterlist']->GetRecipesBySkill($lSkill['code']); $lSkillGroup = "recette"; }
			elseif( $lSkill['code'] == 'METIER' ) 			{ $lPrecisions = $_SESSION['masterlist']->GetJobs(); $lSkillGroup = "métier"; }

		// If precision is not free text, use a <select> input to limit values to skill's possibilities
		$lPrecisionInput = '<input name="skillprecision" type="text" value="" autocomplete="off" maxlength="50"/>';
		if( $lSkillGroup != "compétence" ) {
			$lPrecisionInput = '<select name="skillprecision">';
			$lPrecisionInput .= '<option></option>';	
			foreach( $lPrecisions as $precision )
			{	
				$level = 0;
					if( isset($precision['skillcode']) ) {
						if(substr($precision['skillcode'], 0, 5) == 'SORTN') { $level = substr($precision['skillcode'], 5, 1); }	
						elseif(substr($precision['skillcode'], 0, 6) == 'RECETA') { $level = substr($precision['skillcode'], 6, 1); }
						elseif(substr($precision['skillcode'], 0, 6) == 'RECETH') { $level = substr($precision['skillcode'], 6, 1); }					
					}

				// Add the correct input format if the skill has no level
				if($level == 0) {$lPrecisionInput .= '<option value="'. $precision['name'] .'">'. $precision['name'] . '</option>';}
				else {$lPrecisionInput .= '<option value="'. $precision['name'] .'">'. $precision['name'] . ' (N'.$level.')'.'</option>';}
				
			}
			$lPrecisionInput .= '</select>';			
		}
		

		// Display!
		echo '<div>';

		// Display!
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character"/>';
		echo '<input type="hidden" name="skillid" value="'.$lSkillID.'"/>';

		echo '<p style="float:center;">Veuillez choisir votre ' .$lSkillGroup. '. Notez que ce choix ne peut <b>pas</b> être modifié par la suite.</p>';
	
		echo '<table>';
		echo '<tr><td class="inputname">Compétence</td>		<td class="labelvalue">' .$lSkill['name']. '</td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Précision</td>		<td class="inputbox">' .$lPrecisionInput. '</td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td colspan="2">';
			echo '<button type="submit" name="option" value="Précision compétence" class="submit-button" />';
			echo 'Enregistrer';
			echo '</button>';
		echo '</td></tr>';
		echo '</table>';

		echo '<hr width=70% />';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY CHARACTER'S POSSESSIONS--
	public function DisplayCharacterPossessions()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "DisplayCharacterPossessions:No character defined!"; return; }


		// Prepare data for the form
		$lEquipmentList = array( array('name' => 'Un bidule', 'quantity' => 3), 
					 array('name' => 'Un truc', 'quantity' => 1),
					 array('name' => 'Pis un autre truc', 'quantity' => 2) 
					); #$this->Character->GetEquipment();
		$lBlueprintList = array( array('name' => 'Schéma : Condensateur', 'quantity' => 1), 
					 array('name' => 'Schéma : Collecteur d\'eau', 'quantity' => 1)
					); #$this->Character->GetSchematics();


		// Display!
		echo '<div>';
		echo '<hr width=70% />';

		// Equipment
		echo '<table style="text-align: left;">';
		echo   '<tr>
			<th class="black-cell" style="width:400px;">Équipement</th>
			<th class="black-cell" style="width:50px;">Qté</th>
			</tr>';

		foreach($lEquipmentList as $gear) {
			echo '<tr>';
			echo '<td class="white-cell" style="width:400px;">' 			. $gear['name'] .	'</td>';
			echo '<td class="white-cell" style="width:50px; text-align: center;">'	. $gear['quantity'] .	'</td>';
			echo '</tr>';
		}
		echo '</table>';
		echo '<hr width=50% />';

		// Schematics
		echo '<table style="text-align: left;">';
		echo   '<tr>
			<th class="black-cell" style="width:400px;">Schémas</th>
			<th class="black-cell" style="width:50px;">Qté</th>
			</tr>';

		foreach($lBlueprintList as $prints) {
			echo '<tr>';
			echo '<td class="white-cell" style="width:400px;">' 			. $prints['name'] .	'</td>';
			echo '<td class="white-cell" style="width:50px; text-align: center;">'	. $prints['quantity'] .	'</td>';
			echo '</tr>';
		}
		echo '</table>';

		echo '<hr width=70% />';

		echo '</div>';
	}


	//--DISPLAY CHARACTER'S TEACHINGS--
	public function DisplayCharacterTeachings()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Prepare data
		$lTeachingList = $this->Character->GetTeachings();
		$lSyllabusList = $this->Character->GetSyllabuses();


		// Display!
		echo '<div>';
		echo '<hr width=70% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Nouvel enseignement" class="smalltext-button" style="width:185px; margin-right:5px;"/>';
		echo 'Nouvel enseignement';
		echo '</button>';

		echo '<button type="submit" name="option" value="Nouveau plan de cours" class="smalltext-button" style="width:185px; margin-left:5px;"/>';
		echo 'Nouveau plan de cours';
		echo '</button>';

		echo '</form>';

		echo '<hr width=60% />';

		// Received teachings
		echo '<span class="section-title">Enseignements reçus</span>';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character-lists"/>';

		echo '<table style="text-align: left;">';
		echo '<tr><th class="black-cell" style="width:200px;">Compétence</th> 
			<th class="black-cell" style="width:155px;">Maitre</th> 
			<th class="black-cell" style="width:80px;">État</th>
			<th class="black-cell" style="width:40px;">Valeur</th>
			<th class="black-cell" style="width:40px;"></th>
		      </tr>';

		foreach($lTeachingList as $i => $teaching) {

			if( $teaching['studentid'] == $this->Character->GetID() ) {

				$lStatus = "Inconnu";
				    if( $teaching['status'] == 'ACTIF' ) { $lStatus = "Actif"; }
				elseif( $teaching['status'] == 'INACT' ) { $lStatus = "Dépensé"; }
				elseif( $teaching['status'] == 'RECOM' ) { $lStatus = "Échangé"; }
				elseif( $teaching['status'] == 'REFUS' ) { $lStatus = "Invalide"; }

				$lButtons = '<button type="submit" name="select-teaching" value="' .$i. '" class="icon-button"/><img src="images/icon_see.png" class="icon-button-image"></button>';
				    if( $teaching['status'] == 'ACTIF' && $teaching['xpvalue']) 
					{ $lButtons .= ' <button type="submit" name="redeem-teaching" value="' .$i. '" class="icon-button"/><img src="images/icon_redeem.png" class="icon-button-image"></button>'; }
				    if( $teaching['status'] == 'ACTIF' && $teaching['activityid'] > 152 /*B5 2020*/) 
					{ $lButtons .= ' <button type="submit" name="cancel-teaching" value="' .$i. '" class="icon-button"/><img src="images/icon_delete.png" class="icon-button-image"></button>'; }


				echo '<tr>';
				echo '<td class="white-cell" style="width:200px;">' . $teaching['skillname'] . '</td>';
				echo '<td class="white-cell" style="width:155px;">' . $teaching['mastername'] . '</td>';
				echo '<td class="white-cell" style="width:80px;">' . $lStatus . '</td>';
				echo '<td class="white-cell" style="width:40px;">' . $teaching['xpvalue'] . 'XP</td>';
				echo '<td class="white-cell" style="width:40px;">' . $lButtons . '</td>';
				echo '</tr>';
			}
		}

		echo '</table>';

		echo '<hr width=60% />';

		// Given teachings
		echo '<span class="section-title">Enseignements donnés</span>';

		echo '<table style="text-align: left;">';
		echo '<tr><th class="black-cell" style="width:250px;">Compétence</th> 
			<th class="black-cell" style="width:155px;">Élève</th> 
			<th class="black-cell" style="width:95px;">Activité</th>
			<th class="black-cell" style="width:40px;"></th>
		      </tr>';

		foreach($lTeachingList as $i => $teaching) {

			if( $teaching['masterid'] == $this->Character->GetID() ) {

				$lButtons = '<button type="submit" name="select-teaching" value="' .$i. '" class="icon-button"/><img src="images/icon_see.png" class="icon-button-image"></button>';
				    if( $teaching['status'] == 'ACTIF' && $teaching['activityid'] > 152 /*B5 2020*/) 
					{ $lButtons .= ' <button type="submit" name="cancel-teaching" value="' .$i. '" class="icon-button"/><img src="images/icon_delete.png" class="icon-button-image"></button>'; }


				echo '<tr>';
				echo '<td class="white-cell" style="width:250px;">' . $teaching['skillname'] . '</td>';
				echo '<td class="white-cell" style="width:155px;">' . $teaching['studentname'] . '</td>';
				echo '<td class="white-cell" style="width:95px;">' . $teaching['activityname'] . '</td>';
				echo '<td class="white-cell" style="width:40px;">' . $lButtons . '</td>';
				echo '</tr>';
			}
		}

		echo '</table>';

		echo '<hr width=60% />';

		// Course syllabuses
		echo '<span class="section-title">Plans de cours</span>';

		echo '<table style="text-align: left;">';
		echo '<tr><th class="black-cell" style="width:200px;">Compétence</th> 
			<th class="black-cell" style="width:80px;">État</th>
			<th class="black-cell" style="width:282px;">Raison</th>
		      </tr>';

		foreach($lSyllabusList as $i => $syllabus) {

			$lStatus = "";
				    if( $syllabus['status'] == 'DEM' )   { $lStatus = "Demandé"; }
				elseif( $syllabus['status'] == 'ACTIF' ) { $lStatus = "Approuvé"; }
				elseif( $syllabus['status'] == 'INACT' ) { $lStatus = "Refusé"; }

			$lReason = "";
				if( $syllabus['status'] == 'INACT' ) { $lReason = $syllabus['reason']; }

			echo '<tr>';
			echo '<td class="white-cell" style="width:200px;">' . $syllabus['skillname'] . '</td>';
			echo '<td class="white-cell" style="width:80px;">' . $lStatus . '</td>';
			echo '<td class="white-cell" style="width:282px;">' . $lReason . '</td>';
			echo '</tr>';
		}

		echo '</table>';

		echo '</form>';

		echo '<hr width=70% />';
		echo '</div>';
	}


	//--DISPLAY SELECTED TEACHING DETAIL--
	public function DisplaySelectedTeaching()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Prepare data
		$index = $_POST['select-teaching'];
		$lTeaching = $this->Character->GetTeachings()[$index];


		// Display!
		echo '<div>';
		echo '<span class="section-title">Détail de l\'enseignement</span>';
		echo '<hr width=70% />';

		// Teaching
		if( $lTeaching['status'] == 'REFUS' ) {	echo '<span class="section-title" style="color:red;">*** REFUSÉ! ***</span>'; }

		echo '<table>';
		echo '<tr><td class="labelname">Maître</td>		<td class="labelvalue" style="width: 450px;">' .$lTeaching['mastername']. '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Élève</td>		<td class="labelvalue" style="width: 450px;">' .$lTeaching['studentname']. '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Compétence</td>		<td class="labelvalue" style="width: 450px;">' .$lTeaching['skillname']. '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Activité</td>		<td class="labelvalue" style="width: 450px;">' .$lTeaching['activityname']. '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Lieu</td>		<td class="labelvalue" style="width: 450px;">' .$lTeaching['place']. '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Moment</td>		<td class="labelvalue" style="width: 450px;">' .$lTeaching['moment']. '</td></tr>';

		echo '</table>';

		echo '<hr width=70% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Enseignements personnage" class="smalltext-button"/>';
		echo 'Retour';
		echo '</button>';

		echo '</form>';


		echo '</div>';
	}


	//--DISPLAY CHARACTER'S STORY--
	public function DisplayCharacterBackground()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Prepare data
		$lBG = "<i>Saisissez le <u>résumé</u> de l'histoire de votre personnage.</i>";
		$lStyle = "padding-top: 0px;";
		if( $this->Character->GetBackground() ) { 
			$lBG = $this->Character->GetBackground(); 
			$lStyle = "padding-top: 0px; text-align: left;";
		}

		// Display!
		echo '<div>';
		echo '<hr width=70% />';

		echo '<span class="note" style="'.$lStyle.'">'.nl2br($lBG).'</span>';

		echo '<hr width=70% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Éditer histoire personnage" class="smalltext-button" style="margin-right: 5px;"/>';
		echo 'Éditer';
		echo '</button>';

		echo '<button type="submit" name="option" value="Soumettre histoire personnage" class="smalltext-button" style="margin-left: 5px; margin-right: 5px;"/>';
		echo 'Soumettre';
		echo '</button>';

		echo '<button type="submit" name="option" value="Commentaires histoire personnage" class="smalltext-button" style="margin-left: 5px;"/>';
		echo 'Commentaires';
		echo '</button>';

		echo '</form>';

		echo '</div>';

	}

	//--DISPLAY CHARACTER'S STORY-EDITING FORM--
	public function DisplayBackgroundEditingForm()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Prepare data


		// Display!
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character"/>';

		echo '<hr width=70% />';

		echo '<div style="margin: auto; width: 620px;"><textarea name="background" cols="72" rows="18">' . $this->Character->GetBackground() . '</textarea></div>';	

		echo '<hr width=70% />';

		echo '<button type="submit" name="option" value="Histoire" class="submit-button" />';
		echo 'Enregistrer';
		echo '</button>';

		echo '<span class="note"><b>Note :</b> Vous pouvez agrémenter votre texte à l\'aide des balises HTML &lt;p&gt; (paragraphe), &lt;b&gt; (gras), &lt;i&gt; (italique) et &lt;u&gt; (souligné).</span>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY CHARACTER'S STORY SUBMITING FORM--
	public function DisplayBackgroundSubmitingForm()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Prepare data
		$lApprovalList = $this->Character->GetApprovals();
		$lAlreadyRequested = False;
		foreach($lApprovalList as $request) {
			if( $request->GetSubject() == 'Histoire' && $request->GetStatus() == 'DEM' ) { $lAlreadyRequested = True; break; }
		}


		// Display!
		echo '<div>';
		echo '<hr width=70% />';

		if( $lAlreadyRequested ) {
			echo '<span class="note" style="padding-top: 0px;"><i>Vous avez déjà demandé une approbation qui n\'a pas encore été traitée.</i></span>';
			echo '<hr width=70% />';
			echo '</div>';
			return;
		}

		echo '<span class="note">Soumettre l\'histoire de son personnage signifie que vous avez complété celle-ci et que vous êtes prêt à la faire valider par l\'Organisation. Lorsque ce sera fait, vous recevrez par courriel une réponse, ainsi que des commentaires si votre histoire a besoin d\'ajustements. Vous pouvez également voir cette réponse en appuyant sur le bouton "COMMENTAIRES" situé au bas de l\'interface précédente.</span>';
		echo '<span class="note">Faire approuver d\'avance l\'histoire d\'un nouveau personnage donne 5 points d\'expérience à celui-ci.</span>';
		echo '<span class="note">Voulez-vous envoyer une demande d\'approbation pour votre histoire ?</span>';

		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character"/>';

		echo '<button type="submit" name="option" value="Approbation histoire" class="submit-button" style="margin-right: 5px;" />';
		echo 'Oui';
		echo '</button>';

		echo '<button type="submit" name="option" value="Retour histoire" class="submit-button" style="margin-left: 5px;" />';
		echo 'Non';
		echo '</button>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY COMMENTS ON BACKGROUND--
	public function DisplayBackgroundComments()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Prepare data
		$lApprovalList = $this->Character->GetApprovals();


		// Display!
		echo '<div>';
		echo '<hr width=70% />';

		foreach($lApprovalList as $request){
			if( $request->GetSubject() == 'Histoire' ) {
				$lStatus = "En attente...";
				    if( $request->GetStatus() == 'REFUS' ) { $lStatus = "Refusée."; }
				elseif( $request->GetStatus() == 'ACCEP' ) { $lStatus = "Approuvée!"; }

				echo '<div style="margin: auto; margin-top: 5px; margin-bottom: 5px; width: 620px; text-align: left;">';
				echo '<b>Demande d\'approbation faite le : </b><span class="info">' 	.str_replace(":", "h", substr($request->GetRequestDate(), 0, 16)). 	'</span><br />';
				echo '<b>Réponse de l\'Organisation : </b><span class="info">' 		.$lStatus. 	'</span><br />';
				echo '<b>Commentaires : </b><br />';
				echo '<span class="admin-comment">' .$request->GetComments(). '</span>';
				echo '</div>';
			}
		}

		echo '<hr width=70% />';
		echo '</div>';
	}


	//--DISPLAY CHARACTER'S TITLES--
	public function DisplayCharacterTitles()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Prepare data
		$lTitleList = $this->Character->GetTitles();


		// Display!
		echo '<div>';
		echo '<hr width=70% />';

		echo '<table style="text-align: left;">';
		echo '<tr><th class="black-cell" style="width:100px;">Titre</th> 
			<th class="black-cell" style="width:100px;">Royaume</th> 
			<th class="black-cell" style="width:350px;">Avantages</th>
		      </tr>';

		foreach($lTitleList as $title) {
			echo '<tr>';
			echo '<td class="white-cell" style="width:100px;">' . $title['title'] . '</td>';
			echo '<td class="white-cell" style="width:100px;">' . $title['kingdom'] . '</td>';
			echo '<td class="white-cell" style="width:350px;">' . $title['bonus'] . '</td>';
			echo '</tr>';
		}

		echo '</table>';

		echo '<hr width=70% />';

		echo '</div>';
	}


	//--DISPLAY CHARACTER'S QUESTS--
	public function DisplayCharacterQuests()
 	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Prepare data
		$lBackground = $this->Character->GetBackground();
		$lQuestList = $this->Character->GetQuests();


		// Display!
		echo '<div>';
		echo '<hr width=70% />';

		if( !$lBackground ) {
			echo '<span class="note" style="padding-top: 0px;"><i>Vous devez avoir écrit l\'histoire de votre personnage avant de pouvoir demander des quêtes!</i></span>';
			echo '<hr width=70% />';
			echo '</div>';
			return;
		}

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Nouvelle quête personnelle" class="smalltext-button"/>';
		echo 'Nouvelle';
		echo '</button>';

		echo '</form>';

		// Quests
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character-lists"/>';

		echo '<table style="text-align: left;">';
		echo '<tr><th class="black-cell" style="width:10px;">#</th>
			  <th class="black-cell" style="width:260px;">Objet de la quête</th> 
			  <th class="black-cell" style="width:80px;">État</th>
			  <th class="black-cell" style="width:40px;"></th></tr>';

		foreach($lQuestList as $i => $quest) {
			$line = $i + 1;

			$lStyle = "";
			    if( $quest->GetStatus() == 'ACTIF' ) 
			    	{ $lStyle = "background-color: lightblue; font-weight: bold;"; }
			elseif( $quest->GetStatus() == 'REFUS' || $quest->GetStatus() == 'RECOM' ) 
				{ $lStyle = "background-color: lightgray; color: gray;"; }

			$lStatus = "Inconnu";
			    if( $quest->GetStatus() == 'DEM' )   { $lStatus = "Demande"; }
			elseif( $quest->GetStatus() == 'REPR' )  { $lStatus = "Reprise"; }
			elseif( $quest->GetStatus() == 'ACTIF' ) { $lStatus = "Active"; }
			elseif( $quest->GetStatus() == 'TERM'  ) { $lStatus = "Terminée"; }
			elseif( $quest->GetStatus() == 'RECOM' ) { $lStatus = "Récompensée"; }
			elseif( $quest->GetStatus() == 'ANNUL' ) { $lStatus = "Annulée"; }
			elseif( $quest->GetStatus() == 'REFUS' ) { $lStatus = "Refusée"; }
			elseif( $quest->GetStatus() == 'SUITE' ) { $lStatus = "Suivante"; }

			$lButtons = '<button type="submit" name="select-quest" value="' .$i. '" class="icon-button"/><img src="images/icon_see.png" class="icon-button-image"></button>';
			    if( in_array($quest->GetStatus(), array('DEM', 'REPR', 'SUITE', 'ACTIF' )) ) 
				{ $lButtons .= ' <button type="submit" name="cancel-quest" value="' .$quest->GetID(). '" class="icon-button"/><img src="images/icon_delete.png" class="icon-button-image"></button>'; }
			elseif( $quest->GetStatus() == 'ANNUL' ) 
				{ $lButtons .= ' <button type="submit" name="restore-quest" value="' .$quest->GetID(). '" class="icon-button"/><img src="images/icon_restore.png" class="icon-button-image"></button>'; }


			echo '<tr>';
			echo '<td class="grey-cell" style="width:10px;">' 			.$line.				'</td>';
			echo '<td class="white-cell" style="width:260px; '.$lStyle.'">' 	.$quest->GetSubject().		'</td>';
			echo '<td class="white-cell" style="width:80px;  '.$lStyle.'">'		.$lStatus.			'</td>';
			echo '<td class="white-cell" style="width:40px;  '.$lStyle.'">'		.$lButtons.			'</td>';
			echo '</tr>';
		}
		echo '</table>';

		echo '</form>';
		echo '<hr width=70% />';

		echo '</div>';
	}

	
	//--DISPLAY QUEST'S CREATION FORM--
	public function DisplayQuestRequestForm()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }

		// Prepare data
		$lKingdoms = $_SESSION['masterlist']->GetKingdoms();
		$lCounties = $_SESSION['masterlist']->GetCounties();

		$lOptions = $_SESSION['masterlist']->GetQuestOptions();
		$lMinorTalents = $_SESSION['masterlist']->GetMinorTalents();
		$lMajorTalents = $_SESSION['masterlist']->GetMajorTalents();
		$lTitles = $_SESSION['masterlist']->GetPrestigeTitles();

		$lCurrentSection = "";

		$lSubjectInput = '<select name="quest"><option value="ND|S.O.|0">Veuillez choisir une quête...</option>';
		foreach ($lOptions as $option) {
			// Add section title on it's first ite,
			if( $lCurrentSection != $option['section'] ) { $lSubjectInput .= '<optgroup label="'.$option['section'].'">'; $lCurrentSection = $option['section']; }

			    if( $option['code'] == "MINEURE" )
				{ foreach ($lMinorTalents as $talent) 
					{ $lSubjectInput .= '<option value="'.$option['code'].'|'.$talent['code'].'|'.$talent['name'].'">'.$talent['name'].'</option>'; }
				}
			elseif( $option['code'] == "MAJEURE" )
				{ foreach ($lMajorTalents as $talent) 
					{ $lSubjectInput .= '<option value="'.$option['code'].'|'.$talent['code'].'|'.$talent['name'].'">'.$talent['name'].'</option>'; }
				}
			elseif( $option['code'] == "TITREP" )
				{ foreach ($lTitles as $title) 
					{ $lSubjectInput .= '<option value="'.$option['code'].'|'.$title['code'].'|'.$title['name'].'">'.$title['name'].'</option>'; }
				}
			else	{ 
					if( $option['code'] == 'GAINXP' && $this->Character->GetLevel() < 5 ) { continue; }
					$lSubjectInput .= '<option value="'.$option['code'].'||'.$option['name'].'">'.$option['name'].'</option>'; 
				}

		}
		$lSubjectInput .= '</select>';


		$lCountyInput = '<select name="county" onchange="displayCountyInformationJS(this.value)">';
		$lCountyInput .= '<option>Veuillez choisir...</option>';	
		foreach( $lKingdoms as $kingdom )
		{	
			if( !$kingdom['questgiver'] ) { continue; }

			$lCountyInput .= '<optgroup label="' . $kingdom['name'] . '">';			
			foreach( $lCounties as $county )
			{					    
				if( $county['questgiver'] && $county['status'] == 'ACTIF' && $county['kingdomcode'] == $kingdom['code'] ) 
				{
					//Value is "Leader;LDescription;Scribe;Name". The first 3 are for the JS, the other one for the controller.
					$lCountyInput .= '<option value="'. $county['leader'] .';'. $county['leaderdescription'] .';'. $county['scribe'] .';'. $county['id'] .'">'. $county['name'] .' </option>';
				}	    
			}
			$lCountyInput .= '</optgroup>';
		}
		$lCountyInput .= '</select>';
		
		$lSuggestions = "";
			if( isset($_POST['suggestions']) ) { $lSuggestions = $_POST['suggestions']; }


		// Display!
		echo '<div>';
		echo '<hr width=70% />';

		// Quests
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character"/>';
		
		if(WORLD == 'BELE'){
		echo '<div style="width:70%; margin:auto">';
		echo '<p align="left"><b>Notez que de demander une quête personnelle avant que votre personnage n\'ait atteint le niveau 3 lui retire toutes ses <i>Shadowlives</i>.</b></p>';
		echo '<p align="left"><b>Veuillez également noter que les quêtes suivantes ne peuvent plus être demandées via ce système: </b></p>';
		echo '<div style="margin-left:50px;" align="left">- Quête rôleplay : Remplacées par les Trames de chaque comté.</div >';
		echo '<div style="margin-left:50px;" align="left">- Quête pour prier Golgoth ou Chaos : Ne sont plus disponibles.</div >';
		echo '<div style="margin-left:50px;" align="left">- Quête mythique : Mises en jeu selon l\'intérêt de plusieurs groupes.</div>';
		echo '<p align="left">Pour toutes questions, veuillez envoyer un courriel à <b>« Quetes@Terres-de-Belenos.com »</b>.</p>';
		echo '<p></p>';
		echo '</div>';		
		}
		
		echo '<div>';
		echo '<table>';
		echo '<tr><td class="inputname"  style="float:left;">Objet de la demande</td>	<td class="inputbox" style="width:215px;">' . $lSubjectInput . '</td></tr>';
		if(QUESTS_COUNTY_ENABLED) {
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname"  style="float:left;">Comté ciblé</td>		<td class="inputbox" style="width:215px;">' . $lCountyInput . '</td></tr>';			
		} else { echo '<input type="hidden" name="county" value="N.A.;N.A.;N.A.;0"/>';}
		echo '</table>';
		echo '</div>';
		
		echo '<tr class="filler"></tr>';
		
		if(QUESTS_COUNTY_ENABLED) {
		echo '<span id="leadername"><b>Dirigeant du comté sélectionné :</b></span>';
		echo '<span id="leaderinfo"><b>Description du dirigeant :</b></span>';
		echo '<span id="scribename"><b>Scribe :</b></span>';
		}

		echo '<div style="margin: auto; margin-top: 10px; margin-bottom: 10px; width: 620px;"><textarea name="suggestions" cols="72" rows="18" placeholder="Entrez vos suggestions ici!">' . $lSuggestions . '</textarea></div>';	

		echo '<button type="submit" name="option" value="Quête demandée" class="submit-button" />';
		echo 'Demander';
		echo '</button>';

		echo '<span class="note"><b>Note :</b> Vous pouvez agrémenter votre texte à l\'aide des balises HTML &lt;p&gt; (paragraphe), &lt;b&gt; (gras), &lt;i&gt; (italique) et &lt;u&gt; (souligné).</span>';

		echo '</form>';
		echo '<hr width=70% />';

		echo '</div>';
	}


	//--DISPLAY SELECTED QUEST DETAIL--
	public function DisplaySelectedQuest()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Prepare data
		$index = $_POST['select-quest'];
		$lQuest = $this->Character->GetQuests()[$index];

		$lStatus = "En attente d'approbation";
			    if( $lQuest->GetStatus() == 'ACTIF' ) { $lStatus = "Approuvée et en cours"; }
			elseif( $lQuest->GetStatus() == 'TERM' )  { $lStatus = "Quête terminée"; }
			elseif( $lQuest->GetStatus() == 'RECOM' ) { $lStatus = "Quête récompensée"; }
			elseif( $lQuest->GetStatus() == 'ANNUL' ) { $lStatus = "Annulée par le joueur"; }
			elseif( $lQuest->GetStatus() == 'REFUS' ) { $lStatus = "Refusée par l'Organisation"; }
			elseif( $lQuest->GetStatus() == 'SUITE' ) { $lStatus = "Suite de la quête en cours"; }

		$lCreditCode = $lQuest->GetRewardCode() ? $lQuest->GetRewardCode() : $lQuest->GetOptionCode();
		$lCredits = 0;
			if( isset($this->Character->GetQuestCredits()[$lCreditCode]) ) { $lCredits = $this->Character->GetQuestCredits()[$lCreditCode]; }

		$lText = "Votre quête deviendra disponible une fois votre présence au GN confirmée.";
			$lActivity = $_SESSION['masterlist']->GetActivityByID( $lQuest->GetActivity()->GetID() );	// False if part has no ActivityID

			if( $lActivity == False || $this->Character->HasAttendance($lActivity->GetID()) ) {
				$lText = $lQuest->GetText();
			}					


		// Display!
		echo '<div>';
		echo '<span class="section-title">Détail de la quête</span>';
		echo '<hr width=80% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character-lists"/>';
		echo '<input type="hidden" name="select-quest" value="'.$index.'"/>';

		// Quests
		echo '<table>';
		echo '<tr><td class="labelname">Objet de la quête</td>		<td class="labelvalue" style="width: 450px;">' .$lQuest->GetSubject(). '</td></tr>';
		if(QUESTS_COUNTY_ENABLED) {
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Comté choisi</td>		<td class="labelvalue" style="width: 450px;">' .$lQuest->GetCountyName(). '</td></tr>';
		}
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">État</td>			<td class="labelvalue" style="width: 450px;">' .$lStatus. '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Date de la demande</td>		<td class="labelvalue" style="width: 450px;">' .str_replace(":", "h", substr($lQuest->GetRequestDate(), 0, 16)). '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Date d\'approbation</td>	<td class="labelvalue" style="width: 450px;">' .str_replace(":", "h", substr($lQuest->GetApprovalDate(), 0, 16)). '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Crédits accumulés</td>		<td class="labelvalue" style="width: 450px;">' .$lCredits. ' crédits</td></tr>';

		echo '<tr><td colspan="2"><hr style="margin-top: 10px;" /></td></tr>';

		echo '<tr><td colspan="2" class="labelname" style="text-align: center;">Suggestions données</td></tr>';
		echo '<tr><td colspan="2"><div style="margin: auto; margin-top: 10px; margin-bottom: 10px; width: 620px; text-align:left;"><span>' .nl2br( $lQuest->GetSuggestions() ). '</span></div></td></tr>';	

		echo '<tr><td colspan="2"><hr style="margin-top: 10px;" /></td></tr>';

		echo '<tr><td colspan="2" class="labelname" style="text-align: center;">Quête reçue</td></tr>';
		echo '<tr><td colspan="2"><div style="margin: auto; margin-top: 10px; margin-bottom: 10px; width: 620px; text-align:left;"><span>' .nl2br( $lText ). '</span></div></td></tr>';	

		echo '<tr><td colspan="2"><hr style="margin-top: 10px;" /></td></tr>';

		echo '<tr><td colspan="2" class="labelname" style="text-align: center;">Commentaires des Scripteurs</td></tr>';
		echo '<tr><td colspan="2"><div style="margin: auto; margin-top: 10px; margin-bottom: 10px; width: 620px; text-align:left;"><span>' .nl2br( $lQuest->GetComments() ). '</span></div></td></tr>';	
		echo '</table>';


		echo '<hr width=80% />';

		echo '</form>';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Quêtes personnage" class="smalltext-button"/>';
		echo 'Retour';
		echo '</button>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY SELECTED QUEST PART DETAIL-- ***DECRECATED***
	public function DisplaySelectedQuestPart()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Prepare data
		$lQuestIndex = $_POST['select-quest'];
		$lPartIndex = $_POST['select-questpart'];
		$lPart = $this->Character->GetQuests()[$lQuestIndex]->GetParts()[$lPartIndex];

		$lQuestSubject = $this->Character->GetQuests()[$lQuestIndex]->GetSubject();

		$lStatus = "En rédaction";
			    if( $lPart->GetStatus() == 'ACTIF' ) { $lStatus = "Approuvée et en cours"; }
			elseif( $lPart->GetStatus() == 'TERM' ) { $lStatus = "Terminée"; }
			elseif( $lPart->GetStatus() == 'ANNUL' ) { $lStatus = "Annulée"; }


		// Display!
		echo '<div>';
		echo '<span class="section-title">'.$lQuestSubject.' - Partie '.$lPart->GetNumber().'</span>';
		echo '<hr width=80% />';

		// Part
		echo '<div style="margin: auto; margin-top: 10px; margin-bottom: 10px; width: 620px; text-align:left;"><span>' .nl2br( $lPart->GetDescription() ). '</span></div>';	
		echo '<hr width=80% />';

		echo '<table>';
		echo '<tr><td class="labelname">Écris par</td>			<td class="labelvalue" style="width: 450px;">' .$lPart->GetScriptor()->GetFullName(). '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Date</td>			<td class="labelvalue" style="width: 450px;">' .str_replace(":", "h", substr($lPart->GetCreationDate(), 0, 16)). '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">État</td>			<td class="labelvalue" style="width: 450px;">' .$lStatus. '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td colspan="2" class="labelname" style="text-align: center;">Commentaires</td></tr>';
		echo '</table>';

		echo '<div style="margin: auto; margin-top: 10px; margin-bottom: 10px; width: 620px; text-align:left;"><span>' .nl2br( $lPart->GetComments() ). '</span></div>';	

		echo '<hr width=80% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character-lists"/>';

		echo '<button type="submit" name="select-quest" value="'.$lQuestIndex.'" class="smalltext-button"/>';
		echo 'Retour';
		echo '</button>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY CHARACTER'S RESUMES' LIST--
	public function DisplayCharacterResumes()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Prepare data
		$lResumeList = $this->Character->GetResumes();


		// Display!
		echo '<div>';
		echo '<hr width=70% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Nouveau résumé personnage" class="smalltext-button"/>';
		echo 'Nouveau';
		echo '</button>';

		echo '</form>';

		// Resumés
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character-lists"/>';

		echo '<table style="text-align: left;">';
		echo '<tr><th class="black-cell" style="width:100px;">Activité</th>
			  <th class="black-cell" style="width:260px;">Quête</th> 
			  <th class="black-cell" style="width:120px;">Création</th>
			  <th class="black-cell" style="width:40px;"></th></tr>';

		foreach($lResumeList as $i => $resume) {

			$lQuest = "S.O.";
				if( $resume->GetQuest()->GetID() ) { 
					$lQuest = $resume->GetQuest()->GetSubject(); 
				}

			$lButton = 	'<button type="submit" name="select-resume" value="' .$i. '" class="icon-button"/><img src="images/icon_see.png" class="icon-button-image"></button>
					 <button type="submit" name="edit-resume" value="' .$i. '" class="icon-button"/><img src="images/icon_edit.png" class="icon-button-image"></button>';


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


	//--DISPLAY NEW RESUMÉ'S CREATION FORM--
	public function DisplayResumeCreationForm()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Prepare data
		$lActivityList = $this->Character->GetCharacterAttendances();
			if( !$lActivityList ) { $lActivityList = array(); }

		$lQuestList = $this->Character->GetQuests();
			if( !$lQuestList ) { $lQuestList = array(); }

		$lActivityInput = '<select name="activity"><option value="" selected></option>';
		foreach ($lActivityList as $activity) {
			$lActivityInput .= '<option value="' .$activity->GetID(). '">' .$activity->GetName(). '</option>';
		}
		$lActivityInput .= '</select>';

		$lQuestInput = '<select name="quest"><option value="-" selected>Aucune</option>';
		foreach ($lQuestList as $quest) {
			if( $quest->GetStatus() == 'ACTIF' ) {
				$lQuestInput .= '<option value="'.$quest->GetID().'">' .$quest->GetSubject(). '</option>';
			}
		}
		$lQuestInput .= '</select>';

		$lObjectiveInput1 = "";
			if( isset($_POST['objective1']) ) { $lObjectiveInput1 = $_POST['objective1']; }

		$lObjectiveInput2 = "";
			if( isset($_POST['objective2']) ) { $lObjectiveInput2 = $_POST['objective2']; }

		$lObjectiveInput3 = "";
			if( isset($_POST['objective3']) ) { $lObjectiveInput3 = $_POST['objective3']; }

		$lText = "";
			if( isset($_POST['text']) ) { $lText = $_POST['text']; }


		// Display!
		echo '<div>';
		echo '<hr width=70% />';

		// Quests
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character"/>';

		echo '<span class="note"><b><u>IMPORTANT</b></u><br />Il vous est <b>fortement</b> recommandé d\'écrire d\'abord vos textes à l\'aide d\'un logiciel de traitement de texte (ex.: Word) et de les conservés sur votre ordinateur.</span>';

		echo '<table>';
		echo '<tr><td class="inputname">Activité</td>		<td class="inputbox" style="width: 165px;">' . $lActivityInput . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Quête</td>		<td class="inputbox" style="width: 165px;">' . $lQuestInput . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '</table>';

		echo '<table>';
		echo '<tr><td class="inputname">Objectif #1</td>		
			<td class="inputbox"><input name="objective1" type="text" value="' . $lObjectiveInput1 . '" maxlength="200" style="width: 455px;"/></td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Objectif #2</td>		
			<td class="inputbox"><input name="objective2" type="text" value="' . $lObjectiveInput2 . '" maxlength="200" style="width: 455px;"/></td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Objectif #3</td>		
			<td class="inputbox"><input name="objective3" type="text" value="' . $lObjectiveInput3 . '" maxlength="200" style="width: 455px;"/></td></tr>';
		echo '</table>';

		echo '<div style="margin: auto; margin-top: 10px; margin-bottom: 10px; width: 620px;"><textarea name="text" cols="72" rows="18" placeholder="Entrez votre résumé ici! La forme d\'une missive est préférée. Autrement, tenez-vous en aux informations pertinentes aux rédacteurs, qui leur permettent de personnaliser vos quêtes.">' . $lText . '</textarea></div>';	

		echo '<button type="submit" name="option" value="Créer résumé" class="submit-button" style="margin-right:5px;"/>';
		echo 'Enregistrer';
		echo '</button>';

		echo '<button type="submit" name="option" value="Retour résumés" class="submit-button" style="margin-left:5px;"/>';
		echo 'Annuler';
		echo '</button>';

		echo '<span class="note"><b>Note :</b> Vous pouvez agrémenter votre texte à l\'aide des balises HTML &lt;p&gt; (paragraphe), &lt;b&gt; (gras), &lt;i&gt; (italique) et &lt;u&gt; (souligné).</span>';

		echo '</form>';
		echo '<hr width=70% />';

		echo '</div>';
	}


	//--DISPLAY SELECTED RESUMÉ'S DETAIL--
	public function DisplaySelectedResume()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Prepare data
		$index = $_POST['select-resume'];
		$lResume = $this->Character->GetResumes()[$index];
		$lQuest = False;
			if( $lResume->GetQuest()->GetID() ) 		{ $lQuest = $lResume->GetQuest()->GetSubject(); }


		// Display!
		echo '<div>';
		echo '<span class="section-title">Résumé pour '.$lResume->GetActivity()->GetName().'</span>';
		echo '<hr width=70% />';


		// Resumé
		if( $lQuest ) {
			echo '<span><b>Lien avec la quête : </b>'. $lQuest .'</span>';
			echo '<hr width=70% />';
		}
		echo '<div style="margin: auto; margin-top: 10px; margin-bottom: 10px; width: 620px; text-align: left;"><span>' .nl2br( $lResume->GetText() ). '</span></div>';	

		echo '<hr width=70% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Résumés personnage" class="smalltext-button"/>';
		echo 'Retour';
		echo '</button>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY NEW RESUMÉ'S MODIFICATION FORM--
	public function DisplayResumeModificationForm()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Prepare data
		$index = $_POST['edit-resume'];
		$lResume = $this->Character->GetResumes()[$index];
		$lActivity = $lResume->GetActivity(); 
		$lQuestID = $lResume->GetQuest()->GetID();

		$lQuestList = $this->Character->GetQuests();
			if( !$lQuestList ) { $lQuestList = array(); }

		$lQuestInput = '<select name="quest"><option value="-">Aucune</option>';
		foreach ($lQuestList as $quest) {
			if( $quest->GetStatus() == 'ACTIF' ) {
				$selected = ""; if( $quest->GetID() == $lQuestID ) { $selected = "selected"; }

				$lQuestInput .= '<option value="'.$quest->GetID().'" ' .$selected. '>' .$quest->GetSubject(). '</option>';
			}
		}
		$lQuestInput .= '</select>';

		$lText = $lResume->GetText();
			if( isset($_POST['text']) ) { $lText = $_POST['text']; }


		// Display!
		echo '<div>';
		echo '<hr width=70% />';

		// Quests
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character"/>';
		echo '<input type="hidden" name="resumeid" value="' .$lResume->GetID(). '"/>';

		echo '<table>';
		echo '<tr><td class="inputname">Activité</td>		<td class="labelvalue" style="width: 165px;">' . $lActivity->GetName() . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Quête</td>		<td class="inputbox" style="width: 165px;">' . $lQuestInput . '</td></tr>';
		echo '</table>';

		echo '<div style="margin: auto; margin-top: 10px; margin-bottom: 10px; width: 620px;"><textarea name="text" cols="72" rows="18" placeholder="Entrez votre résumé ici! Tenez-vous en aux informations pertinentes aux rédacteurs, qui leur permettent de personnaliser vos quêtes.">' . $lText . '</textarea></div>';	

		echo '<button type="submit" name="option" value="Modifier résumé" class="submit-button" style="margin-right:5px;"/>';
		echo 'Mettre à jour';
		echo '</button>';

		echo '<button type="submit" name="option" value="Retour résumés" class="submit-button" style="margin-left:5px;"/>';
		echo 'Annuler';
		echo '</button>';

		echo '<span class="note"><b>Note :</b> Vous pouvez agrémenter votre texte à l\'aide des balises HTML &lt;p&gt; (paragraphe), &lt;b&gt; (gras), &lt;i&gt; (italique) et &lt;u&gt; (souligné).</span>';

		echo '</form>';
		echo '<hr width=70% />';

		echo '</div>';
	}


	//--DISPLAY CHARACTER'S LIFE DETAIL--
	public function DisplayCharacterLife()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Prepare data for the form
		$lLifeList = $this->Character->GetLife();

		// Display the title.
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<hr width=70% />';

		// Display data table
		echo '<table>';
		echo '<tr>
			<th class="black-cell" style="width:10px;">#</th> 
			<th class="black-cell" style="width:200px;">Raison</th>
			<th class="black-cell" style="width:50px;">PV</th>
		      </tr>';

		foreach($lLifeList as $i => $lifemod) { 
			$line = $i+1;
			$amount = $lifemod['life']; if( $amount > 0 && $lifemod['reason'] <> 'PV de départ' ) { $amount = "+".$amount; }
			echo '<tr>';
			echo '<td class="grey-cell" style="width:10px;">' 			.$line.			'</td>';
			echo '<td class="white-cell" style="width:200px;text-align:left">' 	.$lifemod['reason'].	'</td>';
			echo '<td class="white-cell" style="width:50px;">' 			.$amount.		'</td>';
			echo '</tr>';
		}

		echo '<tr class="filler"></tr>';
		echo '</table><br />';

		// Submenu
		echo '<span class="note"><b>Important :</b> Avec l\'installation de boîtes aux lettres dans tous les campements, il n\'est désormais plus possible de déclarer sa mort via cette interface. Si vous avez oublié de déposer un papier de Premiers soins, de Résurrection ou encore le réceptacle de Haute magie qui a servi à votre retour à la vie, veuillez simplement envoyer une photo de celui-ci à « Organisation@Terres-de-Belenos.com ». Merci! </span>';

		echo '<hr width=70% />';
		echo '</div>';
	}


	//--DISPLAY CHARACTER'S EXP TRANSFER FORM--
	public function DisplayCharacterXPTransfer()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Prepare data for the form
		$lPlayerXP = 0;		
		if( isset($_SESSION['player']) ) { $lPlayerXP = $_SESSION['player']->GetTotalExperience(); }

		$lTransferedXP = $this->Character->GetTransferedExperience();
		if( !$lTransferedXP ) { $lTransferedXP = 0; }


		// Display form
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character"/>';

		echo '<hr width=70% />';

		echo '<table>';		
		echo '<tr><td class="inputname">Expérience du joueur :</td>	<td class="labelvalue">' .$lPlayerXP. '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Transféré cette année :</td>	<td class="labelvalue">' .$lTransferedXP. ' / '.MAX_XP_TRANSFER.'</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">À transférer :</td>		<td class="inputbox"><input name="xp" type="text" value="" maxlength="3"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td colspan="2">';
			echo '<button type="submit" name="option" value="Transfert XP" class="submit-button" />';
			echo 'Transfert';
			echo '</button>';
		echo '</td></tr>';
		echo '</form>';

		echo '<tr class="filler"></tr>';
		echo '<tr><td colspan="2"><hr /></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';
		echo '<tr><td colspan="2">';
			echo '<button type="submit" name="option" value="Détail expérience personnage" class="submit-button" />';
			echo 'Détails de l\'XP';
			echo '</button>';
		echo '</td></tr>';
		echo '</table>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY CHARACTER'S EXP DETAIL--
	public function DisplayCharacterXPDetail()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Prepare data for the form
		$lExperienceList = $this->Character->GetExperience();

		// Display the title.
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<hr width=70% />';

		// Display data table
		echo '<table>';
		echo '<tr>
			<th class="black-cell" style="width:10px;">#</th> 
			<th class="black-cell" style="width:400px;">Raison</th>
			<th class="black-cell" style="width:60px;">XP</th>
		      </tr>';

		foreach($lExperienceList as $i => $expmod) { 
			$line = $i+1;
			$amount = $expmod['xp']; if( $amount > 0 ) { $amount = "+".$amount; }
			echo '<tr>';
			echo '<td class="grey-cell" style="width:10px;">' 			.$line.			'</td>';
			echo '<td class="white-cell" style="width:400px;text-align:left">' 	.$expmod['reason'].	'</td>';
			echo '<td class="white-cell" style="width:60px;">' 			.$amount.		'</td>';
			echo '</tr>';
		}

		echo '<tr><td colspan="3">';
			echo '<button type="submit" name="option" value="Expérience personnage" class="submit-button" />';
			echo 'Retour';
			echo '</button>';
		echo '</td></tr>';
		echo '</table>';

		echo '<hr width=70% />';
		echo '</div>';
	}


	//--DISPLAY CHARACTER'S ACTIVE LETTERBOX--
	public function DisplayCharacterLetterbox()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Prepare data
		$lLetterList = $this->Character->GetLetters();
		

		// Display!
		echo '<div>';
		echo '<hr width=70% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Nouvelle missive PJ" class="smalltext-button" style="margin-right: 5px;"/>';
		echo 'Écrire à un PJ';
		echo '</button>';

		echo '<button type="submit" name="option" value="Nouvelle missive PNJ" class="smalltext-button" style="margin-right: 5px;"/>';
		echo 'Écrire à un PNJ';
		echo '</button>';

		echo '<button type="submit" name="option" value="Archives missives personnage" class="smalltext-button" style="margin-left: 5px;"/>';
		echo 'Archives';
		echo '</button>';

		echo '</form>';

		// Resumés
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character-lists"/>';

		echo '<table style="text-align: left;">';
		echo '<tr><th class="black-cell" style="width:25px;">#</th>
			  <th class="black-cell" style="width:80px;">Date</th>
			  <th class="black-cell" style="width:120px;">Destinataire</th> 
			  <th class="black-cell" style="width:260px;">Objet</th>
			  <th class="black-cell" style="width:40px;"></th></tr>';

		$index=1; 
		foreach($lLetterList as $i => $letter) {

			// Original posts are listed as the head of each conversation
			if( !$letter->GetOriginalPost() && $letter->GetStatus() <> 'ARCHI' ) {

				$this->Character->GetLetters()[$i]->SetIndex($index);
				$lButton = "";
				if( $letter->GetStatus() == 'REDAC' ) {
					$lButton = 	'<button type="submit" name="edit-letter" value="' .$letter->GetID(). '" class="icon-button"/><img src="images/icon_edit.png" class="icon-button-image"></button>
							 <button type="submit" name="delete-letter" value="' .$letter->GetID(). '" class="icon-button"/><img src="images/icon_delete.png" class="icon-button-image"></button>';
				}
				elseif( $letter->GetStatus() == 'NOUVO' ) {
					$lButton = 	'<button type="submit" name="select-letter" value="' .$letter->GetID(). '" class="icon-button"/><img src="images/icon_see.png" class="icon-button-image"></button>';
				}
				elseif( $letter->GetStatus() == 'LU' ) {
					$lButton = 	'<button type="submit" name="select-letter" value="' .$letter->GetID(). '" class="icon-button"/><img src="images/icon_see.png" class="icon-button-image"></button>
							 <button type="submit" name="archive-letter" value="' .$letter->GetID(). '" class="icon-button"/><img src="images/icon_archive.png" class="icon-button-image"></button>';
				}


				echo '<tr>';
				echo '<td class="grey-cell" style="width:25px;">' 	.$index.							'</td>';
				echo '<td class="gold-cell" style="width:80px;">' 	.str_replace(":", "h", substr($letter->GetDateSent(), 0, 10)).	'</td>';
				echo '<td class="gold-cell" style="width:120px;">'	.$letter->GetRecipientName().					'</td>';
				echo '<td class="gold-cell" style="width:260px;">'	.$letter->GetSubject().						'</td>';
				echo '<td class="white-cell" style="width:40px;">'	.$lButton.							'</td>';
				echo '</tr>';

				// List replies to original post
				$sub=1;
				foreach($lLetterList as $j => $reply) {

					if( $reply->GetOriginalPost() == $letter->GetID() ) {

						// Display different commands and recipient depending if the reply comes from the character or someone else
						$this->Character->GetLetters()[$j]->SetIndex($index); 
						$this->Character->GetLetters()[$j]->SetSubIndex($sub);

						$lButton = '<button type="submit" name="select-letter" value="' .$reply->GetID(). '" class="icon-button"/><img src="images/icon_see.png" class="icon-button-image"></button>';

						if( $reply->GetSenderID() <> $this->Character->GetID() ) { $lRecipient = $this->Character->GetFullName(); }
						else { $lRecipient = $letter->GetRecipientName(); }

						echo '<tr style="font-size:0.9em;">';
						echo '<td class="grey-cell" style="width:10px;">' 	.$index.'-'.$sub.						'</td>';
						echo '<td class="white-cell" style="width:80px;">' 	.str_replace(":", "h", substr($reply->GetDateSent(), 0, 10)).	'</td>';
						echo '<td class="white-cell" style="width:120px;">'	.$lRecipient.							'</td>';
						echo '<td class="white-cell" style="width:260px;">'	.$reply->GetSubject().						'</td>';
						echo '<td class="white-cell" style="width:40px;">'	.$lButton.							'</td>';
						echo '</tr>';

						$sub++;
					}

				}

				$index++;
			}
		}
		echo '</table>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY CHARACTER'S ARCHIVED LETTERS--
	public function DisplayCharacterLetterArchives()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Prepare data
		$lLetterList = $this->Character->GetLetters();
		

		// Display!
		echo '<div>';
		echo '<hr width=70% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';


		echo '<button type="submit" name="option" value="Missives personnage" class="smalltext-button" style="margin-left: 5px;"/>';
		echo 'Missives';
		echo '</button>';

		echo '</form>';

		// Resumés
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character-lists"/>';

		echo '<table style="text-align: left;">';
		echo '<tr><th class="black-cell" style="width:25px;">#</th>
			  <th class="black-cell" style="width:80px;">Date</th>
			  <th class="black-cell" style="width:120px;">Destinataire</th> 
			  <th class="black-cell" style="width:260px;">Objet</th>
			  <th class="black-cell" style="width:40px;"></th></tr>';

		$index=1; 
		foreach($lLetterList as $i => $letter) {

			// Original posts are listed as the head of each conversation
			if( !$letter->GetOriginalPost() && $letter->GetStatus() == 'ARCHI' ) {

				$this->Character->GetLetters()[$i]->SetIndex($index);
				$lButton = '<button type="submit" name="select-letter" value="' .$letter->GetID(). '" class="icon-button"/><img src="images/icon_see.png" class="icon-button-image"></button>';

				echo '<tr>';
				echo '<td class="grey-cell" style="width:25px;">' 	.$index.							'</td>';
				echo '<td class="gold-cell" style="width:80px;">' 	.str_replace(":", "h", substr($letter->GetDateSent(), 0, 10)).	'</td>';
				echo '<td class="gold-cell" style="width:120px;">'	.$letter->GetRecipientName().					'</td>';
				echo '<td class="gold-cell" style="width:260px;">'	.$letter->GetSubject().						'</td>';
				echo '<td class="white-cell" style="width:40px;">'	.$lButton.							'</td>';
				echo '</tr>';

				// List replies to original post
				$sub=1;
				foreach($lLetterList as $j => $reply) {

					if( $reply->GetOriginalPost() == $letter->GetID() ) {

						// Display different commands and recipient depending if the reply comes from the character or someone else
						$this->Character->GetLetters()[$j]->SetIndex($index); 
						$this->Character->GetLetters()[$j]->SetSubIndex($sub);

						$lButton = '<button type="submit" name="select-letter" value="' .$reply->GetID(). '" class="icon-button"/><img src="images/icon_see.png" class="icon-button-image"></button>';

						if( $reply->GetSenderID() <> $this->Character->GetID() ) { $lRecipient = $this->Character->GetFullName(); }
						else { $lRecipient = $letter->GetRecipientName(); }

						echo '<tr style="font-size:0.9em;">';
						echo '<td class="grey-cell" style="width:10px;">' 	.$index.'-'.$sub.						'</td>';
						echo '<td class="white-cell" style="width:80px;">' 	.str_replace(":", "h", substr($reply->GetDateSent(), 0, 10)).	'</td>';
						echo '<td class="white-cell" style="width:120px;">'	.$lRecipient.							'</td>';
						echo '<td class="white-cell" style="width:260px;">'	.$reply->GetSubject().						'</td>';
						echo '<td class="white-cell" style="width:40px;">'	.$lButton.							'</td>';
						echo '</tr>';

						$sub++;
					}

				}

				$index++;
			}
		}
		echo '</table>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY MORE OPTIONS FOR THE CHARACTER SHEET--
	public function DisplayCharacterNewLetterToPCForm()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "DisplayCharacterNewLetterToPCForm : No character defined!"; return; }


		// Prepare data
		$lAccount = ""; if( isset($_POST['account']) ) { $lAccount = $_POST['account']; }
		$lFirstName = ""; if( isset($_POST['firstname']) ) { $lFirstName = $_POST['firstname']; }
		$lLastName = ""; if( isset($_POST['lastname']) ) { $lLastName = $_POST['lastname']; }

		$lTitle = "";
			if( isset($_POST['subject']) ) { $lTitle = $_POST['subject']; }

		$lFileInput = '<input type="file" name="attachedfile" id="attachedfile" />';

		// Display!
		echo '<div>';
		echo '<hr width=70% />';

		// Instructions
		echo   '<div style="margin:auto; margin-bottom:15px; margin-top:15px; padding: 5px; width:620px; border:1px solid black; font-size:0.8em;">
			<span><b><u>MISSIVES À UN JOUEUR</b></u><br />
				Cette interface vous permet d\'envoyer un fichier PDF à l\'Organisation que celle-ci imprimera et placera dans les missives du joueur ciblé. Le joueur en question recevra votre missive sur place lors de son inscription au GN.
			</span>
			</div>';

		// "PC-given" option
		echo '<form method="post" style="margin-bottom:10px;">';
		echo '<input type="hidden" name="action" value="search"/>';

		echo '<span class="section-title">Trouver un joueur</span>';

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

			echo '<form method="post" style="margin-bottom:15px;" enctype="multipart/form-data" >';
			echo '<input type="hidden" name="action" value="manage-character"/>';

			echo '<span class="section-title">Choisir un personnage</span>';
			echo '<table>';

			echo '	<tr>
				<th class="black-cell" style="width:10px;"></th> 
				<th class="black-cell" style="width:250px;">Personnage</th> 
				<th class="black-cell" style="width:250px;">Joueur</th> 
				</tr>';

			foreach($lCharacters as $i => $character) {
				$lButton = '<input type="radio" name="characterid" value="' .$character['characterid']. '" />';

				echo '<tr>';
				echo '<td class="white-cell" style="width:10px;">' 	.$lButton.							'</td>';
				echo '<td class="white-cell" style="width:250px;">' 	.$character['charactername'].				'</td>';
				echo '<td class="white-cell" style="width:250px;">' 	.$character['username'].' ('.$character['account'].	')</td>';
				echo '</tr>';
			}

			echo '</table><br/>';

			echo '<span class="section-title">Sujet et fichier PDF</span>';
			echo '<table style="margin-top: 10px;">';		
			echo '<tr><td class="inputname">Sujet</td>	 <td class="inputbox" style="width: 300px;"><input name="subject" type="text" value="' . $lTitle . '" maxlength="72"/></td></tr>';
			echo '<tr class="filler"></tr>';
			echo '<tr><td class="inputname">Fichier</td>	 <td class="inputbox" style="width: 300px;">' . $lFileInput . '</td></tr>';
			echo '<tr class="filler"></tr>';
			echo '<tr><td colspan="2">';
				echo '<button type="submit" name="option" value="Envoyer missive PJ" class="submit-button" />';
				echo 'Envoyer';
				echo '</button>';
			echo '</td></tr>';
			echo '</table>';		

			echo '</form>';
		}

		echo '<hr width=70% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character"/>';

		echo '<button type="submit" name="option" value="Retour missives" class="submit-button" />';
		echo 'Retour';
		echo '</button>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY NEW LETTER'S CREATION FORM--
	public function DisplayCharacterNewLetterToNPCForm()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "DisplayCharacterNewLetterToNPCForm : No character defined!"; return; }


		// Prepare data
		$lRecipient = "";
			if( isset($_POST['recipient']) ) { $lRecipient = $_POST['recipient']; }

		$lSubject = "";
			if( isset($_POST['subject']) ) { $lSubject = $_POST['subject']; }

		$lFileInput = '<input type="file" name="attachedfile" id="attachedfile" />';


		// Display!
		echo '<div>';
		echo '<hr width=70% />';

		// Instructions
		echo   '<div style="margin:auto; margin-bottom:15px; margin-top:15px; padding: 5px; width:620px; border:1px solid black; font-size:0.8em;">
			<span><b><u>MISSIVES À UN PERSONNAGE NON JOUEUR (PNJ)</b></u><br />
				Cette interface vous permet d\'envoyer à l\'Organisation un fichier PDF représentant votre missive à un PNJ. Ce fichier sera ensuite acheminé par courriel aux personnes responsables de lire et répondre à votre missive. Notez que les PNJ ne sont pas tenus de vous répondre immédiatement pour le GN qui suit et que certains PNJ peuvent même décider de vous ignorer. Le moment où votre lettre est reçue impacte également le délai de réponse.
			</span>
			</div>';

		// NPC-Options
		echo '<form method="post" style="margin-bottom:15px;" enctype="multipart/form-data" >';
		echo '<input type="hidden" name="action" value="manage-character"/>';

		echo '<table>';
		echo '<tr><td class="inputname">Personnage ciblé</td>
			<td class="inputbox"><input name="recipient" type="text" value="' . $lRecipient . '" maxlength="200" style="width: 300px;"/></td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Sujet</td>		
			<td class="inputbox"><input name="subject" type="text" value="' . $lSubject . '" maxlength="200" style="width: 300px;"/></td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Fichier</td>
			<td class="inputbox" style="width: 300px;">' . $lFileInput . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td colspan="2">';
			echo '<button type="submit" name="option" value="Envoyer missive PNJ" class="submit-button" />';
			echo 'Envoyer';
			echo '</button>';
		echo '</td></tr>';
		echo '</table>';		

		echo '</form>';

		echo '<hr width=70% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character"/>';

		echo '<button type="submit" name="option" value="Retour missives" class="submit-button" />';
		echo 'Retour';
		echo '</button>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY SELECTED RESUMÉ'S DETAIL--
	public function DisplaySelectedLetter()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Prepare data
		$lLetter = $this->Character->GetLetterByID($_POST['select-letter']);


		// Display!
		echo '<div>';
		echo '<span class="section-title" style="margin-top: 10px;">Envoyé le '.str_replace(":", "h", substr($lLetter->GetDateSent(), 0, 10)).'</span>';
		echo '<span class="section-title" style="margin-bottom: 10px;">À '.$lLetter->GetRecipientName().'</span>';
		echo '<span class="section-title">'.$lLetter->GetSUbject().'</span>';
		echo '<hr width=70% />';


		// Letter
		echo '<div style="margin: auto; margin-top: 10px; margin-bottom: 10px; width: 600px; text-align: left;"><span>' .nl2br( $lLetter->GetBody() ). '</span></div>';	

		echo '<hr width=70% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Missives personnage" class="smalltext-button"/>';
		echo 'Retour';
		echo '</button>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY CHARACTER'S STATUS MANAGEMENT UI--
	public function DisplayCharacterStatusManagement()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Display options
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<hr width=70% />';

		echo '<button type="submit" name="option" value="Déportation personnage" class="smalltext-button" style="margin-left: 5px; margin-right: 5px;"/>';
		echo 'Déportation';
		echo '</button><br />';

		echo '<button type="submit" name="option" value="Exil personnage" class="smalltext-button" style="margin-left: 5px; margin-right: 5px;"/>';
		echo 'Exil';
		echo '</button><br />';

		echo '<button type="submit" name="option" value="Retrait personnage" class="smalltext-button" style="margin-left: 5px; margin-right: 5px;"/>';
		echo 'Retrait';
		echo '</button><br />';

		echo '<button type="submit" name="option" value="Mort personnage" class="smalltext-button" style="margin-left: 5px; margin-right: 5px;"/>';
		echo 'Mort';
		echo '</button><br />';

		echo '<hr width=70% />';
		echo '</div>';
 	}


	//--DISPLAY CHARACTER'S PERMANENT DEATH DECLARATION FORM--
	public function DisplayCharacterDeathDeclarationForm()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Display!
		echo '<div>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character"/>';

		echo '<span class="note">J\'atteste que ce personnage est mort de manière définitive et n\'a reçu aucun soin ni effet lui permettant d\'éviter ce sort par la suite.</span>';

		echo '<button type="submit" name="option" value="Mort définitive" class="smalltext-button" style="margin-right: 5px;"/>';
		echo 'Oui';
		echo '</button>';

		echo '<button type="submit" name="option" value="Retour statut" class="smalltext-button" style="margin-left: 5px;"/>';
		echo 'Non';
		echo '</button>';

		echo '<div style="text-align:left;">';
		echo '<span class="note"><b>Règles :</b>
			<ul>
			<li>Une mort "définitive", par opposition à une mort dite "temporaire", marque la fin du personnage.</li>
			<li>Seul le pouvoir "Miracle" peut annuler une mort définitive.</li>
			<li>Ce statut ne signifie <b>pas</b> la suppression du personnage. Vous aurez toujours accès à sa fiche.</li>
			</ul>
			</span>';
		echo '</div>';

		echo '</form>';

		echo '<hr width=70% />';
		echo '</div>';
	}


	//--DISPLAY CHARACTER'S DEPORTATION FORM--
	public function DisplayCharacterDeportationForm()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Display!
		echo '<div>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character"/>';

		echo '<span class="note">J\'atteste que ce personnage a été ramené à l\'Inscription pendant le jeu pour être déporté par un groupe adverse.</span>';

		echo '<button type="submit" name="option" value="Déportation" class="smalltext-button" style="margin-right: 5px;"/>';
		echo 'Oui';
		echo '</button>';

		echo '<button type="submit" name="option" value="Retour statut" class="smalltext-button" style="margin-left: 5px;"/>';
		echo 'Non';
		echo '</button>';

		echo '<div style="text-align:left;">';
		echo '<span class="note"><b>Règles :</b>
			<ul>
			<li>Un personnage déporté ne peut être rejoué que si une institution utilise son influence pour le faire libérer.</li>
			<li>Un personnage de niveau 3 ou moins est automatiquement libéré au GN suivant.</li>
			<li>Un personnage déporté peut être mis à mort par le seigneur qui le tient captif s\'il n\'est pas libéré dans le mois qui suit.</li>
			</ul>
			</span>';
		echo '</div>';

		echo '</form>';

		echo '<hr width=70% />';
		echo '</div>';
	}


	//--DISPLAY CHARACTER'S SELF-EXILE FORM--
	public function DisplayCharacterExileForm()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Display!
		echo '<div>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character"/>';

		echo '<span class="note">J\'atteste que j\'ai exilé ce personnage en le retirant du jeu par moi-même alors que les actions d\'envergure était encore permises pour jouer un nouveau personnage.</span>';

		echo '<button type="submit" name="option" value="Exil" class="smalltext-button" style="margin-right: 5px;"/>';
		echo 'Oui';
		echo '</button>';

		echo '<button type="submit" name="option" value="Retour statut" class="smalltext-button" style="margin-left: 5px;"/>';
		echo 'Non';
		echo '</button>';

		echo '<div style="text-align:left;">';
		echo '<span class="note"><b>Règles :</b>
			<ul>
			<li>L\'exil est un geste volontaire fait par un joueur qui désire changer de personnage pendant une activité sans que celui-ci ne soit définitivement mort ou déporté.</li>
			<li>Un personnage exilé ne peut être joué à nouveau avant un an (5 GN). Il redevient automatiquement actif au bout de ce délai.</li>
			<li>Quitter une activité avant sa fin n\'entraîne pas l\'exil.</li>
			</ul>
			</span>';
		echo '</div>';

		echo '</form>';

		echo '<hr width=70% />';
		echo '</div>';
	}


	//--DISPLAY CHARACTER'S SELF-EXILERETIREMENT FORM--
	public function DisplayCharacterRetirementForm()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Display!
		echo '<div>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character"/>';

		echo '<span class="note">J\'atteste que je retire définitivement ce personnage du jeu afin qu\'il prenne sa place dans l\'histoire.</span>';

		echo '<button type="submit" name="option" value="Retrait" class="smalltext-button" style="margin-right: 5px;"/>';
		echo 'Oui';
		echo '</button>';

		echo '<button type="submit" name="option" value="Retour statut" class="smalltext-button" style="margin-left: 5px;"/>';
		echo 'Non';
		echo '</button>';

		echo '<div style="text-align:left;">';
		echo '<span class="note"><b>Règles :</b>
			<ul>
			<li>Le retrait d\'un personnage est un geste volontaire fait par un joueur qui désire mettre fin à l\'évolution normale de celui-ci. Ce geste permet au joueur de lui faire un épilogue que la Rédaction prendra en compte lorsqu\'elle écrit l\'Histoire.</li>
			<li>Un personnage retraité ne peut être rejoué que si un groupe officiel utilise son influence pour le convoquer, et uniquement pour quelques heure si la raison est pertinente.</li>
			<li>Il n\'existe aucun moyen d\'annuler le retrait d\'un personnage une fois que celle-ci a été convenue avec l\'Organisation.</li>
			</ul>
			</span>';
		echo '</div>';

		echo '</form>';

		echo '<hr width=70% />';
		echo '</div>';
	}


	//--DISPLAY CHARACTER'S RENAME FORM--
	public function DisplayCharacterRename()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Display! This form does not require any data.
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character"/>';

		echo '<hr width=70% />';

		echo '<table>';
		echo '<tr><td class="inputname">Prénom</td>			<td class="inputbox"><input name="firstname" type="text" value="' .$this->Character->GetFirstName(). '" maxlength="50"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Nom/Famille/Clan</td>		<td class="inputbox"><input name="lastname" type="text" value="' .$this->Character->GetLastName(). '" maxlength="50"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td colspan="2">';
			echo '<button type="submit" name="option" value="Nom" class="submit-button" />';
			echo 'Enregistrer';
			echo '</button>';
		echo '</td></tr>';
		echo '</table>';

		echo '<hr width=70% />';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY CHARACTER'S ORIGIN FORM--
	public function DisplayCharacterChangeOrigin()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }

		$lPossibleOrigins = $_SESSION['masterlist']->GetKingdoms();
		$lOriginInput = '<td class="inputbox"><select name="origin">';
			foreach($lPossibleOrigins as $origin) {
				$selected = ''; if( $origin['name'] == $this->Character->GetOrigin() ) { $selected = 'selected'; }
				$lOriginInput .= '<option value="'. $origin['name'] .'" '.$selected.'>'. $origin['name'] .'</option>';
			}
		$lOriginInput .= '</select></td>';


		// Display! This form does not require any data.
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character"/>';

		echo '<hr width=70% />';

		echo '<table>';

		echo '<tr><td class="inputname">Provenance</td>' . $lOriginInput . '</tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td colspan="2">';
			echo '<button type="submit" name="option" value="Provenance" class="submit-button" />';
			echo 'Enregistrer';
			echo '</button>';
		echo '</td></tr>';
		echo '</table>';

		echo '<hr width=70% />';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY CHARACTER'S NOTES--
	public function DisplayCharacterNotes()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Prepare data for the form
		$lNoteList = $this->Character->GetNotes();


		// Display skills
		echo '<div>';
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


	//--DISPLAY CHARACTER'S DELETION FORM--
	public function DisplayCharacterDelete()
	{
		// Check if there's a character...
		if( $this->Character == null ) { $this->Error = "No character defined!"; return; }


		// Display! This form does not require any data.
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character"/>';

		echo '<hr width=70% />';

		// Instructions
		echo   '<div style="margin:auto; margin-bottom:15px; padding: 5px; width:620px; border:1px solid red; font-size:0.8em;">
			<span><b><u>IMPORTANT</b></u><br />
				Supprimer un personnage est un option qui sert à se débarasser des personnages créés par erreur ou par test.<br />
				<b>PEU IMPORTE CE QU\'UN INSCRIPTEUR VOUS DIT</b>, ne supprimez pas votre personnage simplement parce qu\'il est mort. La Base de données possède d\'autres mécaniques pour cela.<br />
				Lorsqu\'un joueur supprime l\'un de ses personnages, l\'action est <b>irréversible</b>.<br />
			</span>
			</div>';

		// Input
		echo '<table>';
		echo '<tr><td class="inputname">Votre mot de passe</td>		<td class="inputbox"><input name="password" type="password" value=""/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Encore...</td>			<td class="inputbox"><input name="pw-confirm" type="password" value=""/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td colspan="2">';
			echo '<button type="submit" name="option" value="Supprimer" class="submit-button" />';
			echo 'Supprimer';
			echo '</button>';
		echo '</td></tr>';
		echo '</table>';

		echo '<hr width=70% />';

		echo '</form>';
		echo '</div>';
	}


} // END of CharacterUI class

?>
