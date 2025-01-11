<?php
class Home_model extends CI_Model{

	public function __construct(){
		$this->load->database('db_indiv');
		if(!isset($_SESSION['infoUnivers'])){
			$_SESSION['infoUnivers'] = 'BELE';
		}
	}

	public function verifyUser($idUser){

		$this->db->db_select('db_indiv');

		$this->db->select('Compte, NiveauAcces, Id');
		$this->db->where('Id', $idUser);

		$infoUser = $this->db->get('individus');

		$infoUser = $infoUser->row();

		$codeUnivers = $_SESSION['infoUnivers'];

		$ADMIN_BELEJR = array(129); //JEFFERSON
		if($codeUnivers == 'BELEJR' && in_array($infoUser->Id, $ADMIN_BELEJR)){
			define('IS_ADMIN_BELEJR', true);
		} else{
			define('IS_ADMIN_BELEJR', false);
		}

		$hier = time() - (60*60*24);

		$this->db->db_select('frameworks');
		$this->db->query("DELETE FROM `ci_sessions` WHERE timestamp < " .$hier );

		return $infoUser;
	}

	public function getPresenceCount($idActiv = null){

		$this->db->db_select('db_activ');

		if($idActiv === NULL){

			$this->db->select('Id, Nom');
			$this->db->where('Type','GN');
			$this->db->where('DateFin <', date('Y-m-d H:i:s', time()) );
			$this->db->order_by('DateFin', 'desc');

			$id = $this->db->get('activites', 1);

			$query = "SELECT Id, Nom FROM activites WHERE CodeUnivers = '".$_SESSION['infoUnivers'] ."' AND DateFin < '" . date('Y-m-d H:i:s', time()) . "' ORDER BY DateFin DESC LIMIT 1";
			$activ = $this->db->query($query);

			$activ = $id->row();

			$idActiv = $activ->Id;

			$result['activite'] = $activ;
		} else {
			$this->db->select('Id, Nom');
			$this->db->where('Id', $idActiv);

			$query = $this->db->get('activites');
			$result['activite'] = $query->row();
		}

		$this->db->db_select('db_indiv');
		
		$query = "SELECT   CONCAT(HOUR(DateInscription), ':00-', HOUR(DateInscription)+1, ':00') AS heures, COUNT(*) AS 'nombrePresences'
			FROM     db_indiv.presences
			WHERE    IdActivite = " . $idActiv ." GROUP BY HOUR(DateInscription)";

		$query = $this->db->query($query);

		$result['heures'] = $query->result();

		$result['total'] = 0;

		foreach ($result['heures'] as $heure) {
			$result['total'] = $result['total'] + $heure->nombrePresences;
		}

		/* Get all GN from Last year */

		$this->db->db_select('db_activ');

		$this->db->select('Id, Nom');
		$this->db->where('Type','GN');
		$this->db->where('DateFin <=', date('Y-m-d H:i:s', time()) );
		$this->db->where('DateDebut >=', date('Y-m-d', strtotime("last year")) );
		$this->db->where('CodeUnivers', $_SESSION['infoUnivers']);
		$this->db->order_by('DateFin', 'desc');

		$listGN = $this->db->get('activites');
		$result['listGN'] = $listGN->result();

		return $result;
	}

	public function getRacesCount($idActiv = null){
		$this->db->db_select('db_activ');

		if($idActiv == null){
			$this->db->where('Type','GN');
			$this->db->where('DateFin <', date('Y-m-d H:i:s', time()) );
			$this->db->where('CodeUnivers', $_SESSION['infoUnivers']);
			$this->db->order_by('DateFin', 'desc');

			$query = $this->db->get('activites');

			$id = $query->row();

			$idActiv = $id->Id;		
		}

		$where = "pres.IdActivite = " .$idActiv ." AND insc.IdPersonnage = perso.Id";

		$this->db->select("pilot.Nom as 'label', COUNT(perso.CodeRace) as 'value' ");
		$this->db->from('db_indiv.presences pres');
		$this->db->join('db_activ.inscriptions insc', 'insc.IdActivite = pres.IdActivite', 'left');
		$this->db->join('db_perso.personnages perso', 'perso.IdIndividu = pres.IdIndividu', 'left');
		$this->db->join('db_pilot.races pilot', 'pilot.Code = perso.CodeRace', 'left');
		$this->db->where($where);
		$this->db->group_by('perso.CodeRace');


		$query = $this->db->get();
		return $query->result();
	}

	public function getClassesCount($idActiv = null){
		$this->db->db_select('db_activ');

		if($idActiv == null){
			$this->db->where('Type','GN');
			$this->db->where('DateFin <', date('Y-m-d H:i:s', time()) );
			$this->db->order_by('DateFin', 'desc');

			$query = $this->db->get('activites', 1);

			$id = $query->result();

			$idActiv = $id[0]->Id;		
		}
		$where = "pres.IdActivite = " .$idActiv ." AND insc.IdPersonnage = perso.Id";

		$this->db->select("pilot.Nom as 'label', COUNT(perso.CodeClasse) as 'value' ");
		$this->db->from('db_indiv.presences pres');
		$this->db->join('db_activ.inscriptions insc', 'insc.IdActivite = pres.IdActivite', 'left');
		$this->db->join('db_perso.personnages perso', 'perso.IdIndividu = pres.IdIndividu', 'left');
		$this->db->join('db_pilot.classes pilot', 'pilot.Code = perso.CodeClasse', 'left');
		$this->db->where($where);
		$this->db->group_by('perso.CodeClasse');


		$query = $this->db->get();
		return $query->result();
	}

	public function getReligionCount($idActiv = null){
		$this->db->db_select('db_activ');

		if($idActiv == null){
			$this->db->select('Id');
			$this->db->where('Type','GN');
			$this->db->where('DateFin <', date('Y-m-d H:i:s', time()) );
			$this->db->order_by('DateFin', 'desc');

			$query = $this->db->get('activites', 1);

			$id = $query->result();

			$idActiv = $id[0]->Id;		
		}

		$where = "pres.IdActivite = " .$idActiv ." AND insc.IdPersonnage = perso.Id";

		$this->db->select("pilot.Nom as 'label', COUNT(perso.CodeReligion) as 'value' ");
		$this->db->from('db_indiv.presences pres');
		$this->db->join('db_activ.inscriptions insc', 'insc.IdActivite = pres.IdActivite', 'left');
		$this->db->join('db_perso.personnages perso', 'perso.IdIndividu = pres.IdIndividu', 'left');
		$this->db->join('db_pilot.religions pilot', 'pilot.Code = perso.CodeReligion', 'left');
		$this->db->where($where);
		$this->db->group_by('perso.CodeReligion');


		$query = $this->db->get();
		return $query->result();
	}

	public function getStatsActivites(){
		$this->db->db_select('db_activ');

		$query = "SELECT act.Nom, act.Type, act.DateDebut,
					(SELECT count(*) FROM db_activ.inscriptions ins WHERE ins.IdActivite = act.Id) AS Inscriptions,
					(SELECT count(*) FROM db_indiv.presences pres WHERE pres.IdActivite = act.Id) AS Presences
				 FROM db_activ.activites act
				 WHERE act.CodeUnivers = '" . $_SESSION['infoUnivers'] ."' 
				 ORDER BY act.DateDebut DESC;";

		$result = $this->db->query($query);

		return $result->result();

	}

	public function getStatsPasses(){
		$this->db->db_select('db_activ');

		$query = "SELECT pas.Nom,
					(SELECT count(*) FROM db_indiv.passes_acquises pac WHERE pac.IdPasse = pas.Id) AS Detenteurs
				 FROM db_activ.passes pas
				 ORDER BY pas.Id DESC;";

		$result = $this->db->query($query);

		return $result->result();
	}

	public function getAcces($idUser, $codeUnivers){
		$this->db->db_select('db_indiv');

		$query = "SELECT Acces
				FROM db_indiv.acces 
				WHERE IdIndividu = $idUser 
				AND CodeUnivers = '$codeUnivers'";

		$result = $this->db->query($query);

		return $result->result();
	}
}