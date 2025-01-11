<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Player Controller v1.2 r8 ==				║
║	Implements additional management control logic.		║
║	Requires player services.				║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('services/player-services.class.php');
include_once('controllers/user-controller.class.php');

class PlayerController extends UserController
{

	//--CONSTRUCTOR--
	public function __construct(&$inServices)
	{
		parent::__construct($inServices);
	}

	//--SAVE SQUIRING IN THE DATABASE--
	public function RegisterTutoringGroupMember()
	{
		$this->Error = null;

		// No validation on input required


		// Call the registration function
		$r = $this->Services->RegisterTutoringGroupMember();
		
		if($r) { return True; }

		$this->Error = "Une erreur est survenue lors de l'enregistrement!";
		return False;
	}


	//--RECEIVE FREE ACTIVITY FOR VOLUNTEERING--
	public function AddVolunteerFreeActivity()
	{
		$this->Error = null;

		// Check if user has sufficient points
		if( $this->Services->GetUser()->GetVolunteeringPointCount() < 2 ) {
			$this->Error = "Vous n'avez pas assez de points pour cette récompense.";
			return False;
		}


		// Call the registration function
		if( $this->Services->AddFreeActivityVouchers() && $this->Services->TakeVolunteeringPoints('Entrée gratuite', 2) ) { return True; }

		$this->Error = "Une erreur est survenue lors de l'enregistrement!";
		return False;
	}


	//--RECEIVE FREE KID ENTRIES FOR VOLUNTEERING--
	public function AddVolunteerFreeKidEntries()
	{
		$this->Error = null;

		// Check if user has sufficient points
		if( $this->Services->GetUser()->GetVolunteeringPointCount() < 1 ) {
			$this->Error = "Vous n'avez pas assez de points pour cette récompense.";
			return False;
		}


		// Call the registration function
		if( $this->Services->AddFreeKidVouchers(2) && $this->Services->TakeVolunteeringPoints('Entrées gratuites pour enfant', 1) ) { return True; }

		$this->Error = "Une erreur est survenue lors de l'enregistrement!";
		return False;
	}


} // END of PlayerController class

?>
