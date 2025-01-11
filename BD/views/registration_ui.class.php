<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Registration Views v1.2 r6 ==			║
║	Display activities registration UIs.			║
║	Requires registration manager model.			║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('models/registrationmanager.class.php');

class RegistrationUI
{

protected $Manager;

protected $Error;

	//--CONSTRUCTOR--
	public function __construct($inManager)
	{
		$this->Manager = $inManager;
	}


	//--DISPLAY CORRECT FORM FOR PREREGISTRATIONS--
	public function DisplayPreregistrations()
	{
		// Determine user's situation : not registered, valid preregistration, or late registration
		$lActivity = $this->Manager->GetNextActivity();
		$lRegistration = $this->Manager->GetLastRegistration();
		$lAttendance = $this->Manager->GetLastAttendance();

		if( $lActivity === False ) { $this->DisplayUndefinedComingActivity(); }
			//-- No registration yet (so not attendance either)
		elseif( $lRegistration === False  ) 	{ $this->DisplayMainRegistrationForm( $lActivity ); }
			//-- There's a registration, but no attendance
		elseif( $lAttendance === False  ) 	{ $this->DisplayMainUnregisterForm( $lActivity, $lRegistration ); }
			//-- Last registration and attendance are for the same activity: tabula rasa
		elseif( $lRegistration['activityid'] == $lAttendance['id'] ) 	{ $this->DisplayMainRegistrationForm( $lActivity ); }
			//-- Last registration has no attendance, but the activity has yet to come... user can unregister
		elseif( $lRegistration['activityid'] == $lActivity->GetID() )	{ $this->DisplayMainUnregisterForm( $lActivity, $lRegistration ); }
			//-- Last registration has no attendance, and the activity has passed... user has to transfer
		else { $this->DisplayMissingAttendanceForm( $lRegistration ); }
	}


	//--DISPLAY A WARNING IF NEXT ACTIVITY IS NOT DEFINED--
	public function DisplayUndefinedComingActivity()
	{
		echo '<div>';
		echo '<span class="section-title">Préinscriptions</span>';
		echo '<hr width=70% />';
		echo '<center><span class="error" style="width: 70%;">Une situation malheureuse a pu être constatée en rapatriant la liste des activités. Le prochain GN ne semble pas avoir été planifié dans la base de données.</span></center>';
		echo '</div>';
	}


	//--DISPLAY MAIN REGISTRATION'S FORM--
	public function DisplayMainRegistrationForm( $inActivity )
	{
		// Check if there's a manager
		if( !isset($this->Manager) ) { $this->Error = "No manager set!"; return False; }


		// Prepare data
		$lName = $inActivity->GetName();
		$lPrice = $inActivity->GetEffectivePrice();

		// build character list
		$lCharacterList = $this->Manager->GetCharacters();
		$lCharacterInput = '<select name="characterindex">';
			foreach( $lCharacterList as $i => $character ) {
				$lCharacterInput .= '<option value="' .$i. '">' .$character->GetFullName(). '</option>';
			}
		$lCharacterInput .= '</select>';

		$lYoungChildrenInput = '<select name="youngchildren">
			<option value="0">Aucun</option>
			<option value="1">1 enfant (+10$)</option>
			<option value="2">2 enfants (+20$)</option>
		</select>';


		// Display the title.
		echo '<div>';
		echo '<form name="registration" method="post">';
		echo '<input type="hidden" name="action" value="manage-activities"/>';

		echo '<span class="section-title">Préinscriptions</span>';
		echo '<hr width=70% />';

		// Display!
		echo '<table>';		
		echo '<tr><td class="labelname">Activité :</td>			<td class="labelvalue">' . $lName . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Prix de base :</td>		<td class="labelvalue">' . $lPrice . ' $</td></tr>';
		echo '<tr><td colspan="2"><hr style="margin-top: 10px;" /></td></tr>';

		echo '<tr><td class="inputname">Personnage</td>	 		<td class="inputbox">' . $lCharacterInput . '</td></tr>';
		echo '<tr><td colspan="2"><hr style="margin-top: 10px;" /></td></tr>';

		if(KID_REGISTRATION_ENABLED){
		if( !$this->Manager->IsKid() ) {
			echo '<tr><td class="inputname">Enfants 5-11 ans</td>	 	<td class="inputbox">' . $lYoungChildrenInput . '</td></tr>';
			echo '<tr><td colspan="2"><hr style="margin-top: 10px;" /></td></tr>';
		}
		else { echo '<input type="hidden" name="youngchildren" value="0"/>'; }
		}
		echo '</table>';		

		// NOTE
		echo '<span class="note">En vous préinscrivant, le personnage ci-dessus recevra immédiatement son prochain niveau, vous permettant de mettre sa fiche à jour à l\'avance. 
			Se préinscrire et mettre sa fiche de personnage à jour accélère également les inscriptions sur place.</span>';

		echo   '<div style="margin:auto; margin-bottom:15px; margin-top:15px; padding: 5px; width:620px; border:1px solid black; font-size:0.8em;">
			<span style="color:green;"><b><u>ENTENTE DE PARTICIPATION</b></u></span>
				Il est important de rappeler qu\'en vous présentant sur le terrain des Terres de Bélénos, vous acceptez les conditions de participation détaillées dans le <a href="javascript:window.open(\'https://drive.google.com/file/d/1-Twmmd3kbiTxSbcXvftpS1FZqb-hllPM/view?usp=sharing\',\'Entente de participation des Terres de Bélénos\',\'width=1000,height=1000\')">document suivant</a>. Prenez le temps de vous familiariser avec celui-ci au besoin. 
			</div>';

		echo '<hr width=70% />';

		// Next step - Payment
		echo '<button type="submit" name="request" value="Calculer paiement" class="submit-button" />';
		echo 'Calculer et payer';
		echo '</button>';

		echo '</form>';
		echo '</div>';

	}


	//--DISPLAY MAIN REGISTRATION'S PAYMENT FORM--
	public function DisplayMainPaymentForm()
	{
		// Check if there's a manager
		if( !isset($this->Manager) ) { $this->Error = "No manager set!"; return False; }


		// Prepare data
		$lActivity = $this->Manager->GetNextActivity();
		$lName = $lActivity->GetName();
		$lYoungChildren = 0;
			if(isset($_POST['youngchildren'])) {$lYoungChildren = $_POST['youngchildren'];}		

		$lVoucher = $this->Manager->GetFreeActivityVouchers();
		$lUsedVoucher = 0;
		$lKidVoucher = $this->Manager->GetFreeKidVouchers();
		$lUsedKidVoucher = 0;

		$lActivityPrice = $lActivity->GetEffectivePrice();
			if( $lActivityPrice && $lVoucher ) { $lActivityPrice = 0; $lUsedVoucher = 1; }

		$lKidPrice = $lYoungChildren*15;
			if( $lYoungChildren && $lKidVoucher ) { 
				$lKidPrice = (max( $lYoungChildren-$lKidVoucher, 0 ))*15;
				$lUsedKidVoucher = $lYoungChildren - ($lKidPrice/15);
			}

		$lPrice = $lActivityPrice + $lKidPrice;

		$lOlderKid = $this->Manager->IsKid();
		$lNewPlayer = $this->Manager->IsNewPlayer();

		$lCharacter = $this->Manager->GetCharacterByIndex($_POST['characterindex']);


		// Display the title.
		echo '<div>';
		echo '<form name="registration" method="post">';
		echo '<input type="hidden" name="action" value="manage-activities"/>';
		echo '<input type="hidden" name="request" value="Payer maintenant"/>';

		echo '<input type="hidden" name="characterindex" 	value="'.$_POST['characterindex'].'"/>';
		echo '<input type="hidden" id="price" name="price" 	value="'.$lPrice.'"/>';
		echo '<input type="hidden" name="youngchildren" 	value="'.$lYoungChildren.'"/>';
		echo '<input type="hidden" name="usedvoucher" 		value="'.$lUsedVoucher.'"/>';
		echo '<input type="hidden" name="usedkidvoucher" 	value="'.$lUsedKidVoucher.'"/>';

		echo '<span class="section-title">Préinscriptions</span>';
		echo '<hr width=70% />';

		// Display!
		echo '<table>';		
		echo '<tr><td class="labelname">Activité :</td>			<td class="labelvalue" style="width:200px;">' . $lName . '</td></tr>';
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="labelname">Prix complet :</td>		<td class="labelvalue">' . $lPrice . ' $</td></tr>';
		if( $lUsedVoucher ) {
			echo '<tr class="filler"></tr>';
			echo '<tr><td class="labelname">Entrée gratuite :</td>		<td class="labelvalue">' . $lUsedVoucher . ' entrée gratuite utilisée.</td></tr>';
		}
		echo '<tr class="filler"></tr>';

		echo '<tr><td class="labelname">Personnage</td>	 		<td class="labelvalue">' . $lCharacter->GetFullName() . '</td></tr>';

		if(KID_REGISTRATION_ENABLED){
		if( !$this->Manager->IsKid() ) {
			echo '<tr class="filler"></tr>';
			echo '<tr><td class="labelname">Enfants 5-11 ans</td>	 	<td class="labelvalue">' . $lYoungChildren . '</td></tr>';
			if( $lUsedKidVoucher ) {
				echo '<tr class="filler"></tr>';
				echo '<tr><td class="labelname">Entrée Enfant gratuite</td>	 <td class="labelvalue">' . $lUsedKidVoucher . ' entrée(s) gratuite(s) utilisée(s).</td></tr>';
			}
		}
		}

		echo '<tr><td colspan="2"><hr style="margin-top: 10px;" /></td></tr>';

		echo '</table>';		

		// NOTE
		echo '<span class="note">En utilisant l\'un des moyens de paiement en ligne ci-dessous, vous acceptez les conditions décrites dans la <a href="javascript:window.open(\'./includes/whatis/policies-payment.html\',\'Politique de remboursement\',\'width=1000,height=1000\')">politique de remboursement</a> des Terres de Bélénos.</span>';

		echo   '<div style="margin:auto; margin-bottom:15px; margin-top:15px; padding: 5px; width:620px; border:1px solid black; font-size:0.8em;">
			<span style="color:green;"><b><u>ENTENTE DE PARTICIPATION</b></u></span>
				Il est important de rappeler qu\'en vous présentant sur le terrain des Terres de Bélénos, vous acceptez les conditions de participation détaillées dans le <a href="javascript:window.open(\'https://drive.google.com/file/d/1-Twmmd3kbiTxSbcXvftpS1FZqb-hllPM/view?usp=sharing\',\'Entente de participation des Terres de Bélénos\',\'width=1000,height=1000\')">document suivant</a>. Prenez le temps de vous familiariser avec celui-ci au besoin. 
			</div>';

		echo '<hr width=70% />';


		// PAY LATER
		if( $lPrice == '0' ) {
			echo '<button type="submit" name="request" value="Payer sur place" class="submit-button" />';
			echo 'Se préinscrire';
			echo '</button>';
		}
		else {
			echo '<button type="submit" name="request" value="Payer sur place" class="submit-button" />';
			echo 'Payer sur place';
			echo '</button>';		
		}

		// PAYPAL
  		if(PAYPAL_ENABLED){
  		if( $lPrice > 0 ) {
			echo '<span class="note"><b>OU</b></span>';
			echo '<div id="paypal-button-container" style="width:200px; margin:auto;"></div>';

			    if( $lPrice == 15 ) {
			    	?><script>PaypalOneKid();</script><?php
			    }
			elseif( $lPrice == 30 ) {
			    	?><script>PaypalTwoKids();</script><?php
			    }
			elseif( $lPrice == 30 ) {
			    	?><script>PaypalOlderKidEntry();</script><?php
			    }
			elseif( $lPrice == 35 ) {
			    	?><script>PaypalNewPlayer();</script><?php
			    }
			elseif( $lPrice == 50 ) {
			    	?><script>PaypalNewPlayerOneKid();</script><?php
			    }
			elseif( $lPrice == 50 ) {
			    	?><script>PaypalOneEntry();</script><?php
			    }
			elseif( $lPrice == 65 ) {
			    	?><script>PaypalNewPlayerTwoKids();</script><?php
			    }
			elseif( $lPrice == 65 ) {
			    	?><script>PaypalOneEntryOneKid();</script><?php
			    }
			elseif( $lPrice == 80 ) {
			    	?><script>PaypalOneEntryTwoKids();</script><?php
			    }
		}
 		}

		
		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY MAIN UNREGISTRATION'S FORM--
	public function DisplayMainUnregisterForm( $inActivity, $inRegistration )
	{
		// Check if there's a manager
		if( !isset($this->Manager) ) { $this->Error = "No manager set!"; return False; }


		// Prepare data
		$lName = $inActivity->GetName();
		$lPrice = $inRegistration['price'];
		$lCharacter = $inRegistration['charactername'];
		$lPaymentMethod = "Sur place";
			    if( $inRegistration['price'] == 0 ) { $lPaymentMethod = "Gratuit"; }
			elseif( $inRegistration['prepaid'] ) { $lPaymentMethod = "Prépayé"; }

		// Display the title.
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-activities"/>';
		echo '<input type="hidden" name="paynow" value=""/>';
		echo '<input type="hidden" name="paylater" value=""/>';

		echo '<span class="section-title">Préinscriptions</span>';
		echo '<hr width=70% />';

		// Display!
		echo '<table>';		
		echo '<tr><td class="labelname">Activité :</td>			<td class="labelvalue">' .$lName. '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Prix inscrit :</td>		<td class="labelvalue">' .$lPrice. ' $</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Personnage :</td>	 	<td class="labelvalue">' .$lCharacter. '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Paiement :</td>	 		<td class="labelvalue">' .$lPaymentMethod. '</td></tr>';

		echo '<tr><td colspan="2"><hr style="margin-top: 10px;" /></td></tr>';
		echo '</table>';		

		echo '<span class="note" style="margin-bottom: 10px;">Notez qu\'en vous désinscrivant, le personnage ci-dessus perdra le niveau qu\'il a acquis lors de la préinscription, ainsi que toutes les compétences acquises lors de la mise à jour de sa fiche (compétences en bleu).</span>';

		echo '<button type="submit" name="request" value="Se désinscrire" class="submit-button" />';
		echo 'Se désinscrire';
		echo '</button>';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY MAIN REGISTRATION'S TRANSFER--
	public function DisplayMissingAttendanceForm( $inRegistration )
	{
		// Check if there's a manager
		if( !isset($this->Manager) ) { $this->Error = "No manager set!"; return False; }


		// Prepare data
		$lName = $inRegistration['activityname'];
		$lEndDate = substr($inRegistration['activityend'], 0, 10);
		$lCharacter = $inRegistration['charactername'];


		// Display the title.
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-activities"/>';

		echo '<span class="section-title">Préinscriptions</span>';
		echo '<hr width=70% />';

		// Display!
		echo '<table>';		
		echo '<tr><td class="labelname">Activité :</td>			<td class="labelvalue">' .$lName. '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Date de fin :</td>		<td class="labelvalue">' .$lEndDate. '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Personnage :</td>	 	<td class="labelvalue">' .$lCharacter. '</td></tr>';

		echo '<tr><td colspan="2"><hr style="margin-top: 10px;" /></td></tr>';
		echo '</table>';		

		echo '<span class="note" style="margin-bottom: 10px;">L\'activité ci-dessus est passée et votre présence n\'a pas été enregistrée dans la Base de données malgré votre inscription à celle-ci.<br />

		Si cette situation est normale, veuillez simplement vous désincrire avant de vous réinscrire à une future activité. Notez que le personnage ci-dessus perdra le niveau qu\'il a acquis lors de la préinscription, ainsi que toutes les compétences acquises lors de la mise à jour de sa fiche (compétences en bleu). Il vous faudra donc réajuster votre fiche de personnage si vous aviez acheté de nouvelles compétences.<br />

		Si cette situation est anormale et que vous étiez présent à l\'activité en question, <b>ne vous désinscrivez pas !</b> Veuillez plutôt contacter l\'Organisation afin de faire corriger le problème. Vous pourrez vous préinscrire normalement dès qu\'il sera corrigé.</span>';

		echo '<button type="submit" name="request" value="Se désinscrire" class="submit-button" />';
		echo 'Se désinscrire';
		echo '</button>';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY PASS PURCHASE FORM--
	public function DisplayPassPurchaseForm()
	{
		// Check if there's a manager
		if( !isset($this->Manager) ) { $this->Error = "No manager set!"; return False; }


		// Prepare data
		$lPassList = $this->Manager->GetPasses();


		// PASSES
		// Display the title for passes.
		echo '<div>';
		foreach( $lPassList as $i => $pass ) {
			echo '<form method="post">';
			echo '<input type="hidden" name="action" value="manage-activities"/>';
			echo '<input type="hidden" name="request" value="Acheter passe maintenant"/>';
			echo '<input type="hidden" name="passindex" value="'.$i.'"/>';

			echo '<span class="section-title">'.$pass->GetName().'</span>';
			echo '<hr width=70% />';

			echo '<table style="margin: 10px;">';		
			echo '<tr><td class="labelname">Prix</td> <td style="width:400px;text-align:left; padding-left:5px;">' . $pass->GetPrice() . ' $</td></tr>';
			echo '<tr class="filler"></tr>';
			echo '<tr><td class="labelname">Description</td> <td style="width:400px;text-align:left; padding-left:5px;">' . $pass->GetDescription() . '</td></tr>';
			echo '<tr class="filler"></tr>';
			echo '<tr><td class="labelname">Obtenue</td> <td style="width:400px;text-align:left; padding-left:5px;">' . ($pass->IsAcquired()?'Oui':'Non') . '</td></tr>';
			echo '</table>';		

			if( !$pass->IsAcquired() ) { 
				echo '<br />';
				echo '<button type="submit" name="request" value="Acheter passe sur place" class="submit-button" />';
				echo 'Acheter et payer sur place';
				echo '</button>';

				if(PAYPAL_ENABLED){
  				echo '<span class="note"><b>OU</b></span>';
				echo '<div id="paypal-button-container" style="width:200px; margin:auto;"></div>';
				if( $pass->GetID() == 17 ) { #Passe de saison 2022 
					?><script>PaypalSeasonalPass();</script><?php
				}
				}
			}

			echo '<br />';
			echo '<hr width=70% />';
			echo '</form>';
		}

		echo '<span class="note">Acheter une passe via cette interface vous donnera ses avantages immédiatement.</span>';
		echo '</div>';
	}


	//--DISPLAY PASS PURCHASE FORM--
	public function DisplayPassportPurchaseForm()
	{
		// Check if there's a manager
		if( !isset($this->Manager) ) { $this->Error = "No manager set!"; return False; }


		// Prepare data
		$lFreeActivites = $this->Manager->GetFreeActivityVouchers();
			if( !$lFreeActivites ) { $lFreeActivites = 0; }
		$passport_enabled = "disabled";
			if( $lFreeActivites >= 2 ) { $passport_enabled = "disabled"; }


		// PASSPORTS
		// Display the title for passports.
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-activities"/>';

		echo '<span class="section-title">Achat d\'un Passeport</span>';
		echo '<hr width=70% />';

		// Display!
		echo '<table style="margin-top: 10px;">';		
		echo '<tr><td class="labelname">GN gratuits restants</td>	 	<td class="labelvalue" style="width: 100px;">' . $lFreeActivites . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="labelname">Prix du Passeport</td>	 		<td class="labelvalue" style="width: 100px;">200 $</td></tr>';
		echo '</table>';		

		echo '<span class="note">Acheter un "Passeport GN" vous donne droit à 5 activitées gratuites. Contrairement aux passes de saison, les activités gratuites d\'un Passeport peuvent être dispersées sur plusieurs années. En échange, le rabais est moins important et l\'Organisation ne vous donne pas de crédit pour vos constructions.</span>';

		echo '<button type="submit" name="request" value="Acheter passeport" class="submit-button" '.$passport_enabled.'/>';
		echo 'Acheter';
		echo '</button>';

		echo '<span class="note">Dans le but de protéger les intérêts du joueur, il n\'est pas permis d\'acheter un nouveau Passeport s\'il vous reste plus d\'une activité gratuite inscrite à votre compte.</span>';

		// Warning
		echo '<br />';
		echo '<hr width=70% />';

		echo '<center><span class="error" style="width: 70%;">L\'achat de passeports via cette interface est temporairement désactivée. Vous pouvez acheter un passeport sur place lors d\'une activité ou encore vous pouvez contacter l\'Organisation pour prendre entente.</span></center>';
		echo '<br />';
		echo '<center><span class="error" style="width: 70%;">L\'achat de passes et passeports via la BD reprendra dès que la nouvelle interface, incluant le paiement électronique, sera en place. Merci de votre compréhension!</span></center>';


		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY FIELD SERVICE REQUEST FORM--
	public function DisplayFieldServiceRequestForm()
	{
		// Prepare data
		$lActivity = $this->Manager->GetNextActivity();
			if( $lActivity === False ) { $this->DisplayUndefinedComingActivity(); }

		$lServiceInput = '<select name="service">
					<option value="Eau">Eau potable</option>
					<option value="Bois">Bois de chauffage</option>
					<option value="Transport">Transport de bagages</option>
					<option value="Autre">Demande particulière</option>
				  </select>';

		$lText = "";
			if( isset($_POST['text']) ) { $lText = $_POST['text']; }


		// Title
		echo '<div>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-activities"/>';

		echo '<input type="hidden" name="activityname" value="'.$lActivity->GetName().'"/>';
		echo '<input type="hidden" name="activityid" value="'.$lActivity->GetID().'"/>';

		echo '<span class="section-title">Services aux campements</span>';
		echo '<hr width=70% />';


		// Display!
		echo '<div style="width: 70%; margin: auto; text-align: left;"><table>
				<tr><td style="width: 25%;" valign="top"><b>Eau potable :</b></td>
					<td>Barils de 68L. Le premier est gratuit. 10$ par baril supplémentaire.</td></tr>
				<tr><td style="width: 25%;" valign="top"><b>Bois de chauffage :</b></td>
					<td>Gratuit tant que le terrain peut fournir. Le besoin moyen par campement est un quart de corde de chauffage par GN.</td></tr>
				<tr><td style="width: 25%;" valign="top"><b>Transport de bagage :</b></td>
					<td>10$ par voyage. La boîte de transport fait 4\' par 8\'.</td></tr>
				<tr><td style="width: 25%;" valign="top"><b>Demande particulière :</b></td>
					<td>Pour prendre entente avec les propriétaires pour un besoin particulier de votre campement.</td></tr>
		      </table></div>';

		echo '<hr width=70% />';

		echo '<table style="margin-top: 10px;">';		
		echo '<tr><td class="inputname">Activité</td>	 		<td class="labelvalue" style="width: 165px;">' . $lActivity->GetName() . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Service</td>	 		<td class="inputbox" style="width: 165px;">' . $lServiceInput . '</td></tr>';
		echo '</table>';		

		echo '<div style="margin: auto; margin-top: 10px; margin-bottom: 10px; width: 620px;"><textarea name="text" cols="72" rows="12" placeholder="Entrez les détails de votre demande, notamment pour quel groupe, quel campement et/ou quel bâtiment vous le demandez, ainsi que la quantité qu\'il vous faut.">' . $lText . '</textarea></div>';	

		echo '<button type="submit" name="request" value="Commander service" class="submit-button" />';
		echo 'Commander';
		echo '</button>';

		echo '<span class="note">Notez que dans le cas où l\'Organisation doit vous contacter au sujet de la présente demande de service, elle le fera à l\'aide du courriel que vous avez configuré pour le compte actuellement connecté. Vous pouvez modifier ce courriel dans l\'onglet « Compte bélénois ».</span>';

		echo '</form>';
		echo '</div>';
	}


	//--DISPLAY FIELD SERVICE REQUEST FORM--
	public function DisplayNewspaperArticleSubmitForm()
	{
		// Prepare data
		$lActivity = $this->Manager->GetNextActivity();
			if( $lActivity === False ) { $this->DisplayUndefinedComingActivity(); }

		$lTitle = "";
			if( isset($_POST['title']) ) { $lTitle = $_POST['title']; }

		$lCategory = ""; if( isset($_POST['category']) ) { $lCategory = $_POST['category']; }
		$lCategoryInput = '<select name="category">
					<option value="Actualité" '.	 ($lCategory=='Actualité'?'selected':'')     .'>Actualité</option>
					<option value="Divertissement" '.($lCategory=='Divertissement'?'selected':'').'>Arts & divertissement</option>
					<option value="Évènement" '.	 ($lCategory=='Évènement'?'selected':'')     .'>Évènement</option>
					<option value="Lettre ouverte" '.($lCategory=='Lettre ouverte'?'selected':'').'>Lettre ouverte</option>
					<option value="Publicité" '.	 ($lCategory=='Publicité'?'selected':'')     .'>Publicité ou annonce</option>
				</select>';
			
		$lSignature = "";
			if( isset($_POST['signature']) ) { $lSignature = $_POST['signature']; }

		$lFileInput = '<input type="file" name="attachedfile" id="attachedfile" /><br/><br/>
				<input type="checkbox" name="okwithnofile" id="okwithnofile" value="1" /><label>Je n\'ai pas de fichier. Mon article est dans les instructions.</label>';

		$lRevisionInput = '<input type="checkbox" name="revisionapproved" id="revisionapproved" value="1" />';
			if( isset($_POST['revisionapproved']) && $_POST['revisionapproved'] == 1 ) 
				{ $lRevisionInput = '<input type="checkbox" name="revisionapproved" id="revisionapproved" value="1" checked />'; }

		$lText = "";
			if( isset($_POST['text']) ) { $lText = $_POST['text']; }


		// Title
		echo '<div>';
		echo '<form method="post" enctype="multipart/form-data">';
		echo '<input type="hidden" name="action" value="manage-activities"/>';

		echo '<input type="hidden" name="activityname" value="'.$lActivity->GetName().'"/>';
		echo '<input type="hidden" name="activityid" value="'.$lActivity->GetID().'"/>';

		echo '<span class="section-title">Soumettre un article pour le Feuillet d\'Hyden</span>';
		echo '<hr width=70% />';


		// Display!
		echo '<div style="width: 70%; margin: auto; text-align: left;"><table>
				<tr><td style="width: 25%;" valign="top"><b>Délais :</b></td>
					<td>Vous devez avoir soumis vos articles au plus tard le mardi précédent le GN où il doit paraître.</td></tr>
				<tr><td style="width: 25%;" valign="top"><b>Signature :</b></td>
					<td>Doit être le nom de votre personnage ou de l\'une de ses « Identités secrètes » s\'il possède cette compétence. <b>Le titre et la signature ne seront pas répétés s\'ils sont déjà dans votre texte.</b></td></tr>
				<tr><td style="width: 25%;" valign="top"><b>Fichiers :</b></td>
					<td>Seuls les fichiers PDF, DOCX, ODT, TXT, JPG et PNG de moins de 5 Mo sont acceptés.</td></tr>
				<tr><td style="width: 25%;" valign="top"><b>Instructions :</b></td>
					<td>Utilisez cette section pour aider le rédacteur à placer votre article selon son contexte.</td></tr>
		      </table></div>';

		echo '<hr width=70% />';

		echo '<table style="margin-top: 10px;">';		
		echo '<tr><td class="inputname">Activité</td>	 <td class="labelvalue" style="width: 300px;">' . $lActivity->GetName() . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Titre</td>	 <td class="inputbox" style="width: 300px;"><input name="title" type="text" value="' . $lTitle . '" maxlength="72"/></td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Catégorie</td>	 <td class="inputbox" style="width: 300px;">' . $lCategoryInput . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Signature</td>	 <td class="inputbox" style="width: 300px;"><input name="signature" type="text" value="' . $lSignature . '" maxlength="72"/></td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Fichier</td>	 <td class="inputbox" style="width: 300px;">' . $lFileInput . '</td></tr>';
		echo '<tr class="filler"></tr>';
		echo '<tr><td class="inputname">Correction</td>	 <td style="width: 300px;">' . $lRevisionInput . '<label>Je consens à faire corriger mon texte.</label></td></tr>';
		echo '</table>';		

		echo '<div style="margin: auto; margin-top: 10px; margin-bottom: 10px; width: 620px;"><textarea name="text" cols="72" rows="12" placeholder="Instructions supplémentaires...">' . $lText . '</textarea></div>';	

		echo '<button type="submit" name="request" value="Soumettre article" class="submit-button" />';
		echo 'Soumettre';
		echo '</button>';

		echo '<span class="note">Notez que dans le cas où le rédacteur du Feuillet doit vous contacter au sujet de la présente soumission, il le fera à l\'aide du courriel que vous avez configuré pour le compte actuellement connecté. Vous pouvez modifier ce courriel dans l\'onglet « Compte bélénois ».</span>';

		echo '</form>';
		echo '</div>';
	}


} // END of RegistrationUI class

?>
