<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inscriptions extends CI_Controller {

	public function __construct()	{
		parent::__construct();
		$this->load->model('inscriptions_model');
	}

	public function index($idActiv = null)	{
		$data['activites'] = $this->inscriptions_model->getActivites();

		$data['idActiv'] = $idActiv;

		$this->load->view('template/header', $data);
    $this->load->view('inscriptions/search', $data);
    $this->load->view('template/footer',$data);

	}

	public function searchIndiv($idActiv = null){
		$data['results'] = $this->inscriptions_model->searchIndiv();
		$data['activites'] = $this->inscriptions_model->getActivites();

		$data['idActiv'] = ( isset($_POST['idActiv'] ) )? $_POST['idActiv'] : $idActiv;

		$this->load->view('template/header', $data);
	    $this->load->view('inscriptions/search', $data);
	    $this->load->view('template/footer',$data);
	}

	public function editInscription($idIndiv, $idActiv){
		if($idActiv == 'null'){
			$idActiv = $this->input->post('idActiv');
		}
		$data['hasPaid'] = $this->inscriptions_model->hasPaid($idIndiv, $idActiv);
		$data['inscription'] = $this->inscriptions_model->isInscrit($idIndiv, $idActiv);

		$data['belePoints'] = $this->inscriptions_model->getBelePoints($idIndiv)[0];

		$data['hasDebts'] = $this->inscriptions_model->hasDebts($idIndiv);

		$data['isLocation'] = false;//$this->inscriptions_model->isLocation($idActiv);

		$data['personnages'] = $this->inscriptions_model->getPersonnages($idIndiv, $idActiv);

		$data['activePasses'] = $this->inscriptions_model->getActivePasses();

		$indivHasPass = $this->inscriptions_model->indivHasPass($idIndiv, $idActiv);

		$data['indivHasPass'] = $indivHasPass;

		$data['preInscription'] = $this->inscriptions_model->getPreInscription($idIndiv, $idActiv, $indivHasPass);

		$data['metiers'] = $this->inscriptions_model->getMetiers();

		//Pour Chroniques
		$data['typeActivite'] = $this->inscriptions_model->getTypeActivite($idActiv);
		$data['availableSkills'] = $this->inscriptions_model->getAvailableSkills($idIndiv, $idActiv);
		$data['levelPerso'] = $this->inscriptions_model->getLevelPerso($idIndiv, $idActiv);
		$data['groups'] = $this->inscriptions_model->getGroups();
		//*END*//
		$data['prix'] = '55';//$this->inscriptions_model->getPrix($idActiv);

		//Pour missives et quêtes
		$this->load->model('personnages_model');

		if( $data['typeActivite']->Type == 'GN' || $data['typeActivite']->Type == 'CHRONIQ'){
			$data['has_missives'] = $this->personnages_model->has_missives(null, $idActiv, $idIndiv, $data['typeActivite']->Type);
			$data['has_quests'] = $this->personnages_model->has_quests(null, $idActiv, $idIndiv,  $data['typeActivite']->Type );
			$data['hasNoMetier'] = $this->inscriptions_model->hasNoMetier($data['inscription']->IdPersonnage);
			$data['has_missions'] = $this->personnages_model->has_missions(null, $idActiv, $idIndiv);
		}


    $this->load->view('template/header', $data);
    $this->load->view('inscriptions/editInscription', $data);
    $this->load->view('template/footer',$data);

	}

	public function addInscription($idIndiv, $idActiv){
		$this->inscriptions_model->addInscription($idIndiv, $idActiv);

		if($this->inscriptions_model->getActiviteType($idActiv) == 'GN'){
			$this->inscriptions_model->addXP($idIndiv, $idActiv);
		}

		redirect('/inscriptions/editInscription/' .$idIndiv . '/' . $idActiv ,'refresh');
	}

	public function addPresence($idIndiv, $idActiv, $idPerso = 99999){
		if($this->input->post('typeActivite') == 'CHRONIQ'){
			$this->inscriptions_model->editChroniqueInscription($idIndiv, $idActiv);
		}
		if($this->input->post('typeActivite') == 'TOURNOI'){
			$this->inscriptions_model->addPresence($idIndiv, $idActiv, null);
		}else{
			$this->inscriptions_model->addPresence($idIndiv, $idActiv, $idPerso);
		}

		//	IF USED BELEPOINTS
		if($this->input->post('belePointsUsed') > 0){
			$this->inscriptions_model->useBelePoints($idIndiv, $this->input->post('belePointsUsed'));
		}

		if($this->inscriptions_model->getActiviteType($idActiv) != 'GN'){
			$this->inscriptions_model->addXP($idIndiv, $idActiv);
		}

		if($_SESSION['infoUnivers'] == 'BELEJR'){
			$this->inscriptions_model->addXP($idIndiv, $idActiv);
		}

		//PASS PAYMENT
		//CHECK IF JOUEUR HAS PASSES
		if(!empty($this->input->post('paidPasses'))){
			foreach ($this->input->post('paidPasses') as $idPass) {
				//HAS PASS - REMOVE DEBT
				if( $this->inscriptions_model->indivHasSelectedPass($idIndiv, $idPass) == true ){
					$this->inscriptions_model->payPass($idIndiv, $idPass);
				} else{
					//ADD PASS
					$this->load->model('passes_model');
					$this->passes_model->linkPassPlayer($idIndiv, $idPass);
				}				
			}
		}

		redirect('/inscriptions/editInscription/' .$idIndiv . '/' . $idActiv ,'refresh');
	}

	public function addFreePresence($idIndiv, $idActiv, $idPerso, $isBenevole = false){

		$this->inscriptions_model->addPresence($idIndiv, $idActiv, $idPerso);

		if($isBenevole === false){
			$this->inscriptions_model->removeActiviteGratuite($idIndiv);
		} else{
			$this->inscriptions_model->setInscriptionToGratuit($idIndiv, $idActiv);
		}

		redirect('/inscriptions/editInscription/' .$idIndiv . '/' . $idActiv ,'refresh');
	}

	public function searchInscriptions(){
		$data['activites'] = $this->inscriptions_model->getActivites();

		$this->load->view('template/header', $data);
	    $this->load->view('inscriptions/searchInscriptions', $data);
	    $this->load->view('template/footer',$data);
	}

	public function getInscriptions(){
		$idActiv = $this->input->post('idActiv');

		$typeActivite = $this->inscriptions_model->getTypeActivite($idActiv);

		$results = $this->inscriptions_model->searchInscriptions($idActiv);

		if(count($results) != 0){
			$data['results'] = $this->inscriptions_model->searchInscriptions($idActiv);
		} else {
			$data['noResults'] = true;
		}

		$data['activites'] = $this->inscriptions_model->getActivites();
		$data['typeActivite'] = $typeActivite->Type;
		$data['totalRepas'] = $this->inscriptions_model->getTotalRepas($idActiv);
		$data['groupes'] = $this->inscriptions_model->getGroupesForActivite($idActiv);

		$this->load->view('template/header', $data);
        $this->load->view('inscriptions/searchInscriptions', $data);
        $this->load->view('template/footer',$data);
	}

	public function deleteInscription($idActiv, $idIndiv, $idPerso){
		$this->inscriptions_model->deleteInscription($idActiv, $idIndiv, $idPerso);

		redirect('/inscriptions/searchInscriptions','refresh');
	}

	public function deleteAllInscriptions($idActiv){
		$inscriptions = $this->inscriptions_model->searchInscriptions($idActiv);

		foreach ($inscriptions as $inscription) {
			$this->inscriptions_model->deleteInscription($inscription->IdActivite,$inscription->IdIndividu,$inscription->IdPersonnage);
		}
		redirect('/inscriptions/searchInscriptions','refresh');
	}

	public function searchPresences(){
		$data['activites'] = $this->inscriptions_model->getActivites();

		$this->load->view('template/header', $data);
        $this->load->view('inscriptions/searchPresences', $data);
        $this->load->view('template/footer',$data);
	}

	public function getPresences(){
		$idActiv = $this->input->post('idActiv');
		$data['results']['presences'] = $this->inscriptions_model->getPresences($idActiv);
		$data['results']['inscriptions'] = $this->inscriptions_model->getInscriptions($idActiv);

		$data['total'] = 0;

		foreach ($data['results']['presences'] as $result) {
			$data['total'] += floatval($result->Recu);
		}

		$data['activites'] = $this->inscriptions_model->getActivites();

		$this->load->view('template/header', $data);
        $this->load->view('inscriptions/searchPresences', $data);
        $this->load->view('template/footer',$data);
	}

	public function downloadPresencesList($idActiv){
		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=ListedePrésences.csv');

		// create a file pointer connected to the output stream
		$output = fopen('php://output', 'w');

		// output the column headings
		fputcsv($output, array('Nom du joueur', 'Recu ($)'));

		$vRows = $this->inscriptions_model->downloadPresencesList($idActiv);

		foreach ($vRows as $row) {
			fputcsv($output, $row);
		}
	}

	public function downloadInscriptionList($idActiv){
		//**POUR BJ**//
		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=ListedePrésences.csv');

		// create a file pointer connected to the output stream
		$output = fopen('php://output', 'w');

		// output the column headings
		fputcsv($output, array('Nom du joueur', 'Nom du Personnage', 'Religion', 'Nb Lutin', 'Nb Repas', 'Groupe Cadre', 'Type Billet', 'Prix Inscrit'));

		$vRows = $this->inscriptions_model->downloadInscriptionList($idActiv);

		foreach ($vRows as $row) {
			fputcsv($output, $row);
		}
	}

	public function addMetier($idPerso, $idIndiv, $idActiv){
		$this->inscriptions_model->addMetier($idPerso);

		$this->editInscription($idIndiv, $idActiv);
	}
}

/* End of file Inscriptions.php */
/* Location: ./application/controllers/Inscriptions.php */ ?>