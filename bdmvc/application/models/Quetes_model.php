<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quetes_model extends CI_Model {

	public function __construct()	{
		parent::__construct();
		$this->db->db_select('db_perso');

	}

	public function viewQuests($idUser){

		$quetes = array();

		$this->db->db_select("db_perso");

		$this->db->select("quet.*, CONCAT(pers.Prenom,' ',pers.Nom) as 'nomPerso', CONCAT(indiv.Prenom,' ',indiv.Nom) as 'nomRespo'");
		$this->db->from('db_perso.quetes quet');
		$this->db->join('db_perso.personnages pers', 'pers.Id = quet.IdPersonnage', 'left');
		$this->db->join('db_indiv.individus indiv', 'indiv.Id = quet.IdResponsable', 'left');
		$this->db->where('quet.CodeEtat', 'DEM');
		if( $idUser != null ){
			$this->db->where('quet.IdResponsable', $idUser);
		}
		$query = $this->db->get();

		$quetes['dem'] = $query->result();


		$this->db->select("quet.*, CONCAT(pers.Prenom,' ',pers.Nom) as 'nomPerso', CONCAT(indiv.Prenom,' ',indiv.Nom) as 'nomRespo'");
		$this->db->from('db_perso.quetes quet');
		$this->db->join('db_perso.personnages pers', 'pers.Id = quet.IdPersonnage', 'left');
		$this->db->join('db_indiv.individus indiv', 'indiv.Id = quet.IdResponsable', 'left');
		$this->db->where('quet.CodeEtat', 'ACTIF');
		$this->db->order_by('nomPerso', 'asc');
		if( $idUser != null ){
			$this->db->where('quet.IdResponsable', $idUser);
		}
		$query = $this->db->get();

		$quetes['actif'] = $query->result();

		return $quetes;
	}

	

	

}

/* End of file Quetes_model.php */
/* Location: ./application/models/Quetes_model.php */ ?>