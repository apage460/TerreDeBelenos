<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Custom Services v1.2 r2 ==				║
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


	//--GET STATISTICS ON EACH ACTIVITY--
	public function GetActivityData()
	{
		// Verify data
		if( !isset($this->Data) ) { $this->Error = "Le gestionnaire de données est absent! Contactez un DBA."; return False; }


		// Ask the database
		$lQuery = 	"SELECT act.Nom, act.Type, act.DateDebut,
					(SELECT count(*) FROM db_activ.inscriptions ins WHERE ins.IdActivite = act.Id) AS Inscriptions,
					(SELECT count(*) FROM db_indiv.presences pres WHERE pres.IdActivite = act.Id) AS Presences
				 FROM db_activ.activites act
				 ORDER BY act.DateDebut DESC;";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();

		// Set data
		if( $r ) { $this->Data->Activities = $r; }
		else { $this->Data->Activities = array(); }

		return True;
	}


	//--GET ALL MAGE AND PRIEST CHARACTERS THAT DON'T HAVE THE "UPPER CIRCLES" TALENT--
	public function GetPassData()
	{
		// Verify data
		if( !isset($this->Data) ) { $this->Error = "Le gestionnaire de données est absent! Contactez un DBA."; return False; }


		// Ask the database
		$lQuery = 	"SELECT pas.Nom,
					(SELECT count(*) FROM db_indiv.passes_acquises pac WHERE pac.IdPasse = pas.Id) AS Detenteurs
				 FROM db_activ.passes pas
				 ORDER BY pas.Id ASC;";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();

		// Set data
		if( $r ) { $this->Data->Passes = $r; }
		else { $this->Data->Passes = array(); }

		return True;
	}


} // END of CustomServices class

?>
