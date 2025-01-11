<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Search Services v1.2 r1 ==				║
║	Makes searches in the database.				║
║	Non-serializable. Requires DAL. Uses MySQL queries.	║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('services/database.class.php'); 		// Data Access Layer

class SearchServices
{

protected $DAL;

public $Error;

	//--CONSTRUCTOR--
	public function __construct($inDataAccessLayer)
	{
		$this->DAL = $inDataAccessLayer;
	}


	//--SEARCH FOR PLAYERS--
	public function GetPlayers($inAccount, $inFirstName, $inLastName)
	{
		
		// Ask the database for corresponding user.
		$lQuery = 	"SELECT ind.Id, ind.Compte, CONCAT(ind.Prenom, ' ', ind.Nom) AS NomJoueur, ind.DateNaissance, ind.Courriel
				 FROM db_indiv.individus ind 
				 WHERE ind.Compte LIKE :account
				   AND ind.Prenom LIKE :firstname
				   AND ind.Nom LIKE :lastname
				   AND ind.CodeEtat = 'ACTIF';";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":account", '%'.$inAccount.'%', PDO::PARAM_STR);
			$this->DAL->Bind(":firstname", '%'.$inFirstName.'%', PDO::PARAM_STR);
			$this->DAL->Bind(":lastname", '%'.$inLastName.'%', PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		
		// Return search result.
		$lResults = array();
		if($r) {
			foreach( $r as $result ) {
				$lResults[] = [
					'id'		=> 		$result['Id'],
					'account'	=> 		$result['Compte'],
					'name'		=> 		$result['NomJoueur'],
					'dateofbirth' 	=> 		$result['DateNaissance'],
					'mail'		=> 		$result['Courriel'],
				];
			}

		}
		
		return $lResults;
	}


	//--SEARCH FOR CHARACTERS--
	public function GetCharacters($inAccount, $inFirstName, $inLastName)
	{
		
		// Ask the database for corresponding user.
		$lQuery = 	"SELECT per.IdIndividu, ind.Compte, CONCAT(ind.Prenom, ' ', ind.Nom) AS NomJoueur, 
					per.Id, CONCAT(per.Prenom, ' ', per.Nom) AS NomPersonnage 
				 FROM db_perso.personnages per 
					JOIN db_indiv.individus ind ON per.IdIndividu = ind.Id
				 WHERE ind.Compte LIKE :account
				   AND ind.CodeEtat = 'ACTIF'
				   AND per.Prenom LIKE :firstname
				   AND per.Nom LIKE :lastname
				   AND per.CodeEtat IN ('ACTIF','LEVEL')
				   AND per.CodeUnivers = :universecode;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":account", '%'.$inAccount.'%', PDO::PARAM_STR);
			$this->DAL->Bind(":firstname", '%'.$inFirstName.'%', PDO::PARAM_STR);
			$this->DAL->Bind(":lastname", '%'.$inLastName.'%', PDO::PARAM_STR);
			$this->DAL->Bind(":universecode", WORLD, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		
		// Return search result.
		$lResults = array();
		if($r) {
			foreach( $r as $result ) {
				$lResults[] = [
					'account'	=> 		$result['Compte'],
					'userid'	=> 		$result['IdIndividu'],
					'username'	=> 		$result['NomJoueur'],
					'characterid' 	=> 		$result['Id'],
					'charactername'	=> 		$result['NomPersonnage'],
				];
			}

		}
		
		return $lResults;
	}


	//--SEARCH FOR GROUPS--
	public function GetGroups($inGroupName)
	{
		
		// Ask the database for corresponding user.
		$lQuery = 	"SELECT grp.IdGroupe, grp.Nom, grp.CodeEtat 
				 FROM db_group.groupes grp 
				 WHERE grp.Nom LIKE :groupname ;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":groupname", '%'.$inGroupName.'%', PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		
		// Return search result.
		$lResults = array();
		if($r) {
			foreach( $r as $result ) {
				$lResults[] = [
					'id'		=> 	$result['IdGroupe'],
					'name'		=> 	$result['Nom'],
					'status'	=> 	$result['CodeEtat'],
				];
			}

		}
		
		return $lResults;
	}


	//--SEARCH FOR GROUPS--
	public function GetQuests($inTargetName, $inSubject, $inStatusCode, $inScriptor)
	{
		
		// Ask the database for corresponding user.
		$lQuery = 	"SELECT que.Id, concat(per.Prenom,' ',per.Nom) AS NomPersonnage, que.Objet, que.CodeEtat, que.DateDemande 
				 FROM db_perso.quetes que
				 LEFT JOIN db_perso.personnages per ON que.IdPersonnage = per.Id
				 WHERE que.Objet LIKE :subject 
				   AND que.CodeEtat LIKE :status
				   AND concat(per.Prenom,' ',per.Nom) LIKE :fullname";

		if($inScriptor) { $lQuery .= "AND que.IdResponsable = :scriptorid"; }

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":subject", '%'.$inSubject.'%', PDO::PARAM_STR);
			$this->DAL->Bind(":status", $inStatusCode, PDO::PARAM_STR);
			$this->DAL->Bind(":fullname", '%'.$inTargetName.'%', PDO::PARAM_STR);
			if($inScriptor) { $this->DAL->Bind(":scriptorid", $inScriptor, PDO::PARAM_INT); }
		$r = $this->DAL->FetchResult();

		
		// Return search result.
		$lResults = array();
		if($r) {
			foreach( $r as $result ) {
				$lResults[] = [
					'id'			=> 	$result['Id'],
					'target'		=>	$result['NomPersonnage'],
					'subject'		=> 	$result['Objet'],
					'status'		=> 	$result['CodeEtat'],
					'requestdate'		=>	$result['DateDemande']
				];
			}

		}
		
		return $lResults;
	}


} // END of SearchServices class

?>
