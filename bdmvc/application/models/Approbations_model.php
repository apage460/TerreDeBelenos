<?php
class Approbations_model extends CI_Model{

	public function __construct(){
		$this->load->database('db_perso');
	}


	public function getApprobationBGDemandes(){

		$this->db->db_select('db_perso');

		$this->db->select('personnages.Prenom, personnages.Nom, personnages.Id, approbations.DateDemande');
		$this->db->from('personnages','approbations');
		$this->db->join('approbations', 'approbations.IdPersonnage = personnages.Id');
		$this->db->where('approbations.CodeEtat','DEM');
		$this->db->where('approbations.Objet','Histoire');
		$this->db->where('personnages.CodeUnivers', $_SESSION['infoUnivers']);
		$this->db->order_by('approbations.DateDemande','ASC');
		

		$query = $this->db->get();

		return $query->result_array();
	}

	public function getPersonnageHistoire($id){
		$this->db->db_select('db_perso');

		$this->db->select('personnages.Histoire, personnages.IdIndividu, approbations.Id');
		$this->db->from('personnages', 'approbations');
		$this->db->join('approbations', 'approbations.IdPersonnage =' .$id);
		$this->db->where('personnages.Id',$id);
		$this->db->order_by('DateDemande','DESC');

		$query = $this->db->get();

		return $query->row();
	}

	public function updateREFDemande($id, $emailContent){
		$this->db->db_select('db_perso');

		$updateData = array(
			'CodeEtat' 			=> 'REFUS',
			'DateApprobation' 	=> date('Y-m-d H:i:s', time()),
			'Commentaires'		=> $_POST['emailContent'],
			);

		$this->db->where('Id',$id);
		$this->db->update('approbations',$updateData);

	}

	public function updateACCEPDemande($id, $email, $emailContent, $idPersonnage, $idIndividu){
		$this->db->db_select('db_perso');

		if($emailContent == ''){
			$emailContent = null;
		}

		$updateData = array(
			'CodeEtat' 			=> 'ACCEP',
			'DateApprobation' 	=> date('Y-m-d H:i:s', time()),
			'Commentaires'		=> $_POST['emailContent'],
		);

		$this->db->where('Id',$id);
		$this->db->update('approbations',$updateData);

		// Ajoute 5XP si BG accepté.

		$this->db->db_select('db_perso');

		$insertData = array(
			'IdPersonnage'		=> $idPersonnage,
			'Raison' 			=> 'Accep Histoire',
			'XP' 				=> 5,
			'DateInscription' 	=> date('Y-m-d H:i:s', time()),
			'Commentaires'		=> $emailContent,
			);

		$this->db->where('Id',$idPersonnage);
		$this->db->insert('experience',$insertData);
	}

	public function getPersonnageCours(){

		$this->db->db_select('db_perso');

		$sql = "SELECT maitres.*, pilotSkill.Nom as 'NomSkill', CONCAT(perso.Prenom, ' ', perso.Nom) as 'NomPerso'
					FROM db_perso.maitres maitres
					LEFT JOIN db_perso.personnages perso ON maitres.IdPersonnage = perso.Id
					LEFT JOIN db_pilot.competences_regulieres pilotSkill ON maitres.CodeCompetence = pilotSkill.Code
					WHERE maitres.CodeEtat = 'DEM'
					ORDER BY DateCreation ASC;";

		$query = $this->db->query($sql);
		return $query->result();
	}

	public function approvPlanDeCour($idPlan){
		$this->db->db_select('db_perso');

		$data = array(
			'CodeEtat' => 'ACTIF'
		);

		$this->db->where('Id', $idPlan);
		$query = $this->db->update('maitres', $data);
		
	}

	public function refusPlanDeCour(){
		$this->db->db_select('db_perso');

		$data = array(
			'CodeEtat' => 'INACT',
			'Raison' => $this->input->post('raison')
		);

		$this->db->where('Id', $this->input->post('idPlan'));
		$query = $this->db->update('maitres', $data);
	}

	public function activateSkill($idCours){
		$this->db->db_select('db_perso');

		$updateData = array(
			'CodeEtat' => 'ACTIF'
		);
		$this->db->where('Id',$idCours);

		$this->db->update('maitres',$updateData);
	}

	public function desactivateSkill($idCours){
		$this->db->db_select('db_perso');

		$updateData = array(
			'CodeEtat' => 'INACT'
		);
		$this->db->where('Id',$idCours);

		$this->db->update('maitres',$updateData);
	}

	public function getSkillsList(){
		$this->db->db_select('db_pilot');
		$query = "SELECT reg.Code, reg.Nom FROM db_pilot.competences_regulieres reg ORDER BY Nom";

		$listSkills = $this->db->query($query);
		$listSkills = $listSkills->result_array();

		return $listSkills;
	}

	public function addSkill($codeCours, $idPersonnage){
		$this->db->db_select('db_perso');

		$insertData = array(
			'IdPersonnage' 		=> $idPersonnage,
			'CodeCompetence' 	=> $codeCours,
			'CodeEtat' 			=> 'ACTIF',
			'DateCreation' 		=> date('Y-m-d H:i:s', time())
		);

		$this->db->insert('maitres',$insertData);
	}

	/*******/

	public function approbRaces(){
		$this->db->db_select('db_perso');

		$this->db->select('appro.*,perso.Prenom, perso.Nom, pilot.Nom as NomRace');
		$this->db->from('db_perso.approbations appro');
		$this->db->join('db_perso.personnages perso', 'perso.Id = appro.IdPersonnage', 'left');
		$this->db->join('db_pilot.races pilot', 'pilot.Code = perso.CodeRace', 'left');
		$this->db->where('appro.Objet', 'Race');
		$this->db->where('appro.CodeEtat', 'DEM');

		$query = $this->db->get();
		return $query->result();
	}

	public function loadDemande(){
		$idAppro = $this->input->post('idAppro');

		$this->db->db_select('db_perso');

		$this->db->select('perso.Histoire, appro.Id, appro.IdPersonnage');
		$this->db->from('db_perso.approbations appro');
		$this->db->join('db_perso.personnages perso', 'perso.Id = appro.IdPersonnage', 'left');
		$this->db->where('appro.Id', $idAppro);
		$query = $this->db->get();

		return $query->row();
	}

	public function acceptRace($idAppro){
		$this->db->db_select('db_perso');

		$commentaires = $this->input->post('refusCommentaire');

		if ($commentaires == "") {
			$commentaires = null;
		}

		$data = array(
			'CodeEtat' => 'ACCEP',
			'DateApprobation' => date('Y-m-d H:i:s', time()),
			'Commentaires' => $commentaires
		);

		$this->db->where('Id', $idAppro);
		$this->db->update('approbations', $data);
	}

	public function refusRace($idAppro){
		$this->db->db_select('db_perso');

		$data = array(
			'CodeEtat' => 'REFUS',
			'DateApprobation' => date('Y-m-d H:i:s', time()),
			'Commentaires' => $this->input->post('refusCommentaire')
		);

		$this->db->where('Id', $idAppro);
		$this->db->update('approbations', $data);
	}

	public function getTeacher($idPerso = null){
		$strQuery = "SELECT perso.Id as 'idPerso', CONCAT(perso.Prenom, ' ', perso.Nom) as 'persoFull', CONCAT(indiv.Prenom, ' ', indiv.Nom) as 'indivFull'
					FROM db_perso.maitres mai
					LEFT JOIN db_perso.personnages perso ON perso.Id = mai.IdPersonnage
					LEFT JOIN db_indiv.individus indiv ON indiv.Id = perso.IdIndividu ";

		if(!is_null($idPerso)): $strQuery .="WHERE mai.IdPersonnage = $idPerso "; endif;

		$strQuery .= "GROUP BY persoFull ORDER BY persoFull";

		$query = $this->db->query($strQuery);

		if(is_null($idPerso)){
			return $query->result();
		} else{
			return $query->row();
		}
	}

	
}