<?php 
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Activites extends CI_Controller {

		public function __construct() {
			parent::__construct();
			$this->load->model('activites_model');
		}
	
		public function index()	{
			$data = array();

			$data['activites'] = $this->activites_model->getLastActivites();
			$data['typeActivites'] = $this->activites_model->getTypeActivites();

			$this->load->view('template/header', $data);
	        $this->load->view('activites/activites', $data);
	        $this->load->view('template/footer',$data);
		}

		public function addActivite(){
			$data = array();
			$this->load->library('form_validation');

			$this->form_validation->set_rules('nom', 'Nom', 'trim|required');
			$this->form_validation->set_rules('description', 'Description', 'trim|required');
			$this->form_validation->set_rules('typeActivite', 'Type', 'trim|required');
			$this->form_validation->set_rules('dateDebut', 'Dade de Début', 'trim|required');
			$this->form_validation->set_rules('dateFin', 'Date de Fin', 'trim|required');

			if ($this->form_validation->run() == FALSE) {
				$data['success'] = false;

				$data['activites'] = $this->activites_model->getLastActivites();
				
                $this->load->view('template/header', $data);
		        $this->load->view('activites/activites', $data);
		        $this->load->view('template/footer',$data);
            }
            else {
            	$data['success'] = true;
            	
		        $this->activites_model->addActivite();

		        $data['activites'] = $this->activites_model->getLastActivites();

		        $this->load->view('template/header', $data);
		        $this->load->view('activites/activites', $data);
		        $this->load->view('template/footer',$data);
            }            
		}

		public function editActiviteForm($id){
			$data['activite'] = $this->activites_model->getSingleActivite($id);

			$this->load->view('template/header', $data);
	        $this->load->view('activites/editActiviteForm', $data);
	        $this->load->view('template/footer',$data);

		}

		public function editActivite($id){
			$data = array();
			$this->load->library('form_validation');

			$this->form_validation->set_rules('nom', 'Nom', 'trim|required');
			$this->form_validation->set_rules('description', 'Description', 'trim|required');
			$this->form_validation->set_rules('typeActivite', 'Type', 'trim|required');
			$this->form_validation->set_rules('dateDebut', 'Dade de Début', 'trim|required');
			$this->form_validation->set_rules('dateFin', 'Date de Fin', 'trim|required');

			if ($this->form_validation->run() == FALSE) {
				$data['success'] = false;

				$data['activite'] = $this->activites_model->getSingleActivite($id);
				
                $this->load->view('template/header', $data);
		        $this->load->view('activites/editActiviteForm', $data);
		        $this->load->view('template/footer',$data);
            }
            else {
            	$data['success'] = true;
            	
		        $this->activites_model->updateActivite($id);

		        $data['activites'] = $this->activites_model->getLastActivites();

		        $this->load->view('template/header', $data);
		        $this->load->view('activites/activites', $data);
		        $this->load->view('template/footer',$data);
            }
        }

        public function deleteActivite($id){
        	$this->activites_model->deleteActivite($id);

        	$data['activites'] = $this->activites_model->getLastActivites();

	        $this->load->view('template/header', $data);
	        $this->load->view('activites/activites', $data);
	        $this->load->view('template/footer',$data);
        }
	}
	
	/* End of file Activites.php */
	/* Location: ./application/controllers/Activites.php */
?>