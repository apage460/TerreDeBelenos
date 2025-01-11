<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inscriptions extends CI_Controller {

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
		$data= array();

		$this->load->model('old_inscriptions_model');
		$data['activites'] = $this->old_inscriptions_model->getActivites();

		$this->load->view('template/header', $data);
        $this->load->view('inscriptions', $data);
        $this->load->view('template/footer',$data);
	}

	public function addDebt() {
		$data= array();

		$this->load->model('old_inscriptions_model');
		$data['activites'] = $this->old_inscriptions_model->getActivites();

		$this->load->view('template/header', $data);
        $this->load->view('inscriptions', $data);
        $this->load->view('template/footer',$data);
	}

	public function indexPresences() {
		$data= array();

		$this->load->model('old_inscriptions_model');
		$data['activites'] = $this->old_inscriptions_model->getActivites();

		$this->load->view('template/header', $data);
        $this->load->view('presences', $data);
        $this->load->view('template/footer',$data);
	}

	public function getInscriptions(){
		$idActivite = $this->input->post('idActivite');
		$prenom = $this->input->post('prenom');

		$this->load->model('old_inscriptions_model');
		$data['inscriptions'] = $this->old_inscriptions_model->getInscriptions($idActivite, $prenom);

		$this->load->view('ajax/displayInscriptions', $data);
	}

	public function addPresence(){
		$idIndividu = $this->input->post('idIndividu');
		$idActivite = $this->input->post('idActivite');
		$commentaires = $this->input->post('commentaires');
		$montantDu = $this->input->post('montantDu');
		$nomActivite = $this->input->post('nomActivite');

		$this->load->model('old_inscriptions_model');

		$this->old_inscriptions_model->addPresence($idIndividu,$idActivite, $commentaires, $montantDu, $nomActivite);
	}

	public function getPresences(){
		$idActivite = $this->input->post('idActivite');

		$this->load->model('old_inscriptions_model');
		$data['presences'] = $this->old_inscriptions_model->getPresences($idActivite);

		$this->load->view('ajax/displayPresences',$data);
	}

	public function deletePresence(){
		$idIndividu = $this->input->post('idIndividu');
		$idActivite = $this->input->post('idActivite');

		$this->load->model('old_inscriptions_model');
		$data['presences'] = $this->old_inscriptions_model->delPresence($idIndividu, $idActivite);
	}

	public function searchIndividus(){
		$data = array();

		$this->load->model('individus_model');

		$compte = $_POST['compte'];
		$prenomJoueur = $_POST['prenomJoueur'];
		$nomJoueur = $_POST['nomJoueur'];
		$idActiv = $_POST['idActiv'];

		$data['individus'] = $this->individus_model->quickInscription($compte, $prenomJoueur, $nomJoueur);

		$this->load->model('old_inscriptions_model');
		$data['activites'] = $this->old_inscriptions_model->getActivites();

		if($idActiv == null) :
			$data['activites'] = $this->old_inscriptions_model->getActivites();
			$typeActiv = $data['activites'][0]->Type;
		else :
			$data['activites'] = $this->old_inscriptions_model->getActivites($idActiv);
			$typeActiv = $data['activites'][0]->Type;
		endif;

		$data['couts'] = $this->old_inscriptions_model->getCouts( $typeActiv );

		$data['activites'] = $this->old_inscriptions_model->getActivites();

		$data['idActiv'] = $idActiv;

		$this->load->view('ajax/displayIndivSearchResults',$data);
	}

	public function displayQuickInscription(){
		$data= array();

		$this->load->view('template/header', $data);
        $this->load->view('forms/form-quickInscription', $data);
        $this->load->view('template/footer',$data);
	}

	public function quickInscription(){
		$idActivite = $this->input->post('idActivite');
		$idIndividu = $this->input->post('idIndividu');
		$nomActivite = $this->input->post('nomActivite');
		$typeActivite = $this->input->post('typeActivite');
		$montant = $this->input->post('montant');

		$this->load->model('old_inscriptions_model');
		$this->old_inscriptions_model->quickInscription($idActivite, $idIndividu, $nomActivite, $typeActivite, $montant);

		redirect('/home/index/', 'refresh');
	}

	public function activiteGratuite($idIndiv, $nbActGrat){
		$this->load->model('old_inscriptions_model');
		$this->old_inscriptions_model->activiteGratuite($idIndiv,$nbActGrat);

		redirect('inscriptions/displayQuickInscription','refresh');

	}

	
}
