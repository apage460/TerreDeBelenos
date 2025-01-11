<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Old_pPersonnages extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index() {
		$data = array();

		$this->load->view('template/header', $data);
        $this->load->view('forms/form-searchPersonnages', $data);
        $this->load->view('template/footer',$data);
	}

	public function searchPersonnages() {
		$data = array();

		$this->load->model('personnages_model');

		$prenomJoueur = $_POST['prenomJoueur'];
		$nomJoueur = $_POST['nomJoueur'];

		$prenomPerso = $_POST['prenomPerso'];
		$nomPerso = $_POST['nomPerso'];

		$avantApres = $_POST['avantApres'];
		$ddn = $_POST['ddn'];

		$niveau = $_POST['niveau'];
		$niveauCtrl = $_POST['niveauCtrl'];

		$classe = $_POST['classe'];
		$race = $_POST['race'];
		$religion = $_POST['religion'];

		$data['searchResults'] = $this->personnages_model->getSearchResults($prenomJoueur, $nomJoueur, $avantApres, $ddn, $prenomPerso, $nomPerso, $classe, $race, $religion, $niveau, $niveauCtrl);

		$this->load->view('ajax/displaySearchResults', $data);
        
	}

	public function getSinglePersonnage($id, $idIndiv){
		$data = array();
		$this->load->model('personnages_model');

		
		$data['personnageData'] = $this->personnages_model->getFullPersonnage($id);

		$this->load->model('individus_model');
		$data['individuData'] = $this->individus_model->getFullIndividu($idIndiv);

		$this->load->view('template/header', $data);
        $this->load->view('singleCharacter', $data);
        $this->load->view('template/footer',$data);
	}

	public function getReligion($id){
		$data = array();
		$this->load->model('personnages_model');

		$data['personnageData'] = $this->personnages_model->getReligion($id);

		$this->load->view('modals/changeReligion', $data);     

	}

	public function changeReligion(){
		$newReligion = $_POST['newReligion'];
		$idPerso = $_POST['idPerso'];
		$idIndiv = $_POST['idIndiv']; 

		$this->load->model('personnages_model');
		$returned = $this->personnages_model->updateReligion($newReligion, $idPerso);

		redirect('personnages/getSinglePersonnage/' .$idPerso .'/' .$idIndiv);
	}

	public function adjustXP(){
		$idPerso = $_POST['idPerso'];
		$nbXP = $_POST['nbXP'];
		$commentaireXP = $_POST['commentaireXP'];
		$idIndiv = $_POST['idIndiv']; 

		$this->load->model('personnages_model');
		$returned = $this->personnages_model->adjustXP($idPerso, $nbXP, $commentaireXP);

		if($returned){
			redirect('personnages/getSinglePersonnage/' .$idPerso .'/' .$idIndiv);
		}
	}

	public function addSkill(){
		$idPerso = $_POST['idPerso'];
		$idIndiv = $_POST['idIndiv'];
		$codeSkill = $_POST['codeSkill']; 
		$usagesSkill = $_POST['usagesSkill'];

		$this->load->model('personnages_model');
		$returned = $this->personnages_model->addSkill($idPerso, $codeSkill, $usagesSkill);

		if($returned){
			redirect('personnages/getSinglePersonnage/' .$idPerso .'/' .$idIndiv);
		}
	}

	public function deleteSkill(){
		$idPerso = $_POST['idPerso'];
		$idIndiv = $_POST['idIndiv'];
		$idListSkill = $_POST['idListSkill'];

		$this->load->model('personnages_model');
		$returned = $this->personnages_model->deleteSkill($idListSkill);

		if($returned){
			redirect('personnages/getSinglePersonnage/' .$idPerso .'/' .$idIndiv);
		}
	}

	public function deleteTravail(){
		$idPerso = $_POST['idPerso'];
		$idIndiv = $_POST['idIndiv'];
		$idTravail = $_POST['idTravail'];

		$this->load->model('personnages_model');
		$returned = $this->personnages_model->deleteTravail($idTravail);

		if($returned){
			redirect('personnages/getSinglePersonnage/' .$idPerso .'/' .$idIndiv);
		}

	}

	public function modifPv(){
		$idPerso = $_POST['idPerso'];
		$idIndiv = $_POST['idIndiv'];
		$raison = $_POST['raison'];
		$commentairesPv = $_POST['commentairesPv'];

		$this->load->model('personnages_model');
		$returned = $this->personnages_model->modifPv($idPerso, $raison, $commentairesPv);

		if($returned){
			redirect('personnages/getSinglePersonnage/' .$idPerso .'/' .$idIndiv);
		}
	}

	public function lvlUp(){
		$idPerso = $_POST['idPerso'];
		$idIndiv = $_POST['idIndiv'];

		$this->load->model('personnages_model');

		$returned = $this->personnages_model->lvlUP($idPerso, $idIndiv);

		if($returned){
			redirect('personnages/getSinglePersonnage/' .$idPerso .'/' .$idIndiv);
		}

	}
}

?>