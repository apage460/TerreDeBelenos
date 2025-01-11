<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Repair Services v1.2 r0 ==				║
║	Services meant to keep the database clean.		║
║	Non-serializable. Requires DAL. Uses MySQL queries.	║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('services/database.class.php'); 		// Data Access Layer
include_once('models/repairbot.class.php'); 		// RepairBot Data definition

class RepairServices
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


	//--DATABASE CLEAN-UP MASTER PROCESS--
	public function CleanUpDatabase()
	{
		// Verify data
		if( !isset($this->Data) ) { $this->Error = "No data!"; return False; }
		
		// Initialize the process list
		$lProcessList = array();

		// Call clean-up processes
		$lProcessList[] = $this->CleanBadIDs();
		$lProcessList[] = $this->ApproveLastActivitySkills();


		// Return results
		$this->Data->Processes = $lProcessList;
		return True;
	}


	//--DELETE RECORDS WITH BAD IDs--
	public function CleanBadIDs()
	{
		// Prepare
		$lProcess['Name'] = "Retrait des ID#0";
		$lProcess['Result'] = "Échec";

		// Verify
		if( !isset($this->Data) ) { $this->Error = "No data!"; return False; }

		// Ask the database
		$lQuery = 	"DELETE FROM db_perso.competences_acquises
				 WHERE IdPersonnage = 0";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();
		$ar = $this->DAL->GetRowCount();
		
		$lProcess['Result'] = $ar." suppressions";

		return $lProcess;
	}


	//--AUTO-APPROVE SKILLS FOR PEOPLE PRESENT AT LAST ACTIVITY--
	public function ApproveLastActivitySkills()
	{
		// Prepare
		$lProcess['Name'] = "Approbation des compétences de la dernière activité";
		$lProcess['Result'] = "Échec";

		// Verify
		if( !isset($this->Data) ) { $this->Error = "No data!"; return False; }

		// Ask the database for the activity's data
		$lQuery = 	"SELECT act.Id 
				 FROM db_activ.activites act
				 WHERE act.DateFin < now()
				 AND act.Type = 'GN'
				 ORDER BY act.DateFin DESC
				 LIMIT 1";

		$this->DAL->SetQuery($lQuery);
		$lLastActivity = $this->DAL->FetchResult();


		// Update skills asked before
		$lQuery = 	"UPDATE db_perso.competences_acquises cac
				 SET cac.CodeEtat = 'LEVEL'
				 WHERE cac.IdPersonnage IN (SELECT ins.IdPersonnage FROM db_activ.inscriptions ins
							    WHERE ins.IdActivite = :activityid
							    AND ins.IdIndividu IN (SELECT pres.IdIndividu FROM db_indiv.presences pres
										   WHERE pres.IdActivite = :activityid))
				   AND cac.CodeEtat = 'PRLVL'
				   AND cac.DateCreation < (SELECT pres2.DateInscription 
						 	   FROM db_indiv.presences pres2
							   WHERE pres2.IdIndividu = (SELECT per.IdIndividu FROM db_perso.personnages per WHERE per.Id = cac.IdPersonnage)
                        				     AND pres2.IdActivite = :activityid);";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":activityid", $lLastActivity[0]['Id'], PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();
		$ar = $this->DAL->GetRowCount();
		
		$lProcess['Result'] = $ar." mises à jour";

		return $lProcess;
	}


} // END of RepairServices class

?>
