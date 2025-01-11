<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Master List Services v1.2 r10 ==			║
║	Queries master lists.					║
║	Non-serializable. Requires DAL. Uses MySQL queries.	║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('services/database.class.php'); 		// Data Access Layer
include_once('models/masterlist.class.php'); 		// Master List definition
include_once('models/activity.class.php'); 		// Activity definition
include_once('models/user.class.php'); 			// User definition

class MasterServices
{

protected $DAL;
protected $MasterList;

public $Error;

	//--CONSTRUCTOR--
	public function __construct($inDataAccessLayer, $inMasterList)
	{
		$this->DAL = $inDataAccessLayer;
		$this->MasterList = $inMasterList;
	}


	//--GET/SET FUNCTIONS--
	public function GetMasterList() { return $this->MasterList; }
	public function SetMasterList($inList) { $this->MasterList = $inList; }


	//--BUILD LISTS--
	public function Build()
	{
		$this->GetActivityList();

		$this->GetQuestOptionList();
		$this->GetClassArchetypeList();

		$this->GetMinorTalentList();
		$this->GetMajorTalentList();
		$this->GetPrestigeTitleList();
		$this->GetMythicBeingList();

		$this->GetSpellList();
		$this->GetCurseList();
		$this->GetRecipeList();
		$this->GetJobList();

		$this->GetResurrectionMethodList();

		$this->GetCountyList();
		$this->GetKingdomList();

		$this->GetNPCList();
		
		return True;
	}


	//--BUILD ACTIVITY LIST--
	public function GetActivityList()
	{
		// Ask the database ...
		$lQuery = 	"SELECT act.Id, act.Nom, act.Description, act.Type, act.DateDebut, act.DateFin
				 FROM db_activ.activites act 
				 WHERE act.CodeUnivers = :universecode
				 ORDER BY DateDebut DESC;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		// Build list
		$lActivityList = array();
		foreach($r as $activity) {
			$attributes = [
				'id'		=> 		$activity['Id'],
				'name'		=> 		$activity['Nom'],
				'description' 	=> 		$activity['Description'],
				'type'		=> 		$activity['Type'],
				'startingdate' 	=> 		$activity['DateDebut'],
				'endingdate' 	=> 		$activity['DateFin']
			];

			$lActivityList[] = new Activity($attributes);
		}

		$this->MasterList->SetActivities($lActivityList);
		
		return True;
	}


	//--BUILD QUEST OPTION LIST--
	public function GetQuestOptionList()
	{
		// Ask the database ...
		$lQuery = 	"SELECT opt.Code, opt.Section, opt.Nom, opt.NombreGN 
				 FROM db_pilot.options_quetes opt
				 WHERE opt.CodeEtat = 'ACTIF'
				   AND opt.CodeUnivers = :universecode
				 ORDER BY opt.Numero ASC;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		// Build list
		$lOptionList = array();
		foreach($r as $option) {
			$lOptionList[] = [
				'code'		=>	$option['Code'],
				'section'	=>	$option['Section'],
				'name'		=>	$option['Nom'],
				'length'	=>	$option['NombreGN']
			];
		}

		$this->MasterList->SetQuestOptions($lOptionList);
		
		return True;
	}


	//--BUILD CLASS ARCHETYPE LIST--
	public function GetClassArchetypeList()
	{
		// Ask the database ...
		$lQuery = 	"SELECT arch.CodeClasse, arch.Code, arch.Nom 
				 FROM db_pilot.archetypes arch
				 ORDER BY arch.Nom ASC;";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();


		// Build list
		$lArchetypeList = array();
		foreach($r as $archetype) {
			$lArchetypeList[] = [
				'classcode'	=>	$archetype['CodeClasse'],
				'code'		=>	$archetype['Code'],
				'name'		=>	$archetype['Nom']
			];
		}

		$this->MasterList->SetArchetypes($lArchetypeList);
		
		return True;
	}


	//--BUILD MINOR TALENT LIST--
	public function GetMinorTalentList()
	{
		// Ask the database ...
		$lQuery = 	"SELECT cspec.Code, cspec.Nom 
				 FROM db_pilot.competences_speciales cspec
				 WHERE cspec.Type = 'MINEURE'
				   AND cspec.CodeEtat = 'ACTIF'
				 ORDER BY cspec.Nom ASC;";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();


		// Build list
		$lTalentList = array();
		foreach($r as $talent) {
			$lTalentList[] = [
				'code'		=>	$talent['Code'],
				'name'		=>	$talent['Nom']
			];
		}

		$this->MasterList->SetMinorTalents($lTalentList);
		
		return True;
	}


	//--BUILD MAJOR TALENT LIST--
	public function GetMajorTalentList()
	{
		// Ask the database ...
		$lQuery = 	"SELECT cspec.Code, cspec.Nom 
				 FROM db_pilot.competences_speciales cspec
				 WHERE cspec.Type = 'MAJEURE'
				   AND cspec.CodeEtat = 'ACTIF'
				 ORDER BY cspec.Nom ASC;";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();


		// Build list
		$lTalentList = array();
		foreach($r as $talent) {
			$lTalentList[] = [
				'code'		=>	$talent['Code'],
				'name'		=>	$talent['Nom']
			];
		}

		$this->MasterList->SetMajorTalents($lTalentList);
		
		return True;
	}

	
	//--BUILD PRESTIGE TALENT LIST--
	public function GetPrestigeTitleList()
	{
		// Ask the database ...
		$lQuery = 	"SELECT cspec.Code, cspec.Nom 
				 FROM db_pilot.titres cspec
				 WHERE cspec.Type = 'TITREP'
				   AND cspec.CodeEtat = 'ACTIF'
				 ORDER BY cspec.Nom ASC;";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();


		// Build list
		$lTitleList = array();
		foreach($r as $title) {
			$lTitleList[] = [
				'code'		=>	$title['Code'],
				'name'		=>	$title['Nom']
			];
		}

		$this->MasterList->SetPrestigeTitles($lTitleList);
		
		return True;
	}


	//--BUILD MYTHIC BEING LIST--
	public function GetMythicBeingList()
	{
		// Ask the database ...
		$lQuery = 	"SELECT cspec.Code, cspec.Nom 
				 FROM db_pilot.competences_speciales cspec
				 WHERE cspec.Type = 'MYTHIC'
				   AND cspec.CodeEtat = 'ACTIF'
				 ORDER BY cspec.Nom ASC;";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();


		// Build list
		$lBeingList = array();
		foreach($r as $title) {
			$lBeingList[] = [
				'code'		=>	$title['Code'],
				'name'		=>	$title['Nom']
			];
		}

		$this->MasterList->SetMythicBeings($lBeingList);
		
		return True;
	}


	//--BUILD SPELL LIST--
	public function GetSpellList()
	{
		// Ask the database ...
		$lQuery = 	"SELECT Nom AS name, CodeCompReg AS skillcode, CodeReligion AS religioncode
				 FROM db_pilot.sorts
				 ORDER BY Nom ASC;";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();


		// Build list
		$this->MasterList->SetSpells($r);
		
		return True;
	}


	//--BUILD CURSE LIST--
	public function GetCurseList()
	{
		// Ask the database ...
		$lQuery = 	"SELECT Nom AS name, CodeCompReg AS skillcode
				 FROM db_pilot.maledictions
				 ORDER BY Nom ASC;";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();


		// Build list
		$this->MasterList->SetCurses($r);
		
		return True;
	}


	//--BUILD RECIPE LIST--
	public function GetRecipeList()
	{
		// Ask the database ...
		$lQuery = 	"SELECT Nom AS name, CodeCompReg AS skillcode
				 FROM db_pilot.recettes
				 ORDER BY Nom ASC;";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();


		// Build list
		$this->MasterList->SetRecipes($r);
		
		return True;
	}


	//--BUILD JOB LIST--
	public function GetJobList()
	{
		// Ask the database ...
		$lQuery = 	"SELECT Nom AS name
				 FROM db_pilot.metiers;";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();


		// Build list
		$this->MasterList->SetJobs($r);
		
		return True;
	}


	//--BUILD RESURRECTION METHOD LIST--
	public function GetResurrectionMethodList()
	{
		// Ask the database ...
		$lQuery = 	"SELECT Nom AS name
				 FROM db_pilot.moyens_resurrection;";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();


		// Build list
		$this->MasterList->SetResurrectionMethods($r);
		
		return True;
	}


	//--BUILD COUNTY LIST--
	public function GetCountyList()
	{
		//Ask the database...
		$lQuery = 	"SELECT cmt.Id, cmt.Nom AS 'NomComte', cmt.Dirigeant, cmt.DescriptionDirigeant, cmt.Scribe, 
					duc.CodeRoyaume, pros.Nom AS 'Prosperite', roy.Nom AS 'NomRoyaume', cmt.CodeEtat, cmt.IndQuete
				 FROM db_histo.comtes cmt 
				 	INNER JOIN db_histo.duches duc ON cmt.CodeDuche = duc.Code
				 	INNER JOIN db_histo.royaumes roy ON duc.CodeRoyaume = roy.Code
				 	INNER JOIN db_pilot.prosperite pros ON duc.NiveauProsperite = pros.Niveau
				 WHERE roy.CodeEtat = 'ACTIF' 
				   AND cmt.CodeEtat = 'ACTIF'
				 ORDER BY roy.Nom, cmt.Nom ASC;";
	    
		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();
	    
		// Build list
		$lCountyList = array();
		foreach($r as $county) {
			$lCountyList[] = [
				'id'			=>	$county['Id'],
				'name'			=>	$county['NomComte'],
				'leader'		=>	$county['Dirigeant'],
				'leaderdescription'	=>	$county['DescriptionDirigeant'],
				'scribe'		=>	$county['Scribe'],
				'prosperity'		=>	$county['Prosperite'],
				'kingdomcode'		=>	$county['CodeRoyaume'],
				'kingdomname'		=>	$county['NomRoyaume'],
				'status'		=>	$county['CodeEtat'],
				'questgiver'		=>	$county['IndQuete']
	        ];
	    }
	    
	    $this->MasterList->SetCounties($lCountyList);
	    
	    return True;   
	}
	
	//--BUILD KINGDOM LIST--
	public function GetKingdomList()
	{
		//Ask the database...
		$lQuery = 	"SELECT roy.Code, roy.Nom, roy.IndQuete
				 FROM db_histo.royaumes roy
				 WHERE roy.CodeEtat = 'ACTIF'
				 ORDER BY FIELD(roy.Code, 'DAGOTH','AURELIU','CONCLAV','TAURE','CHAMPAG','PROSPER','ESELDOR','AUTRES');";
	    
		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();
	    
		// Build list
		$lKingdomList = array();
		foreach($r as $kingdom) {
			$lKingdomList[] = [
				'code'		=>	$kingdom['Code'],
				'name'		=>	$kingdom['Nom'],
				'questgiver'	=>	$kingdom['IndQuete']
			];
		}
	    
		$this->MasterList->SetKingdoms($lKingdomList);
	    
		return True;
	}


	//--BUILD KINGDOM LIST--
	public function GetNPCList()
	{
		//Ask the database...
		$lQuery = 	"SELECT per.Id, per.Prenom, per.Nom
				 FROM db_perso.personnages per
				 WHERE per.IdIndividu = 3
				   AND per.CodeEtat = 'PNJ'
				   AND per.CodeUnivers = :universecode
				 ORDER BY per.Prenom ASC;";
	    
		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		// Build list
		$lNPCList = array();
		foreach($r as $npc) {
			$lNPCList[ $npc['Id'] ] = [
				'firstname'	=>	$npc['Prenom'],
				'lastname'	=>	$npc['Nom']
			];
		}    
	    
		$this->MasterList->SetNPCs($lNPCList);
	    
		return True;
	}


} // END of MasterServices class

?>
