<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== User Views v1.2 r13 ==				║
║	Display user creation, update and consultation UIs.	║
║	Requires user model.					║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('models/user.class.php');

class UserUI
{

protected $User;

protected $Error;

	//--CONSTRUCTOR--
	public function __construct($inUser)
	{
		$this->User = $inUser;
	}


	//--DISPLAY REGISTRATION FORM--
	public function DisplayRegForm()
	{
		// Get user's gender
		$Mchecked = ''; if( $this->User->GetGender() == 'M' ) { $Mchecked = 'checked'; }
		$Fchecked = ''; if( $this->User->GetGender() == 'F' ) { $Fchecked = 'checked'; }
		$Achecked = ''; if( $this->User->GetGender() == 'A' ) { $Achecked = 'checked'; }
		$pw = ''; $pwc = '';
		if( isset($_POST['password']) ) { $pw = $_POST['password']; }
		if( isset($_POST['pw-confirm']) ) { $pwc = $_POST['pw-confirm']; }

		// Display!
		echo '<div class="form">';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="register"/>';

		echo '<table>';
		echo '<tr><td class="inputname">Identifiant</td>		<td class="inputbox"><input name="account" type="text" value="' . $this->User->GetAccountName() . '" maxlength="32"/>	</td><td class="comment"> (4-32 caractères)</td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Mot de passe</td>		<td class="inputbox"><input name="password" type="password" value="'.$pw.'" maxlength="32"/>				</td><td class="comment"> (4-32 caractères)</td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Encore...</label></td>		<td class="inputbox"><input name="pw-confirm" type="password" value="'.$pwc.'" maxlength="32"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td colspan="2"><hr /></td></tr>';

		echo '<tr><td class="inputname">Prénom légal</td>		<td class="inputbox"><input name="firstname" type="text" value="' . $this->User->GetFirstName() . '" maxlength="50"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Nom légal</td>			<td class="inputbox"><input name="lastname" type="text" value="' . $this->User->GetLastName() . '" maxlength="50"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Sexe</td>			<td class="inputradio"><input name="gender" type="radio" value="M" '.$Mchecked.'> M <input name="gender" type="radio" value="F" '.$Fchecked.'> F <input name="gender" type="radio" value="A" '.$Achecked.'> N.Bin. </td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Date de naissance</td>		<td class="inputbox"><input name="dateofbirth" type="text" value="' . $this->User->GetBirthDate() . '"/></td><td class="comment"> (AAAA-MM-JJ)</td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Courriel<br/>(16 ans et plus)</td>
			  <td class="inputbox"><input name="mail" type="email" value="' . $this->User->GetMailAddress() . '" maxlength="254"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr class="filler"></tr>';
		echo '<tr><td colspan="2" style="font-size:0.8em;">
			Si on vous connaît sous un autre nom que celui qui se trouve sur vos cartes d\'identité, saisissez le « Nom d\'usage »"</td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Nom d\'usage</td>		<td class="inputbox"><input name="altname" type="text" value="' . $this->User->GetAltName() . '" maxlength="254" placeholder="Facultatif"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td colspan="2"><hr /></td></tr>';
		echo '<tr><td class="inputname">Compte du tuteur<br/>(15 ans et moins)</td>
			  <td class="inputbox"><input name="tutor" type="text" value="' . $this->User->GetTutor() . '" maxlength="32"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr class="filler"></tr>';
		echo '<tr><td colspan="2"><center><input type="submit" value="Enregistrer"/></center></td></tr>';

		echo '</table>';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY USER INFORMATION--
	public function DisplayUser()
	{
		// Check if there's a user...
		if( $this->User == null ) { $this->Error = "No user defined!"; return; }

		// Transform model data
		$lStatus = "Indéterminé";
			    if( $this->User->GetStatus() == 'ACTIF' ) { $lStatus = "Actif"; }
			elseif( $this->User->GetStatus() == 'DELIB' ) { $lStatus = "En délibération"; }
			elseif( $this->User->GetStatus() == 'EXPUL' ) { $lStatus = "Expulsé(e)"; }
			elseif( $this->User->GetStatus() == 'INACT' ) { $lStatus = "Inactif"; }

		$lAccessLevel = "Aucun";
			    if( $this->User->GetAccessLevel() == PLAYER_LEVEL ) { $lAccessLevel = "Joueur"; }
			elseif( $this->User->GetAccessLevel() == ASSIST_LEVEL ) { $lAccessLevel = "Bénévole"; }
			elseif( $this->User->GetAccessLevel() == SCRIPTOR_LEVEL ) { $lAccessLevel = "Rédacteur"; }
			elseif( $this->User->GetAccessLevel() == REFEREE_LEVEL ) { $lAccessLevel = "Arbitre"; }
			elseif( $this->User->GetAccessLevel() == MANAGER_LEVEL ) { $lAccessLevel = "Responsable"; }
			elseif( $this->User->GetAccessLevel() == ADMIN_LEVEL ) { $lAccessLevel = "Organisateur"; }
			elseif( $this->User->GetAccessLevel() == DBA_LEVEL ) { $lAccessLevel = "Administrateur"; }

		$lPermissionList = $this->User->GetPermissions();
		$lFormattedPermissionList = "";
		foreach($lPermissionList as $access) { 
			$lFormattedPermissionList .= $access . "<br />"; 
		}


		// Display the title.
		echo '<div>';
		echo '<span class="section-title">Informations sur le compte</span>';
		echo '<hr width=70% />';

		// Data tables
		echo '<table>';		
		echo '<tr><td class="labelname">Identifiant :</td>'; 		echo '<td class="labelvalue">' . $this->User->GetAccountName() . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Prénom légal :</td>'; 		echo '<td class="labelvalue">' . $this->User->GetFirstName() . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Nom légal :</td>'; 		echo '<td class="labelvalue">' . $this->User->GetLastName() . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Nom d\'usage :</td>'; 		echo '<td class="labelvalue">' . $this->User->GetAltName() . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Sexe :</td>'; 			echo '<td class="labelvalue">' . $this->User->GetGender() . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Date de naissance :</td>'; 	echo '<td class="labelvalue">' . $this->User->GetBirthDate() . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Courriel :</td>'; 		echo '<td class="labelvalue">' . $this->User->GetMailAddress() . '</td></tr>';
		if( $this->User->GetTutor() ) {
			echo '<tr class="filler"></tr>';
			echo '<tr><td class="labelname">Tuteur :</td>'; 		echo '<td class="labelvalue">' . $this->User->GetTutor() . '</td></tr>';
		}

		echo '<tr><td colspan="2"><hr style="margin-top: 10px;" /></td></tr>';

		echo '<tr><td class="labelname">Status :</td>'; 		echo '<td class="labelvalue">' . $lStatus . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Niveau d\'accès :</td>'; 	echo '<td class="labelvalue">' . $lAccessLevel . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Rôles & Permissions :</td>';	echo '<td class="labelvalue">' . $lFormattedPermissionList . '</td></tr>';
		echo '</table>';

		echo '<hr width=70% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Modifier compte" class="smalltext-button" />';
		echo 'Modifier';
		echo '</button>';

		echo '</form>';

		echo '</div>';
	}


	//--DISPLAY USER ACCOUNT MODIFICATION FORM--
	public function DisplayAccountModifForm()
	{
		// Check if there's a user...
		if( $this->User == null ) { $this->Error = "No user defined!"; return; }

		//Prepare data
		$lGender = $this->User->GetGender();	
		$Mdefault = ($lGender == 'M') ? 'checked' : '';
		$Fdefault = ($lGender == 'F') ? 'checked' : '';
		$Adefault = ($lGender == 'A') ? 'checked' : '';

		// Display!
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-account"/>';

		echo '<span class="section-title">Informations du compte</span>';
		echo '<hr width=70% />';

		echo '<table>';
		echo '<tr><td class="inputname">Identifiant</td>		<td class="inputbox"><input name="account" type="text" value="' . $this->User->GetAccountName() . '" maxlength="32"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Prénom légal</td> 		<td class="inputbox"><input name="firstname" type="text" value="' . $this->User->GetFirstName() . '" maxlength="50"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Nom légal</td> 			<td class="inputbox"><input name="lastname" type="text" value="' . $this->User->GetLastName() . '" maxlength="50"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Nom d\'usage</td> 		<td class="inputbox"><input name="altname" type="text" value="' . $this->User->GetAltName() . '" maxlength="100"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Sexe</td>			<td class="inputradio"><input name="gender" type="radio" value="M" '.$Mdefault.'> M <input name="gender" type="radio" value="F" '.$Fdefault.'> F <input name="gender" type="radio" value="A" '.$Adefault.'> N.bin. </td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Date de naissance</td> 		<td class="inputbox"><input name="birthdate" type="text" value="' . $this->User->GetBirthDate() . '"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Courriel</td>			<td class="inputbox"><input name="mail" type="email" value="' . $this->User->GetMailAddress() . '" maxlength="254"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td colspan="2"><hr /></td></tr>';

		echo '<tr><td colspan="2">';
			echo '<button type="submit" name="option" value="Informations compte" class="submit-button" />';
			echo 'Enregistrer';
			echo '</button>';
		echo '</td></tr>';
		echo '</table>';

		echo '<span class="note">Votre nom légal est celui qui est écrit sur vos cartes d\'identité. Si le nom sous lequel vous êtes connu diffère de votre nom légal, utilisez le « Nom d\'usage ».</span>';
		echo '<span class="note">Notez que, pour éviter certaines failles de gestion, une notification est envoyée à l\'Organisation lorsque vous modifiez votre nom ou votre date de naissance.</span>';

		echo '<hr width=70% />';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY USER PASSWORD MODIFICATION FORM--
	public function DisplayPasswordModif()
	{
		// Display! This form does not require any data.
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-account"/>';

		echo '<span class="section-title">Modification du mot de passe</span>';
		echo '<hr width=70% />';

		echo '<table>';
		echo '<tr><td class="inputname">Mot de passe actuel</td>	<td class="inputbox"><input name="old_password" type="password" value=""/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Nouveau mot de passe</td>	<td class="inputbox"><input name="new_password" type="password" value=""/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="inputname">Encore...</td>			<td class="inputbox"><input name="pw-confirm" type="password" value=""/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td colspan="2">';
			echo '<button type="submit" name="option" value="Mot de passe" class="submit-button" />';
			echo 'Enregistrer';
			echo '</button>';
		echo '</td></tr>';
		echo '</table>';

		echo '<hr width=70% />';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY USER'S COMMUNICATION PREFERENCES--
	public function DisplayCommPreferences()
	{
		// Build the list of comm levels
		$lPreferenceInput = '<select name="mailusage">';
			$lPreferenceInput .= '<option value="MINIMUM" '.($this->User->IsContactCode("MINIMUM")?"selected":"").'>Contacts nécessaires seulement</option>';
			$lPreferenceInput .= '<option value="ACTIV" '.($this->User->IsContactCode("ACTIV")?"selected":"").'>Prochaines activités seulement</option>';
			$lPreferenceInput .= '<option value="INFO" '.($this->User->IsContactCode("INFO")?"selected":"").'>Lettre de nouvelles</option>';
		$lPreferenceInput .= '</select>';

		// Display!
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-account"/>';

		echo '<span class="section-title">Préférences de communication</span>';
		echo '<hr width=70% />';

		echo '<span class="note" style="text-align:left">Vous pouvez indiquer ci-dessous vos préférences quant à l\'usage de votre courriel par l\'Organisation des Terres de Bélénos.</span>';
		echo '<span class="note" style="text-align:left">À noter que, peu importe votre choix, seuls les organisateurs ont accès à vos informations. Celles-ci ne sont jamais utilisées hors du cadre de la gestion des activités proposées et ne sont jamais diffusées à des partenaires externes (ex.: nos commanditaires).</span><br />';

		echo '<table>';
		echo '<tr><td class="inputname">Utilisation du courriel</td>		<td class="inputbox" style="width: 165px;">' . $lPreferenceInput . '</td></tr>';
		echo '<tr><td colspan="2"> <a href="javascript:window.open(\'./includes/whatis/account-commprefs.html\',\'commprefs\',\'width=1100,height=550\')">(En savoir plus sur les options proposées)</a> </td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td colspan="2">';
			echo '<button type="submit" name="option" value="Préférences courriel" class="submit-button" />';
			echo 'Enregistrer';
			echo '</button>';
		echo '</td></tr>';
		echo '</table>';

		echo '<span class="note">Vos nouvelles préférences prendront effet immédiatement. Aucune autre action n\'est requise.</span>';

		echo '<hr width=70% />';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY COMPLAINT FORM--
	public function DisplayComplaintForm()
	{
		// Build the list of comm levels

		if( !isset($_POST['category']) ) { $_POST['category'] = ""; }
		$lCategoryInput = '<select name="category">';
			$lCategoryInput .= '<option value="">-- Veuillez choisir un motif --</option>';
			$lCategoryInput .= '<option value="AUTORITE" '.(($_POST['category']=="AUTORITE")?"selected":"").'>Abus d\'autorité</option>';
			$lCategoryInput .= '<option value="DISCRIM"  '.(($_POST['category']=="DISCRIM")?"selected":""). '>Discrimination</option>';
			$lCategoryInput .= '<option value="HPSYCHO"  '.(($_POST['category']=="HPSYCHO")?"selected":""). '>Harcèlement psychologique</option>';
			$lCategoryInput .= '<option value="HSEXUEL"  '.(($_POST['category']=="HSEXUEL")?"selected":""). '>Harcèlement sexuel</option>';
			$lCategoryInput .= '<option value="INTIMID"  '.(($_POST['category']=="INTIMID")?"selected":""). '>Intimidation</option>';
			$lCategoryInput .= '<option value="TRICHE"   '.(($_POST['category']=="TRICHE")?"selected":"").  '>Triche</option>';
			$lCategoryInput .= '<option value="VIOLENCE" '.(($_POST['category']=="VIOLENCE")?"selected":"").'>Violence</option>';
			$lCategoryInput .= '<option value="AUTRE"    '.(($_POST['category']=="AUTRE")?"selected":"").   '>Autre</option>';
		$lCategoryInput .= '</select>';

		if( !isset($_POST['location']) ) { $_POST['location'] = ""; }
		$lLocationInput = '<select name="location">';
			$lLocationInput .= '<option value="">-- Veuillez choisir un lieu --</option>';
			$lLocationInput .= '<option value="TERRAIN"  '.(($_POST['location']=="TERRAIN")?"selected":""). '>Sur le terrain de Bélénos</option>';
			$lLocationInput .= '<option value="HORSBELE" '.(($_POST['location']=="HORSBELE")?"selected":"").'>Hors du terrain</option>';
			$lLocationInput .= '<option value="FACEBOOK" '.(($_POST['location']=="FACEBOOK")?"selected":"").'>Sur Facebook</option>';
			$lLocationInput .= '<option value="DISCORD"  '.(($_POST['location']=="DISCORD")?"selected":""). '>Sur Discord</option>';
			$lLocationInput .= '<option value="COURRIEL" '.(($_POST['location']=="COURRIEL")?"selected":"").'>Par courriel</option>';
			$lLocationInput .= '<option value="INTERNET" '.(($_POST['location']=="INTERNET")?"selected":"").'>Ailleurs sur le Net</option>';
		$lLocationInput .= '</select>';

		$lEvents = ""; 		if( isset($_POST['events']) ) 		{ $lEvents = $_POST['events']; }
		$lDate = ""; 		if( isset($_POST['date']) ) 		{ $lDate = $_POST['date']; }

		$lWitness1 = ""; 	if( isset($_POST['witness1']) ) 	{ $lWitness1 = $_POST['witness1']; }
		$lWitness2 = ""; 	if( isset($_POST['witness2']) ) 	{ $lWitness2 = $_POST['witness2']; }
		$lWitness3 = ""; 	if( isset($_POST['witness3']) ) 	{ $lWitness3 = $_POST['witness3']; }
		$lWitness4 = ""; 	if( isset($_POST['witness4']) ) 	{ $lWitness4 = $_POST['witness4']; }
		$lWitness5 = ""; 	if( isset($_POST['witness5']) ) 	{ $lWitness5 = $_POST['witness5']; }

		$lPhone = ""; 		if( isset($_POST['phone']) ) 		{ $lPhone = $_POST['phone']; }

		// Display!
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-account"/>';

		echo '<span class="section-title">Formuler une plainte officielle</span>';
		echo '<hr width=70% />';

		echo '<span class="note" style="text-align:left;">Vous pouvez utiliser le formulaire suivant afin de produire une plainte officielle à Terres de Bélénos. Notez que <b>toutes vos plaintes sans exception seront acheminées au Comité d\'éthique, sans passer par l\'équipe d\'organisation</b>. Seules les plaintes touchant la gestion et le déroulement des activités (triche, erreur de gestion, violence entre joueurs, etc.) seront ensuite réacheminées à l\'Organisation. Vous pouvez donc faire sans crainte une plainte à caractère sensible ou encore qui ciblerait un organisateur.</span>';
		echo '<span class="note" style="text-align:left">Notez également que le Comité d\'éthique fera le suivi de votre plainte avec vous en utilisant le courriel de votre compte bélénois. Assurez-vous de mettre celui-ci à jour si nécessaire. Vous pouvez également fournir un numéro de téléohone pour vous rejoindre. Afin de vous rassurer, sachez que votre courriel n\'est fourni qu\'à quelques personnes du Comité et de l\'Organisation lorsque ceux-ci doivent vous contacter. Il n\'est jamais divulgué ni aux joueurs ni aux partenaires des Terres de Bélénos.</span>';
		echo '<span class="note" style="text-align:left">Finalement, il est important de ne pas fournir les noms et informations de contact de témoins qui ne souhaitent pas être contactés dans la section "Témoins". Vous pouvez cependant les identifier dans la description des évènements.</span><br />';

		echo '<table>';
		echo '<tr><td colspan=2><b>Plainte</b></td></tr>';
		echo '<tr><td class="inputname" style="width:200px;">Motif de la plainte</td>	
						<td class="inputbox" style="width:300px;">' . $lCategoryInput . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Date du début des évènements</td>	
						<td class="inputbox" style="width: 300px;"><input name="date" type="text" value="' . $lDate . '" maxlength="50" size="40"/></td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Lieu des évènements</td>		
						<td class="inputbox" style="width: 300px;">' . $lLocationInput . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Description des évènements</td><td></td></tr>';
		echo '<tr><td class="inputbox" colspan=2"><textarea name="events" cols="60" rows="12" placeholder="Décrivez tous les évènements pertinents à la plainte, incluant les tentatives de règlesment que vous avez faites.">' .$lEvents. '</textarea></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr class="filler"></tr>';
		echo '<tr><td colspan=2><b>Témoins</b></td></tr>';
		echo '<tr><td class="inputname">#1 - Nom & Contact</td>	
					<td class="inputbox" style="width: 300px;"><input name="witness1" type="text" value="' . $lWitness1 . '" maxlength="250" size="40"/></td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">#2 - Nom & Contact</td>	
					<td class="inputbox" style="width: 300px;"><input name="witness2" type="text" value="' . $lWitness2 . '" maxlength="250" size="40"/></td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">#3 - Nom & Contact</td> 
					<td class="inputbox" style="width: 300px;"><input name="witness3" type="text" value="' . $lWitness3 . '" maxlength="250" size="40"/></td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">#4 - Nom & Contact</td>	
					<td class="inputbox" style="width: 300px;"><input name="witness4" type="text" value="' . $lWitness4 . '" maxlength="250" size="40"/></td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">#5 - Nom & Contact</td>	
					<td class="inputbox" style="width: 300px;"><input name="witness5" type="text" value="' . $lWitness5 . '" maxlength="250" size="40"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr class="filler"></tr>';
		echo '<tr><td colspan=2><b>Téléphone (facultatif)</b></td></tr>';
		echo '<tr><td class="inputname">Numéro et poste</td>	
					<td class="inputbox" style="width: 300px;"><input name="phone" type="text" value="' . $lPhone . '" maxlength="20" size="20"/></td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr class="filler"></tr>';
		echo '<tr><td colspan="2">';
			echo '<button type="submit" name="option" value="Enregistrer plainte" class="submit-button" />';
			echo 'Envoyer';
			echo '</button>';
		echo '</td></tr>';
		echo '</table>';

		echo '<span class="note">Pour obtenir des nouvelles du suivi de votre plainte, écrivez à : <br/> <b>« Ethique@Terres-de-Belenos.com »</b>.</span>';

		echo '<hr width=70% />';

		echo '</form>';
		echo '</div>';
	}


} // END of UserUI class

?>
