<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inscriptions_model extends CI_Model {
	public function __construct()	{
		parent::__construct();
		$this->db->db_select('db_activ');
	}

	public function getActivites($idActiv = null){
		$this->db->db_select('db_activ');

		if($_SESSION['infoUnivers'] == 'BELEJR'){
			$this->db->where('DateFin <', date('Y-m-d H:i:s', strtotime('+5 week') ) );
		} else{
			$this->db->where('DateFin <', date('Y-m-d H:i:s', strtotime('+14 day') ) );
		}
		
		$this->db->where('CodeUnivers', $_SESSION['infoUnivers'] );
		if( !is_null($idActiv) ){
			$this->db->where('Id', $idActiv );
		}
		$this->db->order_by('DateFin', 'desc');

		$query = $this->db->get('activites', 15);
		return $query->result();
	}

	public function searchIndiv(){
		$this->db->db_select('db_indiv');

		/* WHEREs INDIV */
		if($this->input->post('prenomIndiv') != ''){
			$this->db->like('Prenom', $this->input->post('prenomIndiv'), true );
		}
		if($this->input->post('nomIndiv') != ''){
			$this->db->like('Nom', $this->input->post('nomIndiv'), true );
		}
		if($this->input->post('pseudoIndiv') != ''){
			$this->db->like('Pseudo', $this->input->post('pseudoIndiv'), true );
		}
		if($this->input->post('compteIndiv') != ''){
			$this->db->like('Compte', $this->input->post('compteIndiv'), true );
		}

		$this->db->where('CodeEtat', 'ACTIF' );

		$this->db->order_by('Prenom', 'asc');
		$query = $this->db->get('individus');
		return $query->result();

	}
	public function hasPaid($idIndiv, $idActiv){
		$this->db->db_select('db_indiv');
		$this->db->where('IdIndividu', $idIndiv);
		$this->db->where('IdActivite', $idActiv);
		$query = $this->db->get('presences');

		return $query->row();
	}

	public function isInscrit($idIndiv, $idActiv){
		$this->db->db_select('db_activ');
		$this->db->select('inscr.*, indiv.*, activ.Id as IdActiv, activ.Nom as NomActiv');
		$this->db->from('db_activ.inscriptions inscr');
		$this->db->join('db_indiv.individus indiv', 'indiv.Id = inscr.IdIndividu', 'left');
		$this->db->join('db_activ.activites activ', 'activ.Id = inscr.IdActivite', 'left');
		$this->db->where('inscr.IdIndividu', $idIndiv);
		$this->db->where('inscr.IdActivite', $idActiv);

		$query = $this->db->get('inscriptions');

		if ( $query->row() != null ){
			return $query->row();
		} else{

			$this->db->select('indiv.*, activ.Type, activ.Id as IdActiv, activ.Nom as NomActiv');
			$this->db->from('db_indiv.individus indiv, db_activ.activites activ');
			$this->db->where('indiv.Id', $idIndiv);
			$this->db->where('activ.Id', $idActiv);
			$this->db->where('activ.CodeUnivers', $_SESSION['infoUnivers'] );

			$query = $this->db->get();

			return $query->row();
		}

	}

	public function hasDebts($idIndiv){
		$this->db->db_select('db_indiv');

		$this->db->select('SUM(Montant) as Montant');
		$this->db->where('IdIndividu', $idIndiv);
		$query = $this->db->get('sommes_dues');
		return $query->result();
	}

	public function getPersonnages($idIndiv, $idActiv){
		$this->db->db_select('db_perso');

		$this->db->select('Id, CONCAT(Prenom, " ", Nom) as NomComplet');

		$this->db->where('IdIndividu', $idIndiv);
		$this->db->where('CodeEtat !=', 'MORT');
		$this->db->where('CodeEtat !=', 'SUPPR');
		$this->db->where('CodeEtat !=', 'RETIR');
		$this->db->where('CodeEtat !=', 'INACT');
		$this->db->where('CodeUnivers', $_SESSION['infoUnivers'] );

		$query = $this->db->get('personnages');

		return $query->result();
	}

	public function getPreInscription($idIndiv, $idActiv, $indivHasPass = false){
		$this->db->db_select('db_activ');

		if($indivHasPass == true){
			$data = array(
				'PrixInscrit' => 0.00,
				'Commentaires' => 'Prix modifié parce que passe'
			);

			$this->db->where('IdIndividu', $idIndiv);
			$this->db->where('IdActivite', $idActiv);

			$this->db->update('inscriptions', $data);

			$this->db->flush_cache();
		}

		$this->db->where('IdIndividu', $idIndiv);
		$this->db->where('IdActivite', $idActiv);

		$query = $this->db->get('inscriptions');

		if($query->num_rows() > 0){
			return $query->row();
		} else {
			return null;
		}
	}

	public function getPrix($idActiv){
		$this->db->db_select('db_activ');

		$sql = "SELECT type.PrixRegulier 
			FROM db_activ.types_activite type 
			LEFT JOIN db_activ.activites activ ON activ.Type = type.Type
			WHERE activ.Id = $idActiv";

		$query = $this->db->query($sql);

		if($query->num_rows() > 0){
			return $query->row();
		} else {
			return null;
		}
	}

	public function addInscription($idIndiv, $idActiv){
		$idPerso = $this->input->post('idPerso');

		$this->db->db_select('db_perso');
		$this->db->select('CONCAT(Prenom, " ", Nom) as NomComplet, Niveau');
		$this->db->where('Id', $idPerso);
		$query = $this->db->get('personnages');
		$nomPerso = $query->result();

		$message = "Niveau " .(($nomPerso[0]->Niveau)+1) ." !";

		$data = array(
			'IdPersonnage' => $idPerso,
			'Message' => $message,
			'Type' => 'LEVEL',
			'DateCreation' => date('Y-m-d H:i:s', time())
		);

		$this->db->insert('remarques',$data);

		$this->db->db_select('db_activ');

		$data = array(
			'IdActivite' => $idActiv,
			'IdIndividu' =>	$idIndiv,
			'IdPersonnage' => $idPerso,
			'NomPersonnage' => $nomPerso[0]->NomComplet,
			'DateInscription' => date('Y-m-d H:i:s', time()),
			'PrixInscrit' => $this->getMontant($idIndiv, $idActiv),
			'Commentaires' => null,
		);

		$this->db->insert('inscriptions', $data);


	}

	public function addPresence($idIndiv, $idActiv, $idPerso){
		if($idActiv == 'null'){
			$idActiv = $this->input->post('idActiv');
		}
		$montant = $this->input->post('montant');
		if($montant == null ){
			$montant = '0.00$';
		}

		$this->db->db_select('db_indiv');

		$data = array(
			'IdIndividu' => $idIndiv,
			'IdActivite' => $idActiv,
			'DateInscription' => date('Y-m-d H:i:s', time()),
			'Recu' => $montant,
		);

		$this->db->insert('presences', $data);

		$this->db->db_select('db_perso');

		$data = array( 'CodeEtat' => 'LEVEL' );
		$this->db->where('IdPersonnage', $idPerso);
		$this->db->where('CodeEtat', 'PRLVL');
		$this->db->update('competences_acquises', $data);


		$data = array(
			'IdIndividu' => $idIndiv,
			'Quantite' => '1',
			'DateInscription' => date('Y-m-d H:i:s', time()),
			'DateExpiration' => date('Y-m-d', strtotime('December 31 +1 year')),
			'Raison' => 'Presence'
		);

		$this->db->insert('db_indiv.arcanns', $data);		
	}

	public function setInscriptionToGratuit($idIndiv, $idActiv){
		$this->db->db_select('db_activ');

		$data = array(
			'BilletPrincipal' => 'GRATUIT',
			'PrixInscrit' => '0.00'
		);

		$this->db->where('IdIndividu', $idIndiv);
		$this->db->where('IdActivite', $idActiv);

		$this->db->update('inscriptions', $data);
	}

	public function indivHasPass($idIndiv, $idActiv){
		$this->db->db_select('db_activ');

		//get pass ID
		$strQuery = "SELECT IdPasse FROM db_activ.acces_par_passe WHERE IdActivite = $idActiv;";

		$query = $this->db->query($strQuery);

		$return = $query->row();

		(!is_null($return))?$idPasse = $return->IdPasse : null;

		if(!is_null($idPasse)){
			$this->db->flush_cache();

			$strQuery = "SELECT * FROM db_indiv.passes_acquises WHERE IdIndividu = $idIndiv AND IdPasse = $idPasse AND CodeEtat = 'ACTIF';";
			$query = $this->db->query($strQuery);

			if(!is_null($query->row())){
				return true;
			}else{
				return false;
			}
		} else {
			return false;
		}

	}

	public function getActivePasses(){
		$this->db->db_select('db_activ');

		$today = date('Y-m-d H:i:s', time());

		$this->db->where('DateFin >', $today);

		$query = $this->db->get('passes');

		return $query->result();
	}

	public function getMontant($idIndiv, $idActiv){
		$this->db->db_select('db_indiv');

		$this->db->select('DateNaissance, Tuteur');
		$this->db->where('Id', $idIndiv);

		$query = $this->db->get('individus');
		$info = $query->row();

		/*** Selon l'âge ***/
		$from = new DateTime($info->DateNaissance);
		$to   = new DateTime('today');
		$age = $from->diff($to)->y;

		/*** Get Type ***/
		$this->db->select('activ.Cout, activ.CoutMineur');
		$this->db->from('db_activ.activites activ');
		$this->db->where('activ.Id', $idActiv);
		$query = $this->db->get();
		$prix = $query->row();


		if($age >= 16){
			$montant =  $prix->Cout;
		}
		else{
			$montant = $prix->CoutMineur;
		}

		/*** Si Groupe Cadre ***/
		if($info->Tuteur == 'GroupeCadre' && $age >= 12){
			$montant =  '25.00';
		}

		/*** Si passe de saison ***/
		$this->db->db_select('db_activ');

		$this->db->from('db_indiv.passes_acquises acq');

		$this->db->join('db_activ.passes passes', 'passes.Id = acq.IdPasse', 'left');
		$this->db->join('db_activ.acces_par_passe app', 'app.IdPasse = acq.IdPasse', 'left');
		$this->db->where('acq.IdIndividu', $idIndiv);
		$this->db->where('app.IdActivite', $idActiv);
		$this->db->where('acq.CodeEtat !=', 'INACT');
		$query = $this->db->get();

		$hasPasse = $query->row();

		if($hasPasse){
			$montant = '0.00';
		}

		$this->db->db_select('db_activ');

		return $montant;

	}

	public function removeActiviteGratuite($idIndiv){
		$this->db->db_select('db_indiv');

		$this->db->select('ActivitesGratuites');
		$this->db->where('Id', $idIndiv);
		$query = $this->db->get('individus');

		$nbActivitesGratuites = $query->row();

		$data = array(
			'ActivitesGratuites' => ($nbActivitesGratuites->ActivitesGratuites)-1
		);
		$this->db->where('Id', $idIndiv);
		$this->db->update('individus', $data);
	}

	public function getActiviteType($idActiv){
		$this->db->db_select('db_activ');
		$this->db->select('Type');
		$this->db->where('Id', $idActiv);
		$query = $this->db->get('activites');
		$activ = $query->row();

		return $activ->Type;
	}

	public function addXP($idIndiv, $idActiv){
		$idPerso = $this->input->post('idPerso');
		$this->db->db_select('db_activ');
		$this->db->select('Type, Nom, XPJoueur');
		$this->db->where('Id', $idActiv);
		$query = $this->db->get('activites');
		$activ = $query->row();

		if($activ->Type != 'GN'){
			$this->db->db_select('db_indiv');
			$data = array(
				'IdIndividu' => $idIndiv,
				'Raison' => $activ->Nom,
				'XP' => $activ->XPJoueur,
				'DateInscription' => date('Y-m-d H:i:s', time()),
				'Commentaires' => null
			);

			$this->db->insert('experience', $data);

		}else{
			$this->db->db_select('db_perso');
			$this->db->select('Niveau');
			$this->db->where('Id', $idPerso);
			$query = $this->db->get('personnages');
			$perso = $query->row();
			/*** LVL UP ***/
			$data = array('Niveau' => ($perso->Niveau)+1);
			$this->db->where('Id', $idPerso);
			$this->db->update('personnages', $data);
			/*** Give XP ON LEVEL < 15 ***/
			//GET NEW LEVEL
			$this->db->select('Niveau');
			$this->db->where('Id', $idPerso);
			$query = $this->db->get('personnages');
			$perso = $query->row();
			if($perso->Niveau > 1 && $perso->Niveau < 5){
				$data = array(
					'IdPersonnage' => $idPerso,
					'Raison' => 'Niveau ' .( $perso->Niveau),
					'XP' => 50,
					'DateInscription' => date('Y-m-d H:i:s', time()),
					'Commentaires' => null,
					'IdEnseignement' => null
				);

				$this->db->insert('experience', $data);
			}
			if($perso->Niveau >= 5 && $perso->Niveau < 15){
				$data = array(
					'IdPersonnage' => $idPerso,
					'Raison' => 'Niveau ' .( $perso->Niveau),
					'XP' => 20,
					'DateInscription' => date('Y-m-d H:i:s', time()),
					'Commentaires' => null,
					'IdEnseignement' => null
				);

				$this->db->insert('experience', $data);
			}
			/*** UPDATE LEVEL SKILLS TO ACTIVE ***/
			$data = array( 'CodeEtat' => 'ACTIF' );
			$this->db->where('Id', $idPerso);
			$this->db->where('CodeEtat', 'LEVEL');
			$this->db->update('competences_acquises', $data);

			/***  Activate perso ***/
			$data = array(
				'CodeEtat' => 'ACTIF',
			);
			$this->db->where('IdIndividu',$idIndiv);
			$this->db->where('CodeEtat','LEVEL');
			$this->db->update('personnages',$data);

			$data = array(
				'CodeEtat' => 'LEVEL',
			);
			$this->db->where('Id',$idPerso);
			$this->db->update('personnages',$data);
		}

		if($_SESSION['infoUnivers'] == 'BELEJR'){
			$this->db->db_select('db_indiv');
			$data = array(
				'IdIndividu' => $idIndiv,
				'Raison' => 'Participation BéléJR',
				'XP' => 10,
				'DateInscription' => date('Y-m-d H:i:s', time())
			);

			$this->db->insert('experience',$data);
		}
	}

	public function searchInscriptions($idActiv){
		$this->db->db_select('db_activ');
		$this->db->select('CONCAT (indiv.Prenom, " " , indiv.Nom) as NomIndivComplet, insc.*');
		$this->db->from('db_activ.inscriptions insc');
		$this->db->join('db_indiv.individus indiv', 'indiv.Id =  insc.IdIndividu', 'left');
		$this->db->join('db_indiv.presences pres', 'pres.IdIndividu = insc.IdIndividu AND pres.IdActivite = insc.IdActivite', 'left');
		$this->db->where('insc.IdActivite', $idActiv);
		$this->db->where('pres.IdIndividu', null);
		$this->db->where('indiv.Prenom !=', null);
		$this->db->order_by('indiv.Prenom', 'ASC');

		$query = $this->db->get();
		return $query->result();
	}

	public function deleteInscription($idActiv, $idIndiv, $idPerso){
		$this->db->db_select('db_activ');
		$this->db->where('IdIndividu', $idIndiv);
		$this->db->where('IdActivite', $idActiv);
		$this->db->where('IdPersonnage', $idPerso);

		$this->db->delete('inscriptions');

		/*** Remove XP Personnage ***/

		$this->db->select('Nom');
		$this->db->where('Id', $idActiv);
		$query = $this->db->get('activites');
		$nomActiv = $query->row();

		$data = array(
			'Raison' => 'Incr. '.$nomActiv->Nom .' annulée',
			'XP' => 0,
			'DateInscription' => date('Y-m-d H:i:s', time()),
		);

		$this->db->db_select('db_perso');
		$this->db->where('IdPersonnage', $idPerso);
		$this->db->like('Raison', $nomActiv->Nom);

		$this->db->update('experience', $data);

		/*** Level Down ***/

		$this->db->select('Niveau');
		$this->db->where('Id', $idPerso);
		$query = $this->db->get('personnages');
		$niveau = $query->row();

		$data = array(
			'Niveau' => ($niveau->Niveau)-1
		);

		$this->db->where('Id', $idPerso);
		$this->db->update('personnages', $data);

		$data = array(
			'IdPersonnage' => $idPerso,
			'Message' => 'Niveau annulé!',
			'Type' => 'LEVEL',
			'DateCreation' => date('Y-m-d H:i:s', time())
		);

		$this->db->insert('remarques',$data);
	}

	public function getPresences($idActiv){
		$this->db->select('pres.*, indiv.Compte, CONCAT(indiv.Prenom, " ", indiv.Nom) as NomIndivComplet');
		$this->db->from('db_indiv.presences pres');
		$this->db->join('db_indiv.individus indiv', 'indiv.Id = pres.IdIndividu', 'left');
		$this->db->where('pres.IdActivite', $idActiv);
		$this->db->order_by('NomIndivComplet', 'asc');

		$query = $this->db->get();
		return $query->result();
	}

	public function downloadPresencesList($idActiv){
		$this->db->select('CONCAT(indiv.Prenom, " ", indiv.Nom) as NomIndivComplet, pres.Recu');
		$this->db->from('db_indiv.presences pres');
		$this->db->join('db_indiv.individus indiv', 'indiv.Id = pres.IdIndividu', 'left');
		$this->db->where('pres.IdActivite', $idActiv);
		$this->db->order_by('NomIndivComplet', 'asc');

		$query = $this->db->get();
		return $query->result_array();
	}

	public function downloadInscriptionList($idActiv){
		$strQuery = "SELECT CONCAT(ind.Prenom, ' ', ind.Nom) as 'Nom Joueur', ins.NomPersonnage as 'Nom Personnage', per.CodeReligion as 'Religion', Enfants as 'Nb Lutins', Repas as 'Nb Repas', GroupeCadre, BilletPrincipal as 'Billet', PrixInscrit as 'Prix Inscription'
			FROM db_activ.inscriptions ins
			LEFT JOIN db_perso.personnages per ON per.Id = ins.IdPersonnage
			LEFT JOIN db_indiv.individus ind ON ind.Id = ins.IdIndividu
			WHERE ins.IdActivite = $idActiv";

		$query = $this->db->query($strQuery);
		return $query->result_array();
	}

	public function isLocation($idActiv){
		$this->db->db_select('db_activ');

		$this->db->select('typ.IndLocation');
		$this->db->from('db_activ.activites act');
		$this->db->where('Id', $idActiv);
		$this->db->join('db_activ.types_activite typ', 'typ.Type = act.Type', 'left');
		$query = $this->db->get();

		$activite = $query->row();

		if ($activite->IndLocation == '1') {
			return true;
		} else{
			return false;
		}
	}

	public function getInscriptions($idActiv){
		$this->db->db_select('db_activ');

		$query = "SELECT CONCAT(indiv.Prenom,' ', indiv.Nom) AS nomJoueur, act.*, ins.*, act.Type as actType, total.repas as totalRepas 
				FROM inscriptions ins 
				LEFT JOIN db_indiv.individus indiv ON indiv.Id = ins.IdIndividu 
				LEFT JOIN db_activ.activites act ON act.Id = ins.IdActivite 
				CROSS JOIN (
					SELECT SUM(Repas) as repas FROM db_activ.inscriptions WHERE IdActivite = $idActiv
				) total
				WHERE IdActivite = $idActiv
				ORDER BY nomJoueur ASC";

		$query = $this->db->query($query);

		return $query->result();
	}

	public function getTypeActivite($idActiv){
		$this->db->db_select('db_activ');

		$this->db->select('Type');
		$this->db->from('activites');
		$this->db->where('Id', $idActiv);

		$query = $this->db->get();

		return $query->row();
	}

	public function getAvailableSkills($idIndiv, $idActiv){
		//GET PERSO ID

		$this->db->db_select('db_activ');

		$this->db->select('IdPersonnage');
		$this->db->from('inscriptions');
		$this->db->where('IdIndividu', $idIndiv);
		$this->db->where('IdActivite', $idActiv);

		$query = $this->db->get();

		$result = $query->row();

		if(is_null($result) || is_null($result->IdPersonnage)){
			return false;
		}

		$idPerso = $result->IdPersonnage;

		$strQuery = "SELECT DISTINCT(Categorie)
					FROM db_pilot.competences_regulieres
					WHERE Code IN (SELECT CodeCompetence FROM db_perso.competences_acquises WHERE IdPersonnage = $idPerso)
					ORDER BY Categorie ASC;";

		$query = $this->db->query($strQuery);
		return $query->result();
	}
	
	public function getLevelPerso($idIndiv, $idActiv){
		//GET PERSO ID
		$this->db->db_select('db_activ');

		$this->db->select('IdPersonnage');
		$this->db->from('inscriptions');
		$this->db->where('IdIndividu', $idIndiv);
		$this->db->where('IdActivite', $idActiv);

		$query = $this->db->get();

		$result = $query->row();

		if(is_null($result) || is_null($result->IdPersonnage)){
			return false;
		}

		$idPerso = $result->IdPersonnage;

		$strQuery = "SELECT Niveau
					FROM db_perso.personnages
					WHERE Id = $idPerso;";

		$query = $this->db->query($strQuery);

		return $query->row();
	}

	public function getGroups(){
		$this->db->db_select('db_group');
		$codeUnivers = $_SESSION['infoUnivers'];
		$strQuery = "SELECT Id, Nom 
					FROM db_group.groupes 
					WHERE (CodeEtat = 'ACTIF' OR CodeEtat = 'NOUVO') AND CodeUnivers = '$codeUnivers'
					ORDER BY Nom ASC";

		$query = $this->db->query($strQuery);

		return $query->result();
	}

	public function editChroniqueInscription($idIndiv, $idActiv){
		$this->db->db_select('db_activ');

		$idGroupe = utf8_encode(explode('--',$this->input->post('groupe'))[0]);
		$nomGroupe = utf8_encode(explode('--',$this->input->post('groupe'))[1]);

		if($this->input->post('niveauPersonnage') < 5){
			$jetons = 3;
		}elseif($this->input->post('niveauPersonnage') < 9){
			$jetons = 4;
		}elseif($this->input->post('niveauPersonnage') < 14){
			$jetons = 5;
		}else{
			$jetons = 6;
		}

		$data = array(
			'NiveauPersonnage' => $this->input->post('niveauPersonnage'),
			'ClasseAlternative' => $this->input->post('classeAlternative'),
			'Identite' => ($this->input->post('identite') != '')?$this->input->post('identite') : null,
			'IdGroupe' => $idGroupe,
			'NomGroupe' => $nomGroupe,
			'Jetons' => $jetons
		);

		$this->db->where('IdIndividu', $idIndiv);
		$this->db->where('IdActivite', $idActiv);

		$this->db->update('inscriptions', $data);
	}

	public function getTotalRepas($idActiv){
		$this->db->db_select('db_activ');

		$this->db->select('SUM(Repas) as `totalRepas`');
		$this->db->from('inscriptions');
		$this->db->where('IdActivite', $idActiv);
		$this->db->where('Repas >', 0);

		$query = $this->db->get();

		return $query->row();
	}

	public function getGroupesForActivite($idActiv){
		$this->db->db_select('db_activ');

		$this->db->select('NomGroupe, COUNT(NomGroupe) AS `QteJoueurs`');
		$this->db->from('inscriptions');
		$this->db->where('IdActivite', $idActiv);
		$this->db->group_by('NomGroupe');
		$this->db->order_by('NomGroupe', 'ASC');

		$query = $this->db->get();

		return $query->result();
	}

	public function hasNoMetier($idPerso){
		$this->db->db_select('db_perso');

		if(is_null($idPerso)){
			return false;
		}

		$strQuery = "SELECT `Precision` 
					FROM db_perso.competences_acquises
					WHERE IdPersonnage = $idPerso 
					AND CodeCompetence = 'METIER1'";

		$query = $this->db->query($strQuery);

		$result = $query->row();

		if(is_null($result->Precision)){
			return true;
		} else{
			return false;
		}
	}

	public function getMetiers(){
		$this->db->db_select('db_pilot');

		$query = $this->db->get('metiers');

		return $query->result();
	}

	public function addMetier($idPerso){
		$this->db->db_select('db_perso');
		/*CHECK IF HAS SKILL AND ADD IT IF NOT*/
		$strQuery = "SELECT * FROM db_perso.competences_acquises WHERE CodeCompetence = 'METIER1' AND IdPersonnage = $idPerso";
		$query = $this->db->query($strQuery);

		if($query->num_rows() == 0){
			$data = array(
				'IdPersonnage' => $idPerso,
				'CodeCompetence' => 'METIER1',
				'Type' => 'REG',
				'Usages' => null,
				'CoutXP' => 0,
				'DateCreation' => date('Y-m-d H:i:s', time()),
				'CodeAcquisition' => 'DEPART',
				'CodeEtat' => 'ACTIF',
				'Precision' => $this->input->post('metier')
			);

			$this->db->insert('db_perso.competences_acquises', $data);
		}else{
			/*ADD MÉTIER PRECISION*/
			$data = array(
				'Precision' => $this->input->post('metier')
			);

			$this->db->where('IdPersonnage', $idPerso);
			$this->db->where('CodeCompetence', 'METIER1');


			$this->db->update('db_perso.competences_acquises', $data);
		}
	}

	function getBelePoints($idIndiv){
		$this->db->db_select('db_indiv');

		$this->db->select("SUM(Points) as 'belePoints'");
		$this->db->from('benevolat');
		$this->db->where('IdIndividu', $idIndiv);

		$query = $this->db->get();

		return $query->result();
	}

	function useBelePoints($idIndiv, $nbPoints){
		$this->db->db_select('db_indiv');

		$data = array(
			'IdIndividu' => $idIndiv,
			'Raison' => 'Paiement activité',
			'Points' => -$nbPoints,
			'DateInscription' => date('Y-m-d H:i:s', time())
		);

		$this->db->insert('benevolat', $data);

	}

	public function payPass($idIndiv, $idPass){
		$this->db->db_select('db_indiv');

		$data = array(
			'Montant' => 0.00,
			'Commentaires' => 'Payé ' . date('Y-m-d H:i:s', time())
		);

		$this->db->where('IdIndividu', $idIndiv);
		$this->db->where('IdPasse', $idPass);

		$this->db->update('sommes_dues', $data);
	}

	public function indivHasSelectedPass($idIndiv, $idPass){
		$this->db->db_select('db_indiv');

		$this->db->where('IdIndividu', $idIndiv);
		$this->db->where('IdPasse', $idPass);

		$query = $this->db->get('passes_acquises')->result();

		if(empty($query)){
			return FALSE;
		} else{
			return TRUE;
		}
	}
}

/* End of file Inscriptions_model.php */
/* Location: ./application/models/Inscriptions_model.php */

?>