<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Consultations extends CI_Controller {

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
        $this->load->view('consultations', $data);
        $this->load->view('template/footer',$data);
	}

	public function searchPersonnages() {
		$data = array();

		$this->load->view('template/header', $data);
        $this->load->view('forms/searchPersonnages', $data);
        $this->load->view('template/footer',$data);
	}
}
?>