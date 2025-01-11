<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Groupes_model extends CI_Model {

	public function __construct()	{
		parent::__construct();
		$this->db->db_select('db_group');
	}

	public function getGroupes(){
		$query = "SELECT gr.*, SUM(inf.Points) as 'TotalPoints', camp.CodeCamp
				FROM db_group.groupes gr
				LEFT JOIN db_group.influence inf ON inf.IdGroupe = gr.Id
				LEFT JOIN db_group.camps_occupes camp ON camp.IdGroupe = gr.Id AND camp.CodeEtat = 'ACTIF'
				WHERE (gr.CodeEtat = 'ACTIF') OR (gr.CodeEtat = 'NOUV')
				GROUP BY gr.Id
				ORDER BY Nom;";

		$results = $this->db->query($query);
		return $results->result();
	}

	public function singleGroupe($idGroupe){
		$this->db->where('Id', $idGroupe);
		$query = $this->db->get('groupes');
		return $query->row();
	}

	public function getMembres($idGroupe){
		$this->db->select('CONCAT(perso.Prenom, " ", perso.Nom) as NomPerso, CONCAT(indiv.Prenom, " ", indiv.Nom) as NomIndiv, perso.CodeEtat, indiv.Id as IdIndividu, perso.Id as IdPersonnage, resp.IdResponsable');
		$this->db->from('db_group.membres memb');
		$this->db->join('db_perso.personnages perso', 'perso.Id = memb.IdPersonnage', 'right');
		$this->db->join('db_indiv.individus indiv', 'indiv.Id = perso.IdIndividu', 'left');
		$this->db->join('db_group.responsables_groupe resp', 'resp.IdResponsable = indiv.Id', 'left');
		$this->db->where('memb.IdGroupe', $idGroupe);

		$query = $this->db->get();
		return $query->result();

	}

	public function getResponsables($idGroupe){
		$this->db->select('resp.*,CONCAT(indiv.Prenom, " ", indiv.Nom) as NomIndiv, indiv.Courriel, indiv.Compte');
		$this->db->from('db_group.responsables_groupe resp');
		$this->db->join('db_indiv.individus indiv', 'indiv.Id = resp.IdResponsable', 'left');
		$this->db->where('IdGroupe', $idGroupe);
		$this->db->order_by('indiv.Nom', 'asc');

		$query = $this->db->get();
		return $query->result();
	}

	public function getObjectifs($idGroupe){
		$this->db->where('IdGroupe', $idGroupe);

		$query = $this->db->get('objectifs_groupe');
		return $query->result();
	}

	public function getProfils($idGroupe){
		$this->db->select('grppro.*, pilprof.Nom');
		$this->db->from('db_group.profils grppro');
		$this->db->join('db_pilot.profils_groupe pilprof', 'pilprof.Code = grppro.CodeProfil', 'left');
		$this->db->where('grppro.IdGroupe', $idGroupe);
		$query = $this->db->get();

		return $query->result();
	}

	public function getSpecs($idGroupe){
		$this->db->select('av.*');
		$this->db->from('db_group.avantages av');
		$this->db->join('db_pilot.avantages_groupe avgr', 'avgr.Code = av.Avantage', 'left');
		$this->db->where('av.IdGroupe', $idGroupe);
		$query = $this->db->get();

		return $query->result();
	}

	public function getAllowedAvantages($idGroupe){
		$this->db->db_select('db_group');
		$this->db->select('CodeProfil');
		$this->db->where('IdGroupe', $idGroupe);
		$query = $this->db->get('profils');
		$results = $query->result();

		$avantages = [];

		foreach ($results as $result) {
			$this->db->db_select('db_pilot');
			$this->db->where('CodeProfil', $result->CodeProfil);
			$query = $this->db->get('avantages_groupe');

			array_push($avantages, $query->result() );
		}

		return $avantages;
	}

	public function officialGroupe($idGroupe){
		$data = array(
			'CodeEtat' => 'OFFIC'
		);

		$this->db->where('Id', $idGroupe);
		$this->db->update('groupes', $data);
	}

	public function removeFromGroupe($idPerso, $idGroupe){
		$this->db->where('IdPersonnage', $idPerso);
		$this->db->where('IdGroupe', $idGroupe);

		$this->db->delete('membres');
	}

	public  function demoteResponsable($idIndiv, $idGroupe){
		$this->db->where('IdGroupe', $idGroupe);
		$this->db->where('IdResponsable', $idIndiv);

		$this->db->delete('responsables_groupe');
	}

	public function promoteResponsable($idIndiv, $idGroupe){
		$data = array(
			'IdGroupe' => $idGroupe,
			'IdResponsable' => $idIndiv
		);

		$this->db->insert('responsables_groupe', $data);
	}

	public function getInfluence($idGroupe){
		$this->db->select('*, (SELECT SUM(PI) FROM db_group.points_influence WHERE IdGroupe = ' .$idGroupe .') as TotalPI');
		$this->db->where('IdGroupe', $idGroupe);
		$this->db->order_by('DateInscription', 'desc');

		$query = $this->db->get('points_influence');

		return $query->result();
	}

	public function getLastActivity(){
		$this->db->db_select('db_activ');

		$this->db->select('Nom');
		$this->db->where('DateFin <', date('Y-m-d H:i:s', time() ) );
		$this->db->where('Type', 'GN');
		$this->db->order_by('DateFin', 'desc');

		$query = $this->db->get('activites', 1);

		return $query->row();
	}

	public function addInfluence($idGroupe){
		$data = array(
			'IdGroupe' => $idGroupe,
			'Raison' => $this->input->post('raison'),
			'PI' => $this->input->post('pi'),
			'DateInscription' => date('Y-m-d H:i:s', time()),
			'Commentaires' => null
		);

		$this->db->insert('points_influence', $data);
	}

	public function deleteInfluence($idInfluence){
		$this->db->where('Id', $idInfluence);

		$this->db->delete('points_influence');
	}

	public	function viewActions(){

		$this->db->select("DISTINCT(act.IdGroupe), group.Nom as 'Nom'");
		$this->db->from('db_group.actions_faites act');
		$this->db->join('db_group.groupes group', 'group.Id = act.IdGroupe', 'left');
		$this->db->where('act.CodeEtat', 'DEM');
		$this->db->group_by('act.IdGroupe');
		$this->db->order_by('Nom', 'ASC');
		$query = $this->db->get();
		$vGroupes = $query->result_array();

		$return = array();
		foreach ($vGroupes as $key => $groupe) {
			$this->db->select("act.*, pil.Nom, pil.Description, pil.Cout, pil.CodeProfil, pil.AchatsMax");
			$this->db->from('db_group.actions_faites act');
			$this->db->join('db_pilot.actions_geopolitiques pil', 'pil.Code = act.CodeAction', 'left');
			$this->db->join('db_group.groupes group', 'group.Id = act.IdGroupe', 'left');
			$this->db->where('group.Id', $groupe['IdGroupe']);
			$this->db->where('act.CodeEtat', 'DEM');
			$query = $this->db->get();

			$return[$key]['Nom'] = $groupe['Nom'];
			$return[$key]['Id'] = $groupe['IdGroupe'];
			$return[$key]['actions'] = $query->result();

			$this->db->select("InfluenceMaximum");
			$this->db->where('Id', $groupe['IdGroupe']);
			$query = $this->db->get('db_group.groupes');
			$result = $query->row();

			$return[$key]['nbPI'] = $result->Influence;
		}

		return $return;
	}

	public function acceptAction($idGroupe,$idAction){

		$this->db->select("actf.*, acti.CodeProfil, acti.Nom as 'NomAction'");
		$this->db->from('db_group.actions_faites actf');
		$this->db->join('db_pilot.actions_geopolitiques acti', 'acti.Code = actf.CodeAction', 'left');
		$this->db->where('actf.Id', $idAction);
		$query = $this->db->get();
		$action = $query->row();

		if($action->CodeAction == 'SPECIAL') {
			$coutPI = $this->input->post('coutPI');
		} else {
			$coutPI = $action->CoutInfluence;
		}

		/* UPDATE db_group.actions_faites */
		$data = array(
			'CodeEtat' => 'ACCEP',
			'DateApprobation' => date('Y-m-d H:i:s', time() ),
		);

		$this->db->where('Id', $idAction);
		$this->db->update('actions_faites', $data);

		if($action->CodeAction != 'SPECIAL') {
			/*UPDATE db_group.prerequis*/
			switch ($action->CodeProfil) {
				case 'M':
					$nom = 'Spécialisation Militaire';
					break;
				case 'I':
					$nom = 'Spécialisation Interlope';
					break;
				case 'R':
					$nom = 'Spécialisation Religieuse';
					break;
				case 'C':
					$nom = 'Spécialisation Commerciale';
					break;
				case 'A':
					$nom = 'Spécialisation Académique';
					break;
			}

			$this->db->db_select('db_pilot');
			$this->db->select('IndAchatsCumulatifs');
			$this->db->where('Code', $action->CodeAction);
			$query = $this->db->get('actions_geopolitiques');
			$result = $query->row();


			/*$this->db->db_select('db_group');
			if($result->IndAchatsCumulatifs == 1){
				$valeur = $action->Achats;
			} else {
				$valeur = 1;
			}
			$sql = "UPDATE db_group.prerequis 
					SET Valeur = Valeur + '$valeur' 
					WHERE IdGroupe = '$idGroupe' AND Nom = '$nom' ;";


			$this->db->query($sql);*/

		}
		
	}

	public function refusAction($idGroupe,$idAction){

		$this->db->where('Id', $idAction);
		$query = $this->db->get('actions_faites');

		$action = $query->row();

		$data = array(
			'IdGroupe' => $idGroupe,
			'Raison' => 'Refus - ' .$action->CodeAction,
			'Points' => $action->CoutInfluence,
			'DateInscription' => date('Y-m-d H:i:s', time() ),
		);

		$this->db->insert('influence', $data);

		$data = array(
			'CodeEtat' => 'REFUS',
			'RaisonRefus' => $this->input->post('raison'),
		);

		$this->db->where('Id', $idAction);
		$this->db->update('actions_faites', $data);
	}

	public function removeProfil($idGroupe, $codeProfil){
		$this->db->where('IdGroupe', $idGroupe);
		$this->db->where('CodeProfil', $codeProfil);
		$this->db->delete('profils');

		switch ($codeProfil) {
			case 'M':
				$nom = 'Spécialisation Militaire';
				break;
			case 'I':
				$nom = 'Spécialisation Interlope';
				break;
			case 'R':
				$nom = 'Spécialisation Religieuse';
				break;
			case 'C':
				$nom = 'Spécialisation Commerciale';
				break;
			case 'A':
				$nom = 'Spécialisation Académique';
				break;
		}

		$this->db->where('IdGroupe', $idGroupe);
		$this->db->where('Nom', $nom);
		$this->db->delete('prerequis');

		$sql = "DELETE FROM db_group.avantages WHERE Avantage IN (SELECT Code FROM db_pilot.avantages_groupe WHERE CodeProfil = '$codeProfil') AND IdGroupe = '$idGroupe';";

		$this->db->query($sql);


	}

	public function addProfil($idGroupe){
		$data = array(
			'IdGroupe' => $idGroupe,
			'CodeProfil' => $this->input->post('newProfil')
		);

		$this->db->insert('profils', $data);

		switch ($this->input->post('newProfil')) {
			case 'M':
				$nom = 'Spécialisation Militaire';
				break;
			case 'I':
				$nom = 'Spécialisation Interlope';
				break;
			case 'R':
				$nom = 'Spécialisation Religieuse';
				break;
			case 'C':
				$nom = 'Spécialisation Commerciale';
				break;
			case 'A':
				$nom = 'Spécialisation Académique';
				break;
		}

		$data = array(
			'IdGroupe' => $idGroupe,
			'Nom' => $nom,
			'Valeur' => 0,
			'DateCreation' => date('Y-m-d H:i:s', time() ),
			'Commentaires' => null
		);

		$this->db->insert('prerequis', $data);
		
	}

	public function addSpec($idGroupe){
		$this->db->db_select('db_pilot');

		$this->db->db_select('db_group');
		$data = array(
			'IdGroupe' => $idGroupe,
			'CodeAvantage' => $this->input->post('newSpec'),
		 	'CodeEtat' => 'ACTIF',
		 	'DateCreation' => date('Y-m-d H:i:s', time() ),
		 	'Commentaires' => null
		);
		$this->db->insert('specialisations', $data);
	}

	public function getpilotProfils(){
		$this->db->db_select('db_pilot');
		$query = $this->db->get('profils_groupe');

		return $query->result();
	}

	public function getEtats(){

		$this->db->db_select('db_group');

		$this->db->select('DISTINCT(grp.CodeEtat), pil.LibelleM');
		$this->db->from('db_group.groupes grp');
		$this->db->join('db_pilot.codes_etat pil','pil.Code = grp.CodeEtat');

		$query = $this->db->get();

		return $query->result();
	}

	public function updateGroupes($updateData){
		$this->db->db_select('db_group');

		$this->db->update_batch('groupes', $updateData, 'Id');
	}

	public function insertInfluence($insertData){
		$this->db->db_select('db_group');

		$this->db->insert_batch('influence', $insertData);
	}

	public function getInstitutions(){
		$this->db->db_select('db_group');

		$sql = "SELECT insti.*, comte.Nom as 'nomComte', grou.Nom as 'nomGroupe', prof.Nom as 'nomProfil'
				FROM institutions insti
				LEFT JOIN db_monde.comtes comte ON comte.Id = insti.IdComte
				LEFT JOIN db_group.groupes grou ON grou.Id = insti.IdGroupe
				LEFT JOIN db_pilot.profils_organisation prof ON prof.Code = insti.CodeProfil
				WHERE insti.CodeEtat = 'ACTIF'
				
				ORDER BY insti.Id, grou.Nom";

		$query = $this->db->query($sql);

		return $query->result();
	}

	public function getAllProfils(){
		$this->db->db_select('db_pilot');

		$query = $this->db->get('profils_organisation');

		return $query->result();
	}

	public function getInstitution($idInstitution){
		$this->db->db_select('db_group');

		$this->db->where('Id', $idInstitution);

		$query = $this->db->get('institutions');

		return $query->row();
	}

	public function saveInstitution($idInstitution){
		$this->db->db_select('db_group');

		$data = array(
			'Nom' => $this->input->post('Nom'),
			'IdComte' => $this->input->post('Comte'),
			'IdGroupe' => $this->input->post('Groupe'),
			'CodeProfil' => $this->input->post('Profil'),
			'Niveau' => $this->input->post('Niveau'),
			'Chef' => $this->input->post('Chef'),
			'Description' => $this->input->post('Description'),
			'AgendaCache' => $this->input->post('AgendaCache'),
			'CodeEtat' => $this->input->post('CodeEtat'),
		);

		$this->db->where('Id', $idInstitution);

		$this->db->update('institutions', $data);
	}

	public function getHistoriqueGroupe($idGroupe){
		$query = "(SELECT actf.Id, actf.DateCreation, actf.DateApprobation, act.Nom, grp.Nom, act_geo.Description, actf.CoutInfluence, null as InfId, null as InfRaison, null as InfPoints, null as InfDateInscription
				FROM db_group.actions_faites actf
				LEFT JOIN db_activ.activites act ON act.Id = actf.IdActivite
				LEFT JOIN db_group.groupes grp ON grp.Id = actf.IdGroupe
				LEFT JOIN db_pilot.actions_geopolitiques act_geo ON act_geo.Code = actf.CodeAction
				WHERE actf.IdGroupe = $idGroupe
				ORDER BY actf.DateCreation DESC)
				UNION ALL
				(SELECT null, null, null, null, null, null, null, inf.Id, inf.Raison, inf.Points, inf.DateInscription 
				FROM db_group.influence inf
				WHERE inf.IdGroupe = $idGroupe)
				ORDER BY DateCreation DESC, InfDateInscription DESC;";

		$return = $this->db->query($query);

		return $return->result();
	}

	public function getTitlesByGroup(){
		$strQuery = "SELECT tit.Titre, mem.IdGroupe, CONCAT(perso.Prenom, ' ', perso.Nom) as 'persoFull'
					FROM db_perso.titres tit
					LEFT JOIN db_group.membres mem ON mem.IdPersonnage = tit.IdPersonnage
					LEFT JOIN db_group.groupes grp ON grp.Id = mem.IdGroupe AND grp.CodeEtat = 'ACTIF'
					LEFT JOIN db_perso.personnages perso ON perso.Id = tit.IdPersonnage 
					WHERE perso.CodeEtat != 'MORT' AND perso.CodeEtat != 'RETIR'
					ORDER BY mem.idGroupe";

		$query = $this->db->query($strQuery);

		return $query->result();
	}

	public function deleteInfluence2($type, $id){
		if($type == 'achat'){
			$this->db->where('Id', $id);
			$this->db->delete('db_group.actions_faites');
		}elseif($type == 'gain'){
			$this->db->where('Id', $id);
			$this->db->delete('db_group.influence');
		}
	}

	public function getGroupesGivingInfluence(){
		$this->db->db_select('db_group');
		//GET LAST GN ID
		$today = date('Y-m-d', time());
		$strQuery = "SELECT Id FROM db_activ.activites WHERE DateFin < '$today' AND Type = 'GN' ORDER BY DateFin DESC LIMIT 1";

		$lastGN = $this->db->query($strQuery)->row()->Id;

		$strQuery = "SELECT ins.IdGroupe, grp.Nom
			FROM db_activ.inscriptions_groupe ins 
			LEFT JOIN db_group.groupes grp ON ins.IdGroupe = grp.Id
			WHERE ins.IndOfficiel = 1
			AND ins.IdActivite = $lastGN
			ORDER BY grp.Nom ASC";

		$query =  $this->db->query($strQuery);

		return $query->result();
	}
}

/* End of file groupes_model.php */
/* Location: ./application/models/groupes_model.php */ ?>