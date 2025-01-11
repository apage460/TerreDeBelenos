<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Courriel extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('courriel_model');
	}


	public function index()	{
		$data = array();

		$data['groupes'] = $this->courriel_model->getGroupes();
		if(isset($_GET['dest'])):
			$data['destinataires'] = explode(',', $_GET['dest']);
			array_pop($data['destinataires']);
		endif;

		$this->load->view('template/header', $data);
        $this->load->view('courriel/index', $data);
        $this->load->view('template/footer',$data);
		
	}

	public function search(){
		$prenom = $_POST['prenom'];
		$nom = $_POST['nom'];
		$groupe = $_POST['groupe'];
		$pseudo = $_POST['pseudo'];

		$data['results'] = $this->courriel_model->search($prenom, $nom, $groupe, $pseudo);
		if($groupe){
			$data['responsables'] = $this->courriel_model->getResponsables($groupe);
		}

		$this->load->view('courriel/searchresults',$data);

	}

	public function editEmail(){
		$data['destinataires'] = $_POST['destinataires'];

        $this->load->view('courriel/editEmail', $data);
	}

	public function sendEmail(){
		$destinataires = $_POST['destinataires'];
		$sujet = $_POST['sujet'];
		$texte = $_POST['texte'];
		$signature = $_POST['signature'];

		if($signature == 'organisation'){
			$sign = "L'Organisation";
			$from = 'organisation@terres-de-belenos.com';
		} elseif( $signature == 'quetes'){
			$sign = "L'Équipe des Quêtes";
			$from = 'quetes@terres-de-belenos.com';
		} else {
			$sign = "L'Organisation";
			$from = 'organisation@terres-de-belenos.com';
		}

		$this->load->library('email');

		$config['mailtype'] = 'html';

		$this->email->initialize($config);

		$this->email->from($from, 'Les Terres de Bélénos');
		$this->email->to($destinataires);

		$this->email->subject($sujet);



		$message = "<html><head></head><body>";
		$message .= '<div style="width:600px; margin: 0 auto;"><table cellpadding="0" cellspacing="0" border="0">';
		$message .= '<tr><td><img src="http://www.terres-de-belenos.com/wp-content/themes/bele/assets/img/banniere.jpg" alt="logo" style="height:200px; width:auto;"></td></tr>';
		$message .= '<tr><td style="padding-top: 25px; padding-bottom:15px">' .$texte .'</td></tr>';
		$message .= '<tr><td>-----<br><br>' .$sign .'</td></tr>';
        $message .= '</table>';
		$message .= "</div></body></html>";

		$this->email->message($message);

		$this->email->send();

		$this->load->view('courriel/emailSuccess');
	}

}

/* End of file Courriel.php */
/* Location: ./application/controllers/Courriel.php */