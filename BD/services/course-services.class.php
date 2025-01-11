<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Course Services v1.2 r2 ==				║
║	Manages masters' list and teachings.			║
║	Non-serializable. Requires DAL. Uses MySQL queries.	║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('services/database.class.php'); 		// Data Access Layer
include_once('models/coursemanager.class.php');		// Course Manager definition
include_once('models/character.class.php'); 		// Character definition

class CourseServices
{

protected $DAL;
protected $Character;
protected $Manager;

public $Error;

	//--CONSTRUCTOR--
	public function __construct($inDataAccessLayer, $inCharacter =NULL, $inManager =NULL)
	{
		$this->DAL = $inDataAccessLayer;

		if( isset($inCharacter) ) {$this->Character = $inCharacter;}
		if( isset($inManager) ) {$this->Manager = $inManager;}
	}


	//--GET/SET FUNCTIONS--
	public function GetCharacter() { return $this->Character; }
	public function SetCharacter($inCharacter) { $this->Character = $inCharacter; }

	public function GetManager() { return $this->Manager; }
	public function SetManager($inManager) { $this->Manager = $inManager; }


	//--UPDATE MANAGER'S INFO--
	public function UpdateManager()
	{
		// Check is user and activities are defined
		if( !isset($this->Manager) ) { $this->Error = "No manager set!"; return False; }


		// Rebuild the lists
		if( $this->Character ) { $this->GetTeachableSkills(); }
		$this->GetMasterList();
		$this->GetNPCList();
		$this->GetValidActivityList();
				
		return True;
	}


	//--UPDATE THE PC MASTERS' LIST--
	public function GetTeachableSkills()
	{
		// Check if manager is set
		if( !isset($this->Manager) ) { $this->Error = "GetTeachableSkills : No manager set!"; return False; }
		if( !isset($this->Character) ) { $this->Error = "GetTeachableSkills : No character set!"; return False; }

		// Check if character has Grand Master skill 
		$lIsGrandMaster = False;
		$lQuery = 	"SELECT cac.Id FROM db_perso.competences_acquises cac
				 WHERE CodeCompetence = 'GRANDM'
				 AND IdPersonnage = :characterid;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();

		if($r) { $lIsGrandMaster = True; }

		// Non-masters can only teach in their category while masters can teach all.
		$lTeachableList = array(); $results = array();

		if( $lIsGrandMaster ) {
			$lQuery = "SELECT creg.Code, creg.Nom 
				   FROM db_pilot.competences_regulieres creg
				   WHERE creg.Code IN (SELECT cac.CodeCompetence FROM db_perso.competences_acquises cac WHERE cac.IdPersonnage = :characterid)
				     AND creg.DureeCours IS NOT NULL;";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
			$results = $this->DAL->FetchResult();
		}
		else {
			$lQuery = "SELECT creg.Code, creg.Nom 
				   FROM db_pilot.competences_regulieres creg
				   WHERE creg.Code IN (SELECT cac.CodeCompetence FROM db_perso.competences_acquises cac WHERE cac.IdPersonnage = :characterid)
				     AND creg.Categorie IN (SELECT ajc.Categorie FROM db_pilot.ajustements_categorie ajc
							    WHERE ajc.CodeClasse = :characterclass
							      AND ajc.Multiplicateur = 0.75)
				     AND creg.DureeCours IS NOT NULL;";

			$this->DAL->SetQuery($lQuery);
				$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
				$this->DAL->Bind(":characterclass", $this->Character->GetClassCode(), PDO::PARAM_STR);
			$results = $this->DAL->FetchResult();
		}

		// Copy results
		foreach( $results as $skill) {
			$lTeachableList[] = ['code' => $skill['Code'], 'name' => $skill['Nom'] ];
		}

		$this->Manager->SetTeachableSkills( $lTeachableList );

		return True;
	}


	//--UPDATE THE PC MASTERS' LIST--
	public function GetMasterList()
	{
		// Check if manager is set
		if( !isset($this->Manager) ) { $this->Error = "No manager set!"; return False; }


		// Ask the database
		$lQuery = 	"SELECT ind.Prenom AS PrenomJoueur, ind.Nom AS NomJoueur, mai.IdPersonnage, per.Prenom AS PrenomPerso, per.Nom AS NomPerso, mai.CodeCompetence, creg.Nom AS NomCompetence
				 FROM db_perso.maitres mai
					JOIN db_perso.personnages per ON mai.IdPersonnage = per.Id
					JOIN db_indiv.individus ind ON per.IdIndividu = ind.Id
    					JOIN db_pilot.competences_regulieres creg ON mai.CodeCompetence = creg.Code
				 WHERE mai.CodeEtat = 'ACTIF'
				   AND per.IdIndividu <> 0
				   AND per.CodeUnivers = :universecode
				 ORDER BY PrenomPerso, NomPerso ASC;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		$lMasterList = array();
		foreach( $r as $master) {
			
			// Check if master is not already in list
			$index = False;
			foreach ( $lMasterList as $i => $registered ) {
				if( $master['IdPersonnage'] == $registered->GetID() ) {$index = $i;}
			}

			// If he ain't, add him
			if($index === False){

				$skills = array( 0 => array('code' => $master['CodeCompetence'], 'name' => $master['NomCompetence']) );

				$attributes = [
					'id'			=>	$master['IdPersonnage'],
					'firstname'		=>	$master['PrenomPerso'],
					'lastname'		=>	$master['NomPerso'],
					'username'		=>	$master['PrenomJoueur'].' '.$master['NomJoueur'],
					'skills'		=>	$skills,
					'status'		=>	'ACTIF'
				];

				$lMasterList[] = new Character( $attributes );
			}

			// If he is, add the skill to his possible teachings
			else{
				$skill = array('code' => $master['CodeCompetence'], 'name' => $master['NomCompetence']);
				$lMasterList[$index]->AddSkill($skill);
			}
		}

		$this->Manager->SetMasters( $lMasterList );

		return True;
	}


	//--UPDATE THE NPC MASTERS' LIST--
	public function GetNPCList()
	{
		// Check if manager is set
		if( !isset($this->Manager) ) { $this->Error = "No manager set!"; return False; }


		// Ask the database
		$lQuery = 	"SELECT mai.IdPersonnage, per.Prenom AS PrenomPerso, per.Nom AS NomPerso, mai.CodeCompetence, creg.Nom AS NomCompetence
				 FROM db_perso.maitres mai
					JOIN db_perso.personnages per ON mai.IdPersonnage = per.Id
    					JOIN db_pilot.competences_regulieres creg ON mai.CodeCompetence = creg.Code
				 WHERE mai.CodeEtat = 'ACTIF'
				   AND per.IdIndividu = 0
				   AND per.CodeUnivers = :universecode
				 ORDER BY PrenomPerso, NomPerso ASC;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		$lMasterList = array();
		foreach( $r as $master) {
			
			// Check if master is not already in list
			$index = False;
			foreach ( $lMasterList as $i => $registered ) {
				if( $master['IdPersonnage'] == $registered->GetID() ) {$index = $i;}
			}

			// If he ain't, add him
			if($index === False){

				$skills = array( 0 => array('code' => $master['CodeCompetence'], 'name' => $master['NomCompetence']) );

				$attributes = [
					'id'			=>	$master['IdPersonnage'],
					'firstname'		=>	$master['PrenomPerso'],
					'lastname'		=>	$master['NomPerso'],
					'username'		=>	'PNJ',
					'skills'		=>	$skills,
					'status'		=>	'ACTIF'
				];

				$lMasterList[] = new Character( $attributes );
			}

			// If he is, add the skill to his possible teachings
			else{
				$skill = array('code' => $master['CodeCompetence'], 'name' => $master['NomCompetence']);
				$lMasterList[$index]->AddSkill($skill);
			}
		}

		$this->Manager->SetNPCs( $lMasterList );

		return True;
	}


	//--UPDATE THE VALID ACTIVTIES' LIST--
	public function GetValidActivityList()
	{
		// Check if manager is set
		if( !isset($this->Manager) ) { $this->Error = "No manager set!"; return False; }


		// Ask the database
		$lQuery = 	"SELECT act.Id, act.Nom, act.DateDebut, act.DateFin
				 FROM db_activ.activites act
				 WHERE act.Type = 'GN'
				   AND act.CodeUnivers = :universecode
				   AND act.DateFin <= sysdate()
				 ORDER BY act.DateDebut DESC
				 LIMIT 3;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		$lActivityList = array();
		foreach( $r as $activity) {
			
			$attributes = [
				'id'			=>	$activity['Id'],
				'name'			=>	$activity['Nom'],
				'startingdate'		=>	$activity['DateDebut'],
				'endingdate'		=>	$activity['DateFin']
			];

			$lActivityList[] = new Activity( $attributes );
		}

		$this->Manager->SetValidActivities( $lActivityList );

		return True;
	}


	//--GET STUDENT'S BASE INFORMATIONS--
	public function GetStudentCharacter($inCharacterID)
	{
		// Ask the database...
		$lQuery = 	"SELECT per.Prenom, per.Nom, CONCAT(ind.Prenom, ' ', ind.Nom) AS NomJoueur
				 FROM db_perso.personnages per 
					JOIN db_indiv.individus ind ON per.IdIndividu = ind.Id
				 WHERE per.Id = :characterid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $inCharacterID, PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		// Save result
		if($r) {
			$attributes = [
					'id'		=>	$inCharacterID,
					'firstname'	=>	$r[0]['Prenom'],
					'lastname'	=>	$r[0]['Nom'],
					'username'	=>	$r[0]['NomJoueur']
				];

			$lCharacter = new Character( $attributes );

			return $lCharacter;
		}

		return False;
	}


} // END of CourseServices class

?>
