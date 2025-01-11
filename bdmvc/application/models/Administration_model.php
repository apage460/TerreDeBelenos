<?php
class Administration_model extends CI_Model{

	public function __construct(){
		$this->load->database('db_perso');
	}

	public function addCreditOrDebt($idIndividu, $raison, $montant, $commentaires){
		$this->db->db_select('db_indiv');

		$insertData = array(
			'IdIndividu' => $idIndividu,
			'Montant' => $montant,
			'DateInscription' => date('Y-m-d H:i:s', time()),
			'Raison' => $raison,
			'Commentaires' => $commentaires
		);

		$this->db->insert('sommes_dues',$insertData);
	}

	public function getCreditsAndDebts($idIndiv){
		$this->db->db_select('db_indiv');

		$this->db->where('IdIndividu',$idIndiv);
		$sommes = $this->db->get('sommes_dues');
		$returned['sommes'] = $sommes->result_array();


		$this->db->where('Id',$idIndiv);
		$infosJoueurs = $this->db->get('individus');
		$returned['infosJoueurs'] = $infosJoueurs->result_array();

		return $returned;
	}

	public function removeCreditOrDebt($idSomme){
		$this->db->db_select('db_indiv');

		$this->db->where('Id',$idSomme);
		$this->db->delete('sommes_dues');
	}

	public function getavertissements($idIndiv){
		$this->db->db_select('db_indiv');

		$query = "SELECT avert.*, indiv.Prenom, indiv.Nom, indiv.Pseudo
		FROM db_indiv.avertissements avert 
		LEFT JOIN db_indiv.individus indiv ON avert.IdInscripteur = indiv.Id 
		WHERE IdCible = " .$idIndiv .";";

		$indivAvertissements = $this->db->query($query);
		$returned['avertissements'] = $indivAvertissements->result_array();

		$this->db->where('NiveauAcces >=', 4);
		$inscripteurs = $this->db->get('individus');
		$returned['inscripteurs'] = $inscripteurs->result_array();

		$this->db->where('Id', $idIndiv);
		$infosJoueur = $this->db->get('individus');
		$returned['infosJoueur'] = $infosJoueur->result_array();

		return $returned;

	}

	public function getAllAvertissements(){
		$this->db->db_select('db_indiv');

		$query = "SELECT avert.*, CONCAT(cible.Prenom,' ',cible.Nom) as 'Cible_Nom', cible.Pseudo as 'Cible_Pseudo', CONCAT(insc.Prenom,' ',insc.Nom) as 'Insc_Nom', insc.Pseudo as 'Insc_Pseudo', CONCAT(annul.Prenom,' ',annul.Nom) as 'Annul_Nom', annul.Pseudo as 'Annul_Pseudo'
					FROM avertissements avert 
					LEFT JOIN db_indiv.individus cible ON cible.Id = avert.IdCible 
					LEFT JOIN db_indiv.individus insc ON insc.Id = avert.IdInscripteur 
					LEFT JOIN db_indiv.individus annul ON annul.Id = avert.IdAnnulateur 
					WHERE (avert.DateAnnulation IS NULL AND avert.IdAnnulateur IS NULL) 
					ORDER BY avert.DateInscription DESC;";

		$return = $this->db->query($query);		

		return $return->result();
	}

	public function getSingleAvertissement($idAvertissement){
		$this->db->db_select('db_indiv');

		$query = "SELECT avert.*, CONCAT(cible.Prenom,' ',cible.Nom) as 'Cible_Nom', cible.Pseudo as 'Cible_Pseudo', CONCAT(insc.Prenom,' ',insc.Nom) as 'Insc_Nom', insc.Pseudo as 'Insc_Pseudo', CONCAT(annul.Prenom,' ',annul.Nom) as 'Annul_Nom', annul.Pseudo as 'Annul_Pseudo'
					FROM avertissements avert 
					LEFT JOIN db_indiv.individus cible ON cible.Id = avert.IdCible 
					LEFT JOIN db_indiv.individus insc ON insc.Id = avert.IdInscripteur 
					LEFT JOIN db_indiv.individus annul ON annul.Id = avert.IdAnnulateur 
					WHERE avert.Id = $idAvertissement
					ORDER BY avert.DateInscription DESC;";

		$return = $this->db->query($query);		

		return $return->row();
	}

	public function editAvertissement($idAvertissement){
		$this->db->db_select('db_indiv');

		$data = array(
			'Commentaires' => $this->input->post('Commentaires'),
			'Raison' => $this->input->post('Raison'),
		);

		$this->db->where('Id', $idAvertissement);

		$this->db->update('avertissements', $data);
	}

	public function annulerAvertissement($idAvertissement){
		$this->db->db_select('db_indiv');

		$data = array(
			'IdAnnulateur' => $_SESSION['infoUser']->Id,
			'DateAnnulation' => date('Y-m-d H:i:s', time()),
		);

		$this->db->where('Id', $idAvertissement);

		$this->db->update('avertissements', $data);
	}

	public function addAvertissement(){
		$this->db->db_select('db_indiv');

		$data = array(
			'Type' => $this->input->post('type'),
			'IdCible' => $this->input->post('idIndividu'),
			'Raison' => $this->input->post('raison'),
			'DateInscription' => date('Y-m-d H:i:s', time()),
			'IdInscripteur' => $this->input->post('idInscripteur'),
			'Commentaires' => $this->input->post('commentaires')
		);

		$this->db->insert('avertissements', $data);
	}

	public function addAvertSearch_ajax(){

		$indivData = $this->input->post('indivData');
		$persoData = $this->input->post('persoData');

		$query = "SELECT CONCAT(indiv.Prenom, ' ', indiv.Nom) as 'indivNom', indiv.Pseudo, indiv.Id, CONCAT(perso.Prenom, ' ', perso.Nom) as 'persoNom'
				FROM db_indiv.individus indiv
				LEFT JOIN db_perso.personnages perso ON perso.IdIndividu = indiv.Id
				WHERE (indiv.Prenom LIKE '%$indivData%' OR indiv.Nom LIKE '%$indivData%' OR indiv.Pseudo LIKE '%$indivData%')
				AND (perso.Prenom LIKE '%$persoData%' OR perso.Nom LIKE '%$persoData%')
				AND indiv.CodeEtat = 'ACTIF'
				GROUP BY indiv.Id";

		$result = $this->db->query($query);
		return $result->result();
	}

	public function updateNiveauAcces($idIndividu, $newNiveauAcces){
		$this->db->db_select('db_indiv');

		$data = array('NiveauAcces'=>$newNiveauAcces);

		$this->db->where('id',$idIndividu);
		$this->db->update('individus',$data);

	}

	public function getSommaire(){
		$this->db->db_select('db_indiv');

		$this->db->select("som.*, CONCAT(ind.Prenom, ' ', ind.Nom) as nomIndiv");
		$this->db->from('db_indiv.sommes_dues som');
		$this->db->join('db_indiv.individus ind', 'ind.Id = som.IdIndividu', 'left');
		$this->db->order_by('nomIndiv', 'asc');
		$query = $this->db->get();

		return $query->result();
	}

	public function deleteCreditOuDette($idCredit){
		$this->db->db_select('db_indiv');

		$this->db->where('Id', $idCredit);

		$this->db->delete('sommes_dues');
	}

	public function getIndiv($idIndiv){
		$this->db->db_select('db_indiv');

		$this->db->where('Id', $idIndiv);
		$query = $this->db->get('individus');

		return $query->row();
	}

	public function getBenevolat($idIndiv){
		$this->db->db_select('db_indiv');

		$this->db->where('IdIndividu', $idIndiv);
		$query = $this->db->get('benevolat');

		return $query->result();
	}


	public function saveBenevolat($idIndiv){
		$this->db->db_select('db_indiv');

		$data = array(
			'IdIndividu' => $this->input->post('idIndiv'),
			'Points' => $this->input->post('nombrePoints'),
			'Raison' => $this->input->post('raison'),
			'DateInscription' => date('Y-m-d H:i:s', time())
		);
		$this->db->insert('benevolat', $data);
	}

	public function getCamps($idActivite){
		$this->db->db_select('db_pilot');

		$strQuery = "SELECT * 
					FROM db_activ.campements camp
					WHERE BINARY camp.Code NOT IN (SELECT CodeCampement FROM db_activ.entretien_campements WHERE IdActivite = $idActivite) 
					ORDER BY camp.Nom ASC";

		$query = $this->db->query($strQuery);

		return $query->result();
	}

	public function rateCampement($rateCampement){
		$this->db->db_select('db_activ');

		$this->db->insert_batch('entretien_campements', $rateCampement);
	}

	public function budgetCampement($budgetCampement){
		$this->db->db_select('db_activ');

		$this->db->insert_batch('budget_campements', $budgetCampement);
	}

	public function getAcces(){
		$this->db->db_select('db_indiv');

		$strQuery = "SELECT acc.IdIndividu, ind.Id, ind.Prenom, ind.Nom, ind.NiveauAcces, acc.Acces, acc.CodeUnivers
					FROM db_indiv.acces acc
					LEFT JOIN db_indiv.individus ind ON acc.IdIndividu = ind.Id
					WHERE acc.CodeUnivers = '" .$_SESSION['infoUnivers'] ."'
					AND ind.CodeEtat = 'ACTIF' 
					ORDER BY ind.Prenom ASC, ind.Nom ASC, acc.Acces ASC;";

		$query = $this->db->query($strQuery);

		return $query->result();
	}

	public function removeAcces($idIndiv, $acces){
		$this->db->db_select('db_indiv');

		$strQuery = "DELETE FROM db_indiv.acces WHERE IdIndividu = $idIndiv AND Acces = '$acces' AND CodeUnivers = '" .$_SESSION['infoUnivers'] ."'";

		$query = $this->db->query($strQuery);
	}

	public function getIndividus($prenom, $nom){
		$this->db->db_select('db_indiv');

		$this->db->select('Id, Prenom, Nom');
		$this->db->from('db_indiv.individus');
		if(!empty($prenom)){
			$this->db->like('Prenom', $prenom, 'BOTH');
		}
		if(!empty($nom)){
			$this->db->like('Nom', $nom, 'BOTH');
		}
		$this->db->where('CodeEtat', 'ACTIF');

		$query = $this->db->get();

		return $query->result();
	}

	public function addAcces($idIndiv, $acces){
		$this->db->db_select('db_indiv');

		//CHECK IF EXISTS
		$this->db->select('*');
		$this->db->where('IdIndividu', $idIndiv);
		$this->db->where('Acces', $acces);
		$this->db->where('CodeUnivers', $_SESSION['infoUnivers']);

		$query = $this->db->get('acces');

		//IF DOESN'T EXIST, CREATE
		if($query->num_rows() == 0){
			$data = array(
				'IdIndividu' => $idIndiv,
				'Acces' => $acces,
				'CodeUnivers' => $_SESSION['infoUnivers']
			);

			$this->db->insert('acces', $data);

			$strQuery = "SELECT Prenom, Nom
						FROM db_indiv.individus
						WHERE Id = $idIndiv";

			$query = $this->db->query($strQuery);

			$result = $query->row();

			return array(
				'Prenom' => $result->Prenom,
				'Nom' => $result->Nom,
				'Acces' => $acces
			);
		}

		return 0;
	}
}