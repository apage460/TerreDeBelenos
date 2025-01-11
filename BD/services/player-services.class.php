<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Player Services v1.2 r10 ==				║
║	Additional services used for play.			║
║	Non-serializable. Requires DAL. Uses MySQL queries.	║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('services/user-services.class.php'); 	// User services
include_once('models/player.class.php'); 		// Player definition
include_once('models/group.class.php'); 		// Group definition
include_once('models/character.class.php');		// Character definition

class PlayerServices extends UserServices
{

	//--CONSTRUCTOR--
	public function __construct($inDataAccessLayer, $inPlayer =NULL)
	{
		parent::__construct($inDataAccessLayer, $inPlayer );
	}


	//--GET USER'S GAME INFORMATION--
	public function GetPlayerInfo()
	{
		$this->Error = "";

		//Verify there's a user
		if( !isset($this->User) ) { $this->Error = "GetPlayerInfo : No user!"; return False; }
		if( !($this->User instanceof Player) ) { $this->Error = "GetPlayerInfo : User is no player!"; return False; }

		//Get player data
		$this->GetNotes();
		$this->GetExperience();
		$this->GetCharacters();
		$this->GetManagedGroups();
		$this->GetActivities();
		$this->GetWarnings();
		$this->GetBlames();
		$this->GetPasses();
		$this->GetDebts();
		$this->GetFreeActivityVouchers();
	}


	//--GET PLAYER'S NOTES--
	public function GetNotes()
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "GetNotes : No user!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT remi.Id, remi.Message, remi.DateCreation
				 FROM db_indiv.remarques remi
				 WHERE remi.IdIndividu = :userid
				 ORDER BY remi.DateCreation DESC";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Build ans save result array
		$lNoteList = array();
		foreach($r as $i => $note) { 
			$lNoteList[$i]['id'] = $note['Id'];
			$lNoteList[$i]['message'] = $note['Message'];
			$lNoteList[$i]['date'] = $note['DateCreation'];
		}

		$this->User->SetNotes($lNoteList);
		return True;
	}


	//--GET PLAYER'S EXPERIENCE MOD LIST--
	public function GetExperience()
	{
		// Verify there's a user
		if( !isset($this->User) ) { $this->Error = "GetExperience : No user!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT exp.Id, exp.XP, exp.Raison, exp.DateInscription, exp.Commentaires
				 FROM db_indiv.experience exp 
				 WHERE exp.IdIndividu = :userid
				   AND exp.CodeUnivers = :universecode ;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		// Build ans save result array
		$lExperienceModList = array( 'id' => array(), 'xp' => array(), 'reason' => array(), 'date' => array(), 'comment' => array() );
		foreach($r as $i => $experience) { 
			$lExperienceModList['id'][$i] = $experience['Id'];
			$lExperienceModList['xp'][$i] = $experience['XP'];
			$lExperienceModList['reason'][$i] = $experience['Raison'];
			$lExperienceModList['date'][$i] = $experience['DateInscription'];
			$lExperienceModList['comment'][$i] = $experience['Commentaires'];
		}

		$this->User->SetExperience($lExperienceModList);
		return True;
	}


	//--GET PLAYER'S CHARACTERS--
	public function GetCharacters()
	{
		// Verify there's a user and that he has an ID
		if( !isset($this->User) ) { $this->Error = "GetCharacters : No user!"; return False; }


		// Ask the database...
		$lQuery = 	"SELECT per.Id, per.Prenom, per.Nom, per.CodeEtat, grp.Id AS IdGroupe, grp.Nom AS NomGroupe
				 FROM db_perso.personnages per 
					LEFT JOIN db_group.membres mbr ON per.Id = mbr.IdPersonnage
                			LEFT JOIN db_group.groupes grp ON mbr.IdGroupe = grp.Id
				 WHERE per.IdIndividu = :userid
				   AND per.CodeUnivers = :universecode
				   AND per.CodeEtat <> 'SUPPR'";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		// Build result array
		$lCharacterList = array();
		foreach($r as $character) { 
			$lCharacterGroup = new Group( ['id' => $character['IdGroupe'], 'name' => $character['NomGroupe']] );

			$attributes = [
				'id' 		=> $character['Id'],
				'firstname' 	=> $character['Prenom'],
				'lastname' 	=> $character['Nom'],
				'status' 	=> $character['CodeEtat'],
				'group'		=> $lCharacterGroup
			];
			$lCharacterList[] = new Character( $attributes );
		}

		$this->User->SetCharacters($lCharacterList);
		return True;
	}


	//--GET PLAYER'S MANAGED GROUPS--
	public function GetManagedGroups()
	{
		// Verify there's a user and that he has an ID
		if( !isset($this->User) ) { $this->Error = "GetManagedGroups : No user!"; return False; }


		// Ask the database...
		$lQuery = 	"SELECT repg.IdGroupe, grp.Nom
				 FROM db_group.responsables_groupe repg 
				 	JOIN db_group.groupes grp ON repg.IdGroupe = grp.Id
				 WHERE repg.IdResponsable = :userid
				   AND grp.CodeUnivers = :universecode
				 AND grp.CodeEtat <> 'SUPPR'";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		// Build result array
		$lGroupList = array();
		foreach($r as $group) { 
			$attributes = [
				'id' => $group['IdGroupe'],
				'name' => $group['Nom']
			];
			$lGroupList[] = new Group( $attributes );
		}

		$this->User->SetManagedGroups($lGroupList);
		return True;
	}


	//--GET PLAYER'S ATTENDANCE LIST--
	public function GetActivities()
	{
		// Verify there's a user
		if( !isset($this->User) ) { $this->Error = "GetActivities : No user!"; return False; }

		// Prepare attendance array
		$lActivityList = array( 'id' => array(), 'name' => array(), 'type' => array(), 'date' => array(), 'comment' => array() );


		// Ask the database for attendances post-dating it
		$lQuery = 	"SELECT act.Id, act.Nom, act.Type, pres.DateInscription, pres.Commentaires
				 FROM db_indiv.presences pres LEFT JOIN db_activ.activites act ON pres.IdActivite = act.Id
				 WHERE pres.IdIndividu = :userid
				   AND act.CodeUnivers = :universecode
				 ORDER BY pres.DateInscription DESC";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		// Build result array if there's any recent activity
		if($r) {
			foreach($r as $i => $activity) { 
				$lActivityList['id'][$i] = $activity['Id'];
				$lActivityList['name'][$i] = $activity['Nom'];
				$lActivityList['type'][$i] = $activity['Type'];
				$lActivityList['date'][$i] = $activity['DateInscription'];
				$lActivityList['comment'][$i] = $activity['Commentaires'];
			}
		}
		else {
			// *!* OLD DATABASE DATA ALERT! *!*
			// If there's no recent activity, find the most recent attendance in the old database.
			$lQuery = 	"SELECT evn.evn_nom, evn.evn_start
					 FROM db_beleweb.evenement evn
						JOIN db_beleweb.joueur_evenement jev ON evn.evn_uid = jev.evn_uid
						JOIN db_beleweb.joueur jou ON jev.jou_uid = jou.jou_uid
					 WHERE evn.evn_gn = 1
					   AND evn.evn_start >= 1356998400
					   AND (jou.jou_prenom = :firstname
					   	AND jou.jou_nom = :lastname
					   	AND jou.jou_anniversaire = :birthday)
					    OR jou.jou_email = :mail
					ORDER BY evn.evn_start DESC
					LIMIT 1;";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":firstname", $this->User->GetFirstName(), PDO::PARAM_STR);
				$this->DAL->Bind(":lastname", $this->User->GetLastName(), PDO::PARAM_STR);
				$this->DAL->Bind(":birthday", $this->User->GetBirthDate(), PDO::PARAM_STR);
				$this->DAL->Bind(":mail", $this->User->GetMailAddress(), PDO::PARAM_STR);
			$r = $this->DAL->FetchResult();

			if($r) {
				$lActivityList['id'][0] = 0;
				$lActivityList['name'][0] = $r[0]['evn_nom'];
				$lActivityList['type'][0] = 'OLDGN';
				$lActivityList['date'][0] = gmdate("Y-m-d H:i:s", $r[0]['evn_start']);
				$lActivityList['comment'][0] = "Annule le rabais de nouveau joueur.";
			}

		}

		$this->User->SetActivities($lActivityList);
		return True;
	}


	//--GET USER'S WARNINGS FROM THE ORGANISATION--
	public function GetWarnings()
	{
		// Verify there's a user
		if( !isset($this->User) ) { $this->Error = "GetWarnings : No user!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT avt.Id, avt.Raison, avt.DateInscription
				 FROM db_indiv.avertissements avt 
				 WHERE avt.Type = 'AVERT' 
				 AND avt.DateAnnulation IS NULL
				 AND avt.IdCible = :targetid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":targetid", $this->User->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Build and save result array
		$lWarningList = array();
		foreach($r as $i => $warning) { 
			$lWarningList[$i] = [
				'id' => $warning['Id'],
				'reason' => $warning['Raison'],
				'date' => $warning['DateInscription'],
			];
		}

		$this->User->SetWarnings($lWarningList);
		return True;
	}


	//--GET USER'S BLAMES FROM THE ORGANISATION--
	public function GetBlames()
	{
		// Verify there's a user
		if( !isset($this->User) ) { $this->Error = "GetBlames : No user!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT avt.Id, avt.Raison, avt.DateInscription
				 FROM db_indiv.avertissements avt 
				 WHERE avt.Type = 'BLAME' 
				 AND avt.DateAnnulation IS NULL
				 AND avt.IdCible = :targetid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":targetid", $this->User->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Build and save result array
		$lBlameList = array();
		foreach($r as $i => $blame) { 
			$lBlameList[$i] = [
				'id' => $blame['Id'],
				'reason' => $blame['Raison'],
				'date' => $blame['DateInscription'],
			];
		}

		$this->User->SetBlames($lBlameList);
		return True;
	}


	//--GET USER'S SPECIAL PASSES--
	public function GetPasses()
	{
		// Verify there's a user
		if( !isset($this->User) ) { $this->Error = "GetPasses : No user!"; return False; }

		$lQuery = 	"SELECT pac.Id, pac.IdPasse, pass.Nom, pass.Description, pac.Commentaires, pass.DateDebut, pass.DateFin
				 FROM db_indiv.passes_acquises pac 
				 	LEFT JOIN db_activ.passes pass ON pac.IdPasse = pass.Id
				 WHERE pac.IdIndividu = :userid
				 AND pac.CodeEtat = 'ACTIF'
				 ORDER BY pac.Id ASC";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Build and save result array
		$lPassList = array();
		foreach($r as $i => $pass) { 
			$lPassList[$i] = [
				'id' => $pass['Id'],
				'passid' => $pass['IdPasse'],
				'name' => $pass['Nom'],
				'description' => $pass['Description'],
				'comment' => $pass['Commentaires'],
				'startingdate' => $pass['DateDebut'],
				'endingdate' => $pass['DateFin'],
			];
		}

		$this->User->SetPasses($lPassList);
		return True;
	}


	//--GET PLAYER'S DEBTS AND CREDITS LIST--
	public function GetDebts()
	{
		// Verify there's a user
		if( !isset($this->User) ) { $this->Error = "GetDebts : No user!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT somd.Id, somd.Montant, somd.Raison, somd.DateInscription, somd.Commentaires
				 FROM db_indiv.sommes_dues somd 
				 WHERE somd.IdIndividu = :userid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Build ans save result array
		$lDebtList = array( 'id' => array(), 'amount' => array(), 'reason' => array(), 'date' => array(), 'comment' => array() );
		foreach($r as $i => $item) { 
			$lDebtList['id'][$i] = $item['Id'];
			$lDebtList['amount'][$i] = $item['Montant'];
			$lDebtList['reason'][$i] = $item['Raison'];
			$lDebtList['date'][$i] = $item['DateInscription'];
			$lDebtList['comment'][$i] = $item['Commentaires'];
		}

		$this->User->SetDebts($lDebtList);
		return True;
	}


	//--GET PLAYER'S FREE ACTIVITY VOUCHERS--
	public function GetFreeActivityVouchers()
	{
		// Verify there's a user
		if( !isset($this->User) ) { $this->Error = "GetFreeActivityVouchers : No user!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT ind.ActivitesGratuites, ind.EnfantsGratuits
				 FROM db_indiv.individus ind 
				 WHERE ind.Id = :userid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Build ans save result array
		$this->User->SetFreeActivityVouchers($r[0]['ActivitesGratuites']);
		$this->User->SetFreeKidVouchers($r[0]['EnfantsGratuits']);
		return True;
	}


	//--REGISTER A KID TO THE TUTORING GROUP--
	public function RegisterTutoringGroupMember()
	{
		// Verify there's a user
		if( !isset($this->User) ) { $this->Error = "RegisterTutoringGroupMember : No user!"; return False; }

		
		// Insert.
		$lQuery = 	"UPDATE db_indiv.individus ind
				 SET ind.Tuteur = 'GroupeCadre' 
				 WHERE ind.Id = :userid;

				 INSERT INTO db_indiv.remarques (IdIndividu, Message, Type, DateCreation)
				 VALUES ( :userid, :message, 'INFO', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);

			$this->DAL->Bind(":message", 'Inscription au groupe cadre', PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		if($r) { $this->User->SetTutor( 'GroupeCadre' ); }

		return $r;
	}


	//--GET PLAYER'S FREE ACTIVITY VOUCHERS--
	public function AddFreeActivityVouchers()
	{
		// Verify there's a user
		if( !isset($this->User) ) { $this->Error = "AddFreeActivityVouchers : No user!"; return False; }

		// Prepare
		$lVouchers = $this->User->GetFreeActivityVouchers();

		// Ask the database...
		$lQuery = 	"UPDATE db_indiv.individus ind
				 SET ind.ActivitesGratuites = ind.ActivitesGratuites + 1
				 WHERE ind.Id = :userid ;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Save result
		$this->User->SetFreeActivityVouchers($lVouchers + 1);

		return True;
	}


	//--GET PLAYER'S FREE KID VOUCHERS--
	public function AddFreeKidVouchers( $inKidVouchers)
	{
		// Verify there's a user
		if( !isset($this->User) ) { $this->Error = "AddFreeKidVouchers : No user!"; return False; }

		// Prepare
		$lVouchers = $this->User->GetFreeKidVouchers();

		// Ask the database...
		$lQuery = 	"UPDATE db_indiv.individus ind
				 SET ind.EnfantsGratuits = ind.EnfantsGratuits + :kidvouchers
				 WHERE ind.Id = :userid ;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":kidvouchers", $inKidVouchers, PDO::PARAM_INT);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Save result
		$this->User->SetFreeKidVouchers($lVouchers + $inKidVouchers);

		return True;
	}


	//--GET PLAYER'S FREE ACTIVITY VOUCHERS--
	public function TakeVolunteeringPoints( $inReason, $inCost )
	{
		// Verify there's a user
		if( !isset($this->User) ) { $this->Error = "TakeVolunteeringPoints : No user!"; return False; }

		// Prepare
		$lPoints = $this->User->GetVolunteeringPoints();

		// Ask the database...
		$lQuery = 	"INSERT INTO db_indiv.benevolat (IdIndividu, Raison, Points)
				 VALUES (:userid, :reason, :cost);";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":reason", $inReason, PDO::PARAM_STR);
			$this->DAL->Bind(":cost", -$inCost, PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();

		// Save result
		$lPoints[] = ['id' => null, 'reason' => $inReason, 'points' => -$inCost];
		$this->User->SetVolunteeringPoints($lPoints);

		return True;
	}


} // END of PlayerServices class

?>
