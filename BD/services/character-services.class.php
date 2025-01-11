<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Character Services v1.2 r22 ==			║
║	Services used to get and alter characters' data.	║
║	Non-serializable. Requires DAL. Uses MySQL queries.	║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('services/database.class.php'); 		// Data Access Layer
include_once('models/user.class.php'); 			// User definition
include_once('models/character.class.php'); 		// Character definition
include_once('models/group.class.php'); 		// Group definition
include_once('models/quest.class.php'); 		// Quest definition
include_once('models/resume.class.php'); 		// Resumé definition
include_once('models/letter.class.php'); 		// Letter definition
include_once('models/approval.class.php');		// Approval definition
include_once('models/activity.class.php');		// Activity definition
include_once('models/survey.class.php');		// Survey definition

class CharacterServices
{

protected $DAL;
protected $User;
protected $Character;

public $Error;

	//--CONSTRUCTOR--
	public function __construct($inDataAccessLayer, $inUser =NULL, $inCharacter =NULL)
	{
		$this->DAL = $inDataAccessLayer;

		if( isset($inUser) ) {$this->User = $inUser;}
		if( isset($inCharacter) ) {$this->Character = $inCharacter;}
	}


	//--GET/SET FUNCTIONS--
	public function GetUser() { return $this->User; }
	public function GetCharacter() { return $this->Character; }

	public function SetUser($inUser) { $this->User = $inUser; }
	public function SetCharacter($inCharacter) { $this->Character = $inCharacter; }

//=================================================================== DATA RECOVERY SERVICES ===================================================================

	//--GET USER'S CURRENT GROUP--
	public function GetCharacterInfo()
	{
		$this->Error = "";

		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		//Get character data
		$this->GetBaseInformation();
		$this->GetNotes();
		$this->GetGroupAndInvitations();
		$this->GetExperience();
		$this->GetQuestCredits();
		$this->GetLife();
		$this->GetSkills();
		$this->GetTalents();
		$this->GetTeachings();
		$this->GetSyllabuses();
		$this->GetTitles();
		$this->GetQuests();
		$this->GetResumes();
		$this->GetLetters();
		$this->GetApprovals();
		$this->GetUserRecentActivities();
		$this->GetCharacterAttendances();
		$this->GetAnsweredSurveys();
	}


	//--GET CHARACTER'S BASE INFORMATIONS--
	public function GetBaseInformation()
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT per.Prenom, per.Nom, per.CodeRace, rac.Nom AS Race, per.CodeClasse, clas.Nom AS Classe, per.CodeArchetype, arch.Nom AS Archetype, 
					per.Niveau, per.CodeReligion, rel.Nom AS Religion, per.Provenance, per.Histoire, per.NoteRapide, per.CodeEtat
				 FROM db_perso.personnages per 
					LEFT JOIN db_pilot.races rac ON per.CodeRace = rac.Code
					LEFT JOIN db_pilot.classes clas ON per.CodeClasse = clas.Code
					LEFT JOIN db_pilot.archetypes arch ON per.CodeArchetype = arch.Code
					LEFT JOIN db_pilot.religions rel ON per.CodeReligion = rel.Code
				 WHERE per.Id = :characterid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Save result
		if($r) {
			$this->Character->SetFirstName($r[0]['Prenom']);
			$this->Character->SetLastName($r[0]['Nom']);
			$this->Character->SetRace($r[0]['Race']);
			$this->Character->SetRaceCode($r[0]['CodeRace']);
			$this->Character->SetClass($r[0]['Classe']);
			$this->Character->SetClassCode($r[0]['CodeClasse']);
			$this->Character->SetArchetype($r[0]['Archetype']);
			$this->Character->SetArchetypeCode($r[0]['CodeArchetype']);
			$this->Character->SetLevel($r[0]['Niveau']);
			$this->Character->SetReligion($r[0]['Religion']);
			$this->Character->SetReligionCode($r[0]['CodeReligion']);

			$this->Character->SetOrigin($r[0]['Provenance']);
			$this->Character->SetBackground($r[0]['Histoire']);
			$this->Character->SetQuickNote($r[0]['NoteRapide']);

			$this->Character->SetStatus($r[0]['CodeEtat']);
		}
		else { return False;}

		return True;
	}


	//--GET CHARACTER'S NOTES--
	public function GetNotes()
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT remp.Id, remp.Message, remp.DateCreation
				 FROM db_perso.remarques remp
				 WHERE remp.IdPersonnage = :characterid
				 ORDER BY remp.DateCreation DESC";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Build and save result array
		$lNoteList = array();
		foreach($r as $i => $note) { 
			$lNoteList[$i]['id'] = $note['Id'];
			$lNoteList[$i]['message'] = $note['Message'];
			$lNoteList[$i]['date'] = $note['DateCreation'];
		}

		$this->Character->SetNotes($lNoteList);
		return True;
	}


	//--GET CHARACTER'S GROUP AND INVITATIONS--
	public function GetGroupAndInvitations()
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database for group...
		$lQuery = 	"SELECT grp.Id, grp.Nom
				 FROM db_group.groupes grp
				 LEFT JOIN db_group.membres mbr ON mbr.IdGroupe = grp.Id
				 WHERE mbr.IdPersonnage = :characterid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Build and save group
		$lGroup = null;
		if($r) { 
			$attributes = [
				'id'	=>	$r[0]['Id'],
				'name'	=>	$r[0]['Nom']
			];

			$lGroup = new Group($attributes);
		}

		$this->Character->SetGroup($lGroup);


		// Ask the database for invitations if the character has no group... don't waste time otherwise.
		if(!$lGroup) {
			$lQuery = 	"SELECT grp.Id, grp.Nom
					 FROM db_perso.invitations inv
					 LEFT JOIN db_group.groupes grp ON inv.IdGroupe = grp.Id
					 WHERE inv.IdPersonnage = :characterid
					 ORDER BY inv.DateInvitation ASC";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
			$r = $this->DAL->FetchResult();

			// Build and save invitation list
			$lInvitationList = array();
			foreach( $r as $invitation ) { 
				$attributes = [
					'id'	=>	$invitation['Id'],
					'name'	=>	$invitation['Nom']
				];

				$lInvitationList[] = new Group($attributes);
			}

			$this->Character->SetInvitations($lInvitationList);			
		}

		return True;
	}


	//--GET CHARACTER'S EXPERIENCE MOD LIST--
	public function GetExperience()
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT expp.Id, expp.XP, expp.Raison, expp.DateInscription, expp.Commentaires
				 FROM db_perso.experience expp 
				 WHERE expp.IdPersonnage = :characterid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Build and save result array
		$lExperienceModList = array();
		foreach($r as $i => $experience) { 
			$lExperienceModList[] = [
				'id'		=> 	$experience['Id'],
				'xp'		=> 	$experience['XP'],
				'reason'	=> 	$experience['Raison'],
				'date'		=> 	$experience['DateInscription'],
				'comment'	=> 	$experience['Commentaires']
			];
		}

		$this->Character->SetExperience($lExperienceModList);

		// Calculate transfered amount for this year
		$lTransferedXP = $this->GetTransferedExperience();
		$this->Character->SetTransferedExperience($lTransferedXP);

		return True;
	}


	//--TRANSFER XP FROM CURRENT PLAYER TO CURRENT CHARACTER--
	public function GetTransferedExperience()
	{
		// Verify there's a user and a character
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }


		// Ask the database...
		$lQuery =	"SELECT SUM(expp.XP) AS XPTransferes
				 FROM db_perso.experience expp
				 WHERE expp.IdPersonnage = :characterid
				   AND expp.Raison = 'Transfert'
				   AND expp.DateInscription >= :yearstart
				   AND expp.DateInscription <= :yearend;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":yearstart", date("Y").'-01-01', PDO::PARAM_STR);
			$this->DAL->Bind(":yearend", date("Y").'-12-31', PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		$total = 0;
		if($r) { $total = $r[0]['XPTransferes']; }

		return $total;
	}


	//--GET CHARACTER'S QUEST CREDIT LIST--
	public function GetQuestCredits()
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// UNIVERSAL CREDITS
		// Ask the database...
		$lQuery = 	"SELECT per.CreditsQuete
				 FROM db_perso.personnages per 
				 WHERE per.Id = :characterid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Build and save result array
		$lCredits = 0; if( isset($r[0]) ) { $lCredits = $r[0]['CreditsQuete']; }

		$this->Character->SetQuestCredits('UNIV', $lCredits);

		// SPECIFIC CREDITS
		// Ask the database...
		$lQuery = 	"SELECT cq.CodeOption, cq.CodeRecompense, cq.Credits
				 FROM db_perso.credits_quete cq 
				 WHERE cq.IdPersonnage = :characterid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Build and save result array
		foreach( $r as $rewardline) {
			if( !$rewardline['CodeRecompense'] ) { $this->Character->SetQuestCredits($rewardline['CodeOption'] , $rewardline['Credits']); }
			else { $this->Character->SetQuestCredits($rewardline['CodeRecompense'] , $rewardline['Credits']); }
		}

		return True;
	}


	//--GET CHARACTER'S LIFE MOD LIST--
	public function GetLife()
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT pv.Id, pv.PV, pv.Raison, pv.DateInscription, pv.Commentaires
				 FROM db_perso.points_de_vie pv
				 WHERE pv.IdPersonnage = :characterid
				 ORDER BY pv.DateInscription ASC";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Build and save result array
		$lLifeModList = array();
		foreach($r as $i => $lifemod) { 
			$lLifeModList[] = [
				'id'		=> 	$lifemod['Id'],
				'life'		=> 	$lifemod['PV'],
				'reason'	=> 	$lifemod['Raison'],
				'date'		=>	$lifemod['DateInscription'],
				'comment'	=>	$lifemod['Commentaires']
			];
		}

		$this->Character->SetLife($lLifeModList);
		return True;
	}


	//--GET CHARACTER'S SKILLS--
	public function GetSkills()
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT cac.Id, cac.CodeCompetence, creg.Nom, creg.Niveau, cac.Type, cac.Usages, cac.CoutXP, cac.CodeAcquisition, cac.CodeEtat, 
					creg.IndPrecision, cac.Precision, creg.CodeCompPrerequise AS 'Prerequis'
				 FROM db_perso.competences_acquises cac JOIN db_pilot.competences_regulieres creg 
					ON cac.CodeCompetence = creg.Code
				 WHERE cac.IdPersonnage = :characterid
				   AND cac.CodeEtat NOT IN ('INACT', 'SUPPR')
				   AND cac.Type = 'REG'
				 ORDER BY creg.Nom ASC, cac.Precision ASC";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();

		// Build and save result array
		$lSkillList = array();

		foreach($r as $skill) { 
			$lSkillList[] = [
				'id' 		=> 	$skill['Id'],
				'code'		=> 	$skill['CodeCompetence'],
				'name'		=> 	$skill['Nom'],
				'level'		=> 	$skill['Niveau'],
				'type'		=> 	$skill['Type'],
				'quantity'	=> 	$skill['Usages'],
				'xpcost'	=> 	$skill['CoutXP'],
				'acquisition'	=> 	$skill['CodeAcquisition'],
				'status'	=> 	$skill['CodeEtat'],
				'precisable'	=> 	$skill['IndPrecision'],
				'precision' 	=> 	$skill['Precision'],
				'prerequisites'	=> 	explode( ";", $skill['Prerequis'] )
			];
		}

		$this->Character->SetSkills($lSkillList);
		return True;
	}


	//--GET CHARACTER'S TALENTS--
	public function GetTalents()
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT cac.Id, cac.CodeCompetence, cspec.Nom, cspec.Type, cac.Usages, cac.CodeEtat
				 FROM db_perso.competences_acquises cac JOIN db_pilot.competences_speciales cspec 
					ON cac.CodeCompetence = cspec.Code
				 WHERE cac.IdPersonnage = :characterid
				   AND cac.CodeEtat <> 'INACT'
				   AND cac.Type <> 'REG'";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Build and save result array
		$lTalentList = array();

		foreach($r as $talent) { 
			$lTalentList[] = [
				'id' 		=> 	$talent['Id'],
				'code'		=> 	$talent['CodeCompetence'],
				'name'		=> 	$talent['Nom'],
				'type' 		=> 	$talent['Type'],
				'quantity' 	=> 	$talent['Usages'],
				'status'	=> 	$talent['CodeEtat']
			];
		}

		$this->Character->SetTalents($lTalentList);
		return True;
	}


	//--GET CHARACTER'S TEACHINGS--
	public function GetTeachings()
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT ens.Id, ens.IdMaitre, CONCAT(maitre.Prenom, ' ', maitre.Nom) AS NomMaitre, 
					ens.IdEtudiant, CONCAT(eleve.Prenom, ' ', eleve.Nom) AS NomEtudiant, 
					ens.CodeCompetence, creg.Nom AS NomCompetence, 
					ens.IdActivite, act.Nom AS NomActivite, ens.Moment, ens.Lieu, ens.CodeEtat,
					ens.ValeurXP
				 FROM db_perso.enseignements ens
				 	JOIN db_perso.personnages maitre ON ens.IdMaitre = maitre.Id
				 	JOIN db_perso.personnages eleve ON ens.IdEtudiant = eleve.Id
				 	JOIN db_pilot.competences_regulieres creg ON ens.CodeCompetence = creg.Code
				 	JOIN db_activ.activites act ON ens.IdActivite = act.Id
				 WHERE ens.IdMaitre = :characterid
				   OR ens.IdEtudiant = :characterid
				 ORDER BY ens.DateCreation DESC";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Build and save result array
		$lTeachingList = array();
		foreach($r as $i => $teaching) { 
			$lTeachingList[] = [
				'id' 		=> 	$teaching['Id'],
				'masterid' 	=> 	$teaching['IdMaitre'],
				'mastername' 	=> 	$teaching['NomMaitre'],
				'studentid' 	=> 	$teaching['IdEtudiant'],
				'studentname' 	=> 	$teaching['NomEtudiant'],
				'skillcode'	=> 	$teaching['CodeCompetence'],
				'skillname'	=> 	$teaching['NomCompetence'],
				'activityid'	=> 	$teaching['IdActivite'],
				'activityname' 	=> 	$teaching['NomActivite'],
				'moment' 	=> 	$teaching['Moment'],
				'place' 	=> 	$teaching['Lieu'],
				'status' 	=> 	$teaching['CodeEtat'],
				'xpvalue'	=> 	$teaching['ValeurXP']
			];
		}

		$this->Character->SetTeachings($lTeachingList);
		return True;
	}


	//--GET CHARACTER'S TEACHINGS--
	public function GetSyllabuses()
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT mai.Id, mai.CodeCompetence, creg.Nom AS NomCompetence, mai.CodeEtat, mai.Raison
				 FROM db_perso.maitres mai
				 	JOIN db_pilot.competences_regulieres creg ON mai.CodeCompetence = creg.Code
				 WHERE mai.IdPersonnage = :characterid
				 ORDER BY creg.Nom DESC";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Build and save result array
		$lSyllabusList = array();
		foreach($r as $i => $syllabus) { 
			$lSyllabusList[] = [
				'id' 		=> 	$syllabus['Id'],
				'skillcode'	=> 	$syllabus['CodeCompetence'],
				'skillname'	=> 	$syllabus['NomCompetence'],
				'status' 	=> 	$syllabus['CodeEtat'],
				'reason'	=>	$syllabus['Raison']
			];
		}

		$this->Character->SetSyllabuses($lSyllabusList);
		return True;
	}


	//--GET CHARACTER'S TITLES--
	public function GetTitles()
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT titr.Id, titr.Titre, titr.Description, titr.Avantages, roy.Nom AS Royaume
				 FROM db_perso.titres titr
				 	LEFT JOIN db_histo.royaumes roy ON roy.Code = titr.CodeRoyaume
				 WHERE titr.IdPersonnage = :characterid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Build and save result array
		$lTitleList = array();
		foreach($r as $title) { 
			$lTitleList[] = [
				'id' 		=> 	$title['Id'],
				'title'		=> 	$title['Titre'],
				'description' 	=> 	$title['Description'],
				'bonus'		=> 	$title['Avantages'],
				'kingdom'	=> 	$title['Royaume']
			];
		}

		$this->Character->SetTitles($lTitleList);
		return True;
	}


	//--GET CHARACTER'S QUESTS--
	public function GetQuests()
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT que.Id, que.IdPersonnage,
					que.Objet, que.CodeOption, que.CodeRecompense, que.Suggestions, que.Fichier, que.CodeEtat, que.DateDemande, que.DateApprobation, que.Commentaires,
					que.IdComte, cmt.Nom AS NomComte, duc.CodeRoyaume, roy.Nom AS NomRoyaume,
					que.IdActivite, act.Nom AS NomActivite, act.DateDebut,
					que.IdResponsable, rev.Prenom AS PrenomResponsable, rev.Nom AS NomResponsable, 
					que.IdScripteur, scr.Prenom AS PrenomScripteur, scr.Nom AS NomScripteur
				 FROM db_perso.quetes que 
				 	LEFT JOIN db_histo.comtes cmt ON que.IdComte = cmt.Id
				 	LEFT JOIN db_histo.duches duc ON cmt.CodeDuche = duc.Code
				 	LEFT JOIN db_histo.royaumes roy ON duc.CodeRoyaume = roy.Code
				 	LEFT JOIN db_activ.activites act ON que.IdActivite = act.Id
				 	LEFT JOIN db_perso.personnages per ON que.IdPersonnage = per.Id
				 	LEFT JOIN db_indiv.individus rev ON que.IdResponsable = rev.Id
				 	LEFT JOIN db_indiv.individus scr ON que.IdScripteur = scr.Id
				 WHERE que.IdPersonnage = :characterid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Build and save result array
		$lQuestList = array();
		foreach($r as $i => $quest) { 
			$lActivity = new Activity( ['id' => $quest['IdActivite'], 'name' => $quest['NomActivite'], 'startingdate' => $quest['DateDebut'] ] );
			$lRevisor = new User( array('id' => $quest['IdResponsable'], 'firstname' => $quest['PrenomResponsable'], 'lastname' => $quest['NomResponsable']) );
			$lScriptor = new User( array('id' => $quest['IdScripteur'], 'firstname' => $quest['PrenomScripteur'], 'lastname' => $quest['NomScripteur']) );
			$attributes = [
					'id'			=>	$quest['Id'],
					'status'		=>	$quest['CodeEtat'],
					'subject'		=>	$quest['Objet'],
					'optioncode'		=>	$quest['CodeOption'],
					'rewardcode'		=>	$quest['CodeRecompense'],
					'suggestions'		=>	$quest['Suggestions'],
					'countyid'		=>	$quest['IdComte'],
					'countyname'		=>	$quest['NomComte'],
					'kingdomcode'		=>	$quest['CodeRoyaume'],
					'kingdomname'		=>	$quest['NomRoyaume'],
					'requestdate'		=>	$quest['DateDemande'],
					'activity'		=>	$lActivity,
					'revisor'		=>	$lRevisor,
					'approvaldate'		=>	$quest['DateApprobation'],
					'scriptor'		=>	$lScriptor,
					'file'			=>	$quest['Fichier'],
					'comments'		=>	$quest['Commentaires']
			];

			$lQuestList[$i] = new Quest( $attributes );
		}

		$this->Character->SetQuests($lQuestList);
		return True;
	}


	//--GET CHARACTER'S RESUMES--
	public function GetResumes()
	{
		// Verify there's a user and a character
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT resu.Id, resu.IdActivite, act.Nom, resu.Resume, resu.IdQuete, que.Objet, resu.DateCreation
				 FROM db_perso.resumes resu
				 	JOIN db_activ.activites act ON resu.IdActivite = act.Id
				 	LEFT JOIN db_perso.quetes que ON resu.IdQuete = que.Id
				 WHERE resu.IdPersonnage = :characterid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Build and save result array
		$lResumeList = array();
		foreach($r as $i => $resume) { 
			$lActivity = new Activity( ['id' => $resume['IdActivite'], 'name' => $resume['Nom']] );
			$lQuest = new Quest( ['id' => $resume['IdQuete'], 'subject' =>$resume['Objet']] );
			$attributes = [
				'id'		=> 	$resume['Id'],
				'activity'	=> 	$lActivity,
				'quest'		=> 	$lQuest,
				'creationdate'	=> 	$resume['DateCreation'],
				'text'		=>	$resume['Resume']
			];

			$lResumeList[$i] = new Resume( $attributes );
		}

		$this->Character->SetResumes($lResumeList);
		return True;
	}


	//--GET CHARACTER'S LETTERS--
	public function GetLetters()
	{
		// Verify there's a user and a character
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT mis.Id, mis.IdEcrivain, CONCAT(ecr.Prenom, ' ', ecr.Nom) AS NomEcrivain, mis.IdDestinataire, CONCAT(des.Prenom, ' ', des.Nom) AS NomDestinataire, mis.Type, mis.Objet, mis.Corps, mis.CodeEtat, mis.DateEnvoi, mis.ReponseA
				 FROM db_perso.missives mis
				 LEFT JOIN db_perso.personnages ecr ON mis.IdEcrivain = ecr.Id
				 LEFT JOIN db_perso.personnages des ON mis.IdDestinataire = des.Id
				 WHERE mis.IdEcrivain = :characterid
				    OR mis.IdDestinataire = :characterid
				 ORDER BY mis.DateEnvoi ASC;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Build and save result array
		$lLetterList = array();
		foreach($r as $i => $letter) { 
			$attributes = [
				'id'		=> 	$letter['Id'],
				'senderid'	=> 	$letter['IdEcrivain'],
				'sendername'	=> 	$letter['NomEcrivain'],
				'recipientid'	=> 	$letter['IdDestinataire'],
				'recipientname'	=> 	$letter['NomDestinataire'],
				'type'		=> 	$letter['Type'],
				'subject'	=> 	$letter['Objet'],
				'body'		=> 	$letter['Corps'],
				'status'	=> 	$letter['CodeEtat'],
				'datesent'	=> 	$letter['DateEnvoi'],
				'originalpost'	=>	$letter['ReponseA']
			];

			$lLetterList[$i] = new Letter( $attributes );
		}

		$this->Character->SetLetters($lLetterList);
		return True;
	}


	//--GET CHARACTER'S APPROVAL--
	public function GetApprovals()
	{
		// Verify there's a user and a character
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT apro.Id, apro.IdPersonnage, apro.Objet, apro.CodeEtat, apro.DateDemande, apro.DateApprobation, apro.Commentaires
				 FROM db_perso.approbations apro
				 WHERE apro.IdPersonnage = :characterid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Build and save result array
		$lApprovalList = array();
		foreach($r as $i => $approval) { 
			$attributes = [
				'id'		=> 	$approval['Id'],
				'subject'	=> 	$approval['Objet'],
				'status'	=> 	$approval['CodeEtat'],
				'requestdate'	=> 	$approval['DateDemande'],
				'approvaldate'	=> 	$approval['DateApprobation'],
				'comments'	=>	$approval['Commentaires']
			];

			$lApprovalList[] = new Approval( $attributes );
		}

		$this->Character->SetApprovals($lApprovalList);
		return True;
	}


	//--GET USER'S MOST RECENT SUBCRIPTIONS FOR A GIVEN CHARACTER --
	public function GetUserRecentActivities()
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT act.Id, act.Nom, act.Type
				 FROM db_activ.activites act
				 	JOIN db_indiv.presences pres ON act.Id = pres.IdActivite
					JOIN db_indiv.individus ind ON pres.IdIndividu = ind.Id
					JOIN db_perso.personnages per ON ind.Id = per.IdIndividu
 				 WHERE per.Id = :characterid
 				   AND act.Type = 'GN'
 				   AND act.CodeUnivers = :universecode
                		 ORDER BY act.DateDebut DESC
                		 LIMIT 5;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		// Build and save result array
		$lActivityList = array();
		foreach($r as $i => $activity) { 
			$attributes = [
				'id'		=> 	$activity['Id'],
				'name'		=> 	$activity['Nom'],
				'type'		=>	$activity['Type']
			];

			$lActivityList[$i] = new Activity( $attributes );
		}

		$this->Character->SetUserRecentActivities($lActivityList);
		return True;
	}


	//--GET USER'S ATTENDANCES FOR A GIVEN CHARACTER --
	public function GetCharacterAttendances()
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT act.Id, act.Nom, act.Type
				 FROM db_activ.activites act
					JOIN db_activ.inscriptions insc ON act.Id = insc.IdActivite AND insc.IdPersonnage = :characterid
					JOIN db_indiv.presences pres ON act.Id = pres.IdActivite AND pres.IdIndividu = insc.IdIndividu
				 WHERE act.Type = 'GN'
 				   AND act.CodeUnivers = :universecode
				 ORDER By act.DateDebut DESC;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		// Build and save result array
		$lActivityList = array();
		foreach($r as $i => $activity) { 
			$attributes = [
				'id'		=> 	$activity['Id'],
				'name'		=> 	$activity['Nom'],
				'type'		=>	$activity['Type']
			];

			$lActivityList[$i] = new Activity( $attributes );
		}

		$this->Character->SetCharacterAttendances($lActivityList);
		return True;
	}


	//--GET CHARACTER'S ANSWERED SURVEY--
	public function GetAnsweredSurveys()
	{
		// Verify there's a user and a character
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT qtnr.Code, qtnr.Nom, qtnr.Consignes
				 FROM db_pilot.questionnaires qtnr
				 WHERE qtnr.TypeCible = 'PERSO'
				   AND qtnr.CodeEtat = 'ACTIF'
				   AND qtnr.Code IN (SELECT DISTINCT repq.CodeQuestionnaire 
						     FROM db_perso.reponses_questionnaire repq
						     WHERE IdPersonnage = :characterid);";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Build Survey List
		$lSurveyList = array();
		foreach($r as $i => $survey) { 
			$attributes = [
				'code'		=> 	$survey['Code'],
				'name'		=> 	$survey['Nom'],
				'instructions'	=>	$survey['Consignes'],
				'type'		=>	"PERSO",
				'status'	=>	"ACTIF"
			];

			$lSurvey = new Survey($attributes);
			$lSurvey->SetQuestions( $this->GetSurveyQuestions($survey['Code']) );
			$lSurvey->SetAnswers( $this->GetSurveyAnswers($survey['Code']) );
			
			$lSurveyList[$i] = $lSurvey;
		}

		$this->Character->SetAnsweredSurveys($lSurveyList);
		return True;
	}


	//--CHECK IF CHARACTER'S GOT A PENDING SURVEY--
	public function GetPendingSurvey()
	{
		// Verify there's a user and a character
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }


		// First survey comes at level 4...
		if( $this->Character->GetLevel() < 4 ) { return False; }


		// Ask the database...
		$lQuery = 	"SELECT qtnr.Code, qtnr.Nom, qtnr.Consignes
				 FROM db_pilot.questionnaires qtnr
				 WHERE qtnr.TypeCible = 'PERSO'
				   AND qtnr.CodeEtat = 'ACTIF'
				   AND qtnr.Code NOT IN (SELECT DISTINCT repq.CodeQuestionnaire 
							 FROM db_perso.reponses_questionnaire repq
							 WHERE IdPersonnage = :characterid)
				 LIMIT 1;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Build Survey
		if($r) { 
			$attributes = [
				'code'		=> 	$r[0]['Code'],
				'name'		=> 	$r[0]['Nom'],
				'instructions'	=>	$r[0]['Consignes'],
				'type'		=>	"PERSO",
				'status'	=>	"ACTIF"
			];

			$lSurvey = new Survey($attributes);
			$lSurvey->SetQuestions( $this->GetSurveyQuestions($r[0]['Code']) );
			
			$this->Character->SetPendingSurvey($lSurvey);

			return True;
		}

		return False;
	}


	//--GET A SPECIFIC SURVEY'S QUESTION LIST--
	public function GetSurveyQuestions($inCode)
	{
		// Ask the database...
		$lQuery = 	"SELECT quetn.Numero, quetn.SousTitre, quetn.Question, quetn.TypeReponse, quetn.ListeChoix
				 FROM db_pilot.questions quetn
				 WHERE CodeQuestionnaire = :surveycode;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":surveycode", $inCode, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		// Build and return question list
		$lQuestionList = array();		
		foreach ($r as $question) {
			$lQuestionList[] = [
					'number'	=> 	$question['Numero'],
					'subtitle'	=> 	$question['SousTitre'],
					'question'	=>	$question['Question'],
					'type'		=>	$question['TypeReponse'],
					'choices'	=>	$question['ListeChoix']
					];
		}

		return $lQuestionList;
	}


	//--GET A SPECIFIC SURVEY'S ANSWERS FOR THE CURRENT CHARACTER--
	public function GetSurveyAnswers($inCode)
	{
		// Ask the database...
		$lQuery = 	"SELECT repq.Id, repq.NumeroQuestion, repq.Reponse
				 FROM db_perso.reponses_questionnaire repq
				 WHERE IdPersonnage = :characterid
				   AND CodeQuestionnaire = :surveycode
				 ORDER BY NumeroQuestion ASC;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":surveycode", $inCode, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		// Build and return question list
		$lAnswerList = array();		
		foreach ($r as $answer) {
			$number = $answer['NumeroQuestion'];

			$lAnswerList[$number] = [
						'id'		=> 	$answer['Id'],
						'number'	=> 	$number,
						'answer'	=>	$answer['Reponse']
						];
		}

		return $lAnswerList;
	}

//=================================================================== MANAGEMENT SERVICES ===================================================================

	//--REGISTER CHARACTER'S SKILL PRECISION--
	public function UpdateSkillPrecision( $inSkillID, $inSkillPrecision )
	{
		// Ask the database...
		$lQuery = 	"UPDATE db_perso.competences_acquises cac
				 SET cac.Precision = :skillprecision
				 WHERE cac.Id = :skillid 
				   AND cac.IdPersonnage = :characterid;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":skillprecision", $inSkillPrecision, PDO::PARAM_STR);
			$this->DAL->Bind(":skillid", $inSkillID, PDO::PARAM_INT);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--CHECK IF TEACHING EXISTS--
	public function TeachingExists( $inMasterID, $inStudentID, $inSkillCode )
	{
		// Ask the database...
		$lQuery = 	"SELECT ens.Id 
				 FROM db_perso.enseignements ens
				 WHERE ens.IdMaitre = :masterid
				   AND ens.IdEtudiant = :studentid
				   AND ens.CodeCompetence = :skillcode;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":masterid", $inMasterID, PDO::PARAM_INT);
			$this->DAL->Bind(":studentid", $inStudentID, PDO::PARAM_INT);
			$this->DAL->Bind(":skillcode", $inSkillCode, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		// Return True if the teaching exists
		if ($this->DAL->GetRowCount() > 0) { return True; }
		return False;
	}


	//--REGISTER CHARACTER'S TEACHING--
	public function AddTeaching( $inMasterID, $inStudentID, $inSkillCode, $inActivityID, $inPlace, $inMoment )
	{
		// Ask the database...
		$lQuery = 	"INSERT INTO db_perso.enseignements (IdMaitre, IdEtudiant, CodeCompetence, IdActivite, Lieu, Moment, DateCreation, CodeEtat)
				 VALUES (:masterid, :studentid, :skillcode, :activityid, :place, :moment, sysdate(), 'ACTIF');";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":masterid", $inMasterID, PDO::PARAM_INT);
			$this->DAL->Bind(":studentid", $inStudentID, PDO::PARAM_INT);
			$this->DAL->Bind(":skillcode", $inSkillCode, PDO::PARAM_STR);
			$this->DAL->Bind(":activityid", $inActivityID, PDO::PARAM_INT);
			$this->DAL->Bind(":place", $inPlace, PDO::PARAM_STR);
			$this->DAL->Bind(":moment", $inMoment, PDO::PARAM_STR);
		return $this->DAL->FetchResult();
	}


	//--CANCEL CHARACTER'S TEACHING--
	public function CancelTeaching( $inID )
	{
		// Verify there's a user and a character
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }
		

		// Ask the database...
		$lQuery = 	"DELETE FROM db_perso.enseignements
				 WHERE Id = :teachingid
				   AND CodeEtat = 'ACTIF';

				 DELETE FROM db_perso.experience
				 WHERE IdEnseignement = :teachingid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":teachingid", $inID, PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--REDEEM CHARACTER'S TEACHING FOR XP--
	public function RedeemTeaching( $inTeaching )
	{
		// Verify there's a user and a character
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }
		

		// Ask the database...
		$lQuery = 	"UPDATE db_perso.enseignements
				 SET CodeEtat = 'RECOM'
				 WHERE Id = :teachingid;

				 INSERT INTO db_perso.experience (IdPersonnage, Raison, XP, IdEnseignement)
				 VALUES (:characterid, :reason, :xp, :teachingid)";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":teachingid", $inTeaching['id'], PDO::PARAM_INT);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":reason", 'Compensation - Cours '.$inTeaching['skillname'], PDO::PARAM_STR);
			$this->DAL->Bind(":xp", $inTeaching['xpvalue'], PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--CALCULATE CHARACTER'S REWARD FOR TEACHING A SKILL--
	public function CalculateMasterReward( $inMasterID, $inSkillCode, $inActivityID )
	{
		// Ask the database for received reward as to date
		$lQuery = 	"SELECT SUM(CEILING(creg.DureeCours/6)) AS RecompenseRecue
				 FROM db_perso.enseignements ens
				 	JOIN db_pilot.competences_regulieres creg ON ens.CodeCompetence = creg.Code
				 WHERE ens.IdMaitre = :masterid
				   AND ens.IdActivite = :activityid
				   AND ens.CodeEtat <> 'ANNUL';";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":masterid", $inMasterID, PDO::PARAM_INT);
			$this->DAL->Bind(":activityid", $inActivityID, PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();
		$lReceivedReward = $r[0]['RecompenseRecue'];

		if( $lReceivedReward < 20 ) {

			$limit = 20 - $lReceivedReward;

			// Ask the database how much XP the given class is worth
			$lQuery = 	"SELECT CEILING(creg.DureeCours/6) AS RecompenseCours
					 FROM db_pilot.competences_regulieres creg
					 WHERE creg.Code = :skillcode;";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":skillcode", $inSkillCode, PDO::PARAM_STR);
			$r = $this->DAL->FetchResult();
			$lSkillWorth = $r[0]['RecompenseCours'];

			if( $lSkillWorth > $limit ) { $lSkillWorth = $limit; }

			return $lSkillWorth;
		}

		return 0;
	}


	//--INSERT CHARACTER'S XP REWARD FOR TEACHING--
	public function ApplyMasterReward( $inMasterID, $inStudentID, $inSkillCode, $inReward )
	{
		// Ask the database...
		// Get teaching ID
		$lQuery = 	"SELECT ens.Id
				 FROM db_perso.enseignements ens
				 WHERE ens.IdMaitre = :masterid
				   AND ens.IdEtudiant = :studentid
				   AND ens.CodeCompetence = :skillcode;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":masterid", $inMasterID, PDO::PARAM_INT);
			$this->DAL->Bind(":studentid", $inStudentID, PDO::PARAM_INT);
			$this->DAL->Bind(":skillcode", $inSkillCode, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();
		$lTeachingID = $r[0]['Id'];


		// Get student name
		$lQuery = 	"SELECT CONCAT(per.Prenom, ' ', per.Nom) AS NomPerso
				 FROM db_perso.personnages per
				 WHERE per.Id = :characterid;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $inStudentID, PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();
		$lCharacterName = $r[0]['NomPerso'];


		// Get skill name
		$lQuery = 	"SELECT creg.Nom
				 FROM db_pilot.competences_regulieres creg
				 WHERE creg.Code = :skillcode;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":skillcode", $inSkillCode, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();
		$lSkillName = $r[0]['Nom'];


		// Insert XP
		$lQuery = 	"INSERT INTO db_perso.experience (IdPersonnage, Raison, XP, DateInscription, IdEnseignement)
				 VALUES (:characterid, :reason, :xp, sysdate(), :teachingid);";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $inMasterID, PDO::PARAM_INT);
			$this->DAL->Bind(":reason", "Enseignement: ".$lSkillName." à ".$lCharacterName, PDO::PARAM_STR);
			$this->DAL->Bind(":xp", $inReward, PDO::PARAM_INT);
			$this->DAL->Bind(":teachingid", $lTeachingID, PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--READJUST TEACHINGS' WORTHS--
	public function RecalculateTeachingsWorth( $inMasterID, $inActivityID )
	{
		// Ask the database the list of teachings given by master for particular activity
		$lQuery = 	"SELECT ens.Id, CAST(creg.DureeCours/6 AS UNSIGNED) AS Valeur, exp.XP
				 FROM db_perso.enseignements ens
				 	LEFT JOIN db_perso.experience exp ON ens.Id = exp.IdEnseignement
				 	LEFT JOIN db_pilot.competences_regulieres creg ON ens.CodeCompetence = creg.Code
				 WHERE ens.IdMaitre = :masterid
				   AND ens.IdActivite = :activityid
				 ORDER BY ens.Id ASC;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":masterid", $inMasterID, PDO::PARAM_INT);
			$this->DAL->Bind(":activityid", $inActivityID, PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Update XP values that are wrong
		$lTotalReward = 0;
		foreach ($r as $teaching) {

			// If the cap is reached, all other teachings are worth 0
			if( $lTotalReward >= 20 ){
				if( $teaching['XP'] > 0 ) { $this->UpdateTeachingWorth( $teaching['Id'], 0 ); }
			}

			// Else if the whole value doesn't reach the cap, the whole value is given
			elseif( $teaching['Valeur'] <= (20 - $lTotalReward) ) {
				if( $teaching['Valeur'] != $teaching['XP'] ) { $this->UpdateTeachingWorth( $teaching['Id'], $teaching['Valeur'] ); }	
				$lTotalReward += $teaching['Valeur'];
			}

			// Else what's left before reaching the cap is given
			else {
				if( $teaching['XP'] != (20 - $lTotalReward) ) { $this->UpdateTeachingWorth( $teaching['Id'], (20 - $lTotalReward) ); }
				$lTotalReward = 20;
			}

		}

		return False;
	}


	//--UPDATE CHARACTER'S XP REWARD FOR A SPECIFIC TEACHING--
	public function UpdateTeachingWorth( $inTeachingID, $inNewReward )
	{
		// Ask the database...
		$lQuery = 	"UPDATE db_perso.experience
				 SET XP = :xp
				 WHERE IdEnseignement = :teachingid;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":xp", $inNewReward, PDO::PARAM_INT);
			$this->DAL->Bind(":teachingid", $inTeachingID, PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--SAVE FIELD SERVICE REQUEST--
	public function RegisterSyllabusProposal( $inSkillCode, $inFileIndex )
	{
		// Check if manager is set
		if( !isset($this->Character) ) { $this->Error = "RegisterSyllabusProposal : No character set!"; return False; }

		// Prepare data
		$lCharacterID = $this->Character->GetID();
		$lTarget = SYLLABUS_UPLOAD_DIR . 'Plan de cours - '.$inSkillCode.' - P'.$lCharacterID.'.pdf';

		// Upload file
		$r = move_uploaded_file($_FILES[$inFileIndex]['tmp_name'], $lTarget);
  		
  		// If upload is successful...
  		if ($r) { 
  			// ... check if syllabus already exists ...
  			$lQuery = 	"SELECT Id FROM db_perso.maitres
					 WHERE IdPersonnage = :characterid
					   AND CodeCompetence = :skillcode
					   AND CodeEtat <> 'ACTIF';";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":characterid", $lCharacterID, PDO::PARAM_INT);
				$this->DAL->Bind(":skillcode", $inSkillCode, PDO::PARAM_STR);
			$n = $this->DAL->FetchResult();


  			// ... add a request to the masters' list...
  			if ($n) {
				$lQuery = 	"UPDATE db_perso.maitres
						 SET CodeEtat = 'DEM'
						 WHERE IdPersonnage = :characterid
						   AND CodeCompetence = :skillcode
						   AND CodeEtat <> 'ACTIF';";

				$this->DAL->SetQuery($lQuery);
					$this->DAL->Bind(":characterid", $lCharacterID, PDO::PARAM_INT);
					$this->DAL->Bind(":skillcode", $inSkillCode, PDO::PARAM_STR);
				$this->DAL->FetchResult();		
  			}
  			else {
				$lQuery = 	"INSERT INTO db_perso.maitres (IdPersonnage, CodeCompetence, CodeEtat)
						 VALUES (:characterid, :skillcode, 'DEM');";

				$this->DAL->SetQuery($lQuery);
					$this->DAL->Bind(":characterid", $lCharacterID, PDO::PARAM_INT);
					$this->DAL->Bind(":skillcode", $inSkillCode, PDO::PARAM_STR);
				$this->DAL->FetchResult();		
  			}


  			// ... and add a note in the character's journal
  			$lMessage = "Le fichier ". htmlspecialchars( basename( $_FILES[$inFileIndex]['name'] )). " a été soumis."; 

			// Insert log
			$lQuery = 	"INSERT INTO db_perso.remarques (IdPersonnage, Message, Type)
					 VALUES (:characterid, :message, 'TELEV');";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":characterid", $lCharacterID, PDO::PARAM_INT);
				$this->DAL->Bind(":message", $lMessage, PDO::PARAM_STR);
			$this->DAL->FetchResult();

			return True;
  		} 
 
 		$this->Error = "Il y a eu un problème avec le dépôt de votre fichier.";
		return False;
	}


	//--SAVE CHARACTER'S BACKGROUND STORY--
	public function SaveCharacterBackground($inBackground)
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"UPDATE db_perso.personnages per
				 SET per.Histoire = :background
				 WHERE per.Id = :characterid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":background", $inBackground, PDO::PARAM_STR);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--SEND ACCESS MAIL
	public function SendApprovalRequestMail($inSubject)
	{
		// Prepare data
		$lUserName = $this->User->GetFullName();
		$lAccount = $this->User->GetAccountName();
		$lMail = $this->User->GetMailAddress();

		$lCharacterName = $this->Character->GetFullName();
		$lCharacterClass = $this->Character->GetClass();	# Class code instead of name...
		$lCharacterRace = $this->Character->GetRace();		# Same
		$lCharacterReligion = $this->Character->GetReligion();	# Same


		// Define mail transport
		$transport = Swift_SmtpTransport::newInstance("smtp.gmail.com", 465, "ssl")
			->setUsername(GMAIL_USERNAME)
			->setPassword(GMAIL_PASSWORD);

		$mailer = Swift_Mailer::newInstance($transport);

		$message = Swift_Message::newInstance('Demande d\'approbation - '.$lCharacterName.' - '.$inSubject.' de personnage')
		  ->setFrom(array('TI@Terres-de-Belenos.com' => 'BD bélénoise'))
		  ->setTo(array('Organisation@Terres-de-Belenos.com' => 'Organisation des Terres de Bélénos'))
		  ->setBody('Chers Organisateurs,<br />
		  	<br />
		  	'.$lUserName.' requiert une approbation au sujet de son/sa <b>"'.$inSubject.' de personnage"</b>. Voici ses informations de personnage et de compte :<br />
		  	<br />
		  	<b>Personnage : </b>'.$lCharacterName.'<br />
		  	<b>Classe : </b>'.$lCharacterClass.'<br />
		  	<b>Race : </b>'.$lCharacterRace.'<br />
		  	<b>Religion : </b>'.$lCharacterReligion.'<br />
		  	---------------------------------------<br />
		  	<b>Compte : </b>'.$lAccount.'<br />
		  	<b>Courriel : </b>'.$lMail.'<br />
		  	<br />
		  	Merci et bonne journée!<br />
		  	<br />
		  	<i>- Votre Base de données</i>', 'text/html');

		return $mailer->send($message);	
	}


	//--SAVE APPROVAL REQUEST--
	public function RegisterApprovalRequest( $inSubject )
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// If request exists, update
		$lApproval = $this->Character->GetSubjectApproval( $inSubject );
		if( $lApproval ) {
			$lQuery = 	"UPDATE db_perso.approbations 
					 SET CodeEtat = 'DEM', DateDemande = sysdate()
					 WHERE IdPersonnage = :characterid AND Objet = :subject ;";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
				$this->DAL->Bind(":subject", $inSubject, PDO::PARAM_STR);
		}

		// Else, insert
		else {
			$lQuery = 	"INSERT INTO db_perso.approbations (IdPersonnage, Objet, CodeEtat, DateDemande)
					 VALUES ( :characterid, :subject, 'DEM', sysdate() );";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
				$this->DAL->Bind(":subject", $inSubject, PDO::PARAM_STR);
		}
		
		return $this->DAL->FetchResult();
	}


	//--REGISTER CHARACTER'S QUEST REQUEST--
	public function RegisterQuestRequest( $inSubject, $inOptionCode, $inRewardCode, $inCountyID, $inSuggestions, $inStatusCode, $inNextActivity )
	{
		// Verify there's a user and a character
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }


		// Ask the database...
		$lQuery = 	"INSERT INTO db_perso.quetes (IdPersonnage, Objet, CodeOption, CodeRecompense, IdComte, Suggestions, CodeEtat, DateDemande, IdActivite)
				 VALUES ( :characterid, :subject, :optioncode, :rewardcode, :countyid, :suggestions, :statuscode, sysdate(), :activityid );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":subject", $inSubject, PDO::PARAM_STR);
			$this->DAL->Bind(":optioncode", $inOptionCode, PDO::PARAM_STR);
			$this->DAL->Bind(":rewardcode", $inRewardCode, PDO::PARAM_STR);
			$this->DAL->Bind(":countyid", $inCountyID, PDO::PARAM_INT);
			$this->DAL->Bind(":suggestions", $inSuggestions, PDO::PARAM_STR);
			$this->DAL->Bind(":statuscode", $inStatusCode, PDO::PARAM_STR);
			$this->DAL->Bind(":activityid", $inNextActivity, PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--CANCEL CHARACTER'S QUEST--
	public function CancelQuest( $inID )
	{
		// Verify there's a user and a character
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }
		

		// Ask the database...
		$lQuery = 	"DELETE FROM db_perso.quetes
				 WHERE Id = :questid 
				   AND IdPersonnage = :characterid 
				   AND DateApprobation IS NULL;

				 UPDATE db_perso.quetes que
				 SET que.CodeEtat = 'ANNUL'
				 WHERE que.Id = :questid 
				   AND que.IdPersonnage = :characterid
				   AND que.DateApprobation IS NOT NULL;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":questid", $inID, PDO::PARAM_INT);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--RESTORE CHARACTER'S QUEST--
	public function RestoreQuest( $inID )
	{
		// Verify there's a user and a character
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }
		

		// Ask the database...
		$lQuery = 	"UPDATE db_perso.quetes que
				 SET que.CodeEtat = 'REPR'
				 WHERE que.Id = :questid 
				   AND que.IdPersonnage = :characterid
				   AND que.CodeEtat = 'ANNUL';";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":questid", $inID, PDO::PARAM_INT);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--CHECK IF RESUMÉ ALREADY EXISTS--
	public function ResumeExists( $inActivityID )
	{
		// Verify there's a user and a character
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT 1 FROM db_perso.resumes resu
				 WHERE resu.IdPersonnage = :characterid AND resu.IdActivite = :activityid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":activityid", $inActivityID, PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Return True if the name exists for this account
		if ($this->DAL->GetRowCount() > 0) { return True; }
		return False;
	}


	//--REGISTER CHARACTER'S RESUMÉ--
	public function RegisterNewResume( $inActivityID, $inText, $inQuestID =NULL )
	{
		// Verify there's a user and a character
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"INSERT INTO db_perso.resumes (IdPersonnage, IdActivite, Resume, IdQuete, DateCreation)
				 VALUES ( :characterid, :activityid, :resume, :questid, sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":activityid", $inActivityID, PDO::PARAM_INT);
			$this->DAL->Bind(":resume", $inText, PDO::PARAM_STR);
			$this->DAL->Bind(":questid", $inQuestID, PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--UPDATE CHARACTER'S RESUMÉ--
	public function UpdateResume( $inResumeID, $inText, $inQuestID =NULL )
	{
		// Verify there's a user and a character
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"UPDATE db_perso.resumes resu
				 SET resu.Resume = :resume, resu.IdQuete = :questid
				 WHERE IdPersonnage = :characterid
				   AND Id = :resumeid;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":resumeid", $inResumeID, PDO::PARAM_INT);
			$this->DAL->Bind(":resume", $inText, PDO::PARAM_STR);
			$this->DAL->Bind(":questid", $inQuestID, PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--REGISTER CHARACTER'S NEW LETTER--
	public function CreateNewLetter( $inRecipientID, $inSubject, $inText, $inType )
	{
		// Verify there's a user and a character
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Get next activity's ID
		$lActivityID = $_SESSION['masterlist']->GetNextMainActivity()->GetID();

		// Ask the database...
		$lQuery = 	"INSERT INTO db_perso.missives (IdEcrivain, IdDestinataire, IdActivite, Type, Objet, Corps, CodeEtat, DateEnvoi)
				 VALUES ( :characterid, :recipientid, :activityid, :type, :subject, :body, 'NOUVO', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":recipientid", $inRecipientID, PDO::PARAM_INT);
			$this->DAL->Bind(":activityid", $lActivityID, PDO::PARAM_INT);
			$this->DAL->Bind(":type", $inType, PDO::PARAM_STR);
			$this->DAL->Bind(":subject", $inSubject, PDO::PARAM_STR);
			$this->DAL->Bind(":body", $inText, PDO::PARAM_STR);
		return $this->DAL->FetchResult();
	}


	//--UPDATE CHARACTER'S LETTER--
	public function UpdateLetter( $inLetterID, $inSubject, $inText, $inStatus )
	{
		// Verify there's a user and a character
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		if( $inStatus == 'NOUVO') {
			$lQuery = 	"UPDATE db_perso.missives
					 SET Objet = :subject, Corps = :body, CodeEtat = :statuscode, DateEnvoi = sysdate()
					 WHERE Id = :letterid;";
		}
		else {
			$lQuery = 	"UPDATE db_perso.missives
					 SET Objet = :subject, Corps = :body, CodeEtat = :statuscode
					 WHERE Id = :letterid;";			
		}

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":letterid", $inLetterID, PDO::PARAM_INT);
			$this->DAL->Bind(":subject", $inSubject, PDO::PARAM_STR);
			$this->DAL->Bind(":body", $inText, PDO::PARAM_STR);
			$this->DAL->Bind(":statuscode", $inStatus, PDO::PARAM_STR);
		return $this->DAL->FetchResult();
	}


	//--UPLOAD CHARACTER'S LETTER--
	public function UploadPCLetter( $inRecipientID, $inSubject )
	{
		// Check if character is set
		if( !isset($this->Character) ) { $this->Error = "UploadLetter : No character set!"; return False; }

		// Prepare data
		$lNextActivityName = $_SESSION['masterlist']->GetNextMainActivity()->GetName();
		$lRecipientName = null;
		$lTarget = null;

  		// Get recipient name
  		$lQuery = 	"SELECT CONCAT(Prenom, ' ', Nom) AS NomPersonnage
  				 FROM db_perso.personnages
				 WHERE Id = :characterid
				   AND CodeEtat <> 'MORT';";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $inRecipientID, PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();

		if($r) { 
			$lRecipientName = $r[0]['NomPersonnage']; 
			$lTarget = LETTERS_UPLOAD_DIR . $lNextActivityName . '/'.$lRecipientName.' - '.$inSubject.'.pdf';
		}
		else {
			$this->Error = "UploadLetter : Invalid recipient!"; return False;
		}

		// Create directory if it does not exists
		if( !file_exists( LETTERS_UPLOAD_DIR . $lNextActivityName ) ) { mkdir( LETTERS_UPLOAD_DIR . $lNextActivityName ); }

		// Upload file
		$r = move_uploaded_file($_FILES['attachedfile']['tmp_name'], $lTarget);
  		
  		// If upload is successful...
  		if ($r) { 
  
  			// ... and add a note in the character's journal
  			$lMessage = "Le fichier ". htmlspecialchars( basename( $_FILES['attachedfile']['name'] )). " a été soumis."; 

			// Insert log
			$lQuery = 	"INSERT INTO db_perso.remarques (IdPersonnage, Message, Type)
					 VALUES (:characterid, :message, 'TELEV');";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
				$this->DAL->Bind(":message", $lMessage, PDO::PARAM_STR);
			$this->DAL->FetchResult();

			return True;
  		} 
 
 		$this->Error = "Il y a eu un problème avec le dépôt de votre fichier.";
		return False;
	}


	//--UPLOAD CHARACTER'S LETTER--
	public function UploadNPCLetter( $inRecipientName, $inSubject )
	{
		// Check if character is set
		if( !isset($this->Character) ) { $this->Error = "UploadLetter : No character set!"; return False; }

		// Prepare data
		$lNextActivityName = $_SESSION['masterlist']->GetNextMainActivity()->GetName();
		$lTarget = LETTERS_UPLOAD_DIR . $lNextActivityName . '/'.$inRecipientName.' - '.$inSubject.'.pdf';

		// Create directory if it does not exists
		if( !file_exists( LETTERS_UPLOAD_DIR . $lNextActivityName ) ) { mkdir( LETTERS_UPLOAD_DIR . $lNextActivityName ); }

		// Upload file
		$r = move_uploaded_file($_FILES['attachedfile']['tmp_name'], $lTarget);
  		
  		// If upload is successful...
  		if ($r) { 
  
  			// ... and add a note in the character's journal
  			$lMessage = "Le fichier ". htmlspecialchars( basename( $_FILES['attachedfile']['name'] )). " a été soumis."; 

			// Insert log
			$lQuery = 	"INSERT INTO db_perso.remarques (IdPersonnage, Message, Type)
					 VALUES (:characterid, :message, 'TELEV');";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
				$this->DAL->Bind(":message", $lMessage, PDO::PARAM_STR);
			$this->DAL->FetchResult();

			return True;
  		} 
 
 		$this->Error = "Il y a eu un problème avec le dépôt de votre fichier.";
		return False;
	}


	//--SEND NPC LETTER BY MAIL
	public function SendNPCLetterMail($inRecipientName, $inSubject)
	{
		// Prepare data
		$lUserName = $this->User->GetFullName();
		$lAccount = $this->User->GetAccountName();
		$lMail = $this->User->GetMailAddress();

		$lCharacterName = $this->Character->GetFullName();

		$lActivityName = $_SESSION['masterlist']->GetNextMainActivity()->GetName();


		// Define mail transport
		$transport = Swift_SmtpTransport::newInstance("smtp.gmail.com", 465, "ssl")
			->setUsername(GMAIL_USERNAME)
			->setPassword(GMAIL_PASSWORD);

		$mailer = Swift_Mailer::newInstance($transport);

		$message = Swift_Message::newInstance('Missive à un PNJ - '.$lCharacterName.' à '.$inRecipientName)
		  ->setFrom(array($lMail => $lUserName))
		  ->setTo(array('Organisation@Terres-de-Belenos.com' => 'Organisation des Terres de Bélénos'))
		  ->setBody('Bonjour,<br />
		  	<br />
		  	Voici une missive à un PNJ envoyée avant '.$lActivityName.' :<br />
		  	<br />c
		  	<b><u>Soumissionnaire</u></b><br />
		  	<b>Nom : </b>'.$lUserName.'<br />
		  	<b>Courriel : </b>'.$lMail.'<br />
		  	____________________________________________<br />
		  	<br />
		  	<b><u>Missive</u></b><br />
		  	<b>De : </b>'.$lCharacterName.'<br />
		  	<b>À : </b>'.$inRecipientName.'<br />
		  	<b>Objet : </b>'.$inSubject.'<br />
		  	<br />
		  	La missive elle-même est en pièce jointe.<br />
		  	<br />
		  	Merci et bonne journée!<br />
		  	<br />
		  	<i>- Courriel automatique produit via la Base de données bélénoise</i>', 'text/html');

		// Define attachment
		$attachment = Swift_Attachment::fromPath( LETTERS_UPLOAD_DIR . $lActivityName . '/' . $inRecipientName.' - '.$inSubject.'.pdf');
		$message->attach($attachment);
		

		return $mailer->send($message);	
	}


	//--DELETE CHARACTER'S LETTER--
	public function DeleteLetter( $inLetterID )
	{
		// Verify there's a user and a character
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"DELETE FROM db_perso.missives
				 WHERE Id = :letterid;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":letterid", $inLetterID, PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--ARCHIVE CHARACTER'S LETTER--
	public function ArchiveLetter( $inLetterID )
	{
		// Verify there's a user and a character
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"UPDATE db_perso.missives
				 SET CodeEtat = 'ARCHI'
				 WHERE Id = :letterid
				    OR ReponseA = :letterid;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":letterid", $inLetterID, PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--UPDATE CHARACTER'S STATUS--
	public function UpdateCharacterStatus( $inStatus )
	{
		// Verify there's a user and a character
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }


		// Prepare data
		$lStatusName = 'Inconnu';
		    if( $inStatus == 'NOUVO' ) 	{ $lStatusName = 'Nouveau'; }
		elseif( $inStatus == 'ACTIF' ) 	{ $lStatusName = 'Actif'; }
		elseif( $inStatus == 'LEVEL' ) 	{ $lStatusName = 'Actuel'; }
		elseif( $inStatus == 'EXIL' ) 	{ $lStatusName = 'Exilé'; }
		elseif( $inStatus == 'DEPOR' ) 	{ $lStatusName = 'Déporté'; }
		elseif( $inStatus == 'RETIR' ) 	{ $lStatusName = 'Retraité'; }
		elseif( $inStatus == 'MORT' ) 	{ $lStatusName = 'Décédé'; }


		// Ask the database...
		$lQuery = 	"UPDATE db_perso.personnages per
				 SET per.CodeEtat = :status
				 WHERE per.Id = :characterid;

				 INSERT INTO db_perso.remarques (IdPersonnage, Message, Type, DateCreation)
				 VALUES ( :characterid, :message, 'STAT', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":status", $inStatus, PDO::PARAM_STR);
			$this->DAL->Bind(":message", "Modification du status : ".$lStatusName, PDO::PARAM_STR);
		return $this->DAL->FetchResult();
	}


	//--REGISTER NEW LIFE ADJUSTMENT FOR CHARACTER--
	public function InsertLifeAdjustment($inAmount, $inReason, $inComments)
	{
		// Verify there's a user and a character
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }


		// Prepare data
		$lComments = NULL;
			if( $inComments ) { $lComments = $inComments; }
		$lCurrentLife = $this->Character->GetTotalLife();
		$lModifiedLife = $lCurrentLife + $inAmount;
		$lAmount = $inAmount;
			if( $lModifiedLife <= 0 ) { $lAmount = 1 - $lCurrentLife; }	// Total life cannot go under 1. Correct adjustment if necessary.

		// Ask the database...
		$lQuery = 	"INSERT INTO db_perso.points_de_vie (IdPersonnage, Raison, PV, DateInscription, Commentaires)
				 VALUES ( :characterid, :reason, :life, sysdate(), :comments );

				 INSERT INTO db_perso.remarques (IdPersonnage, Message, Type, DateCreation)
				 VALUES ( :characterid, :message, 'STAT', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":reason", $inReason, PDO::PARAM_STR);
			$this->DAL->Bind(":life", $lAmount, PDO::PARAM_INT);
			$this->DAL->Bind(":comments", $lComments, PDO::PARAM_STR);
			$this->DAL->Bind(":message", "Ajustement des PV : ".$lAmount." pour ".$inReason, PDO::PARAM_STR);
		return $this->DAL->FetchResult();
	}


	//--TRANSFER XP FROM CURRENT PLAYER TO CURRENT CHARACTER--
	public function TransferExperience($inXP)
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"INSERT INTO db_indiv.experience (IdIndividu, Raison, XP, DateInscription, CodeUnivers)
				 VALUES ( :userid, :reason, -:xp, sysdate(), :universecode );

				 INSERT INTO db_perso.experience (IdPersonnage, Raison, XP, DateInscription)
				 VALUES ( :characterid, 'Transfert', :xp, sysdate() );

				 INSERT INTO db_indiv.remarques (IdIndividu, Message, Type, DateCreation)
				 VALUES ( :userid, :playermessage, 'XP', sysdate() );

				 INSERT INTO db_perso.remarques (IdPersonnage, Message, Type, DateCreation)
				 VALUES ( :characterid, :charactermessage, 'XP', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":reason", 'Transfert vers '.$this->Character->GetFullName(), PDO::PARAM_STR);
			$this->DAL->Bind(":xp", $inXP, PDO::PARAM_INT);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":playermessage", 'Transfert de '.$inXP.' XP vers '.$this->Character->GetFullName(), PDO::PARAM_STR);
			$this->DAL->Bind(":charactermessage", 'Transfert de '.$inXP.' XP de joueur', PDO::PARAM_STR);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		return $this->DAL->FetchResult();
	}


	//--CHECK IF CHARACTER'S FULL NAME CAN BE USED--
	public function CharacterNameExists( $inUserID, $inFirstName, $inLastName )
	{
		// If it exists in the databse for this user, then it's taken.
		$lQuery = "SELECT per.Id FROM db_perso.personnages per WHERE per.IdIndividu = :userid AND per.Prenom = :firstname AND per.Nom = :lastname";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $inUserID, PDO::PARAM_INT);
			$this->DAL->Bind(":firstname", $inFirstName, PDO::PARAM_STR);
			$this->DAL->Bind(":lastname", $inLastName, PDO::PARAM_STR);
		$this->DAL->FetchResult();


		// Return True if the name exists for this account
		if ($this->DAL->GetRowCount() > 0) { return True; }
		return False;
	}


	//--RENAME CURRENT CHARACTER--
	public function RenameCurrentCharacter( $inFirstName, $inLastName )
	{
		// Verify there's a user and a character
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"UPDATE db_perso.personnages per
				 SET per.Prenom = :firstname, per.Nom = :lastname
				 WHERE per.Id = :characterid;

				 INSERT INTO db_perso.remarques (IdPersonnage, Message, Type, DateCreation)
				 VALUES (:characterid, :message, 'INFO', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":firstname", $inFirstName, PDO::PARAM_STR);
			$this->DAL->Bind(":lastname", $inLastName, PDO::PARAM_STR);

			$this->DAL->Bind(":message", 'Personnage renommé « '.$inFirstName.' '.$inLastName.' ».', PDO::PARAM_STR);
		return $this->DAL->FetchResult();
	}


	//--CHANGE CURRENT CHARACTER'S ORIGIN--
	public function ChangeOrigin( $inNewOrigin )
	{
		// Verify there's a user and a character
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"UPDATE db_perso.personnages per
				 SET per.Provenance = :origin
				 WHERE per.Id = :characterid;

				 INSERT INTO db_perso.remarques (IdPersonnage, Message, Type, DateCreation)
				 VALUES (:characterid, :message, 'INFO', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":origin", $inNewOrigin, PDO::PARAM_STR);

			$this->DAL->Bind(":message", 'Nouvelle provenance : « '.$inNewOrigin.' ».', PDO::PARAM_STR);
		return $this->DAL->FetchResult();
	}


	//--VALIDATE USER AND PASSWORD FOR DELETION PROCESS--
	public function ValidateDeletion( $inPassword )
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }

		// Encrypt password using SHA-256
		$lSalted = hash("sha256" , $inPassword.SALT);

		// Ask the database...
		$lQuery = 	"SELECT ind.Compte FROM db_indiv.individus ind
				 WHERE ind.Id = :userid AND ind.MotDePasse = :password";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":password", $lSalted, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		if( $r[0]['Compte'] == $this->User->GetAccountName() ) { return True; }
		return False;
	}


	//--DELETE CURRENT CHARACTER--
	public function DeleteCurrentCharacter()
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Get quest list for parts' deletion
		$lQuery = 	"SELECT que.Id FROM db_perso.quetes que WHERE que.IdPersonnage = :characterid;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();

		$lQuestList = array();
		foreach( $r as $quest ) { $lQuestList[] = $quest['Id']; }
		$lQuestString = implode(',', $lQuestList);

		// Logical deletion
		$lQuery = "";
		if( $this->Character->GetLevel() == 0 ) {
			$lQuery = 	"DELETE FROM db_perso.personnages WHERE Id = :characterid;
					 DELETE FROM db_perso.titres WHERE IdPersonnage = :characterid;
					 DELETE FROM db_perso.maitres WHERE IdPersonnage = :characterid;
					 DELETE FROM db_perso.quetes WHERE IdPersonnage = :characterid;
					 DELETE FROM db_perso.invitations WHERE IdPersonnage = :characterid;
					 DELETE FROM db_group.membres WHERE IdPersonnage = :characterid;";
		}
		else {
			$lQuery = 	"UPDATE db_perso.personnages SET CodeEtat = 'SUPPR' WHERE Id = :characterid;
					 DELETE FROM db_perso.titres WHERE IdPersonnage = :characterid;
					 UPDATE db_perso.maitres SET CodeEtat = 'INACT' WHERE IdPersonnage = :characterid;
					 UPDATE db_perso.quetes SET CodeEtat = 'ANNUL' WHERE IdPersonnage = :characterid;
					 DELETE FROM db_perso.invitations WHERE IdPersonnage = :characterid;
					 DELETE FROM db_group.membres WHERE IdPersonnage = :characterid;";
		}
		
		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":questid", $lQuestString, PDO::PARAM_STR);
		return $this->DAL->FetchResult();
	}


	//--DELETE CURRENT CHARACTER--
	public function SaveSurveyAnswers($inSurveyCode, $inAnswerList)
	{
		// Verify there's a user and a character
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }


		// Delete possibly existing answers for this survey
		$lQuery = 	"DELETE FROM db_perso.reponses_questionnaire
				 WHERE IdPersonnage = :characterid
				   AND CodeQuestionnaire = :surveycode;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":surveycode", $inSurveyCode, PDO::PARAM_STR);
		$this->DAL->FetchResult();


		// Insert each answer in the database
		foreach ($inAnswerList as $number => $answer) {
			$lQuery = 	"INSERT INTO db_perso.reponses_questionnaire (IdPersonnage, CodeQuestionnaire, NumeroQuestion, Reponse, DateInscription)
					 VALUES (:characterid, :surveycode, :questionnumber, :answer, sysdate());";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
				$this->DAL->Bind(":surveycode", $inSurveyCode, PDO::PARAM_STR);
				$this->DAL->Bind(":questionnumber", $number, PDO::PARAM_INT);
				$this->DAL->Bind(":answer", $answer, PDO::PARAM_STR);
			$r = $this->DAL->FetchResult();

			if(!$r) { return False;	}
		}

		return True;
	}


//=================================================================== NEW CHARACTER SERVICES ===================================================================

	//--GET A RACE'S NAME FROM ITS CODE--
	public function GetRaceName()
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT rac.Nom
				 FROM db_pilot.races rac
				 WHERE rac.Code = :racecode";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":racecode", $this->Character->GetRaceCode(), PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		$this->Character->SetRace( $r[0]['Nom'] );
		return True;
	}


	//--GET A CLASS' NAME FROM ITS CODE--
	public function GetClassName()
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT clas.Nom
				 FROM db_pilot.classes clas
				 WHERE clas.Code = :classcode";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":classcode", $this->Character->GetClassCode(), PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		$this->Character->SetClass( $r[0]['Nom'] );
		return True;
	}


	//--GET A SKILLSET' NAME FROM ITS CODE--
	public function GetArchetypeName()
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT arch.Nom
				 FROM db_pilot.archetypes arch
				 WHERE arch.Code = :code";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":code", $this->Character->GetArchetypeCode(), PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		$this->Character->SetArchetype( $r[0]['Nom'] );
		return True;
	}


	//--GET A RELIGION'S NAME FROM ITS CODE--
	public function GetReligionName()
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT rel.Nom
				 FROM db_pilot.religions rel
				 WHERE rel.Code = :religioncode";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":religioncode", $this->Character->GetReligionCode(), PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		$this->Character->SetReligion( $r[0]['Nom'] );
		return True;
	}


	//--GET A NEW CHARACTER'S RACE CHOICES--
	public function GetPossibleRaces()
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT rac.Code, rac.Nom
				 FROM db_pilot.races rac
				 WHERE rac.CodeUnivers = :universecode
				 ORDER BY FIELD(rac.Code, 'HUMAIN','NAIN','ELFE','CHAPPY','HLEZARD','ORC','GOBELIN','AMAI','HRAT','GNOLL','DEMI');";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		// Build and save result array
		$lRaceList = array();
		foreach($r as $i => $race) { 
			$lRaceList[$i]['code'] = $race['Code'];
			$lRaceList[$i]['name'] = $race['Nom'];
		}

		$this->Character->SetRaceList($lRaceList);
		return True;
	}


	//--GET A NEW CHARACTER'S RACIAL CLASSES--
	public function GetPossibleClasses()
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT clas.Code, clas.Nom
				 FROM db_pilot.classes clas
				 WHERE clas.Code NOT IN (SELECT cin.CodeClasse
							 FROM db_pilot.classes_interdites cin
							 WHERE cin.CodeRace = :racecode)
				 ORDER BY clas.Nom ASC";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":racecode", $this->Character->GetRaceCode(), PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		// Build and save result array
		$lClassList = array();
		foreach($r as $i => $class) { 
			$lClassList[$i]['code'] = $class['Code'];
			$lClassList[$i]['name'] = $class['Nom'];
		}

		$this->Character->SetPossibleClasses($lClassList);
		return True;
	}


	//--GET A NEW CHARACTER'S CLASS OPTIONS--
	public function GetPossibleArchetypes()
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT arch.Code, arch.Nom
				 FROM db_pilot.archetypes arch
				 WHERE arch.CodeClasse = :classcode";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":classcode", $this->Character->GetClassCode(), PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		// Build and save result array
		$lArchetypeList = array();
		foreach($r as $i => $archetype) { 
			$lArchetypeList[$i]['code'] = $archetype['Code'];
			$lArchetypeList[$i]['name'] = $archetype['Nom'];
		}

		$this->Character->SetPossibleArchetypes($lArchetypeList);
		return True;
	}


	//--GET A NEW CHARACTER'S APPROVED RELIGIONS--
	public function GetPossibleReligions()
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Prepare data
		$lClassCode = $this->Character->GetClassCode();


		// Ask the database...
		$lQuery = 	"SELECT rel.Code, rel.Nom, rel.Type
				 FROM db_pilot.religions rel
				 WHERE rel.Type <> 'SPECIAL'
				   AND rel.Code NOT IN (SELECT rin.CodeReligion
							 FROM db_pilot.religions_interdites rin
							 WHERE rin.CodeRace = :racecode
							   AND rin.CodeReligion IS NOT NULL)
				   AND rel.Type NOT IN (SELECT rin.TypeReligion
							 FROM db_pilot.religions_interdites rin
							 WHERE rin.CodeRace = :racecode
							   AND rin.TypeReligion IS NOT NULL)
				 ORDER BY rel.Type DESC, rel.Nom ASC";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":racecode", $this->Character->GetRaceCode(), PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		// Build and save result array
		$lReligionList = array();
		foreach($r as $i => $religion) { 
				// Exceptions...
			    if( ($lClassCode == 'CLERC' || $lClassCode == 'PRETRE') && ($religion['Type'] == 'AUTRE' || $religion['Code'] == 'GOLGOTH' 
															|| $religion['Code'] == 'CHAOS') )	{ /* Skip! */ }
			elseif( ($lClassCode == 'ADEPTE') && ($religion['Code'] <> 'ADEMOS' && $religion['Code'] <> 'CHAOS' && $religion['Code'] <> 'GOLGOTH') ){ /* Skip! */ }
			elseif( ($lClassCode == 'SHAMAN') && ($religion['Code'] == 'ADEMOS') )									{ /* Skip! */ }

				// Rule!
			else {
				$lReligionList[$i]['code'] = $religion['Code'];
				$lReligionList[$i]['name'] = $religion['Nom'];
			}
		}

		$this->Character->SetPossibleReligions($lReligionList);
		return True;
	}


	//--GET A PERK'S NAME FROM ITS CODE--
	public function GetSkill( $inSkillCode )
	{
		// Ask the database...
		$lQuery = 	"SELECT creg.Nom, creg.Niveau, creg.Usages
				 FROM db_pilot.competences_regulieres creg
				 WHERE creg.Code = :skillcode";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":skillcode", $inSkillCode, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		$lSkill = array(
				'code' 		=> $inSkillCode,
				'name' 		=> $r[0]['Nom'],
				'level' 	=> $r[0]['Niveau'],
				'type' 		=> 'REG',
				'quantity' 	=> $r[0]['Usages'],
				'xpcost' 	=> 0,
				'date' 		=> NULL,
				'acquisition' 	=> 'DEPART',
				'status' 	=> 'ACTIF',
				'precision'	=> NULL);

		return $lSkill;
	}


	//--GET A NEW CHARACTER'S STARTING KIT--
	public function GetStartingSkillsAndTalents()
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		// Prepare data
		$lUpgradables = array();
		$lTalentList = array();
		$lSkillList = array();

		// -- UPGRADABLE SKILLS --
		// Ask the database
		$lQuery = 	"SELECT creg.Code, 
					creg.Evolution, 
					(SELECT evo.Nom FROM db_pilot.competences_regulieres evo WHERE evo.Code = creg.Evolution) AS NomEvolution
				  FROM db_pilot.competences_regulieres creg 
				  WHERE creg.Evolution IS NOT NULL
				    AND creg.CodeEtat = 'ACTIF';";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult(); 

		foreach ($r as $upgrade) {
			$lUpgradables[ $upgrade['Code'] ] = [	# We what the upgradable skill's code as the array's index (List['VOL1'] = VOL1-data)
				'Evolution'	=> $upgrade['Evolution'], 
				'NomEvolution'	=> $upgrade['NomEvolution']];	
		}


		// -- SPECIAL TALENTS --
		// Ask the database... Talents must go first because of divine powers and such.
		$lQuery = 	"SELECT crac.CodeCompSpec, cspec.Nom, cspec.Type
				 FROM db_pilot.competences_raciales crac 
				 	JOIN db_pilot.competences_speciales cspec ON crac.CodeCompSpec = cspec.Code
				 WHERE crac.CodeRace = :racecode
				   AND crac.CodeCompSpec IS NOT NULL
				UNION ALL
				 SELECT cdep.CodeCompSpec, cspec.Nom, cspec.Type
				 FROM db_pilot.competences_depart cdep 
				 	JOIN db_pilot.competences_speciales cspec ON cdep.CodeCompSpec = cspec.Code
				 WHERE cdep.CodeClasse = :classcode
				   AND cdep.CodeCompSpec IS NOT NULL
				UNION ALL 
				 SELECT carc.CodeCompSpec, cspec.Nom, cspec.Type
				 FROM db_pilot.competences_archetype carc 
				 	JOIN db_pilot.competences_speciales cspec ON carc.CodeCompSpec = cspec.Code
				 WHERE carc.CodeArchetype = :archetypecode
				   AND carc.CodeCompSpec IS NOT NULL;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":racecode", $this->Character->GetRaceCode(), PDO::PARAM_STR);
			$this->DAL->Bind(":classcode", $this->Character->GetClassCode(), PDO::PARAM_STR);
			$this->DAL->Bind(":archetypecode", $this->Character->GetArchetypeCode(), PDO::PARAM_STR);
		$r = $this->DAL->FetchResult(); 

		// Build and save result array
		foreach($r as $talent) {
			$status = 'ACTIF';

			// Add talent to the list
			$lTalentList[] = [
				'code' 		=>	$talent['CodeCompSpec'],
				'name' 		=>	$talent['Nom'],
				'type' 		=>	$talent['Type'],
				'quantity' 	=>	NULL,
				'xpcost' 	=>	0,
				'date' 		=>	NULL,
				'acquisition' 	=> 	'DEPART',
				'status' 	=>	$status,
				'precision' 	=>	NULL
			];
		}
		$this->Character->SetTalents($lTalentList);


		// -- REGULAR SKILLS --
		// Ask the database...
		$lQuery = 	"SELECT crac.CodeCompReg, creg.Nom, creg.Niveau, creg.Usages, '1' AS NbrApplications
				  FROM db_pilot.competences_raciales crac 
				  	JOIN db_pilot.competences_regulieres creg ON crac.CodeCompReg = creg.Code
				  WHERE crac.CodeRace = :racecode
				    AND crac.CodeCompReg IS NOT NULL
				 UNION ALL
				 (SELECT cdep.CodeCompReg, creg.Nom, creg.Niveau, creg.Usages, cdep.NbrApplications
				  FROM db_pilot.competences_depart cdep 
				  	JOIN db_pilot.competences_regulieres creg ON cdep.CodeCompReg = creg.Code
				  WHERE cdep.CodeClasse = :classcode
				    AND cdep.CodeCompReg IS NOT NULL)
				 UNION ALL 
				 (SELECT carc.CodeCompReg, creg.Nom, creg.Niveau, creg.Usages, carc.NbrApplications
				  FROM db_pilot.competences_archetype carc 
				  	JOIN db_pilot.competences_regulieres creg ON carc.CodeCompReg = creg.Code
				  WHERE carc.CodeArchetype = :archetypecode
				   	 AND carc.CodeCompReg IS NOT NULL);";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":racecode", $this->Character->GetRaceCode(), PDO::PARAM_STR);
			$this->DAL->Bind(":classcode", $this->Character->GetClassCode(), PDO::PARAM_STR);
			$this->DAL->Bind(":archetypecode", $this->Character->GetArchetypeCode(), PDO::PARAM_STR);
		$r = $this->DAL->FetchResult(); 

		// Build and save result array
		foreach($r as $skill) {
			for ($i=1; $i <= $skill['NbrApplications']; $i++) {
				$code = $skill['CodeCompReg'];
				$name = $skill['Nom'];
				$level = $skill['Niveau'];
				$status = 'ACTIF';

				//Exception - Upgradables
				if(isset( $lUpgradables[$code] )) {			// If skillcode index exists in Upgradables, then it IS an upgradable skill.
					foreach ($lSkillList as $index => $candidate) {	// Only burden the process with this search if there's a chance there'll be an upgrade.
						if($candidate['code'] == $code) {	// Seek a second instance of the skill and make the necessary changes if one is found.
							$lSkillList[$index]['status'] = 'REMPL';	
							$name = $lUpgradables[$code]['NomEvolution'];
							$code = $lUpgradables[$code]['Evolution'];
						}
					}
				}

				// Add skill to the list
				$lSkillList[] = [
					'code'		=>	$code,
					'name'		=>	$name,
					'level'		=>	$level,
					'type'		=>	'REG',
					'quantity'	=>	$skill['Usages'],
					'xpcost'	=>	0,
					'date'		=>	NULL,
					'acquisition'	=>	'DEPART',
					'status'	=>	$status,
					'precision'	=>	NULL
				];
			}
		}
		$this->Character->SetSkills($lSkillList);

		return True;
	}


	//--GET A NEW CHARACTER'S STARTING LIFE MODS--
	public function GetStartingLife()
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }

		$lLife = array();


		// -- CLASS' STARTING LIFE --
		$lQuery = 	"SELECT clas.PVDepart
				 FROM db_pilot.classes clas
				 WHERE clas.Code = :classcode";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":classcode", $this->Character->GetClassCode(), PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		$lLife[0] = ['reason' => 'PV de départ', 'life' => $r[0]['PVDepart'] ];


		// -- RACE MODIFIER --
		$lTalents = $this->Character->GetTalents();
		foreach( $lTalents as $talent ) {
			    if( $talent['code'] == 'PVBONUS' ) { $lLife[] = ['reason' => 'Ajustement racial', 'life' => 1 ]; }
			elseif( $talent['code'] == 'PVMALUS' ) { $lLife[] = ['reason' => 'Ajustement racial', 'life' => -1 ]; }
		}

		$this->Character->SetLife( $lLife );
		return True;
	}


	//--REGISTER A NEW CHARACTER--
	public function RegisterCharacter($inUserID, $inFirstName, $inLastName, $inRaceCode, $inClassCode, $inArchetypeCode, $inReligion, $inOrigin, $inSkills, $inTalents, $inLife)
	{
		// Check if the character name can be used
		if( $this->CharacterNameExists($inUserID, $inFirstName, $inLastName) ) { $this->Error = "You can't have the same character name twice for the same user!"; return False; }


		// INSERT new character in the database.
		$lQuery = 	"INSERT INTO db_perso.personnages (IdIndividu, Prenom, Nom, CodeRace, CodeClasse, CodeArchetype, Niveau, CodeReligion, Provenance, CodeEtat, DateCreation, CodeUnivers) 
				 VALUES (:userid, :firstname, :lastname, :racecode, :classcode, :archetypecode, '0', :religioncode, :origin, 'NOUVO', sysdate(), :universecode )";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $inUserID, PDO::PARAM_INT);
			$this->DAL->Bind(":firstname", trim($inFirstName), PDO::PARAM_STR);
			$this->DAL->Bind(":lastname", trim($inLastName), PDO::PARAM_STR);
			$this->DAL->Bind(":racecode", $inRaceCode, PDO::PARAM_STR);
			$this->DAL->Bind(":classcode", $inClassCode, PDO::PARAM_STR);
			$this->DAL->Bind(":archetypecode", $inArchetypeCode, PDO::PARAM_STR);
			$this->DAL->Bind(":religioncode", $inReligion, PDO::PARAM_STR);
			$this->DAL->Bind(":origin", trim($inOrigin), PDO::PARAM_STR);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		// Get character Id
		$lQuery = "SELECT LAST_INSERT_ID() AS 'Id' ";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();
		$lCharacterID = $r[0]['Id'];


		// INSERT new character's skills and talents in the database.
		$lQuery = 	"INSERT INTO db_perso.competences_acquises (IdPersonnage, CodeCompetence, Type, Usages, CoutXP, DateCreation, CodeAcquisition, CodeEtat, `Precision`)
				 VALUES "; 

		foreach($inSkills as $i => $skill) {
			$quantity = 'NULL'; if( $skill['quantity'] ) { $quantity = $skill['quantity']; }
			$precision = 'NULL'; if( $skill['precision'] ) { $precision = "'".$skill['precision']."'"; }

			if($i) { $lQuery .= ","; }
			$lQuery .= "(:characterid, '". $skill['code'] ."', 'REG', ". $quantity .", '0', sysdate(), 'DEPART', '".$skill['status']."', ". $precision .")";
		}
		foreach($inTalents as $i => $talent) {
			$precision = 'NULL'; if( $talent['precision'] ) { $precision = "'".$talent['precision']."'"; }

			$lQuery .= ",(:characterid, '". $talent['code']. "', '". $talent['type']. "', NULL, NULL, sysdate(), 'DEPART', '".$talent['status']."', ". $precision .")";
		}

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $lCharacterID, PDO::PARAM_INT);
		$this->DAL->FetchResult();


		// INSERT new character's life mods in the database.
		$lQuery = 	"INSERT INTO db_perso.points_de_vie (IdPersonnage, Raison, PV, DateInscription)
				 VALUES "; 

		foreach($inLife as $i => $lifemod) {
			if($i) { $lQuery .= ","; }
			$lQuery .= "(:characterid, '". $lifemod['reason']. "', ". $lifemod['life']. ", sysdate() )";
		}

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $lCharacterID, PDO::PARAM_INT);
		$this->DAL->FetchResult();


		// INSERT new character's life mods in the database.
		$lQuery = 	"INSERT INTO db_perso.remarques (IdPersonnage, Message, Type, DateCreation)
				 VALUES (:characterid, 'Création du personnage.', 'SYS', sysdate() )"; 

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $lCharacterID, PDO::PARAM_INT);
		$this->DAL->FetchResult();


		return True;
	}


	//--REGISTER A NEW CHARACTER--
	public function RegisterCharacterBase($inUserID, $inFirstName, $inLastName, $inRaceCode, $inClassCode, $inArchetypeCode, $inReligion, $inOrigin)
	{
		// Check if the character name can be used
		if( $this->CharacterNameExists($inUserID, $inFirstName, $inLastName) ) { $this->Error = "You can't have the same character name twice for the same user!"; return False; }


		// INSERT new character in the database.
		$lQuery = 	"INSERT INTO db_perso.personnages (IdIndividu, Prenom, Nom, CodeRace, CodeClasse, CodeArchetype, Niveau, CodeReligion, Provenance, CodeEtat, DateCreation, CodeUnivers) 
				 VALUES (:userid, :firstname, :lastname, :racecode, :classcode, :archetypecode, '0', :religioncode, :origin, 'NOUVO', sysdate(), :universecode )";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $inUserID, PDO::PARAM_INT);
			$this->DAL->Bind(":firstname", trim($inFirstName), PDO::PARAM_STR);
			$this->DAL->Bind(":lastname", trim($inLastName), PDO::PARAM_STR);
			$this->DAL->Bind(":racecode", $inRaceCode, PDO::PARAM_STR);
			$this->DAL->Bind(":classcode", $inClassCode, PDO::PARAM_STR);
			$this->DAL->Bind(":archetypecode", $inArchetypeCode, PDO::PARAM_STR);
			$this->DAL->Bind(":religioncode", $inReligion, PDO::PARAM_STR);
			$this->DAL->Bind(":origin", trim($inOrigin), PDO::PARAM_STR);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		// Get character Id
		$lQuery = "SELECT LAST_INSERT_ID() AS 'Id' ";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();
		$lCharacterID = $r[0]['Id'];


		// INSERT comment in the database.
		$lQuery = 	"INSERT INTO db_perso.remarques (IdPersonnage, Message, Type, DateCreation)
				 VALUES (:characterid, 'Création du personnage.', 'SYS', sysdate() )"; 

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $lCharacterID, PDO::PARAM_INT);
		$this->DAL->FetchResult();


		return True;
	}

//=================================================================== SOFT RESET SERVICES ===================================================================

	//--RESET CHARACTER... SOFTLY--
	public function RegisterCharacterResetData()
	{
		// Get data
		$lCharacterID = $this->Character->GetID();
		$lCharacterArchetypeCode = $this->Character->GetArchetypeCode();
		$lCharacterOrigin = $this->Character->GetOrigin();

		$lCharacterSkills = $this->Character->GetSkills();
		$lCharacterTalents = $this->Character->GetTalents();

		// UPDATE character.
		$lQuery = 	"UPDATE db_perso.personnages 
				 SET CodeArchetype = :archetypecode, Provenance = :origin
				 WHERE Id = :characterid;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $lCharacterID, PDO::PARAM_INT);
			$this->DAL->Bind(":archetypecode", $lCharacterArchetypeCode, PDO::PARAM_STR);
			$this->DAL->Bind(":origin", $lCharacterOrigin, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		// INSERT new character's skills and talents in the database.
		$lQuery = 	"INSERT INTO db_perso.competences_acquises (IdPersonnage, CodeCompetence, Type, Usages, CoutXP, DateCreation, CodeAcquisition, CodeEtat, `Precision`)
				 VALUES "; 

		foreach($lCharacterSkills as $i => $skill) {
			$quantity = 'NULL'; if( $skill['quantity'] ) { $quantity = $skill['quantity']; }
			$precision = 'NULL'; if( $skill['precision'] ) { $precision = "'".$skill['precision']."'"; }

			if($i) { $lQuery .= ","; }
			$lQuery .= "(:characterid, '". $skill['code'] ."', 'REG', ". $quantity .", '0', sysdate(), 'DEPART', '".$skill['status']."', ". $precision .")";
		}
		foreach($lCharacterTalents as $i => $talent) {
			if( $talent['code'] != 'FAVCHAO' && $talent['code'] != 'FAVGOLG' && $talent['code'] != 'RHUONGS' && $talent['code'] != 'MIR888' ) {
				$precision = 'NULL'; if( $talent['precision'] ) { $precision = "'".$talent['precision']."'"; }

				$lQuery .= ",(:characterid, '". $talent['code']. "', '". $talent['type']. "', NULL, NULL, sysdate(), 'DEPART', '".$talent['status']."', ". $precision .")";
			}
		}

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $lCharacterID, PDO::PARAM_INT);
		$this->DAL->FetchResult();


		// INSERT journal entry in the database.
		$lQuery = 	"INSERT INTO db_perso.remarques (IdPersonnage, Message, Type, DateCreation)
				 VALUES (:characterid, 'Réinitialisation du personnage.', 'SYS', sysdate() )"; 

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $lCharacterID, PDO::PARAM_INT);
		$this->DAL->FetchResult();


		return True;
	}


} // END of CharacterServices class

?>
