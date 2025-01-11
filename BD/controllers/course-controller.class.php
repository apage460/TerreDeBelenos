<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Course Controller v1.2 r1 ==				║
║	Implements teachings' registration control logic.	║
║	Requires registration services.				║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('services/course-services.class.php');

class CourseController
{

protected $Services;

public $Error;

	//--CONSTRUCTOR--
	public function __construct(&$inServices)
	{
		$this->Services = $inServices;
	}


	//--PREPARE COURSE MANAGER'S DATA FOR NPC MASTER CHOICE--
	public function PrepareNPCMaster()
	{
		// Prepare data
		$index = $_POST['npc-index'];
		$lCharacter = $this->Services->GetCharacter();
		$lCharacter->SetUserName( $_SESSION['player']->GetFullName() );
		

		// Call selection function
		$this->Services->GetManager()->SelectNPCMaster($index);
		$this->Services->GetManager()->SetSelectedStudent($lCharacter);
		
		return True;
	}


	//--PREPARE COURSE MANAGER'S DATA FOR PC MASTER CHOICE--
	public function PreparePCMaster()
	{
		// Prepare data
		$index = $_POST['pc-index'];
		$lCharacter = $this->Services->GetCharacter();
		$lCharacter->SetUserName( $_SESSION['player']->GetFullName() );


		// Call selection function
		$this->Services->GetManager()->SelectPCMaster($index);
		$this->Services->GetManager()->SetSelectedStudent($lCharacter);
		

		// Verify that nobody's self-taught
		if( $_SESSION['player']->GetFullName() == $this->Services->GetManager()->GetSelectedMaster()->GetUserName() ) {
			$this->Error = "Vous ne pouvez pas choisir un personnage qui vous appartient comme maître!";
			return False;
		}
		
		return True;
	}


	//--PREPARE COURSE MANAGER'S DATA FOR PC MASTER CHOICE--
	public function PreparePCStudent()
	{
		// Prepare data
		$lCharacter = $this->Services->GetCharacter();
		$lMaster = $this->Services->GetManager()->GetMasterFromID( $lCharacter->GetID() );

		$lStudentID = $_POST['teach-student'];
		$lStudent =  $this->Services->GetStudentCharacter( $lStudentID );
		

		// Validate master and student exists as such
		if( $lMaster === False ) {
			$this->Error = "Vous ne faites pas partie de la liste des maîtres valides! Vous devez d'abord faire approuver au moins un plan de cours.";
			return False;
		}
		if( $lStudent === False ) {
			$this->Error = "Une erreur imprévue s'est produite! Les données du personnage élève n'ont pas pu être rappatriées. Contactez les Administrateurs à TI@Terres-de-Belenos.com!";
			return False;
		}
		

		// Verify that nobody's self-taught
		if( $lMaster->GetUserName() == $lStudent->GetUserName() ) {
			$this->Error = "Vous ne pouvez pas choisir un personnage qui vous appartient comme élève!";
			return False;
		}


		// Call selection function
		$this->Services->GetManager()->SetSelectedMaster($lMaster);
		$this->Services->GetManager()->SetSelectedStudent($lStudent);
		
		return True;
	}


} // END of CourseController class

?>
