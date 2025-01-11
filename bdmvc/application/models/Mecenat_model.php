<?php
	defined('BASEPATH') OR exit('No direct script access allowed');

	class Mecenat_model extends CI_Model {

	public function __construct()	{
		parent::__construct();
		$this->db->db_select('db_indiv');

	}

	public function getJoueurs(){

		$dateToday = date('Y-m-d', strtotime("today"));

		if( $this->input->post('username') ):
			$this->db->like('Compte', $this->input->post('username') );
		endif;

		if( $this->input->post('prenom') ):
			$this->db->like('Prenom', $this->input->post('prenom') );
		endif;

		if( $this->input->post('nom') ):
			$this->db->like('Nom', $this->input->post('nom') );
		endif;

		$this->db->order_by('Compte', 'ASC');
		$query = $this->db->get('individus');

		return $query->result();
		
	}

	public function getSingleJoueur($id){
		$this->db->where('Id', $id);
		$query = $this->db->get('individus');

		return $query->row();
	}

	public function getMecenat($id){
		$this->db->where('IdIndividu', $id);
		$query = $this->db->get('mecenat');

		return $query->result();
	}

	public function addMecenat($id){
		$data = array(
			'IdIndividu'		=> $id,
			'Projet'			=> $this->input->post('projet'),
			'Montant'			=> $this->input->post('montant') .'.00',
			'DateInscription'	=> date('Y-m-d H:i:s', time())
		);

		$this->db->insert('mecenat',$data);
	}

	public function deleteMecenat($id){
		$this->db->where('Id', $id);
		$this->db->delete('mecenat');
	}

}

/* End of file Mecenat_model.php */
/* Location: ./application/models/Mecenat_model.php */ ?>