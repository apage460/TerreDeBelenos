<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mecenat extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('mecenat_model');
	}

	public function index()	{
		$data = array();

		$this->load->view('template/header', $data);
        $this->load->view('mecenat/mecenat', $data);
        $this->load->view('template/footer',$data);
	}

	public function searchJoueurs(){
		$data = array('joueurs' => '');

		$data['joueurs'] = $this->mecenat_model->getJoueurs();

		$this->load->view('template/header', $data);
        $this->load->view('mecenat/mecenat', $data);
        $this->load->view('template/footer',$data);
	}

	public function mecenatParJoueur($id){
		$data['infoJoueur'] = $this->mecenat_model->getSingleJoueur($id);
		$data['infoMecenat'] = $this->mecenat_model->getMecenat($id);

		$this->load->view('template/header', $data);
        $this->load->view('mecenat/mecenatparjoueur', $data);
        $this->load->view('template/footer',$data);
	}

	public function addMecenat($id){
		$data = array();
		$this->load->library('form_validation');

		$this->form_validation->set_rules('projet', 'Projet', 'trim|required|alpha_numeric');
		$this->form_validation->set_rules('montant', 'Montant', 'trim|required|integer');

		if ($this->form_validation->run() == FALSE) {
			$data['success'] = false;

			$data['infoJoueur'] = $this->mecenat_model->getSingleJoueur($id);
			$data['infoMecenat'] = $this->mecenat_model->getMecenat($id);
			
            $this->load->view('template/header', $data);
	        $this->load->view('mecenat/mecenatparjoueur', $data);
	        $this->load->view('template/footer',$data);
        }
        else {
        	$data['success'] = true;
        	
	        $this->mecenat_model->addMecenat($id);

	        $data['infoJoueur'] = $this->mecenat_model->getSingleJoueur($id);
			$data['infoMecenat'] = $this->mecenat_model->getMecenat($id);

	        $this->load->view('template/header', $data);
	        $this->load->view('mecenat/mecenatparjoueur', $data);
	        $this->load->view('template/footer',$data);
        }
	}

	public function deleteMecenat($idIndiv, $idMecenat){
		$this->mecenat_model->deleteMecenat($idMecenat);

		$data['infoJoueur'] = $this->mecenat_model->getSingleJoueur($idIndiv);
		$data['infoMecenat'] = $this->mecenat_model->getMecenat($idIndiv);

		$this->load->view('template/header', $data);
        $this->load->view('mecenat/mecenatparjoueur', $data);
        $this->load->view('template/footer',$data);
	}

}

/* End of file Mecenat.php */
/* Location: ./application/controllers/Mecenat.php */ ?>