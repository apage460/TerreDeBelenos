<?php
class Histoire_model extends CI_Model{

  public function __construct(){
    $this->load->database('db_monde');
    $this->db->db_select('db_monde');
  }

  public function getBaronnies($idComte = false, $idBaronnie = false){
    $this->db->db_select('db_monde');


    $this->db->select('b.*, c.Nom AS NomComte');
    $this->db->from('db_monde.baronnies b');
    $this->db->join('db_monde.comtes c', 'c.Id = b.IdComte' , 'left');
    $this->db->order_by('cast(b.Cadastre as unsigned)', 'asc');

    if($idComte){
      $this->db->where('b.IdComte', $idComte);
    }

    if($idBaronnie){
      $this->db->where('b.Id', $idBaronnie);
    }

    $this->db->order_by('b.IdComte ASC, b.Cadastre ASC');
    $query = $this->db->get();

    return $query->result();

  }

  public function getComtes($codeDuche = false, $order = false){
    $this->db->db_select('db_monde');


    $this->db->select('c.*, d.Nom AS NomDuche');
    $this->db->from('db_monde.comtes c');
    $this->db->join('db_monde.duches d', 'd.Code = c.CodeDuche' , 'left');

    if($codeDuche){
      $this->db->where('c.CodeDuche', $codeDuche);
    }

    $this->db->where('c.CodeEtat', 'ACTIF');

    if($order == false){
      $this->db->order_by('d.Nom ASC, c.Nom ASC');
    } else {
      $this->db->order_by('c.Nom ASC');
    }
    $query = $this->db->get();

    return $query->result();

  }

  public function getDuches(){
    $this->db->db_select('db_monde');

    $this->db->select('d.*, r.Nom AS NomRoyaume');
    $this->db->from('db_monde.duches d');
    $this->db->join('db_monde.royaumes r', 'r.Code = d.CodeRoyaume' , 'left');
    $this->db->where('d.CodeEtat','ACTIF');

    $this->db->where('d.CodeEtat', 'ACTIF');

    $this->db->order_by('r.Nom ASC, d.Nom ASC');
    $query = $this->db->get();

    return $query->result();
  }

  public function getRoyaumes(){
    $this->db->db_select('db_monde');

    $this->db->where('CodeEtat', 'ACTIF');
    $this->db->order_by('Code', 'ASC');
    $query = $this->db->get('royaumes');

    return $query->result();

  }

  public function editBaronnie($idBaronnie){
    $this->db->db_select('db_monde');

    $data = array(
      'IdComte' => $_POST['idComte'],
      'Nom' => $_POST['Nom'],
      'Baron' => $_POST['Baron'],
      'CodeEtat' => $_POST['CodeEtat'],
    );

    $this->db->where('Id', $idBaronnie);
    $this->db->update('baronnies', $data);

  }

  public function editComte($idComte){
    $this->db->db_select('db_monde');

    $data = array(
      'Nom' => $_POST['Nom'],
      'Couleur' => $_POST['Couleur'],
      'CodeDuche' => $_POST['CodeDuche'],
      'Dirigeant' => $_POST['Dirigeant'],
      'DescriptionDirigeant' => $_POST['DescriptionDirigeant'],
      'Scribe' => $_POST['Scribe'],
      'DescriptionScribe' => $_POST['DescriptionScribe'],
    );

    $this->db->where('Id', $idComte);
    $this->db->update('comtes', $data);
  }

  public function editDuche($codeDuche){
    $this->db->db_select('db_monde');

    $data = array(
      'Nom' => $_POST['Nom'],
      'CodeRoyaume' => $_POST['CodeRoyaume'],
      'Description' => $_POST['Description'],
      'Couleur' => $_POST['Couleur'],
      'Dirigeant' => $_POST['Dirigeant']
    );

    $this->db->where('Code', $codeDuche);
    $this->db->update('duches', $data);
  }

  public function editRoyaume($codeRoyaume){
    $this->db->db_select('db_monde');

    $data = array(
      'Nom' => $_POST['Nom'],
      'Description' => $_POST['Description'],
      'CodeEtat' => $_POST['CodeEtat'],
      'Couleur' => $_POST['Couleur'],
      'Dirigeant' => $_POST['Dirigeant']
    );

    $this->db->where('Code', $codeRoyaume);
    $this->db->update('royaumes', $data);
  }

  public function getTrames(){
    $this->db->db_select('db_monde');
    
    $this->db->select("tra.*, com.Nom as Comte, CONCAT(ind.Prenom, ' ' , ind.Nom) as Createur");
    $this->db->from("db_monde.trames tra");
    $this->db->join('db_monde.comtes com', 'com.Id = tra.IdComte' , 'left');
    $this->db->join('db_indiv.individus ind', 'ind.Id = tra.IdCreateur' , 'left');
    $this->db->order_by('tra.Id');

    $query = $this->db->get();

    return $query->result();
  }

  public function addTrame(){
    $this->db->db_select('db_monde');

    $data = array(
      'Id' => $_POST['Id'],
      'Nom' => $_POST['Nom'],
      'IdComte' => $_POST['IdComte'],
      'Description' => $_POST['Description'],
      'CodeEtat' => $_POST['CodeEtat'],
      'IdCreateur' => $_SESSION['infoUser']->Id,
      'DateCreation' => date('Y-m-d H:i:s', time()),
    );
    
    $this->db->insert('trames', $data);
  }

  public function editTrame($idTrame){
    $this->db->db_select('db_monde');

    $data = array(
      'Id' => $_POST['Id'],
      'IdComte' => $_POST['IdComte'],
      'Nom' => $_POST['Nom'],
      'CodeEtat' => $_POST['CodeEtat'],
      'Description' => $_POST['Description'],
    );

    $this->db->where('Id', $idTrame);
    $this->db->update('trames', $data);
  }

  public function getTrame($idTrame){
    $this->db->db_select('db_monde');

    $this->db->select("tr.*, CONCAT(ind.Prenom, ' ' , ind.Nom) as Createur");
    $this->db->from('trames tr');
    $this->db->join('db_indiv.individus ind', 'ind.Id = tr.IdCreateur');
    $this->db->where('tr.Id', $idTrame);
    $query = $this->db->get('trames');

    return $query->row();
  }

  public function getChapitres($idTrame){
    $this->db->db_select('db_monde');

    $this->db->where('IdTrame', $idTrame);
    $query = $this->db->get('chapitres');

    return $query->result();
  }

  public function addChapitre(){
    $this->db->db_select('db_monde');

    $data = array(
      'IdTrame' => $_POST['IdTrame'],
      'Numero' => $_POST['Numero'],
      'Texte' => $_POST['Texte'],
      'CodeEtat' => $_POST['CodeEtat'],
      'DateCreation' => date('Y-m-d H:i:s', time()),
    );

    $this->db->insert('chapitres', $data);
  }

  public function editChapitre($idChapitre){
    $this->db->db_select('db_monde');

    $data = array(
      'Numero' => $_POST['Numero'],
      'Texte' => $_POST['Texte'],
      'CodeEtat' => $_POST['CodeEtat'],
    );

    $this->db->where('Id', $idChapitre);
    $this->db->update('chapitres', $data);
  }

  public function ajax_getTrames($idComte, $type){
    $this->db->db_select('db_monde');


    if($type == 'baronnies' || $type == 'comtes'){
      $this->db->where('IdComte',$idComte);
      $this->db->where('CodeEtat','ACTIF');
      $this->db->order_by('DateCreation','ASC');

      $query = $this->db->get('trames');

      return $query->result();
    } else {
      return null;
    }
  }

  public function ajax_getComte($id, $type, $idBaronnie){
    $this->db->db_select('db_monde');

    if($type == 'baronnies'){
      $this->db->where('Cadastre',$idBaronnie);
      $query = $this->db->get('baronnies');
    }elseif($type == 'comtes'){
      $this->db->where('Id',$id);
      $query = $this->db->get('comtes');
    } elseif( $type == 'duches') {
      $this->db->where('Code',$id);
      $query = $this->db->get('duches');
    } elseif($type == "royaumes"){
      $this->db->where('Code',$id);
      $query = $this->db->get('royaumes');
    }

    return $query->row();
  }

  public function getMissions($idMission = null){
    $this->db->db_select('db_perso');

    $this->db->select('mis.*, act.Nom as "nomActivite"');

    $this->db->join('db_activ.activites act', 'act.Id = mis.IdActivite', 'left');

    if(!is_null($idMission)){
      $this->db->where('mis.Id', $idMission);
    }
    $this->db->order_by('act.DateDebut', 'DESC');

    $query = $this->db->get('missions mis');

    return $query->result();    
  }

  public function addMission(){
    $data = array(
      'IdActivite' => $this->input->post('idActivite'),
      'Code' => $this->input->post('Code'),
      'Nom' => $this->input->post('Nom'),
      'ProposePar' => $this->input->post('ProposePar'),
      'MiseEnSituation' => ($this->input->post('MiseEnSituation') == '')?null:$this->input->post('MiseEnSituation'),
      'CodeDuche' => $this->input->post('CodeRoyaume'),
      'Objectif' => ($this->input->post('Objectif') == '')?null:$this->input->post('Objectif'),
      'ActionDemandee' => $this->input->post('actionDemandee'),
      'Chads' => $this->input->post('Chads'),
      'Faveurs' => ($this->input->post('Faveurs') == '')?null:$this->input->post('Faveurs'),
      'AutresRessources' => ($this->input->post('AutreRessources') == '')?null:$this->input->post('AutreRessources'),
      'IndSucces' => $this->input->post('IndSucces'),
      'IndSabotage' => $this->input->post('IndSabotage'),
      'IndExpedition' => $this->input->post('IndExpedition'),
      'FichierResultat' => ($_FILES['FichierResultat']['name'] == '')?null:$_FILES['FichierResultat']['name']
    );

    $this->db->insert('db_perso.missions', $data);
  }

  public function getMission($idMission){
    $this->db->db_select('db_perso');

    $this->db->where('Id', $idMission);

    $query = $this->db->get('missions');

    return $query->row();
  }

  public function editMission($idMission, $fileName = null){
    $invalid = array('/','\\','?','.',':','#','*','|','"', '<', '>');
    $replace = '_';

    if(!is_null($fileName)){
      $curratedFileName = str_replace($invalid, $replace, $fileName) . ".pdf";
    }
    $data = array(
      'IdActivite' => $this->input->post('idActivite'),
      'Code' => $this->input->post('Code'),
      'Nom' => $this->input->post('Nom'),
      'ProposePar' => $this->input->post('ProposePar'),
      'MiseEnSituation' => ($this->input->post('MiseEnSituation') == '')?null:$this->input->post('MiseEnSituation'),
      'CodeDuche' => $this->input->post('CodeRoyaume'),
      'Objectif' => ($this->input->post('Objectif') == '')?null:$this->input->post('Objectif'),
      'ActionDemandee' => ($this->input->post('actionDemandee') == '')?null:$this->input->post('actionDemandee'),
      'Chads' => $this->input->post('Chads'),
      'Faveurs' => ($this->input->post('Faveurs') == '')?null:$this->input->post('Faveurs'),
      'AutresRessources' => ($this->input->post('AutreRessources') == '')?null:$this->input->post('AutreRessources'),
      'IndSucces' => $this->input->post('IndSucces'),
      'IndSabotage' => $this->input->post('IndSabotage'),
      'IndExpedition' => $this->input->post('IndExpedition'),
      'FichierResultat' => (is_null($fileName))?null:$fileName . ".pdf"
    );

    $this->db->where('Id', $idMission);

    $this->db->update('db_perso.missions', $data);
  }

  public function getQRLectures(){
    $this->db->db_select('db_activ');

    $strQuery = "SELECT qr.*, pers.Id, pers.Prenom, pers.Nom, ind.Prenom as 'indPrenom', ind.Nom as 'indNom'
                FROM db_activ.lecture_qr qr
                LEFT JOIN db_perso.personnages pers ON pers.Id = qr.IdPersonnage
                LEFT JOIN db_indiv.individus ind ON ind.Id = pers.IdIndividu
                ORDER BY pers.Id ASC";

    $query = $this->db->query($strQuery);

    return $query->result();
  }

  public function getPersoMetiers($lectures){
    $this->db->db_select('db_pilot');

    if(!empty($lectures)){
      $return = array();
      foreach($lectures as $lecture){
        $strQuery = "SELECT Nom 
                    FROM db_pilot.metiers
                    WHERE Nom IN (SELECT `Precision` FROM db_perso.competences_acquises WHERE IdPersonnage = $lecture->IdPersonnage AND CodeCompetence LIKE 'METIER%' )";

        $query = $this->db->query($strQuery);

        $return[$lecture->IdPersonnage] = $query->result();
      }

      return $return;
    }else{
      return false;
    }
  }

  public function clearQRLectures(){
    $this->db->db_select('db_activ');

    $this->db->empty_table('lecture_qr');

  }

  public function populateMission($data){
    $this->db->db_select('db_perso');

    #echo '<pre>'; var_dump($data); die;

    $this->db->insert_batch('participants_mission', $data);

    return true;
  }

  public function updateChefMission($idMission, $idChef){
    $this->db->db_select('db_perso');

    $data = array(
      'IdChef' => $idChef
    );

    $this->db->where('Id', $idMission);
    $this->db->update('missions', $data);

    return true;
  }

  public function duplicateMission($idMission){
    $this->db->db_select('db_perso');

    $strQuery = "INSERT INTO db_perso.missions( IdActivite, Code, Nom, ProposePar, MiseEnSituation, IdChef, IdGroupe, Cible, CodeRoyaume, Objectif, Chads, Faveurs, ListeMecenes, AutreRessources, IndSucces, IndSabotage ) 
      SELECT  IdActivite, Code, Nom, ProposePar, MiseEnSituation, IdChef, IdGroupe, Cible, CodeRoyaume, Objectif, Chads, Faveurs, ListeMecenes, AutreRessources, IndSucces, IndSabotage 
      FROM db_perso.missions 
      WHERE Id = $idMission;";

    $query = $this->db->query($strQuery);

    return true;
  }

  public function getMissionParticipant($idMission){
    $this->db->db_select('db_perso');

    $this->db->select('participants_mission.*, personnages.Prenom, personnages.Nom');
    $this->db->from('participants_mission');
    $this->db->join('personnages', 'personnages.Id = participants_mission.IdPersonnage', 'left');
    $this->db->where('IdMission', $idMission);

    $query = $this->db->get();

    return $query->result();
  }

  public function removeParticipant($idParticipation){
    $this->db->db_select('db_perso');

    $this->db->where('Id', $idParticipation);
    $this->db->delete('participants_mission');
  }
  
}