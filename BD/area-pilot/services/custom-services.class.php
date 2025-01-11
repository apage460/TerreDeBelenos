<?php
define('SALT', 'Chad4Ever');

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Custom Services v1.2 r5 ==				║
║	Custom services meant for temporary jobs.		║
║	Non-serializable. Requires DAL. Uses MySQL queries.	║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('services/database.class.php'); 		// Data Access Layer
include_once('models/custom.class.php'); 		// Custom Data definition

class CustomServices
{

public $DAL;
public $Data;

public $Error;

	//--CONSTRUCTOR--
	public function __construct($inDataAccessLayer, $inData =NULL)
	{
		$this->DAL = $inDataAccessLayer;

		if( isset($inData) ) {$this->Data = $inData;}
	}


	//--GET ALL PLAYER WHOSE ACCOUNT IS LINKED TO ANOTHER--
	public function GetLinkedAccounts()
	{
		// Verify data
		if( !isset($this->Data) ) { $this->Error = "No data!"; return False; }


		// Ask the database
		$lQuery = 	"SELECT ind.Id, ind.Prenom, ind.Nom, ind.Sexe, ind.DateNaissance, ind.Courriel, jou.jou_uid, jou.jou_exp, temp.IdAncienCompte, temp.TransfertPresences, temp.PremierTransfertXP, temp.SecondTransfertXP, temp.DernierTransfertXP
				 FROM db_indiv.individus ind
					LEFT JOIN db_beleweb.joueur jou ON concat(ind.Prenom, ind.Nom, ind.DateNaissance) = concat(jou.jou_prenom, jou.jou_nom, jou.jou_anniversaire)
					LEFT JOIN db_indiv.temp_transferts temp ON ind.Id = temp.IdIndividu
				 WHERE jou.jou_uid IS NOT NULL
                		 ORDER BY ind.Id";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();

		// Set data
		if( $r ) { $this->Data->LinkedAccounts = $r; }
		else { $this->Data->LinkedAccounts = array(); }

		return True;
	}


	//--GET CHARACTERS THAT ARE STILL WOUNDED AND HAVE NOT HEALED THIS YEAR--
	public function GetWoundedCharacters()
	{
		// Verify data
		if( !isset($this->Data) ) { $this->Error = "No data!"; return False; }


		// Ask the database
		$lQuery = 	"SELECT DISTINCT pv.IdPersonnage, 
						 per.Prenom, 
        					 per.Nom,
        					 (SELECT SUM(pvt.PV) FROM db_perso.points_de_vie pvt 
        					  WHERE pvt.IdPersonnage = pv.IdPersonnage) AS PVTotal,
						 (SELECT sum(pvn.PV) FROM db_perso.points_de_vie pvn 
        					  WHERE pvn.IdPersonnage = pv.IdPersonnage
        					    AND pvn.Raison IN ('PV de départ','Ajustement racial','Achat de PV','Handicap pérein%')) AS PVNaturels,
						 (SELECT sum(pvr.PV) FROM db_perso.points_de_vie pvr 
        					  WHERE pvr.IdPersonnage = pv.IdPersonnage
        					    AND pvr.Raison = 'Restitution annuelle'
        					    AND pvr.DateInscription >= :yearstart
						    AND pvr.DateInscription <= :yearend) AS PVRestitues		
				 FROM db_perso.points_de_vie pv
				 JOIN db_perso.personnages per ON pv.IdPersonnage = per.Id
				 WHERE pv.IdPersonnage IN (SELECT DISTINCT pvb.IdPersonnage FROM db_perso.points_de_vie pvb
							   WHERE pvb.PV < 0
							     AND pvb.Raison NOT IN ('Ajustement racial','Handicap pérein'))
				   AND per.CodeEtat <> 'MORT'
				 ORDER BY per.Prenom ASC;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":yearstart", date("Y").'-01-01', PDO::PARAM_STR);
			$this->DAL->Bind(":yearend", date("Y").'-12-31', PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		// Set data
		if( $r ) { $this->Data->WoundedCharacters = $r; }
		else { $this->Data->WoundedCharacters = array(); }

		return True;
	}


	//--GET PASS LIST--
	public function GetPassList()
	{
		// Verify data
		if( !isset($this->Data) ) { $this->Error = "No data!"; return False; }


		// Ask the database
		$lQuery = 	"SELECT pass.Id, pass.Nom
				 FROM db_activ.passes pass
				 WHERE pass.Nom NOT LIKE '%saison%'
				 ORDER BY pass.Id ASC;";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();

		// Set data
		if( $r ) { 
			$this->Data->Passes = $r; 

			// Get free activities for each pass
			foreach( $this->Data->Passes as $i => $pass) {

				// Create access array
				$this->Data->Passes[$i]['Acces'] = array();

				// Ask the database...
				$lQuery = 	"SELECT app.IdActivite
						 FROM db_activ.acces_par_passe app
						 WHERE app.IdPasse = :passid;";

				$this->DAL->SetQuery($lQuery);
					$this->DAL->Bind(":passid", $pass['Id'], PDO::PARAM_INT);
				$t = $this->DAL->FetchResult();

				foreach ($t as $activity) {	$this->Data->Passes[$i]['Acces'][] = $activity['IdActivite'];	}

			}
		}
		else { $this->Data->Passes = array(); }

		return True;
	}


	//--GET ALL CHARACTERS FROM A CLASS THAT DON'T HAVE THE POINTED SKILL--
	public function GetIncompleteCharacters($inSkillCode, $inClassCode =NULL)
	{
		// Verify data
		if( !isset($this->Data) ) { $this->Error = "No data!"; return False; }


		// Ask the database
		$lQuery = 	"SELECT per.Id, per.Prenom, per.Nom, per.CodeClasse
				 FROM db_perso.personnages per
				 WHERE per.Id NOT IN (SELECT cac.IdPersonnage FROM db_perso.competences_acquises cac WHERE cac.CodeCompetence = :skillcode)";

  		if( $inClassCode )  { $lQuery .= "AND per.CodeClasse = :classcode;"; }

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":skillcode", $inSkillCode, PDO::PARAM_STR);
			if( $inClassCode )  { $this->DAL->Bind(":classcode", $inClassCode, PDO::PARAM_STR); }
		$r = $this->DAL->FetchResult();

		// Set data
		if( $r ) { $this->Data->SkillReceivers = $r; }
		else { $this->Data->SkillReceivers = array(); }

		return True;
	}


	//--GET CHARACTERS THAT NEED THE LAST MASSIVE UPDATE--
	// ** Note : Massive updates are manually recoded each time there is one. This is a "wild card" kind of function. **
	public function GetOutdatedCharacters()
	{
		// Verify data
		if( !isset($this->Data) ) { $this->Error = "No data!"; return False; }


		// Ask the database
		$lQuery = 	"SELECT DISTINCT per.Id, per.Prenom, per.Nom, per.CodeClasse
				 FROM db_perso.personnages per
				 	JOIN db_perso.competences_acquises cac ON cac.IdPersonnage = per.Id
				 WHERE cac.CodeCompetence IN (	SELECT creg.Code 
								FROM db_pilot.competences_regulieres creg
							 	WHERE creg.IndPrecision = 1)
				   AND cac.Usages IS NOT NULL
				   AND cac.CodeEtat <> 'SUPPR'
				 ORDER BY per.Prenom ASC;";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();

		// Set data
		if( $r ) { $this->Data->OutdatedCharacters = $r; }
		else { $this->Data->OutdatedCharacters = array(); }

		return True;
	}


	//--TRANSFER ACTIVITIES FOR LINKED ACCOUNT--
	public function TransferActivitiesForLinkedAccounts()
	{
		// Verify data
		if( !isset($this->Data) ) { $this->Error = "No data!"; return False; }


		// Cycle linked account. Transfer activities for those who aren't already processed
		foreach ($this->Data->LinkedAccounts as $account) {
			if( !$account['TransfertPresences'] ){

				// Ask the database for old activities.
				$lQuery = 	"SELECT act.Id, act.Nom, evn.evn_uid, FROM_UNIXTIME(evn.evn_start,'%Y-%m-%d') AS DateInscription
						 FROM db_beleweb.joueur_evenement jev
							JOIN db_beleweb.evenement evn ON jev.evn_uid = evn.evn_uid
							JOIN db_activ.activites act ON evn.evn_nom = act.Nom
						 WHERE jev.jou_uid = :oldid;";

				$this->DAL->SetQuery($lQuery);
					$this->DAL->Bind(":oldid", $account['jou_uid'], PDO::PARAM_INT);
				$lOldAttendances = $this->DAL->FetchResult();

				// Create an attendance for each activity
				if( $lOldAttendances ) {
					foreach ($lOldAttendances as $attendance) {
						// Ask the database...
						$lQuery = 	"INSERT IGNORE INTO db_indiv.presences (IdIndividu, IdActivite, DateInscription)
								 VALUES ( :userid, :activityid, :inscriptiondate );";

						$this->DAL->SetQuery($lQuery);
							$this->DAL->Bind(":userid", $account['Id'], PDO::PARAM_INT);
							$this->DAL->Bind(":activityid", $attendance['Id'], PDO::PARAM_INT);
							$this->DAL->Bind(":inscriptiondate", $attendance['DateInscription'], PDO::PARAM_STR);
						$this->DAL->FetchResult();
					}
				}

				// Set account as transfered
				if( !$account['IdAncienCompte'] ) {
					$lQuery = 	"INSERT INTO db_indiv.temp_transferts (IdIndividu, IdAncienCompte, TransfertPresences)
							 VALUES ( :userid, :oldid, sysdate() );";	
				}
				else {
					$lQuery = 	"UPDATE db_indiv.temp_transferts temp
							 SET temp.TransfertPresences = sysdate()
							 WHERE temp.IdIndividu = :userid
							   AND temp.IdAncienCompte = :oldid;";	
				}		

				$this->DAL->SetQuery($lQuery);
					$this->DAL->Bind(":userid", $account['Id'], PDO::PARAM_INT);
					$this->DAL->Bind(":oldid", $account['jou_uid'], PDO::PARAM_INT);
				$this->DAL->FetchResult();
			
			}
		}

		return True;
	}


	//--TRANSFER EXPERIENCE FOR LINKED ACCOUNT--
	public function TransferAllExperienceForLinkedAccounts()
	{
		// Verify data
		if( !isset($this->Data) ) { $this->Error = "No data!"; return False; }


		// Cycle linked account. Transfer activities for those who aren't already processed
		foreach ($this->Data->LinkedAccounts as $account) {
			if( $account['DernierTransfertXP'] == null ){

				// Prepare data					
				$lOldXP = 0;		// Total player XP in old account
				$lTransferedXP = 0;	// XP transfered from old account


				// Ask the database for total xp in old account.
				$lQuery = 	"SELECT jou.jou_exp FROM db_beleweb.joueur jou
						 WHERE jou.jou_uid = :oldid;";

				$this->DAL->SetQuery($lQuery);
					$this->DAL->Bind(":oldid", $account['jou_uid'], PDO::PARAM_INT);
				$r = $this->DAL->FetchResult();

				$lOldXP = $r[0]['jou_exp'];


				// Set the amount of XP to be transfered. In this case : everything!
				$lTransferedXP = $lOldXP;

				
				// Give XP on new account
				if( $lOldXP ) {
					$lComments = "Ancien:".$lOldXP." - Transféré:".$lTransferedXP;

					$lQuery = 	"INSERT INTO db_indiv.experience (IdIndividu, Raison, XP, DateInscription, Commentaires)
							 VALUES (:userid, 'Dernier transfert', :xp, sysdate(), :comments);";

					$this->DAL->SetQuery($lQuery);
						$this->DAL->Bind(":userid", $account['Id'], PDO::PARAM_INT);
						$this->DAL->Bind(":xp", $lTransferedXP, PDO::PARAM_INT);
						$this->DAL->Bind(":comments", $lComments, PDO::PARAM_STR);
					$this->DAL->FetchResult();
				}


				// Remove XP from old account
				if( $lTransferedXP ) {
					$lQuery = 	"UPDATE db_beleweb.joueur jou
							 SET jou.jou_exp = :xp
							 WHERE jou_uid = :oldid;";

					$this->DAL->SetQuery($lQuery);
						$this->DAL->Bind(":oldid", $account['jou_uid'], PDO::PARAM_INT);
						$this->DAL->Bind(":xp", $lOldXP-$lTransferedXP, PDO::PARAM_INT);
					$this->DAL->FetchResult();
				}


				// Set account as transfered
				if( !$account['IdAncienCompte'] ) {
					$lQuery = 	"INSERT INTO db_indiv.temp_transferts (IdIndividu, IdAncienCompte, DernierTransfertXP)
							 VALUES ( :userid, :oldid, :xp );";	
				}
				else {
					$lQuery = 	"UPDATE db_indiv.temp_transferts temp
							 SET temp.DernierTransfertXP = :xp
							 WHERE temp.IdIndividu = :userid
							   AND temp.IdAncienCompte = :oldid;";	
				}		

				$this->DAL->SetQuery($lQuery);
					$this->DAL->Bind(":userid", $account['Id'], PDO::PARAM_INT);
					$this->DAL->Bind(":oldid", $account['jou_uid'], PDO::PARAM_INT);
					$this->DAL->Bind(":xp", $lTransferedXP, PDO::PARAM_INT);
				$this->DAL->FetchResult();
			
			}
		}

		return True;
	}


	//--RESTORE HEALTH TO WOUNDED CHARACTERS--
	public function HealWoundedCharacters()
	{
		// Verify data
		if( !isset($this->Data) ) { $this->Error = "No data!"; return False; }


		// Cycle linked account. Transfer activities for those who aren't already processed
		foreach ($this->Data->WoundedCharacters as $wounded) {

			// if there is no wound left, move on...
			if( $wounded['PVManquants'] < 0 ) {

				// if wounded character has not lost as much life as can be restored, restore all lost life. Else, restore preset amount.
				$lRestoredLife = RESTORED_LIFE;
				$lWounds = -$wounded['PVManquants'];

				if( $lWounds < RESTORED_LIFE ) { $lRestoredLife = $lWounds; }

				$lQuery = 	"INSERT INTO db_perso.points_de_vie (IdPersonnage, Raison, PV, DateInscription)
						 VALUES (:characterid, 'Restitution annuelle', :life, sysdate());";

				$this->DAL->SetQuery($lQuery);
					$this->DAL->Bind(":characterid", $wounded['IdPersonnage'], PDO::PARAM_INT);
					$this->DAL->Bind(":life", $lRestoredLife, PDO::PARAM_INT);
				$r = $this->DAL->FetchResult();
			}				

		}

		return True;
	}


	//--GIVE XP GIVEAWAY FOR SELECTED PASS--
	public function GivePassXP()
	{
		// Verify data
		if( !isset($this->Data) ) { $this->Error = "No data!"; return False; }
		if( !isset($_POST['passid']) ) { $this->Error = "No pass selected!"; return False; }

		$lSelectedPass = $this->Data->GetPassById( $_POST['passid'] );
			$lFreeActivityCount = count($lSelectedPass['Acces']);
		$lPassBuyers = array();
		$lAffectedPlayers = array();


		// Get players who purchased that pass along with pertinent presences and have not received the giveaway yet
		$lQuery = 	"SELECT pac.IdIndividu,
					(SELECT count(pres.IdActivite) 
					 FROM db_indiv.presences pres
        				 WHERE pres.IdIndividu = pac.IdIndividu
          				 AND pres.IdActivite IN (
						SELECT app.IdActivite
						FROM db_activ.acces_par_passe app
						WHERE app.IdPasse = :passid
          				 )
    					) as NbPresences
				 FROM db_indiv.passes_acquises pac
				 WHERE pac.IdPasse = :passid
				   AND pac.IdIndividu NOT IN (
				   	SELECT exp.IdIndividu
				   	FROM db_indiv.experience exp
				   	WHERE Raison = :passname
				   ) ;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":passid", $lSelectedPass['Id'], PDO::PARAM_INT);
			$this->DAL->Bind(":passname", $lSelectedPass['Nom'], PDO::PARAM_STR);
		$lPassBuyers = $this->DAL->FetchResult();


		// Compare the number of free activities to the number of pertinent attendances and build a list of affected players
		foreach ($lPassBuyers as $i => $player) {
			if( $player['NbPresences'] < $lFreeActivityCount ) { $lAffectedPlayers[] = $player['IdIndividu']; }
		}


		// Give away XP
		foreach ($lAffectedPlayers as $playerid) {
			
			$lQuery =	"INSERT INTO db_indiv.experience (IdIndividu, Raison, XP)
					 VALUES (:playerid, :reason, 20)";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":playerid", $playerid, PDO::PARAM_INT);
				$this->DAL->Bind(":reason", $lSelectedPass['Nom'], PDO::PARAM_STR);
			$r = $this->DAL->FetchResult();
		}

		return True;
	}


	//--GIVE A SKILL TO A (PRESUMABLY LARGE) LIST OF CHARACTERS--
	public function GiveSkillToClassMembers($inSkillCode)
	{
		// Verify data
		if( !isset($this->Data) ) { $this->Error = "No data!"; return False; }
		if( !$inSkillCode ) { $this->Error = "No skill code!"; return False; }


		// Add skill to each previously obtained character
		foreach ($this->Data->SkillReceivers as $character) {

			// Add the talent
			$lQuery = 	"INSERT INTO db_perso.competences_acquises (IdPersonnage, CodeCompetence, Type, CoutXP, DateCreation, CodeAcquisition, CodeEtat)
					 VALUES (:characterid, :skillcode, 'REG', 0, sysdate(), 'DEPART', 'ACTIF');";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":characterid", $character['Id'], PDO::PARAM_INT);
				$this->DAL->Bind(":skillcode", $inSkillCode, PDO::PARAM_STR);
			$r = $this->DAL->FetchResult();

		}

		return True;
	}


	//--UPDATE ALL OUTDATED CHARACTERS--
	public function UpdateAllOutdatedCharacters()
	{
		// Verify data
		if( !isset($this->Data) ) { $this->Error = "No data!"; return False; }


		// Update each outdated character
		foreach ($this->Data->OutdatedCharacters as $character) {
			//// -- Massive Update #1 : Transform precisable skills -- 

			// Get outdated skills
			$SkillList = array();

			$lQuery = 	"SELECT cac.Id, cac.CodeCompetence, cac.Usages, cac.DateCreation, cac.CodeAcquisition, cac.CodeEtat  
					 FROM db_perso.competences_acquises cac
					 WHERE cac.CodeCompetence IN (SELECT creg.Code 
									FROM db_pilot.competences_regulieres creg
							 		WHERE creg.IndPrecision = 1)
					   AND cac.Usages IS NOT NULL
					   AND cac.CodeEtat <> 'SUPPR'
					   AND cac.IdPersonnage = :characterid ;";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":characterid", $character['Id'], PDO::PARAM_INT);
			$SkillList = $this->DAL->FetchResult();

			// Update the skills
			foreach ($SkillList as $skill) {
				// Update the skill's status
				$lQuery = 	"UPDATE db_perso.competences_acquises
						 SET CodeEtat = 'SUPPR'
						 WHERE Id = :skillid ;";

				$this->DAL->SetQuery($lQuery);
					$this->DAL->Bind(":skillid", $skill['Id'], PDO::PARAM_INT);
				$r = $this->DAL->FetchResult();


				// Create one line for each usage of the skill
				for ($i=1; $i <= $skill['Usages'] ; $i++) { 
					$lQuery = 	"INSERT INTO db_perso.competences_acquises (IdPersonnage, CodeCompetence, Type, CoutXP, DateCreation, CodeAcquisition, CodeEtat, `Precision`)
							 VALUES (:characterid, :skillcode, 'REG', 0, :creationdate, :acquisitioncode, :status, :precision);";

					$this->DAL->SetQuery($lQuery);
						$this->DAL->Bind(":characterid", $character['Id'], PDO::PARAM_INT);
						$this->DAL->Bind(":skillcode", $skill['CodeCompetence'], PDO::PARAM_STR);
						$this->DAL->Bind(":creationdate", $skill['DateCreation'], PDO::PARAM_STR);
						$this->DAL->Bind(":acquisitioncode", $skill['CodeAcquisition'], PDO::PARAM_STR);
						$this->DAL->Bind(":status", $skill['CodeEtat'], PDO::PARAM_STR);
						$this->DAL->Bind(":precision", 'Sort/Recette/Métier #'.$i, PDO::PARAM_STR);
					$r = $this->DAL->FetchResult();
				}
			}

			// Update the character
			// -- No character update required for MU #1

		}

		return True;
	}


	//--RESET SEARCH RESULTS--
	public function ResetSearchData()
	{
		$this->Data->SearchSubject = NULL;
		$this->Data->SearchResults = array();		
	}


	//--FIND USER OR CHARACTER MATCHING SEARCH STRING--
	public function FindUserOrCharacter($inSubject, $inSearchString)
	{
		// Verify data
		if( !isset($this->Data) ) 	{ $this->Error = "No data!"; return False; }
		if( !$inSubject ) 		{ $this->Error = "No subject!"; return False; }
		if( !$inSearchString ) 		{ $this->Error = "No search string!"; return False; }


		// Prepare data
		$lString = '%'.str_replace('%', '', $inSearchString).'%';	// Strip wild card from the seach string, then wrap it in them to avoid bad SQL query.


		// Ask the database
		$lQuery = "SELECT '0' AS Id, 'Aucun résultat' AS Compte, 'pour' AS Prenom, :searchstring AS Nom, '' AS CodeEtat FROM DUAL;" ;

		if( $inSubject == 'Comptes' ) {
			$lQuery = 	"SELECT ind.Id, ind.Compte, ind.Prenom, ind.Nom, ind.CodeEtat
					 FROM db_indiv.individus ind
					 WHERE ind.Compte LIKE :searchstring 
					    OR ind.Prenom LIKE :searchstring
					    OR ind.Nom LIKE :searchstring ;";
		}
		elseif( $inSubject == 'Personnages' ) {
			$lQuery = 	"SELECT per.Id, ind.Compte, per.Prenom, per.Nom, per.CodeEtat
					 FROM db_perso.personnages per 
					 	JOIN db_indiv.individus ind ON per.IdIndividu = ind.Id
					 WHERE ind.Compte LIKE :searchstring 
					    OR per.Prenom LIKE :searchstring
					    OR per.Nom LIKE :searchstring ;";
		}

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":searchstring", $lString, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		// Set data
		$this->Data->SearchSubject = $inSubject;
		if( $r ) { $this->Data->SearchResults = $r; }
		else { $this->Data->SearchResults = array(); }

		return True;
	}


	//--RESET AN ACCOUNT AND ITS PASSWORD--
	public function ResetAccount($inAccountID, $inPassword)
	{
		// Verify data
		if( !isset($this->Data) ) 	{ $this->Error = "No data!"; return False; }
		if( !$inAccountID ) 		{ $this->Error = "No account!"; return False; }
		if( !$inPassword ) 		{ $this->Error = "No password!"; return False; }


		// Prepare data
		$lSalted = hash("sha256" , $inPassword.SALT);


		// Validate the account's existence
		$lQuery = "SELECT Id, Compte, Prenom, Nom, Courriel FROM db_indiv.individus WHERE Id = :accountid;" ;

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":accountid", $inAccountID, PDO::PARAM_INT);
		$check = $this->DAL->FetchResult();

		if( !$check ) { $this->Error = "Account does not exist!"; return False; }


		// Ask the database
		$lQuery = 	"UPDATE db_indiv.individus 
				 SET CodeEtat = 'ACTIF', MotDePasse = :pw
				 WHERE Id = :accountid;" ;

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":accountid", $inAccountID, PDO::PARAM_INT);
			$this->DAL->Bind(":pw", $lSalted, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		// Set data
		$this->Data->SearchResults = $check[0];

		return True;
	}


	//--CHANGE A CHARACTER'S CLASS AND GET NEW SKILL LIST--
	public function ChangeCharacterClass($inCharacterID, $inClassCode, $inArchetypeCode)
	{
		// Verify data
		if( !isset($this->Data) ) { $this->Error = "No data!"; return False; }
		if( !$inCharacterID ) { $this->Error = "No character!"; return False; }
		if( !$inClassCode ) { $this->Error = "No new class!"; return False; }
		if( !$inArchetypeCode ) { $this->Error = "No class archetype!"; return False; }


		// Validate class code
		$lQuery = 	"SELECT Code FROM db_pilot.classes
				 WHERE Code = :classcode;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":classcode", $inClassCode, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		if( !$this->DAL->GetRowCount() ) { $this->Error = "Class does not exist!"; return False; }


		// Validate archetype choice code
		$lQuery = 	"SELECT Code FROM db_pilot.archetypes
				 WHERE Code = :archetypecode;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":archetypecode", $inArchetypeCode, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		if( !$this->DAL->GetRowCount() ) { $this->Error = "Archetype does not exist!"; return False; }


		// Validate character's existence
		$lQuery = 	"SELECT Id FROM db_perso.personnages
				 WHERE Id = :characterid;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $inCharacterID, PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();

		if( !$this->DAL->GetRowCount() ) { $this->Error = "Character does not exist!"; return False; }


		// Update character's class
		$lQuery = 	"UPDATE db_perso.personnages
				 SET CodeClasse = :classcode, CodeArchetype = :archetypecode
				 WHERE Id = :characterid;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":classcode", $inClassCode, PDO::PARAM_STR);
			$this->DAL->Bind(":archetypecode", $inArchetypeCode, PDO::PARAM_STR);
			$this->DAL->Bind(":characterid", $inCharacterID, PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Update life
		$lQuery = 	"UPDATE db_perso.points_de_vie pv
				 SET pv.PV = (SELECT c.PVDepart FROM db_pilot.classes c
				 	   WHERE c.Code = :classcode)
				 WHERE pv.IdPersonnage = :characterid
				   AND pv.Raison = 'PV de départ';";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":classcode", $inClassCode, PDO::PARAM_STR);
			$this->DAL->Bind(":characterid", $inCharacterID, PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Delete lost skills
		$lQuery = 	"DELETE FROM db_perso.competences_acquises
				 WHERE IdPersonnage = :characterid
				 AND CodeAcquisition = 'DEPART'
				 AND CodeCompetence NOT IN (SELECT CodeCompReg
							    FROM db_pilot.competences_depart
							    WHERE CodeClasse = :classcode
				                              AND CodeCompReg IS NOT NULL)
				 AND CodeCompetence NOT IN (SELECT CodeCompSpec
							    FROM db_pilot.competences_depart
							    WHERE CodeClasse = :classcode
				                              AND CodeCompSpec IS NOT NULL)
				 AND CodeCompetence NOT IN (SELECT CodeCompReg 
							    FROM db_pilot.competences_archetype
							    WHERE CodeArchetype = :archetypecode
				                              AND CodeCompReg IS NOT NULL)
				 AND CodeCompetence NOT IN (SELECT CodeCompSpec 
							    FROM db_pilot.competences_archetype
							    WHERE CodeArchetype = :archetypecode
				                              AND CodeCompSpec IS NOT NULL)
				 AND CodeCompetence NOT IN (SELECT CodeCompReg 
							    FROM db_pilot.competences_raciales
							    WHERE CodeRace = (SELECT CodeRace FROM db_perso.personnages WHERE Id = :characterid))
				 AND CodeCompetence NOT IN (SELECT CodeCompSpec 
							    FROM db_pilot.competences_raciales
							    WHERE CodeRace = (SELECT CodeRace FROM db_perso.personnages WHERE Id = :characterid));";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $inCharacterID, PDO::PARAM_INT);
			$this->DAL->Bind(":classcode", $inClassCode, PDO::PARAM_STR);
			$this->DAL->Bind(":archetypecode", $inArchetypeCode, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		// Insert missing single-application skills
			// CLASS - REGULAR
		$lQuery = 	"INSERT INTO db_perso.competences_acquises (IdPersonnage, CodeCompetence, Type, Usages, CoutXP, DateCreation, CodeAcquisition, CodeEtat)
				 SELECT :characterid, cdep.CodeCompReg, 'REG', creg.Usages, 0, sysdate(), 'DEPART', 'ACTIF'
				 FROM db_pilot.competences_depart cdep
					JOIN db_pilot.competences_regulieres creg ON creg.Code = cdep.CodeCompReg
				 WHERE cdep.CodeClasse = :classcode
				 AND cdep.CodeCompReg IS NOT NULL
				 AND cdep.NbrApplications = 1
				 AND cdep.CodeCompReg NOT IN (SELECT cac.CodeCompetence FROM db_perso.competences_acquises cac
							      WHERE cac.IdPersonnage = :characterid
							        AND cac.CodeAcquisition = 'DEPART'
								AND cac.CodeEtat = 'ACTIF');";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $inCharacterID, PDO::PARAM_INT);
			$this->DAL->Bind(":classcode", $inClassCode, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

			// CLASS - SPECIAL
		$lQuery = 	"INSERT INTO db_perso.competences_acquises (IdPersonnage, CodeCompetence, Type, Usages, CoutXP, DateCreation, CodeAcquisition, CodeEtat)
				 SELECT :characterid, cdep.CodeCompSpec, cspec.Type, NULL, 0, sysdate(), 'DEPART', 'ACTIF'
				 FROM db_pilot.competences_depart cdep
					JOIN db_pilot.competences_speciales cspec ON cspec.Code = cdep.CodeCompSpec
				 WHERE cdep.CodeClasse = :classcode
				 AND cdep.CodeCompSpec IS NOT NULL
				 AND cdep.NbrApplications = 1
				 AND cdep.CodeCompSpec NOT IN (SELECT cac.CodeCompetence FROM db_perso.competences_acquises cac
								WHERE cac.IdPersonnage = :characterid
								AND cac.CodeAcquisition = 'DEPART'
								AND cac.CodeEtat = 'ACTIF');";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $inCharacterID, PDO::PARAM_INT);
			$this->DAL->Bind(":classcode", $inClassCode, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

			// ARCHETYPE - REGULAR
		$lQuery = 	"INSERT INTO db_perso.competences_acquises (IdPersonnage, CodeCompetence, Type, Usages, CoutXP, DateCreation, CodeAcquisition, CodeEtat)
				 SELECT :characterid, carch.CodeCompReg, 'REG', creg.Usages, 0, sysdate(), 'DEPART', 'ACTIF'
				 FROM db_pilot.competences_archetype carch
					JOIN db_pilot.competences_regulieres creg ON creg.Code = carch.CodeCompReg
				 WHERE carch.CodeArchetype = :archetypecode
				 AND carch.CodeCompReg IS NOT NULL
				 AND carch.NbrApplications = 1
				 AND carch.CodeCompReg NOT IN (SELECT cac.CodeCompetence FROM db_perso.competences_acquises cac
							      WHERE cac.IdPersonnage = :characterid
								AND cac.CodeAcquisition = 'DEPART'
								AND cac.CodeEtat = 'ACTIF');";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $inCharacterID, PDO::PARAM_INT);
			$this->DAL->Bind(":archetypecode", $inArchetypeCode, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

			// ARCHETYPE - SPECIAL
		$lQuery = 	"INSERT INTO db_perso.competences_acquises (IdPersonnage, CodeCompetence, Type, Usages, CoutXP, DateCreation, CodeAcquisition, CodeEtat)
				 SELECT :characterid, carch.CodeCompSpec, cspec.Type, NULL, 0, sysdate(), 'DEPART', 'ACTIF'
				 FROM db_pilot.competences_archetype carch
					JOIN db_pilot.competences_speciales cspec ON cspec.Code = carch.CodeCompSpec
				 WHERE carch.CodeArchetype = :archetypecode
				 AND carch.CodeCompSpec IS NOT NULL
				 AND carch.NbrApplications = 1
				 AND carch.CodeCompSpec NOT IN (SELECT cac.CodeCompetence FROM db_perso.competences_acquises cac
								WHERE cac.IdPersonnage = :characterid
								AND cac.CodeAcquisition = 'DEPART'
								AND cac.CodeEtat = 'ACTIF');";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $inCharacterID, PDO::PARAM_INT);
			$this->DAL->Bind(":archetypecode", $inArchetypeCode, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		// Manage multi-application skills
			// Get list
		$lQuery = 	"SELECT cdep.CodeCompReg, creg.Usages, cdep.NbrApplications
				 FROM db_pilot.competences_depart cdep
					JOIN db_pilot.competences_regulieres creg ON creg.Code = cdep.CodeCompReg
				 WHERE cdep.CodeClasse = :classcode
				 AND cdep.CodeCompReg IS NOT NULL
				 AND cdep.NbrApplications > 1
				 UNION ALL
				 SELECT carch.CodeCompReg, creg.Usages, carch.NbrApplications
				 FROM db_pilot.competences_archetype carch
					JOIN db_pilot.competences_regulieres creg ON creg.Code = carch.CodeCompReg
				 WHERE carch.CodeArchetype = :archetypecode
				 AND carch.CodeCompReg IS NOT NULL
				 AND carch.NbrApplications > 1;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":classcode", $inClassCode, PDO::PARAM_STR);
			$this->DAL->Bind(":archetypecode", $inArchetypeCode, PDO::PARAM_STR);
		$sublist = $this->DAL->FetchResult();

		foreach ($sublist as $skill) {

			// Get number of applications the character already has
			$lQuery = "SELECT count(cac.CodeCompetence) AS Compteur 
				   FROM db_perso.competences_acquises cac
				   WHERE cac.IdPersonnage = :characterid
				     AND cac.CodeCompetence = :skillcode
				     AND cac.CodeAcquisition = 'DEPART'
				     AND cac.CodeEtat = 'ACTIF';";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":characterid", $inCharacterID, PDO::PARAM_INT);
				$this->DAL->Bind(":skillcode", $skill['CodeCompReg'], PDO::PARAM_STR);
			$skillcount = $this->DAL->FetchResult()[0]['Compteur'];

			// Insert or delete the difference
			$difference = $skill['NbrApplications'] - $skillcount;
			echo "Différence : ".$difference;

			if ($difference > 0) {
				// Character needs more. Insert
				for ($i=1; $i <= $difference ; $i++) { 
					$lQuery = "INSERT INTO db_perso.competences_acquises (IdPersonnage, CodeCompetence, Type, Usages, CoutXP, DateCreation, CodeAcquisition, CodeEtat)
						   VALUES (:characterid, :skillcode, 'REG', :skilluses, 0, sysdate(), 'DEPART', 'ACTIF');";

					$this->DAL->SetQuery($lQuery);
						$this->DAL->Bind(":characterid", $inCharacterID, PDO::PARAM_INT);
						$this->DAL->Bind(":skillcode", $skill['CodeCompReg'], PDO::PARAM_STR);
						$this->DAL->Bind(":skilluses", $skill['Usages'], PDO::PARAM_INT);
					$r = $this->DAL->FetchResult();
				}
			}
			elseif ($difference < 0) {
				echo -$difference;
				//Character has too many. Delete.
				$lQuery = 	"DELETE FROM db_perso.competences_acquises 
						 WHERE IdPersonnage = :characterid
						   AND CodeCompetence = :skillcode
						   AND CodeAcquisition = 'DEPART'
			     			   AND CodeEtat = 'ACTIF'
						 ORDER BY Id DESC LIMIT :difference ;";

				$this->DAL->SetQuery($lQuery);
					$this->DAL->Bind(":characterid", $inCharacterID, PDO::PARAM_INT);
					$this->DAL->Bind(":skillcode", $skill['CodeCompReg'], PDO::PARAM_STR);
					$this->DAL->Bind(":difference", (-$difference), PDO::PARAM_INT);
				$r = $this->DAL->FetchResult();
			}

		}


		// Get new skill list
		$lQuery = 	"SELECT cac.CodeCompetence, cac.Type, cac.Usages, cac.CoutXP, cac.CodeAcquisition, cac.CodeEtat 
				 FROM db_perso.competences_acquises cac
				 WHERE cac.IdPersonnage = :characterid
				 ORDER BY cac.CodeCompetence ASC;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $inCharacterID, PDO::PARAM_INT);
		$this->Data->NewSkillList = $this->DAL->FetchResult();


		return True;
	}


	//--RESET AN ACCOUNT AND ITS PASSWORD--
	public function DeleteCharacter($inCharacterID, $inPassword)
	{
		// Verify data
		if( !isset($this->Data) ) 	{ $this->Error = "No data!"; return False; }
		if( !$inCharacterID ) 		{ $this->Error = "No character!"; return False; }
		if( !$inPassword ) 		{ $this->Error = "No password!"; return False; }


		// Prepare data
		$lSalted = hash("sha256" , $inPassword.SALT);


		// Validate the account's existence
		$lQuery = "SELECT Compte FROM db_indiv.individus WHERE Id = :accountid AND MotDePasse = :pw AND NiveauAcces = :accesslevel ;" ;

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":accountid", $_SESSION['authenticated']->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":pw", $lSalted, PDO::PARAM_STR);
			$this->DAL->Bind(":accesslevel", DBA_LEVEL, PDO::PARAM_INT);
		$check = $this->DAL->FetchResult();

		if( !$check ) { $this->Error = "Your Administrator password is incorrect!"; return False; }


		// Ask the database
		// Physical deletion
		$lQuery = 	"DELETE FROM db_perso.personnages WHERE Id = :characterid;
				 DELETE FROM db_perso.competences_acquises WHERE IdPersonnage = :characterid;
				 DELETE FROM db_perso.titres WHERE IdPersonnage = :characterid;
				 DELETE FROM db_perso.experience WHERE IdPersonnage = :characterid;
				 DELETE FROM db_perso.points_de_vie WHERE IdPersonnage = :characterid;
				 DELETE FROM db_perso.maitres WHERE IdPersonnage = :characterid;
				 DELETE FROM db_perso.parties_quete WHERE IdQuete IN (SELECT Id FROM db_perso.quetes WHERE IdPersonnage = :characterid);
				 DELETE FROM db_perso.quetes WHERE IdPersonnage = :characterid;
				 DELETE FROM db_perso.resumes WHERE IdPersonnage = :characterid;
				 DELETE FROM db_perso.invitations WHERE IdPersonnage = :characterid;
				 DELETE FROM db_perso.approbations WHERE IdPersonnage = :characterid;
				 DELETE FROM db_perso.remarques WHERE IdPersonnage = :characterid;
				 DELETE FROM db_group.membres WHERE IdPersonnage = :characterid;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $inCharacterID, PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Set data
		$this->Data->SearchResults = $check[0];

		return True;
	}


} // END of CustomServices class

?>
