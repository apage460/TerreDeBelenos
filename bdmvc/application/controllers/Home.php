<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

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

		if(!isset($_GET['id'])){

			$this->load->view('template/header', $data);
	        $this->load->view('errors/error_login', $data);
	        $this->load->view('template/footer',$data);
	    } 
	    else{

	    	$idUser = $_GET['id'];

	    	$this->load->model('home_model');
			$infoUser = $this->home_model->verifyUser($idUser);

			if($infoUser->NiveauAcces < 2 && $_GET['id'] != 129 ){
				$this->load->view('template/header', $data);
	        	$this->load->view('errors/error_login', $data);
	        	$this->load->view('template/footer',$data);

			}
			else{
				if(isset($_GET['univers']) ){
					$univers = $_GET['univers'];
				} else{
					$univers = 'BELE';
				}

				$vAcces = $this->home_model->getAcces($idUser, $univers);

				if(!empty($vAcces)){
					$userAcces = array();
					foreach ($vAcces as $acces) {
						array_push($userAcces, $acces->Acces);
					}
				}

				$this->session->set_userdata('infoUser',$infoUser);
				$this->session->set_userdata('infoUnivers',$univers);
				$this->session->set_userdata('userAcces',$userAcces);
				
				$data['presencesCount'] = $this->home_model->getPresenceCount();
				/*$data['classesCount'] = $this->home_model->getClassesCount();
				$data['racesCount'] = $this->home_model->getRacesCount();
				$data['religionCount'] = $this->home_model->getReligionCount();*/

				/* NEW STATS*/
				if($infoUser->NiveauAcces > 3 || $_SESSION['codeUnivers'] == 'BELEJR'){
					$data['statsActivites'] = $this->home_model->getStatsActivites();
					$data['statsPasses'] = $this->home_model->getStatsPasses();
				}


		    	$this->load->view('template/header', $data);
		        $this->load->view('home', $data);
		        $this->load->view('template/footer',$data);
	    	}
	    }
	}

	public function switchChart(){
		$idActiv = $_POST['IdActiv'];

		$this->load->model('home_model');
		$data['presencesCount'] = $this->home_model->getPresenceCount($idActiv);
		$data['classesCount'] = $this->home_model->getClassesCount($idActiv);
		$data['racesCount'] = $this->home_model->getRacesCount($idActiv);
		$data['religionCount'] = $this->home_model->getReligionCount($idActiv);

		$this->load->view('ajax/switchChart',$data);
	}
}
