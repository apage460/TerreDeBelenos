<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Administration extends CI_Controller {

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
        $this->load->view('admin/credits', $data);
        $this->load->view('template/footer',$data);
	}

	public function creditsEtDettes() {
		$data = array();

		$this->load->model('administration_model');

		$data['sommaire'] = $this->administration_model->getSommaire();

		$this->load->view('template/header', $data);
        $this->load->view('admin/dettes', $data);
        $this->load->view('template/footer',$data);
	}

	public function deleteCreditOuDette($idCredit){

		$this->load->model('administration_model');

		$this->administration_model->deleteCreditOuDette($idCredit);

		redirect('/administration/creditsEtDettes','refresh');
	}

	public function searchIndividusCredit(){
		$data = array();

		$this->load->model('individus_model');

		$compte = $_POST['compte'];
		$prenomJoueur = $_POST['prenomJoueur'];
		$pseudoJoueur = $_POST['pseudoJoueur'];
		$nomJoueur = $_POST['nomJoueur'];

		$data['individus'] = $this->individus_model->quickInscription($compte, $prenomJoueur, $nomJoueur, $pseudoJoueur);

		$this->load->model('old_inscriptions_model');
		$data['activites'] = $this->old_inscriptions_model->getActivites();

		$this->load->view('ajax/displayAdminCreditsResult',$data);
	}

	public function searchIndividusDette(){
		$data = array();

		$this->load->model('individus_model');

		$compte = $_POST['compte'];
		$prenomJoueur = $_POST['prenomJoueur'];
		$nomJoueur = $_POST['nomJoueur'];

		$data['individus'] = $this->individus_model->quickInscription($compte, $prenomJoueur, $nomJoueur);

		$this->load->view('ajax/displayAdminDettesResult',$data);
	}

	public function addCredit(){
		$montant = $_POST['montantCredit'];
		$idIndividu = $_POST['idIndividu'];
		$raison = $_POST['raisonCredit'];
		$commentaires = $_POST['commentairesCredit'];

		$this->load->model('administration_model');
		$this->administration_model->addCreditOrDebt($idIndividu, $raison, $montant, $commentaires);
		
	}

	public function addDette(){
		$montant = -$_POST['montantDette'];
		$idIndividu = $_POST['idIndividu'];
		$raison = $_POST['raisonDette'];
		$commentaires = $_POST['commentairesDette'];

		$this->load->model('administration_model');
		$this->administration_model->addCreditOrDebt($idIndividu, $raison, $montant, $commentaires);

	}

	public function removeCreditOrDebt(){
		$idSomme = $_POST['idSomme'];

		$this->load->model('administration_model');
		$this->administration_model->removeCreditOrDebt($idSomme);
	}

	public function getCreditsAndDebts($idIndiv){
		$this->load->model('administration_model');
		$data['CreditsAndDebts'] = $this->administration_model->getCreditsAndDebts($idIndiv);

		$this->load->view('template/header', $data);
        $this->load->view('admin/credits-and-debts', $data);
        $this->load->view('template/footer',$data);
	}

	public function avertissements(){
		$data = array();

		$this->load->model('administration_model');
		$data['avertissements'] = $this->administration_model->getAllAvertissements();

		$this->load->view('template/header', $data);
        $this->load->view('admin/avertissements', $data);
        $this->load->view('template/footer',$data);
	}

	public function searchIndividusAvertissements(){
		$data = array();

		$this->load->model('individus_model');

		$compte = $_POST['compte'];
		$prenomJoueur = $_POST['prenomJoueur'];
		$nomJoueur = $_POST['nomJoueur'];

		$data['individus'] = $this->individus_model->quickInscription($compte, $prenomJoueur, $nomJoueur);

		$this->load->view('admin/displayAvertissementSearch',$data);

	}

	public function getavertissements($idIndiv){
		$this->load->model('administration_model');
		$data['avertissements'] = $this->administration_model->getavertissements($idIndiv);

		$this->load->view('template/header', $data);
        $this->load->view('admin/sommaireAvertissements', $data);
        $this->load->view('template/footer',$data);
	}

	public function getSingleAvertissement($idAvertissement){
		$this->load->model('administration_model');
		$data['avertissement'] = $this->administration_model->getSingleAvertissement($idAvertissement);

		echo json_encode($data['avertissement']);
	}

	public function editAvertissement($idAvertissement){
		$this->load->model('administration_model');
		$data['avertissement'] = $this->administration_model->editAvertissement($idAvertissement);

		redirect("Administration/avertissements");
	}

	public function annulerAvertissement($idAvertissement){
		$this->load->model('administration_model');
		$data['avertissement'] = $this->administration_model->annulerAvertissement($idAvertissement);

		redirect("Administration/avertissements");
	}

	public function addAvertSearch_ajax(){
		$this->load->model('administration_model');
		$result = $this->administration_model->addAvertSearch_ajax();

		echo json_encode($result);
	}

	public function addAvertissement(){
		$this->load->model('administration_model');
		$result = $this->administration_model->addAvertissement();
		
		redirect("Administration/avertissements");
	}

	public function acces(){
		$data = array();

		$this->load->view('template/header', $data);
        $this->load->view('admin/acces', $data);
        $this->load->view('template/footer',$data);
	}

	public function searchIndividusAcces(){
		$data = array();

		$this->load->model('individus_model');

		$compte = $_POST['compte'];
		$prenomJoueur = $_POST['prenomJoueur'];
		$nomJoueur = $_POST['nomJoueur'];
		$pseudoJoueur = $_POST['pseudoJoueur'];
		//$acceslvl = $_POST['acceslvl'];

		$data['individus'] = $this->individus_model->quickInscription($compte, $prenomJoueur, $nomJoueur, $pseudoJoueur);

		$this->load->view('admin/displayAccesSearch',$data);

	}

	public function updateNiveauAcces(){
		$this->load->model('administration_model');

		$idIndividu = $_POST['idIndividu'];
		$newNiveauAcces = $_POST['newNiveauAcces'];

		$this->administration_model->updateNiveauAcces($idIndividu, $newNiveauAcces);

		$this->load->view('template/header', $data);
        $this->load->view('admin/acces', $data);
        $this->load->view('template/footer',$data);
	}

	public function getIndiv($idIndiv){

		$this->load->model('administration_model');
		$result = $this->administration_model->getIndiv($idIndiv);

		echo json_encode($result);

	}

	public function getBenevolat($idIndiv){

		$this->load->model('administration_model');
		$result = $this->administration_model->getBenevolat($idIndiv);

		echo json_encode($result);

	}

	public function saveBenevolat($idIndiv){
		$this->load->model('administration_model');
		$result = $this->administration_model->saveBenevolat($idIndiv);

	}

	public function camps(){
		$this->load->model('activites_model');
		$data['activite'] = $this->activites_model->getLastActivite();

		$this->load->model('administration_model');
		$data['campements'] = $this->administration_model->getCamps($data['activite']->Id);

		$this->load->view('template/header', $data);
        $this->load->view('admin/camps', $data);
        $this->load->view('template/footer',$data);
	}

	public function rateCampements(){
		$data = json_decode($_POST['sendData']);
		$rateCampement = array();
		$budgetCampement = array();


		foreach ($data as $row) {
			$rateCampement[] = [
				'IdActivite' => $row->idActiv,
				'CodeCampement' => $row->codeCampement,
				'Proprete' => $row->proprete,
				'Securite' => $row->securite,
				'Rangement' => $row->rangement,
				'Commentaires' => $row->commentaire,
				'DateInscription' => date('Y-m-d H:i:s', time()),
			];

			$this->load->model('administration_model');
			$total = intval($row->proprete) + intval($row->securite) + intval($row->rangement);

			if($total >= 2){
				$montant = ($total==3)?50:20;

				$budgetCampement[] = [
					'CodeCampement' => $row->codeCampement,
					'Raison' => 'Entretien - ' . $row->nomActivite,
					'Montant' => $montant,
					'IndServices' => 1,
					'DateInscription' => date('Y-m-d H:i:s', time())

				];
			};
		}

		$this->administration_model->rateCampement($rateCampement);

		if(count($budgetCampement) > 0){
			$this->administration_model->budgetCampement($budgetCampement);
		}
	}

	public function manageAcces(){
		$data = array();
		$this->load->model('administration_model');

		$data['acces'] = $this->administration_model->getAcces();

		$this->load->view('template/header', $data);
        $this->load->view('admin/manageAcces', $data);
        $this->load->view('template/footer',$data);
	}

	public function removeAcces(){
		$this->load->model('administration_model');

		$idIndiv = $_POST['idIndiv'];
		$acces = $_POST['acces'];

		$data['acces'] = $this->administration_model->removeAcces($idIndiv, $acces);
	}

	public function getIndividus(){
		$this->load->model('administration_model');

		$prenom = $_GET['prenom'];
		$nom = $_GET['nom'];

		echo json_encode( $this->administration_model->getIndividus($prenom, $nom) );

	}

	public function addAcces($idIndiv, $acces){
		$this->load->model('administration_model');

		$result = $this->administration_model->addAcces($idIndiv, $acces);

		echo json_encode($result);
	}
}

?>