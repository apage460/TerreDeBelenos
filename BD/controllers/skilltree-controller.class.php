<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Skill Tree Controller v1.2 r1 ==			║
║	Implements skill management control logic.		║
║	Requires player services.				║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('services/skilltree-services.class.php');

class SkillTreeController
{

protected $Services;

public $Error;

	//--CONSTRUCTOR--
	public function __construct(&$inServices)
	{
		$this->Services = $inServices;
	}


	//--SKILL PURCHASE LOGIC--
	public function BuySkill()
	{
		$this->Error = null;

		// Check if the posted informations are valid
		//-- SKILL CODE IS MANDATORY
		if(!$_POST['buy-skill']) { 
			$this->Error = "Le code de la compétence que vous avez tenté d'acheter ne s'est pas rendu au serveur! Contactez un DBA!"; 
			return False; 
		}


		// Call register service
		if( $this->Services->RegisterSkillPurchase( $_POST['buy-skill'] ) ) {
			// Check for skills with special situations
			    if( $_POST['buy-skill'] == 'PVSUP' ) { $this->Services->RegisterLifeMod( 1, 'Achat de PV' ); }
			elseif( $_POST['buy-skill'] == 'MAGIC1' || $_POST['buy-skill'] == 'MAGIS1' || $_POST['buy-skill'] == 'MAGIM1' ) 
				{ $this->Services->RegisterFreeSkill('SORTN1'); $this->Services->RegisterFreeSkill('SORTN1'); $this->Services->RegisterFreeSkill('SORTN1');}
			elseif( $_POST['buy-skill'] == 'ALCHIM1' ) 
				{ $this->Services->RegisterFreeSkill('RECETA1');}
			elseif( $_POST['buy-skill'] == 'HERBO1' ) 
				{ $this->Services->RegisterFreeSkill('RECETH1');}

			return True;
		}		

		$this->Error = "Une erreur est survenue lors de l'ajout de cette compétence...";
		return False;
	}


	//--SKILL CANCEL LOGIC--
	public function CancelSkill()
	{
		$this->Error = null;

		// Check if the posted informations are valid
		//-- SKILL CODE IS MANDATORY
		if( $_POST['refund-skill'] === False ) { 
			$this->Error = "No skill selected!"; 
			return False; 
		}


		// Call cancel service
		if( $this->Services->CancelSkillPurchase( $_POST['refund-skill'] ) ) {
			// Check for skills with special situations
			$lSkillCode = $this->Services->GetSkillTree()->GetObtainedSkills()[$_POST['refund-skill']]['code'];

			    if( $lSkillCode == 'PVSUP' ) { $this->Services->CancelLifeMod( 1, 'Achat de PV' ); }

			return True;
		}		

		$this->Error = "Une erreur est survenue lors du retrait de cette compétence...";
		return False;
	}


	//--TALENT PURCHASE LOGIC--
	public function BuyTalent()
	{
		$this->Error = null;

		// Check if the posted informations are valid
		//-- TALENT CODE IS MANDATORY
		if(!$_POST['buy-talent']) { 
			$this->Error = "Le code de la compétence que vous avez tenté d'acquérir ne s'est pas rendu au serveur! Contactez un DBA!"; 
			return False; 
		}


		// Call register service
		if( $this->Services->RegisterTalentPurchase( $_POST['buy-talent'] ) ) {
			// Check for skills with special situations
			// NONE FOR NOW

			return True;
		}		

		$this->Error = "Une erreur est survenue lors de l'ajout de cette compétence...";
		return False;
	}


	//--SKILL CANCEL LOGIC--
	public function CancelTalent()
	{
		$this->Error = null;

		// Check if the posted informations are valid
		//-- TALENT CODE IS MANDATORY
		if( $_POST['refund-talent'] === False ) { 
			$this->Error = "No skill selected!"; 
			return False; 
		}


		// Call cancel service
		if( $this->Services->CancelTalentPurchase( $_POST['refund-talent'] ) ) {
			// Check for skills with special situations
			// NONE FOR NOW

			return True;
		}		

		$this->Error = "Une erreur est survenue lors du retrait de cette compétence...";
		return False;
	}


} // END of PlayerController class

?>
