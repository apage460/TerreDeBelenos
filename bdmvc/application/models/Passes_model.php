<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Passes_model extends CI_Model {

	public function __construct()	{
		parent::__construct();
		$this->db->db_select('db_activ');

	}

	public function getValidPasses(){
		$dateToday = date('Y-m-d', strtotime("today"));

		$this->db->db_select('db_activ');

		$this->db->where('DateFin >', $dateToday);
		$this->db->order_by('DateDebut', 'desc');
		$query = $this->db->get('passes', 30);

		return $query->result();
	}

	public function addPasse(){

		$data = array(
            'Nom'        	=> $this->input->post('nom'),
            'Description'   => $this->input->post('description'),
            'Prix'      	=> $this->input->post('prix') . '.00',
            'DateDebut'     => $this->input->post('dateDebut'),
            'DateFin'    	=> $this->input->post('dateFin'),
        );

		$this->db->insert('passes',$data);
	}

	public function getSinglePasse($idPasse){
		$this->db->where('Id', $idPasse);
		$query = $this->db->get('passes');

		return $query->row();
	}

	public function activitesParPasses($idPasse){
		$this->db->where('IdPasse', $idPasse);
		$query = $this->db->get('acces_par_passe');

		return $query->result();
	}

	public function activitesReliees($idPasse){
		$this->db->where('IdPasse', $idPasse);
		$query = $this->db->get('acces_par_passe');

		$vIds = $query->result();
		$activites = [];
		foreach ($vIds as $id) {
			$this->db->db_select('db_activ');
			$this->db->where('Id', $id->IdActivite);
			$this->db->order_by('Type','ASC');
			$query = $this->db->get('activites');

			$activites[] = $query->row();
		}
		return $activites;
	}

	public function updatePasse($idPasse){
		$data = array(
            'Nom'        	=> $this->input->post('nom'),
            'Description'   => $this->input->post('description'),
            'Prix'      	=> $this->input->post('prix') . '.00',
            'DateDebut'     => $this->input->post('dateDebut'),
            'DateFin'    	=> $this->input->post('dateFin'),
        );

		$this->db->where('Id',$idPasse);
		$this->db->update('passes', $data);
	}

	public function linkPasses($idPasse){
		$data = array(
			'IdPasse' => $idPasse,
			'IdActivite' => $this->input->post('activite'),
		);

		$this->db->insert('acces_par_passe', $data);
	}

	public function deleteActiviteReliee($idPasse, $idActivite){
		$this->db->where('IdPasse',$idPasse);
		$this->db->where('IdActivite',$idActivite);

		$this->db->delete('acces_par_passe');
	}

	public function getJoueurs(){
		$this->db->db_select('db_indiv');

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

		if( $this->input->post('pseudo') ):
			$this->db->like('Pseudo', $this->input->post('pseudo') );
		endif;

		$this->db->order_by('Prenom', 'ASC');
		$query = $this->db->get('individus');

		$vJoueurs = $query->result();

		$joueurs = [];

		foreach ($vJoueurs as $key => $joueur) {
			$joueurs[$key]['infoJoueur'] = $joueur;
			$query = "SELECT passe.*, passesacq.CodeEtat FROM db_activ.passes passe LEFT JOIN db_indiv.passes_acquises passesacq ON passe.Id = passesacq.IdPasse WHERE passesacq.IdIndividu = " . $joueur->Id . " AND passe.DateFin > '" . $dateToday ."';";
			$query = $this->db->query($query);

			$joueurs[$key]['passeDuJoueur'] = $query->result();
		}
		

		return $joueurs;
		
	}

	public function searchParPasse(){
		$this->db->db_select('db_indiv');

		$dateToday = date('Y-m-d', strtotime("today"));

		$joueurs = [];
		$vJoueurs = [];

		$this->db->where('IdPasse', $this->input->post('idPasse') );
		$query = $this->db->get('passes_acquises');

		$vIds = $query->result();

		foreach($vIds as $id){
			$this->db->where('Id', $id->IdIndividu );
			$query = $this->db->get('individus');

			$vJoueurs[] = $query->result();
		}

		foreach ($vJoueurs as $key => $joueur) {
			$joueurs[$key]['infoJoueur'] = $joueur[0];
			$query = "SELECT passe.*, passesacq.CodeEtat FROM db_activ.passes passe LEFT JOIN db_indiv.passes_acquises passesacq ON passe.Id = passesacq.IdPasse WHERE passesacq.IdIndividu = " . $joueur[0]->Id . " AND passe.DateFin > '" . $dateToday ."';";
			$query = $this->db->query($query);

			$joueurs[$key]['passeDuJoueur'] = $query->result();
		}

		return $joueurs;

	}

	public function linkPassPlayer($idJoueur, $idPasse = FALSE){
		if($idPasse == FALSE){
			$idPasse = $this->input->post('idPasse');
		}

		$this->db->db_select('db_indiv');

		$data = array(
			'IdIndividu' 	=> $idJoueur,
			'IdPasse' 		=> $idPasse,
			'CodeEtat'		=> 'ACTIF',
			'DateAcquisition' => date('Y-m-d H:i:s', time()),
			'Commentaires'	=> NULL,
		);

		$this->db->insert('passes_acquises',$data);
	}

	public function unlinkPassPlayer($idJoueur, $idPasse){
		$this->db->db_select('db_indiv');

		$data = array('CodeEtat' => 'INACT');

		$this->db->where('IdIndividu', $idJoueur);
		$this->db->where('IdPasse', $idPasse);

		$this->db->update('passes_acquises',$data);
	}
	
	public function relinkPassPlayer($idJoueur, $idPasse){
		$this->db->db_select('db_indiv');

		$data = array('CodeEtat' => 'ACTIF');

		$this->db->where('IdIndividu', $idJoueur);
		$this->db->where('IdPasse', $idPasse);

		$this->db->update('passes_acquises',$data);
	}

	public function downloadPassList($idPasse){
		$this->db->db_select('db_indiv');

		$query = "SELECT indiv.Prenom, indiv.Nom, indiv.Compte FROM db_indiv.individus indiv LEFT JOIN db_indiv.passes_acquises passesacq ON indiv.Id = passesacq.IdIndividu WHERE passesacq.IdPasse = " . $idPasse . " AND passesacq.CodeEtat = 'ACTIF' ORDER BY indiv.Prenom ASC;";

		$query = $this->db->query($query);

		return $query->result_array();
	}
}


/* End of file Activites_model.php */
/* Location: ./application/models/Activites_model.php */
?>