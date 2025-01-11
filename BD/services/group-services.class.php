<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Group Services v1.2 r13 ==				║
║	Manages Group Data.					║
║	Non-serializable. Requires DAL. Uses MySQL queries.	║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('services/database.class.php'); 		// Data Access Layer
include_once('models/user.class.php');	 		// User definition
include_once('models/character.class.php');	 	// Character definition
include_once('models/groupmanager.class.php'); 		// Group Manager definition
include_once('models/group.class.php'); 		// Group definition
include_once('models/activity.class.php'); 		// Activity definition
include_once('models/quest.class.php'); 		// Quest definition
include_once('models/resume.class.php'); 		// Resumé definition

class GroupServices
{

private $DAL;
private $User;
private $Manager;

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


	//--UPDATE ALL GENERAL GROUP MANAGEMENT LISTS--
	public function GetLists()
	{
		// Check if manager is defined
		if( !isset($this->Manager) ) { $this->Error = "GetLists : No manager set!"; return False; }

		// Build the lists
		$this->GetGroupList();
		$this->GetCampList();
		$this->GetSpecializationList();
		$this->GetProfileList();
		$this->GetPossibleActionList();
		$this->GetActivityList();
		
		return True;
	}


	//--UPDATE THE GROUP LIST--
	public function GetGroupList()
	{

		// Ask the database
		$lQuery = 	"SELECT grp.Id, grp.Nom, grp.CodeEtat 
				 FROM db_group.groupes grp
				 WHERE grp.CodeEtat NOT IN ('ERAD', 'DISSO', 'INACT')
				   AND grp.CodeUnivers = :universecode
				 ORDER BY grp.Nom ASC;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		$lGroupList = array();
		foreach( $r as $i => $group) {
			$lGroupList[$i] = [
				'id'		=>	$group['Id'],
				'name'		=>	$group['Nom'],
				'status'	=>	$group['CodeEtat']
			];
		}

		$this->Manager->SetGroups( $lGroupList );

		return True;
	}


	//--UPDATE THE CAMP LIST--
	public function GetCampList()
	{

		// Ask the database
		$lQuery = 	"SELECT camp.Code, camp.Nom, camp.Type 
				 FROM db_pilot.campements camp
				 ORDER BY camp.Numero ASC;";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();


		$lCampList = array();
		foreach( $r as $camp) {
			$lCampList[] = [
				'code'		=>	$camp['Code'],
				'name'		=>	$camp['Nom'],
				'type'		=>	$camp['Type']
			];
		}

		$this->Manager->SetCamps( $lCampList );

		return True;
	}


	//--UPDATE THE SPECIALIZATION LIST--
	public function GetSpecializationList()
	{

		// Ask the database
		$lQuery = 	"SELECT spec.Code, spec.Nom 
				 FROM db_pilot.specialisations spec
				 ORDER BY spec.Nom ASC;";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();


		$lSpecializationList = array();
		foreach( $r as $spec) {
			$lSpecializationList[] = [
				'code'		=>	$spec['Code'],
				'name'		=>	$spec['Nom']
			];
		}

		$this->Manager->SetPossibleSpecializations( $lSpecializationList );

		return True;
	}


	//--UPDATE THE PROFILE LIST--
	public function GetProfileList()
	{

		// Ask the database
		$lQuery = 	"SELECT prorg.Code, prorg.Nom 
				 FROM db_pilot.profils_organisation prorg
				 ORDER BY CASE prorg.Code
				 	WHEN 'M' THEN 1
				 	WHEN 'I' THEN 2
				 	WHEN 'C' THEN 3
				 	WHEN 'R' THEN 4
				 	WHEN 'A' THEN 5				 	
				 END;";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();


		$lProfileList = array();
		foreach( $r as $profile) {
			$lProfileList[] = [
				'code'		=>	$profile['Code'],
				'name'		=>	$profile['Nom']
			];
		}

		$this->Manager->SetPossibleProfiles( $lProfileList );

		return True;
	}


	//--UPDATE THE ACTION LIST--
	public function GetPossibleActionList()
	{

		// Ask the database
		$lQuery = 	"SELECT act.Code, act.Nom, act.CodeProfil, act.Niveau, act.Prosperite, act.Cout, act.AchatsMax, act.IndAchatsCumulatifs, act.Description
				 FROM db_pilot.actions_geopolitiques act
				 ORDER BY act.Nom ASC;";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();


		$lActionList = array();
		foreach( $r as $action) {
			$lActionList[] = [
				'code'		=>	$action['Code'],
				'name'		=>	$action['Nom'],
				'profilecode'	=>	$action['CodeProfil'],
				'level'		=>	$action['Niveau'],
				'prosperity'	=>	$action['Prosperite'],
				'cost'		=>	$action['Cout'],
				'maxpurchases'	=>	$action['AchatsMax'],
				'separable'	=>	$action['IndAchatsCumulatifs'],
				'description'	=>	$action['Description']
			];
		}

		$this->Manager->SetPossibleActions( $lActionList );

		return True;
	}


	//--UPDATE THE ACTIVITY LIST--
	public function GetActivityList()
	{

		// Ask the database
		$lQuery = 	"SELECT act.Id, act.Nom, act.DateDebut, act.DateFin
				  FROM db_activ.activites act
				  WHERE act.Type = 'GN'
				    AND act.CodeUnivers = :universecode
				    AND act.DateDebut > :rulesdate
				    AND act.DateFin < DATE(now())
				 UNION
				 (SELECT act.Id, act.Nom, act.DateDebut, act.DateFin
				  FROM db_activ.activites act
				  WHERE act.Type = 'GN'
				    AND act.CodeUnivers = :universecode
				    AND act.DateDebut > :rulesdate
				    AND act.DateFin > DATE(now()) LIMIT 1)
				   ORDER BY DateDebut DESC;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":rulesdate", EXTENDED_RULES_START_DATE, PDO::PARAM_STR);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		$lActivityList = array();
		foreach( $r as $activity) {
			$attributes = [
					'id'		=>	$activity['Id'],
					'name'		=>	$activity['Nom'],
					'startingdate'	=>	$activity['DateDebut'],
					'endingdate'	=>	$activity['DateFin']
			];

			$lActivityList[] = new Activity($attributes);
		}

		$this->Manager->SetActivities( $lActivityList );

		return True;
	}


	//--UPDATE MANAGER'S INFO--
	public function UpdateManager()
	{
		// Check if user and manager are defined
		if( !isset($this->User) ) { $this->Error = "UpdateManager : No user set!"; return False; }
		if( !isset($this->Manager) ) { $this->Error = "UpdateManager : No manager set!"; return False; }


		// Rebuild the lists
		$this->GetInvitationList();
		$this->GetAllegianceList();
		
		if ($this->GetActiveGroup() ) {
			$this->GetBaseCamp();
			$this->GetObjectives();
			$this->GetGroupMembers();
			$this->GetPeopleInCharge();
			$this->GetInfluence();
			$this->GetInstitutions();
			$this->GetActions();
			$this->GetAdvantages();
			$this->GetQuests();
			$this->GetResumes();
		}


		return True;
	}


	//--UPDATE THE INVITATION LIST--
	public function GetInvitationList()
	{

		// Ask the database
		$lQuery = 	"SELECT inv.IdPersonnage, CONCAT(per.Prenom, ' ', per.Nom) AS NomPersonnage, 
					inv.IdIndividu, CONCAT(ind.Prenom, ' ', ind.Nom) AS NomIndividu, 
					inv.IdGroupe, grp.Nom AS NomGroupe, inv.DateInvitation
				 FROM db_perso.invitations inv 
					JOIN db_perso.personnages per ON inv.IdPersonnage = per.Id
					JOIN db_indiv.individus ind ON inv.IdIndividu = ind.Id
					JOIN db_group.groupes grp ON inv.IdGroupe = grp.Id
				 WHERE per.IdIndividu = :userid
				   AND grp.CodeUnivers = :universecode
				 ORDER BY inv.DateInvitation ASC";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		$lInviteList = array();
		foreach( $r as $i => $invite) {
			$lInviteList[$i] = [
				'characterid'	=>	$invite['IdPersonnage'],
				'charactername'	=>	$invite['NomPersonnage'],
				'inviterid'	=>	$invite['IdIndividu'],
				'invitername'	=>	$invite['NomIndividu'],
				'groupid'	=>	$invite['IdGroupe'],
				'groupname'	=>	$invite['NomGroupe'],
				'date'		=>	$invite['DateInvitation']
			];
		}

		$this->Manager->SetInvitations( $lInviteList );

		return True;
	}


	//--UPDATE THE ALLEGIANCE LIST--
	public function GetAllegianceList()
	{

		// Ask the database
		$lQuery = 	"SELECT per.Id, per.Prenom AS PrenomPersonnage, per.Nom AS NomPersonnage, mbr.IdGroupe, grp.Nom AS NomGroupe
				 FROM db_perso.personnages per 
					LEFT JOIN db_group.membres mbr ON per.Id = mbr.IdPersonnage
					LEFT JOIN db_group.groupes grp ON mbr.IdGroupe = grp.Id
				 WHERE per.IdIndividu = :userid
				   AND per.CodeUnivers = :universecode
				 ORDER BY per.Id ASC";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		$lAllegianceList = array();
		foreach( $r as $i => $allegiance) {
			$lAllegianceList[$i] = [
				'characterid'		=>	$allegiance['Id'],
				'characterfirstname'	=>	$allegiance['PrenomPersonnage'],
				'characterlastname'	=>	$allegiance['NomPersonnage'],
				'groupid'		=>	$allegiance['IdGroupe'],
				'groupname'		=>	$allegiance['NomGroupe']
			];
		}

		$this->Manager->SetAllegiances( $lAllegianceList );

		return True;
	}


	//--GET ACTIVE GROUP'S BASE INFO--
	public function GetActiveGroup()
	{
		// Determine the active group.
		if( !$this->Manager->GetActiveGroup() ) { return False; }

		// Get group's base data from the database.
		$lQuery = 	"SELECT grp.Id, grp.Nom, grp.Description, grp.Historique, grp.InfoSup, spec.Nom AS 'Specialisation', grp.InfluenceMaximum, grp.CodeEtat 
				 FROM db_group.groupes grp
				 	LEFT JOIN db_pilot.specialisations spec ON spec.Code = grp.CodeSpecialisation
				 WHERE grp.Id = :groupid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		if($r) {
			$attributes = [
				'id'			=> 	$r[0]['Id'],
				'name'			=> 	$r[0]['Nom'],
				'status' 		=> 	$r[0]['CodeEtat'],
				'specialization'	=>	$r[0]['Specialisation'],
				'maxinfluence'		=>	$r[0]['InfluenceMaximum'],
				'description' 		=> 	$r[0]['Description'],
				'background' 		=> 	$r[0]['Historique'],
				'moreinfo'		=> 	$r[0]['InfoSup']
			];

			$lGroup = new Group($attributes);
			$this->Manager->SetActiveGroup( $lGroup );
		}
		else { return False;}
		
		return True;
	}


	// GET GROUP'S ACTIVE BASE CAMP
	private function GetBaseCamp()
	{

		// Ask the database
		$lQuery = 	"SELECT camo.CodeCamp, camp.Nom
				 FROM db_group.camps_occupes camo
				 	JOIN db_pilot.campements camp ON camo.CodeCamp = camp.Code
				 WHERE camo.IdGroupe = :groupid
				   AND camo.CodeEtat = 'ACTIF'
				   AND camo.DateCreation <= sysdate()
				 ORDER BY camo.DateCreation DESC
				 LIMIT 1;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();

		$lBaseCamp = array('code' => NULL, 'name' => 'Aucun');
		if($r) { 
			$lBaseCamp = [
				'code'	=> 	$r[0]['CodeCamp'],
				'name' 	=> 	$r[0]['Nom']
			];
		}

		$this->Manager->GetActiveGroup()->SetBaseCamp( $lBaseCamp );

		return True;
	}


	// GET GROUP'S OBJECTIVES
	private function GetObjectives()
	{

		// Ask the database
		$lQuery = 	"SELECT objg.Id, objg.Type, objg.Nom, objg.Description
				 FROM db_group.objectifs objg
				 WHERE objg.IdGroupe = :groupid
				 ORDER BY objg.Type ASC, objg.DateCreation ASC";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();

		$lObjectiveList = array();
		foreach ($r as $i => $objective) { 
			$lObjectiveList[$i] = [
				'id'		=> 	$objective['Id'],
				'type'		=> 	$objective['Type'],
				'name'		=> 	$objective['Nom'],
				'description' 	=> 	$objective['Description']
			];
		}

		$this->Manager->GetActiveGroup()->SetObjectives( $lObjectiveList );

		return True;
	}


	// GET ACTIVE GROUP'S MEMBERS
	private function GetGroupMembers()
	{

		// Ask the database
		$lQuery = 	"SELECT mbr.IdPersonnage, per.Prenom, per.Nom, per.CodeEtat, 
					per.IdIndividu, CONCAT(ind.Prenom, ' ', ind.Nom) AS NomJoueur, ind.Compte,
					dact.IdDernierGN, dact.NomDernierGN, dact.DateDebutDernierGN, dact.DateFinDernierGN 
				 FROM db_group.membres mbr 
					LEFT JOIN db_perso.personnages per ON mbr.IdPersonnage = per.Id
					LEFT JOIN db_indiv.individus ind ON per.IdIndividu = ind.Id
 					LEFT JOIN (	SELECT insc.IdPersonnage, act.Id AS IdDernierGN, act.Nom AS NomDernierGN, act.DateDebut AS DateDebutDernierGN, act.DateFin AS DateFinDernierGN
								FROM db_indiv.presences pres
								JOIN db_activ.inscriptions insc ON pres.IdIndividu = insc.IdIndividu AND pres.IdActivite = insc.IdActivite
								JOIN db_activ.activites act ON pres.IdActivite = act.Id
								WHERE act.Id = ( SELECT act2.Id 
										 FROM db_indiv.presences pres2
										 JOIN db_activ.inscriptions insc2 ON pres2.IdIndividu = insc2.IdIndividu AND pres2.IdActivite = insc2.IdActivite
										 JOIN db_activ.activites act2 ON pres2.IdActivite = act2.Id
										 WHERE insc2.IdPersonnage = insc.IdPersonnage AND act2.Type = 'GN'
										 ORDER BY act2.DateDebut DESC
										 LIMIT 1)
								AND insc.IdPersonnage <> 0) AS dact ON dact.IdPersonnage = mbr.IdPersonnage
				 WHERE mbr.IdGroupe = :groupid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();

		$lMemberList = array();
		foreach ($r as $i => $member) { 
			$lActivity = new Activity( array( 'id' => $member['IdDernierGN'], 'name' => $member['NomDernierGN'], 
							  'startingdate' => $member['DateDebutDernierGN'],'endingdate' => $member['DateFinDernierGN'] ) );

			$attributes = [
				'id' 		=> 	$member['IdPersonnage'],
				'firstname' 	=> 	$member['Prenom'],
				'lastname' 	=> 	$member['Nom'],
				'status' 	=> 	$member['CodeEtat'],
				'userid' 	=> 	$member['IdIndividu'],
				'useraccount' 	=> 	$member['Compte'],
				'username' 	=> 	$member['NomJoueur'],
				'characterattendances'	=>	$lActivity
			];

			$lMemberList[$i] = new Character($attributes); 
		}

		$this->Manager->GetActiveGroup()->SetMembers( $lMemberList );

		return True;
	}


	// GET ACTIVE GROUP'S MANAGERS
	private function GetPeopleInCharge()
	{

		// Ask the database
		$lQuery = 	"SELECT rgr.IdResponsable, ind.Compte, ind.Prenom, ind.Nom
				 FROM db_group.responsables_groupe rgr JOIN db_indiv.individus ind ON rgr.IdResponsable = ind.Id
				 WHERE rgr.IdGroupe = :groupid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
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

		$this->Manager->GetActiveGroup()->SetPeopleInCharge( $lPersonList );

		return True;
	}


	// GET GROUP'S INFLUENCE
	private function GetInfluence()
	{

		// Ask the database
		$lQuery = 	"SELECT inf.Id, inf.IdActivite, act.Nom, inf.Raison, inf.Points, inf.DateInscription
				 FROM db_group.influence inf 
				 LEFT JOIN db_activ.activites act ON inf.IdActivite = act.Id
				 WHERE inf.IdGroupe = :groupid
				   AND inf.IndSurplus = 0
				 ORDER BY inf.DateInscription ASC;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();

		// Build and save result array
		$lInfluenceList = array();
		foreach($r as $influence) { 
			$lInfluenceList[] = [
				'id'			=> 	$influence['Id'],
				'activityid'		=> 	$influence['IdActivite'],
				'activityname' 		=> 	$influence['Nom'],
				'reason'		=> 	$influence['Raison'],
				'points' 		=> 	$influence['Points'],
				'date'			=>	$influence['DateInscription']
			];

		}

		$this->Manager->GetActiveGroup()->SetInfluence( $lInfluenceList );

		return True;
	}


	// GET GROUP'S INSTITUTIONS
	private function GetInstitutions()
	{

		// Ask the database
		$lQuery = 	"SELECT instig.Id, instig.Nom, instig.CodeProfil, instig.Niveau, instig.IdComte, instig.Chef, instig.Description, instig.AgendaCache, instig.CodeEtat
				 FROM db_group.institutions instig 
				 WHERE instig.IdGroupe = :groupid
				   AND instig.CodeEtat = 'ACTIF';";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
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

		$this->Manager->GetActiveGroup()->SetInstitutions( $lInstitutionList );

		return True;
	}


	// GET GROUP'S ACTIONS
	private function GetActions()
	{

		// Ask the database for standard actions
		$lQuery = 	"SELECT act.Id, act.IdGroupe, act.IdActivite, act.CodeAction, act.Achats, actg.Nom, act.InfoSupp, act.CoutInfluence, act.CodeEtat, act.DateCreation, act.DateApprobation, act.RaisonRefus
				 FROM db_group.actions_faites act
					JOIN db_pilot.actions_geopolitiques actg ON act.CodeAction = actg.Code
				 WHERE act.IdGroupe = :groupid;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();

		$lActionList = array();
		foreach ($r as $action) { 
			$attributes = [
					'id' 			=> 	$action['Id'],
					'groupid' 		=> 	$action['IdGroupe'],
					'activityid' 		=> 	$action['IdActivite'],
					'code' 			=> 	$action['CodeAction'],
					'name' 			=> 	$action['Nom'],
					'purchases' 		=> 	$action['Achats'],
					'moreinfo' 		=> 	$action['InfoSupp'],
					'cost' 			=> 	$action['CoutInfluence'],
					'status' 		=> 	$action['CodeEtat'],
					'creationdate' 		=> 	$action['DateCreation'],
					'aprovaldate' 		=> 	$action['DateApprobation'],
					'reasonofdenial' 	=> 	$action['RaisonRefus']
			];

			$lActionList[] = $attributes;
		}

		$this->Manager->GetActiveGroup()->SetActions( $lActionList );

		return True;
	}


	// GET GROUP'S ADVANTAGES
	private function GetAdvantages()
	{

		// Ask the database
		$lQuery = 	"SELECT ava.Id, ava.Nom, ava.Description, ava.IdInstitution
				 FROM db_group.avantages ava 
				 WHERE ava.IdGroupe = :groupid
				   AND ava.CodeEtat = 'ACTIF'";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();

		// Build and save result array
		$lAdvantageList = array();
		foreach($r as $advantage) { 
			$attributes = [
				'id'			=> 	$advantage['Id'],
				'name'			=> 	$advantage['Nom'],
				'description'		=> 	$advantage['Description'],
				'institutionid'		=> 	$advantage['IdInstitution']
			];

			// Add advantage to group
			$lAdvantageList[] = $attributes;

		}

		$this->Manager->GetActiveGroup()->SetAdvantages( $lAdvantageList );

		return True;
	}


	// GET GROUP'S QUESTS
	private function GetQuests()
	{

		// Ask the database
		$lQuery = 	"SELECT que.Id, que.IdGroupe, que.Objet, que.CodeOption, que.CodeRecompense, que.IdComte, cmt.Nom AS NomComte, que.Suggestions, que.Texte, 
					que.CodeEtat, que.DateDemande, que.DateApprobation, que.Commentaires,
					que.IdActivite, act.Nom AS NomActivite,
					que.IdResponsable, ind.Prenom, ind.Nom
				 FROM db_group.quetes que 
				 	LEFT JOIN db_histo.comtes cmt ON que.IdComte = cmt.Id
					LEFT JOIN db_activ.activites act ON que.IdActivite = act.Id
					LEFT JOIN db_indiv.individus ind ON que.IdResponsable = ind.Id
				 WHERE que.IdGroupe = :groupid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();

		// Build and save result array
		$lQuestList = array();
		foreach($r as $i => $quest) { 
			$lScriptor = new User( ['id' => $quest['IdResponsable'], 'firstname' => $quest['Prenom'], 'lastname' => $quest['Nom']] );
			$lActivity = new Activity( ['id' => $quest['IdActivite'], 'name' => $quest['NomActivite']] );
			$attributes = [
				'id'			=> 	$quest['Id'],
				'status'		=> 	$quest['CodeEtat'],
				'subject'		=> 	$quest['Objet'],
				'optioncode' 		=> 	$quest['CodeOption'],
				'rewardcode' 		=> 	$quest['CodeRecompense'],
				'countyid' 		=> 	$quest['IdComte'],
				'countyname' 		=> 	$quest['NomComte'],
				'suggestions'		=> 	$quest['Suggestions'],
				'text'			=> 	$quest['Texte'],
				'requestdate'		=> 	$quest['DateDemande'],
				'approvaldate'		=> 	$quest['DateApprobation'],
				'comments'		=> 	$quest['Commentaires'],
				'activity'		=>	$lActivity,
				'scriptor'		=>	$lScriptor
			];

			$lQuestList[$i] = new Quest( $attributes );
		}

		$this->Manager->GetActiveGroup()->SetQuests( $lQuestList );

		return True;
	}


	// GET GROUP'S REUMES
	private function GetResumes()
	{

		// Ask the database...
		$lQuery = 	"SELECT resu.Id, resu.IdActivite, act.Nom, resu.Resume, resu.IdQuete, que.Objet, resu.DateCreation
				 FROM db_group.resumes resu
				 	JOIN db_activ.activites act ON resu.IdActivite = act.Id
				 	LEFT JOIN db_group.quetes que ON resu.IdQuete = que.Id
				 WHERE resu.IdGroupe = :groupid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
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

		$this->Manager->GetActiveGroup()->SetResumes($lResumeList);
		return True;
	}


	//--REGISTER MEMBERSHIP--
	public function AddCharacterToGroup( $inCharacterID, $inGroupID )
	{
		// Get character name for logs
		$lQuery = 	"SELECT CONCAT(per.Prenom, ' ', per.Nom) AS NomComplet
				 FROM db_perso.personnages per
				 WHERE per.Id = :characterid;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $inCharacterID, PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();
		if(!$r) { return True; }
		$lCharacterName = $r[0]['NomComplet'];


		// Insert membership.
		$lQuery = 	"INSERT INTO db_group.membres (IdPersonnage, IdGroupe)
				 VALUES ( :characterid, :groupid );

				 INSERT INTO db_group.journalisation (IdGroupe, Message, Type, DateCreation) 
				 VALUES (:groupid, :message, 'MEMBR', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $inCharacterID, PDO::PARAM_INT);
			$this->DAL->Bind(":groupid", $inGroupID, PDO::PARAM_INT);

			$this->DAL->Bind(":message", $lCharacterName.' s\'est joint au groupe!', PDO::PARAM_STR);
		return $this->DAL->FetchResult();
	}


	//--DELETE MEMBERSHIP--
	public function RemoveCharacterToGroup( $inCharacterID, $inGroupID )
	{
		// Get character name
		$lQuery = 	"SELECT CONCAT(per.Prenom, ' ', per.Nom) AS NomComplet
				 FROM db_perso.personnages per
				 WHERE per.Id = :characterid;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $inCharacterID, PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();
		$lCharacterName = "Un personnage inconnu";
		if($r) { $lCharacterName = $r[0]['NomComplet']; }


		// Insert membership.
		$lQuery = 	"DELETE FROM db_group.membres
				 WHERE IdPersonnage = :characterid 
				   AND IdGroupe = :groupid;

				 INSERT INTO db_group.journalisation (IdGroupe, Message, Type, DateCreation) 
				 VALUES (:groupid, :message, 'MEMBR', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $inCharacterID, PDO::PARAM_INT);
			$this->DAL->Bind(":groupid", $inGroupID, PDO::PARAM_INT);

			$this->DAL->Bind(":message", $lCharacterName.' a quitté le groupe!', PDO::PARAM_STR);
		return $this->DAL->FetchResult();
	}


	//--REFUSE MEMBERSHIP--
	public function RefuseInvitation( $inCharacterID, $inGroupID )
	{
		// Get character name
		$lQuery = 	"SELECT CONCAT(per.Prenom, ' ', per.Nom) AS NomComplet
				 FROM db_perso.personnages per
				 WHERE per.Id = :characterid;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $inCharacterID, PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();
		if(!$r) { return True; }
		$lCharacterName = $r[0]['NomComplet'];


		// Insert refusal in group logs.
		$lQuery = 	"INSERT INTO db_group.journalisation (IdGroupe, Message, Type, DateCreation) 
				 VALUES (:groupid, :message, 'MEMBR', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupid", $inGroupID, PDO::PARAM_INT);
			$this->DAL->Bind(":message", $lCharacterName.' a refusé votre invitation à se joindre au groupe.', PDO::PARAM_STR);
		return $this->DAL->FetchResult();
	}


	//--REMOVE AN INVITATION--
	public function RemoveInvitation( $inCharacterID, $inGroupID )
	{
		// Delete invitation.
		$lQuery = 	"DELETE FROM db_perso.invitations 
				 WHERE IdPersonnage = :characterid
				   AND IdGroupe = :groupid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $inCharacterID, PDO::PARAM_INT);
			$this->DAL->Bind(":groupid", $inGroupID, PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--CHECK IF GROUP NAME CAN BE USED--
	public function GroupNameExists($inName)
	{
		// If it exists in the databse, then it's taken.
		$lQuery = "SELECT grp.Nom FROM db_group.groupes grp
			    WHERE grp.Nom = :name
			      AND grp.CodeUnivers = :universecode ;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":name", $inName, PDO::PARAM_STR);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$this->DAL->FetchResult();


		// Return False if the account ain't free
		if ($this->DAL->GetRowCount() > 0) { return True; }
		return False;
	}


	//--CHECK IF USER IS NOT ALREADY IN CHARGE OF ANOTHER GROUP--
	public function AlreadyInCharge( $inAccount =NULL )
	{
		// Prepare data
		$lAccountName = $this->User->GetAccountName();
		if( $inAccount ) { $lAccountName = $inAccount; }

		// If it exists in the databse for a group that's not eradicated, then it's already in charge.
		$lQuery = 	"SELECT rgr.IdResponsable 
				 FROM db_group.responsables_groupe rgr 
				 	LEFT JOIN db_group.groupes grp ON rgr.IdGroupe = grp.Id
                   			LEFT JOIN db_indiv.individus ind ON rgr.IdResponsable = ind.Id
				 WHERE ind.Compte = :account
				   AND grp.CodeEtat IN ('ACTIF', 'NOUV')
				   AND grp.CodeUnivers = :universecode ;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":account", $lAccountName, PDO::PARAM_STR);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		// Return False if the account ain't free
		if (count($r) > 0) { return True; }
		return False;
	}


	//--REGISTER A NEW GROUP--
	public function RegisterGroup( $inName, $inDescription, $inBackground, $inCampCode, $inMoreInfo )
	{
		// Check is user and manager are defined
		if( !isset($this->User) ) { $this->Error = "No user set!"; return False; }
		if( !isset($this->Manager) ) { $this->Error = "No manager set!"; return False; }

		// Prepare data


		// Insert new group in the database.
		$lQuery = 	"INSERT INTO db_group.groupes (Nom, Description, Historique, InfoSup, CodeEtat, DateCreation, CodeUnivers) 
				 VALUES (:name, :description, :background, :moreinfo, 'NOUV', sysdate(), :universecode )";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":name", $inName, PDO::PARAM_STR);
			$this->DAL->Bind(":description", $inDescription, PDO::PARAM_STR);
			$this->DAL->Bind(":background", $inBackground, PDO::PARAM_STR);
			$this->DAL->Bind(":moreinfo", $inMoreInfo, PDO::PARAM_STR);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		// Get character Id
		$lQuery = "SELECT LAST_INSERT_ID() AS 'Id' ";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();

		$lGroupID = $r[0]['Id'];


		// Insert person-in-charge	
		$lQuery = 	"INSERT INTO db_group.responsables_groupe (IdGroupe, IdResponsable) 
				 VALUES (:groupid, :picid);

				 INSERT INTO db_group.journalisation (IdGroupe, Message, Type, DateCreation) 
				 VALUES (:groupid, 'Création du groupe!', 'SYS', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupid", $lGroupID, PDO::PARAM_INT);
			$this->DAL->Bind(":picid", $this->User->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();

		
		// Insert base camp if given
		if( $inCampCode ) {	
			$lQuery = 	"INSERT INTO db_group.camps_occupes (IdGroupe, CodeCamp, CodeEtat, DateCreation) 
					 VALUES (:groupid, :campcode, 'ACTIF', sysdate() );";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":groupid", $lGroupID, PDO::PARAM_INT);
				$this->DAL->Bind(":campcode", $inCampCode, PDO::PARAM_STR);
			$this->DAL->FetchResult();
		}


		return True;
	}


	//--SAVE GROUP'S NEW DESCRIPTION--
	public function SaveGroupDescription( $inDescription )
	{
		// Verify there's a manager
		if( !isset($this->Manager) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"UPDATE db_group.groupes grp
				 SET grp.Description = :description
				 WHERE grp.Id = :groupid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":description", $inDescription, PDO::PARAM_STR);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--SAVE GROUP'S NEW BASE CAMP--
	public function SaveNewBaseCamp( $inCampCode )
	{
		// Verify there's a manager
		if( !isset($this->Manager) ) { $this->Error = "No character!"; return False; }


		// If new camp is same as old, don't do anything
		$lCurrentCamp = $this->Manager->GetActiveGroup()->GetBaseCamp();
		if( $lCurrentCamp['code'] == $inCampCode ) { return True; }

		// Deactivate old base camp
		$lQuery = 	"UPDATE db_group.camps_occupes
				 SET CodeEtat = 'INACT'
				 WHERE IdGroupe = :groupid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();

		if(!$r) { $this->Error = "Error updating old camp!"; return False; }


		// Insert new base camp
		$lQuery = 	"INSERT INTO db_group.camps_occupes (CodeCamp, IdGroupe, CodeEtat, DateCreation)
				 VALUES (:campcode, :groupid, 'ACTIF', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":campcode", $inCampCode, PDO::PARAM_STR);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--SAVE GROUP'S BASE CAMP INFORMATION--
	public function SaveMoreInformation( $inInformation )
	{
		// Verify there's a manager
		if( !isset($this->Manager) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"UPDATE db_group.groupes grp
				 SET grp.InfoSup = :information
				 WHERE grp.Id = :groupid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":information", $inInformation, PDO::PARAM_STR);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--SAVE GROUP'S NEW BACKGROUND--
	public function SaveGroupBackground( $inBackground )
	{
		// Verify there's a manager
		if( !isset($this->Manager) ) { $this->Error = "No character!"; return False; }

		// Ask the database...
		$lQuery = 	"UPDATE db_group.groupes grp
				 SET grp.Historique = :background
				 WHERE grp.Id = :groupid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":background", $inBackground, PDO::PARAM_STR);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--NAME PERSON IN CHARGE--
	public function NamePersonInCharge( $inAccount )
	{
		// Verify there's a manager
		if( !isset($this->Manager) ) { $this->Error = "No group manager!"; return False; }


		// Get user ID
		$lQuery = 	"SELECT ind.Id FROM db_indiv.individus ind WHERE ind.Compte = :account;";
		
		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":account", $inAccount, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();
		if(!$r) { return False; }
		$lUserID = $r[0]['Id'];


		// Register PIC's ID in the database
		$lQuery = 	"INSERT INTO db_group.responsables_groupe (IdGroupe, IdResponsable) 
				 VALUES (:groupid, :picid);

				 INSERT INTO db_group.journalisation (IdGroupe, Message, Type, DateCreation)
				 VALUES (:groupid, :message, 'RESP', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":picid", $lUserID, PDO::PARAM_INT);

			$this->DAL->Bind(":message", 'Nouveau responsable : '.$inAccount, PDO::PARAM_STR);
		return $this->DAL->FetchResult();
	}


	//--REMOVE PERSON IN CHARGE--
	public function RemovePersonInCharge()
	{
		// Verify there's a user and a manager
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Manager) ) { $this->Error = "No group manager!"; return False; }


		// Remove PIC's ID in the database
		$lQuery = 	"DELETE FROM db_group.responsables_groupe 
				 WHERE IdResponsable = :userid;

				 INSERT INTO db_group.journalisation (IdGroupe, Message, Type, DateCreation)
				 VALUES (:groupid, :message, 'RESP', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);

			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":message", $this->User->GetAccountName().' n\'est plus responsable!', PDO::PARAM_STR);
		return $this->DAL->FetchResult();
	}


	//--RENAME ACTIVE GROUP--
	public function RenameActiveGroup( $inName )
	{
		// Verify there's a manager
		if( !isset($this->Manager) ) { $this->Error = "No group manager!"; return False; }

		// Ask the database...
		$lQuery = 	"UPDATE db_group.groupes grp
				 SET grp.Nom = :name
				 WHERE grp.Id = :groupid;

				 INSERT INTO db_group.journalisation (IdGroupe, Message, Type, DateCreation)
				 VALUES (:groupid, :message, 'INFO', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":name", $inName, PDO::PARAM_STR);

			$this->DAL->Bind(":message", 'Groupe renommé « '.$inName.' ».', PDO::PARAM_STR);
		return $this->DAL->FetchResult();
	}


	//--UPDATE ACTIVE GROUP'S STATUS--
	public function UpdateGroupStatus( $inStatusCode )
	{
		// Verify there's a manager
		if( !isset($this->Manager) ) { $this->Error = "No group manager!"; return False; }

		// Prepare data
		$lStatusName = "Nouveau";
		    if( $inStatusCode == 'INACT' ) { $lStatusName = "Inactif"; }
		elseif( $inStatusCode == 'ERAD'  ) { $lStatusName = "Éradiqué"; }
		elseif( $inStatusCode == 'DISSO' ) { $lStatusName = "Dissout"; }

		// Ask the database...
		$lQuery = 	"UPDATE db_group.groupes grp
				 SET grp.CodeEtat = :statuscode
				 WHERE grp.Id = :groupid;

				 INSERT INTO db_group.journalisation (IdGroupe, Message, Type, DateCreation)
				 VALUES (:groupid, :message, 'INFO', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":statuscode", $inStatusCode, PDO::PARAM_STR);

			$this->DAL->Bind(":message", 'Nouveau statut : '.$lStatusName.'.', PDO::PARAM_STR);
		return $this->DAL->FetchResult();
	}


	//--REGISTER NEW INSTITUTION--
	public function RegisterNewInstitution( $inName, $inCountyID, $inProfileCode, $inLeader, $inDescription, $inHiddenAgenda =NULL )
	{
		// Verify there's a user and a character
		if( !isset($this->Manager) ) { $this->Error = "RegisterNewInstitution : No manager!"; return False; }

		// Ask the database...
		$lQuery = 	"INSERT INTO db_group.institutions (Nom, IdComte, IdGroupe, CodeProfil, Niveau, Chef, Description, AgendaCache, CodeEtat, DateCreation)
				 VALUES ( :name, :countyid, :groupid, :profilecode, 0, :leader, :description, :hiddenagenda, 'ACTIF', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":name", $inName, PDO::PARAM_STR);
			$this->DAL->Bind(":countyid", $inCountyID, PDO::PARAM_INT);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":profilecode", $inProfileCode, PDO::PARAM_STR);
			$this->DAL->Bind(":leader", $inLeader, PDO::PARAM_STR);
			$this->DAL->Bind(":description", $inDescription, PDO::PARAM_STR);
			$this->DAL->Bind(":hiddenagenda", $inHiddenAgenda, PDO::PARAM_STR);
		return $this->DAL->FetchResult();
	}


	//--DELETE GROUP'S INSTITUTION--
	public function DeleteInstitution( $inID )
	{
		// Ask the database...
		$lQuery = 	"DELETE FROM db_group.institutions
				 WHERE Id = :institutionid 
				   AND IdGroupe = :groupid 
				   AND Niveau = 0;

				 UPDATE db_group.institutions instig
				 SET instig.CodeEtat = 'SUPPR'
				 WHERE instig.Id = :institutionid 
				   AND instig.IdGroupe = :groupid
				   AND instig.Niveau <> 0;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":institutionid", $inID, PDO::PARAM_INT);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--UPDATE GROUP'S INSTITUTION--
	public function UpdateInstitution( $inID, $inStatus )
	{
		// Ask the database...
		$lQuery = 	"UPDATE db_group.institutions instig
				 SET instig.CodeEtat = :status
				 WHERE instig.Id = :institutionid 
				   AND instig.IdGroupe = :groupid;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":status", $inStatus, PDO::PARAM_STR);
			$this->DAL->Bind(":institutionid", $inID, PDO::PARAM_INT);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--REGISTER GROUP'S QUEST REQUEST--
	public function RegisterQuestRequest( $inSubject, $inOptionCode, $inRewardCode, $inCountyID, $inSuggestions, $inStatusCode, $inNextActivity, $inAskingUserID )
	{
		// Ask the database...
		$lQuery = 	"INSERT INTO db_group.quetes (IdGroupe, Objet, CodeOption, CodeRecompense, IdComte, Suggestions, CodeEtat, DateDemande, IdActivite, IdDemandeur)
				 VALUES ( :groupid, :subject, :optioncode, :rewardcode, :countyid, :suggestions, :status, sysdate(), :activityid, :userid );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":subject", $inSubject, PDO::PARAM_STR);
			$this->DAL->Bind(":optioncode", $inOptionCode, PDO::PARAM_STR);
			$this->DAL->Bind(":rewardcode", $inRewardCode, PDO::PARAM_STR);
			$this->DAL->Bind(":countyid", $inCountyID, PDO::PARAM_INT);
			$this->DAL->Bind(":suggestions", $inSuggestions, PDO::PARAM_STR);
			$this->DAL->Bind(":status", $inStatusCode, PDO::PARAM_STR);
			$this->DAL->Bind(":activityid", $inNextActivity, PDO::PARAM_INT);
			$this->DAL->Bind(":userid", $inAskingUserID, PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--CANCEL GROUP'S QUEST--
	public function CancelQuest( $inID )
	{
		// Ask the database...
		$lQuery = 	"DELETE FROM db_group.quetes
				 WHERE Id = :questid 
				   AND IdGroupe = :groupid 
				   AND DateApprobation IS NULL;

				 UPDATE db_group.quetes que
				 SET que.CodeEtat = 'ANNUL'
				 WHERE que.Id = :questid 
				   AND que.IdGroupe = :groupid
				   AND que.DateApprobation IS NOT NULL;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":questid", $inID, PDO::PARAM_INT);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--RESTORE GROUP'S QUEST--
	public function RestoreQuest( $inID )
	{
		// Ask the database...
		$lQuery = 	"UPDATE db_group.quetes que
				 SET que.CodeEtat = 'REPR'
				 WHERE que.Id = :questid 
				   AND que.IdGroupe = :groupid
				   AND que.CodeEtat = 'ANNUL';";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":questid", $inID, PDO::PARAM_INT);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--CHECK IF RESUMÉ ALREADY EXISTS--
	public function ResumeExists( $inActivityID )
	{
		// Ask the database...
		$lQuery = 	"SELECT 1 FROM db_group.resumes resu
				 WHERE resu.IdGroupe = :groupid AND resu.IdActivite = :activityid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":activityid", $inActivityID, PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Return True if the name exists for this account
		if ($this->DAL->GetRowCount() > 0) { return True; }
		return False;
	}


	//--REGISTER GROUP'S RESUMÉ--
	public function RegisterNewResume( $inActivityID, $inText, $inQuestID =NULL )
	{
		// Ask the database...
		$lQuery = 	"INSERT INTO db_group.resumes (IdGroupe, IdActivite, Resume, IdQuete, DateCreation)
				 VALUES ( :groupid, :activityid, :resume, :questid, sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":activityid", $inActivityID, PDO::PARAM_INT);
			$this->DAL->Bind(":resume", $inText, PDO::PARAM_STR);
			$this->DAL->Bind(":questid", $inQuestID, PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--UPDATE GROUP'S RESUMÉ--
	public function UpdateResume( $inResumeID, $inText, $inQuestID =NULL )
	{
		// Ask the database...
		$lQuery = 	"UPDATE db_group.resumes resu
				 SET resu.Resume = :resume, resu.IdQuete = :questid
				 WHERE IdGroupe = :groupid
				   AND Id = :resumeid;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":resumeid", $inResumeID, PDO::PARAM_INT);
			$this->DAL->Bind(":resume", $inText, PDO::PARAM_STR);
			$this->DAL->Bind(":questid", $inQuestID, PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--VERIFY THAT CHARACTER EXISTS--
	public function CharacterExists( $inCharacterID )
	{

		// Ask the database...
		$lQuery = 	"SELECT per.Id FROM db_perso.personnages per WHERE per.Id = :characterid;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $inCharacterID, PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();

		if ($this->DAL->GetRowCount() > 0) { return True; }
		return False;
	}


	//--VERIFY IF CHARACTER IS ALREADY INVITED OR A MEMBER OF THE GROUP--
	public function AlreadyAMember( $inCharacterID )
	{
		// Verify there's a manager
		if( !isset($this->Manager) ) { $this->Error = "No group manager!"; return False; }


		// Prepare data
		$lGroupID = $this->Manager->GetActiveGroup()->GetID();


		// Ask the database if character is a member of active group
		$lQuery = 	"SELECT mbr.IdPersonnage FROM db_group.membres mbr WHERE mbr.IdPersonnage = :characterid AND mbr.IdGroupe = :groupid;;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $inCharacterID, PDO::PARAM_INT);
			$this->DAL->Bind(":groupid", $lGroupID, PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();

		if ($this->DAL->GetRowCount() > 0) { return True; }


		// Ask the database if the character is already invited
		$lQuery = 	"SELECT inv.IdPersonnage FROM db_perso.invitations inv WHERE inv.IdPersonnage = :characterid AND inv.IdGroupe = :groupid;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $inCharacterID, PDO::PARAM_INT);
			$this->DAL->Bind(":groupid", $lGroupID, PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();

		if ($this->DAL->GetRowCount() > 0) { return True; }

		return False;
	}


	//--INVITE CHARACTER TO GROUP--
	public function InviteMember( $inCharacterID )
	{
		// Verify there's a user and a character
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Manager) ) { $this->Error = "No group manager!"; return False; }


		// Add invitation in the database
		$lQuery = 	"INSERT INTO db_perso.invitations (IdPersonnage, IdGroupe, IdIndividu, DateInvitation) 
				 VALUES (:characterid, :groupid, :userid, sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $inCharacterID, PDO::PARAM_INT);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--REMOVE MEMBER FROM GROUP--
	public function RemoveMember( $inCharacterIndex )
	{
		// Verify there's a user and a manager
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Manager) ) { $this->Error = "No group manager!"; return False; }


		// Prepare data
		$lMembers = $this->Manager->GetActiveGroup()->GetMembers();

		// Remove member in the database
		$lQuery = 	"DELETE FROM db_group.membres 
				 WHERE IdPersonnage = :characterid;

				 INSERT INTO db_group.journalisation (IdGroupe, Message, Type, DateCreation)
				 VALUES (:groupid, :message, 'MEMBR', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $lMembers[$inCharacterIndex]->GetID(), PDO::PARAM_INT);

			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":message", $lMembers[$inCharacterIndex]->GetFullName().' a été retiré du groupe par '.$this->User->GetFullName().'!', PDO::PARAM_STR);
		return $this->DAL->FetchResult();
	}


	//--REGISTER A NEW OBJECTIVE FOR THE GROUP--
	public function RegisterObjective( $inObjective, $inType, $inDescription )
	{
		// Verify there's a user and a manager
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Manager) ) { $this->Error = "No group manager!"; return False; }


		// Add invitation in the database
		$lQuery = 	"INSERT INTO db_group.objectifs (IdGroupe, Type, Nom, Description, DateCreation, IdCreateur) 
				 VALUES (:groupid, :type, :name, :description, sysdate(), :userid );

				 INSERT INTO db_group.journalisation (IdGroupe, Message, Type, DateCreation)
				 VALUES (:groupid, :message, 'OBJEC', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":type", $inType, PDO::PARAM_STR);
			$this->DAL->Bind(":name", $inObjective, PDO::PARAM_STR);
			$this->DAL->Bind(":description", $inDescription, PDO::PARAM_STR);
			$this->DAL->Bind(":userid", $this->User->GetID(), PDO::PARAM_INT);

			$this->DAL->Bind(":message", $this->User->GetFullName().' a ajouter l\'objectif « '.$inObjective.' ».', PDO::PARAM_STR);
		return $this->DAL->FetchResult();
	}


	//--REVEAL AN OBJECTIVE--
	public function RevealObjective( $inObjectiveID )
	{
		// Verify there's a user and a manager
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Manager) ) { $this->Error = "No group manager!"; return False; }


		// Update an objective in the database
		$lQuery = 	"UPDATE db_group.objectifs objg
				 SET objg.Type = 'AVOUE'
				 WHERE Id = :objectiveid;

				 INSERT INTO db_group.journalisation (IdGroupe, Message, Type, DateCreation)
				 VALUES (:groupid, :message, 'OBJEC', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":objectiveid", $inObjectiveID, PDO::PARAM_INT);

			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":message", $this->User->GetFullName().' a dévoilé un objectif.', PDO::PARAM_STR);
		return $this->DAL->FetchResult();
	}


	//--REMOVE AN OBJECTIVE--
	public function RemoveObjective( $inObjectiveID )
	{
		// Verify there's a user and a manager
		if( !isset($this->User) ) { $this->Error = "No user!"; return False; }
		if( !isset($this->Manager) ) { $this->Error = "No group manager!"; return False; }


		// Delete an objective in the database
		$lQuery = 	"DELETE FROM db_group.objectifs 
				 WHERE Id = :objectiveid;

				 INSERT INTO db_group.journalisation (IdGroupe, Message, Type, DateCreation)
				 VALUES (:groupid, :message, 'OBJEC', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":objectiveid", $inObjectiveID, PDO::PARAM_INT);

			$this->DAL->Bind(":groupid", $this->Manager->GetActiveGroup()->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":message", $this->User->GetFullName().' a retiré un objectif.', PDO::PARAM_STR);
		return $this->DAL->FetchResult();
	}


	//--REMOVE AN ACTION--
	public function DeleteAction( $inActionID )
	{
		// Verify there's a user and a manager
		if( !isset($this->User) ) { $this->Error = "DeleteAction : No user!"; return False; }
		if( !isset($this->Manager) ) { $this->Error = "DeleteAction : No group manager!"; return False; }


		// Delete from the database.
		$lQuery = 	"DELETE FROM db_group.actions_faites 
				 WHERE Id = :actionid
				   AND CodeEtat = 'DEM';";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":actionid", $inActionID, PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--UPDATE AN ACTION--
	public function UpdateAction( $inActionID, $inPurchases, $inDetails, $inCost )
	{
		// Verify there's a user and a manager
		if( !isset($this->User) ) { $this->Error = "UpdateAction : No user!"; return False; }
		if( !isset($this->Manager) ) { $this->Error = "UpdateAction : No group manager!"; return False; }


		// Ask the database...
		$lQuery = 	"UPDATE db_group.actions_faites 
				 SET Achats = :purchases, CoutInfluence = :cost, InfoSupp = :details, DateCreation = sysdate()
				 WHERE Id = :actionid
				   AND CodeEtat = 'DEM';";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":actionid", $inActionID, PDO::PARAM_INT);
			$this->DAL->Bind(":purchases", $inPurchases, PDO::PARAM_INT);
			$this->DAL->Bind(":cost", $inCost, PDO::PARAM_INT);
			$this->DAL->Bind(":details", $inDetails, PDO::PARAM_STR);
		return $this->DAL->FetchResult();
	}


	//--REGISTER A NEW ACTION FOR THE GROUP AND THE COMING ACTIVITY--
	public function RegisterAction( $inGroupID, $inActivityID, $inActionCode, $inPurchases, $inDetails, $inCost )
	{
		// Verify there's a user and a manager
		if( !isset($this->User) ) { $this->Error = "RegisterAction : No user!"; return False; }
		if( !isset($this->Manager) ) { $this->Error = "RegisterAction : No group manager!"; return False; }


		// Ask the database...
		$lQuery = 	"INSERT INTO db_group.actions_faites (IdGroupe, IdActivite, CodeAction, Achats, InfoSupp, CoutInfluence, CodeEtat, DateCreation) 
				 VALUES (:groupid, :activityid, :code, :purchases, :moreinfo, :cost, 'DEM', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupid", $inGroupID, PDO::PARAM_INT);
			$this->DAL->Bind(":activityid", $inActivityID, PDO::PARAM_INT);
			$this->DAL->Bind(":code", $inActionCode, PDO::PARAM_STR);
			$this->DAL->Bind(":purchases", $inPurchases, PDO::PARAM_INT);
			$this->DAL->Bind(":moreinfo", $inDetails, PDO::PARAM_STR);
			$this->DAL->Bind(":cost", $inCost, PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--ADD INFLUENCE TO A DEFINED GROUP--
	public function AddInfluence( $inGroupID, $inActivityID, $inReason, $inInfluence )
	{
		// Verify there's a user and a manager
		if( !isset($this->User) ) { $this->Error = "RegisterAction : No user!"; return False; }
		if( !isset($this->Manager) ) { $this->Error = "RegisterAction : No group manager!"; return False; }


		// Ask the database...
		$lQuery = 	"INSERT INTO db_group.influence (IdGroupe, IdActivite, Raison, Points)
				 VALUES (:groupid, :activityid, :reason, :influencepoints);";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupid", $inGroupID, PDO::PARAM_INT);
			$this->DAL->Bind(":activityid", $inActivityID, PDO::PARAM_INT);
			$this->DAL->Bind(":reason", $inReason, PDO::PARAM_STR);
			$this->DAL->Bind(":influencepoints", $inInfluence, PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


} // END of GroupServices class

?>
