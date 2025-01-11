<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Skill Tree Services v1.2 r1 ==			║
║	Manages lists from a common skill tree.			║
║	Non-serializable. Requires DAL. Uses MySQL queries.	║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('services/database.class.php'); 		// Data Access Layer
include_once('models/character.class.php'); 		// Character definition
include_once('models/skilltree.class.php'); 		// Skill Tree definition

class SkillTreeServices
{

protected $DAL;
protected $Character;
protected $SkillTree;

public $Error;

	//--CONSTRUCTOR--
	public function __construct($inDataAccessLayer, $inCharacter =NULL, $inTree =NULL)
	{
		$this->DAL = $inDataAccessLayer;

		if( isset($inCharacter) ) {$this->Character = $inCharacter;}
		if( isset($inTree) ) {$this->SkillTree = $inTree;}
	}


	//--GET/SET FUNCTIONS--
	public function GetCharacter() { return $this->Character; }
	public function SetCharacter($inCharacter) { $this->Character = $inCharacter; }

	public function GetSkillTree() { return $this->SkillTree; }
	public function SetSkillTree($inTree) { $this->SkillTree = $inTree; }


	//--BUILD TREES--
	public function BuildTrees()
	{
		$this->BuildSkillTree();
		$this->BuildTalentTree();
	}


	//--BUILD TREE--
	public function BuildSkillTree()
	{
		// Check if race and class are defined
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }
		if( !$this->Character->GetRace() || !$this->Character->GetClass() ) { $this->Error = "No race or class defined!"; return False; }


		// Prepare data
		$lCharacterXP = $this->Character->GetTotalExperience();
		$lCharacterLevel = $this->Character->GetLevel();
		$lCharacterReligion = $this->Character->GetReligionCode();

		$lObtainedTalents = $this->Character->GetTalents();
		$lObtainedSkills = $this->Character->GetSkills();
			$this->SkillTree->SetObtainedSkills( $lObtainedSkills );
			$lObtainedSkillCodes = array_column($lObtainedSkills, 'code');
			$lSkillCodeCounts = array_count_values($lObtainedSkillCodes);

		$lHasMagic = False;
			    if( array_search('MAGIA1', $lObtainedSkillCodes) !== False ) { $lHasMagic = True; }
			elseif( array_search('MAGIC1', $lObtainedSkillCodes) !== False ) { $lHasMagic = True; }
			elseif( array_search('MAGIM1', $lObtainedSkillCodes) !== False ) { $lHasMagic = True; }
			elseif( array_search('MAGIS1', $lObtainedSkillCodes) !== False ) { $lHasMagic = True; }

		$lHasSuperiorMagic = False;
			    if( array_search('HMAGIA', $lObtainedSkillCodes) !== False ) { $lHasSuperiorMagic = True; }
			    if( array_search('HMAGIC', $lObtainedSkillCodes) !== False ) { $lHasSuperiorMagic = True; }

		$this->GetTeachings();


		// Ask the database for the skill tree
		$lQuery = 	"SELECT creg.Code, creg.Nom, creg.Niveau, creg.Categorie, ajc.Multiplicateur, (ajc.Multiplicateur*ccr.CoutXP) AS 'CoutMultiplie', 
					creg.CodeCompPrerequise AS 'Prerequis', creg.CodeCompRemplacee AS 'Remplace', creg.Usages, creg.Achats
				 FROM db_pilot.competences_regulieres creg
				    INNER JOIN db_pilot.ajustements_categorie ajc ON creg.Categorie = ajc.Categorie
						JOIN db_pilot.classes clas ON ajc.CodeClasse = clas.Code
				    INNER JOIN db_pilot.cout_competences_reg ccr  ON creg.Code = ccr.CodeCompReg
						JOIN db_pilot.races rac ON ccr.CodeRace = rac.Code
				 WHERE creg.CodeEtat = 'ACTIF'
				   AND clas.Nom = :class 
				   AND rac.Nom = :race
				 ORDER BY creg.Nom ASC";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":class", $this->Character->GetClass(), PDO::PARAM_STR);
			$this->DAL->Bind(":race", $this->Character->GetRace(), PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();


		// Build the skill tree
		$lSkillTree = array();
		foreach($r as $i => $skill) {

			// Calculate final cost
			$cost = $skill['CoutMultiplie'];
			$trained = array_search( $skill['Code'], $this->SkillTree->GetTeachings() );
			if( $skill['Niveau'] > 1 ) { $cost *= 0.5; }					// If second level or more... half the cost
			if( $trained !== False ) { $cost *= 0.8; } 					// If character has received teaching for this skill... 20% bonus!

			// Handle skill attributes
			$lSkillTree[$i]['code'] = $skill['Code'];
			$lSkillTree[$i]['name'] = $skill['Nom'];
			$lSkillTree[$i]['level'] = $skill['Niveau'];
			$lSkillTree[$i]['category'] = $skill['Categorie'];
			$lSkillTree[$i]['adjustment'] = $skill['Multiplicateur'];
			$lSkillTree[$i]['cost'] = ceil($cost);
			$lSkillTree[$i]['prerequisites'] = explode( ";", $skill['Prerequis'] );
			$lSkillTree[$i]['replace'] = $skill['Remplace'];
			$lSkillTree[$i]['uses'] = $skill['Usages'];

			// Handle max purchases for Rats and others
			if( $this->Character->GetRaceCode() == 'HRAT' && $skill['Achats'] == 6 ) {
				$lSkillTree[$i]['maxpurchases'] = 3;
			}
			else { $lSkillTree[$i]['maxpurchases'] = $skill['Achats']; }
			

			if( array_search( $skill['Code'], $lObtainedSkillCodes ) !== False ) 
				{ $lSkillTree[$i]['obtained'] = $lSkillCodeCounts[ $skill['Code'] ]; }
			else 	{ $lSkillTree[$i]['obtained'] = 0; }

			if( $trained === False ) 
				{ $lSkillTree[$i]['trained'] = False; }
			else 	{ $lSkillTree[$i]['trained'] = True; }

			$lSkillTree[$i]['buyable'] = True; $lSkillTree[$i]['reason'] = null;
				// Skill is already fully obtained
			if( $lSkillTree[$i]['obtained'] >= $lSkillTree[$i]['maxpurchases'] ) { $lSkillTree[$i]['buyable'] = False; $lSkillTree[$i]['reason'] = "Maximum d'achats atteint"; }
				// Character's level is too low
			elseif( $skill['Niveau'] > $lCharacterLevel ) { $lSkillTree[$i]['buyable'] = False; $lSkillTree[$i]['reason'] = "Niveau de compétence trop élevé"; }
				// Golgoth, Chaos and Ademos cannot have clerical magic
			elseif( ($lCharacterReligion == 'GOLGOTH' || $lCharacterReligion == 'CHAOS' || $lCharacterReligion == 'ADEMOS') && substr($skill['Code'], 0, 5) == 'MAGIC' ) { $lSkillTree[$i]['buyable'] = False; $lSkillTree[$i]['reason'] = "Requiert un dieu actif"; }
				// Character already has magic
			elseif( $lHasMagic && ($skill['Code'] == 'MAGIA1' || $skill['Code'] == 'MAGIC1' || $skill['Code'] == 'MAGIM1' || $skill['Code'] == 'MAGIS1') ) { $lSkillTree[$i]['buyable'] = False; $lSkillTree[$i]['reason'] = "Un seul type de magie permis"; }
				# High magic prereq. Check if necessary
			elseif( !$lHasSuperiorMagic && ($skill['Code'] == 'MAGIA6' || $skill['Code'] == 'MAGIC6' || $skill['Code'] == 'MAGIM6' || $skill['Code'] == 'MAGIS6') ) { $lSkillTree[$i]['buyable'] = False; $lSkillTree[$i]['reason'] = "Requiert haute magie"; }
				// Prerequisites check
			elseif( $skill['Prerequis'] ) {

				$lPrereqMet = False;
				//$lOnlyOneLevel = True;

				// Check if prereq is obtained and is not on the 'Level up' list
				foreach( $lSkillTree[$i]['prerequisites'] as $option) {
					$index = array_search( $option, $lObtainedSkillCodes );
					if( $index !== False ) { $lPrereqMet = True; }
				}

				$lSkillTree[$i]['buyable'] = $lPrereqMet; //&& $lOnlyOneLevel;
					if( !$lPrereqMet ) { $lSkillTree[$i]['reason'] = "Prérequis manquant"; }
			}

			if( $lSkillTree[$i]['cost'] <= $lCharacterXP ) { $lSkillTree[$i]['affordable'] = True; }
			else { $lSkillTree[$i]['affordable'] = False; }
		}

		$this->SkillTree->SetSkillTree( $lSkillTree );
		return True;;
	}


	//--BUILD TREE--
	public function BuildTalentTree()
	{
		// Check if race and class are defined
		if( !isset($this->Character) ) { $this->Error = "No character!"; return False; }
		if( !$this->Character->GetRace() || !$this->Character->GetReligionCode() ) { $this->Error = "No race or religion defined!"; return False; }


		// Prepare data
		$lCharacterCredits = $this->Character->GetQuestCredits();
			$this->SkillTree->SetObtainedCredits( $lCharacterCredits );

		$lObtainedTalents = $this->Character->GetTalents();
			$this->SkillTree->SetObtainedTalents( $lObtainedTalents );
			$lObtainedTalentCodes = array_column($lObtainedTalents, 'code');
		$lObtainedSkills = $this->Character->GetSkills();
			$this->SkillTree->SetObtainedSkills( $lObtainedSkills );
			$lObtainedSkillCodes = array_column($lObtainedSkills, 'code');
			$lSkillCodeCounts = array_count_values($lObtainedSkillCodes);

		$lIsChaosDisciple = False;
			    if( $this->Character->GetReligionCode() == 'AMAIRA'  || 
				$this->Character->GetReligionCode() == 'CHAOS'   ||
				$this->Character->GetReligionCode() == 'DAGOTH'  ||
				$this->Character->GetReligionCode() == 'GODTAKK' ||
				$this->Character->GetReligionCode() == 'KAALKH'  ||
				$this->Character->GetReligionCode() == 'KHALII'  ||
				$this->Character->GetReligionCode() == 'NOCTAVE' ||
				$this->Character->GetReligionCode() == 'OTTOKOM' ||
				$this->Character->GetReligionCode() == 'ESPRITS' ||
				$this->Character->GetReligionCode() == 'TOYASH'  ) { $lIsChaosDisciple = True; }

		$lCanBeScavenger = False;
			    if( $this->Character->GetRaceCode() == 'HUMAIN'  ||
			    	$this->Character->GetRaceCode() == 'HLEZARD' ||
			    	$this->Character->GetRaceCode() == 'ORC'     ||
			    	$this->Character->GetRaceCode() == 'GOBELIN' ||
			    	$this->Character->GetRaceCode() == 'HRAT'    ) { $lCanBeScavenger = True; }


		// Ask the database for the skill tree
		$lQuery = 	"SELECT cspec.Code, cspec.Nom, cspec.Type, opt.NombreGN AS CoutCredits, cspec.CodePrerequis1, cspec.CodePrerequis2
				 FROM db_pilot.competences_speciales cspec
    					INNER JOIN db_pilot.options_quetes opt ON cspec.Type = opt.Code
				 WHERE cspec.Type IN ('MINEURE','MAJEURE')
				   AND cspec.CodeEtat = 'ACTIF'
				 ORDER BY cspec.Nom ASC";

		$this->DAL->SetQuery($lQuery);
		$r = $this->DAL->FetchResult();


		// Build the talent tree
		$lTalentTree = array();
		foreach($r as $i => $talent) {

			$lSpecificCredits = 0;
				if( isset($lCharacterCredits[$talent['Code']]) ) { $lSpecificCredits = $lCharacterCredits[$talent['Code']]; }

			// Handle skill attributes
			$lTalentTree[$i]['code'] = $talent['Code'];
			$lTalentTree[$i]['name'] = $talent['Nom'];
			$lTalentTree[$i]['type'] = $talent['Type'];
			$lTalentTree[$i]['cost'] = $talent['CoutCredits'];
			$lTalentTree[$i]['prerequisites1'] = explode( ";", $talent['CodePrerequis1'] );
			$lTalentTree[$i]['prerequisites2'] = $talent['CodePrerequis2'];

			if( array_search( $talent['Code'], $lObtainedTalentCodes ) !== False ) 
				{ $lTalentTree[$i]['obtained'] = 1; }
			else 	{ $lTalentTree[$i]['obtained'] = 0; }


			$lTalentTree[$i]['buyable'] = True; $lTalentTree[$i]['reason'] = null;
			// Cannot reacquire acquired talents
			if( $lTalentTree[$i]['obtained'] ) { $lTalentTree[$i]['buyable'] = False; $lTalentTree[$i]['reason'] = "Déjà acquis."; }
			// Exception - Scavenger talent
			elseif( $talent['Code'] == 'CHAROGN' && !$lCanBeScavenger ) { $lTalentTree[$i]['buyable'] = False; $lTalentTree[$i]['reason'] = "Votre race ne peut acquérir cette compétence."; }
			// Exception - Sacrificer
			elseif( $talent['Code'] == 'SACRIFI' && !$lIsChaosDisciple ) { $lTalentTree[$i]['buyable'] = False; $lTalentTree[$i]['reason'] = "Votre religion ne peut acquérir cette compétence."; }
			// Premier prérequis (liste)
			elseif( $talent['CodePrerequis1'] ) {

				$lPrereq1Met = False;
				$lPrereq2Met = False;

				// Check if prereq 1 is obtained
				foreach( $lTalentTree[$i]['prerequisites1'] as $option) {
					$lSkillIndex1 = array_search( $option, $lObtainedSkillCodes );
					$lTalentIndex1 = array_search( $option, $lObtainedTalentCodes );
					if( $lSkillIndex1 !== False || $lTalentIndex1 !== False ) { 
						$lPrereq1Met = True;
					}
				}

				// Check if prereq 2 is obtained
				if( !$talent['CodePrerequis2'] ) { $lPrereq2Met = True; }
				else{
					$lSkillIndex2 = array_search( $talent['CodePrerequis2'], $lObtainedSkillCodes );
					$lTalentIndex2 = array_search( $talent['CodePrerequis2'], $lObtainedTalentCodes );
					if( $lSkillIndex2 !== False || $lTalentIndex2 !== False ) { 
						$lPrereq2Met = True;
					}
				}

				$lTalentTree[$i]['buyable'] = $lPrereq1Met && $lPrereq2Met;
					if( !$lTalentTree[$i]['buyable'] ) { $lTalentTree[$i]['reason'] = "Prérequis manquant"; }
			}

			if( $lTalentTree[$i]['cost'] <= ($lCharacterCredits['UNIV'] + $lSpecificCredits) ) 
				{ $lTalentTree[$i]['affordable'] = True; }
			else { $lTalentTree[$i]['affordable'] = False; }
		}

		$this->SkillTree->SetTalentTree( $lTalentTree );
		return True;
	}


	//--GET TEACHINGS ASSOCIATED WITH STORED CHARACTER ID--
	public function GetTeachings()
	{
		// Check if race and class are defined
		if( !$this->Character->GetID() ) { $this->Error = "No real character selected!"; return False; }


		// Ask the database...
		$lQuery = 	"SELECT ens.CodeCompetence 
				 FROM db_perso.enseignements ens
				 WHERE ens.CodeEtat = 'ACTIF'
				   AND ens.IdEtudiant = :characterid";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
		$r = $this->DAL->FetchResult();


		$lTeachings = array();
		foreach( $r as $teaching ) { $lTeachings[] = $teaching['CodeCompetence']; }
		
		$this->SkillTree->SetTeachings( $lTeachings );
		return True;
	}


	//--REGISTER A PURCHASED SKILL--
	public function RegisterSkillPurchase( $inSkillCode )
	{
		// Check if race and class are defined
		if( !isset( $this->Character ) ) { $this->Error = "No character set!"; return False; }
		if( !isset( $this->SkillTree ) ) { $this->Error = "No skill tree set!"; return False; }

		// Prepare data
		$lTree = $this->SkillTree->GetSkillTree();
		$index = $this->SkillTree->GetSkillIndex( $inSkillCode );
		$lAcquisitionCode = 'NORMALE';
			if( $lTree[$index]['trained'] ) { $lAcquisitionCode = 'RABAIS'; }


		// Ask the database...
		$lQuery = 	"INSERT INTO db_perso.experience (IdPersonnage, Raison, XP, DateInscription)
				 VALUES (:characterid, :reason, -:xpcost, sysdate() );

				 INSERT INTO db_perso.competences_acquises (IdPersonnage, CodeCompetence, Type, Usages, CoutXP, CodeAcquisition, CodeEtat, DateCreation)
				 VALUES (:characterid, :skillcode, 'REG', :uses, :xpcost, :acquisitioncode, 'PRLVL', sysdate() );

				 UPDATE db_perso.competences_acquises cac
				 SET cac.CodeEtat = 'REMPL'
				 WHERE cac.CodeEtat = 'ACTIF'
				   AND cac.CodeCompetence = :replaced
				   AND cac.IdPersonnage = :characterid;

				 UPDATE db_perso.enseignements ens
				 SET ens.CodeEtat = 'INACT'
				 WHERE ens.CodeEtat = 'ACTIF'
				   AND ens.IdEtudiant = :characterid
				   AND ens.CodeCompetence = :skillcode
				 ORDER BY ens.IdActivite ASC LIMIT 1;

				 INSERT INTO db_perso.remarques (IdPersonnage, Message, Type, DateCreation)
				 VALUES ( :characterid, :message, 'SKILL', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":reason", 'Achat - '.$lTree[$index]['name'], PDO::PARAM_STR);
			$this->DAL->Bind(":xpcost", $lTree[$index]['cost'], PDO::PARAM_INT);

			$this->DAL->Bind(":skillcode", $inSkillCode, PDO::PARAM_STR);
			$this->DAL->Bind(":uses", $lTree[$index]['uses'], PDO::PARAM_INT);
			$this->DAL->Bind(":acquisitioncode", $lAcquisitionCode, PDO::PARAM_STR);

			$this->DAL->Bind(":replaced", $lTree[$index]['replace'], PDO::PARAM_STR);

			$this->DAL->Bind(":message", 'Achat de la compétence "'.$lTree[$index]['name'].'" au coût de '.$lTree[$index]['cost'].' XP.', PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		
		if($r) { return True; }
		return False;
	}


	//--REGISTER A FREE SKILL--
	public function RegisterFreeSkill( $inSkillCode )
	{
		// Check if race and class are defined
		if( !isset( $this->Character ) ) { $this->Error = "No character set!"; return False; }
		if( !isset( $this->SkillTree ) ) { $this->Error = "No skill tree set!"; return False; }

		// Prepare data
		$lTree = $this->SkillTree->GetSkillTree();
		$index = $this->SkillTree->GetSkillIndex( $inSkillCode );

		// Ask the database...
		$lQuery = 	"INSERT INTO db_perso.competences_acquises (IdPersonnage, CodeCompetence, Type, Usages, CoutXP, CodeAcquisition, CodeEtat, DateCreation)
				 VALUES (:characterid, :skillcode, 'REG', :uses, 0, 'GRATUIT', 'PRLVL', sysdate() );

				 INSERT INTO db_perso.remarques (IdPersonnage, Message, Type, DateCreation)
				 VALUES ( :characterid, :message, 'SKILL', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":skillcode", $inSkillCode, PDO::PARAM_STR);
			$this->DAL->Bind(":uses", $lTree[$index]['uses'], PDO::PARAM_INT);

			$this->DAL->Bind(":message", 'Ajout automatique de la compétence "'.$lTree[$index]['name'], PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		
		if($r) { return True; }
		return False;
	}


	//--CANCEL A PURCHASED SKILL--
	public function CancelSkillPurchase( $inSkillIndex )
	{
		// Check if race and class are defined
		if( !isset( $this->Character ) ) { $this->Error = "No character set!"; return False; }
		if( !isset( $this->SkillTree ) ) { $this->Error = "No skill tree set!"; return False; }

		// Prepare data
		$lTree = $this->SkillTree->GetSkillTree();
		$lSkillCode = $this->SkillTree->GetObtainedSkills()[$inSkillIndex]['code'];
		$lAcquiredID = $this->SkillTree->GetObtainedSkills()[$inSkillIndex]['id'];
		$index = $this->SkillTree->GetSkillIndex( $lSkillCode );
		$lXPCost = $this->SkillTree->GetObtainedSkills()[$inSkillIndex]['xpcost'];
		$lAcquisitionCode = $this->SkillTree->GetObtainedSkills()[$inSkillIndex]['acquisition'];


		// Ask the database...
		$lQuery = 	"DELETE FROM db_perso.experience
	  			 WHERE IdPersonnage = :characterid
	  			 AND Raison = :reason
	  			 AND XP = :xp
	  			 LIMIT 1;

				 DELETE FROM db_perso.competences_acquises
				 WHERE Id = :acquiredid;

				 UPDATE db_perso.competences_acquises cac
				 SET cac.CodeEtat = 'ACTIF'
				 WHERE cac.CodeEtat = 'REMPL'
				   AND cac.CodeCompetence = :replaced;

				 INSERT INTO db_perso.remarques (IdPersonnage, Message, Type, DateCreation)
				 VALUES ( :characterid, :message, 'SKILL', sysdate() );";

		if( $lAcquisitionCode == 'RABAIS' ) {
			$lQuery .= 	"UPDATE db_perso.enseignements ens
					 SET ens.CodeEtat = 'ACTIF'
					 WHERE ens.CodeEtat = 'INACT'
					   AND ens.IdEtudiant = :characterid
					   AND ens.CodeCompetence = :skillcode
					 ORDER BY ens.IdActivite DESC LIMIT 1;";
		}

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":reason", 'Achat - '.$lTree[$index]['name'], PDO::PARAM_STR);
			$this->DAL->Bind(":xp", -$lXPCost, PDO::PARAM_INT);

			$this->DAL->Bind(":acquiredid", $lAcquiredID, PDO::PARAM_INT);

			$this->DAL->Bind(":replaced", $lTree[$index]['replace'], PDO::PARAM_STR);

			$this->DAL->Bind(":message", 'Remboursement de la compétence "'.$lTree[$index]['name'].'" pour '.$lXPCost.' XP.', PDO::PARAM_STR);

			$this->DAL->Bind(":skillcode", $lSkillCode, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		
		if($r) { return True; }
		return False;
	}


	//--CANCEL A FREE SKILL--
	public function CancelFreeSkill( $inSkillCode )
	{
		// Check if race and class are defined
		if( !isset( $this->Character ) ) { $this->Error = "No character set!"; return False; }
		if( !isset( $this->SkillTree ) ) { $this->Error = "No skill tree set!"; return False; }

		// Prepare data
		$lTree = $this->SkillTree->GetSkillTree();
		$index = $this->SkillTree->GetSkillIndex( $inSkillCode );


		// Ask the database...
		$lQuery = 	"DELETE FROM db_perso.competences_acquises
	  			 WHERE IdPersonnage = :characterid
	  			 AND CodeCompetence = :skillcode
	  			 AND CoutXP = 0
	  			 AND CodeAcquisition = 'GRATUIT'
	  			 LIMIT 1;

				 INSERT INTO db_perso.remarques (IdPersonnage, Message, Type, DateCreation)
				 VALUES ( :characterid, :message, 'SKILL', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":skillcode", $inSkillCode, PDO::PARAM_STR);

			$this->DAL->Bind(":message", 'Retrait automatique de la compétence "'.$lTree[$index]['name'], PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		
		if($r) { return True; }
		return False;
	}


	//--REGISTER A PURCHASED SKILL--
	public function RegisterTalentPurchase( $inTalentCode )
	{
		// Check if race and class are defined
		if( !isset( $this->Character ) ) { $this->Error = "No character set!"; return False; }
		if( !isset( $this->SkillTree ) ) { $this->Error = "No skill tree set!"; return False; }

		// Prepare data
		$lTree = $this->SkillTree->GetTalentTree();
		$index = $this->SkillTree->GetTalentIndex( $inTalentCode );
		$lCharacterCredits = $this->Character->GetQuestCredits();

		$lUniversalCreditCost = 0;
		$lSpecificCreditCost = $lTree[$index]['cost'];
			if( $lTree[$index]['cost'] > $lCharacterCredits[$inTalentCode] ) { 
				$lSpecificCreditCost = $lCharacterCredits[$inTalentCode];
				$lUniversalCreditCost = $lTree[$index]['cost'] - $lCharacterCredits[$inTalentCode];
			}

		// Ask the database...
		$lQuery = 	"UPDATE db_perso.personnages 
				 SET CreditsQuete = CreditsQuete - :universalcreditcost
				 WHERE Id = :characterid;

				 UPDATE db_perso.credits_quete 
				 SET Credits = Credits - :specificcreditcost
				 WHERE IdPersonnage = :characterid
				   AND CodeRecompense = :talentcode;

				 INSERT INTO db_perso.competences_acquises (IdPersonnage, CodeCompetence, Type, CoutXP, CodeAcquisition, CodeEtat, DateCreation)
				 VALUES (:characterid, :talentcode, :type,  0, 'QUETE', 'ACTIF', sysdate() );

				 INSERT INTO db_perso.remarques (IdPersonnage, Message, Type, DateCreation)
				 VALUES ( :characterid, :message, 'SKILL', sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":universalcreditcost", $lUniversalCreditCost, PDO::PARAM_INT);
			$this->DAL->Bind(":specificcreditcost", $lSpecificCreditCost, PDO::PARAM_INT);

			$this->DAL->Bind(":talentcode", $inTalentCode, PDO::PARAM_STR);
			$this->DAL->Bind(":type", $lTree[$index]['type'], PDO::PARAM_STR);

			$this->DAL->Bind(":message", 'Acquisition de la compétence "'.$lTree[$index]['name'].'" au coût de '.$lTree[$index]['cost'].' crédits.', PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		
		if($r) { return True; }
		return False;
	}


	//--CANCEL A PURCHASED SKILL--
	public function CancelTalentPurchase( $inSkillIndex )
	{
		// Check if race and class are defined
		if( !isset( $this->Character ) ) { $this->Error = "No character set!"; return False; }
		if( !isset( $this->SkillTree ) ) { $this->Error = "No skill tree set!"; return False; }

		// Prepare data
		$lTree = $this->SkillTree->GetSkillTree();
		$lSkillCode = $this->SkillTree->GetObtainedSkills()[$inSkillIndex]['code'];
		$lAcquiredID = $this->SkillTree->GetObtainedSkills()[$inSkillIndex]['id'];
		$index = $this->SkillTree->GetSkillIndex( $lSkillCode );
		$lXPCost = $this->SkillTree->GetObtainedSkills()[$inSkillIndex]['xpcost'];
		$lAcquisitionCode = $this->SkillTree->GetObtainedSkills()[$inSkillIndex]['acquisition'];


		// Ask the database...
		$lQuery = 	"DELETE FROM db_perso.experience
	  			 WHERE IdPersonnage = :characterid
	  			 AND Raison = :reason
	  			 AND XP = :xp
	  			 LIMIT 1;

				 DELETE FROM db_perso.competences_acquises
				 WHERE Id = :acquiredid;

				 UPDATE db_perso.competences_acquises cac
				 SET cac.CodeEtat = 'ACTIF'
				 WHERE cac.CodeEtat = 'REMPL'
				   AND cac.CodeCompetence = :replaced;

				 INSERT INTO db_perso.remarques (IdPersonnage, Message, Type, DateCreation)
				 VALUES ( :characterid, :message, 'SKILL', sysdate() );";

		if( $lAcquisitionCode == 'RABAIS' ) {
			$lQuery .= 	"UPDATE db_perso.enseignements ens
					 SET ens.CodeEtat = 'ACTIF'
					 WHERE ens.CodeEtat = 'INACT'
					   AND ens.IdEtudiant = :characterid
					   AND ens.CodeCompetence = :skillcode
					 ORDER BY ens.IdActivite DESC LIMIT 1;";
		}

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":reason", 'Achat - '.$lTree[$index]['name'], PDO::PARAM_STR);
			$this->DAL->Bind(":xp", -$lXPCost, PDO::PARAM_INT);

			$this->DAL->Bind(":acquiredid", $lAcquiredID, PDO::PARAM_INT);

			$this->DAL->Bind(":replaced", $lTree[$index]['replace'], PDO::PARAM_STR);

			$this->DAL->Bind(":message", 'Remboursement de la compétence "'.$lTree[$index]['name'].'" pour '.$lXPCost.' XP.', PDO::PARAM_STR);

			$this->DAL->Bind(":skillcode", $lSkillCode, PDO::PARAM_STR);
		$r = $this->DAL->FetchResult();

		
		if($r) { return True; }
		return False;
	}


	//--REGISTER A LIFE MODIFIER--
	public function RegisterLifeMod( $inLifeMod, $inReason )
	{
		// Check if race and class are defined
		if( !isset( $this->Character ) ) { $this->Error = "No character set!"; return False; }


		// Ask the database...
		$lQuery = 	"INSERT INTO db_perso.points_de_vie (IdPersonnage, Raison, PV, DateInscription)
				 VALUES (:characterid, :reason, :life, sysdate() );";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":reason", $inReason, PDO::PARAM_STR);
			$this->DAL->Bind(":life", $inLifeMod, PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


	//--CANCEL A LIFE MODIFIER--
	public function CancelLifeMod( $inLifeMod, $inReason )
	{
		// Check if race and class are defined
		if( !isset( $this->Character ) ) { $this->Error = "No character set!"; return False; }


		// Ask the database...
		$lQuery = 	"DELETE FROM db_perso.points_de_vie 
				 WHERE IdPersonnage = :characterid
				   AND Raison = :reason
				   AND PV = :life
 				 ORDER BY Id DESC LIMIT 1;";

		$this->DAL->SetQuery($lQuery);
			$this->DAL->Bind(":characterid", $this->Character->GetID(), PDO::PARAM_INT);
			$this->DAL->Bind(":reason", $inReason, PDO::PARAM_STR);
			$this->DAL->Bind(":life", $inLifeMod, PDO::PARAM_INT);
		return $this->DAL->FetchResult();
	}


} // END of SkillTreeServices class

?>
