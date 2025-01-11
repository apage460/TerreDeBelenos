<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quetes extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('quetes_model');
	}


	public function viewQuests($printPage = null){

		$idUser = null;
		if($_SESSION['infoUser']->NiveauAcces < 5){
			$idUser = $_SESSION['infoUser']->Id;
		}

		$data['quetes'] = $this->quetes_model->viewQuests($idUser);

		$this->load->model('activites_model');

		$data['nextGNName'] = $this->activites_model->getNextGNName();

		//Get upcoming quests to print
		$data['quetesToPrint'] = glob('/home/terresdebelenos/terres-de-belenos.com/BD/uploads/Quetes/' .$data['nextGNName']->Nom .'/*.pdf');

		$this->load->view('template/header', $data);
		$this->load->view('quetes/view-quests', $data);
		$this->load->view('template/footer',$data);
	}

	public function printQuetes($printPage = null){
		$erreurFichier = array();

		require_once('/home/terresdebelenos/terres-de-belenos.com/bdmvc/fpdf/fpdf.php');
		require_once('/home/terresdebelenos/terres-de-belenos.com/bdmvc/fpdf/fdpi/autoload.php');
		require_once('/home/terresdebelenos/terres-de-belenos.com/bdmvc/fpdf/fdpi/Fpdi.php');

		$pdf = new \setasign\Fpdi\Fpdi();

		foreach($_POST['quetes'] as $key => $value){
			$value = "/home/terresdebelenos/terres-de-belenos.com/BD/uploads/Quetes/" . $value;
			try{
				$pdf->setSourceFile($value);
			} catch(Exception $e){
				array_push($erreurFichier, $value);
				continue;
			}
			$pageCount = $pdf->setSourceFile($value);

			$arrValue = explode('/', $value);

			$activite = utf8_decode(array_slice($arrValue, -2, 1)[0]);

			for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
		        // import a page
		        $templateId = $pdf->importPage($pageNo);
		        // get the size of the imported page
		        $size = $pdf->getTemplateSize($templateId);

		        // add a page with the same orientation and size
		        $pdf->AddPage($size['orientation'], $size);

		        // use the imported page
		        $pdf->useTemplate($templateId);
		    }

		}

		if(count($erreurFichier) > 0){
			$pdf->AddPage();
			$pdf->SetXY(5, 20);
			$pdf->SetFontSize(12);
			$pdf->SetFont('Courier', 'B');
			$pdf->Cell(0, 10,  utf8_decode('Fichier(s) en erreur :'), 0, 1);

			$pdf->SetFontSize(8);
			$pdf->SetFont('','');
			foreach ($erreurFichier as $erreur) {
				$pdf->Cell(0, 10,  '- ' . utf8_decode($erreur), 0, 1);
			}
		}
		$pdf->Output('D', 'Quetes - ' . $activite . ' - page' .$printPage .'.pdf');

		redirect("/Quetes/viewQuests/" .$printPage);

	}	

}

/* End of file Quetes.php */
/* Location: ./application/controllers/Quetes.php */ ?>