<?php

/*
=SCRIPTOR FILE=
╔══CLASS════════════════════════════════════════════════════════╗
║	== Statistics Services v1.2 r0 ==			║
║	Services used to get statistics.			║
║	Non-serializable. Requires DAL. Uses MySQL queries.	║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('services/database.class.php'); 		// Data Access Layer
include_once('models/statistics.class.php'); 		// Statistics definition

class StatisticsServices
{

protected $DAL;
protected $Statistics;

public $Error;

	//--CONSTRUCTOR--
	public function __construct($inDataAccessLayer, $inStatistics =NULL)
	{
		$this->DAL = $inDataAccessLayer;

		if( isset($inStatistics) ) {$this->Statistics = $inStatistics;}
	}


	//--GET/SET FUNCTIONS--
	public function GetStatistics() { return $this->Statistics; }

	public function SetStatistics($inStatistics) { $this->Statistics = $inStatistics; }


	//--GET SCRIPTORS' USEFUL STATISTICS--
	public function GetScriptorStatistics()
	{
		$this->Error = "";

		// Verify there's a statistics holder
		if( !isset($this->Statistics) ) { $this->Error = "No statistics holder!"; return False; }

		//Get character data
		$this->GetRaceStatistics();
		$this->GetClassStatistics();
		$this->GetReligionStatistics();
	}


	//--GET CHARACTERS VERSUS RACES STATISTICS--
	public function GetRaceStatistics()
	{
		// Ask the database...
		$lQuery = 	"SELECT  rac.Nom,
					(SELECT count(per.Id) FROM db_perso.personnages per WHERE per.CodeRace = rac.Code AND per.Niveau = 1 AND per.CodeEtat = 'LEVEL') AS Perso1,
					(SELECT count(per.Id) FROM db_perso.personnages per WHERE per.CodeRace = rac.Code AND per.Niveau > 1 AND per.Niveau < 5 AND per.CodeEtat = 'LEVEL') AS Perso2a4,
					(SELECT count(per.Id) FROM db_perso.personnages per WHERE per.CodeRace = rac.Code AND per.Niveau >= 5 AND per.Niveau < 10 AND per.CodeEtat = 'LEVEL') AS Perso5a9,
					(SELECT count(per.Id) FROM db_perso.personnages per WHERE per.CodeRace = rac.Code AND per.Niveau >= 10 AND per.CodeEtat = 'LEVEL') AS Perso10
				 FROM db_pilot.races rac";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();


		// Build and save statistics
		if($r) {
			$data = array();
			foreach ($r as $i => $row) {
				$data[$i] = [ 0 => $row['Nom'], 1 => $row['Perso1'], 2 => $row['Perso2a4'], 3 => $row['Perso5a9'], 4 => $row['Perso10'] ];
			}

			$attributes = [
					'name'		=>	'Races VS Personnages actifs',
					'columns'	=>	[ 'Races', 'Niveaux 1', 'Niveaux 2 à 4', 'Niveaux 5 à 9', 'Niveaux 10+' ],
					'rows'		=>	null,
					'data'		=>	$data
			];

			$this->Statistics->AddDataTable( new DataTable( $attributes ) );
		}
		else { return False;}

		return True;
	}


	//--GET CHARACTERS VERSUS CLASSES STATISTICS--
	public function GetClassStatistics()
	{
		// Ask the database...
		$lQuery = 	"SELECT  clas.Nom,
					(SELECT count(per.Id) FROM db_perso.personnages per WHERE per.CodeClasse = clas.Code AND per.Niveau = 1 AND per.CodeEtat = 'LEVEL') AS Perso1,
					(SELECT count(per.Id) FROM db_perso.personnages per WHERE per.CodeClasse = clas.Code AND per.Niveau > 1 AND per.Niveau < 5 AND per.CodeEtat = 'LEVEL') AS Perso2a4,
					(SELECT count(per.Id) FROM db_perso.personnages per WHERE per.CodeClasse = clas.Code AND per.Niveau >= 5 AND per.Niveau < 10 AND per.CodeEtat = 'LEVEL') AS Perso5a9,
					(SELECT count(per.Id) FROM db_perso.personnages per WHERE per.CodeClasse = clas.Code AND per.Niveau >= 10 AND per.CodeEtat = 'LEVEL') AS Perso10
				 FROM db_pilot.classes clas";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();


		// Build and save statistics
		if($r) {
			$data = array();
			foreach ($r as $i => $row) {
				$data[$i] = [ 0 => $row['Nom'], 1 => $row['Perso1'], 2 => $row['Perso2a4'], 3 => $row['Perso5a9'], 4 => $row['Perso10'] ];
			}

			$attributes = [
					'name'		=>	'Classes VS Personnages actifs',
					'columns'	=>	[ 'Classes', 'Niveaux 1', 'Niveaux 2 à 4', 'Niveaux 5 à 9', 'Niveaux 10+' ],
					'rows'		=>	null,
					'data'		=>	$data
			];

			$this->Statistics->AddDataTable( new DataTable( $attributes ) );
		}
		else { return False;}



		return True;
	}


	//--GET CHARACTERS VERSUS RELIGIONS STATISTICS--
	public function GetReligionStatistics()
	{
		// Ask the database...
		$lQuery = 	"SELECT  rel.Nom,
					(SELECT count(per.Id) FROM db_perso.personnages per WHERE per.CodeReligion = rel.Code AND per.Niveau = 1 AND per.CodeEtat = 'LEVEL') AS Perso1,
					(SELECT count(per.Id) FROM db_perso.personnages per WHERE per.CodeReligion = rel.Code AND per.Niveau > 1 AND per.Niveau < 5 AND per.CodeEtat = 'LEVEL') AS Perso2a4,
					(SELECT count(per.Id) FROM db_perso.personnages per WHERE per.CodeReligion = rel.Code AND per.Niveau >= 5 AND per.Niveau < 10 AND per.CodeEtat = 'LEVEL') AS Perso5a9,
					(SELECT count(per.Id) FROM db_perso.personnages per WHERE per.CodeReligion = rel.Code AND per.Niveau >= 10 AND per.CodeEtat = 'LEVEL') AS Perso10,
					(SELECT count(per.Id) FROM db_perso.personnages per WHERE per.CodeReligion = rel.Code AND per.CodeClasse IN ('PRETRE','CLERC','CULTIST','ACOLYTE','MOINE','SHAMAN') AND per.CodeEtat = 'LEVEL') AS Pretres
				 FROM db_pilot.religions rel";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();


		// Build and save statistics
		if($r) {
			$data = array();
			foreach ($r as $i => $row) {
				$data[$i] = [ 0 => $row['Nom'], 1 => $row['Perso1'], 2 => $row['Perso2a4'], 3 => $row['Perso5a9'], 4 => $row['Perso10'], 5 => $row['Pretres'] ];
			}

			$attributes = [
					'name'		=>	'Religions VS Personnages actifs',
					'columns'	=>	[ 'Religions', 'Niveaux 1', 'Niveaux 2 à 4', 'Niveaux 5 à 9', 'Niveaux 10+', 'Prêtres' ],
					'rows'		=>	null,
					'data'		=>	$data
			];

			$this->Statistics->AddDataTable( new DataTable( $attributes ) );
		}
		else { return False;}



		return True;
	}


} // END of StatisticsServices class

?>
