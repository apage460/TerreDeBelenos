<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Courriel_model extends CI_Model {
	public function __construct() {
		parent::__construct();
		$this->db->db_select('db_indiv');
	}

	public function getGroupes(){
		$this->db->db_select('db_group');

		$this->db->select('Id,Nom');
		$this->db->where('CodeEtat', 'NOUV');
		$this->db->or_where('CodeEtat', 'OFFIC');
		$this->db->or_where('CodeEtat', 'ACTIF');
		$this->db->order_by('Nom', 'asc');
		$query = $this->db->get('groupes');

		return $query->result();
	}

	public function search($prenom, $nom, $groupe, $pseudo){
		
		if($groupe){

			$this->db->select('perso.IdIndividu');
			$this->db->from('db_group.membres grp');
			$this->db->join('db_perso.personnages perso', 'perso.Id = grp.IdPersonnage', 'left');
			$this->db->where('IdGroupe', $groupe);

			$query = $this->db->get();

			$results = $query->result();

			foreach ($results as $result) {				
				$arrId[] = $result->IdIndividu;
			}
		}

		$this->db->db_select('db_indiv');

		$this->db->select('indiv.Id, indiv.Prenom, indiv.Nom, indiv.Pseudo, indiv.Courriel');
		$this->db->from('db_indiv.individus indiv');

		if($prenom){
			$this->db->like('indiv.Prenom', $prenom, 'BOTH');
		}
		if($nom){
			$this->db->like('indiv.Nom', $nom, 'BOTH');
		}
		if($groupe){
			$this->db->where_in('Id',$arrId);
		}
		if($pseudo){
			$this->db->like('indiv.Pseudo', $pseudo, 'BOTH');
		}

		$this->db->db_select('db_indiv');

		$query = $this->db->get();

		return $query->result();
	}

	public function getResponsables($groupe){
		$this->db->db_select('db_group');

		$this->db->where('IdGroupe', $groupe);
		$query = $this->db->get('responsables_groupe');

		return $query->result();
	}
	

}

/* End of file Courriel_model.php */
/* Location: ./application/models/Courriel_model.php */ ?>