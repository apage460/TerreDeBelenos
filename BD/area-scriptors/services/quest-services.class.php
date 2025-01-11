<?php

/*
=SCRIPTOR FILE=
╔══CLASS════════════════════════════════════════════════════════╗
║	== Quest Management Services v1.2 r11 ==		║
║	Services used to manage all quests.			║
║	Non-serializable. Requires DAL. Uses MySQL queries.	║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('services/database.class.php'); 		// Data Access Layer
include_once('models/user.class.php'); 			// User definition
include_once('models/questmanager.class.php'); 		// Quest Manager definition
include_once('models/character.class.php'); 		// Character definition
include_once('models/group.class.php'); 		// Group definition
include_once('models/activity.class.php'); 		// Activity definition
include_once('models/resume.class.php'); 		// Resumé definition
include_once('models/survey.class.php'); 		// Survey definition

class QuestServices
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
	public function GetManager() { return $this->Manager; }

	public function SetUser($inUser) { $this->User = $inUser; }
	public function SetManager($inManager) { $this->Manager = $inManager; }


	//--GET ALL QUESTS--
	public function GetAllQuestLists()
	{
		$this->GetPersonalQuestLists();
		$this->GetGroupQuestLists();
		$this->Manager->SetMythicQuests( array() );	# TBD...
	}


	//--GET ALL PERSONAL QUESTS--
	public function GetPersonalQuestLists()
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Manager) ) { $this->Error = "No quest manager!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT que.Id, que.IdPersonnage, per.Prenom AS PrenomPerso, per.Nom AS NomPerso, que.Objet, que.CodeOption, que.CodeRecompense, 
					que.IdComte, cmt.Nom AS NomComte, que.Suggestions, que.Texte, que.CodeEtat, que.DateDemande, que.DateApprobation, 
					que.IdActivite, act.Nom AS NomActivite, que.IdResponsable, ind.Prenom AS PrenomScripteur, ind.Nom AS NomScripteur, que.Commentaires,
					dem.Prenom AS PrenomJoueur, dem.Nom AS NomJoueur, dem.Courriel
				 FROM db_perso.quetes que 
				 	LEFT JOIN db_histo.comtes cmt ON que.IdComte = cmt.Id
				 	LEFT JOIN db_activ.activites act ON que.IdActivite = act.Id
				 	LEFT JOIN db_perso.personnages per ON que.IdPersonnage = per.Id
				 	LEFT JOIN db_indiv.individus ind ON que.IdResponsable = ind.Id
				 	LEFT JOIN db_indiv.individus dem ON per.IdIndividu = dem.Id
				 WHERE que.CodeEtat <> 'SUPPR'
				   AND per.CodeUnivers = :universecode
				 ORDER BY que.Id ASC;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		// Build quest list
		$lQuestList = array();
		foreach ($r as $quest) {
			// Create scriptor's user object and the character's object
			$lActivity = new Activity( ['id' => $quest['IdActivite'], 'name' => $quest['NomActivite']] );
			$lScriptor = new User( array('id' => $quest['IdResponsable'], 'firstname' => $quest['PrenomScripteur'], 'lastname' => $quest['NomScripteur']) );
			$lApplicant = new User( array('firstname' => $quest['PrenomJoueur'], 'lastname' => $quest['NomJoueur'], 'mail' => $quest['Courriel']) );
			$lCharacter = new Character( array('id' => $quest['IdPersonnage'], 'firstname' => $quest['PrenomPerso'], 'lastname' => $quest['NomPerso']) );

			$attributes = [
					'id'			=>	$quest['Id'],
					'type'			=>	'PERSONAL',
					'character'		=>	$lCharacter,
					'applicant'		=>	$lApplicant,
					'status'		=>	$quest['CodeEtat'],
					'subject'		=>	$quest['Objet'],
					'optioncode'		=>	$quest['CodeOption'],
					'rewardcode'		=>	$quest['CodeRecompense'],
					'countyid'		=>	$quest['IdComte'],
					'countyname'		=>	$quest['NomComte'],
					'suggestions'		=>	$quest['Suggestions'],
					'text'			=>	$quest['Texte'],
					'requestdate'		=>	$quest['DateDemande'],
					'approvaldate'		=>	$quest['DateApprobation'],
					'activity'		=>	$lActivity,
					'scriptor'		=>	$lScriptor,
					'comments'		=>	$quest['Commentaires']
			];

			$lQuestList[] = new Quest( $attributes );
		}

		$this->Manager->SetPersonalQuests( $lQuestList );

		return True;
	}


	//--GET ALL PERSONAL QUESTS--
	public function GetGroupQuestLists()
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Manager) ) { $this->Error = "No quest manager!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT que.Id, que.IdGroupe, grp.Nom AS NomGroupe, que.Objet, que.CodeOption, que.CodeRecompense, que.IdComte, cmt.Nom AS NomComte, 
					que.Suggestions, que.Texte, que.CodeEtat, que.DateDemande, que.IdDemandeur, que.IdActivite, que.DateApprobation, que.IdResponsable, 
					ind.Prenom AS PrenomScripteur, ind.Nom AS NomScripteur, que.Commentaires,
					dem.Prenom AS PrenomJoueur, dem.Nom AS NomJoueur, dem.Courriel,
					que.IdActivite, act.Nom AS NomActivite
				 FROM db_group.quetes que 
				 	LEFT JOIN db_histo.comtes cmt ON que.IdComte = cmt.Id
				 	LEFT JOIN db_group.groupes grp ON que.IdGroupe = grp.Id
					LEFT JOIN db_activ.activites act ON que.IdActivite = act.Id
				 	LEFT JOIN db_indiv.individus ind ON que.IdResponsable = ind.Id
				 	LEFT JOIN db_indiv.individus dem ON que.IdDemandeur = dem.Id
				 WHERE que.CodeEtat <> 'SUPPR'
				   AND grp.CodeUnivers = :universecode
				 ORDER BY que.Id ASC;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		// Build quest list
		$lQuestList = array();
		foreach ($r as $quest) {
			// Create scriptor's user object and the character's object
			$lScriptor = new User( array('id' => $quest['IdResponsable'], 'firstname' => $quest['PrenomScripteur'], 'lastname' => $quest['NomScripteur']) );
			$lApplicant = new User( array('id' => $quest['IdDemandeur'],'firstname' => $quest['PrenomJoueur'], 'lastname' => $quest['NomJoueur'], 'mail' => $quest['Courriel']) );
			$lActivity = new Activity( ['id' => $quest['IdActivite'], 'name' => $quest['NomActivite']] );
			$lGroup = new Group( array('id' => $quest['IdGroupe'], 'name' => $quest['NomGroupe']) );

			$attributes = [
					'id'			=>	$quest['Id'],
					'type'			=>	'GROUP',
					'group'			=>	$lGroup,
					'applicant'		=>	$lApplicant,
					'status'		=>	$quest['CodeEtat'],
					'subject'		=>	$quest['Objet'],
					'optioncode' 		=> 	$quest['CodeOption'],
					'rewardcode' 		=> 	$quest['CodeRecompense'],
					'countyid' 		=> 	$quest['IdComte'],
					'countyname' 		=> 	$quest['NomComte'],
					'suggestions'		=> 	$quest['Suggestions'],
					'text'			=> 	$quest['Texte'],
					'requestdate'		=>	$quest['DateDemande'],
					'approvaldate'		=>	$quest['DateApprobation'],
					'activity'		=>	$lActivity,
					'scriptor'		=>	$lScriptor,
					'comments'		=>	$quest['Commentaires']
			];

			$lQuestList[] = new Quest( $attributes );
		}

		$this->Manager->SetGroupQuests( $lQuestList );

		return True;
	}


	//--GET ONE SELECTED PERSONAL QUEST--
	public function GetSelectedPersonalQuest($inQuestID)
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Manager) ) { $this->Error = "No quest manager!"; return False; }


		//-- BASE INFORMATION --
		// Ask the database...
		$lQuery = 	"SELECT que.Id, que.IdPersonnage, per.Prenom AS PrenomPerso, per.Nom AS NomPerso, que.Objet, que.CodeOption, que.CodeRecompense, 
					que.IdComte, cmt.Nom AS NomComte, que.Suggestions, que.Texte, que.CodeEtat, que.DateDemande, que.DateApprobation, 
					que.IdActivite, act.Nom AS NomActivite, que.IdResponsable, ind.Prenom AS PrenomScripteur, ind.Nom AS NomScripteur, que.Commentaires,
					dem.Prenom AS PrenomJoueur, dem.Nom AS NomJoueur, dem.Courriel
				 FROM db_perso.quetes que 
				 	LEFT JOIN db_histo.comtes cmt ON que.IdComte = cmt.Id
				 	LEFT JOIN db_activ.activites act ON que.IdActivite = act.Id
				 	LEFT JOIN db_perso.personnages per ON que.IdPersonnage = per.Id
				 	LEFT JOIN db_indiv.individus ind ON que.IdResponsable = ind.Id
				 	LEFT JOIN db_indiv.individus dem ON per.IdIndividu = dem.Id
				 WHERE que.Id = :questid ;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":questid", $inQuestID, PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();

		// Build quest object
		$lActivity = new Activity( ['id' => $r[0]['IdActivite'], 'name' => $r[0]['NomActivite']] );
		$lScriptor = new User( array('id' => $r[0]['IdResponsable'], 'firstname' => $r[0]['PrenomScripteur'], 'lastname' => $r[0]['NomScripteur']) );
		$lApplicant = new User( array('firstname' => $r[0]['PrenomJoueur'], 'lastname' => $r[0]['NomJoueur'], 'mail' => $r[0]['Courriel']) );
		$lCharacter = new Character( array('id' => $r[0]['IdPersonnage'], 'firstname' => $r[0]['PrenomPerso'], 'lastname' => $r[0]['NomPerso']) );

		$attributes = [
				'id'			=>	$r[0]['Id'],
				'type'			=>	'PERSONAL',
				'character'		=>	$lCharacter,
				'applicant'		=>	$lApplicant,
				'status'		=>	$r[0]['CodeEtat'],
				'subject'		=>	$r[0]['Objet'],
				'optioncode'		=>	$r[0]['CodeOption'],
				'rewardcode'		=>	$r[0]['CodeRecompense'],
				'countyid'		=>	$r[0]['IdComte'],
				'countyname'		=>	$r[0]['NomComte'],
				'suggestions'		=>	$r[0]['Suggestions'],
				'text'			=>	$r[0]['Texte'],
				'requestdate'		=>	$r[0]['DateDemande'],
				'approvaldate'		=>	$r[0]['DateApprobation'],
				'activity'		=>	$lActivity,
				'scriptor'		=>	$lScriptor,
				'comments'		=>	$r[0]['Commentaires']
		];

		$this->Manager->SetSelectedQuest( new Quest($attributes) );

		return True;
	}


	//--GET ONE SELECTED GROUP QUEST--
	public function GetSelectedGroupQuest($inQuestID)
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Manager) ) { $this->Error = "No quest manager!"; return False; }


		//-- BASE INFORMATION --
		// Ask the database...
		$lQuery = 	"SELECT que.Id, que.IdGroupe, grp.Nom AS NomGroupe, que.Objet, que.CodeOption, que.CodeRecompense, que.IdComte, cmt.Nom AS NomComte, 
					que.Suggestions, que.Texte, que.CodeEtat, que.DateDemande, que.IdDemandeur, que.IdActivite, que.DateApprobation, que.IdResponsable, 
					ind.Prenom AS PrenomScripteur, ind.Nom AS NomScripteur, que.Commentaires,
					dem.Prenom AS PrenomJoueur, dem.Nom AS NomJoueur, dem.Courriel,
					que.IdActivite, act.Nom AS NomActivite
				 FROM db_group.quetes que 
				 	LEFT JOIN db_histo.comtes cmt ON que.IdComte = cmt.Id
				 	LEFT JOIN db_group.groupes grp ON que.IdGroupe = grp.Id
					LEFT JOIN db_activ.activites act ON que.IdActivite = act.Id
				 	LEFT JOIN db_indiv.individus ind ON que.IdResponsable = ind.Id
				 	LEFT JOIN db_indiv.individus dem ON que.IdDemandeur = dem.Id
				 WHERE que.Id = :questid ;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":questid", $inQuestID, PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();

		// Build quest object
		$lScriptor = new User( array('id' => $r[0]['IdResponsable'], 'firstname' => $r[0]['PrenomScripteur'], 'lastname' => $r[0]['NomScripteur']) );
		$lApplicant = new User( array('id' => $r[0]['IdDemandeur'], 'firstname' => $r[0]['PrenomJoueur'], 'lastname' => $r[0]['NomJoueur'], 'mail' => $r[0]['Courriel']) );
		$lActivity = new Activity( ['id' => $r[0]['IdActivite'], 'name' => $r[0]['NomActivite']] );
		$lGroup = new Group( array('id' => $r[0]['IdGroupe'], 'name' => $r[0]['NomGroupe']) );

		$attributes = [
				'id'			=>	$r[0]['Id'],
				'type'			=>	'GROUP',
				'group'			=>	$lGroup,
				'applicant'		=>	$lApplicant,
				'status'		=>	$r[0]['CodeEtat'],
				'subject'		=>	$r[0]['Objet'],
				'optioncode' 		=> 	$r[0]['CodeOption'],
				'rewardcode' 		=> 	$r[0]['CodeRecompense'],
				'countyid' 		=> 	$r[0]['IdComte'],
				'countyname' 		=> 	$r[0]['NomComte'],
				'suggestions'		=> 	$r[0]['Suggestions'],
				'text'			=> 	$r[0]['Texte'],
				'requestdate'		=>	$r[0]['DateDemande'],
				'approvaldate'		=>	$r[0]['DateApprobation'],
				'activity'		=>	$lActivity,
				'scriptor'		=>	$lScriptor,
				'comments'		=>	$r[0]['Commentaires']
		];

		$lQuest = new Quest($attributes);
		$this->Manager->SetSelectedQuest( $lQuest );

		return True;
	}


	//--GET CHARACTER'S BASE INFORMATIONS--
	public function GetCharacterDetails($inQuestID)
	{
		// Prepare data
		$this->Manager->SelectQuest('PERSONAL', $inQuestID);
		$lQuest = $this->Manager->GetSelectedQuest();
		$lCharacter = $lQuest->GetCharacter();


		//-- BASE INFORMATION --
		// Ask the database ...
		$lQuery = 	"SELECT rac.Nom AS Race, clas.Nom AS Classe, per.Niveau, rel.Nom AS Religion, per.Provenance, per.Histoire, per.CodeEtat,
					mbr.IdGroupe, grp.Nom AS NomGroupe, grp.Description
				 FROM db_perso.quetes que
				 	     JOIN db_perso.personnages per ON que.IdPersonnage = per.Id
					LEFT JOIN db_pilot.races rac ON per.CodeRace = rac.Code
					LEFT JOIN db_pilot.classes clas ON per.CodeClasse = clas.Code
					LEFT JOIN db_pilot.religions rel ON per.CodeReligion = rel.Code
					LEFT JOIN db_group.membres mbr ON per.Id = mbr.IdPersonnage
					LEFT JOIN db_group.groupes grp ON mbr.IdGroupe = grp.Id
				 WHERE que.Id = :questid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":questid", $inQuestID, PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();

		// Save results in the character object
		if($r) {
			$lCharacterGroup = new Group([ 'id' => $r[0]['IdGroupe'], 'name' => $r[0]['NomGroupe'], 'description' => $r[0]['Description'] ]);

			$lCharacter->SetGroup($lCharacterGroup);
			$lCharacter->SetRace($r[0]['Race']);
			$lCharacter->SetClass($r[0]['Classe']);
			$lCharacter->SetLevel($r[0]['Niveau']);
			$lCharacter->SetReligion($r[0]['Religion']);
			$lCharacter->SetOrigin($r[0]['Provenance']);
			$lCharacter->SetBackground($r[0]['Histoire']);
			$lCharacter->SetStatus($r[0]['CodeEtat']);
		}
		else { return False;}


		//-- SKILLS --
		$lQuery = 	"SELECT cac.Id, cac.CodeCompetence, creg.Nom, creg.Niveau, cac.Type, cac.Usages, cac.CodeEtat, 
					creg.IndPrecision, cac.Precision
				 FROM db_perso.competences_acquises cac JOIN db_pilot.competences_regulieres creg 
					ON cac.CodeCompetence = creg.Code
				 WHERE cac.IdPersonnage = :characterid
				   AND cac.CodeEtat NOT IN ('INACT', 'SUPPR')
				   AND cac.Type = 'REG'
				 ORDER BY creg.Nom ASC, cac.Precision ASC";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $lCharacter->GetID(), PDO::PARAM_INT);
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
				'status'	=> 	$skill['CodeEtat'],
				'precisable'	=> 	$skill['IndPrecision'],
				'precision' 	=> 	$skill['Precision'],
			];
		}

		$lCharacter->SetSkills($lSkillList);


		//-- TALENTS --
		// Ask the database...
		$lQuery = 	"SELECT cac.Id, cac.CodeCompetence, cspec.Nom, cspec.Type, cac.CodeEtat
				 FROM db_perso.competences_acquises cac JOIN db_pilot.competences_speciales cspec 
					ON cac.CodeCompetence = cspec.Code
				 WHERE cac.IdPersonnage = :characterid
				   AND cac.CodeEtat <> 'INACT'
				   AND cac.Type <> 'REG'";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $lCharacter->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Build and save result array
		$lTalentList = array();

		foreach($r as $talent) { 
			$lTalentList[] = [
				'id' 		=> 	$talent['Id'],
				'code'		=> 	$talent['CodeCompetence'],
				'name'		=> 	$talent['Nom'],
				'type' 		=> 	$talent['Type'],
				'status'	=> 	$talent['CodeEtat']
			];
		}

		$lCharacter->SetTalents($lTalentList);


		// Set data
		$lQuest->SetCharacter( $lCharacter );
		$this->Manager->SetSelectedQuest( $lQuest );
		$this->Manager->SetCHaracter( $lCharacter );

		return True;
	}


	//--GET GROUP INFORMATIONS--
	public function GetGroupDetails($inQuestID)
	{
		// Prepare data
		$lQuest = $this->Manager->GetSelectedQuest();
		$lGroup = $lQuest->GetGroup();


		//-- BASE INFORMATION --
		// Ask the database ...
		$lQuery = 	"SELECT que.IdGroupe, grp.Nom, grp.Description, grp.Chefferie, grp.Historique, grp.Nobles, grp.CodeEtat,
					grp.CodeSpecialisation, spec.Nom AS NomSpecialisation,
					camp.Nom AS NomCampement
				 FROM db_group.quetes que
				 	     JOIN db_group.groupes grp ON que.IdGroupe = grp.Id
					LEFT JOIN db_pilot.specialisations spec ON grp.CodeSpecialisation = spec.Code
					LEFT JOIN db_group.camps_occupes cocp ON cocp.IdGroupe = grp.Id AND cocp.CodeEtat = 'ACTIF'
					LEFT JOIN db_pilot.campements camp ON cocp.CodeCamp = camp.Code
				 WHERE que.Id = :questid;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":questid", $inQuestID, PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();

		// Save results in the character object
		if($r) {
			$lGroup->SetDescription($r[0]['Description']);
			$lGroup->SetLeadership($r[0]['Chefferie']);
			$lGroup->SetBackground($r[0]['Historique']);
			$lGroup->SetAffiliatedNobles($r[0]['Nobles']);
			$lGroup->SetSpecialization($r[0]['NomSpecialisation']);
			$lGroup->SetBaseCamp($r[0]['NomCampement']);
			$lGroup->SetStatus($r[0]['CodeEtat']);
		}
		else { return False;}


		//-- MEMBERS --
		$lQuery = 	"SELECT mbr.IdPersonnage, per.Prenom, per.Nom, rac.Nom AS Race, per.Niveau, per.CodeEtat, 
					per.IdIndividu, CONCAT(ind.Prenom, ' ', ind.Nom) AS NomJoueur, ind.Compte
				 FROM db_group.membres mbr 
					LEFT JOIN db_perso.personnages per ON mbr.IdPersonnage = per.Id
					LEFT JOIN db_indiv.individus ind ON per.IdIndividu = ind.Id
					     JOIN db_pilot.races rac ON per.CodeRace = rac.Code
				 WHERE mbr.IdGroupe = :groupid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupid", $lGroup->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Build and save result array
		$lMemberList = array();
		foreach($r as $member) { 
			$attributes = [
				'id'		=> 	$member['IdPersonnage'],
				'firstname'	=> 	$member['Prenom'],
				'lastname'	=> 	$member['Nom'],
				'race'		=> 	$member['Race'],
				'level'		=> 	$member['Niveau'],
				'status'	=> 	$member['CodeEtat'],
				'userid'	=> 	$member['IdIndividu'],
				'useraccount'	=> 	$member['NomJoueur'],
				'username'	=>	$member['Compte']
			];

			$lMemberList[] = new Character( $attributes );
		}

		$lGroup->SetMembers($lMemberList);


		//-- PEOPLE IN CHARGE --
		$lQuery = 	"SELECT rgr.IdResponsable, ind.Compte, ind.Prenom, ind.Nom
				 FROM db_group.responsables_groupe rgr JOIN db_indiv.individus ind ON rgr.IdResponsable = ind.Id
				 WHERE rgr.IdGroupe = :groupid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupid", $lGroup->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();

		$lPersonList = array();
		foreach ($r as $i => $person) { 
			$attributes = [
				'id' 		=> 	$person['IdResponsable'],
				'account' 	=> 	$person['Compte'],
				'firstname' 	=> 	$person['Prenom'],
				'lastname' 	=> 	$person['Nom']
			];

			$lPersonList[$i] = new User($attributes); 
		}

		$lGroup->SetPeopleInCharge($lPersonList);


		//-- INSTITUTIONS --
		$lQuery = 	"SELECT instig.Id, instig.Nom, instig.CodeProfil, instig.Niveau, instig.IdComte, instig.Chef, instig.Description, instig.AgendaCache, instig.CodeEtat
				 FROM db_group.institutions instig 
				 WHERE instig.IdGroupe = :groupid
				   AND instig.CodeEtat = 'ACTIF';";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupid", $lGroup->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();

		// Build and save result array
		$lInstitutionList = array();
		foreach($r as $institution) { 
			$attributes = [
				'id'			=> 	$institution['Id'],
				'name'			=> 	$institution['Nom'],
				'profile' 		=> 	$institution['CodeProfil'],
				'level'			=> 	$institution['Niveau'],
				'countyid' 		=> 	$institution['IdComte'],
				'leader'		=>	$institution['Chef'],
				'description'		=> 	$institution['Description'],
				'hiddenagenda'		=> 	$institution['AgendaCache'],
				'status'		=> 	$institution['CodeEtat']
			];

			$lInstitutionList[] = new Institution( $attributes );
		}

		$lGroup->SetInstitutions( $lInstitutionList );


		//-- RESUMÉS --
		$lQuery = 	"SELECT resu.Id, resu.IdActivite, act.Nom, resu.Resume, resu.IdQuete, que.Objet, resu.DateCreation
				 FROM db_group.resumes resu
				 	JOIN db_activ.activites act ON resu.IdActivite = act.Id
				 	LEFT JOIN db_group.quetes que ON resu.IdQuete = que.Id
				 WHERE resu.IdGroupe = :groupid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupid", $lGroup->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Build and save result array
		$lResumeList = array();
		foreach($r as $resume) { 
			$lActivity = new Activity( ['id' => $resume['IdActivite'], 'name' => $resume['Nom']] );
			$lQuest = new Quest( ['id' => $resume['IdQuete'], 'subject' =>$resume['Objet']] );
			$attributes = [
				'id'		=> 	$resume['Id'],
				'activity'	=> 	$lActivity,
				'quest'		=> 	$lQuest,
				'creationdate'	=> 	$resume['DateCreation'],
				'text'		=>	$resume['Resume']
			];

			$lResumeList[] = new Resume( $attributes );
		}

		$lGroup->SetResumes($lResumeList);

		// To get the copy we made of the group back to the selected quest
		$this->Manager->GetSelectedQuest()->SetGroup( $lGroup );
		$this->Manager->SetGroup( $lGroup );

		return True;
	}


	//--GET PERSONAL RESUMES--
	public function GetPersonalResumes($inQuestID)
	{
		// Prepare data
		$this->Manager->SelectQuest('PERSONAL', $inQuestID);
		$lQuest = $this->Manager->GetSelectedQuest();
		$lCharacterID = $lQuest->GetCharacter()->GetID();

		// Ask the database ...
		$lQuery = 	"SELECT resu.Id, resu.IdActivite, act.Nom, resu.Resume, resu.IdQuete, que.Objet, resu.DateCreation
				 FROM db_perso.resumes resu
					LEFT JOIN db_perso.quetes que ON resu.IdQuete = que.Id
					LEFT JOIN db_activ.activites act ON resu.IdActivite = act.Id
				 WHERE resu.IdPersonnage = :characterid ;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $lCharacterID, PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Build and save result array
		$lResumeList = array();
		foreach($r as $resume) { 
			$lActivity = new Activity( ['id' => $resume['IdActivite'], 'name' => $resume['Nom']] );
			$lQuest = new Quest( ['id' => $resume['IdQuete'], 'subject' =>$resume['Objet']] );
			$attributes = [
				'id'		=> 	$resume['Id'],
				'activity'	=> 	$lActivity,
				'quest'		=> 	$lQuest,
				'creationdate'	=> 	$resume['DateCreation'],
				'text'		=>	$resume['Resume']
			];

			$lResumeList[] = new Resume( $attributes );
		}

		$this->Manager->SetResumes($lResumeList);

		return True;
	}


	//--GET GROUP RESUMES--
	public function GetGroupResumes($inQuestID)
	{
		// Prepare data
		$lQuest = $this->Manager->GetSelectedQuest();

		// Ask the database ...
		$lQuery = 	"SELECT resu.Id, resu.IdActivite, act.Nom, resu.Resume, resu.IdQuete, que.Objet, resu.IdPartie, resu.DateCreation
				 FROM db_group.quetes que
					LEFT JOIN db_group.resumes resu ON que.IdGroupe = resu.IdGroupe	
					LEFT JOIN db_activ.activites act ON resu.IdActivite = act.Id
				 WHERE resu.IdQuete = :questid ;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":questid", $inQuestID, PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Build and save result array
		$lResumeList = array();
		foreach($r as $resume) { 
			$lActivity = new Activity( ['id' => $resume['IdActivite'], 'name' => $resume['Nom']] );
			$lQuest = new Quest( ['id' => $resume['IdQuete'], 'subject' =>$resume['Objet']] );
			#$lPart = new QuestPart( ['id' => $resume['IdPartie'], 'number' => $resume['Partie']] );
			$attributes = [
				'id'		=> 	$resume['Id'],
				'activity'	=> 	$lActivity,
				'quest'		=> 	$lQuest,
				#'questpart'	=> 	$lPart,
				'creationdate'	=> 	$resume['DateCreation'],
				'text'		=>	$resume['Resume']
			];

			$lResumeList[] = new Resume( $attributes );
		}

		$this->Manager->SetResumes($lResumeList);

		return True;
	}


	//--GET SCRIPTOR LIST--
	public function GetScriptorList()
	{
		// Verify there's a manager
		if( !isset($this->Manager) ) { $this->Error = "No quest manager!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT ind.Id, ind.Prenom, ind.Nom, ind.NiveauAcces 
				 FROM db_indiv.individus ind
				 LEFT JOIN db_indiv.acces acc ON ind.Id = acc.IdIndividu
				 WHERE acc.Acces = 'Scripteur'
				   AND ind.CodeEtat = 'ACTIF'
				 ORDER BY ind.NiveauAcces ASC, ind.Prenom ASC;";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();

		$lScriptorList = array();
		if($r) {
			foreach ($r as $scriptor) {
				$attributes = [
						'id'		=>	$scriptor['Id'],
						'firstname'	=>	$scriptor['Prenom'],
						'lastname'	=>	$scriptor['Nom'],
						'accesslevel'	=>	$scriptor['NiveauAcces']
				];

				$lScriptorList[] = new User( $attributes );
			}
		}

		$this->Manager->SetScriptors( $lScriptorList );

		return True;
	}


	//--GET TITLE LIST--
	public function GetTitleList()
	{
		// Verify there's a manager
		if( !isset($this->Manager) ) { $this->Error = "No quest manager!"; return False; }

		// Ask the database...
		$lQuery = 	"SELECT tit.Code, tit.Nom, tit.Description, tit.Avantages, tit.CodePouvoirBase
				 FROM db_pilot.titres tit
				 WHERE tit.Type = 'TITREP'
				   AND tit.CodeEtat = 'ACTIF'
				 ORDER BY tit.Nom ASC;";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();

		$lTitleList = array();
		if($r) {
			foreach ($r as $title) {
				$attributes = [
						'code'		=>	$title['Code'],
						'name'		=>	$title['Nom'],
						'description'	=>	$title['Description'],
						'advantages'	=>	$title['Avantages'],
						'specialpower'	=>	$title['CodePouvoirBase']
				];

				$lTitleList[] = $attributes;
			}
		}

		$this->Manager->SetTitles( $lTitleList );

		return True;
	}


	//--UPDATE EXISTING QUEST'S SCRIPTOR--
	public function UpdateQuestScriptor( $inScriptorID )
	{
		// Verify there's a manager
		if( !isset($this->Manager) ) { $this->Error = "No quest manager!"; return False; }


		// Ask the database to update data...
		if( $this->Manager->GetSelectedQuest()->GetType() == 'PERSONAL' ) {
			$lQuery = "UPDATE db_perso.quetes que
				   SET que.IdResponsable = :scriptorid
				   WHERE que.Id = :questid;";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":questid", $this->Manager->GetSelectedQuest()->GetID(), PDO::PARAM_INT);
				$this->DAL->Bind(":scriptorid", $inScriptorID, PDO::PARAM_INT);
			$r = $this->DAL->FetchResult();

			if(!$r) { return False; }		
		}
		elseif( $this->Manager->GetSelectedQuest()->GetType() == 'GROUP' ) {
			$lQuery = "UPDATE db_group.quetes que
				   SET que.IdResponsable = :scriptorid
				   WHERE que.Id = :questid;";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":questid", $this->Manager->GetSelectedQuest()->GetID(), PDO::PARAM_INT);
				$this->DAL->Bind(":scriptorid", $inScriptorID, PDO::PARAM_INT);
			$r = $this->DAL->FetchResult();

			if(!$r) { return False; }		
		}
		return True;
	}


	//--UPDATE EXISTING QUEST'S STATUS--
	public function UpdateQuestStatus( $inStatus )
	{
		// Verify there's a manager
		if( !isset($this->Manager) ) { $this->Error = "No quest manager!"; return False; }


		// Prepare data
		$lQuest = $this->Manager->GetSelectedQuest();

		$lApproval = False;
			if( $inStatus == 'ACTIF' && !$lQuest->GetApprovalDate() ) { $lApproval = True; }


		// Ask the database to update data...
		if( $lQuest->GetType() == 'PERSONAL' ) {
			$lQuery = 	"UPDATE db_perso.quetes que
					 SET que.CodeEtat = :status
					 WHERE que.Id = :questid;";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":questid", $lQuest->GetID(), PDO::PARAM_INT);
				$this->DAL->Bind(":status", $inStatus, PDO::PARAM_STR);
			$r = $this->DAL->FetchResult();

			if(!$r) { return False; }

			// Approval registering
			if ($lApproval) {
				$lQuery = 	"UPDATE db_perso.quetes que
						 SET que.DateApprobation = sysdate()
						 WHERE que.Id = :questid;";

				$this->DAL->SetQuery($lQuery);
					$this->DAL->Bind(":questid", $lQuest->GetID(), PDO::PARAM_INT);
				$this->DAL->FetchResult();
			}
		}
		elseif( $lQuest->GetType() == 'GROUP' ) {
			$lQuery = 	"UPDATE db_group.quetes que
					 SET que.CodeEtat = :status
					 WHERE que.Id = :questid;";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":questid", $lQuest->GetID(), PDO::PARAM_INT);
				$this->DAL->Bind(":status", $inStatus, PDO::PARAM_STR);
			$r = $this->DAL->FetchResult();

			if(!$r) { return False; }

			// Approval registering
			if ($lApproval) {
				$lQuery = 	"UPDATE db_group.quetes que
						 SET que.DateApprobation = sysdate()
						 WHERE que.Id = :questid;";

				$this->DAL->SetQuery($lQuery);
					$this->DAL->Bind(":questid", $lQuest->GetID(), PDO::PARAM_INT);
				$this->DAL->FetchResult();
			}
		}

		return True;
	}


	//--UPDATE EXISTING QUEST'S SCRIPTOR--
	public function UpdateQuestTexts( $inText, $inComments )
	{
		// Verify there's a manager
		if( !isset($this->Manager) ) { $this->Error = "No quest manager!"; return False; }


		// Ask the database to update data...
		if( $this->Manager->GetSelectedQuest()->GetType() == 'PERSONAL' ) {
			$lQuery = 	"UPDATE db_perso.quetes que
					 SET que.Texte = :questtext, que.Commentaires = :comments
					 WHERE que.Id = :questid;";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":questid", $this->Manager->GetSelectedQuest()->GetID(), PDO::PARAM_INT);
				$this->DAL->Bind(":questtext", $inText, PDO::PARAM_STR);
				$this->DAL->Bind(":comments", $inComments, PDO::PARAM_STR);
			$r = $this->DAL->FetchResult();

			if(!$r) { return False; }
		}
		elseif( $this->Manager->GetSelectedQuest()->GetType() == 'GROUP' ) {
			$lQuery = 	"UPDATE db_group.quetes que
					 SET que.Texte = :questtext, que.Commentaires = :comments
					 WHERE que.Id = :questid;";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":questid", $this->Manager->GetSelectedQuest()->GetID(), PDO::PARAM_INT);
				$this->DAL->Bind(":questtext", $inText, PDO::PARAM_STR);
				$this->DAL->Bind(":comments", $inComments, PDO::PARAM_STR);
			$r = $this->DAL->FetchResult();

			if(!$r) { return False; }
		}

		return True;
	}


	//--GIVE QUEST REWARD--
	public function GivePersonalQuestReward( $inStageReward =0 )
	{
		// Verify there's a manager
		if( !isset($this->Manager) ) { $this->Error = "No quest manager!"; return False; }


		// Prepare data
		$lQuest = $this->Manager->GetSelectedQuest();
		$lOption = $lQuest->GetOptionCode();
		$lReward = $lQuest->GetRewardCode();


		// Determine case
		    if( $lOption == 'GAINXP' ) 	{ $this->GiveXPReward( $inStageReward*10 ); }
		elseif( $lOption == 'MINEURE' ) { $this->GiveCreditReward($lOption, $lReward); }
		elseif( $lOption == 'MAJEURE' ) { $this->GiveCreditReward($lOption, $lReward); }
		elseif( $lOption == 'BARON' ) 	{ $this->GiveCreditReward('BARON'); }
		elseif( $lOption == 'TITREP' ) 	{ $this->GiveTitleReward($lReward); }
		elseif( $lOption == 'RELIQUE' ) { $this->GiveRelicReward(); }
		else { $this->Error = "Option de récompense invalide. Contacter un DBA."; return False; }


		// Update quest status
		$lQuery = 	"UPDATE db_perso.quetes que
				 SET que.CodeEtat = 'RECOM'
				 WHERE que.Id = :questid;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":questid", $lQuest->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();

		if($r) { return True; }

		$this->Error = "L'état de la quête n'a pas pu être mise à jour. Contacter un DBA."; 
		return False;
	}


	//--GIVE XP REWARD--
	public function GiveXPReward( $inAmount )
	{
		// Prepare data
		$lQuest = $this->Manager->GetSelectedQuest();
		$lCharacter = $lQuest->GetCharacter();


		// Insert XP row in the database...
		$lQuery = 	"INSERT INTO db_perso.experience (IdPersonnage, Raison, XP, DateInscription)
				 VALUES (:characterid, :reason, :xp, sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $lCharacter->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":reason", 'Quête - Gain d\'expérience', PDO::PARAM_STR);
			$this->DAL->Bind(":xp", $inAmount, PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();

		if($r) { return True; }

		return False;
	}


	//--GIVE CREDIT REWARD--
	public function GiveCreditReward( $inOptionCode, $inRewardCode =NULL )
	{
		// Prepare data
		$lQuest = $this->Manager->GetSelectedQuest();
		$lCharacter = $lQuest->GetCharacter();


		// Get the skill code associated with the subject...
		$lQuery = 	"SELECT cq.Id
				 FROM db_perso.credits_quete cq
				 WHERE cq.IdPersonnage = :characterid
				   AND cq.CodeOption = :optioncode
				   AND cq.CodeRecompense = :rewardcode ;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $lCharacter->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":optioncode", $inOptionCode, PDO::PARAM_STR);
			$this->DAL->Bind(":rewardcode", $inRewardCode, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		// Insert or Update
		if(!$r) { 
			$lQuery = 	"INSERT INTO db_perso.credits_quete (IdPersonnage, CodeOption, CodeRecompense, Credits)
					 VALUES (:characterid, :optioncode, :rewardcode, 1);";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":characterid", $lCharacter->GetID(), PDO::PARAM_INT);
				$this->DAL->Bind(":optioncode", $inOptionCode, PDO::PARAM_STR);
				$this->DAL->Bind(":rewardcode", $inRewardCode, PDO::PARAM_STR);
			$r = $this->DAL->FetchResult();

			if(!$r) { return False; }
		}
		else {
			$lQuery = 	"UPDATE db_perso.credits_quete 
					 SET Credits = Credits + 1
					 WHERE IdPersonnage = :characterid
					   AND CodeOption = :optioncode
					   AND CodeRecompense = :rewardcode ;";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":characterid", $lCharacter->GetID(), PDO::PARAM_INT);
				$this->DAL->Bind(":optioncode", $inOptionCode, PDO::PARAM_STR);
				$this->DAL->Bind(":rewardcode", $inRewardCode, PDO::PARAM_STR);
			$r = $this->DAL->FetchResult();

			if(!$r) { return False; }
		}

		return True;
	}


	//--GIVE TITLE REWARD--
	public function GiveTitleReward( $inTitleCode )
	{
		// Prepare data
		$lQuest = $this->Manager->GetSelectedQuest();
		$lCharacter = $lQuest->GetCharacter();
		$lTitle = $this->Manager->GetTitle( $inTitleCode );
			if( $lTitle === False ) { $this->Error = "Titre inexistant."; return False; }

		// Database query
		$lQuery = 	"INSERT INTO db_perso.titres (IdPersonnage, Titre, Description, Avantages, DateAcquisition)
				 VALUES (:characterid, :titlename, :description, :advantages, sysdate());

				 INSERT INTO db_perso.competences_acquises (IdPersonnage, CodeCompetence, Type, CoutXP, DateCreation, CodeAcquisition, CodeEtat)
				 VALUES (:characterid, :powercode, 'SPECIAL', 0, sysdate(), 'QUETE', 'ACTIF');";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $lCharacter->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":titlename", $lTitle['name'], PDO::PARAM_STR);
			$this->DAL->Bind(":description", $lTitle['description'], PDO::PARAM_STR);
			$this->DAL->Bind(":advantages", $lTitle['advantages'], PDO::PARAM_STR);
			$this->DAL->Bind(":powercode", $lTitle['specialpower'], PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		// Get the skill code associated with the subject...
		$this->Error = "Implantation requise...";
		return False;
	}


	//--GIVE RELIC REWARD--
	public function GiveRelicReward()
	{
		// Prepare data
		$lQuest = $this->Manager->GetSelectedQuest();
		$lCharacter = $lQuest->GetCharacter();

		// Database query
		$lQuery = 	"INSERT INTO db_perso.competences_acquises (IdPersonnage, CodeCompetence, Type, CoutXP, DateCreation, CodeAcquisition, CodeEtat)
				 VALUES (:characterid, 'RELICM', 'SPECIAL', 0, sysdate(), 'QUETE', 'ACTIF');";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $lCharacter->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Get the skill code associated with the subject...
		$this->Error = "Implantation requise...";
		return False;
	}


} // END of QuestServices class

?>
