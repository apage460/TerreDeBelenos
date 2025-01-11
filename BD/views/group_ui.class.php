<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Group Views v1.2 r11 ==				║
║	Display group UIs.					║
║	Requires user, character and group model.		║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('models/user.class.php');
include_once('models/character.class.php');
include_once('models/groupmanager.class.php');
include_once('models/masterlist.class.php');

class GroupUI
{

private $Manager;

public $Error;

	//--CONSTRUCTOR--
	public function __construct($inManager)
	{
		$this->Manager = $inManager;
	}


	//--DISPLAY GROUP INVITATIONS--
	public function DisplayInvitations()
	{
		// Check if user and manager is set
		if( $this->Manager == null ) { $this->Error = "DisplayInvitations : No manager defined!"; return; }


		// Build invitation list
		$lInvitations = $this->Manager->GetInvitations();


		// Display!
		echo '<div>';
		echo '<span class="section-title">Invitations à faire partie d\'un groupe</span>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-group-lists"/>';

		echo '<table>';

		echo '<tr><th class="black-cell" style="width:10px;">#</th> 
			  <th class="black-cell" style="width:160px;">Personnage</th> 
			  <th class="black-cell" style="width:160px;">Invité par</th> 
			  <th class="black-cell" style="width:160px;">Groupe</th>
			  <th class="black-cell" style="width:30px;"></th></tr>';

		foreach($lInvitations as $i => $invite) {
			$line = $i + 1;
			$key = $invite['characterid'].'-'.$invite['groupid'];
			$lButtons = 	'<button type="submit" name="accept-invite" value="' .$key. '" class="icon-button"/><img src="images/icon_accept.png" class="icon-button-image"></button>
					 <button type="submit" name="refuse-invite" value="' .$key. '" class="icon-button"/><img src="images/icon_delete.png" class="icon-button-image"></button>';

			echo '<tr>';
			echo '<td class="grey-cell" style="width:10px;">' 	.$line.				'</td>';
			echo '<td class="white-cell" style="width:160px;">' 	.$invite['charactername'].	'</td>';
			echo '<td class="white-cell" style="width:160px;">' 	.$invite['invitername'].	'</td>';
			echo '<td class="white-cell" style="width:160px;">' 	.$invite['groupname'].		'</td>';
			echo '<td class="white-cell" style="width:30px;">' 	.$lButtons.			'</td>';
			echo '</tr>';
		}

		echo '</table>';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY GROUP ALLEGIANCES--
	public function DisplayAllegiances()
	{
		// Check if user and manager is set
		if( $this->Manager == null ) { $this->Error = "DisplayAllegiances : No manager defined!"; return; }


		// Build invitation list
		$lAllegiances = $this->Manager->GetAllegiances();


		// Display!
		echo '<div>';
		echo '<span class="section-title">Allégeances de vos personnages envers un groupe</span>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-group-lists"/>';

		echo '<table>';

		echo '<tr>
			<th style="width:160px;">Personnage</th> 
			<th style="width:260px;">Groupe</th> 
			<th style="width:30px;"></th></tr>';
		echo '<tr class="filler"></tr>';

		foreach($lAllegiances as $i => $entry) {
			$lCharacterName = $entry['characterfirstname'].' '.$entry['characterlastname'];

			$lGroupName = "<i>Aucun</i>";
				if( $entry['groupname'] ) { $lGroupName = '<b>'.$entry['groupname'].'</b>'; }

			$key = $entry['characterid'].'-'.$entry['groupid'];

			$lButton = '<button type="submit" name="join-group" value="' .$entry['characterid']. '" class="icon-button"/><img src="images/icon_plus.png" class="icon-button-image"></button>';
				if( $entry['groupname'] ) { $lButton = '<button type="submit" name="quit-group" value="' .$key. '" class="icon-button"/><img src="images/icon_delete.png" class="icon-button-image"></button>'; }

			echo '<tr>';
			echo '<td class="labelname">' 		.$lCharacterName.	'</td>';
			echo '<td style="color: blue;">'	.$lGroupName.		'</td>';
			echo '<td>' 				.$lButton.		'</td>';
			echo '</tr>';
			echo '<tr class="filler"></tr>';
		}

		echo '</table>';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY CHARACTER'S WORK--
	public function DisplayJoinGroupForm()
	{
		// Check if there's a character...
		if( $this->Manager == null ) { $this->Error = "DisplayJoinGroupForm : No manager defined!"; return; }

		// Prepare data
		$lGroupList = $_SESSION['groupmanager']->GetGroups();
		$lCharacter = $this->Manager->GetActiveCharacter();
			if (!$lCharacter) { $this->Error = "DisplayJoinGroupForm : No character defined!"; return; }

		$lGroupInput = '<select name="groupid">';
		foreach ($lGroupList as $group) {
			$lGroupInput .= '<option value="' .$group['id']. '">' .$group['name']. '</option>';
		}
		$lGroupInput .= '</select>';


		// Display!
		echo '<div>';
		echo '<span class="section-title">Rejoindre un groupe officiel</span>';
		echo '<hr width=70% />';


		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-group"/>';
		echo '<input type="hidden" name="characterid" value="'.$lCharacter->GetID().'"/>';

		echo '<table>';
		echo '<tr><td class="inputname">Personnage</td>			<td class="labelvalue" style="width: 165px;">' . $lCharacter->GetFullName() . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Groupe</td>			<td class="inputbox" style="width: 165px;">' . $lGroupInput . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td colspan="2">
			<button type="submit" name="option" value="Rejoindre" class="submit-button" />Enregistrer</button>
		      </td></tr>';
		echo '</table>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY REGISTRATION FORM--
	public function DisplayGroupRegForm()
	{
		// Prepare data
		$lSpecializationList = $this->Manager->GetPossibleSpecializations();
		$lCampList = $this->Manager->GetCamps();


		$lName = ""; 		if( isset($_POST['name']) ) 		{ $lName = $_POST['name']; }
		$lDescription = ""; 	if( isset($_POST['description']) ) 	{ $lDescription = $_POST['description']; }

		$lBackground = ""; 	if( isset($_POST['background']) ) 	{ $lBackground = $_POST['background']; }

		$lBaseCampCode = ""; 	if( isset($_POST['basecampcode']) ) 	{ $lBaseCampCode = $_POST['basecampcode']; }
		$lMoreInfo = ""; 	if( isset($_POST['moreinfo']) ) 	{ $lMoreInfo = $_POST['moreinfo']; }

		$lBaseCampInput = '<select name="basecampcode">';
			foreach ($lCampList as $camp) {
				$selected = ""; if( $camp['code'] == $lBaseCampCode ) { $selected = "selected"; }
				$lBaseCampInput .= '<option value="'.$camp['code'].'">'.$camp['name'].'</option>';
			}
		$lBaseCampInput .= '</select>';


		// Display! This form does not require any data.
		echo '<div>';
		echo '<span class="section-title">Créer un groupe</span>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-group"/>';

		echo   '<div style="margin:auto; padding: 5px; width:620px; border:1px solid red; font-size:0.8em;">
			<span><b><u>IMPORTANT</b></u><br />Il vous est <b>fortement</b> recommandé d\'écrire d\'abord vos textes à l\'aide d\'un logiciel de traitement de texte (ex.: Word).</span>
			</div>';
		echo '<hr width=70% />';

		echo '<table>';
		echo '<tr><td class="inputname" style="width: 100px;">Nom</td>		<td class="inputbox"><input name="name" type="text" value="'.$lName.'" style="width: 480px;" maxlength="50"/></td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname" style="width: 100px;">Description</td> <td></td></tr>';
		echo '<tr><td class="inputbox" colspan="2">				<textarea name="description" cols="72" rows="12" placeholder="Environ 300 mots.">'.$lDescription.'</textarea></td></tr>';

		echo '<tr><td colspan="2"><hr style="margin-top: 10px;"/></td></tr>';

		echo '<tr><td class="inputname" style="width: 100px;">Histoire</td> <td></td></tr>';
		echo '<tr><td class="inputbox" colspan="2">				<textarea name="background" cols="72" rows="15" placeholder="Facultatif. L\'histoire en bref de votre groupe depuis sa création jusqu\'à sa venue en Bélénos. Maximum 800 mots.">'.$lBackground.'</textarea></td></tr>';

		if(GROUP_BASECAMP_ENABLED){
		echo '<tr><td colspan="2"><hr style="margin-top: 10px;"/></td></tr>';

		echo '<tr><td class="inputname" style="width: 100px;">Campement</td>		<td class="inputbox">'.$lBaseCampInput.'</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname" style="width: 100px;">Détails</td> <td></td></tr>';
		echo '<tr><td class="inputbox" colspan="2">				<textarea name="moreinfo" cols="72" rows="6" placeholder="Facultatif. Incrivez le ou les bâtiments que vous habiterez, ainsi que vos projets de construction pour l\'année qui vient.">'.$lMoreInfo.'</textarea></td></tr>';
		} else { echo '<input type="hidden" name="basecampcode" value=""/><input type="hidden" name="moreinfo" value=""/>'; }

		echo '</table>';
		echo '<hr width=70% />';

		echo '<span class="note">N\'oubliez pas d\'envoyer des invitations à vos membres et de définir vos objectifs de groupe (avoués ou cachés) une fois votre groupe créé.</span>';

		echo '<button type="submit" name="option" value="Créer" class="submit-button" />';
		echo 'Enregistrer';
		echo '</button>';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY USER INFORMATION--
	public function DisplayGroupInfo( $inSubUI =NULL )
	{
		// Check if there's a group...
		if( $this->Manager == null ) { $this->Error = "DisplayGroupInfo : No group manager defined!"; return; }
		if( !$this->Manager->GetActiveGroup() ) { $this->Error = "DisplayGroupInfo : No group defined!"; return; }


		// Prepare data
		$lGroup = $this->Manager->GetActiveGroup();

		$lGMMOnly = ""; if( !$this->Manager->GetGroupManagerMode() ) { $lGMMOnly = 'disabled'; }	// To disable buttons when out of GMM

		$lStatus = "À déterminer";
			    if( $lGroup->GetStatus() == 'NOUV' )  { $lStatus = "Nouveau"; }
			elseif( $lGroup->GetStatus() == 'ACTIF' ) { $lStatus = "Actif"; }
			elseif( $lGroup->GetStatus() == 'INACT' ) { $lStatus = "Inactif"; }
			elseif( $lGroup->GetStatus() == 'ERAD'  ) { $lStatus = "Éradiqué"; }
			elseif( $lGroup->GetStatus() == 'DISSO' ) { $lStatus = "Dissout"; }
			elseif( $lGroup->GetStatus() == 'RETIR' ) { $lStatus = "Retiré"; }

		$lPeopleInCharge = "";
			foreach($lGroup->GetPeopleInCharge() as $i => $person) {
				if($i) { $lPeopleInCharge .= '<br />'; }
				$lPeopleInCharge .= $person->GetFullName(). ' ('.$person->GetAccountName().')';
			}

		$lObjectives = "";
			foreach($lGroup->GetObjectives() as $i => $objective) {
				$style = '"color: black;"'; if( $objective['type'] == 'CACHE' ) { $style = '"color: darkgrey;"'; }
				$lObjectives .= '<div style='.$style.'>'.$objective['name']. '</div>';
			}


		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Fiche de groupe</span>';
		echo '<hr width=70% />';

		// First data table
		echo '<table>';		
		echo '<tr><td class="labelname">Nom :</td> 			<td class="labelvalue">' . $lGroup->GetName() . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Status :</td>	 		<td class="labelvalue">' . $lStatus . '</td></tr>';
		if(GROUP_SPECIALIZATION_ENABLED){
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Spécialisation :</td>	 	<td class="labelvalue">' . $lGroup->GetSpecialization() . '</td></tr>'; }
		if(GROUP_OBJECTIVES_ENABLED){
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Objectifs :</td> 		<td class="labelvalue" style="width: 400px;">' . $lObjectives . '</td></tr>'; }
		if(GROUP_BASECAMP_ENABLED){
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Campement :</td> 		<td class="labelvalue" style="width: 400px;">' . $lGroup->GetBaseCamp()['name'] . '</td></tr>'; }
		if(GROUP_INFLUENCE_ENABLED){
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Influence :</td> 		<td class="labelvalue" style="width: 400px;">' . $lGroup->GetInfluenceCount() . ' / '. $lGroup->GetMaxInfluence() .'</td></tr>'; }

		echo '<tr><td colspan="2"><hr /></td></tr>';

		echo '<tr><td class="labelname">Membres :</td>	 		<td class="labelvalue">' . $lGroup->GetMemberCount() . ' membres ('.$lGroup->GetActiveMemberCount().' actifs)</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Responsable(s) :</td> 		<td class="labelvalue" style="width: 400px;">' . $lPeopleInCharge . '</td></tr>';

		echo '</table>';
		echo '<hr width=70% />';


		// Submenu
		$lOptionCount = 0;
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		if(GROUP_ACTIONS_ENABLED){
		echo '<button type="submit" name="option" value="Actions groupe" class="smalltext-button" style="margin:0px;"/>';
		echo 'Actions';
		echo '</button>';
			$lOptionCount++; if($lOptionCount >= OPTIONS_PER_LINE) {echo '<br />'; $lOptionCount = 0; }
		}
		if(GROUP_INSTITUTIONS_ENABLED){
		echo '<button type="submit" name="option" value="Institutions groupe" class="smalltext-button" style="margin:0px;"/>';
		echo 'Institutions';
		echo '</button>';
			$lOptionCount++; if($lOptionCount >= OPTIONS_PER_LINE) {echo '<br />'; $lOptionCount = 0; }
		}
		if(GROUP_QUESTS_ENABLED){
		echo '<button type="submit" name="option" value="Quêtes groupe" class="smalltext-button" style="margin:0px;"/>';
		echo 'Quêtes';
		echo '</button>';
			$lOptionCount++; if($lOptionCount >= OPTIONS_PER_LINE) {echo '<br />'; $lOptionCount = 0; }
		}
		if(GROUP_OBJECTIVES_ENABLED){
		echo '<button type="submit" name="option" value="Objectifs groupe" class="smalltext-button"  style="margin:0px;"/>';
		echo 'Objectifs';
		echo '</button>';
			$lOptionCount++; if($lOptionCount >= OPTIONS_PER_LINE) {echo '<br />'; $lOptionCount = 0; }
		}
		if(GROUP_DEFINITION_ENABLED){
		echo '<button type="submit" name="option" value="Définition groupe" class="smalltext-button" style="margin:0px;"/>';
		echo 'Définition';
		echo '</button>';
			$lOptionCount++; if($lOptionCount >= OPTIONS_PER_LINE) {echo '<br />'; $lOptionCount = 0; }
		}
		if(GROUP_INFLUENCE_ENABLED){
		echo '<button type="submit" name="option" value="Influence groupe" class="smalltext-button" style="margin:0px;"/>';
		echo 'Influence';
		echo '</button>';
			$lOptionCount++; if($lOptionCount >= OPTIONS_PER_LINE) {echo '<br />'; $lOptionCount = 0; }
		}
		if(GROUP_ADVANTAGES_ENABLED){
		echo '<button type="submit" name="option" value="Avantages groupe" class="smalltext-button" style="margin:0px;"/>';
		echo 'Avantages';
		echo '</button>';
			$lOptionCount++; if($lOptionCount >= OPTIONS_PER_LINE) {echo '<br />'; $lOptionCount = 0; }
		}
		if(GROUP_RESUMES_ENABLED){
		echo '<button type="submit" name="option" value="Résumés groupe" class="smalltext-button" style="margin:0px;"/>';
		echo 'Résumés';
		echo '</button>';
			$lOptionCount++; if($lOptionCount >= OPTIONS_PER_LINE) {echo '<br />'; $lOptionCount = 0; }
		}
		if(GROUP_MEMBERS_ENABLED){
		echo '<button type="submit" name="option" value="Membres groupe" class="smalltext-button" style="margin:0px;"/>';
		echo 'Membres';
		echo '</button>';
			$lOptionCount++; if($lOptionCount >= OPTIONS_PER_LINE) {echo '<br />'; $lOptionCount = 0; }
		}
		if(GROUP_OTHERS_ENABLED){
		echo '<button type="submit" name="option" value="Autres options groupe" class="smalltext-button" style="margin:0px;" '.$lGMMOnly.'/>';
		echo 'Autres';
		echo '</button>';
			$lOptionCount++; if($lOptionCount >= OPTIONS_PER_LINE) {echo '<br />'; $lOptionCount = 0; }
		}

		echo '</form>';

		echo '</div>';


		// Display SubUI if not null
		    if( $inSubUI == 'INSTITUTIONS' ) 			{ $this->DisplayGroupInstitutions(); }
			elseif( $inSubUI == 'NEW INSTITUTION' ) 		{ $this->DisplayInstitutionCreationForm(); }
		elseif( $inSubUI == 'ADVANTAGES' ) 			{ $this->DisplayGroupAdvantages(); }
		elseif( $inSubUI == 'QUESTS' ) 				{ $this->DisplayGroupQuests(); }
			elseif( $inSubUI == 'NEW QUEST' ) 			{ $this->DisplayQuestRequestForm(); }
		elseif( $inSubUI == 'RESUMES' ) 			{ $this->DisplayGroupResumes(); }
			elseif( $inSubUI == 'NEW RESUME' ) 			{ $this->DisplayResumeCreationForm(); }
		elseif( $inSubUI == 'OBJECTIVES' ) 			{ $this->DisplayObjectives(); }
			elseif( $inSubUI == 'ADD OBJECTIVES' ) 			{ $this->DisplayAddObjectiveForm(); }
			elseif( $inSubUI == 'REVEAL OBJECTIVES' ) 		{ $this->DisplayRevealObjectiveForm(); }
			elseif( $inSubUI == 'REMOVE OBJECTIVES' ) 		{ $this->DisplayRemoveObjectiveForm(); }
		elseif( $inSubUI == 'MEMBERS' ) 			{ $this->DisplayMembers(); }
			elseif( $inSubUI == 'INVITE MEMBERS')			{ $this->DisplayInviteMembersForm(); }
			elseif( $inSubUI == 'REMOVE MEMBERS')			{ $this->DisplayRemoveMembersForm(); }
		elseif( $inSubUI == 'DEFINITION' ) 			{ $this->DisplayDefinitionOptions(); }
			elseif( $inSubUI == 'DESCRIPTION' ) 			{ $this->DisplayGroupDescription(); }
				elseif( $inSubUI == 'EDIT DESCRIPTION' ) 		{ $this->DisplayDescriptionEditingForm(); }
			elseif( $inSubUI == 'BACKGROUND' ) 			{ $this->DisplayGroupBackground(); }
				elseif( $inSubUI == 'EDIT BACKGROUND' ) 		{ $this->DisplayBackgroundEditingForm(); }
			elseif( $inSubUI == 'BASECAMP' ) 			{ $this->DisplayBaseCampInformation(); }
				elseif( $inSubUI == 'EDIT BASECAMP' ) 			{ $this->DisplayBaseCampInformationEditingForm(); }
		elseif( $inSubUI == 'OTHERS' ) 				{ $this->DisplayOtherOptions(); }
			elseif( $inSubUI == 'STATUS' ) 				{ $this->DisplayGroupStatusForm(); }
			elseif( $inSubUI == 'PEOPLE IN CHARGE' ) 		{ $this->DisplayPICManagementForm(); }
			elseif( $inSubUI == 'RENAME' ) 				{ $this->DisplayGroupRename(); }
	}


	//--DISPLAY MORE OPTIONS FOR THE ACTIVE GROUP--
	public function DisplayOtherOptions()
	{
		// Check if there's a manager
		if( $this->Manager == null ) { $this->Error = "No manager defined!"; return; }


		// Prepare data
		$lGMMOnly = ""; if( !$this->Manager->GetGroupManagerMode() ) { $lGMMOnly = 'disabled'; }	// To disable buttons when out of GMM


		// Display!
		echo '<div>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Renommer groupe" class="smalltext-button" />';
		echo 'Renommer';
		echo '</button><br />';

		echo '<button type="submit" name="option" value="Statut groupe" class="smalltext-button" '.$lGMMOnly.'/>';
		echo 'Statut';
		echo '</button><br />';

		echo '<button type="submit" name="option" value="Nommer responsables" class="smalltext-button" />';
		echo 'Responsables';
		echo '</button><br />';

		echo '</form>';

		echo '<hr width=70% />';

		echo '</div>';
	}


	//--DISPLAY MORE OPTIONS FOR THE ACTIVE GROUP--
	public function DisplayDefinitionOptions()
	{
		// Check if there's a manager
		if( $this->Manager == null ) { $this->Error = "No manager defined!"; return; }


		// Prepare data


		// Display!
		echo '<div>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Description groupe" class="smalltext-button" />';
		echo 'Description';
		echo '</button><br />';

		echo '<button type="submit" name="option" value="Histoire groupe" class="smalltext-button" />';
		echo 'Histoire';
		echo '</button><br />';

		if(GROUP_BASECAMP_ENABLED){
		echo '<button type="submit" name="option" value="Campement groupe" class="smalltext-button" '.$lGMMOnly.'/>';
		echo 'Campement';
		echo '</button><br />'; }

		echo '</form>';

		echo '<hr width=70% />';

		echo '</div>';
	}


	//--DISPLAY ACTIVE GROUP'S INSTITUTIONS--
	public function DisplayGroupInstitutions()
	{
		// Prepare data
		$lInstitutionList = $this->Manager->GetActiveGroup()->GetInstitutions();
		$lGroup = $this->Manager->GetActiveGroup();


		// Display!
		echo '<div>';
		echo '<hr width=70% />';
		echo '<span class="section-title">Liste des Institutions</span>';

		// Action count
		/*echo '<div style="margin: 10px;"><table>';
		echo '<tr><td class="labelname" style="width: 80px; font-size: 1.0em;">Actions :</td>	 
			  <td class="labelvalue" style="width: 200px; color: black; background: LightGray;">'. $lGroup->GetActionCountByStatus('DEM') .' demandées / '. $lGroup->GetMaximumActionCountPerActivity() .' permises</td></tr>';
		echo '</table></div>';*/

		// Submenu
		if( $this->Manager->GetGroupManagerMode() ) {
			echo '<form method="post">';
			echo '<input type="hidden" name="action" value="select-menu-option"/>';

			echo '<button type="submit" name="option" value="Nouvelle institution" class="smalltext-button"/>';
			echo 'Nouvelle';
			echo '</button>';

			echo '</form>';
		}

		// List
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-group-lists"/>';

		echo '<table style="text-align: left;">';
		echo '<tr>';
		echo '<th class="black-cell" style="width:240px;">Institution</th>';
		echo '<th class="black-cell" style="width:70px;">Profil</th>';
		echo '<th class="black-cell" style="width:40px;">Niveau</th>';
		echo '<th class="black-cell" style="width:120px;">Comté</th>';
		echo '<th class="black-cell" style="width:40px;"></th>';
		echo '</tr>';

		foreach($lInstitutionList as $i => $institution) {
			$lCounty = $_SESSION['masterlist']->GetCountyByID( $institution->GetCountyID() );
			$lButtons = '<button type="submit" name="select-institution" value="' .$i. '" class="icon-button"/><img src="images/icon_see.png" class="icon-button-image"></button>';
			    if( $institution->GetLevel() > 0 ) 
				{ $lButtons .= ' <button type="submit" name="retire-institution" value="' .$institution->GetID(). '" class="icon-button"/><img src="images/icon_minus.png" class="icon-button-image"></button>'; }
			    else 
				{ $lButtons .= ' <button type="submit" name="delete-institution" value="' .$institution->GetID(). '" class="icon-button"/><img src="images/icon_delete.png" class="icon-button-image"></button>'; }

			echo '<tr>';
			echo '<td class="white-cell" style="width:240px;">' 	.$institution->GetName().	'</td>';
			echo '<td class="white-cell" style="width:70px;">'	.$institution->GetProfileName().'</td>';
			echo '<td class="white-cell" style="width:40px;">'	.$institution->GetLevel().	'</td>';
			echo '<td class="white-cell" style="width:120px;">'	.$lCounty['name'].		'</td>';
			echo '<td class="white-cell" style="width:40px;">'	.$lButtons.			'</td>';
			echo '</tr>';
		}
		echo '</table>';

		echo '</form>';
		echo '<hr width=70% />';

		echo '</div>';
	}


	//--DISPLAY NEW INSTITUTION'S CREATION FORM--
	public function DisplayInstitutionCreationForm()
	{
		// Prepare data
		$lKingdoms = $_SESSION['masterlist']->GetKingdoms();
		$lCounties = $_SESSION['masterlist']->GetCounties();
		$lProfiles = $this->Manager->GetPossibleProfiles();

		$lCountyInput = '<select name="countyid">';
		$lCountyInput .= '<option></option>';	
		foreach( $lKingdoms as $kingdom ) {			
			$lCountyInput .= '<optgroup label="' . $kingdom['name'] . '">';			
			foreach( $lCounties as $county ) {					    
				if( $county['status'] == 'ACTIF' && $county['kingdomcode'] == $kingdom['code'] ) 
				{
					$lCountyInput .= '<option value="'. $county['id'] .'">'. $county['name'] .' </option>';
				}	    
			}
			$lCountyInput .= '</optgroup>';
		}
		$lCountyInput .= '</select>';
		
		$ProfileInput = '<select name="profilecode">';
		$ProfileInput .= '<option></option>';	
		foreach( $lProfiles as $profile ) {					    
			$ProfileInput .= '<option value="'. $profile['code'] .'">'. $profile['name'] .' </option>';
		}
		$ProfileInput .= '</select>';
		
		$lName = "";		if( isset($_POST['name']) ) 		{ $lName = $_POST['name']; }
		$lDescription = "";	if( isset($_POST['description']) ) 	{ $lDescription = $_POST['description']; }
		$lLeader = "";		if( isset($_POST['leader']) ) 		{ $lLeader = $_POST['leader']; }


		// Display!
		echo '<div>';
		echo '<hr width=70% />';

		// Quests
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-group"/>';

		echo '<span class="note"><b><u>IMPORTANT</b></u><br />Il vous est <b>fortement</b> recommandé d\'écrire d\'abord vos textes à l\'aide d\'un logiciel de traitement de texte (ex.: Word) et de les conservés sur votre ordinateur.</span>';

		echo '<table>';
		echo '<tr><td class="inputname">Nom</td>		<td class="inputbox"><input name="name" type="text" value="'.$lName.'" style="width: 320px;" maxlength="50"/></td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Comté</td>		<td class="inputbox" style="width: 320px;">' . $lCountyInput . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Profil</td>		<td class="inputbox" style="width: 320px;">' . $ProfileInput . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Dirigeant</td>		<td class="inputbox"><input name="leader" type="text" value="'.$lLeader.'" style="width: 320px;" maxlength="50"/></td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname" style="width: 100px;">Description</td> <td></td></tr>';
		echo '<tr><td class="inputbox" colspan="2"> <textarea name="description" cols="72" rows="12" placeholder="Entrez une courte description de votre Institution. Les longs textes peuvent être fournis en supplément via un courriel à « Redaction@Terres-de-Belenos.com »">'.$lDescription.'</textarea></td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname" style="width: 100px;">Agenda caché</td> 
			<td> <a href="javascript:window.open(\'./includes/whatis/institution-hiddenagenda.html\',\'hiddenagenda\',\'width=500,height=500\')">(Qu\'est-ce que c\'est)</a> </td></tr>';
		echo '<tr><td class="inputbox" colspan="2"> <textarea name="hiddenagenda" cols="72" rows="12" placeholder="Facultatif. Entrez l\'agenda secret que votre groupe souhaite donner aux dirigeants de votre Institution. Ce qui n\'est pas consigné ici ne sera pas tenu en compte.">'.$lDescription.'</textarea></td></tr>';
		echo '</table>';

		echo '<span class="note"><b>Note :</b> Vous pouvez agrémenter votre texte à l\'aide des balises HTML &lt;p&gt; (paragraphe), &lt;b&gt; (gras), &lt;i&gt; (italique) et &lt;u&gt; (souligné).</span>';

		echo '<button type="submit" name="option" value="Créer institution" class="submit-button" style="margin-right:5px;"/>';
		echo 'Enregistrer';
		echo '</button>';

		echo '<button type="submit" name="option" value="Retour institutions" class="submit-button" style="margin-left:5px;"/>';
		echo 'Annuler';
		echo '</button>';

		echo '</form>';
		echo '<hr width=70% />';

		echo '</div>';
	}


	//--DISPLAY SELECTED INSTITUTION DETAIL--
	public function DisplaySelectedInstitution()
	{
		// Prepare data
		$index = $_POST['select-institution'];
		$lInstitution = $this->Manager->GetActiveGroup()->GetInstitutions()[$index];

		$lCounty = $_SESSION['masterlist']->GetCountyByID( $lInstitution->GetCountyID() );

		$lLastAction = array('date' => '1991-01-01 12:00:00'); ## To be continued


		// Display!
		echo '<div>';
		echo '<span class="section-title">Détail de l\'Institution</span>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-group-lists"/>';
		echo '<input type="hidden" name="select-institution" value="'.$index.'"/>';

		// Quests
		echo '<table>';
		echo '<tr><td class="labelname">Nom</td>			<td class="labelvalue" style="width: 450px;">' .$lInstitution->GetName(). '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Profil</td>			<td class="labelvalue" style="width: 450px;">' .$lInstitution->GetProfileName(). '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Niveau</td>			<td class="labelvalue" style="width: 450px;">' .$lInstitution->GetLevel(). '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Comté</td>			<td class="labelvalue" style="width: 450px;">' .$lCounty['name']. '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Prospérité</td>			<td class="labelvalue" style="width: 450px;">' .$lCounty['prosperity']. '</td></tr>';

		echo '<tr><td colspan="2"><hr style="margin-top: 10px;" /></td></tr>';
		echo '<tr><td colspan="2" class="labelname" style="text-align: center;">Description</td></tr>';
		echo '<tr><td colspan="2" class="labelvalue-text" style="text-align: left;">' .nl2br( $lInstitution->GetDescription() ). '</td></tr>';	

		echo '<tr class="filler"></tr>';
		echo '<tr><td colspan="2" class="labelname" style="text-align: center;">Agenda caché</td></tr>';
		echo '<tr><td colspan="2" class="labelvalue-text" style="text-align: left;">' .nl2br( $lInstitution->GetHiddenAgenda() ). '</td></tr>';	
		echo '</table>';

		echo '<hr width=70% />';

		echo '</form>';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Institutions groupe" class="smalltext-button"/>';
		echo 'Retour';
		echo '</button>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY INSTITUTION COMMAND OPTIONS
	public function DisplayGroupActions()
	{
		// Data management
		$lGroup = $this->Manager->GetActiveGroup();
		$lActivityList = $this->Manager->GetActivities();

		$lNow = new DateTime(); 


		// Display!
		echo '<div>';
		echo '<span class="section-title">Actions du groupe par GN</span>';
		echo '<hr width=70% />';

		echo '<span class="note">Le tableau suivant montre les actions faites par le groupe pour chaque activité. Vous avez jusqu\'à '.INSTITUTION_COMMANDS_DELAY.' jours avant chaque GN pour en ajouter.</span> <span class="note">Les actions faites en jeu via la boîte aux lettres ne peuvent être modifiés de cette manière. Vous devez alors écrire à <b>Organisation@Terres-de-Belenos.com</b></span>';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-group-lists"/>';

		echo '<table>';
		echo '<tr><th class="black-cell" style="width:100px;">GN</th>
			  <th class="black-cell" style="width:370px;">Ordres</th>
			  <th class="black-cell" style="width:30px;"></th></tr>';


		foreach($lActivityList as $i => $activity) {
			$lMaxDate = new DateTime( $activity->GetStartingDate() ); $lMaxDate->modify('- '.INSTITUTION_COMMANDS_DELAY.' day');

			$lActions = $lGroup->GetActivityActions( $activity->GetID() );
			$lActionCount = count($lActions);
			$lListedActions = ""; $lActionNumber = 0;
				foreach ($lActions as $action) { $lActionNumber++;
								 $lListedActions .= $action['name']; 
								 if( $lActionNumber < $lActionCount) { $lListedActions .= ", ";} }

			// Controller and services will need to know which institution acts for which activity
			$lStyle = "";
			    if( $lNow <= $lMaxDate ) 	{ $lStyle = "background-color: lightblue; font-weight: bold;"; }
			elseif( !count($lActions) ) 	{ $lStyle = "background-color: lightgray; color: gray;"; }
			else 				{ $lStyle = "background-color: white; color: black;"; }

			$lButtons = "";
			    if( $lNow <= $lMaxDate && $this->Manager->GetGroupManagerMode() )	
			    	{ $lButtons = '<button type="submit" name="change-actions" value="' .$activity->GetID(). '" class="icon-button"/><img src="images/icon_edit.png" class="icon-button-image"></button>'; }
			  else	{ $lButtons = '<button type="submit" name="view-actions" value="' .$activity->GetID(). '" class="icon-button"/><img src="images/icon_see.png" class="icon-button-image"></button>'; }


			echo '<tr>';
			echo '<td class="grey-cell" style="width:100px; text-align: left;">'			.$activity->GetName().		'</td>';
			echo '<td class="white-cell" style="width:370px; text-align: left; '.$lStyle.'">'	.$lListedActions.		'</td>';
			echo '<td class="white-cell" style="width:30px;  '.$lStyle.'">'				.$lButtons.			'</td>';
			echo '</tr>';
		}
		echo '</table>';
		echo '</form>';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Voir fiche groupe" class="smalltext-button" style="margin-top: 5px;" />';
		echo 'Retour';
		echo '</button>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY GROUP'S ACTIONS REGISTRATION FORM--
	public function DisplayActionsRegForm()
	{
		// Data management
		$lActivity = isset($_POST['change-actions']) ? $this->Manager->GetActivityByID( $_POST['change-actions'] ) : $this->Manager->GetActivityByID( $_POST['activityid'] );
		$lGroup = $this->Manager->GetActiveGroup();

		$lMilitaryInstitutions = $lGroup->GetInstitutionsByProfile('M'); $lMilitaryLevel = 0;
			foreach($lMilitaryInstitutions as $institution) { if( $institution->GetLevel() > $lMilitaryLevel ) { $lMilitaryLevel = $institution->GetLevel(); }}
		$lUnderworldInstitutions = $lGroup->GetInstitutionsByProfile('I'); $lUnderworldLevel = 0;
			foreach($lUnderworldInstitutions as $institution) { if( $institution->GetLevel() > $lUnderworldLevel ) { $lUnderworldLevel = $institution->GetLevel(); }}
		$lCommercialInstitutions = $lGroup->GetInstitutionsByProfile('C'); $lCommercialLevel = 0;
			foreach($lCommercialInstitutions as $institution) { if( $institution->GetLevel() > $lCommercialLevel ) { $lCommercialLevel = $institution->GetLevel(); }}
		$lAcademicInstitutions = $lGroup->GetInstitutionsByProfile('A'); $lAcademicLevel = 0;
			foreach($lAcademicInstitutions as $institution) { if( $institution->GetLevel() > $lAcademicLevel ) { $lAcademicLevel = $institution->GetLevel(); }}
		$lReligiousInstitutions = $lGroup->GetInstitutionsByProfile('R'); $lReligiousLevel = 0;
			foreach($lReligiousInstitutions as $institution) { if( $institution->GetLevel() > $lReligiousLevel ) { $lReligiousLevel = $institution->GetLevel(); }}

		$lPossibleActionList = $this->Manager->GetPossibleActions();
		$lRegisteredActionList = $lGroup->GetActivityActions( $lActivity->GetID() );


		// Display!
		echo '<div>';
		echo '<span class="section-title">Actions pour '.$lActivity->GetName().'</span>';
		echo '<hr width=70% />';

		#echo '<span class="note" style="text-align:left;">Pour une définition de chaque action : <a href="javascript:window.open(\'./includes/popups/institution-actions.php\',\'actions\',\'width=500,height=500\')">cliquez ici</a>.</span>';
		#echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-group"/>';
		echo '<input type="hidden" name="activityid" value="'.$lActivity->GetID().'"/>';

		// Actions
		// GENERAL ACTIONS FIRST
		echo '<span class="section-title">ACTIONS COMMUNES</span>';
		echo '<table>';

		echo '	<tr>
			<th class="black-cell" style="width:60px;">Sélection</th> 
			<th class="black-cell" style="width:160px;">Action</th> 
			<th class="black-cell" style="width:30px;">Coût</th> 
			<th class="black-cell" style="width:300px;">Précisions</th>
			</tr>';

		foreach($lPossibleActionList as $i => $action) {
			if($action['profilecode'] == 'G') {
				if($action['code'] == 'SPECGROUPE' && $lGroup->GetSpecialization()) { continue; } // Only one specialization permitted.

				if( isset($lRegisteredActionList[$action['code']]) ) {
					$_POST[$action['code'].'-registered'] = $lRegisteredActionList[$action['code']];
				}

				$selection = $this->Manager->GetActionSelectionControl($action['code'], $action['maxpurchases'], $action['cost']);
				$precision = $this->Manager->GetActionPrecisionControl($action['code']);

				echo '<tr>';
				echo '<td class="white-cell" style="width:60px;">' 	.$selection.			'</td>';
				echo '<td class="white-cell" style="width:160px;">' 	.$action['name'].		'</td>';
				echo '<td class="white-cell" style="width:30px;">' 	.$action['cost'].		'</td>';
				echo '<td class="white-cell" style="width:300px;">'	.$precision.			'</td>';
				echo '</tr>';

			}
		}

		echo '</table>';

		// MILITARY ACTIONS
		if( count($lMilitaryInstitutions) ) {
			echo '<hr width=70% />';

			echo '<span class="section-title">INSTITUTION MILITAIRE</span>';
			echo '<table>';

			echo '	<tr>
				<th class="black-cell" style="width:60px;">Sélection</th> 
				<th class="black-cell" style="width:160px;">Action</th> 
				<th class="black-cell" style="width:30px;">Coût</th> 
				<th class="black-cell" style="width:300px;">Précisions</th>
				</tr>';

			foreach($lPossibleActionList as $i => $action) {
				if($action['profilecode'] == 'M' && $action['level'] <= $lMilitaryLevel) {

					if( isset($lRegisteredActionList[$action['code']]) ) {
						$_POST[$action['code'].'-registered'] = $lRegisteredActionList[$action['code']];
					}

					$selection = $this->Manager->GetActionSelectionControl($action['code'], $action['maxpurchases'], $action['cost']);
					$precision = $this->Manager->GetActionPrecisionControl($action['code']);

					echo '<tr>';
					echo '<td class="white-cell" style="width:60px;">' 	.$selection.			'</td>';
					echo '<td class="white-cell" style="width:160px;">' 	.$action['name'].		'</td>';
					echo '<td class="white-cell" style="width:30px;">' 	.$action['cost'].		'</td>';
					echo '<td class="white-cell" style="width:300px;">'	.$precision.			'</td>';
					echo '</tr>';
				}
			}
			echo '</table>';

		}

		// UNDERWORLD ACTIONS
		if( count($lUnderworldInstitutions) ) {
			echo '<hr width=70% />';

			echo '<span class="section-title">INSTITUTION INTERLOPE</span>';
			echo '<table>';

			echo '	<tr>
				<th class="black-cell" style="width:60px;">Sélection</th> 
				<th class="black-cell" style="width:160px;">Action</th> 
				<th class="black-cell" style="width:30px;">Coût</th> 
				<th class="black-cell" style="width:300px;">Précisions</th>
				</tr>';

			foreach($lPossibleActionList as $i => $action) {
				if($action['profilecode'] == 'I' && $action['level'] <= $lUnderworldLevel) {

					if( isset($lRegisteredActionList[$action['code']]) ) {
						$_POST[$action['code'].'-registered'] = $lRegisteredActionList[$action['code']];
					}

					$selection = $this->Manager->GetActionSelectionControl($action['code'], $action['maxpurchases'], $action['cost']);
					$precision = $this->Manager->GetActionPrecisionControl($action['code']);

					echo '<tr>';
					echo '<td class="white-cell" style="width:60px;">' 	.$selection.			'</td>';
					echo '<td class="white-cell" style="width:160px;">' 	.$action['name'].		'</td>';
					echo '<td class="white-cell" style="width:30px;">' 	.$action['cost'].		'</td>';
					echo '<td class="white-cell" style="width:300px;">'	.$precision.			'</td>';
					echo '</tr>';
				}
			}
			echo '</table>';

		}

		// COMMERCIAL ACTIONS
		if( count($lCommercialInstitutions) ) {
			echo '<hr width=70% />';

			echo '<span class="section-title">INSTITUTION COMMERCIALE</span>';
			echo '<table>';

			echo '	<tr>
				<th class="black-cell" style="width:60px;">Sélection</th> 
				<th class="black-cell" style="width:160px;">Action</th> 
				<th class="black-cell" style="width:30px;">Coût</th> 
				<th class="black-cell" style="width:300px;">Précisions</th>
				</tr>';

			foreach($lPossibleActionList as $i => $action) {
				if($action['profilecode'] == 'C' && $action['level'] <= $lCommercialLevel) {

					if( isset($lRegisteredActionList[$action['code']]) ) {
						$_POST[$action['code'].'-registered'] = $lRegisteredActionList[$action['code']];
					}

					$selection = $this->Manager->GetActionSelectionControl($action['code'], $action['maxpurchases'], $action['cost']);
					$precision = $this->Manager->GetActionPrecisionControl($action['code']);

					echo '<tr>';
					echo '<td class="white-cell" style="width:60px;">' 	.$selection.			'</td>';
					echo '<td class="white-cell" style="width:160px;">' 	.$action['name'].		'</td>';
					echo '<td class="white-cell" style="width:30px;">' 	.$action['cost'].		'</td>';
					echo '<td class="white-cell" style="width:300px;">'	.$precision.			'</td>';
					echo '</tr>';
				}
			}
			echo '</table>';

		}

		// ACADEMIC ACTIONS
		if( count($lAcademicInstitutions) ) {
			echo '<hr width=70% />';

			echo '<span class="section-title">INSTITUTION ACADÉMIQUE</span>';
			echo '<table>';

			echo '	<tr>
				<th class="black-cell" style="width:60px;">Sélection</th> 
				<th class="black-cell" style="width:160px;">Action</th> 
				<th class="black-cell" style="width:30px;">Coût</th> 
				<th class="black-cell" style="width:300px;">Précisions</th>
				</tr>';

			foreach($lPossibleActionList as $i => $action) {
				if($action['profilecode'] == 'A' && $action['level'] <= $lAcademicLevel) {

					if( isset($lRegisteredActionList[$action['code']]) ) {
						$_POST[$action['code'].'-registered'] = $lRegisteredActionList[$action['code']];
					}

					$selection = $this->Manager->GetActionSelectionControl($action['code'], $action['maxpurchases'], $action['cost']);
					$precision = $this->Manager->GetActionPrecisionControl($action['code']);

					echo '<tr>';
					echo '<td class="white-cell" style="width:60px;">' 	.$selection.			'</td>';
					echo '<td class="white-cell" style="width:160px;">' 	.$action['name'].		'</td>';
					echo '<td class="white-cell" style="width:30px;">' 	.$action['cost'].		'</td>';
					echo '<td class="white-cell" style="width:300px;">'	.$precision.			'</td>';
					echo '</tr>';
				}
			}
			echo '</table>';

		}

		// RELIGIOUS ACTIONS
		if( count($lReligiousInstitutions) ) {
			echo '<hr width=70% />';

			echo '<span class="section-title">INSTITUTION RELIGIEUSE</span>';
			echo '<table>';

			echo '	<tr>
				<th class="black-cell" style="width:60px;">Sélection</th> 
				<th class="black-cell" style="width:160px;">Action</th> 
				<th class="black-cell" style="width:30px;">Coût</th> 
				<th class="black-cell" style="width:300px;">Précisions</th>
				</tr>';

			foreach($lPossibleActionList as $i => $action) {
				if($action['profilecode'] == 'R' && $action['level'] <= $lReligiousLevel) {

					if( isset($lRegisteredActionList[$action['code']]) ) {
						$_POST[$action['code'].'-registered'] = $lRegisteredActionList[$action['code']];
					}

					$selection = $this->Manager->GetActionSelectionControl($action['code'], $action['maxpurchases'], $action['cost']);
					$precision = $this->Manager->GetActionPrecisionControl($action['code']);

					echo '<tr>';
					echo '<td class="white-cell" style="width:60px;">' 	.$selection.			'</td>';
					echo '<td class="white-cell" style="width:160px;">' 	.$action['name'].		'</td>';
					echo '<td class="white-cell" style="width:30px;">' 	.$action['cost'].		'</td>';
					echo '<td class="white-cell" style="width:300px;">'	.$precision.			'</td>';
					echo '</tr>';
				}
			}
			echo '</table>';

		}
		echo '<hr width=70% />';

		// Submenu
		echo '<button type="submit" name="option" value="Actions" class="submit-button" style="margin-right:5px"/>';
		echo 'Enregistrer';
		echo '</button>';

		echo '<button type="submit" name="option" value="Retour actions" class="submit-button" style="margin-left:5px" />';
		echo 'Retour';
		echo '</button>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY GROUP'S ACTIONS FOR ONE ACTIVITY--
	public function DisplayActivityActions()
	{
		// Data management
		// Data management
		$lActivity = $this->Manager->GetActivityByID( $_POST['view-actions'] );
		$lGroup = $this->Manager->GetActiveGroup();
		$lRegisteredActions = $lGroup->GetActivityActions( $lActivity->GetID() );

		$lMilitaryInstitutions = $lGroup->GetInstitutionsByProfile('M'); $lMilitaryLevel = 0;
			foreach($lMilitaryInstitutions as $institution) { if( $institution->GetLevel() > $lMilitaryLevel ) { $lMilitaryLevel = $institution->GetLevel(); }}
		$lUnderworldInstitutions = $lGroup->GetInstitutionsByProfile('I'); $lUnderworldLevel = 0;
			foreach($lUnderworldInstitutions as $institution) { if( $institution->GetLevel() > $lUnderworldLevel ) { $lUnderworldLevel = $institution->GetLevel(); }}
		$lCommercialInstitutions = $lGroup->GetInstitutionsByProfile('C'); $lCommercialLevel = 0;
			foreach($lCommercialInstitutions as $institution) { if( $institution->GetLevel() > $lCommercialLevel ) { $lCommercialLevel = $institution->GetLevel(); }}
		$lAcademicInstitutions = $lGroup->GetInstitutionsByProfile('A'); $lAcademicLevel = 0;
			foreach($lAcademicInstitutions as $institution) { if( $institution->GetLevel() > $lAcademicLevel ) { $lAcademicLevel = $institution->GetLevel(); }}
		$lReligiousInstitutions = $lGroup->GetInstitutionsByProfile('R'); $lReligiousLevel = 0;
			foreach($lReligiousInstitutions as $institution) { if( $institution->GetLevel() > $lReligiousLevel ) { $lReligiousLevel = $institution->GetLevel(); }}

		$lPossibleActionList = $this->Manager->GetPossibleActions();
		$lRegisteredActionList = $lGroup->GetActivityActions( $lActivity->GetID() );


		// Display!
		echo '<div>';
		echo '<span class="section-title">Actions pour '.$lActivity->GetName().'</span>';
		echo '<hr width=70% />';

		echo '<table>';

		echo '	<tr>
			<th class="black-cell" style="width:60px;">Sélection</th> 
			<th class="black-cell" style="width:160px;">Action</th> 
			<th class="black-cell" style="width:30px;">Coût</th> 
			<th class="black-cell" style="width:300px;">Précisions</th>
			<th class="black-cell" style="width:80px;">État</th>
			</tr>';

		foreach($lRegisteredActions as $i => $action) {

			$lStatus = "Inconnu";
			    if( $action['status'] == 'DEM' )   { $lStatus = "Demandée"; }
			elseif( $action['status'] == 'ACCEP' ) { $lStatus = "Traitée"; }
			elseif( $action['status'] == 'REFUS' ) { $lStatus = "Refusée"; }
			elseif( $action['status'] == 'REMPL' ) { $lStatus = "Remplacée"; }

			echo '<tr>';
			echo '<td class="white-cell" style="width:60px;">' 	.$action['purchases'].		'</td>';
			echo '<td class="white-cell" style="width:160px;">' 	.$action['name'].		'</td>';
			echo '<td class="white-cell" style="width:30px;">' 	.$action['cost'].		'</td>';
			echo '<td class="white-cell" style="width:300px;">'	.nl2br($action['moreinfo']).	'</td>';
			echo '<td class="white-cell" style="width:80px;">'	.$lStatus.			'</td>';
			echo '</tr>';

			if($action['status'] == 'REFUS'){
				echo '<tr><td class="grey-cell" colspan="5" style="width:auto;">Raison : ' .$action['reasonofdenial'].	'</td></tr>';
			}
			echo '<tr class="filler"></tr>';

		}

		echo '</table>';


		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-group"/>';

		echo '<button type="submit" name="option" value="Retour actions" class="smalltext-button"/>';
		echo 'Retour';
		echo '</button>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY GROUP'S INFLUENCE DETAIL--
	public function DisplayGroupInfluence()
	{
		// Prepare data for the form
		$lGroup = $this->Manager->GetActiveGroup();
		$lInfluenceList = $lGroup->GetInfluence();

		// Display the title.
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-group"/>';
		echo '<span class="section-title">Gains et dépenses d\'Influence</span>';
		echo '<hr width=70% />';

		// Display data table
		echo '<table>';
		echo '<tr>
			<th class="black-cell" style="width:10px;">#</th> 
			<th class="black-cell" style="width:100px;">Activité</th>
			<th class="black-cell" style="width:300px;">Raison</th>
			<th class="black-cell" style="width:60px;">Influence</th>
		      </tr>';

		foreach($lInfluenceList as $i => $influence) { 
			$line = $i+1;
			$amount = $influence['points']; if( $amount > 0 ) { $amount = "+".$amount; }
			echo '<tr>';
			echo '<td class="grey-cell" style="width:10px;">' 			.$line.				'</td>';
			echo '<td class="white-cell" style="width:100px;text-align:left">' 	.$influence['activityname'].	'</td>';
			echo '<td class="white-cell" style="width:300px;text-align:left">' 	.$influence['reason'].		'</td>';
			echo '<td class="white-cell" style="width:60px;">' 			.$amount.			'</td>';
			echo '</tr>';
		}

		echo '<tr><td colspan="4">';
			echo '<button type="submit" name="option" value="Retour influence" class="smalltext-button" style="margin-top: 5px;" />';
			echo 'Retour';
			echo '</button>';
		echo '</td></tr>';
		echo '</table>';

		echo '<hr width=70% />';
		echo '</div>';
	}


	//--DISPLAY ACTIVE GROUP'S DESCRIPTION--
	public function DisplayGroupDescription()
	{
		// Check if there's a manager
		if( $this->Manager == null ) { $this->Error = "No manager defined!"; return; }


		// Prepare data
		$lDescription = $this->Manager->GetActiveGroup()->GetDescription();

		// Display!
		echo '<div>';
		echo '<hr width=70% />';
		echo '<span class="section-title">Description du groupe</span>';

		echo '<span class="note" style="padding-top: 0px; text-align: left;">'.nl2br($lDescription).'</span>';

		echo '<hr width=70% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		if( $this->Manager->GetGroupManagerMode() ) {
			echo '<button type="submit" name="option" value="Éditer description groupe" class="smalltext-button" />';
			echo 'Éditer';
			echo '</button>';
		}

		echo '<button type="submit" name="option" value="Définition groupe" class="smalltext-button" style="margin-left: 5px;"/>';
		echo 'Retour';
		echo '</button>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY ACTIVE GROUP'S DESCRIPTION-EDITING FORM--
	public function DisplayDescriptionEditingForm()
	{
		// Check if there's a manager
		if( $this->Manager == null ) { $this->Error = "No manager defined!"; return; }


		// Display!
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-group"/>';

		echo '<hr width=70% />';

		echo '<div style="margin: auto; width: 620px;"><textarea name="description" cols="72" rows="18">' . $this->Manager->GetActiveGroup()->GetDescription() . '</textarea></div>';

		echo '<hr width=70% />';

		echo '<button type="submit" name="option" value="Description" class="submit-button" />';
		echo 'Enregistrer';
		echo '</button>';

		echo '<span class="note"><b>Note :</b> Vous pouvez agrémenter votre texte à l\'aide des balises HTML &lt;p&gt; (paragraphe), &lt;b&gt; (gras), &lt;i&gt; (italique) et &lt;u&gt; (souligné).</span>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY ACTIVE GROUP'S DESCRIPTION--
	public function DisplayGroupBackground()
	{
		// Check if there's a manager
		if( $this->Manager == null ) { $this->Error = "No manager defined!"; return; }


		// Prepare data
		$lBackground = $this->Manager->GetActiveGroup()->GetBackground();

		// Display!
		echo '<div>';
		echo '<hr width=70% />';
		echo '<span class="section-title">Histoire du groupe</span>';

		echo '<span class="note" style="padding-top: 0px; text-align: left;">'.nl2br($lBackground).'</span>';

		echo '<hr width=70% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		if( $this->Manager->GetGroupManagerMode() ) {
			echo '<button type="submit" name="option" value="Éditer histoire" class="smalltext-button" style="margin-right: 5px;"/>';
			echo 'Éditer';
			echo '</button>';
		}

		echo '<button type="submit" name="option" value="Définition groupe" class="smalltext-button" style="margin-left: 5px;"/>';
		echo 'Retour';
		echo '</button>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY ACTIVE GROUP'S BACKGROUND-EDITING FORM--
	public function DisplayBackgroundEditingForm()
	{
		// Check if there's a manager
		if( $this->Manager == null ) { $this->Error = "No manager defined!"; return; }


		// Display!
		echo '<div>';
		echo '<span class="section-title">Histoire du groupe</span>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-group"/>';

		echo '<div style="margin: auto; width: 620px;"><textarea name="background" cols="72" rows="18">' . $this->Manager->GetActiveGroup()->GetBackground() . '</textarea></div>';

		echo '<hr width=70% />';

		echo '<button type="submit" name="option" value="Historique" class="submit-button" />';
		echo 'Enregistrer';
		echo '</button>';

		echo '<span class="note"><b>Note :</b> Vous pouvez agrémenter votre texte à l\'aide des balises HTML &lt;p&gt; (paragraphe), &lt;b&gt; (gras), &lt;i&gt; (italique) et &lt;u&gt; (souligné).</span>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY ACTIVE GROUP'S QUESTS--
	public function DisplayGroupQuests()
	{
		// Prepare data
		$lQuestList = $this->Manager->GetActiveGroup()->GetQuests();


		// Display!
		echo '<div>';
		echo '<hr width=70% />';

		// Submenu
		if( $this->Manager->GetGroupManagerMode() ) {
			echo '<form method="post">';
			echo '<input type="hidden" name="action" value="select-menu-option"/>';

			echo '<button type="submit" name="option" value="Nouvelle quête groupe" class="smalltext-button"/>';
			echo 'Nouvelle';
			echo '</button>';

			echo '</form>';
		}

		// Quests
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-group-lists"/>';

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
			    if( in_array($quest->GetStatus(), array('DEM', 'REPR', 'SUITE', 'ACTIF' )) && $this->Manager->GetGroupManagerMode()) 
				{ $lButtons .= ' <button type="submit" name="cancel-quest" value="' .$quest->GetID(). '" class="icon-button"/><img src="images/icon_delete.png" class="icon-button-image"></button>'; }
			elseif( $quest->GetStatus() == 'ANNUL' && $this->Manager->GetGroupManagerMode()) 
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
		// Prepare data
		$lKingdoms = $_SESSION['masterlist']->GetKingdoms();
		$lCounties = $_SESSION['masterlist']->GetCounties();
		$lInstitutionList = $this->Manager->GetActiveGroup()->GetInstitutions();


		$lSubjectInput = '<select name="quest">';
			$lSubjectInput .= '<option value="ND|S.O.|0">Veuillez choisir une quête...</option>';
			$lSubjectInput .= '<optgroup label="Avantages spécifiques">';
				$lSubjectInput .= '<option value="AVANT|NOUVEAU|Nouvel avantage">Nouvel avantage</option>';
				$lSubjectInput .= '<option value="AVANT|RECUP|Récupérer un avantage">Récupérer un avantage perdu</option>';
			$lSubjectInput .= '<optgroup label="Histoire">';
				$lSubjectInput .= '<option value="HISTO|RETRAIT|Retraite">Retraite de groupe</option>';
				$lSubjectInput .= '<option value="HISTO|TRAME|Trame">Trame spéciale</option>';
			$lSubjectInput .= '</optgroup>';		 
		$lSubjectInput .= '</select>';


		$lCountyInput = '<select name="county" onchange="displayCountyInformationJS(this.value)">';
		$lCountyInput .= '<option>Veuillez choisir...</option>';	
		foreach( $lKingdoms as $kingdom )
		{			
			$lCountyInput .= '<optgroup label="' . $kingdom['name'] . '">';			
			foreach( $lCounties as $county )
			{					    
				if( $county['status'] == 'ACTIF' && $county['kingdomcode'] == $kingdom['code'] ) 
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
		echo '<input type="hidden" name="action" value="manage-group"/>';

		echo '<div style="width:70%; margin:auto">';
		echo '<p align="left"><b>Veuillez noter que les quêtes de groupe servent uniquement à obtenir ou récupérer un avantage spécifique à votre groupe. Des exemples valides de demande incluent :</b></p>';
		echo '<div style="margin-left:50px;" align="left">- Un avantage lié à un bâtiment (ex.: un laboratoire qui fournit soleil et feu lorsque requis par une recette).</div >';
  		echo '<div style="margin-left:50px;" align="left">- Un objet spécial ou une relique (requiert une Institution du bon type/niveau).</div >';
		echo '<div style="margin-left:50px;" align="left">- Une pièce, une zone ou un outil spécial en jeu.</div >';
		echo '<div style="margin-left:50px;" align="left">- Un titre ou un statut spécial dans la géopolitique.</div >';
		echo '<div style="margin-left:50px;" align="left">- La création d\'une Trame spéciale permettant d\'atteindre l\'un des buts du groupe.</div>';
		echo '<p align="left">Pour toutes questions, veuillez envoyer un courriel à <b>« Quetes@Terres-de-Belenos.com »</b>. Prenez note que les avantages sont toujours balancés par l\'Organisation et non par les joueurs et qu«\'ils sont tous volables ou annulables d\'une certaine manière.</p>';
		echo '<p></p>';
		echo '</div>';
		echo '<hr width=70% />';
		
		echo '<div>';
		echo '<span class="section-title">Formulaire de demande</span><br/>';
		echo '<table>';
		echo '<tr><td class="inputname"  style="float:left;">Objet de la demande</td>	<td class="inputbox" style="width:215px;">' . $lSubjectInput . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname"  style="float:left;">Comté ciblé</td>		<td class="inputbox" style="width:215px;">' . $lCountyInput . '</td></tr>';
		echo '</table>';
		echo '</div>';
		
		echo '<tr class="filler"></tr>';
		
		echo '<span id="leadername"><b>Dirigeant du comté sélectionné :</b></span>';
		echo '<span id="leaderinfo"><b>Description du dirigeant :</b></span>';
		echo '<span id="scribename"><b>Scribe :</b></span>';

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
		// Prepare data
		$index = $_POST['select-quest'];
		$lQuest = $this->Manager->GetActiveGroup()->GetQuests()[$index];

		$lStatus = "En attente d'approbation";
			    if( $lQuest->GetStatus() == 'ACTIF' ) { $lStatus = "Approuvée et en cours"; }
			elseif( $lQuest->GetStatus() == 'TERM' )  { $lStatus = "Quête terminée"; }
			elseif( $lQuest->GetStatus() == 'RECOM' ) { $lStatus = "Quête récompensée"; }
			elseif( $lQuest->GetStatus() == 'ANNUL' ) { $lStatus = "Annulée par le joueur"; }
			elseif( $lQuest->GetStatus() == 'REFUS' ) { $lStatus = "Refusée par l'Organisation"; }
			elseif( $lQuest->GetStatus() == 'SUITE' ) { $lStatus = "Suite de la quête en cours"; }


		// Display!
		echo '<div>';
		echo '<span class="section-title">Détail de la quête</span>';
		echo '<hr width=80% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-group-lists"/>';
		echo '<input type="hidden" name="select-quest" value="'.$index.'"/>';

		// Quests
		echo '<table>';
		echo '<tr><td class="labelname">Objet de la quête</td>		<td class="labelvalue" style="width: 450px;">' .$lQuest->GetSubject(). '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Comté choisi</td>		<td class="labelvalue" style="width: 450px;">' .$lQuest->GetCountyName(). '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">État</td>			<td class="labelvalue" style="width: 450px;">' .$lStatus. '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Date de la demande</td>		<td class="labelvalue" style="width: 450px;">' .str_replace(":", "h", substr($lQuest->GetRequestDate(), 0, 16)). '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Date d\'approbation</td>	<td class="labelvalue" style="width: 450px;">' .str_replace(":", "h", substr($lQuest->GetApprovalDate(), 0, 16)). '</td></tr>';
		echo '<tr><td colspan="2"><hr style="margin-top: 10px;" /></td></tr>';

		echo '<tr><td colspan="2" class="labelname" style="text-align: center;">Suggestions données</td></tr>';
		echo '</table>';

		echo '<div style="margin: auto; margin-top: 10px; margin-bottom: 10px; width: 620px; text-align:left;"><span>' .nl2br( $lQuest->GetSuggestions() ). '</span></div>';	

		echo '<hr width=80% />';

		echo '</form>';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Quêtes groupe" class="smalltext-button"/>';
		echo 'Retour';
		echo '</button>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY SELECTED QUEST DETAIL--
	public function DisplaySelectedQuestPart()
	{
		// Prepare data
		$lQuestIndex = $_POST['select-quest'];
		$lPartIndex = $_POST['select-questpart'];
		$lPart = $this->Manager->GetActiveGroup()->GetQuests()[$lQuestIndex]->GetParts()[$lPartIndex];

		$lQuestSubject = $this->Manager->GetActiveGroup()->GetQuests()[$lQuestIndex]->GetSubject();

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
		echo '<input type="hidden" name="action" value="manage-group-lists"/>';

		echo '<button type="submit" name="select-quest" value="'.$lQuestIndex.'" class="smalltext-button"/>';
		echo 'Retour';
		echo '</button>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY ACTIVE GROUP'S RESUMES' LIST--
	public function DisplayGroupResumes()
	{
		// Prepare data
		$lResumeList = $this->Manager->GetActiveGroup()->GetResumes();


		// Display!
		echo '<div>';
		echo '<hr width=70% />';

		// Submenu
		if( $this->Manager->GetGroupManagerMode() ) {
			echo '<form method="post">';
			echo '<input type="hidden" name="action" value="select-menu-option"/>';

			echo '<button type="submit" name="option" value="Nouveau résumé groupe" class="smalltext-button"/>';
			echo 'Nouveau';
			echo '</button>';

			echo '</form>';
		}

		// Resumés
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-group-lists"/>';

		echo '<table style="text-align: left;">';
		echo '<tr><th class="black-cell" style="width:100px;">Activité</th>
			  <th class="black-cell" style="width:260px;">Quête</th> 
			  <th class="black-cell" style="width:120px;">Création</th>
			  <th class="black-cell" style="width:40px;"></th></tr>';

		foreach($lResumeList as $i => $resume) {

			$lQuest = "";
				if( $resume->GetQuest()->GetID() ) { 
					$lQuest .= $resume->GetQuest()->GetSubject(); 
				}

			$lButton = '<button type="submit" name="select-resume" value="' .$i. '" class="icon-button"/><img src="images/icon_see.png" class="icon-button-image"></button>';
				if( $this->Manager->GetGroupManagerMode() ) { $lButton .= '<button type="submit" name="edit-resume" value="' .$i. '" class="icon-button"/><img src="images/icon_edit.png" class="icon-button-image"></button>'; }


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
		// Prepare data
		$lActivityList = $this->Manager->GetActivities();
			if( !$lActivityList ) { $lActivityList = array(); }

		$lQuestList = $this->Manager->GetActiveGroup()->GetQuests();
			if( !$lQuestList ) { $lQuestList = array(); }

		$lActivityInput = '<select name="activity"><option value="" selected></option>';
		foreach ($lActivityList as $activity) {
			$lActivityInput .= '<option value="' .$activity->GetID(). '">' .$activity->GetName(). '</option>';
		}
		$lActivityInput .= '</select>';

		$lQuestInput = '<select name="questid"><option value="-" selected>Aucune</option>';
		foreach ($lQuestList as $quest) {
			if( $quest->GetStatus() == 'ACTIF' ) {
				$selected = "";
					if( isset($_POST['questid']) &&  $_POST['questid'] == $quest->GetID() ) { $selected = "selected"; }

				$lQuestInput .= '<option value="'.$quest->GetID().'" ' .$selected. '>' .$quest->GetSubject(). '</option>';			
			}
		}
		$lQuestInput .= '</select>';

		$lText = "";
			if( isset($_POST['text']) ) { $lText = $_POST['text']; }


		// Display!
		echo '<div>';
		echo '<hr width=70% />';

		// Quests
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-group"/>';

		echo '<span class="note"><b><u>IMPORTANT</b></u><br />Il vous est <b>fortement</b> recommandé d\'écrire d\'abord vos textes à l\'aide d\'un logiciel de traitement de texte (ex.: Word) et de les conservés sur votre ordinateur.</span>';

		echo '<table>';
		echo '<tr><td class="inputname">Activité</td>		<td class="inputbox" style="width: 165px;">' . $lActivityInput . '</td></tr>';
		if(GROUP_QUESTS_ENABLED) {
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Quête</td>		<td class="inputbox" style="width: 165px;">' . $lQuestInput . '</td></tr>';		
		}
		echo '</table>';

		echo '<div style="margin: auto; margin-top: 10px; margin-bottom: 10px; width: 620px;"><textarea name="text" cols="72" rows="18" placeholder="Entrez votre résumé ici! Tenez-vous en aux informations pertinentes aux rédacteurs, qui leur permettent de personnaliser vos quêtes.">' . $lText . '</textarea></div>';	

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
		// Prepare data
		$index = $_POST['select-resume'];
		$lResume = $this->Manager->GetActiveGroup()->GetResumes()[$index];
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

		echo '<button type="submit" name="option" value="Résumés groupe" class="smalltext-button"/>';
		echo 'Retour';
		echo '</button>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY NEW RESUMÉ'S MODIFICATION FORM--
	public function DisplayResumeModificationForm()
	{
		// Prepare data
		$index = $_POST['edit-resume'];
		$lResume = $this->Manager->GetActiveGroup()->GetResumes()[$index];
		$lActivity = $lResume->GetActivity(); 

		$lQuestList = $this->Manager->GetActiveGroup()->GetQuests();
			if( !$lQuestList ) { $lQuestList = array(); }

		$lQuestInput = '<select name="questid"><option value="-">Aucune</option>';
		foreach ($lQuestList as $quest) {
			if( $quest->GetStatus() == 'ACTIF' ) {
				$selected = "";
					if( isset($_POST['questid']) &&  $_POST['questid'] == $quest->GetID() ) { $selected = "selected"; }

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
		echo '<input type="hidden" name="action" value="manage-group"/>';
		echo '<input type="hidden" name="resumeid" value="' .$lResume->GetID(). '"/>';

		echo '<table>';
		echo '<tr><td class="inputname">Activité</td>	<td class="labelvalue" style="width: 165px;">' . $lActivity->GetName() . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Quête</td>	<td class="inputbox" style="width: 165px;">' . $lQuestInput . '</td></tr>';
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


	//--DISPLAY GROUP MEMBERS--
	public function DisplayObjectives()
	{
		// Check if user and manager is set
		if( $this->Manager == null ) { $this->Error = "No manager defined!"; return; }


		// Build invitation list
		$lObjectives = $this->Manager->GetActiveGroup()->GetObjectives();


		// Display!
		echo '<div>';
		echo '<span class="section-title">Liste des objectifs du groupe</span>';
		echo '<hr width=70% />';

		// Submenu
		if( $this->Manager->GetGroupManagerMode() ) {
			echo '<form method="post">';
			echo '<input type="hidden" name="action" value="select-menu-option"/>';

			echo '<button type="submit" name="option" value="Ajouter objectif" class="smalltext-button" style="margin-right: 5px;"/>';
			echo 'Ajouter';
			echo '</button>';

			echo '<button type="submit" name="option" value="Dévoiler objectifs" class="smalltext-button" style="margin-right: 5px; margin-left: 5px;"/>';
			echo 'Dévoiler';
			echo '</button>';

			echo '<button type="submit" name="option" value="Retirer objectifs" class="smalltext-button" style="margin-left: 5px;"/>';
			echo 'Retirer';
			echo '</button>';

			echo '</form>';

			echo '<hr width=70% />';
		}

		// Objectives
		foreach($lObjectives as $i => $objective) {
			$lType = "BUT AVOUÉ : ";	if( $objective['type'] == 'CACHE' ) { $lType = "BUT CACHÉ : "; }
			echo '<span class="note" style="padding-bottom: 5px;"><b>
					<span style="display: inline; color: rgb(0, 112, 192)">' .$lType. '</span>' 
				.$objective['name']. '
			      </b></span>';
			echo '<span class="note" style="padding-top: 0px;">' 		.nl2br($objective['description']). 	'</span>';
			echo '<hr width=30% /*style="color: rgb(0, 112, 192); background-color: rgb(0, 112, 192);"*/ />';
		}

		echo '</div>';
	}


	//--DISPLAY ADD OBJECTIVE FORM--
	public function DisplayAddObjectiveForm()
	{
		// Check if user and manager is set
		if( $this->Manager == null ) { $this->Error = "No manager defined!"; return; }


		// Prepare data
		$lObjective = ""; 	if( isset($_POST['objective']) ) 	{ $lObjective = $_POST['objective']; }
		$lDescription = ""; 	if( isset($_POST['description']) ) 	{ $lDescription = $_POST['description']; }
		$lPublicGoal = ''; 	if( isset($_POST['type']) &&  $_POST['type'] == 'AVOUE' ) 	{ $lPublicGoal = 'checked'; }
		$lHiddenGoal = ''; 	if( isset($_POST['type']) &&  $_POST['type'] == 'CACHE' ) 	{ $lHiddenGoal = 'checked'; }


		// Display!
		echo '<div>';
		echo '<span class="section-title">Ajouter un objectif de groupe</span>';
		echo '<hr width=70% />';

		// Search controls
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-group"/>';

		echo '<table>';
		echo '<tr><td class="inputname">Objectif</td>		<td class="inputbox"><input name="objective" type="text" value="' .$lObjective. '" style="width: 460px;" maxlength="50"/></td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Type</td>		<td class="inputradio"><input name="type" type="radio" value="AVOUE" '.$lPublicGoal.'> Avoué 
												<input name="type" type="radio" value="CACHE" '.$lHiddenGoal.'> Caché </td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Description</td>	<td class="inputbox"><textarea name="description" cols="56" rows="18">'.$lDescription.'</textarea></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td colspan="2">';
			echo '<button type="submit" name="option" value="Ajouter objectif" class="submit-button" />';
			echo 'Ajouter';
			echo '</button>';
		echo '</td></tr>';
		echo '</table>';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY REVEAL OBJECTIVE FORM--
	public function DisplayRevealObjectiveForm()
	{
		// Check if user and manager is set
		if( $this->Manager == null ) { $this->Error = "No manager defined!"; return; }


		// Build invitation list
		$lObjectives = $this->Manager->GetActiveGroup()->GetObjectives();
		$lHiddenObjectives = array();
		foreach($lObjectives as $i => $objective) {
			if( $objective['type'] == 'CACHE' ) { $lHiddenObjectives[] = $objective; }
		}


		// Display!
		echo '<div>';
		echo '<span class="section-title">Dévoiler des objectifs</span>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-group-lists"/>';


		// Objectives
		echo '<span class="note">Dévoiler un objectif permet d\'avouer un objectif caché.</span>';

		echo '<table>';

		echo '	<tr>
			<th class="black-cell" style="width:10px;">#</th> 
			<th class="black-cell" style="width:250px;">Objectif</th> 
			<th class="black-cell" style="width:16px;"></th>
			</tr>';

		foreach($lHiddenObjectives as $i => $objective) {
			$line = $i + 1;
			$lButtons = '<button type="submit" name="reveal-objective" value="' .$objective['id']. '" class="icon-button"/><img src="images/icon_see.png" class="icon-button-image"></button>';

			echo '<tr>';
			echo '<td class="grey-cell" style="width:10px;">' 	.$line.			'</td>';
			echo '<td class="white-cell" style="width:250px;">' 	.$objective['name'].	'</td>';
			echo '<td class="white-cell" style="width:16px;">' 	.$lButtons.		'</td>';
			echo '</tr>';
		}

		echo '</table>';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY REMOVE OBJECTIVE FORM--
	public function DisplayRemoveObjectiveForm()
	{
		// Check if user and manager is set
		if( $this->Manager == null ) { $this->Error = "No manager defined!"; return; }


		// Build invitation list
		$lObjectives = $this->Manager->GetActiveGroup()->GetObjectives();


		// Display!
		echo '<div>';
		echo '<span class="section-title">Retirer des objectifs</span>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-group-lists"/>';


		// Objectives
		echo '<table>';

		echo '	<tr>
			<th class="black-cell" style="width:10px;">#</th> 
			<th class="black-cell" style="width:250px;">Objectif</th> 
			<th class="black-cell" style="width:16px;"></th>
			</tr>';

		foreach($lObjectives as $i => $objective) {
			$line = $i + 1;
			$lButtons = '<button type="submit" name="remove-objective" value="' .$objective['id']. '" class="icon-button"/><img src="images/icon_delete.png" class="icon-button-image"></button>';

			echo '<tr>';
			echo '<td class="grey-cell" style="width:10px;">' 	.$line.			'</td>';
			echo '<td class="white-cell" style="width:250px;">' 	.$objective['name'].	'</td>';
			echo '<td class="white-cell" style="width:16px;">' 	.$lButtons.		'</td>';
			echo '</tr>';
		}

		echo '</table>';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY GROUP'S STATUS MODIFICATION FORM--
	public function DisplayGroupStatusForm()
	{
		// Check if user and manager is set
		if( $this->Manager == null ) { $this->Error = "No manager defined!"; return; }


		// Prepare status list
		$lStatusInput = '<select name="status">
					<option value="ACTIF">Actif</option>
					<option value="INACT">Inactif</option>
					<option value="ERAD">Éradiqué</option>
					<option value="DISSO">Dissout</option>
					<option value="RETIR">Retraité</option>
				 </select>';

		// Display! This form does not require any data.
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-group"/>';

		echo '<hr width=70% />';
		echo '<span class="section-title" style="margin-bottom:10px">Modifier le statut du groupe</span>';

		echo '<table>';
		echo '<tr><td class="inputname">Nouveau statut</td>	<td class="inputbox">'	.$lStatusInput.	'</td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td colspan="2">';
			echo '<button type="submit" name="option" value="Statut" class="submit-button" />';
			echo 'Enregistrer';
			echo '</button>';
		echo '</td></tr>';
		echo '</table>';

		echo '<hr width=70% />';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY GROUP MEMBERS--
	public function DisplayMembers()
	{
		// Check if user and manager is set
		if( $this->Manager == null ) { $this->Error = "No manager defined!"; return; }


		// Build invitation list
		$lMembers = $this->Manager->GetActiveGroup()->GetMembers();


		// Display!
		echo '<div>';
		echo '<hr width=70% />';
		echo '<span class="section-title">Liste des membres</span>';

		// Submenu
		if( $this->Manager->GetGroupManagerMode() ) {
			echo '<form method="post">';
			echo '<input type="hidden" name="action" value="select-menu-option"/>';

			echo '<button type="submit" name="option" value="Inviter membres" class="smalltext-button" style="margin-right: 5px;"/>';
			echo 'Inviter';
			echo '</button>';

			echo '<button type="submit" name="option" value="Expulser membres" class="smalltext-button" style="margin-left: 5px;"/>';
			echo 'Expulser';
			echo '</button>';

			echo '</form>';
		}

		// Members
		echo '<table>';

		echo '	<tr>
			<th class="black-cell" style="width:20px;">#</th> 
			<th class="black-cell" style="width:200px;">Personnage</th> 
			<th class="black-cell" style="width:200px;">Joueur</th> 
			<th class="black-cell" style="width:100px;">Dernier GN</th>
			</tr>';

		foreach($lMembers as $i => $member) {
			$line = $i + 1;
			echo '<tr>';
			echo '<td class="grey-cell" style="width:20px;">' 	.$line.							'</td>';
			echo '<td class="white-cell" style="width:200px;">' 	.$member->GetFullName().				'</td>';
			echo '<td class="white-cell" style="width:200px;">' 	.$member->GetUserName().' ('.$member->GetUserAccount().')</td>';
			echo '<td class="white-cell" style="width:100px;">' 	.$member->GetCharacterAttendances()->GetName().		'</td>';
			echo '</tr>';
		}

		echo '</table>';

		echo '</div>';
	}


	//--DISPLAY INVITE MEMBERS FORM--
	public function DisplayInviteMembersForm()
	{
		// Check if user and manager is set
		if( $this->Manager == null ) { $this->Error = "No manager defined!"; return; }


		// Prepare data
		$lAccount = ""; if( isset($_POST['account']) ) { $lAccount = $_POST['account']; }
		$lFirstName = ""; if( isset($_POST['firstname']) ) { $lFirstName = $_POST['firstname']; }
		$lLastName = ""; if( isset($_POST['lastname']) ) { $lLastName = $_POST['lastname']; }


		// Display!
		echo '<div>';
		echo '<span class="section-title">Inviter un personnage à joindre le groupe</span>';
		echo '<hr width=70% />';

		// Search controls
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="search"/>';
		echo '<span class="section-title">Chercher un personnage</span>';

		echo '<table>';
		echo '<tr><td class="inputname">Compte joueur</td>	<td class="inputbox"><input name="account" type="text" value="' .$lAccount. '" maxlength="32"/></td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Prénom</td>		<td class="inputbox"><input name="firstname" type="text" value="' .$lFirstName. '" maxlength="50"/></td></tr>'
		;
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Nom/Famille/Clan</td>	<td class="inputbox"><input name="lastname" type="text" value="' .$lLastName. '" maxlength="50"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td colspan="2">';
			echo '<button type="submit" name="option" value="Personnages" class="submit-button" />';
			echo 'Rechercher';
			echo '</button>';
		echo '</td></tr>';
		echo '</table>';

		echo '</form>';

		echo '<span class="note"><b>Note :</b> Les personnages de niveau 0 ne sont pas considérés dans la recherche.</span>';

		echo '<hr width=70% />';


		// Results
		if( isset($_POST['search_results']) ) {
			$lCharacters = $_POST['search_results'];

			echo '<form method="post">';
			echo '<input type="hidden" name="action" value="manage-group-lists"/>';
			echo '<span class="section-title">Choisir un personnage à inviter</span>';

			echo '<table>';

			echo '	<tr>
				<th class="black-cell" style="width:10px;">#</th> 
				<th class="black-cell" style="width:250px;">Personnage</th> 
				<th class="black-cell" style="width:250px;">Joueur</th> 
				<th class="black-cell" style="width:16px;"></th>
				</tr>';

			foreach($lCharacters as $i => $character) {
				$line = $i + 1;
				$lButtons = '<button type="submit" name="invite-member" value="' .$character['characterid']. '" class="icon-button"/><img src="images/icon_plus.png" class="icon-button-image"></button>';

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

		echo '</div>';
	}


	//--DISPLAY REMOVE MEMBERS FORM--
	public function DisplayRemoveMembersForm()
	{
		// Check if user and manager is set
		if( $this->Manager == null ) { $this->Error = "No manager defined!"; return; }


		// Build invitation list
		$lMembers = $this->Manager->GetActiveGroup()->GetMembers();


		// Display!
		echo '<div>';
		echo '<span class="section-title">Retirer des membres</span>';
		echo '<hr width=70% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Voir membres" class="smalltext-button"/>';
		echo 'Retour';
		echo '</button>';

		echo '</form>';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-group-lists"/>';


		// Members
		echo '<table>';

		echo '	<tr>
			<th class="black-cell" style="width:10px;">#</th> 
			<th class="black-cell" style="width:160px;">Personnage</th> 
			<th class="black-cell" style="width:160px;">Joueur</th> 
			<th class="black-cell" style="width:80px;">Classe</th> 
			<th class="black-cell" style="width:80px;">Race</th>
			<th class="black-cell" style="width:16px;"></th>
			</tr>';

		foreach($lMembers as $i => $member) {
			$line = $i + 1;
			$lButtons = '<button type="submit" name="remove-member" value="' .$i. '" class="icon-button"/><img src="images/icon_delete.png" class="icon-button-image"></button>';

			echo '<tr>';
			echo '<td class="grey-cell" style="width:10px;">' 	.$line.							'</td>';
			echo '<td class="white-cell" style="width:160px;">' 	.$member->GetFullName().				'</td>';
			echo '<td class="white-cell" style="width:160px;">' 	.$member->GetUserName().' ('.$member->GetUserAccount().	')</td>';
			echo '<td class="white-cell" style="width:80px;">' 	.$member->GetClass().					'</td>';
			echo '<td class="white-cell" style="width:80px;">' 	.$member->GetRace().					'</td>';
			echo '<td class="white-cell" style="width:16px;">' 	.$lButtons.						'</td>';
			echo '</tr>';
		}

		echo '</table>';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY GROUP BASE CAMP INFORMATIONS--
	public function DisplayBaseCampInformation()
	{
		// Check if user and manager is set
		if( $this->Manager == null ) { $this->Error = "No manager defined!"; return; }


		// Build invitation list
		$lBaseCamp = $this->Manager->GetActiveGroup()->GetBaseCamp();
		$lBuildingNotes = $this->Manager->GetActiveGroup()->GetMoreInformation();


		// Display!
		echo '<div>';
		echo '<span class="section-title">Informations relatives au campement du groupe</span>';
		echo '<hr width=70% />';

		echo '<table>';
		echo '<tr><td class="labelname" style="width:120px">Camp de base</td>	<td class="labelvalue" style="width:450px">'	.$lBaseCamp['name'].	'</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname" colspan="2" style="width:600px">Travaux requis :</td></tr>';
		echo '<td colspan="2" style="width:600px"><span style="padding-top: 0px; text-align:left;">' .nl2br($lBuildingNotes).'</span></td></tr>';
		echo '</table>';

		echo '<hr width=70% />';


		// Submenu
		if( $this->Manager->GetGroupManagerMode() ) {
			echo '<form method="post">';
			echo '<input type="hidden" name="action" value="select-menu-option"/>';

			echo '<button type="submit" name="option" value="Éditer campement" class="smalltext-button" style="margin-right: 5px;"/>';
			echo 'Éditer';
			echo '</button>';

			echo '</form>';
		}

		echo '</div>';
	}


	//--DISPLAY ACTIVE GROUP'S BASE CAMP INFORMATION-EDITING FORM--
	public function DisplayBaseCampInformationEditingForm()
	{
		// Check if there's a manager
		if( $this->Manager == null ) { $this->Error = "No manager defined!"; return; }


		// Build invitation list
		$lCampList = $this->Manager->GetCamps();
		$lBaseCamp = $this->Manager->GetActiveGroup()->GetBaseCamp();
		$lBuildingNotes = $this->Manager->GetActiveGroup()->GetMoreInformation();

		$lBaseCampInput = '<select name="basecampcode">';
		foreach( $lCampList as $camp ){
			$selected = ""; if( $camp['code'] == $lBaseCamp['code'] ) { $selected = 'selected'; }
			$lBaseCampInput .= '<option value="' .$camp['code']. '" '.$selected.'>' .$camp['name']. '</option>';
		}
		$lBaseCampInput .= '</select>';


		// Display!
		echo '<div>';
		echo '<span class="section-title">Informations relatives au campement du groupe</span>';
		echo '<hr width=70% />';

		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-group"/>';

		echo '<table>';
		echo '<tr><td class="inputname" style="width:120px">Camp de base</td>	<td class="inputvalue" style="width:450px">'	.$lBaseCampInput.	'</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Travaux requis</td><td></td></tr>';
		echo '<td colspan="2" style="width:600px; text-align:left;"><textarea name="information" cols="60" rows="18">' . $lBuildingNotes . '</textarea></td></tr>';
		echo '</table>';
		echo '';

		echo '<hr width=70% />';

		echo '<button type="submit" name="option" value="Retour campement" class="submit-button" style="margin-right:5px;"/>';
		echo 'Annuler';
		echo '</button>';

		echo '<button type="submit" name="option" value="Campement" class="submit-button" style="margin-left:5px;"/>';
		echo 'Enregistrer';
		echo '</button>';

		echo '<span class="note"><b>Note :</b> Vous pouvez agrémenter votre texte à l\'aide des balises HTML &lt;p&gt; (paragraphe), &lt;b&gt; (gras), &lt;i&gt; (italique) et &lt;u&gt; (souligné).</span>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY ACTIVE GROUP'S ADVANTAGES--
	public function DisplayGroupAdvantages()
	{
		// Check if there's a manager
		if( $this->Manager == null ) { $this->Error = "No manager defined!"; return; }


		// Prepare data
		$lAdvantageList = $this->Manager->GetActiveGroup()->GetAdvantages();


		// Display!
		echo '<div>';
		echo '<hr width=70% />';

		echo '<table style="text-align: left;">';
		echo '<tr>';
		echo '<th class="black-cell" style="width:200px;">Avantage</th>';
		echo '<th class="black-cell" style="width:380px;">Description</th>';
		echo '</tr>';

		foreach($lAdvantageList as $i => $advantage) {
			echo '<tr>';
			echo '<td class="white-cell" style="width:200px;">' 	.$advantage['name'].		'</td>';
			echo '<td class="white-cell" style="width:380px;">'	.$advantage['description'].	'</td>';
			echo '</tr>';
		}
		echo '</table>';

		echo '<hr width=70% />';

		echo '</div>';
	}


	//--DISPLAY ACTIVE GROUP'S LOGS--
	public function DisplayGroupLogs()
	{
		// Check if there's a manager
		if( $this->Manager == null ) { $this->Error = "No manager defined!"; return; }


		// Prepare data 
		$lMessageList = $this->Manager->GetActiveGroup()->GetLogs();


		// Display skills
		echo '<div>';
		echo '<hr width=70% />';

		echo '<table style="text-align: left;">';
		echo '<tr><th class="black-cell" style="width:500px;">Message</th> <th class="black-cell" style="width:80px;">Date</th></tr>';

		foreach($lMessageList as $message) {
			echo '<tr>';
			echo '<td class="white-cell" style="width:500px;">' . $message['message'] . '</td>';
			echo '<td class="white-cell" style="width:80px;">' . substr($message['date'], 0, 10) . '</td>';
			echo '</tr>';
		}

		echo '</table>';

		echo '<hr width=70% />';

		echo '</div>';
	}


	//--DISPLAY GROUP'S FORM FOR PiC MANAGEMENT--
	public function DisplayPICManagementForm()
	{
		// Check if there's a character...
		if( $this->Manager == null ) { $this->Error = "No manager defined!"; return; }


		// Display! This form does not require any data.
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-group"/>';

		echo '<hr width=70% />';

		echo '<span class="section-title">Nommer un nouveau responsable</span>';
		echo '<span class="note">Prenez note qu\'un individu ne peut être responsable que d\'un seul groupe à la fois.</span>';
		echo '<table>';
		echo '<tr>';
		echo '<td class="inputname">Compte du responsable</td>';
		echo '<td class="inputbox"><input name="newpic" type="text" value="" maxlength="32"/></td>';
		echo '</tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td colspan="2">';
			echo '<button type="submit" name="option" value="Ajouter responsable" class="submit-button" />';
			echo 'Nommer';
			echo '</button>';
		echo '</td></tr>';
		echo '</table>';

		if( $this->Manager->GetActiveGroup()->GetPeopleInChargeCount() > 1 ) {
			echo '<hr width=70% />';
	
			echo '<span class="section-title">Quitter mes fonctions de responsable</span>';
			echo '<span class="note">Si vous n\'êtes pas le dernier responsable de votre groupe, vous pouvez vous retirer de ce rôle en appuyant sur « Me retirer ». Autrement, nommer d\'abord un autre responsable.</span>';
	
			echo '<button type="submit" name="option" value="Retirer responsable" class="submit-button" />';
			echo 'Me retirer';
			echo '</button>';
		}

		echo '<hr width=70% />';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY GROUP'S RENAME FORM--
	public function DisplayGroupRename()
	{
		// Check if user and manager is set
		if( $this->Manager == null ) { $this->Error = "No manager defined!"; return; }


		// Display! This form does not require any data.
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-group"/>';

		echo '<hr width=70% />';
		echo '<span class="section-title" style="margin-bottom:10px">Renommer le groupe</span>';

		echo '<table>';
		echo '<tr><td class="inputname">Nom du groupe</td>	<td class="inputbox"><input name="name" type="text" value="' .$this->Manager->GetActiveGroup()->GetName(). '" maxlength="50"/></td></tr>';
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


} // END of GroupUI class

?>
