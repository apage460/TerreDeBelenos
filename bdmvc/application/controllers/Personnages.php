<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Personnages extends CI_Controller {
	public function __construct()	{
		parent::__construct();
		$this->load->model('personnages_model');
	}

	public function index()	{
		$data = array();

		$data['races'] = $this->personnages_model->getRaces();
		$data['religions'] = $this->personnages_model->getReligions();
		$data['classes'] = $this->personnages_model->getClasses();

		$this->load->view('template/header', $data);
        $this->load->view('personnages/search', $data);
        $this->load->view('template/footer',$data);
	}

	public function searchPerso(){
		$data = array();

		$data['races'] = $this->personnages_model->getRaces();
		$data['religions'] = $this->personnages_model->getReligions();
		$data['classes'] = $this->personnages_model->getClasses();

		$data['results'] = $this->personnages_model->getResults();

		$this->load->view('template/header', $data);
        $this->load->view('personnages/search', $data);
        $this->load->view('template/footer',$data);
	}

	public function editPersonnage($idPerso, $idIndiv){
		$data = array();

		$data['races'] = $this->personnages_model->getRaces();
		$data['religions'] = $this->personnages_model->getReligions();
		$data['classes'] = $this->personnages_model->getClasses();
		$data['subClasses'] = $this->personnages_model->getSubClasses();
		$data['moyens_resurrection'] = $this->personnages_model->getMoyensResurrection();

		$data['infoIndiv'] = $this->personnages_model->getIndivInfo($idIndiv);
		$data['infoPerso'] = $this->personnages_model->getPersoInfo($idPerso);

		$data['XP'] = $this->personnages_model->getXP($idPerso);
		$data['PV'] = $this->personnages_model->getPV($idPerso);

		$data['titres'] = $this->personnages_model->getTitres($idPerso);

		$data['conditions'] = $this->personnages_model->getConditions($idPerso);

		$data['allTitres'] = $this->personnages_model->getAllTitres($idPerso);

		$data['skills'] = $this->personnages_model->getSkills($idPerso, FALSE);

		$data['spells'] = $this->personnages_model->getSpells($idPerso);

		$data['moyens_resurrection'] = $this->personnages_model->getMoyensResurrection($idPerso);

		$this->load->model('activites_model');
		$nextGN = $this->activites_model->getNextGN();

		$data['nextGN'] = $nextGN;

		$data['has_paid'] = $this->personnages_model->has_paid($idIndiv, $nextGN->Id);

		$data['has_missives'] = $this->personnages_model->has_missives($idPerso, $nextGN->Id);
		$data['has_quests'] = $this->personnages_model->has_quests($idPerso, $nextGN->Id);
		$data['has_missions'] = $this->personnages_model->has_missions($idPerso, $nextGN->Id);

		$this->load->view('template/header', $data);
        $this->load->view('personnages/singleJoueur', $data);
        $this->load->view('template/footer',$data);
	}

	public function editReligion($idPerso, $idIndiv){

		$this->personnages_model->editReligion($idPerso);

		redirect('/personnages/editPersonnage/' .$idPerso . '/' . $idIndiv ,'refresh');

	}

	public function editClasse($idPerso, $idIndiv){
		$this->personnages_model->editClasse($idPerso);
		redirect('/personnages/editPersonnage/' .$idPerso . '/' . $idIndiv ,'refresh');
	}

	public function editSkills($idPerso, $idIndiv){
		$data['infoIndiv'] = $this->personnages_model->getIndivInfo($idIndiv);
		$data['infoPerso'] = $this->personnages_model->getPersoInfo($idPerso);

		$data['XP'] = $this->personnages_model->getXP($idPerso);

		$data['skills'] = $this->personnages_model->getSkills($idPerso);
		$data['regSkills'] = $this->personnages_model->getRegSkills($idPerso);

		$data['specSkills'] = $this->personnages_model->getSpecSkills();

		$this->load->view('template/header', $data);
        $this->load->view('personnages/editskills', $data);
        $this->load->view('template/footer',$data);
	}

	public function paySkill($idPerso, $idIndiv){
		$this->personnages_model->paySkill($idPerso);

		redirect('/personnages/editSkills/' .$idPerso . '/' . $idIndiv ,'refresh');

	}

	public function giveSkill($idPerso, $idIndiv){
		$this->personnages_model->giveSkill($idPerso);

		redirect('/personnages/editSkills/' .$idPerso . '/' . $idIndiv ,'refresh');
	}

	public function deleteSkill($idPerso, $idIndiv, $idSkill, $codeEtat){
		$this->personnages_model->deleteSkills($idSkill, $codeEtat);

		redirect('/personnages/editSkills/' .$idPerso . '/' . $idIndiv ,'refresh');
	}

	public function declareMort($idPerso, $idIndiv){
		$this->personnages_model->declareMort($idPerso);

		redirect('/personnages/editPersonnage/' .$idPerso . '/' . $idIndiv ,'refresh');
	}

	public function levelUP($idPerso, $idIndiv, $currentLvl){
		$this->personnages_model->levelUP($idPerso, $idIndiv, $currentLvl);

		redirect('/personnages/editPersonnage/' .$idPerso . '/' . $idIndiv ,'refresh');
	}

	public function addTitre($idPerso, $idIndiv){

		$this->personnages_model->addTitre($idPerso, $idIndiv);
		redirect('/personnages/editPersonnage/' .$idPerso . '/' . $idIndiv ,'refresh');

	}

	public function addSpell($idPerso, $idIndiv){

		$this->personnages_model->addSpell($idPerso);
		redirect('/personnages/editPersonnage/' .$idPerso . '/' . $idIndiv ,'refresh');

	}

	public function removeTitre($idPerso, $idIndiv, $idTitre){

		$this->personnages_model->removeTitre($idTitre);
		redirect('/personnages/editPersonnage/' .$idPerso . '/' . $idIndiv ,'refresh');

	}

	public function editSpells($idPerso, $idIndiv){
		$data['infoIndiv'] = $this->personnages_model->getIndivInfo($idIndiv);
		$data['infoPerso'] = $this->personnages_model->getPersoInfo($idPerso);

		$data['allMetiers'] = $this->personnages_model->getAllMetiers();
		$data['allSpells'] = $this->personnages_model->getAllSpells();
		$data['allRecettes'] = $this->personnages_model->getAllRecettes();

		$data['spells'] = $this->personnages_model->getSpells($idPerso);

		$this->load->view('template/header', $data);
    $this->load->view('personnages/editspells', $data);
    $this->load->view('template/footer',$data);
	}

	public function updateSpells($idPerso, $idIndiv){
		$spells = [];

		foreach ($_POST as $key => $value) {
			if ($value != "") {
				$spells[$key] = $value;
			}
		}

		$this->personnages_model->updateSpells($idPerso, $idIndiv, $spells);

		redirect('/personnages/editSpells/' .$idPerso . '/' . $idIndiv ,'refresh');
	}

	public function editNoteRapide($idPerso, $idIndiv){

		$data = $this->input->post('noteRapide');

		$this->personnages_model->editNoteRapide($idPerso, $idIndiv, $data);

		redirect('/personnages/editPersonnage/' .$idPerso . '/' . $idIndiv ,'refresh');
	}

	public function changeState($idPerso, $idIndiv){

		if($_POST['newState']){
			$newState = $_POST['newState'];
			$this->personnages_model->changeState($idPerso, $newState);
		}

		redirect('/personnages/editPersonnage/' .$idPerso . '/' . $idIndiv ,'refresh');
	}

	public function missives(){
		//GET NEXT GN Name
		$this->load->model('activites_model');
		$nextGNName = $this->activites_model->getNextGNName();

		$this->load->model('personnages_model');
		$missiveDir = "/home/terresdebelenos/terres-de-belenos.com/BD/uploads/Missives/";
		$allMissives = glob($missiveDir . $nextGNName->Nom ."/*.pdf");

		$missivesNonPNJ = $this->personnages_model->getMissivesNonPNJ($nextGNName->Id);

		$data['missives'] = array();

		foreach ($missivesNonPNJ as $key => $missive) {
			$missiveDir = "/home/terresdebelenos/terres-de-belenos.com/BD/uploads/Missives/";
			$missivePath = glob($missiveDir . $nextGNName->Nom ."/*" .$missive->Objet .".pdf");

			foreach($missivePath as $value){
				if(!in_array($value, $data['missives'])){
					array_push($data['missives'], $value);
				}				
			}			
		}

		$data['nextGNName'] = $nextGNName->Nom;
		$data['nextGNId'] = $nextGNName->Id;

		$data['groupes'] = $this->personnages_model->getGroups();

		$this->template->load('personnages/missives', $data);
	}

	public function printMissives($printPage = null){
		$erreurFichier = array();
		
		require_once('/home/terresdebelenos/terres-de-belenos.com/bdmvc/fpdf/fpdf.php');
		require_once('/home/terresdebelenos/terres-de-belenos.com/bdmvc/fpdf/fdpi/autoload.php');
		require_once('/home/terresdebelenos/terres-de-belenos.com/bdmvc/fpdf/fdpi/Fpdi.php');

		$pdf = new \setasign\Fpdi\Fpdi();



		foreach($_POST['missives'] as $key => $value){
			try{
				$pdf->setSourceFile($value);
			} catch(Exception $e){
				array_push($erreurFichier, $value);
				continue;
			}
			$pageCount = $pdf->setSourceFile($value);

			$arrValue = explode('/', $value);
			$fichier = end($arrValue);
			$destinataire = explode('-',$fichier)[0];

			$activite = utf8_decode(array_slice($arrValue, -2, 1)[0]);


			for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
		        // import a page
		        $templateId = $pdf->importPage($pageNo);
		        // get the size of the imported page
		        $size = $pdf->getTemplateSize($templateId);
		        // add a page with the same orientation and size
		        $pdf->AddPage($size['orientation'], $size);
		        // use the imported page
		        $pdf->useTemplate($templateId);

		        if($pageNo == 1){
			        $pdf->SetFont('Helvetica');
			        $pdf->SetXY(5, 5);
			        $pdf->Write(8, 'Destinataire : ' . utf8_decode($destinataire));
			    }
		    }
		    $pdf->AddPage();
		}

		if(count($erreurFichier) > 0){
			$pdf->AddPage();
			$pdf->SetXY(5, 20);
			$pdf->SetFontSize(12);
			$pdf->SetFont('', 'B');
			$pdf->Cell(0, 10,  utf8_decode('Fichier(s) en erreur :'), 0, 1);

			$pdf->SetFontSize(8);
			$pdf->SetFont('','');
			foreach ($erreurFichier as $erreur) {
				$pdf->Cell(0, 10,  '- ' . utf8_decode($erreur), 0, 1);
			}
		}

		$pdf->Output('D', 'Missives - ' . $activite .' - page' .$printPage .'.pdf');

		redirect("/personnages/missives");
	}

	public function ajax_getMissivesThread($idMissive){

		$data = array(
			'missive' => $this->personnages_model->getMissive($idMissive),
			'replies' => $this->personnages_model->getReplies($idMissive)
		);

		//Change CodeEtat to 'LU'
		$this->personnages_model->readMissive($idMissive);

		echo json_encode($data);

	}

	public function ajax_searchMissive(){

		$data = $this->personnages_model->searchMissive();

		echo json_encode($data);

	}

	public function ajax_searchPersoMissive(){
		$data = $this->personnages_model->searchPersoMissive();

		echo json_encode($data);

	}

	public function sendMissive(){
		/*VALIDATION*/
		$this->load->library('form_validation');

		$this->form_validation->set_rules('objet', 'Objet', 'required');
		$this->form_validation->set_rules('corps', 'Corps', 'required');
		$this->form_validation->set_rules('idDestinataire', 'Destinataire', 'required');
		$this->form_validation->set_rules('ecrivain', 'Écrivain', 'required');

		if($this->form_validation->run() == FALSE){
			$data['npcs'] = $this->personnages_model->getNpcs();
			$data['missives'] = $this->personnages_model->getOGMissives();

			$this->template->load('personnages/missives', $data);
		} else {
			/*$this->load->library('email');

			$indiv = $this->personnages_model->getIndivFromPerso($_POST['idDestinataire']);
			
			$this->email->from('email@terres-de-belenos.com', 'Les Terres de Bélénos');
			$this->email->to($indiv->Courriel);
			
			$this->email->subject('La Guerre de la Plume - Vous avez été répondu!');
			$this->email->message('message');
			
			$this->email->send();*/

			$this->personnages_model->sendMissive();

			$data['npcs'] = $this->personnages_model->getNpcs();
			$data['missives'] = $this->personnages_model->getOGMissives();
			$data['success'] = $_POST['objet']; 



			redirect('/personnages/missives/' .$_POST['objet'] );
		}
	}

	public function createNewPersonnage($step = 0, $idIndiv = null){
		$data = array();

		//SEARCH INDIV
		if(isset($_GET['pseudoIndiv']) || isset($_GET['prenomIndiv']) || isset($_GET['nomIndiv']) ){
			$this->load->model('individus_model');
			$data['searchResults'] = $this->individus_model->createPersoGetIndividus($_GET);
		}

		//STEP1
		if($step == 1){
			$this->load->model('individus_model');
			$data['step'] = $step;
			$data['indiv'] = $this->individus_model->getSingleIndividu($idIndiv);
			$this->load->model('pilot_model');
			$data['pilotRaces'] = $this->pilot_model->getRaces($idIndiv);
		}elseif($step == 2){
			$_SESSION['newPerso'] = array(
				'prenom' => $this->input->post('persoPrenom'),
				'nom' => $this->input->post('persoNom'),
				'race' => $this->input->post('persoRace')
			);

			$this->load->model('pilot_model');
			$data['pilotRaces'] = $this->pilot_model->getRaces($idIndiv);
			$data['pilotClasses'] = $this->pilot_model->getClasses($this->input->post('persoRace'));

			$this->load->model('individus_model');
			$data['step'] = $step;
			$data['indiv'] = $this->individus_model->getSingleIndividu($idIndiv);
		}elseif($step == 3){
			$_SESSION['newPerso']['classe'] = $this->input->post('persoClasse');

			$this->load->model('pilot_model');
			$data['pilotRaces'] = $this->pilot_model->getRaces($idIndiv);
			$data['pilotClasses'] = $this->pilot_model->getClasses($_SESSION['newPerso']['race']);
			$data['pilotArchetypes'] = $this->pilot_model->getArchetypes($this->input->post('persoClasse'));
			$data['pilotReligions'] = $this->pilot_model->getReligions($_SESSION['newPerso']['race']);
			$data['pilotProvenances'] = $this->pilot_model->getRoyaumes();


			$this->load->model('individus_model');
			$data['step'] = $step;
			$data['indiv'] = $this->individus_model->getSingleIndividu($idIndiv);
		}elseif($step == 4){
			$_SESSION['newPerso']['archetype'] = $this->input->post('persoArchetype');
			$_SESSION['newPerso']['religion'] = $this->input->post('persoReligion');
			$_SESSION['newPerso']['provenance'] = $this->input->post('persoProvenance');
			$_SESSION['newPerso']['allegeance'] = $this->input->post('persoAllegeance');

			$this->load->model('pilot_model');
			$data['pilotRaces'] = $this->pilot_model->getRaces($idIndiv);
			$data['pilotClasses'] = $this->pilot_model->getClasses($_SESSION['newPerso']['race']);
			$data['pilotArchetypes'] = $this->pilot_model->getArchetypes($this->input->post('persoClasse'));
			$data['pilotReligions'] = $this->pilot_model->getReligions($_SESSION['newPerso']['race']);
			$data['pilotProvenances'] = $this->pilot_model->getRoyaumes();

			//List Skills
			$data['startingSpecSkills'] = $this->pilot_model->getStartingSpecSkills($_SESSION['newPerso']['race'], $_SESSION['newPerso']['classe'], $_SESSION['newPerso']['archetype']);
			$data['startingRegularSkills'] = $this->pilot_model->getStartingRegularSkills($_SESSION['newPerso']['race'], $_SESSION['newPerso']['classe'], $_SESSION['newPerso']['archetype']);


			$this->load->model('individus_model');
			$data['step'] = $step;
			$data['indiv'] = $this->individus_model->getSingleIndividu($idIndiv);
		}


		$this->load->view('template/header', $data);
        $this->load->view('personnages/createNewPersonnage', $data);
        $this->load->view('template/footer',$data);
	}

	public function printAll($idPerso,$idActiv,$idIndiv){
		$erreurFichier = array();
		$mainDir = "/home/terresdebelenos/terres-de-belenos.com/BD/uploads/";

		$this->load->model('individus_model');
		$lastPresence = $this->individus_model->getLastGN($idPerso, $idActiv, $idIndiv);
		$GNToPrint = $this->individus_model->getMissedGN($lastPresence->DateDebut, $idActiv);

		$filePaths = array();

		//getTypeActivite
		$this->load->model('inscriptions_model');
		$typeActivite = $this->inscriptions_model->getTypeActivite($idActiv);

		$this->load->model('personnages_model');
		if($typeActivite->Type == 'CHRONIQ'){
			//GET ACTIV PERSOS
			$persos = $this->personnages_model->getActivePersos($idIndiv);
		} else{
			$persos = null;
		}
		
		$infoPerso = $this->personnages_model->getPersoInfo($idPerso);

		//GET MISSIVES TO PRINT
		if(!is_null($persos)){
			foreach ($persos as $perso) {
				$lastPresence = $this->individus_model->getLastGN($perso->Id, $idActiv, $idIndiv);
				$GNToPrint = $this->individus_model->getMissedGN($lastPresence->DateDebut, $idActiv);

				$paths = $this->getMissivesPaths($lastPresence->DateDebut, $perso->Id);
				foreach ($paths as $path) {
					array_push($filePaths, $path);
				}
			}
		}
		else{
			$filePaths = $this->getMissivesPaths($lastPresence->DateDebut, $idPerso);
		}

		require_once('/home/terresdebelenos/terres-de-belenos.com/bdmvc/fpdf/fpdf.php');
		require_once('/home/terresdebelenos/terres-de-belenos.com/bdmvc/fpdf/fdpi/autoload.php');
		require_once('/home/terresdebelenos/terres-de-belenos.com/bdmvc/fpdf/fdpi/Fpdi.php');

		$pdf = new \setasign\Fpdi\Fpdi();

		$activite = '';
		$printPage = '';

		foreach($filePaths as $key => $value){
			if(file_exists($value) == false){
				array_push($erreurFichier, $value);
				continue;
			}
			try{
				$pdf->setSourceFile($value);
			} catch(Exception $e){
				array_push($erreurFichier, $value);
				continue;
			}
			$pageCount = $pdf->setSourceFile($value);

			$arrValue = explode('/', $value);

			$activite = utf8_decode(array_slice($arrValue, -2, 1)[0]);

			for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
				
		        // import a page
		        $templateId = $pdf->importPage($pageNo);
		        // get the size of the imported page
		        $size = $pdf->getTemplateSize($templateId);

		        // add a page with the same orientation and size
		        $pdf->AddPage($size['orientation'], $size);

		        // use the imported page
		        $pdf->useTemplate($templateId);

		        if($pageNo == 1){
			        $pdf->SetFont('Helvetica');
			        $pdf->SetXY(5, 5);
			        $pdf->Write(8, utf8_decode('Activité : ') . $activite);
			    }
		    }

		    if(count($filePaths) != $key+1){
			    $pdf->AddPage();
			}
		}

		if(count($erreurFichier) > 0){
			$pdf->AddPage();
			$pdf->SetXY(5, 20);
			$pdf->SetFontSize(12);
			$pdf->SetFont('Courier', 'B');
			$pdf->Cell(0, 10,  utf8_decode('Fichier(s) en erreur :'), 0, 1);

			$pdf->SetFontSize(8);
			$pdf->SetFont('','');
			foreach ($erreurFichier as $erreur) {
				$pdf->Cell(0, 10,  '- ' . utf8_decode($erreur), 0, 1);
			}
		}
		$pdf->Output('I', 'Documents - ' . $activite .' - page' .$printPage .'.pdf');


		redirect('personnages/editPersonnage/' .$idPerso .'/' .$idIndiv);
	}

	private function getMissivesPaths($dateDebut, $idPerso){
		$missivesToPrint = $this->personnages_model->getMissivesToPrint($dateDebut, $idPerso);
		$quetesToPrint = $this->personnages_model->getQuetesToPrint($dateDebut, $idPerso);
		$missionsToPrint = $this->personnages_model->getMissionsToPrint($dateDebut, $idPerso);

		#echo '<pre>'; var_dump($missivesToPrint); die;

		$mainDir = "/home/terresdebelenos/terres-de-belenos.com/BD/uploads/";
		$filePaths = array();

		if($missivesToPrint !== false){
			foreach ($missivesToPrint as $missive) {
				if($missive->Type == 'MISSION'){
					$toPush = $mainDir . "Missions/" . $missive->Nom . "/" . $missive->Fichier;
					array_push($filePaths, $toPush);
				} else{
					$toPush = $mainDir . "Missives/" . $missive->Nom . "/" . $missive->Fichier;
					array_push($filePaths, $toPush);
				}				
			}
		}

		if($quetesToPrint !== false){
			foreach ($quetesToPrint as $quete) {
				$toPush = $mainDir . "Quetes/" . $quete->Nom . "/" . $quete->Fichier;
				array_push($filePaths, $toPush);
			}
		}

		if($missionsToPrint !== false){
			foreach ($missionsToPrint as $mission) {
				$toPush = $mainDir . "Missions/" . $mission->Nom . "/" . $mission->FichierResultat;
				array_push($filePaths, $toPush);
			}
		}

		return $filePaths;
	}

	public function printMetierRez($idPerso, $idActiv = null){
		$skills = $this->personnages_model->getSkills($idPerso, false);
		$infoPerso = $this->personnages_model->getPersoInfo($idPerso);
		$infoIndiv = $this->personnages_model->getIndivFromPerso($idPerso);
		$metiers = $this->personnages_model->getMetiers($idPerso);

		if( !is_null($idActiv) ){
			$this->load->model('inscriptions_model');
			$activite = $this->inscriptions_model->getActivites($idActiv)[0];
		} else{
			$this->load->model('activites_model');
			$activite = $this->activites_model->getLastActivite();
		}

		$nbRez = 0;
		$arrMetier = array();

		//add QR Package
		//DOC : https://github.com/domProjects/CI-PHP_QR_Code/blob/master/application/controllers/Welcome.php
		$this->load->add_package_path(APPPATH . 'third_party/php_qrcode');
		$this->load->library('php_qrcode');

		//set QR DIR
		$saveFolder = 'images'.DIRECTORY_SEPARATOR;
		$outputformat = 'png';

		//Generate QR CODE
		$url = array(
			'data' => 'terres-de-belenos.com/BD/read-work-card.php?IdPerso=' . $idPerso,
			'save_folder' => $saveFolder,
			'save_name' => "qr_" . $idPerso,
			'level' => 'L',
			'size' => 4,
			'margin' => 1,
			'saveandprint' => FALSE,
			'outputformat' => $outputformat
		);

		$data['qr_code'] = $this->php_qrcode->generate($url);

		$qr_path = 'images/qr_' . $idPerso .'.' .$outputformat;

		//STARTS PDF
		require_once('/home/terresdebelenos/terres-de-belenos.com/bdmvc/fpdf/fpdf.php');
		require_once('/home/terresdebelenos/terres-de-belenos.com/bdmvc/fpdf/fdpi/autoload.php');
		require_once('/home/terresdebelenos/terres-de-belenos.com/bdmvc/fpdf/fdpi/Fpdi.php');

		$pdf = new \setasign\Fpdi\Fpdi('P','in',array(3.5,5));

		$ptConstant = 1/72;//A dot is 1/72 of an inch
		$pdf->AddFont('Althea','','althea.php');
		$pdf->AddPage();
		$pdf->SetMargins(0.25,0.25);
		$pdf->SetFont('Althea', '','14');
		$pdf->SetFillColor(255,0,0);
		$pdf->Line(0.25,0.25,3.25,0.25);
		$pdf->Line(3.25,0.25,3.25,4.5);
		$pdf->Line(3.25,4.5,0.25,4.5);
		$pdf->Line(0.25,4.5,0.25,0.25);
		$startingX = $pdf->GetX();
		$startingY = $pdf->GetY();
		$pdf->Cell(2.70,14*$ptConstant,iconv('UTF-8', 'windows-1252', 'Carte de Métier'),'LTR', 2, 'C');
		$pdf->SetFontSize('10');
		$nomActivite = $activite->Nom;
		$pdf->Cell(2.70,10*$ptConstant,iconv('UTF-8', 'windows-1252',$nomActivite),'LR', 2, 'C');
		$pdf->Cell(2.70,5*$ptConstant,iconv('UTF-8', 'windows-1252',''),'LR', 2, 'C');
		$pdf->Cell(2.70,10*$ptConstant,iconv('UTF-8', 'windows-1252', $infoPerso->Prenom . ' ' .$infoPerso->Nom),'LR', 2, 'C');
		$pdf->Cell(2.70,10*$ptConstant,iconv('UTF-8', 'windows-1252', $infoIndiv->Prenom . ' ' . $infoIndiv->Nom),'LR', 2, 'C');
		$pdf->Cell(2.70,10*$ptConstant,iconv('UTF-8', 'windows-1252', '# ' . $idPerso),'LRB', 2, 'C');
		$pdf->Cell(0,20*$ptConstant,'',0, 2, 'C');//SAUT DE LIGNE

		$pdf->SetFontSize('14');
		$pdf->Cell(0,14*$ptConstant,iconv('UTF-8', 'windows-1252', 'Métiers du personnage :'),0, 1, 'L');

		$pdf->SetFontSize('10');
		$pdf->Cell(0,10*$ptConstant,'',0, 1, 'C');

		$pdf->Cell(20*$ptConstant,0,'',0, 0, 'C');
		foreach ($metiers as $metier) {
			$pdf->Cell(0,10*$ptConstant,iconv('UTF-8', 'windows-1252', '[  ] ' . $metier->Precision),0, 1, 'L');
			$pdf->Cell(0,2.5*$ptConstant,'',0, 2, 'C');
			$pdf->Cell(20*$ptConstant,0,'',0, 0, 'C');
		}

		$pageWidth = $pdf->GetPageWidth();
		$imageSizes = getimagesize($qr_path);
		$margin = $pdf->GetX();
		$posY = $pdf->GetY();
		$pdf->Image($qr_path, 1.70 - (($imageSizes[0]/2)*$ptConstant) + 25*$ptConstant, 20*$ptConstant + $posY);

		//ajout Bele Involable
		$beleInvolablePath = 'images/bele-involable.jpg';

		$pdf->Image($beleInvolablePath, (0.25+(1/72)), (3.75-(1/72)), 0.75, 0.75);

		//END CARTON METIER
		//ADD CARTON DEPOT
		//Generate QR CODE
		$url = array(
			'data' => 'terres-de-belenos.com/BD/read-drop-card.php?IdPerso=' . $idPerso,
			'save_folder' => $saveFolder,
			'save_name' => "qr2_" . $idPerso,
			'level' => 'L',
			'size' => 4,
			'margin' => 1,
			'saveandprint' => FALSE,
			'outputformat' => $outputformat
		);

		$data['qr_code2'] = $this->php_qrcode->generate($url);

		$qr_path2 = 'images/qr2_' . $idPerso .'.' .$outputformat;

		$pdf->AddPage();

		$pdf->SetFont('Althea', '','14');
		$pdf->SetFillColor(255,0,0);
		$pdf->Line(0.25,0.25,3.25,0.25);
		$pdf->Line(3.25,0.25,3.25,4.5);
		$pdf->Line(3.25,4.5,0.25,4.5);
		$pdf->Line(0.25,4.5,0.25,0.25);
		$pdf->SetXY($startingX, $startingY); 
		$pdf->Cell(2.70,14*$ptConstant,iconv('UTF-8', 'windows-1252', 'Carte de Dépôt'),'LTR', 2, 'C');
		$pdf->SetFontSize('10');
		$nomActivite = $activite->Nom;
		$pdf->Cell(2.70,10*$ptConstant,iconv('UTF-8', 'windows-1252',$nomActivite),'LR', 2, 'C');
		$pdf->Cell(2.70,5*$ptConstant,iconv('UTF-8', 'windows-1252',''),'LR', 2, 'C');
		$pdf->Cell(2.70,10*$ptConstant,iconv('UTF-8', 'windows-1252', $infoPerso->Prenom . ' ' .$infoPerso->Nom),'LR', 2, 'C');
		$pdf->Cell(2.70,10*$ptConstant,iconv('UTF-8', 'windows-1252', $infoIndiv->Prenom . ' ' . $infoIndiv->Nom),'LR', 2, 'C');
		$pdf->Cell(2.70,10*$ptConstant,iconv('UTF-8', 'windows-1252', '# ' . $idPerso . ' - ' .$infoIndiv->Id),'LRB', 2, 'C');
		$pdf->Cell(0,20*$ptConstant,'',0, 2, 'C');//SAUT DE LIGNE

		$pdf->SetFontSize('20');

		$pdf->Cell(0,10*$ptConstant,'',0, 1, 'C');

		$pdf->Cell(20*$ptConstant,0,'',0, 0, 'C');
		$pdf->SetX(0.25);
		$pdf->Cell(0,10*$ptConstant,iconv('UTF-8', 'windows-1252', 'DÉPÔT'),0, 1, 'C');


		$pageWidth = $pdf->GetPageWidth();
		$imageSizes = getimagesize($qr_path2);
		$margin = $pdf->GetX();
		$posY = $pdf->GetY();
		$pdf->Image($qr_path2, 1.70 - (($imageSizes[0]/2)*$ptConstant) + 25*$ptConstant, 20*$ptConstant + $posY);

		//ajout Bele Involable
		$beleInvolablePath = 'images/bele-involable.jpg';

		$pdf->Image($beleInvolablePath, (0.25+(1/72)), (3.75-(1/72)), 0.75, 0.75);




		$pdf->Output('I');

		//DELETE QR CODE IMAGE
		unlink($qr_path);
		unlink($qr_path2);
	}

	public function printMissivesByGroup(){
		$idGroupe = $this->input->post('idGroup');
		$idActivite = $this->input->post('idActivite');
		$missivesToPrint = $this->personnages_model->getMissivesByGroup($idGroupe, $idActivite);

		$this->load->model('activites_model');
		$nomActivite = $this->activites_model->getSingleActivite($idActivite);

		$path = "/home/terresdebelenos/terres-de-belenos.com/BD/uploads/Missives/" . $nomActivite->Nom ."/";
		$filePaths = array();

		foreach ($missivesToPrint as $missive) {
			array_push($filePaths, $path . $missive->Fichier);
		}

		$erreurFichier = array();

		require_once('/home/terresdebelenos/terres-de-belenos.com/bdmvc/fpdf/fpdf.php');
		require_once('/home/terresdebelenos/terres-de-belenos.com/bdmvc/fpdf/fdpi/autoload.php');
		require_once('/home/terresdebelenos/terres-de-belenos.com/bdmvc/fpdf/fdpi/Fpdi.php');

		$pdf = new \setasign\Fpdi\Fpdi();

		$activite = '';
		$printPage = '';

		foreach($filePaths as $key => $value){
			if(file_exists($value) == false){
				array_push($erreurFichier, $value);
				continue;
			}
			try{
				$pdf->setSourceFile($value);
			} catch(Exception $e){
				array_push($erreurFichier, $value);
				continue;
			}
			$pageCount = $pdf->setSourceFile($value);

			$arrValue = explode('/', $value);

			$activite = utf8_decode(array_slice($arrValue, -2, 1)[0]);

			for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
				
		        // import a page
		        $templateId = $pdf->importPage($pageNo);
		        // get the size of the imported page
		        $size = $pdf->getTemplateSize($templateId);

		        // add a page with the same orientation and size
		        $pdf->AddPage($size['orientation'], $size);

		        // use the imported page
		        $pdf->useTemplate($templateId);

		        if($pageNo == 1){
			        $pdf->SetFont('Helvetica');
			        $pdf->SetXY(5, 5);
			        $pdf->Write(8, utf8_decode('Activité : ') . $activite);
			    }
		    }

		    if(count($filePaths) != $key+1){
			    $pdf->AddPage();
			}
		}

		if(count($erreurFichier) > 0){
			$pdf->AddPage();
			$pdf->SetXY(5, 20);
			$pdf->SetFontSize(12);
			$pdf->SetFont('Courier', 'B');
			$pdf->Cell(0, 10,  utf8_decode('Fichier(s) en erreur :'), 0, 1);

			$pdf->SetFontSize(8);
			$pdf->SetFont('','');
			foreach ($erreurFichier as $erreur) {
				$pdf->Cell(0, 10,  '- ' . utf8_decode($erreur), 0, 1);
			}
		}
		$pdf->Output('I', 'Documents - ' . $activite .' - page' .$printPage .'.pdf');


		redirect('personnages/editPersonnage/' .$idPerso .'/' .$idIndiv);
	}
}

/* End of file Personnages.php */
/* Location: ./application/controllers/Personnages.php */ 
?>