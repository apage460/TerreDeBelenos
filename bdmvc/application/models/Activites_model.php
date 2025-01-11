<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activites_model extends CI_Model {

	public function __construct()	{
		parent::__construct();
		$this->db->db_select('db_activ');

	}

	public function getActivites($idActiv = null){
		$this->db->db_select('db_activ');

		if(!is_null($idActiv)){
			$this->db->where('Id', $idActiv);
		}else{
			$this->db->where('DateFin <', date('Y-m-d H:i:s', strtotime('yesterday') ) );
			$this->db->where('DateDebut >', date('Y-m-d H:i:s', strtotime('-1 month') ) );
			$this->db->where('CodeUnivers', $_SESSION['infoUnivers'] );
			$this->db->where('Type', 'GN' );
		}
		$this->db->order_by('DateFin', 'desc');

		$query = $this->db->get('activites', 15);
		return $query->result();
	}

	public function getPreviousAndNextActivites($today){
		$this->db->db_select('db_activ');

		$strQuery = "(SELECT *
						FROM db_activ.activites  
						WHERE DateDebut > '$today' 
						AND CodeUnivers = '" .$_SESSION['infoUnivers'] ."' 
						AND (Type = 'GN' OR Type = 'CHRONIQ') 
						ORDER BY DateDebut ASC
						LIMIT 1)
						UNION
						(SELECT *
						FROM db_activ.activites 
						WHERE DateDebut < '$today' 
						AND CodeUnivers = '" .$_SESSION['infoUnivers'] ."' 
						AND (Type = 'GN' OR Type = 'CHRONIQ') 
						ORDER BY DateFin DESC
						LIMIT 5);";

		$query = $this->db->query($strQuery);

		return $query->result();
	}

	public function getLastActivite(){
		$this->db->db_select('db_activ');

		$this->db->where('DateDebut <', date('Y-m-d H:i:s', time() ));
		$this->db->where('CodeUnivers', $_SESSION['infoUnivers'] );
		$this->db->order_by('DateDebut', 'desc');

		$query = $this->db->get('activites', 1);

		return $query->row();

	}

	public function getLastActivites() {
		$date6m = date('Y-m-d', strtotime("last year"));

		$this->db->where('DateDebut >', $date6m);
		$this->db->where('CodeUnivers', $_SESSION['infoUnivers'] );
		$this->db->order_by('DateDebut', 'desc');
		$query = $this->db->get('activites', 30);

		return $query->result();
	}

	public function getTypeActivites(){
		$this->db->db_select('db_activ');

		$query = $this->db->get('types_activite');

		return $query->result();
	}

	public function addActivite(){

		$data = array(
            'Nom'        	=> $this->input->post('nom'),
            'Description'   => $this->input->post('description'),
            'Type'      	=> $this->input->post('typeActivite'),
            'DateDebut'     => $this->input->post('dateDebut'),
            'DateFin'    	=> $this->input->post('dateFin'),
            'XPJoueur'		=> $this->input->post('nbXP'),
            'CodeUnivers'	=> $_SESSION['infoUnivers'],
            'Cout'			=> $this->input->post('cout'),
            'CoutMineur'	=> $this->input->post('coutMineur'),
            'CoutEnfant'	=> $this->input->post('coutEnfant'),
        );

		$this->db->insert('activites',$data);
	}

	public function getSingleActivite($id){
		$this->db->where('Id', $id);
		$this->db->where('CodeUnivers', $_SESSION['infoUnivers'] );
		$query = $this->db->get('activites');

		return $query->row();
	}

	public function updateActivite($id){
		$data = array(
            'Nom'        	=> $this->input->post('nom'),
            'Description'   => $this->input->post('description'),
            'Type'      	=> $this->input->post('typeActivite'),
            'DateDebut'     => $this->input->post('dateDebut'),
            'DateFin'    	=> $this->input->post('dateFin'),
        );

        $this->db->where('Id', $id);
        $this->db->update('activites', $data);

	}

	public function deleteActivite($id){
		$this->db->where('Id', $id);
        $this->db->delete('activites');
	}

	public function getNextGNName(){
		$dateNow = date('Y-m-d', time());
		$codeUnivers = $_SESSION['infoUnivers'];

		$strQuery = "SELECT Nom, Id 
					FROM db_activ.activites 
					WHERE (Type = 'GN' OR Type = 'GN')
					AND DateFin >= '$dateNow'
					AND CodeUnivers = '$codeUnivers' 
					ORDER BY DateDebut ASC 
					LIMIT 1;";
		/*$this->db->select('Nom, Id');
		$this->db->where('Type', 'GN');
		$this->db->or_where('Type', 'CHRONIQ');
		$this->db->where('DateFin >=', date('Y-m-d', time()) );
		$this->db->where('CodeUnivers', $_SESSION['infoUnivers'] );
		$this->db->order_by('DateDebut', 'ASC');

		$query = $this->db->get('activites', 1);*/

		$query = $this->db->query($strQuery);
		return $query->row();
	}

	public function getNextGN(){
		$this->db->db_select('db_activ');

		$this->db->where('DateFin >=', date('Y-m-d'));
		$this->db->where('Type', 'GN');
		$this->db->where('CodeUnivers', $_SESSION['infoUnivers'] );

		$query = $this->db->get('activites', 1,);

		return $query->row();
	}

	public function getPreviousGN(){
		$this->db->db_select('db_activ');

		$this->db->where('DateFin <=', date('Y-m-d'));
		$this->db->where('Type', 'GN');
		$this->db->where('CodeUnivers', $_SESSION['infoUnivers'] );
		$this->db->order_by('DateFin','DESC');

		$query = $this->db->get('activites', 1,);

		return $query->row();
	}

}


/* End of file Activites_model.php */
/* Location: ./application/models/Activites_model.php */
?>