<?php
class Old_inscriptions_model extends CI_Model{

	public function __construct(){
		
	}


	public function getActivites($idActiv = null){
		$this->db->db_select('db_activ');

		$this->db->select('Id,Nom, Type');
		if($idActiv == null) :
			$this->db->where('DateDebut <=',  date('Y-m-d', strtotime('+2 week') ));
			$this->db->where('DateDebut >=', date('Y-m-d', strtotime('last year') ) );
		else :
			$this->db->where('Id',$idActiv);
		endif;
		$this->db->order_by('DateFin', 'DESC');
		$query = $this->db->get('activites');

		return $query->result();
	}


	public function getInscriptions($idActivite, $prenom){

		$query = 'SELECT insc.*, indiv.Prenom, indiv.Nom, indiv.ActivitesGratuites, (SELECT SUM(sommes.Montant) as Montant FROM db_indiv.sommes_dues sommes WHERE sommes.IdIndividu = insc.IdIndividu AND Montant <= 0) as `Montant_Du`
		FROM db_activ.inscriptions insc 
		LEFT JOIN db_indiv.individus indiv ON insc.IdIndividu = indiv.Id
		LEFT JOIN db_indiv.presences pres ON insc.IdIndividu = pres.IdIndividu AND insc.IdActivite = pres.IdActivite
		WHERE insc.IdActivite = ' .$idActivite  
		.' AND pres.IdIndividu IS NULL
        AND indiv.Prenom IS NOT NULL
		ORDER BY indiv.Prenom';

		$query = $this->db->query($query);

		return $query->result_object();
	}	

	public function getCouts($type){
		$this->db->where('Type',$type);
		$query = $this->db->get('types_activite');

		return $query->row();
	}

	public function addPresence($idIndividu, $idActivite, $commentaires, $montantDu, $nomActivite){
		
		/*** INSERT DANS PRESENCES ***/

		$this->db->db_select('db_indiv');

		$insertData = array(
			'IdIndividu' => $idIndividu,
			'IdActivite' => $idActivite,
			'Commentaires' => $commentaires,
			'DateInscription' => date('Y-m-d H:i:s', time()),
		);

		$this->db->insert('presences', $insertData);

		/*** INSERT DANS REMARQUES***/

		if($commentaires == ''){
			$message = "Paiement : " .$montantDu ." pour " .$nomActivite .".";
		}else{
			$message = $commentaires ."\n Paiement : " .$montantDu ." pour " .$nomActivite .".";
		}
		/* UPDATE INSCRIPTIONS */

		$this->db->db_select('db_activ');

		$data = array(
			'Commentaires' => $commentaires
		);

		$this->db->where('IdActivite',$idActivite);
		$this->db->where('IdIndividu', $idIndividu);

		$this->db->update('inscriptions',$data);

		/* AJOUT REMARQUE */
		$this->db->db_select('db_indiv');

		$insertData = array(
			'IdIndividu' 	=> $idIndividu,
			'DateCreation'	=> date('Y-m-d H:i:s', time()),
			'Type' 			=> 'PAIE',
			'Message'		=> 'Présence confirmée : ' .$nomActivite .'.',
		);

		$this->db->insert('remarques', $insertData);

		/*** Montant Dû à 0 ***/

		$this->db->query("DELETE FROM sommes_dues WHERE IdIndividu = " .$idIndividu ." AND Raison = 'Préinscription - " .$nomActivite ."'");

	}

	public function getPresences($idActivite){
		$this->db->db_select('db_indiv');

		$query = "SELECT * FROM  db_indiv.presences pres LEFT JOIN db_indiv.individus indiv ON pres.IdIndividu = indiv.Id WHERE pres.IdActivite = " .$idActivite;

		$query = $this->db->query($query);

		return $query->result_object();
	}

	public function delPresence($idIndividu, $idActivite){
		$this->db->db_select('db_indiv');

		$this->db->where('IdIndividu' , $idIndividu);
		$this->db->where('IdActivite' , $idActivite);
		$this->db->delete('presences');
	}

	public function quickInscription($idActivite, $idIndividu, $nomActivite, $typeActivite, $montant){

		/* INSERT PRESENCES */

		$this->db->db_select('db_indiv');

		$dataInsert = array(
			'IdIndividu' => $idIndividu,
			'IdActivite' => $idActivite,
			'DateInscription' => date('Y-m-d H:i:s', time()),
		);

		$this->db->insert('presences',$dataInsert);

		if ($typeActivite != 'GN' && $typeActivite != 'GALA') {

			$dataInsert = array(
				'IdIndividu' => $idIndividu,
				'Raison' => 'Inscription ' .$nomActivite,
				'XP' => 20,
				'DateInscription' => date('Y-m-d H:i:s', time()),
			);

			$this->db->insert('experience',$dataInsert);
		}

		/* INSERT INSCRIPTIONS */

		if ($typeActivite == 'GN') {
			$this->db->db_select('db_activ');

			$dataInsert = array(
				'IdIndividu' => $idIndividu,
				'IdActivite' => $idActivite,
				'IdPersonnage' => 0,
				'NomPersonnage' => '',
				'PrixInscrit' => $montant,
				'DateInscription' => date('Y-m-d H:i:s', time()),
				'Commentaires' => 'Inscription Rapide',
			);

			$this->db->insert('inscriptions',$dataInsert);
		}
	}

	public function activiteGratuite($idIndiv,$nbActGrat){

		$idActivite = $this->input->post('idActiv');

		$this->db->db_select('db_indiv');
		$data = array(
			'ActivitesGratuites' => $nbActGrat-1,
		);
		$this->db->where('Id', $idIndiv);
		$this->db->update('individus', $data);

		$data = array(
			'IdIndividu' => $idIndiv,
			'IdActivite' => $idActivite,
			'DateInscription' => date('Y-m-d H:i:s', time()),
			'Commentaires' => NULL,
		);

		$this->db->insert('presences', $data);

	}
}