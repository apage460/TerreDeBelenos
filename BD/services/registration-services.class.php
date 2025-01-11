<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Registration Services v1.2 r8 ==			║
║	Manages Registrations to activities.			║
║	Non-serializable. Requires DAL. Uses MySQL queries.	║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('services/database.class.php'); 		// Data Access Layer
include_once('models/user.class.php'); 			// User definition
include_once('models/registrationmanager.class.php');	// Registration Manager definition

class RegistrationServices
{

protected $DAL;
protected $User;
protected $Manager;

public $Error;

	//--CONSTRUCTOR--
	public function __construct($inDataAccessLayer, $inUser =NULL, $inManager =NULL)
	{
		$this->DAL = $inDataAccessLayer;

		if( isset($inUser) ) {$this->User = $inUser;}
		if( isset($inManager) ) {$this->Manager = $inManager;}
	}


	//--GET/SET FUNCTIONS--
	public function GetUser() { return $this->User; }
	public function SetUser($inUser) { $this->User = $inUser; }

	public function GetManager() { return $this->Manager; }
	public function SetManager($inManager) { $this->Manager = $inManager; }


	//--UPDATE MANAGER'S INFO--
	public function UpdateManager()
	{
		// Check is user and activities are defined
		if( !isset($this->User) ) { $this->Error = "No user set!"; return False; }
		if( !isset($this->Manager) ) { $this->Error = "No manager set!"; return False; }


		// Rebuild the lists
		$this->GetActivityList();
		$this->GetPassList();
		
		$this->GetUserRegistrations();
		$this->GetUserAttendances();
		$this->GetUserCharacters();

		// Determine if user is a kid
		if( $this->User->GetAge() < 16 ) { $this->Manager->SetKidStatus(True); }
		else { $this->Manager->SetKidStatus(False); }

		// Determine if user is a new player
		if( !$this->User->GetLastAttendance() && !$this->User->GetOldAttendance() ) { $this->Manager->SetNewPlayer(True); }
		else { $this->Manager->SetNewPlayer(False); }

		// Get user's free activity vouchers
		$this->Manager->SetFreeActivityVouchers( $this->User->GetFreeActivityVouchers() );
		$this->Manager->SetFreeKidVouchers( $this->User->GetFreeKidVouchers() );

		return True;
	}


	//--UPDATE THE ACTIVITY LIST--
	public function GetActivityList()
	{
		// Check if manager is set
		if( !isset($this->Manager) ) { $this->Error = "No manager set!"; return False; }


		// Ask the database
		$lQuery = 	"SELECT act.Id, act.Nom, act.Description, act.Type, act.DateDebut, act.DateFin, cpa.PrixRegulier, cpa.DelaiRetard, cpa.PrixRetard
				 FROM db_activ.activites act 
					JOIN db_activ.types_activite cpa ON act.Type = cpa.Type
				 WHERE act.Type = 'GN'
				   AND act.CodeUnivers = :universecode
				 ORDER BY act.DateDebut ASC";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		$lActivityList = array();
		foreach( $r as $activity) {
			$attributes = [
				'id'			=>	$activity['Id'],
				'name'			=>	$activity['Nom'],
				'description'		=>	$activity['Description'],
				'type'			=>	$activity['Type'],
				'regularprice'		=>	$activity['PrixRegulier'],
				'lateprice'		=>	$activity['PrixRetard'],
				'delaybeforelate'	=>	$activity['DelaiRetard'],
				'startingdate'		=>	$activity['DateDebut'],
				'endingdate'		=>	$activity['DateFin']
			];

			$lActivityList[] = new Activity( $attributes );
		}

		$this->Manager->SetActivities( $lActivityList );

		return True;
	}


	//--UPDATE THE PASS LIST--
	public function GetPassList()
	{
		// Check if manager is set
		if( !isset($this->User) ) { $this->Error = "No user set!"; return False; }
		if( !isset($this->Manager) ) { $this->Error = "No manager set!"; return False; }


		// Ask the database
		$lQuery = 	"SELECT pass.Id, pass.Nom, pass.Description, pass.Prix, pass.DateDebut, pass.DateFin, 
							(SELECT 1 FROM db_indiv.passes_acquises pac WHERE pac.IdPasse = pass.Id AND pac.CodeEtat = 'ACTIF' AND pac.IdIndividu = :userid LIMIT 1) AS Obtenue
				 FROM db_activ.passes pass 
				 WHERE pass.DateFin > sysdate()
				 ORDER BY pass.DateDebut DESC";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		$lPassList = array();
		foreach( $r as $i => $activity) {
			$attributes = [
				'id'		=>	$activity['Id'],
				'name'		=>	$activity['Nom'],
				'description'	=>	$activity['Description'],
				'price'		=>	$activity['Prix'],
				'startingdate'	=>	$activity['DateDebut'],
				'endingdate'	=>	$activity['DateFin'],
				'endingdate'	=>	$activity['DateFin'],
				'acquired'	=>	$activity['Obtenue']
			];

			$lPassList[$i] = new Pass( $attributes );
			$lPassList[$i]->SetFreeActivities( $this->GetFreeActivities( $activity['Id'] ) );
		}

		$this->Manager->SetPasses( $lPassList );
		$this->Manager->AdjustActivityPrices();

		return True;
	}


	//--UPDATE THE PASS LIST--
	public function GetFreeActivities( $inPassID )
	{
		// Ask the database
		$lQuery = 	"SELECT app.IdActivite 
				 FROM db_activ.acces_par_passe app 
				 WHERE app.IdPasse = :passid
				 ORDER BY app.IdActivite ASC";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":passid", $inPassID, PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		$lFreeActivities = array();
		foreach( $r as $activity) { 
			$lFreeActivities[] = $activity['IdActivite'];
		}

		return $lFreeActivities;
	}


	//--UPDATE USER'S REGISTRATION LIST--
	public function GetUserRegistrations()
	{
		// Check if manager is set
		if( !isset($this->User) ) { $this->Error = "No user set!"; return False; }
		if( !isset($this->Manager) ) { $this->Error = "No manager set!"; return False; }


		// Ask the database
		$lQuery = 	"SELECT insc.IdIndividu, insc.IdActivite, act.Nom, act.DateFin, insc.PrixInscrit, insc.DateInscription, insc.IndPrepaye, insc.Enfants, 
					insc.EntreeGratuite, insc.EntreesEnfantGratuites, insc.IdPersonnage, insc.NomPersonnage, per.Niveau
				 FROM db_activ.inscriptions insc
				 	JOIN db_activ.activites act ON act.Id = insc.IdActivite
				 	LEFT JOIN db_perso.personnages per ON insc.IdPersonnage = per.Id
				 WHERE insc.IdIndividu = :userid
				   AND act.Type = 'GN'
				   AND act.CodeUnivers = :universecode
				 ORDER BY act.DateDebut DESC";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		$lRegList = array();
		foreach( $r as $registration) {
			$lRegList[] = [
					'userid'		=>	$registration['IdIndividu'],
					'activityid'		=>	$registration['IdActivite'],
					'activityname'		=>	$registration['Nom'],
					'activityend'		=>	$registration['DateFin'],
					'characterid'		=>	$registration['IdPersonnage'],
					'charactername'		=>	$registration['NomPersonnage'],
					'characterlevel'	=>	$registration['Niveau'],
					'price'			=>	$registration['PrixInscrit'],
					'date'			=>	$registration['DateInscription'],
					'prepaid'		=>	$registration['IndPrepaye'],
					'free'			=>	$registration['EntreeGratuite'],
					'kids'			=>	$registration['Enfants'],
					'freekids'		=>	$registration['EntreesEnfantGratuites']
				];
		}

		$this->Manager->SetRegistrations( $lRegList );

		return True;
	}


	//--GET USER'S ATTENDANCE LIST--
	public function GetUserAttendances()
	{
		// Verify there's a user
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT act.Id, act.Nom, pres.DateInscription
				 FROM db_indiv.presences pres 
				 	LEFT JOIN db_activ.activites act ON pres.IdActivite = act.Id
				 WHERE pres.IdIndividu = :userid
				   AND act.Type = 'GN'
				   AND act.CodeUnivers = :universecode
				 ORDER BY act.DateDebut DESC";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		// Build ans save result array
		$lAttendanceList = array();
		foreach($r as $i => $activity) { 
			$lAttendanceList[] = [
					'id'		=> 	$activity['Id'],
					'name'		=>	$activity['Nom'],
					'date'		=>	$activity['DateInscription']
			];
		}

		$this->Manager->SetAttendances( $lAttendanceList );

		return True;
	}


	//--GET USER'S CHARACTER LIST--
	public function GetUserCharacters()
	{
		// Check if manager is set
		if( !isset($this->Manager) ) { $this->Error = "GetUserCharacters : No manager set!"; return False; }
		if( !isset($this->User) ) { $this->Error = "GetUserCharacters : No user!"; return False; }


		// Ask the database...
		$lQuery = 	"SELECT per.Id, per.Prenom, per.Nom, per.CodeEtat, per.Niveau
				 FROM db_perso.personnages per 
				 WHERE per.IdIndividu = :userid
				   AND per.CodeUnivers = :universecode
				   AND per.CodeEtat NOT IN ('SUPPR','MORT','RETIR','DEPOR','EXIL','INACT');";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		// Build result array
		$lCharacterList = array();
		foreach($r as $character) { 
			$attributes = [
				'id' 		=> $character['Id'],
				'firstname' 	=> $character['Prenom'],
				'lastname' 	=> $character['Nom'],
				'status' 	=> $character['CodeEtat'],
				'level'		=> $character['Niveau']
			];
			$lCharacterList[] = new Character( $attributes );
		}

		$this->Manager->SetCharacters($lCharacterList);
		return True;
	}


	//--PREREGISTER USER--
	public function PreregisterUser( $inUserID, $inActivity, $inCharacter, $inType, $inPrice, $inAlreadyPaid, $inYoungChildren, $inUsedVoucher, $inUsedKidVoucher )
	{
		// Check if manager is set
		if( !isset($this->User) ) { $this->Error = "No user set!"; return False; }
		if( !isset($this->Manager) ) { $this->Error = "No manager set!"; return False; }

		// Prepare data
		$lMessage = 'Préinscription : '.$inActivity->GetName().' pour '.$inPrice.' $. ';
			if( $inAlreadyPaid ) { $lMessage .= 'Prépayé.'; }


		// Register
		$lQuery = 	"INSERT INTO db_activ.inscriptions (IdIndividu, IdActivite, IdPersonnage, NomPersonnage, DateInscription, Type, PrixInscrit, 
								    IndPreinscription, IndPrepaye, Enfants, EntreeGratuite, EntreesEnfantGratuites)
				 VALUES	(:userid, :activityid, :characterid, :charactername, sysdate(), :type, :price, 1, :prepaid, :youngchildren, :usedvoucher, :usedkidvoucher );

				 INSERT INTO db_indiv.remarques (IdIndividu, Message, Type, DateCreation)
				 VALUES ( :userid, :message, 'PRINS', sysdate() );

				 UPDATE db_indiv.individus 
				 SET ActivitesGratuites = ActivitesGratuites - :usedvoucher, EnfantsGratuits = EnfantsGratuits - :usedkidvoucher
				 WHERE Id = :userid ;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $inUserID, PDO::PARAM_INT);
			$this->DAL->Bind(":activityid", $inActivity->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":characterid", $inCharacter->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":charactername", $inCharacter->GetFullName(), PDO::PARAM_STR);
			$this->DAL->Bind(":type", $inType, PDO::PARAM_STR);
			$this->DAL->Bind(":price", $inPrice, PDO::PARAM_STR);
			$this->DAL->Bind(":prepaid", $inAlreadyPaid, PDO::PARAM_INT);
			$this->DAL->Bind(":youngchildren", $inYoungChildren, PDO::PARAM_INT);

			$this->DAL->Bind(":usedvoucher", $inUsedVoucher, PDO::PARAM_INT);
			$this->DAL->Bind(":usedkidvoucher", $inUsedKidVoucher, PDO::PARAM_INT);

			$this->DAL->Bind(":message", $lMessage , PDO::PARAM_STR);
		$this->DAL->FetchResult();

		// Secure new level for chosen character
		$inCharacter->SetLevel( $inCharacter->GetLevel()+1 );

		$lQuery = 	"UPDATE db_perso.personnages per
				 SET per.CodeEtat = 'ACTIF'
				 WHERE per.IdIndividu = :userid
				   AND per.CodeEtat = 'LEVEL';

				 UPDATE db_perso.personnages per
				 SET per.CodeEtat = 'LEVEL', per.Niveau = :characterlevel
				 WHERE per.Id = :characterid;				 

				 UPDATE db_perso.competences_acquises cac
				 SET cac.CodeEtat = 'ACTIF'
				 WHERE cac.IdPersonnage = :characterid
				   AND cac.CodeEtat = 'LEVEL';

				 INSERT INTO db_perso.remarques (IdPersonnage, Message, Type, DateCreation)
				 VALUES ( :characterid, :message, 'LEVEL', sysdate() );";


		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $inUserID, PDO::PARAM_INT);
			$this->DAL->Bind(":characterid", $inCharacter->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":characterlevel", $inCharacter->GetLevel(), PDO::PARAM_INT);
			$this->DAL->Bind(":message", 'Niveau '.$inCharacter->GetLevel().'!', PDO::PARAM_STR);
		$this->DAL->FetchResult();

		// Grant experience if new level is between 2 and 5
		if( $inCharacter->GetLevel() > 1 && $inCharacter->GetLevel() <= 5) {

		// Ask the database...
			$lQuery = 	"INSERT INTO db_perso.experience (IdPersonnage, Raison, XP, DateInscription)
				 	 VALUES ( :characterid, :reason, 50, sysdate() );";
	
			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":characterid", $inCharacter->GetID(), PDO::PARAM_INT);
				$this->DAL->Bind(":reason", 'Niveau '. $inCharacter->GetLevel(), PDO::PARAM_STR);
			$this->DAL->FetchResult();
		}

		return True;
	}


	//--UNREGISTER USER--
	public function UnregisterUser( $inRegistration )
	{
		// Check if manager is set
		if( !isset($this->User) ) { $this->Error = "No user set!"; return False; }
		if( !isset($this->Manager) ) { $this->Error = "No manager set!"; return False; }


		// Prepare data
		$lPrepaid = $inRegistration['prepaid'];
		$lUsedVoucher = $inRegistration['free'];
			if( $lPrepaid ) { $lUsedVoucher = 1; }
		$lUsedKidVoucher = $inRegistration['freekids'];
			if( $lPrepaid ) { $lUsedKidVoucher = $inRegistration['kids']; }

		$lNewCharacterLevel = $inRegistration['characterlevel'] - 1;

		$lCanceledSkills = array();
		$lReplacedSkills = array();
		$lRefundedTeachings = array();
		$lCanceledLife = 0;


		// Get canceled skills
		$lQuery = 	"SELECT cac.Id, cac.CodeCompetence, cac.CoutXP, cac.CodeAcquisition, creg.CodeCompRemplacee, creg.Nom
				 FROM db_perso.competences_acquises cac JOIN db_pilot.competences_regulieres creg ON cac.CodeCompetence = creg.Code
				 WHERE cac.IdPersonnage = :characterid
				   AND cac.CodeEtat = 'PRLVL';";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $inRegistration['characterid'], PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Calculate impacts and refund XP
		foreach( $r as $skill ) {
			if( $skill['CoutXP'] ) 				{ $this->DeleteSkillCost( $inRegistration['characterid'], $skill['Nom'], $skill['CoutXP'] ); }
			if( $skill['CodeAcquisition'] == 'RABAIS' ) 	{ $lRefundedTeachings[] = $skill['CodeCompetence']; }
			if( $skill['CodeCompRemplacee'] ) 		{ $lReplacedSkills[] = $skill['CodeCompRemplacee']; }
			if( $skill['CodeCompetence'] == 'PVSUP' )	{ $lCanceledLife += 1; }
		}


		// Unregister
		$lQuery = 	"DELETE FROM db_activ.inscriptions
				 WHERE IdIndividu = :userid AND IdActivite = :activityid;

				 INSERT INTO db_indiv.remarques (IdIndividu, Message, Type, DateCreation)
				 VALUES ( :userid, 'Désinscription', 'PRINS', sysdate() );

				 UPDATE db_indiv.individus 
				 SET ActivitesGratuites = ActivitesGratuites + :usedvoucher, EnfantsGratuits = EnfantsGratuits + :usedkidvoucher
				 WHERE Id = :userid ;

				 UPDATE db_perso.personnages per
				 SET per.Niveau = :newcharacterlevel, per.CodeEtat = 'ACTIF'
				 WHERE per.Id = :characterid;

				 DELETE FROM db_perso.experience
				 WHERE IdPersonnage = :characterid
				   AND Raison = :lostlevelreason;

				 DELETE FROM db_perso.competences_acquises
				 WHERE IdPersonnage = :characterid
				   AND CodeEtat = 'PRLVL';

				 UPDATE db_perso.competences_acquises cac
				 SET cac.CodeEtat = 'ACTIF'
				 WHERE cac.IdPersonnage = :characterid
				   AND cac.CodeCompetence in (:replacedlist)
				   AND cac.CodeEtat = 'REMPL';

				 INSERT INTO db_perso.remarques (IdPersonnage, Message, Type, DateCreation)
				 VALUES ( :characterid, 'Niveau annulé!', 'LEVEL', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $inRegistration['userid'], PDO::PARAM_INT);
			$this->DAL->Bind(":activityid", $inRegistration['activityid'], PDO::PARAM_INT);

			$this->DAL->Bind(":characterid", $inRegistration['characterid'], PDO::PARAM_INT);
			$this->DAL->Bind(":newcharacterlevel", $lNewCharacterLevel, PDO::PARAM_INT);
			$this->DAL->Bind(":lostlevelreason", 'Niveau '.$inRegistration['characterlevel'], PDO::PARAM_STR);
			$this->DAL->Bind(":replacedlist", implode(',', $lReplacedSkills), PDO::PARAM_STR);

			$this->DAL->Bind(":usedvoucher", $lUsedVoucher, PDO::PARAM_INT);
			$this->DAL->Bind(":usedkidvoucher", $lUsedKidVoucher, PDO::PARAM_INT);
		$this->DAL->FetchResult();


		// Refund used teachings
		foreach( $lRefundedTeachings as $skillcode ) {
			$lQuery = 	"UPDATE db_perso.enseignements ens
					 SET ens.CodeEtat = 'ACTIF'
					 WHERE ens.CodeEtat = 'INACT'
					   AND ens.IdEtudiant = :characterid
					   AND ens.CodeCompetence = :skillcode
					 ORDER BY ens.IdActivite DESC LIMIT 1;";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":characterid", $inRegistration['characterid'], PDO::PARAM_INT);
				$this->DAL->Bind(":skillcode", $skillcode, PDO::PARAM_STR);
	 		$this->DAL->FetchResult();
		}


		// Take back added Life
		$lQuery = 	"DELETE FROM db_perso.points_de_vie
				 WHERE IdPersonnage = :characterid
				   AND Raison = 'Achat de PV'
				   AND PV = 1
				 ORDER BY Id DESC 
				 LIMIT :amount;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $inRegistration['characterid'], PDO::PARAM_INT);
			$this->DAL->Bind(":amount", $lCanceledLife, PDO::PARAM_INT);
 		$this->DAL->FetchResult();


		return True;
	}


	//--REFUND A PARTICULAR SKILL--
	public function DeleteSkillCost( $inCharacterID, $inSkillName, $inSkillCost )
	{
		$lQuery = " DELETE FROM db_perso.experience
	  		    WHERE IdPersonnage = :characterid
	  		    AND Raison = :reason
	  		    AND XP = :xp
	  		    LIMIT 1;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $inCharacterID, PDO::PARAM_INT);
			$this->DAL->Bind(":reason", 'Achat - '.$inSkillName, PDO::PARAM_STR);
			$this->DAL->Bind(":xp", -$inSkillCost, PDO::PARAM_INT);
		$this->DAL->FetchResult();
	}


	//--BUY A NEW PASS--
	public function BuyPass( $inPassIndex, $inPrepaid =False )
	{
		// Check if manager is set
		if( !isset($this->User) ) { $this->Error = "No user set!"; return False; }
		if( !isset($this->Manager) ) { $this->Error = "No manager set!"; return False; }


		// Prepare data
		$lPass = $this->Manager->GetPasses()[$inPassIndex];


		// Check if pass does not already exists
		$lQuery = 	"SELECT pac.Id FROM db_indiv.passes_acquises pac 
				 WHERE pac.IdIndividu = :userid AND pac.IdPasse = :passid AND CodeEtat = 'ACTIF';";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":passid", $lPass->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();
		if($r) { return False; }


		// Ask the database to register new pass
		$lQuery = 	"INSERT INTO db_indiv.passes_acquises (IdIndividu, IdPasse, CodeEtat, DateAcquisition)
				 VALUES	(:userid, :passid, 'ACTIF', sysdate() );

				 INSERT INTO db_indiv.remarques (IdIndividu, Message, Type, DateCreation)
				 VALUES (:userid, :message, 'ACHAT', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":passid", $lPass->GetID(), PDO::PARAM_INT);

			$this->DAL->Bind(":message", 'Achat : '.$lPass->GetName().' pour '.$lPass->GetPrice().' $.' , PDO::PARAM_STR);
		$this->DAL->FetchResult();

		if( !$inPrepaid ) {
			$lQuery = 	"INSERT INTO db_indiv.sommes_dues (IdIndividu, Raison, Montant, DateInscription)
					 VALUES (:userid, :passname, -:passprice, sysdate() );";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
				$this->DAL->Bind(":passname", $lPass->GetName(), PDO::PARAM_STR);
				$this->DAL->Bind(":passprice", $lPass->GetPrice(), PDO::PARAM_INT);
			$this->DAL->FetchResult();
		}
		return True;
	}


	//--BUY A PASSPORT--
	public function BuyPassport()
	{
		// Check if manager is set
		if( !isset($this->User) ) { $this->Error = "No user set!"; return False; }
		if( !isset($this->Manager) ) { $this->Error = "No manager set!"; return False; }


		// Prepare data
		$lPassportPrice = 200;
		$lPatronage = $this->User->GetTotalPatronage();
			if( $lPatronage >= 100 ) { $lPatronage = 100; }
		$lAdjustedPrice = $lPassportPrice - $lPatronage;


		// Check if user does not have too many free activities
		$lQuery = 	"SELECT ind.ActivitesGratuites FROM db_indiv.individus ind
				 WHERE ind.Id = :userid AND ind.ActivitesGratuites < 2;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();
		if($r) { $this->Error = "Too many free activities!"; return False; }


		// Ask the database to register new pass
		$lQuery = 	"UPDATE db_indiv.individus
				 SET ActivitesGratuites = ActivitesGratuites + 5
				 WHERE Id = :userid;

				 INSERT INTO db_indiv.sommes_dues (IdIndividu, Raison, Montant, DateInscription)
				 VALUES (:userid, 'Passeport GN', -:paidprice, sysdate() );

				 INSERT INTO db_indiv.remarques (IdIndividu, Message, Type, DateCreation)
				 VALUES (:userid, :message, 'ACHAT', sysdate() );";

		if( $lPatronage ) { $lQuery .= "INSERT INTO db_indiv.mecenat (IdIndividu, Projet, Montant, DateInscription)
						VALUES (:userid, 'Passeport GN', -:patronage, sysdate() );"; }

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":paidprice", $lAdjustedPrice, PDO::PARAM_INT);
			$this->DAL->Bind(":message", 'Achat : Passeport GN pour '.$lPassportPrice.' $.' , PDO::PARAM_STR);
			$this->DAL->Bind(":patronage", $lPatronage , PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--SAVE FIELD SERVICE REQUEST--
	public function SaveServiceRequest($inActivityID, $inRequestedService, $inServiceDetails)
	{
		// Check if manager is set
		if( !isset($this->User) ) { $this->Error = "SaveServiceRequest : No user set!"; return False; }
		if( !isset($this->Manager) ) { $this->Error = "SaveServiceRequest : No manager set!"; return False; }


		// Prepare data


		// Check if user does not have too many free activities
		$lQuery = 	"INSERT INTO db_indiv.services (IdIndividu, IdActivite, Service, InfoSupp)
				 VALUES (:userid, :activityid, :service, :moreinfo);";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":activityid", $inActivityID, PDO::PARAM_INT);
			$this->DAL->Bind(":service", $inRequestedService, PDO::PARAM_STR);
			$this->DAL->Bind(":moreinfo", $inServiceDetails, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		if($r){ return True; }

		$this->Error = "Il y a eu un problème lors de l'enregistrement de la demande.";
		return False;
	}


	//--SEND SERVICE REQUEST MAIL
	public function SendServiceRequestMail($inActivityName, $inRequestedService, $inServiceDetails)
	{
		// Prepare data
		$lUserName = $this->User->GetFullName();
		$lMail = $this->User->GetMailAddress();


		// Define mail transport
		$transport = Swift_SmtpTransport::newInstance("smtp.gmail.com", 465, "ssl")
			->setUsername(GMAIL_USERNAME)
			->setPassword(GMAIL_PASSWORD);

		$mailer = Swift_Mailer::newInstance($transport);

		$message = Swift_Message::newInstance('Demande de service - '.$inRequestedService.' - '.$lUserName)
		  ->setFrom(array($lMail => $lUserName))
		  ->setTo(array('Organisation@Terres-de-Belenos.com' => 'Organisation des Terres de Bélénos'))
		  ->setBody('Bonjour,<br />
		  	<br />
		  	Ceci est une demande de services aux campememnts. Voici les détails :<br />
		  	<br />
		  	<b>Nom : </b>'.$lUserName.'<br />
		  	<b>Courriel : </b>'.$lMail.'<br />
		  	____________________________________________<br />
		  	<br />
		  	<b>Service demandé : </b>'.$inRequestedService.'<br />
		  	<b>Activité : </b>'.$inActivityName.'<br />
		  	<b>Détails : </b>'.$inServiceDetails.'<br />
		  	<br />
		  	Merci et bonne journée!<br />
		  	<br />
		  	<i>- Courriel automatique produit via la Base de données bélénoise</i>', 'text/html');

		$message->setCC(array('BD@Terres-de-Belenos.com'));

		return $mailer->send($message);	
	}


	//--SAVE FIELD SERVICE REQUEST--
	public function UploadFileToServer()
	{
		// Check if manager is set
		if( !isset($this->User) ) { $this->Error = "UploadFileToServer : No user set!"; return False; }
		if( !isset($this->Manager) ) { $this->Error = "UploadFileToServer : No manager set!"; return False; }

		// Prepare data
		$lTarget = NEWS_UPLOAD_DIR . $this->GetUser()->GetID() . '/'. basename($_FILES['attachedfile']['name']);

		// Create directory if it does not exists
		if( !file_exists(NEWS_UPLOAD_DIR . $this->GetUser()->GetID()) ) { mkdir( NEWS_UPLOAD_DIR . $this->GetUser()->GetID() ); }

		// Upload file
		$r = move_uploaded_file($_FILES['attachedfile']['tmp_name'], $lTarget);
  		
  		if ($r) { 
  			$lMessage = "Le fichier ". htmlspecialchars( basename( $_FILES['attachedfile']['name'] )). " a été soumis."; 

			// Insert log
			$lQuery = 	"INSERT INTO db_indiv.remarques (IdIndividu, Message, Type)
					 VALUES (:userid, :message, 'TELEV');";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
				$this->DAL->Bind(":message", $lMessage, PDO::PARAM_STR);
			$this->DAL->FetchResult();

			return True;
  		} 
 
 		$this->Error = "Il y a eu un problème avec le dépôt de votre fichier.";
		return False;
	}


	//--SEND SERVICE REQUEST MAIL
	public function SendArticleSubmissionMail($inActivityName, $inTitle, $inCategory, $inSignature, $inFileAttached, $inDetails, $inRevisionApproved)
	{
		// Prepare data
		$lUserName = $this->User->GetFullName();
		$lMail = $this->User->GetMailAddress();


		// Define mail transport
		$transport = Swift_SmtpTransport::newInstance("smtp.gmail.com", 465, "ssl")
			->setUsername(GMAIL_USERNAME)
			->setPassword(GMAIL_PASSWORD);

		$mailer = Swift_Mailer::newInstance($transport);

		// Define message
		$message = Swift_Message::newInstance('Article pour le Feuillet - '.$inActivityName.' - '.$inTitle)
		  ->setFrom(array($lMail => $lUserName))
		  ->setTo(array(NEWSPAPER_MAIL => 'Rédacteur du Feuillet d\'Hyden'))
		  ->setCC(array('Organisation@Terres-de-Belenos.com' => 'Organisation des Terres de Bélénos'))
		  ->setBody('Bonjour,<br />
		  	<br />
		  	Voici un article soumis au Feuillet d\'Hyden pour '.$inActivityName.'. Voici les détails :<br />
		  	<br />
		  	<b><u>Soumissionnaire</u></b><br />
		  	<b>Nom : </b>'.$lUserName.'<br />
		  	<b>Courriel : </b>'.$lMail.'<br />
		  	____________________________________________<br />
		  	<br />
		  	<b><u>Article</u></b><br />
		  	<b>Titre : </b>'.$inTitle.'<br />
		  	<b>Catégorie : </b>'.$inCategory.'<br />
		  	<b>Signature : </b>'.$inSignature.'<br />
		  	<b>Permission de corriger : </b>'.($inRevisionApproved?'Oui':'Non').'<br />
		  	<b>Détails : </b>'.$inDetails.'<br />
		  	<br />
		  	Merci et bonne journée!<br />
		  	<br />
		  	<i>- Courriel automatique produit via la Base de données bélénoise</i>', 'text/html');

		// Define attachment
		if( $inFileAttached ) {
			$attachment = Swift_Attachment::fromPath( NEWS_UPLOAD_DIR . $this->GetUser()->GetID() . '/'. basename($_FILES['attachedfile']['name']) );
			$message->attach($attachment);
		}

		return $mailer->send($message);	
	}


} // END of RegistrationServices class

?>
