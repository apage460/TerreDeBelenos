<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Campsite Controller v1.2 r0 ==			║
║	Implements campsite control logic.			║
║	Requires campsite services.				║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('services/campsite-services.class.php');


class CampsiteController
{

protected $Services;

public $Error;

	//--CONSTRUCTOR--
	public function __construct(&$inServices)
	{
		$this->Services = $inServices;
	}


	//--REQUEST CAMPSITE SERVICE--
	public function RequestCampsiteServices()
	{
		$this->Error = null;

		// Input checks
		// ACTIVITY IS MANDATORY
		if( !isset($_POST['activityid']) || !isset($_POST['activityname']) ) { 
			$this->Error = "L'activité n'a pas pu être déterminée!"; 
			return False;
		}
		// REQUESTED SERVICE IS MANDATORY
		if( !$_POST['water'] && !$_POST['charcoal'] ) { 
			$this->Error = "Vous n'avez choisi aucun service!"; 
			return False;
		}

		// Remove potentially harmful tags
		$lDetails = strip_tags($_POST['text']);
		$lAmount = ($_POST['water']*10) + ($_POST['charcoal']*25);

		// send mail
		$r = $this->Services->SaveServiceRequest( $_POST['activityid'], $_POST['water'], $_POST['charcoal'], $lAmount, $_POST['building'], $lDetails );
		$s = True; #$this->Services->SendServiceRequestMail( $_POST['activityname'], $_POST['service'], $lDetails );
		
		if($r && $s) { 
			return True; 
		}

		$this->Error = "Une erreur est survenue lors de la demande!";
		return False;
	}


} // END of CampsiteController class

?>
