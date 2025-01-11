<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Noblesse extends CI_Controller {

	public function __construct()	{
		parent::__construct();
		$this->load->model('noblesse_model');
	}

	public function index()	{
		
	}

	public function listTitres(){
		$data = array();

		$data['titres'] = $this->noblesse_model->listTitres();

		$this->load->view('template/header', $data);
        $this->load->view('noblesse/listeTitres', $data);
        $this->load->view('template/footer',$data);
	}

}

/* End of file Noblesse.php */
/* Location: ./application/controllers/Noblesse.php */ ?>