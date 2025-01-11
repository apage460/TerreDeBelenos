<?php 
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Passes extends CI_Controller {

		public function __construct() {
			parent::__construct();
			$this->load->model('passes_model');
		}
	
		public function index()	{
			$data = array();

			$data['passes'] = $this->passes_model->getValidPasses();

			$this->load->view('template/header', $data);
	        $this->load->view('passes/passes', $data);
	        $this->load->view('template/footer',$data);
		}

		public function addPasse(){
			$data = array();
			$this->load->library('form_validation');

			$this->form_validation->set_rules('nom', 'Nom', 'trim|required');
			$this->form_validation->set_rules('description', 'Description', 'trim|required');
			$this->form_validation->set_rules('prix', 'Prix', 'trim|required|numeric');
			$this->form_validation->set_rules('dateDebut', 'Dade de Début', 'trim|required');
			$this->form_validation->set_rules('dateFin', 'Date de Fin', 'trim|required');

			if ($this->form_validation->run() == FALSE) {
				$data['success'] = false;

				$data['passes'] = $this->passes_model->getValidPasses();
				
                $this->load->view('template/header', $data);
		        $this->load->view('passes/passes', $data);
		        $this->load->view('template/footer',$data);
            }
            else {
            	$data['success'] = true;
            	
		        $this->passes_model->addPasse();

		        $data['passes'] = $this->passes_model->getValidPasses();

		        $this->load->view('template/header', $data);
		        $this->load->view('passes/passes', $data);
		        $this->load->view('template/footer',$data);
            }
		}

		public function editPasseForm($idPasse) {

			$data['passe'] = $this->passes_model->getSinglePasse($idPasse);

			$data['accesParPasse'] = $this->passes_model->activitesParPasses($idPasse);

			$data['activitesReliees'] = $this->passes_model->activitesReliees($idPasse);

			$this->load->model('activites_model');
			$data['activites'] = $this->activites_model->getLastActivites();

			$this->load->view('template/header', $data);
	        $this->load->view('passes/editPasseForm', $data);
	        $this->load->view('template/footer',$data);

		}

		public function editPasse($idPasse){
			$data = array();
			$this->load->library('form_validation');

			$this->form_validation->set_rules('nom', 'Nom', 'trim|required');
			$this->form_validation->set_rules('description', 'Description', 'trim|required');
			$this->form_validation->set_rules('prix', 'Prix', 'trim|required|numeric');
			$this->form_validation->set_rules('dateDebut', 'Dade de Début', 'trim|required');
			$this->form_validation->set_rules('dateFin', 'Date de Fin', 'trim|required');

			if ($this->form_validation->run() == FALSE) {
				$data['success'] = false;

				$data['passe'] = $this->passes_model->getSinglePasse($idPasse);

				$data['accesParPasse'] = $this->passes_model->activitesParPasses($idPasse);

				$data['activitesReliees'] = $this->passes_model->activitesReliees($idPasse);

				$this->load->model('activites_model');
				$data['activites'] = $this->activites_model->getLastActivites();

				$this->load->view('template/header', $data);
		        $this->load->view('passes/editPasseForm', $data);
		        $this->load->view('template/footer',$data);
            }
            else {
            	$data['success'] = true;

            	$this->passes_model->updatePasse($idPasse);
            	
		        $data['passe'] = $this->passes_model->getSinglePasse($idPasse);

				$data['accesParPasse'] = $this->passes_model->activitesParPasses($idPasse);

				$data['activitesReliees'] = $this->passes_model->activitesReliees($idPasse);

				$this->load->model('activites_model');
				$data['activites'] = $this->activites_model->getLastActivites();

				$this->load->view('template/header', $data);
		        $this->load->view('passes/editPasseForm', $data);
		        $this->load->view('template/footer',$data);
            }
		}

		public function linkPasses($idPasse){
			$this->passes_model->linkPasses($idPasse);
            	
	        $data['passe'] = $this->passes_model->getSinglePasse($idPasse);

			$data['accesParPasse'] = $this->passes_model->activitesParPasses($idPasse);

			$data['activitesReliees'] = $this->passes_model->activitesReliees($idPasse);

			$this->load->model('activites_model');
			$data['activites'] = $this->activites_model->getLastActivites();

			$this->load->view('template/header', $data);
	        $this->load->view('passes/editPasseForm', $data);
	        $this->load->view('template/footer',$data);
		}

		public function deleteActiviteReliee($idPasse, $idActivite){
			$this->passes_model->deleteActiviteReliee($idPasse, $idActivite);

			$data['passe'] = $this->passes_model->getSinglePasse($idPasse);

			$data['accesParPasse'] = $this->passes_model->activitesParPasses($idPasse);

			$data['activitesReliees'] = $this->passes_model->activitesReliees($idPasse);

			$this->load->model('activites_model');
			$data['activites'] = $this->activites_model->getLastActivites();

			$this->load->view('template/header', $data);
	        $this->load->view('passes/editPasseForm', $data);
	        $this->load->view('template/footer',$data);
		}

		public function passesParJoueur(){
			$data = array('joueurs' => '');

			$data['passes'] = $this->passes_model->getValidPasses();

			$this->load->view('template/header', $data);
	        $this->load->view('passes/passesParJoueur', $data);
	        $this->load->view('template/footer',$data);
		}

		public function searchJoueurs(){
			$data = array('joueurs' => '');

			$data['passes'] = $this->passes_model->getValidPasses();
			$data['joueurs'] = $this->passes_model->getJoueurs();

			$this->load->view('template/header', $data);
	        $this->load->view('passes/passesParJoueur', $data);
	        $this->load->view('template/footer',$data);
		}

		public function searchParPasse(){
			$data = array('joueurs' => '');

			$data['passes'] = $this->passes_model->getValidPasses();
			$data['joueurs'] = $this->passes_model->searchParPasse();

			$this->load->view('template/header', $data);
	        $this->load->view('passes/passesParJoueur', $data);
	        $this->load->view('template/footer',$data);
		}

		public function linkPassPlayer($idJoueur){
			$this->passes_model->linkPassPlayer($idJoueur);

			$data['passes'] = $this->passes_model->getValidPasses();
			$data['joueurs'] = $this->passes_model->getJoueurs();

			$this->load->view('template/header', $data);
	        $this->load->view('passes/passesParJoueur', $data);
	        $this->load->view('template/footer',$data);
		}

		public function unlinkPassPlayer($idJoueur, $idPasse){
			$this->passes_model->unlinkPassPlayer($idJoueur, $idPasse);

			$data['passes'] = $this->passes_model->getValidPasses();
			$data['joueurs'] = $this->passes_model->getJoueurs();

			$this->load->view('template/header', $data);
	        $this->load->view('passes/passesParJoueur', $data);
	        $this->load->view('template/footer',$data);
		}

		public function relinkPassPlayer($idJoueur, $idPasse){
			$this->passes_model->relinkPassPlayer($idJoueur, $idPasse);

			$data['passes'] = $this->passes_model->getValidPasses();
			$data['joueurs'] = $this->passes_model->getJoueurs();

			$this->load->view('template/header', $data);
	        $this->load->view('passes/passesParJoueur', $data);
	        $this->load->view('template/footer',$data);
		}

		public function downloadPassList($idPasse){
			// output headers so that the file is downloaded rather than displayed
			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename=ListedeJoueur.csv');

			// create a file pointer connected to the output stream
			$output = fopen('php://output', 'w');

			// output the column headings
			fputcsv($output, array('Prénom', 'Nom', 'Compte'));

			$vRows = $this->passes_model->downloadPassList($idPasse);

			foreach ($vRows as $row) {
				fputcsv($output, $row);
			}

		}
	}
	
	/* End of file Activites.php */
	/* Location: ./application/controllers/Activites.php */
?>