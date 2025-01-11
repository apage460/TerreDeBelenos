<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Old_Personnages_model extends CI_Model{

	public function __construct(){
		$this->load->database('db_perso');
	}


	public function getSearchResults($prenomJoueur, $nomJoueur, $avantApres, $ddn, $prenomPerso, $nomPerso, $classe, $race, $religion , $niveau, $niveauCtrl) {

		$this->db->db_select('db_perso');


		$query = "SELECT perso.*, indiv.Prenom as PrenomJoueur, indiv.Nom as NomJoueur, indiv.DateNaissance as DDN, indiv.Id as IdIndiv FROM db_perso.personnages perso ";
		$joinQuery = "LEFT JOIN db_indiv.individus indiv ON perso.IdIndividu = indiv.Id ";
		
		if( !empty($prenomJoueur) || !empty($nomJoueur) || !empty($prenomPerso) || !empty($nomPerso) || !empty($classe) || !empty($race) || !empty($religion) || !empty($ddn) || !empty($niveau)) {
			$whereParameters = "WHERE ";
		} else{
			$whereParameters = "";
		}

		if(!empty($prenomJoueur) ) {
			$whereParameters .= "indiv.Prenom LIKE '%" .$prenomJoueur ."%' ";
		}

		if(!empty($nomJoueur) ) {
			if(!empty($prenomJoueur)){
				$whereParameters .= "AND ";
			}
			$whereParameters .= "indiv.Nom LIKE '%" .$nomJoueur ."%' ";
		}

		if(!empty($prenomPerso) ) {
			if(!empty($prenomJoueur) || !empty($nomJoueur)){
				$whereParameters .= "AND ";
			}
			$whereParameters .= "perso.Prenom LIKE '%" .$prenomPerso ."%' ";
		}

		if(!empty($nomPerso)) {
			if(!empty($prenomJoueur) || !empty($nomJoueur) || !empty($prenomPerso)){
				$whereParameters .= "AND ";
			}
			$whereParameters .= "perso.Nom LIKE '%" .$nomPerso ."%' ";
		}

		if(!empty($classe)) {
			if(!empty($prenomJoueur) || !empty($nomJoueur) || !empty($prenomPerso) || !empty($nomPerso)){
				$whereParameters .= "AND ";
			}
			$whereParameters .= "perso.CodeClasse = '" .$classe ."' ";
		}

		if(!empty($race)) {
			if(!empty($prenomJoueur) || !empty($nomJoueur) || !empty($prenomPerso) || !empty($nomPerso) || !empty($classe)){
				$whereParameters .= "AND ";
			}
			$whereParameters .= "perso.CodeRace = '" .$race ."' ";
		}

		if(!empty($religion)) {
			if(!empty($prenomJoueur) || !empty($nomJoueur) || !empty($prenomPerso) || !empty($nomPerso) || !empty($race) || !empty($classe)){
				$whereParameters .= "AND ";
			}
			$whereParameters .= "perso.CodeReligion = '" .$religion ."' ";
		}

		if(!empty($ddn)){
			if(!empty($prenomJoueur) || !empty($nomJoueur) || !empty($prenomPerso) || !empty($nomPerso) || !empty($race) || !empty($classe) || !empty($religion) ){
				$whereParameters .= "AND ";
			}
			$whereParameters .= "indiv.DateNaissance " .$avantApres ." '" .$ddn ."'";
		}

		if(!empty($niveau)){
			if(!empty($prenomJoueur) || !empty($nomJoueur) || !empty($prenomPerso) || !empty($nomPerso) || !empty($race) || !empty($classe) || !empty($religion) || !empty($ddn) ){
				$whereParameters .= "AND ";
			}
			$whereParameters .= "perso.Niveau " .$niveauCtrl ." " .$niveau ."";
		}


		$fullQuery = $query .$joinQuery;

		if(!empty($whereParameters)){
			$fullQuery .= $whereParameters;
		}

		$results = $this->db->query($fullQuery);

		return $results->result_object();
	}

	public function getFullPersonnage($id){
		$this->db->db_select('db_perso');

		$returned = array();

		$query = "SELECT perso.*, pilotRace.Nom as persoRace, pilotClasse.Nom as persoClasse, pilotReligion.Nom as persoReligion 
				FROM db_perso.personnages perso 
				LEFT JOIN db_pilot.races pilotRace ON perso.CodeRace = pilotRace.Code
				LEFT JOIN db_pilot.classes pilotClasse ON perso.CodeClasse = pilotClasse.Code
				LEFT JOIN db_pilot.religions pilotReligion ON perso.CodeReligion = pilotReligion.Code
				WHERE perso.Id = " .$id .";";

		$persoData = $this->db->query($query);
		$returned['persoData'] = $persoData->result();

		$query = "SELECT Id,IdPersonnage,CodeCompetence,skills.Type,SUM(skills.Usages),CoutXP,DateCreation,CodeAcquisition,skills.CodeEtat, pilot.Nom as skillNom, skillSpec.Nom as skillSpecNom FROM db_perso.competences_acquises skills 
			LEFT JOIN db_pilot.competences_regulieres pilot ON skills.CodeCompetence = pilot.Code 
			LEFT JOIN db_pilot.competences_speciales skillSpec ON skills.CodeCompetence = skillSpec.Code 
			WHERE skills.IdPersonnage = " .$id ." AND (skills.CodeEtat = 'ACTIF' OR skills.CodeEtat = 'LEVEL' ) 
			GROUP BY CodeCompetence;";

		$persoSkills = $this->db->query($query);
		$returned['persoSkills'] = $persoSkills->result_array();

		$query = "SELECT experience.* FROM db_perso.experience experience WHERE experience.IdPersonnage = " .$id ." ORDER BY DateInscription DESC;";

		$persoExperience = $this->db->query($query);

		$returned['persoExperience'] = $persoExperience->result_array();
		
		$query = "SELECT quetes.* FROM db_perso.quetes quetes WHERE quetes.IdPersonnage = " .$id .";";

		$persoQuetes = $this->db->query($query);
		$returned['persoQuetes'] = $persoQuetes->result_array();

		$query = "SELECT persoPV.Raison, persoPV.PV, persoPV.Commentaires FROM db_perso.points_de_vie persoPV WHERE IdPersonnage = " .$id .";";

		$persoPv = $this->db->query($query);
		$returned['persoPv'] = $persoPv->result_array();

		$this->db->db_select('db_pilot');
		$query = "SELECT reg.Code, reg.Nom, reg.Usages FROM db_pilot.competences_regulieres reg ORDER BY Nom";

		$listSkills = $this->db->query($query);
		$returned['listSkills'] = $listSkills->result_array();

		/* TRAVAIL */

		$this->db->db_select('db_group');
		$query = "SELECT travailleurs.*, groupes.Nom as groupeNom
					FROM db_group.travailleurs travailleurs 
					LEFT JOIN db_group.groupes groupes ON travailleurs.IdGroupe = groupes.Id
					WHERE travailleurs.IdPersonnage = " .$id .";";


		$travail = $this->db->query($query);
		$returned['travail']=$travail->row();


		return $returned;
	}

	public function getReligion($id){
		$this->db->db_select('db_perso');

		$returned = array();

		$query = "SELECT codeReligion, Id, IdIndividu FROM db_perso.personnages perso WHERE perso.Id = " .$id .";";

		$persoData = $this->db->query($query);
		$returned['persoData'] = $persoData->result();

		return $returned;
	}

	public function updateReligion($newReligion, $idPerso){
		$this->db->db_select('db_perso');

		$query = "UPDATE personnages SET CodeReligion= '" .$newReligion ."' WHERE Id = " .$idPerso ." ;";

		$this->db->query($query);

		return true;
	}

	public function adjustXP($idPerso, $nbXP, $commentaireXP){
		$this->db->db_select('db_perso');

		$insertData = array(
			'IdPersonnage' => $idPerso,
			'Raison' => 'Ajustement manuel',
			'XP' => $nbXP,
			'DateInscription' => date('Y-m-d H:i:s', time()),
			'Commentaires' => $commentaireXP
		);

		$this->db->insert('experience',$insertData);

		return true;
	}

	public function addSkill($idPerso, $codeSkill, $usagesSkill){
		$this->db->db_select('db_perso');

		$insertData = array(
			'IdPersonnage' => $idPerso,
			'CodeCompetence' => $codeSkill,
			'Type' => 'REG',
			'Usages' => $usagesSkill,
			'CoutXP' => 0,
			'CodeAcquisition' => 'GRATUIT',
			'CodeEtat' => 'ACTIF',
			'DateCreation' => date('Y-m-d H:i:s', time()),
		);

		$this->db->insert('competences_acquises',$insertData);

		return true;
	}

	public function deleteSkill($idListSkill){
		$this->db->db_select('db_perso');

		$updateData = array(
			'CodeEtat' => 'REMPL',
		);

		$this->db->where('Id', $idListSkill);
		$this->db->update('competences_acquises', $updateData);

		return true;
	}

	public function deleteTravail($idTravail){
		$this->db->db_select('db_group');

		$this->db->where('Id',$idTravail);
		$this->db->delete('travailleurs');
	}

	public function modifPv($idPerso, $raison, $commentairesPv){
		$this->db->db_select('db_perso');

		$insertData = array(
			'IdPersonnage' 	=> $idPerso,
			'Raison'		=> $raison,
			'PV'			=> -1,
			'DateInscription'	=> date('Y-m-d H:i:s', time()),
			'Commentaires'	=>	$commentairesPv
		);

		$this->db->insert('points_de_vie',$insertData);
	}

	public function lvlUP($idPerso, $idIndiv){
		$this->db->db_select('db_perso');

		/* Activate Temp skills */
		$updateData = array(
			'CodeEtat' => 'ACTIF'
		);

		$this->db->where('IdPersonnage',$idPerso);
		$this->db->where('CodeEtat','LEVEL');

		$this->db->update('competences_acquises',$updateData);


		$updateData = array(
			'CodeEtat' => 'ACTIF',
		);

		$this->db->where('Id',$idPerso);
		$this->db->where('CodeEtat','LEVEL');

		$this->db->update('personnages',$updateData);

		/* Grant a level */

		$query = "UPDATE db_perso.personnages per
				 SET per.Niveau = per.Niveau+1, per.CodeEtat = 'LEVEL'
				 WHERE per.Id = " .$idPerso
				 ." AND per.CodeEtat IN ('NOUVO', 'ACTIF')";

		$this->db->query($query);

		/* Get new Level */

		$this->db->select('Niveau');
		$this->db->where('Id',$idPerso);
		$niveau = $this->db->get('personnages');

		$niveau = $niveau->row();

		/* Update character's journal */

		$insertData = array(
			'IdPersonnage' => $idPerso,
			'Message' => 'Niveau ' .$niveau->Niveau .'!',
			'Type' => 'LEVEL',
			'DateCreation' => date('Y-m-d H:i:s', time())
		);

		$this->db->insert('remarques',$insertData);

		/*Check if tutored*/
		$this->db->db_select('db_indiv');
		$this->db->select('Tuteur');
		$this->db->where('Id',$idIndiv);

		$boolTuteur = $this->db->get('individus');
		$boolTuteur = $boolTuteur->row();


		$this->db->db_select('db_perso');
		/* Grant XP */
		$niveau = $niveau->Niveau;
		if($niveau > 1 && $niveau <= 5){
			$lXP = 50;
			if( !is_null( $boolTuteur->Tuteur ) ){
				$lXP = 25;
			}

			$insertData = array(
				'IdPersonnage' => $idPerso,
				'Raison' => 'Niveau ' .$niveau,
				'XP' => $lXP,
				'DateInscription' => date('Y-m-d H:i:s', time()),
				'Commentaires' => '',
			);

			$this->db->insert('experience',$insertData);

		}

	}
			
}