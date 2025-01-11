<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Noblesse_model extends CI_Model {
	public function __construct()	{
		parent::__construct();
		$this->db->db_select('db_perso');
	}

	public function listTitres(){
		$this->db->select("CONCAT(perso.Prenom, ' ' , perso.Nom) as 'persoNom', tit.Titre, tit.Description, tit.Avantages, CONCAT(indiv.Prenom, ' ' , indiv.Nom) as 'indivNom', perso.Id as 'idPerso', indiv.Id as 'idIndiv'");
		$this->db->from('db_perso.titres tit');
		$this->db->join('db_perso.personnages perso', 'perso.Id = tit.IdPersonnage', 'left');
		$this->db->join('db_indiv.individus indiv', 'indiv.Id = perso.IdIndividu', 'left');
		$this->db->order_by('tit.Titre', 'ASC');

		$query = $this->db->get();

		return $query->result();
	}
	

}

/* End of file Noblesse_model.php */
/* Location: ./application/models/Noblesse_model.php */ ?>