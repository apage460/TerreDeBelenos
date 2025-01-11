<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Individus extends CI_Controller {

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
    $this->load->view('home', $data);
    $this->load->view('template/footer',$data);
	}

	public function addDebt() {
		$data= array();

		$idPerso = $_POST['idPerso'];
		$commentaireDette = $_POST['commentaireDette'];
		$montantDette = $_POST['montantDette'];
		$idIndiv = $_POST['idIndiv']; 

		$this->load->model('individus_model');
		$data['activites'] = $this->individus_model->insertDette($idIndiv, $montantDette, $commentaireDette);

		if($returned){
			redirect('personnages/getSinglePersonnage/' .$idPerso .'/' .$idIndiv);
		}
	}

	public function arcanns(){
		$data = array();
		$this->load->view('template/header', $data);
    $this->load->view('individus/arcanns', $data);
    $this->load->view('template/footer',$data);
	}

	public function arcannSearchIndiv(){
		$this->load->model('inscriptions_model');
		$data['results'] = $this->inscriptions_model->searchIndiv();

		$this->load->view('template/header', $data);
    $this->load->view('individus/arcanns', $data);
    $this->load->view('template/footer',$data);
	}

	public function arcannSingleIndiv($idIndiv, $retrait = false){
		$this->load->model('individus_model');
		$data['user'] = $this->individus_model->getSingleIndividu($idIndiv);

		$data['retrait'] = $retrait;

		$data['qtyArcann'] = $this->individus_model->getQtyArcanns($idIndiv);


		$this->load->view('template/header', $data);
    $this->load->view('individus/arcannSingleIndiv', $data);
    $this->load->view('template/footer',$data);
	}

	public function initArcann2019(){
		
		/*$this->load->model('individus_model');
		$this->individus_model->initArcann2019();*/

		
		$data = array();
		$this->load->view('template/header', $data);
    $this->load->view('individus/arcanns', $data);
    $this->load->view('template/footer',$data);

	}

	public function retraitArcann($idIndiv){
		$this->load->model('individus_model');
		$this->individus_model->retraitArcann($idIndiv);

		redirect('/individus/arcannSingleIndiv/' .$idIndiv . '/' . true ,'refresh');
	}

}
