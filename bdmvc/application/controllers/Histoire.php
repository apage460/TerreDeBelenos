<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Histoire extends CI_Controller {

  /**
   * Index Page for this controller.
   *
   * Maps to the following URL
   *    http://example.com/index.php/welcome
   *  - or -
   *    http://example.com/index.php/welcome/index
   *  - or -
   * Since this controller is set as the default controller in
   * config/routes.php, it's displayed at http://example.com/
   *
   * So any other public methods not prefixed with an underscore will
   * map to /index.php/welcome/<method_name>
   * @see https://codeigniter.com/user_guide/general/urls.html
   */


  public function index() {
    $data = array();
  }

  public function carte($json = false, $active = null){
    $this->load->model('histoire_model');

    $data = array(
      'baronnies' => $this->histoire_model->getBaronnies(),
      'comtes' => $this->histoire_model->getComtes(),
      'duches' => $this->histoire_model->getDuches(),
      'royaumes' => $this->histoire_model->getRoyaumes(),
      'jsonSuccess' => $json,
      'active' => $active,
    );

    $this->load->view('template/header', $data);
    $this->load->view('histoire/carte', $data);
    $this->load->view('template/footer',$data);
  }

  public function generateMapConfig(){
    $this->load->model('histoire_model');

    $data = array(); 

    if( $_SESSION['infoUser']->NiveauAcces < 6 && $_SESSION['infoUser']->Id != 129){
      return;
    }

    $royaumes = $this->histoire_model->getRoyaumes();
    foreach ($royaumes as $r) {
      $data['royaumes'][$r->Code] = array(
        'Code' => $r->Code,
        'Nom' => $r->Nom,
        'Couleur' => $r->Couleur,
        'CodeEtat' => $r->CodeEtat
      );
    }

    $duches = $this->histoire_model->getDuches();
    foreach ($duches as $d) {
      $data['duches'][$d->Code] = array(
        'Code' => $d->Code,
        'Nom' => $d->Nom,
        'CodeRoyaume' => $d->CodeRoyaume,
        'Couleur' => $d->Couleur,
        'CodeEtat' => $d->CodeEtat
      );
    }

    $comtes = $this->histoire_model->getComtes();
    foreach ($comtes as $c) {
      $data['comtes'][$c->Id] = array(
        'Id' => $c->Id,
        'Nom' => $c->Nom,
        'Couleur' => $c->Couleur,
        'CodeDuche' => $c->CodeDuche,
        'CodeEtat' => $c->CodeEtat,
      );
    }

    $baronnies = $this->histoire_model->getBaronnies();
    foreach ($baronnies as $b) {
      foreach ($comtes as $c) {
        if($c->Id == $b->IdComte){
          $comte = $c;

          foreach($duches as $d){
            if($d->Code == $c->CodeDuche){
              $duche = $d;

              foreach($royaumes as $r){
                if($r->Code == $d->CodeRoyaume){
                  $royaume = $r;
                }
              }
            }
          }
        } 
      }

      $data['baronnies'][$b->Cadastre] = array(
        'Id' => $b->Id,
        'Cadastre' => $b->Cadastre,
        'Nom' => $b->Nom,
        'IdComte' => $b->IdComte,
        'CodeDuche' => $comte->CodeDuche,
        'CodeRoyaume' => $duche->CodeRoyaume,
        'CodeEtat' => $b->CodeEtat,
        'Couleur' => $comte->Couleur,
        'CouleurDuche' => $duche->Couleur,
        'CouleurRoyaume' => $royaume->Couleur,
      );
    }

    $file = '/home/terresdebelenos/terres-de-belenos.com/bdmvc/assets/map/config.json';
    //$file = '/webdev/bele/bdmvc/assets/map/config.json';

    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    file_put_contents($file, $json);    

    redirect('Histoire/carte/true', 'refresh');
  }

  public function editBaronnie($idBaronnie){
    $this->load->model('histoire_model');

    $this->histoire_model->editBaronnie($idBaronnie);

    redirect('Histoire/carte/false/baronnies', 'refresh');
  }

  public function editComte($idComte){
    $this->load->model('histoire_model');

    $this->histoire_model->editComte($idComte);

    redirect('Histoire/carte/false/comtes', 'refresh');
  }

  public function editDuche($codeDuche){
    $this->load->model('histoire_model');

    $this->histoire_model->editDuche($codeDuche);

    redirect('Histoire/carte/false/duches', 'refresh');
  }

  public function editRoyaume($codeRoyaume){
    $this->load->model('histoire_model');

    $this->histoire_model->editRoyaume($codeRoyaume);

    redirect('Histoire/carte/false/royaumes', 'refresh');
  }

  public function trames($json = false){
    $this->load->model('histoire_model');

    $data = array(
      'comtes' => $this->histoire_model->getComtes(),
      'trames' => $this->histoire_model->getTrames(),

    );

    $this->load->view('template/header', $data);
    $this->load->view('histoire/trames', $data);
    $this->load->view('template/footer',$data);
  }

  public function addTrame(){
    $this->load->model('histoire_model');

    $this->load->library('form_validation');

    $this->form_validation->set_rules('Id', 'Identification', 'required|numeric');
    $this->form_validation->set_rules('Nom', 'Nom', 'required|is_unique[trames.Nom]|alpha_numeric_spaces');
    $this->form_validation->set_rules('IdComte', 'Comté', 'required|min_length[1]');
    $this->form_validation->set_rules('Description', 'Description', 'required|alpha_numeric_spaces');

    if ($this->form_validation->run() == FALSE){

      $this->load->model('histoire_model');

      $this->histoire_model->addTrame();

      $data = array(
        'comtes' => $this->histoire_model->getComtes(),
        'trames' => $this->histoire_model->getTrames(),
      );

      $this->load->view('template/header', $data);
      $this->load->view('histoire/trames', $data);
      $this->load->view('template/footer',$data);
      
    }
    else{
      $this->histoire_model->addTrame();
    }
    

    redirect('Histoire/trames/', 'refresh');
  }

  public function editTrame($idTrame){
    $this->load->model('histoire_model');

    $this->histoire_model->editTrame($idTrame);

    redirect('Histoire/trames/', 'refresh');
  }

  public function getChapitres($idTrame){
    $this->load->model('histoire_model');    

    $data = array(
      'trame' => $this->histoire_model->getTrame($idTrame),
      'chapitres' => $this->histoire_model->getChapitres($idTrame),
    );

    $this->load->view('template/header', $data);
    $this->load->view('histoire/chapitres', $data);
    $this->load->view('template/footer',$data);
  }

  public function addChapitre(){
    $this->load->model('histoire_model');

    $this->histoire_model->addChapitre();

    redirect('Histoire/getChapitres/' . $_POST['IdTrame'], 'refresh');
  }

  public function editChapitre($idChapitre){
    $this->load->model('histoire_model');

    $this->histoire_model->editChapitre($idChapitre);

    redirect('Histoire/getChapitres/' . $_POST['IdTrame'], 'refresh');
  }

  public function ajax_getTrames($idComte, $type, $idBaronnie = null){
    $this->load->model('histoire_model');

    $return = array(
      'trames' => $this->histoire_model->ajax_getTrames($idComte, $type),
      'territoire' => $this->histoire_model->ajax_getComte($idComte, $type, $idBaronnie),
    );
    echo json_encode($return);
  }

  public function pdfMissive($idMissive){

    require_once(APPPATH . 'third_party/fpdf182/fpdf.php');
    $this->load->model('personnages_model');
    $missive = $this->personnages_model->getMissive($idMissive);

    setlocale(LC_TIME, 'fr_FR.utf8','fra');

    $pdf = new \FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(0,10, strftime('%e %B ', strtotime($missive->DateEnvoi)) . '770',0,1,'R');
    $pdf->MultiCell(0,10, utf8_decode($missive->Corps), 0, '');
    
    
    $pdf->Output();

  }

  public function missions(){
    $this->load->model('activites_model');
    $today = date('Y-m-d H:i:s', time());
    $data['activites'] = $this->activites_model->getPreviousAndNextActivites($today);

    $this->load->model('histoire_model');
    $data['royaumes'] = $this->histoire_model->getDuches();
    $data['missions'] = $this->histoire_model->getMissions();

    $this->load->view('template/header', $data);
    $this->load->view('histoire/missions', $data);
    $this->load->view('template/footer',$data);
  }

  public function addMission(){
    $today = date('Y-m-d H:i:s', time());
    $this->load->library('form_validation');

    $this->form_validation->set_rules('Code', 'Code de mission', 'trim|required',
        array('required' => "Le champ 'Code de mission' est requis.")
    );
    $this->form_validation->set_rules('Nom', 'Nom de mission', 'trim|required',
        array('required' => "Le champ 'Nom de mission' est requis.")
    );
    $this->form_validation->set_rules('Chads', 'Chads', 'trim|is_natural',
        array('is_natural' => "Le champ 'Chads' doit être un nombre, minimum : 0.")
    );

    if($this->form_validation->run() == FALSE){
      $this->load->model('activites_model');
      $data['activites'] = $this->activites_model->getPreviousAndNextActivites($today);

      $this->load->model('histoire_model');
      $data['royaumes'] = $this->histoire_model->getRoyaumes();
      $data['missions'] = $this->histoire_model->getMissions();

      $this->load->view('template/header', $data);
      $this->load->view('histoire/missions', $data);
      $this->load->view('template/footer',$data);
    } else{
      //GET Activite Nom
      $this->load->model('activites_model');
      $activite = $this->activites_model->getActivites( $this->input->post('idActivite') )[0];

      $this->load->model('histoire_model');
    
      /*//Create DIR IF not exists
      if(is_dir('/home/terresdebelenos/terres-de-belenos.com/BD/uploads/Missions/' . $activite->Nom) == FALSE){
        mkdir('/home/terresdebelenos/terres-de-belenos.com/BD/uploads/Missions/' . $activite->Nom, 0755, true);
      }

      $fileName = "IDMISSION" .'-' .$this->input->post('Code') .'-' .$this->input->post('Nom') ."_NOMCHEF.pdf";

      //UPLOAD FILE
      $config['upload_path']          = '/home/terresdebelenos/terres-de-belenos.com/BD/uploads/Missions/' . $activite->Nom .'/';
      $config['allowed_types']        = 'pdf';
      $config['overwrite']            = TRUE;
      $config['remove_spaces']        = FALSE;
      $config['file_name']        = $fileName;

      $this->load->library('upload', $config);*/

      /*if ( $_FILES['FichierResultat']['size'] != 0 && !$this->upload->do_upload( 'FichierResultat' ) ){
        $this->load->model('activites_model');
        $data['activites'] = $this->activites_model->getPreviousAndNextActivites($today);

        $this->load->model('histoire_model');
        $data['royaumes'] = $this->histoire_model->getRoyaumes();
        $data['missions'] = $this->histoire_model->getMissions();

        $data['file_error'] = $this->upload->display_errors();

        $this->load->view('template/header', $data);
        $this->load->view('histoire/missions', $data);
        $this->load->view('template/footer',$data);
      }
      else{*/

        $this->histoire_model->addMission();

        /*if($_FILES['FichierResultat']['size'] > 0)
        $data = array('upload_data' => $this->upload->data());*/

        redirect('histoire/missions','refresh');
      //}
    }
  }

  public function getMission($idMission){
      $this->load->model('histoire_model');

      $mission = $this->histoire_model->getMission($idMission);
      $participants = $this->histoire_model->getMissionParticipant($idMission);

      $jsonMission = json_encode($mission);

      $this->load->model('activites_model');
      $activite = $this->activites_model->getActivites( $mission->IdActivite )[0];

      $arrayMission = json_decode($jsonMission, true);

      $arrayMission['NomActivite'] = $activite->Nom;
      $arrayMission['participants'] = $participants;

      $return = json_encode($arrayMission);

      echo $return;

  }

  public function editMission($idMission){

    //GET Activite Nom
    $this->load->model('activites_model');
    $activite = $this->activites_model->getActivites( $this->input->post('idActivite') )[0];

    $this->load->model('personnages_model');
    $chef = $this->personnages_model->getPersoInfo( $this->input->post('idChef') );

    $this->load->model('histoire_model');
  
    //Create DIR IF not exists
    if(is_dir('/home/terresdebelenos/terres-de-belenos.com/BD/uploads/Missions/' . $activite->Nom) == FALSE){
      mkdir('/home/terresdebelenos/terres-de-belenos.com/BD/uploads/Missions/' . $activite->Nom, 0755, true);
    }

    $fileName = $idMission .'-' .$this->input->post('Code') .'-' .$this->input->post('Nom') .'_' .$chef->nomPerso;
    $invalid = array('/','\\','?','.',':','#','*','|','"', '<', '>');
    $replace = '_';

    //UPLOAD FILE
    $config['upload_path']      = '/home/terresdebelenos/terres-de-belenos.com/BD/uploads/Missions/' . $activite->Nom .'/';
    $config['allowed_types']    = 'pdf';
    $config['overwrite']        = TRUE;
    $config['remove_spaces']    = FALSE;
    $config['file_name']        = str_replace($invalid, $replace, $fileName) . ".pdf";

    $this->load->library('upload', $config);

    //GET Activite Nom
    $this->load->model('activites_model');
    $activite = $this->activites_model->getActivites( $this->input->post('idActivite') )[0];

    //Create DIR IF not exists
    if(is_dir('/home/terresdebelenos/terres-de-belenos.com/BD/uploads/Missions/' . $activite->Nom) == FALSE){
      mkdir('/home/terresdebelenos/terres-de-belenos.com/BD/uploads/Missions/' . $activite->Nom, 0755, true);
    }

    if ( $_FILES['FichierResultat']['size'] != 0 && !$this->upload->do_upload( 'FichierResultat' ) ){
      $this->load->model('activites_model');
      $data['activites'] = $this->activites_model->getPreviousAndNextActivites($today);

      $this->load->model('histoire_model');
      $data['royaumes'] = $this->histoire_model->getRoyaumes();
      $data['missions'] = $this->histoire_model->getMissions();

      $data['file_error'] = $this->upload->display_errors();

      $this->load->view('template/header', $data);
      $this->load->view('histoire/missions', $data);
      $this->load->view('template/footer',$data);
    }
    else{
      $this->load->model('histoire_model');

      if($_FILES['FichierResultat']['size'] > 0){
        $data = array('upload_data' => $this->upload->data());
      } else{
        $fileName = null;
      }

      $this->histoire_model->editMission($idMission, $fileName);

      redirect('histoire/missions','refresh');
    }
  }

  public function getQRLectures(){
    $this->load->model('histoire_model');
    $lectures = $this->histoire_model->getQRLectures();

    $metiers = $this->histoire_model->getPersoMetiers($lectures);

    $return = array(
      'lectures' => $lectures,
      'metiers' => $metiers
    );

    echo json_encode($return);
  }

  public function populateMission($idMission){
    
    $data = array();

    foreach ($_POST as $key => $input) {
      if(str_contains($key, 'Resultat') !== FALSE){
        $idPerso = explode('-',$key)[1];
        $data[$idPerso]["Résultat"] = $input;
        $data[$idPerso]["IdPersonnage"] = $idPerso;
        $data[$idPerso]["IdMission"] = $idMission;
      }

      if(str_contains($key, 'IdChef') !== FALSE){
        $idChef = $input;
      }

      if(str_contains($key, 'Metier') !== FALSE){
        $idPerso = explode('-',$key)[1];
        $data[$idPerso]["Metier"] = $input;
      }
    }    

    $this->load->model('histoire_model');
    $result = $this->histoire_model->populateMission($data);
    $result = $this->histoire_model->updateChefMission($idMission, $idChef);

    $this->histoire_model->clearQRLectures();

    redirect('histoire/missions','refresh');
  }

  public function duplicateMission($idMission){
    $this->load->model('histoire_model');
    
    $this->histoire_model->duplicateMission($idMission);

    redirect('histoire/missions','refresh');
  }

  public function removeParticipant($idMission){
    $this->load->model('histoire_model');
    
    $this->histoire_model->removeParticipant($idMission);
  }
}

?>