<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Search Controller v1.2 r2 ==				║
║	Implements the search engine control logic.		║
║	Requires search services.				║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('services/search-services.class.php');

class SearchController
{

protected $Services;

public $Error;

	//--CONSTRUCTOR--
	public function __construct(&$inServices)
	{
		$this->Services = $inServices;
	}


	//--PLAYER SEARCH LOGIC--
	public function SearchForPlayers()
	{
		$this->Error = null;

		// Check if posted information is valid
		//-- EVERY INFO CANNOT BE NULL
		if(!$_POST['account'] && !$_POST['firstname'] && !$_POST['lastname']) { 
			$this->Error = "Vous devez fournir une partie du nom du joueur ou du compte. Les recherches sans paramètre ne sont pas permises!"; 
			return False;
		}


		// Call search function
		$r = $this->Services->GetPlayers( $_POST['account'], $_POST['firstname'], $_POST['lastname'] );

		if($r) { $_POST['search_results'] = $r; return True; }

		$this->Error = "La recherche n'a produit aucun résultat!";
		return False;
	}


	//--CHARACTER SEARCH LOGIC--
	public function SearchForCharacters()
	{
		$this->Error = null;

		// Check if posted information is valid
		//-- EVERY INFO CANNOT BE NULL
		if(!$_POST['account'] && !$_POST['firstname'] && !$_POST['lastname']) { 
			$this->Error = "Vous devez fournir une partie du nom du personnage ou du compte joueur. Les recherches sans paramètre ne sont pas permises!"; 
			return False;
		}


		// Call search function
		$r = $this->Services->GetCharacters( $_POST['account'], $_POST['firstname'], $_POST['lastname'] );

		if($r) { $_POST['search_results'] = $r; return True; }

		$this->Error = "La recherche n'a produit aucun résultat!";
		return False;
	}


	//--GROUP SEARCH LOGIC--
	public function SearchForGroups()
	{
		$this->Error = null;

		// Check if posted information is valid
		//-- EVERY INFO CANNOT BE NULL
		if(!$_POST['account'] && !$_POST['firstname'] && !$_POST['lastname']) { 
			$this->Error = "Vous devez fournir au moins un critère de recherche !"; 
			return False;
		}


		// Call search function
		$r = $this->Services->GetCharacters( $_POST['account'], $_POST['firstname'], $_POST['lastname'] );

		if($r) { $_POST['search_results'] = $r; return True; }

		$this->Error = "La recherche n'a produit aucun résultat!";
		return False;
	}


	//--QUEST SEARCH LOGIC--
	public function SearchForQuests()
	{
		$this->Error = null;

		// Check if posted information is valid
		//-- EVERY INFO CANNOT BE NULL
		if(!$_POST['targetname'] && !$_POST['questsubject'] && !$_POST['status'] && !$_POST['scriptor']) { 
			$this->Error = "Vous devez fournir au moins un critère de recherche !"; 
			return False;
		}


		// Call search function
		$r = $this->Services->GetQuests( $_POST['targetname'], $_POST['questsubject'], $_POST['status'], $_POST['scriptor']);

		if($r) { $_POST['search_results'] = $r; return True; }

		$this->Error = "La recherche n'a produit aucun résultat!";
		return False;
	}


} // END of SearchController class

?>
