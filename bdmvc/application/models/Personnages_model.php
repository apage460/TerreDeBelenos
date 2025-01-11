<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Personnages_model extends CI_Model {

	public function __construct()	{
		parent::__construct();
		$this->db->db_select('db_perso');
	}

	public function getRaces(){
		$this->db->db_select('db_pilot');

		$this->db->select('Code, Nom');
		$query = $this->db->get('races');

		return $query->result();
	}

	public function getReligions(){
		$this->db->db_select('db_pilot');

		$this->db->select('Code, Nom');
		$query = $this->db->get('religions');

		return $query->result();
	}

	public function getClasses(){
		$this->db->db_select('db_pilot');

		$this->db->select('Code, Nom');
		$query = $this->db->get('classes');

		return $query->result();
	}

	public function getSubClasses(){
		$this->db->db_select('db_pilot');

		$this->db->select("arch.Code, arch.CodeClasse, arch.Nom as 'subNom', classe.Nom as Nom");
		$this->db->from('db_pilot.archetypes arch');
		$this->db->join('db_pilot.classes classe', 'classe.Code = arch.CodeClasse', 'left');
		$this->db->order_by('Nom, subNom', 'ASC');
		$query = $this->db->get();

		return $query->result();
	}

	public function getMoyensResurrection(){
		$this->db->db_select('db_pilot');

		$this->db->select("Nom");
		$this->db->from('moyens_resurrection');
		$query = $this->db->get();

		return $query->result();
	}

	public function getTitres($idPerso){
		$this->db->db_select('db_perso');

		$this->db->where('IdPersonnage', $idPerso);
		$query = $this->db->get('titres');

		return $query->result();
	}

	public function getConditions($idPerso){
		$this->db->db_select('db_perso');

		$this->db->where('IdPersonnage', $idPerso);
		$query = $this->db->get('conditions');

		return $query->result();
	}

	public function getAllTitres($idPerso){
		$strQuery = "SELECT spe.Code as 'Code', spe.Nom as 'Nom', NULL as 'Avantages' 
					FROM db_pilot.competences_speciales spe
					WHERE (spe.Type = 'MYTHIQUE' OR spe.Type = 'PRESTIGE')
					AND (spe.CodeEtat = 'ACTIF')
					UNION 
					SELECT tit.Code as 'titCode', tit.Nom as 'titNom', tit.Avantages as 'titAvantages' 
					FROM db_pilot.titres tit
					WHERE (tit.CodeEtat = 'ACTIF')";

		$query = $this->db->query($strQuery);

		return $query->result();
	}

	public function getResults(){

		$this->db->db_select('db_indiv');

		$this->db->select('indiv.Nom as nomIndiv, indiv.Id as idIndiv, indiv.Pseudo as pseudoIndiv, indiv.Prenom as prenomIndiv');
		$this->db->from('individus indiv');
		$this->db->where('indiv.CodeEtat', 'ACTIF');
		
		$this->db->join('db_perso.personnages perso', 'perso.IdIndividu = indiv.Id', 'left');

		/* WHEREs INDIV */
		if($this->input->post('prenomIndiv')){
			$this->db->like('indiv.Prenom', $this->input->post('prenomIndiv') );
		}
		if($this->input->post('nomIndiv')){
			$this->db->like('indiv.Nom', $this->input->post('nomIndiv') );
		}
		if($this->input->post('pseudoIndiv')){
			$this->db->like('indiv.pseudo', $this->input->post('pseudoIndiv') );
		}
		if($this->input->post('ddnIndiv')){
			$this->db->where('indiv.DateNaissance ' .$this->input->post('ddnIndivOp'), $this->input->post('ddnIndiv') );
		}
		/* WHEREs PERSO */
		if($this->input->post('prenomPerso')){
			$this->db->like('perso.Prenom', $this->input->post('prenomPerso') );
		}
		if($this->input->post('nomPerso')){
			$this->db->like('perso.Nom', $this->input->post('nomPerso') );
		}
		if($this->input->post('niveauPerso')){
			$this->db->where('perso.Niveau ' .$this->input->post('niveauPersoOp'), $this->input->post('niveauPerso') );
		}
		if($this->input->post('classePerso') != 'NULL'){
			$this->db->where('perso.CodeClasse', $this->input->post('classePerso') );
		}
		if($this->input->post('racePerso') != 'NULL'){
			$this->db->where('perso.CodeRace', $this->input->post('classePerso') );
		}
		if($this->input->post('religionPerso') != 'NULL'){
			$this->db->where('perso.CodeReligion', $this->input->post('classePerso') );
		}

		$this->db->group_by('idIndiv');

		$this->db->order_by('prenomIndiv, nomIndiv', 'asc');

		$query = $this->db->get();
		$vJoueurs = $query->result_array();

		$joueurs = [];

		foreach ($vJoueurs as $key => $joueur) {

			$this->db->db_select('db_perso');
			$this->db->where('IdIndividu', $joueur['idIndiv'] );
			$this->db->where('CodeEtat !=', 'SUPPR');
			$this->db->where('CodeUnivers', $_SESSION['infoUnivers']);
			$query = $this->db->get('personnages');

			$joueurs[$key]['nomIndiv'] = $joueur['nomIndiv'];
			$joueurs[$key]['idIndiv'] = $joueur['idIndiv'];
			$joueurs[$key]['pseudoIndiv'] = $joueur['pseudoIndiv'];
			$joueurs[$key]['prenomIndiv'] = $joueur['prenomIndiv'];

			$joueurs[$key]['Personnages'] = $query->result();

		}

		return $joueurs;
	}

	public function getIndivInfo($idIndiv){
		$this->db->db_select('db_indiv');
		$this->db->select('Id, Prenom, Nom');
		$this->db->where('Id', $idIndiv);

		$query = $this->db->get('individus');
		return $query->row();
	}

	public function getIndivFromPerso($idPerso){
		$this->db->db_select('db_indiv');

		$this->db->select('indiv.*');
		$this->db->from('db_indiv.individus indiv');
		$this->db->join('db_perso.personnages perso', 'perso.IdIndividu = indiv.Id', 'left');
		$this->db->where('perso.Id', $idPerso);

		$query = $this->db->get();

		return $query->row();
	}

	public function getPersoInfo($idPerso){
		$this->db->db_select('db_perso');

		$this->db->select('perso.*, race.Nom as Race, classe.Nom as Classe, religion.Nom as Religion, CONCAT(perso.Prenom, " ", perso.Nom) as "nomPerso"');
		$this->db->from('personnages perso');
		$this->db->join('db_pilot.races race', 'race.Code = perso.CodeRace', 'left');
		$this->db->join('db_pilot.classes classe', 'classe.Code = perso.CodeClasse', 'left');
		$this->db->join('db_pilot.religions religion', 'religion.Code = perso.CodeReligion', 'left');
		$this->db->where('Id', $idPerso);

		$query = $this->db->get();
		return $query->row();
	}

	public function editReligion($idPerso) {
		$codeReligion = $this->input->post('newReligion');

		$data = array(
			'CodeReligion' => $codeReligion,
		);

		$this->db->db_select('db_perso');

		$this->db->where('Id', $idPerso);
		$this->db->update('personnages', $data);

	}

	public function editClasse($idPerso){
		$codeSubClasse = $this->input->post('newSubClasse');
		$codeRace = $this->input->post('codeRace');

		$this->db->where('IdPersonnage', $idPerso);
		$this->db->where('CodeAcquisition', 'DEPART');

		$this->db->delete('competences_acquises');

		$this->db->db_select('db_pilot');
		$this->db->select('CodeClasse');
		$this->db->where('Code', $codeSubClasse);
		$query = $this->db->get('archetypes');

		$result = $query->row();
		$codeNewClasse = $result->CodeClasse;

		$skillsClasse = [];
		$skillsRace = [];

		/*Competences de classe générale*/

		$this->db->select("compdep.CodeCompReg, compdep.CodeCompSpec, reg.Usages as 'Usages' ");
		$this->db->from('db_pilot.competences_depart compdep');
		$this->db->join('db_pilot.competences_regulieres reg', 'reg.Code = compdep.CodeCompReg', 'left');
		$this->db->join('db_pilot.competences_speciales spec', 'spec.Code = compdep.CodeCompSpec', 'left');
		$this->db->where('CodeClasse', $codeNewClasse);

		$query = $this->db->get();
		$results = $query->result();

		foreach ($results as $result) {
			if ($result->CodeCompReg == null) {
				$code = $result->CodeCompSpec;
				$type = 'CLASSE';
			} else {
				$code = $result->CodeCompReg;
				$type = 'REG';
			}

			$data = array(
				'IdPersonnage' 	 	=> $idPerso,
				'CodeCompetence'	=> $code,
				'Type' 				=> $type,
				'Usages'			=> $result->Usages,
				'CoutXP'			=> 0,
				'DateCreation'		=> date('Y-m-d H:i:s', time()),
				'CodeAcquisition'	=> 'DEPART',
				'CodeEtat'			=> 'ACTIF'
			);

			array_push($skillsClasse, $data);
		}

		/* Competences de sous-classe */

		$this->db->select('arch.CodeCompReg, arch.CodeCompSpec');
		$this->db->from('db_pilot.competences_archetype arch');
		$this->db->join('db_pilot.competences_regulieres reg', 'reg.Code = arch.CodeCompReg', 'left');
		$this->db->where('CodeArchetype', $codeSubClasse);

		$query = $this->db->get();
		$results = $query->result();

		foreach ($results as $result) {
			if ($result->CodeCompReg == null) {
				$code = $result->CodeCompSpec;
				$type = 'CLASSE';
			} else {
				$code = $result->CodeCompReg;
				$type = 'REG';
			}

			$data = array(
				'IdPersonnage' 	 	=> $idPerso,
				'CodeCompetence'	=> $code,
				'Type' 				=> $type,
				'Usages'			=> NULL,
				'CoutXP'			=> 0,
				'DateCreation'		=> date('Y-m-d H:i:s', time()),
				'CodeAcquisition'	=> 'DEPART',
				'CodeEtat'			=> 'ACTIF'
			);

			array_push($skillsClasse, $data);
		}

		/* Competences Raciales */

		$this->db->select('rac.CodeCompReg, rac.CodeCompSpec');
		$this->db->from('db_pilot.competences_raciales rac');
		$this->db->where('rac.CodeRace', $codeRace);

		$query = $this->db->get();
		$results = $query->result();

		foreach ($results as $result) {

			if ($result->CodeCompReg == null) {
				$code = $result->CodeCompSpec;
			} else {
				$code = $result->CodeCompReg;
			}

			$data = array(
				'IdPersonnage' 	 	=> $idPerso,
				'CodeCompetence'	=> $code,
				'Type' 				=> 'RACIALE',
				'Usages'			=> 1,
				'CoutXP'			=> 0,
				'DateCreation'		=> date('Y-m-d H:i:s', time()),
				'CodeAcquisition'	=> 'DEPART',
				'CodeEtat'			=> 'ACTIF'
			);

			array_push($skillsRace, $data);
		}

		/* Cherche Doublons pour skills niv.2 SELON CLASSE - RACE */
		$skills = [];

		foreach ($skillsClasse as $skillClass) {
			foreach ($skillsRace as $skillRace) {
				if($skillClasse['CodeCompetence'] == $skillRace['CodeCompetence']){
					$this->db->db_select('db_pilot');

					$this->db->select('Code, CodeCompRemplacee');
					$this->db->where('CodeCompRemplacee', $skill['CodeCompetence']);
					$query = $this->db->get('competences_regulieres');
					$result = $query->row();
					if($result) {
						$skillRace['CodeCompetence'] = $result->Code;
						$skillClasse['CodeEtat'] = 'REMPL';
					}
				}
			}
		}

		$this->db->db_select('db_perso');
		foreach ($skills as $skill) {
			$this->db->insert('competences_acquises', $skill);
		}

		$this->db->db_select('db_perso');
		$this->db->where('Id', $idPerso);

		$this->db->update('personnages', array('CodeClasse' => $codeNewClasse));

		return $skills;
		
	}

	public function getSkills($idPerso, $orderByUEC = FALSE){

		$this->db->db_select('db_perso');

		$this->db->select('skills.Id, skills.CodeCompetence, skills.Type, SUM(skills.Usages) as UEC, skills.CoutXP, skills.DateCreation, skills.CodeAcquisition, skills.CodeEtat, reg.Nom as regNom, spec.Nom as specNom, skills.Precision');
		$this->db->from('db_perso.competences_acquises skills');
		$this->db->join('db_pilot.competences_regulieres reg', 'reg.Code = skills.CodeCompetence', 'left');
		$this->db->join('db_pilot.competences_speciales spec', 'spec.Code = skills.CodeCompetence', 'left');

		$where = "skills.IdPersonnage = " .$idPerso ." AND (skills.CodeEtat = 'ACTIF' OR skills.CodeEtat = 'LEVEL' OR skills.CodeEtat = 'PRLVL' ) ";
		$this->db->where($where);
		$this->db->group_by('skills.CodeCompetence');

		if($orderByUEC){
			$this->db->order_by("UEC", 'DESC');
		} else{
			$this->db->order_by("reg.Nom, spec.Nom", 'ASC');
		}

		$query = $this->db->get();

		return $query->result();
	}

	public function getRegSkills($idPerso){

		$this->db->db_select('db_perso');

		/* Get Perso XP */
		$this->db->select('perso.CodeClasse, perso.CodeRace, SUM(exp.XP) as XP');
		$this->db->from('db_perso.personnages perso');
		$this->db->join('db_perso.experience exp', 'exp.IdPersonnage = perso.Id', 'left');
		$this->db->where('perso.Id', $idPerso);

		$query = $this->db->get();
		$perso = $query->row();

		$persoXP = $perso->XP;
		$persoClasse = $perso->CodeClasse;
		$persoRace = $perso->CodeRace;
		$univers = $_SESSION['infoUnivers'];

		/* Get Reg Skills */
		$this->db->where('IdPersonnage', $idPerso);
		$this->db->where('Type', 'REG');
		$query = $this->db->get('competences_acquises');
		$persoRegSkills = $query->result();

		/* Get Non Reg Skills */
		$this->db->where('IdPersonnage', $idPerso);
		$this->db->where('Type !=', 'REG');
		$query = $this->db->get('competences_acquises');
		$persoTalents = $query->result();

		$lSkillCodeCounts = array();
		foreach ($persoRegSkills as $key => $persoRegSkill) {
			$lSkillCodeCounts[$persoRegSkill->CodeCompetence] = 1;
			if ($key >1 && $persoRegSkills[$key]->CodeCompetence == $persoRegSkills[$key-1]->CodeCompetence) {
				$lSkillCodeCounts[$persoRegSkill->CodeCompetence] .= 1;
			}
		}

		//$lSkillCodeCounts = array_count_values($persoRegSkills['CodeCompetence']);

		$hasMagic = false;
		foreach ($persoRegSkills as $persoRegSkill) {
			if( $persoRegSkill->CodeCompetence == 'MAGIA1' || $persoRegSkill->CodeCompetence == 'MAGIC1' || $persoRegSkill->CodeCompetence == 'MAGIM1' || $persoRegSkill->CodeCompetence == 'MAGIS1' ) { 
				
				$hasMagic = true; 
			}
		}

		$hasSuperiorMagic = false;
		foreach ($persoTalents as $persoTalent) {
			if( $persoTalent->CodeCompetence == 'NIVMSUP' ) { $hasSuperiorMagic = true; }
		}
		

		$this->db->select('CodeCompetence');
		$this->db->where('IdEtudiant', $idPerso);
		$this->db->where('CodeEtat', 'ACTIF');
		$query = $this->db->get('enseignements');
		$teachings = $query->result();

		// Ask the database for the skill tree
		$query = 	"SELECT creg.Code, creg.Nom, creg.Niveau, creg.Categorie, 
					ajc.Multiplicateur, (ajc.Multiplicateur*ccr.CoutXP) AS 'CoutMultiplie', 
					creg.CodeCompPrerequise AS 'Prerequis', creg.Usages, creg.Achats
				 FROM db_pilot.competences_regulieres creg
					LEFT JOIN db_pilot.ajustements_categorie ajc ON creg.Categorie = ajc.Categorie
						LEFT JOIN db_pilot.classes clas ON ajc.CodeClasse = clas.Code AND clas.CodeUnivers = '" .$univers ."'
					LEFT JOIN db_pilot.cout_competences_reg ccr  ON creg.Code = ccr.CodeCompReg
						LEFT JOIN db_pilot.races rac ON ccr.CodeRace = rac.Code AND rac.CodeUnivers = '" .$univers ."'
				 WHERE creg.CodeEtat = 'ACTIF'
				   AND clas.Nom = '" .$persoClasse ."'
				   AND rac.Nom = '" .$persoRace ."'
				 ORDER BY creg.Nom ASC";



		$query = $this->db->query($query);
		$skillTree = $query->result();

		// Build the skill tree
		$return = array();
		foreach($skillTree as $i => $skill) {

			// Calculate final cost
			$cost = $skill->CoutMultiplie;
			$trained = false;
			foreach ($teachings as $teaching) {
				if ($teaching->CodeCompetence == $skill->Code) {
					$trained = true;
				}
			}
			
			if( $skill->Multiplicateur < 1 && $skill->Remplace) { $cost *= 0.5; }	// If second-or-more level and in a bonus category... halved!
			if( $trained !== false ) { $cost *= 0.8; } 					// If character has received teaching for this skill... 20% bonus!

			// Handle skill attributes
			$return[$i]['code'] = $skill->Code;
			$return[$i]['name'] = $skill->Nom;
			$return[$i]['category'] = $skill->Categorie;
			$return[$i]['adjustment'] = $skill->Multiplicateur;
			$return[$i]['cost'] = ceil($cost);
			$return[$i]['prerequisites'] = explode( ";", $skill->Prerequis );
			$return[$i]['replace'] = $skill->Remplace;
			$return[$i]['uses'] = $skill->Usages;
			$return[$i]['maxpurchases'] = $skill->Achats;

			$return[$i]['obtained'] = 0;
			foreach ($persoRegSkills as $persoRegSkill) {
				if ($persoRegSkill->CodeCompetence == $skill->Code) {
					$return[$i]['obtained']++;
				}
			}

			if( $trained === false ) 
				{ $return[$i]['trained'] = false; }
			else 	{ $return[$i]['trained'] = true; }

			$return[$i]['buyable'] = true;
			if( $return[$i]['obtained'] >= $return[$i]['maxpurchases'] ) { $return[$i]['buyable'] = false; }
			elseif( $hasMagic && ($skill->Code == 'MAGIA1' || $skill->Code == 'MAGIC1' || $skill->Code == 'MAGIM1' ||$skill->Code == 'MAGIS1') ) { $return[$i]['buyable'] = false; }
			elseif( !$hasSuperiorMagic && ($skill->Code == 'MAGIA4' || $skill->Code == 'MAGIC4' || $skill->Code == 'MAGIM4' || $skill->Code == 'MAGIS4') ) { $return[$i]['buyable'] = false; }
			elseif( $skill->Prerequis ) {

				$lPrereqMet = false;
				$lOnlyOneLevel = true;

				// Check if prereq is obtained and is not on the 'Level up' list
				foreach( $return[$i]['prerequisites'] as $option) {
					foreach ($persoRegSkills as $persoRegSkill) {
						if ($persoRegSkill->CodeCompetence == $option) {
							$lPrereqMet = true;
						}
					}
				}

				// Check if skill is not the next level of a bought skill
				if( $skill->Remplace ) {
					foreach ($persoRegSkills as $persoRegSkill) {
						if ($persoRegSkill->CodeCompetence == $skill->Remplace) {
							$lOnlyOneLevel = true;
						}
					}
				}

				$return[$i]['buyable'] = $lPrereqMet && $lOnlyOneLevel;
			}

			if( $return[$i]['cost'] <= $persoXP ) { $return[$i]['affordable'] = true; }
			else { $return[$i]['affordable'] = false; }
		}

		return $return;

	}

	public function getSpecSkills(){
		$this->db->db_select('db_pilot');

		$this->db->select('Code, Nom, Type');
		$query = $this->db->get('competences_speciales');
		$this->db->order_by('Nom', 'asc');

		return $query->result();

	}

	public function getXP($idPerso){
		$this->db->db_select('db_perso');

		$this->db->select_sum('XP', 'XP');
		$this->db->where('IdPersonnage', $idPerso);

		$query = $this->db->get('experience');

		return $query->row();
	}

	public function getPV($idPerso){
		$this->db->db_select('db_perso');

		$this->db->select("*, (SELECT SUM(PV) FROM db_perso.points_de_vie WHERE IdPersonnage = " .$idPerso ." ) as 'SommePV'");
		$this->db->where('IdPersonnage', $idPerso);

		$query = $this->db->get('points_de_vie');

		return $query->result();
	}

	public function getMana($idPerso){
		/*
		$lMana = 0; 
		foreach ($this->Skills as $skill) {
			    if( substr($skill['code'], 0, 4) == 'MAGI' && $skill['status'] != 'INACT') { 
			    	$lMana += $skill['quantity']; 
			    }
				elseif( substr($skill['code'], 0, 6) == 'ELMMAG' && $skill['status'] != 'INACT') {
					$lMana += $skill['quantity'];
				}
		}
		foreach ($this->Talents as $talent) {
			    if( $talent['code'] == 'RESERVE' && $talent['status'] != 'INACT'){
			    	$lMana += 15;
			    }
				elseif( $talent['code'] == 'PROPHET' && $talent['status'] != 'INACT'){
					$lMana += 5;
				}
				elseif( $talent['code'] == 'ARCHIM'  && $talent['status'] != 'INACT'){
					$lMana += 5;
				}
		}
		return $lMana;
		*/
	}

	public function getTravail($idPerso){
		$this->db->db_select('db_group');

		$this->db->select('trav.*, group.Nom');
		$this->db->from('db_group.travailleurs trav');
		$this->db->where('IdPersonnage', $idPerso);
		$this->db->join('db_group.groupes group', 'trav.IdGroupe = group.Id', 'left');
		$query = $this->db->get();

		return $query->row();
	}

	public function paySkill($idPerso){
				

		$info = explode(',', $this->input->post('paySkill') );

		$code = $info[0];
		$cost = $info[1];

		$this->db->db_select('db_pilot');

		$this->db->select('Usages, Nom');
		$this->db->where('Code', $code);
		$query = $this->db->get('competences_regulieres');
		$usages = $query->row();

		$this->db->db_select('db_perso');

		if ($usages == 'NULL') : $usages = NULL; endif;

		$data = array(
			'IdPersonnage' => $idPerso,
			'CodeCompetence' => $code,
			'Type' => 'REG',
			'Usages' => $usages->Usages,
			'CoutXP' => $cost,
			'DateCreation' => date('Y-m-d H:i:s', time()),
			'CodeAcquisition' => 'NORMALE',
			'CodeEtat' => 'LEVEL',
		);

		$this->db->insert('competences_acquises', $data);

		$this->db->db_select('db_perso');

		$data = array(
			'IdPersonnage' => $idPerso,
			'Raison' => 'Achat - ' .$usages->Nom,
			'XP' => intval(-$cost),
			'DateInscription' => date('Y-m-d H:i:s', time()),
			'Commentaires' => NULL,
			'IdEnseignement' => NULL
		);

		$this->db->insert('experience', $data);

		if ( substr($code,0,2) == 'PV') {
			$data = array(
				'IdPersonnage' => $idPerso,
				'Raison' => 'Achat de PV',
				'PV' => 1,
				'DateInscription' => date('Y-m-d H:i:s', time()),
				'Commentaires' => null,
			);

			$this->db->insert('points_de_vie', $data);
		}

		$this->updatePreviousSkill($idPerso, $code);
	}

	public function giveSkill($idPerso){	

		$info = explode(',', $this->input->post('giveSkill') );



		if( $info[1] == null ) {

			$this->db->db_select('db_pilot');
			$this->db->select('Usages');
			$this->db->where('Code', $info[0]);
			$query = $this->db->get('competences_regulieres');
			$usages = $query->row();
			$usages = $usages->Usages;
		}

		$this->db->db_select('db_perso');

		if(!isset($info[2])){
			$code = $info[0];
			$type = $info[1];
			$usages = NULL;
			$cost = 0;
			$codeAcquisition = 'GRATUIT';
			$etat = 'LEVEL';
		}elseif( $info[1] != null ) {
			$code = $info[0];
			$type = 'REG';
			$usages = $info[1];
			$cost = 0;
			$codeAcquisition = 'GRATUIT';
			$etat = 'LEVEL';
		} else{ 
			$code = $info[0];
			$type = 'REG';
			$usages = NULL;
			$cost = 0;
			$codeAcquisition = 'GRATUIT';
			$etat = 'ACTIF';
		}

		if($usages == 0): $usages = null; endif;

		$data = array(
			'IdPersonnage' => $idPerso,
			'CodeCompetence' => $code,
			'Type' => $type,
			'Usages' => $usages,
			'CoutXP' => 0,
			'DateCreation' => date('Y-m-d H:i:s', time()),
			'CodeAcquisition' => $codeAcquisition,
			'CodeEtat' => $etat,
		);

		$this->db->insert('competences_acquises', $data);

		//$this->updatePreviousSkill($idPerso, $code);
		
	}

	public function updatePreviousSkill($idPerso, $codeCompetence){
		$this->db->db_select('db_pilot');

		$this->db->select('CodeCompRemplacee');
		$this->db->where('Code', $codeCompetence);
		$query = $this->db->get('competences_regulieres');
		$codeCompRemplacee = $query->row();

		$this->db->db_select('db_perso');
		if($codeCompRemplacee && $codeCompRemplacee->CodeCompRemplacee != null){
			$this->db->where('IdPersonnage', $idPerso);
			$this->db->where('CodeCompetence', $codeCompRemplacee->CodeCompRemplacee);

			$data = array('CodeEtat' => 'REMPL');
			$this->db->update('competences_acquises', $data);
		}
	}

	public function deleteSkills($idSkill, $codeEtat){;

		if($codeEtat == 'ACTIF'){
			$updateData = array(
				'CodeEtat' => 'INACT',
			);

			$this->db->where('Id', $idSkill);
			$this->db->update('competences_acquises', $updateData);
			return;
		}
		if($codeEtat == 'LEVEL'){


			$this->db->select('acq.*, pilot.Nom as NomSkillComplet, pilot.CodeCompPrerequise as Prerequis');
			$this->db->from('db_perso.competences_acquises acq');
			$this->db->join('db_pilot.competences_regulieres pilot', 'pilot.Code = acq.CodeCompetence', 'left');
			$this->db->where('acq.Id', $idSkill);
			$query = $this->db->get();
			$acq = $query->row();

			/**/
			$this->db->where('Id', $idSkill);
			$this->db->delete('competences_acquises');

			/**/
			$this->db->db_select('db_perso');
			$raison = 'Achat - ' .$acq->NomSkillComplet;
			$sql = "DELETE FROM experience WHERE IdPersonnage = " .$acq->IdPersonnage ." AND Raison = '" .$raison ."' LIMIT 1;";
			$this->db->query($sql);

			/**/
			if($acq->CodeAcquisition == 'RABAIS'){
				$sql = "UPDATE db_perso.enseignements SET CodeEtat = 'ACTIF' WHERE IdEtudiant = " .$acq->IdPersonnage ." AND CodeCompetence = '" .$acq->CodeCompetence ."'  AND CodeEtat = 'INACT' LIMIT 1";
				$this->db->query($sql);
			}

			if($acq->Prerequis != null){
				$sql = "UPDATE db_perso.competences_acquises SET CodeEtat = 'ACTIF' WHERE IdPersonnage = " .$acq->IdPersonnage ." AND CodeCompetence = '" .$acq->Prerequis ."';";
				$this->db->query($sql);
			}

		}
	}

	public function declareMort($idPerso){

		$comment = $this->input->post('comment');

		if ($this->input->post('comment') == '' ):
			$comment = NULL;
		endif;

		$raison = $this->input->post('declareMort');

		if($raison == 'MortDefinitive'){
			$data = array(
				'CodeEtat' => 'MORT'
			);

			$this->db->where('Id', $idPerso);
			$this->db->update('personnages', $data);
		} else{
			$data = array(
				'IdPersonnage' => $idPerso,
				'Raison' => $raison,
				'PV' => '-1',
				'DateInscription' => date('Y-m-d H:i:s', time()),
				'Commentaires' => $comment,
			);

			$this->db->insert('points_de_vie', $data);
		}
	}

	public function deleteTravail($idPerso){
		$this->db->db_select('db_group');

		$this->db->where('IdPersonnage',$idPerso);
		$this->db->delete('travailleurs');
	}

	public function levelUP($idPerso, $idIndiv, $currentLvl){
		$this->db->db_select('db_perso');

		$currentLvl = intval($currentLvl);

		/* Activate Temp skills */
		$data = array(
			'CodeEtat' => 'ACTIF'
		);

		$this->db->where('IdPersonnage',$idPerso);
		$this->db->where('CodeEtat','LEVEL');

		$this->db->update('competences_acquises',$data);

		$data = array(
			'CodeEtat' => 'ACTIF',
		);
		$this->db->where('IdIndividu',$idIndiv);
		$this->db->where('CodeEtat','LEVEL');
		$this->db->update('personnages',$data);


		$data = array(
			'CodeEtat' => 'ACTIF',
		);
		$this->db->where('Id',$idPerso);
		$this->db->update('personnages',$data);

		/* Grant a level */

		$data = array(
			'Niveau' => $currentLvl+1,
			'CodeEtat' => 'LEVEL'
		);
		$where = "Id= " .$idPerso ." AND CodeEtat IN ('NOUVO', 'ACTIF')";
		$this->db->where($where);
		$this->db->update('personnages', $data);

		/* Update character's journal */		

		$message = "Niveau " .($currentLvl+1) ." !";

		$data = array(
			'IdPersonnage' => $idPerso,
			'Message' => $message,
			'Type' => 'LEVEL',
			'DateCreation' => date('Y-m-d H:i:s', time())
		);

		$this->db->insert('remarques',$data);

		$this->db->db_select('db_indiv');

		/* CHECK IF TUTORED */
		$this->db->select('Tuteur');
		$this->db->where('Id',$idIndiv);

		$query = $this->db->get('individus');
		$boolTuteur = $query->row();

		/* Grant XP */	
		
		$this->db->db_select('db_perso');
		
		if( ($currentLvl+1) > 1 && ($currentLvl+1) <= 5){
			$message = "Niveau " .($currentLvl+1);

			$XP = 50;
			if( !is_null( $boolTuteur->Tuteur ) && $boolTuteur->Tuteur != 'GroupeCadre' ){
				$XP = 25;
			}

			$insertData = array(
				'IdPersonnage' => $idPerso,
				'Raison' => $message,
				'XP' => $XP,
				'DateInscription' => date('Y-m-d H:i:s', time()),
				'Commentaires' => null,
			);

			$this->db->insert('experience',$insertData);

		}
	}

	public function addTitre($idPerso, $idIndiv){
		$explodedTitre = explode('|',$this->input->post('titre'));

		$insertData = array(
			'IdPersonnage' => $idPerso,
			'CodeTitre' => $explodedTitre[0],
			'Titre' => $explodedTitre[1],
			/*'Description' => $this->input->post('description'),
			'Avantages' => $this->input->post('avantages'),*/
			'DateAcquisition' => date('Y-m-d H:i:s', time())
		);

		$this->db->db_select('db_perso');
		$this->db->insert('titres', $insertData);
	}

	public function removeTitre($idTitre){
		$this->db->db_select('db_perso');
		$this->db->where('Id', $idTitre);
		$this->db->delete('titres');
	}

	public function getSpells($idPerso){
		$this->db->db_select('db_perso');

		$query = "SELECT acq.*, reg.Nom as 'cleanNom' 
			FROM competences_acquises acq
			LEFT JOIN db_pilot.competences_regulieres reg ON reg.Code = acq.CodeCompetence 
			WHERE acq.IdPersonnage = {$idPerso} 
			AND (acq.CodeCompetence LIKE 'RECET%' OR acq.CodeCompetence LIKE 'SORT%' OR acq.CodeCompetence LIKE 'MALEDIC' OR acq.CodeCompetence LIKE 'MAGI%' AND acq.Precision IS NOT NULL) 
			AND (acq.CodeEtat = 'ACTIF' OR acq.CodeEtat = 'PRLVL' OR acq.CodeEtat = 'LEVEL')
			ORDER BY FIELD(acq.CodeCompetence, 'MAGIM1','MAGIC1','ALCHIM1', 'HERBO1', 'RECETA1', 'RECETH1', 'SORT1', 'MAGIM2','MAGIC2', 'ALCHIM2', 'HERBO2', 'RECETA2', 'RECETH2', 'SORT2', 'MAGIM3','MAGIC3', 'ALCHIM3', 'HERBO3', 'RECETA3', 'RECETH3', 'SORT3', 'MAGIM4','MAGIC4', 'ALCHIM4', 'HERBO4', 'RECETA4', 'RECETH4', 'SORT4', 'MAGIM5','MAGIC5', 'ALCHIM5', 'HERBO5', 'RECETA5', 'RECETH5', 'SORT5', 'MAGIM6','MAGIC6', 'MAGIM7','MAGIC7', 'MAGIM8','MAGIC8', 'MAGIM9','MAGIC9', 'MAGIM10','MAGIC10');";
		

		$query = $this->db->query($query);

		return $query->result();
	}

	public function getAllMetiers(){
		$this->db->db_select('db_pilot');

		$this->db->order_by('Nom', 'asc');
		$query = $this->db->get('metiers');

		return $query->result();
	}

	public function getAllSpells(){
		$this->db->db_select('db_pilot');

		$this->db->order_by('Nom', 'asc');
		$query = $this->db->get('sorts');

		return $query->result();
	}

	public function getAllRecettes(){
		$this->db->db_select('db_pilot');

		$this->db->order_by('Nom', 'asc');
		$query = $this->db->get('recettes');

		return $query->result();
	}

	public function updateSpells($idPerso, $idIndiv, $spells){

		foreach ($spells as $key => $spell) {
			$data = [
				'Precision' => $spell
			];

			$this->db->where('Id', $key);
			$this->db->where('IdPersonnage', $idPerso);
			$this->db->update('competences_acquises', $data);
		}
	}

	public function editNoteRapide($idPerso, $idIndiv, $data){
		$this->db->db_select('db_perso');

		$this->db->where('Id', $idPerso);
		$this->db->update('personnages', ['NoteRapide' => $data]);
	}

	public function changeState($idPerso, $newState){
		$this->db->db_select('db_perso');

		$this->db->where('Id', $idPerso);
		$this->db->update('personnages', ['CodeEtat' => $newState]);
	}

	public function getNpcs(){
		$this->db->db_select('db_perso');

		$this->db->where('IdIndividu', 3);
		$this->db->where('CodeEtat', 'PNJ');
		$this->db->order_by('Prenom');
		$query = $this->db->get('personnages');

		return $query->result();

	}

	public function getMissives($idActivite = NULL){
		$this->db->db_select('db_perso');

		$this->db->select("miss.*, act.Nom as 'NomActivite', CONCAT(ecr.Prenom, ' ', ecr.Nom) as 'nomEcrivain', CONCAT(dest.Prenom, ' ', dest.Nom) as 'nomDestinataire'");
		$this->db->from('missives miss');
		$this->db->join('db_perso.personnages ecr', 'ecr.Id = miss.IdEcrivain', 'left');
		$this->db->join('db_perso.personnages dest', 'dest.Id = miss.IdDestinataire', 'left');
		$this->db->join('db_activ.activites act', 'act.Id = miss.IdActivite', 'left');
		if(!is_null($idActivite)){
			$this->db->where('miss.IdActivite', $idActivite);
		}
		$this->db->order_by("miss.DateEnvoi");
		$query = $this->db->get();

		return $query->result();

	}

	public function getMissive($idMissive){
		$this->db->db_select('db_perso');

		$this->db->select("miss.*, perso.Id as 'IdPerso', CONCAT(perso.Prenom, ' ', perso.Nom) as 'Ecrivain', CONCAT(perso2.Prenom, ' ', perso2.Nom) as 'Destinataire'");
		$this->db->from('missives miss');
		$this->db->join('personnages perso', 'perso.Id = miss.IdEcrivain', 'left');
		$this->db->join('personnages perso2', 'perso2.Id = miss.IdDestinataire', 'left');
		$this->db->where('miss.Id', $idMissive);
		$query = $this->db->get();

		return $query->row();
	}

	public function getReplies($idMissive){
		$this->db->db_select('db_perso');

		$this->db->select("miss.*, CONCAT(perso.Prenom, ' ', perso.Nom) as 'Ecrivain', CONCAT(perso2.Prenom, ' ', perso2.Nom) as 'Destinataire', (SELECT COUNT(Id) FROM missives WHERE ReponseA = $idMissive AND CodeEtat = 'NOUVO') as 'NbReplies'");
		$this->db->from('missives miss');
		$this->db->join('personnages perso', 'perso.Id = miss.IdEcrivain', 'left');
		$this->db->join('personnages perso2', 'perso.Id = miss.IdDestinataire', 'left');
		$this->db->where('miss.ReponseA', $idMissive);
		$this->db->order_by("FIELD(miss.CodeEtat, 'NOUVO', 'REDAC', 'LU'), miss.Id");
		$query = $this->db->get();

		return $query->result();
	}

	public function readMissive($idMissive){
		$this->db->db_select('db_perso');

		$update = array(
			'CodeEtat' => 'LU'
		);
		$this->db->where('Id', $idMissive);

		$this->db->update('missives', $update);
	}

	public function searchMissive(){
		$this->db->db_select('db_perso');

		$this->db->select("miss.*, CONCAT(perso.Prenom, ' ', perso.Nom) as 'nomDestinataire'");
		$this->db->from('missives miss');
		$this->db->join('db_perso.personnages perso', 'perso.Id = miss.IdDestinataire', 'left');
		$this->db->where('IdDestinataire', $_GET['IdDestinataire']);

		$this->db->order_by("miss.IdDestinataire, FIELD(miss.CodeEtat, 'NOUVO', 'REDAC', 'LU'), miss.Id");

		$query = $this->db->get();

		return $query->result();

	}

	public function searchPersoMissive(){
		$this->db->db_select('db_indiv');

		$this->db->select("perso.Id, indiv.Prenom as 'indivPrenom', indiv.Nom as 'indivNom', perso.Prenom as 'persoPrenom', perso.Nom as 'persoNom'" );
		$this->db->from('db_indiv.individus indiv');
		$this->db->join('db_perso.personnages perso', 'perso.IdIndividu = indiv.Id', 'left');

		if($_GET['prenomJoueur'] != ''){
			$this->db->like('indiv.Prenom', $_GET['prenomJoueur'], 'BOTH');
		}

		if($_GET['nomJoueur'] != ''){
			$this->db->like('indiv.Nom', $_GET['nomJoueur'], 'BOTH');
		}

		if($_GET['prenomPerso'] != ''){
			$this->db->like('perso.Prenom', $_GET['prenomPerso'], 'BOTH');
		}

		if($_GET['nomPerso'] != ''){
			$this->db->like('perso.Nom', $_GET['nomPerso'], 'BOTH');
		}

		$this->db->where_in("perso.codeEtat",  array('ACTIF', 'LEVEL'));

		$this->db->order_by('perso.Id', 'asc');

		$query = $this->db->get();

		return $query->result();
	}

	public function sendMissive(){
		$this->db->db_select('db_perso');

		$data = array(
			'IdEcrivain' => $this->input->post('ecrivain'),
			'IdDestinataire' => $this->input->post('idDestinataire'),
			'Type' => 'NPC',
			'Objet' => $this->input->post('objet'),
			'Corps' => $this->input->post('corps'),
			'CodeEtat' => 'NOUVO',
			'DateEnvoi' => date('Y-m-d H:i:s', time()),
			'ReponseA' => ($this->input->post('reponseA') != null)?$this->input->post('reponseA'):null
		);

		$this->db->insert('missives', $data);
	}

	public function addSpell($idPerso){
		$this->db->db_select('db_perso');

		$data = array(
			'IdPersonnage' => $idPerso,
			'CodeCompetence' => 'SORT1',
			'Type' => 'REG',
			'Usages' => NULL,
			'CoutXP' => 0,
			'DateCreation' => date('Y-m-d H:i:s', time()),
			'CodeAcquisition' => 'GRATUIT',
			'CodeEtat' => 'ACTIF'
		);

		$this->db->insert('competences_acquises', $data);
	}

	public function getCourrielFromPlanDeCour( $idPlan ){
		$sql = "SELECT Courriel, CASE WHEN indiv.Pseudo = NULL THEN CONCAT(indiv.Prenom, ' ', indiv.Nom) ELSE indiv.Pseudo END AS 'nomIndiv', CONCAT(perso.Prenom,' ', perso.Nom) as 'nomPerso', pilotSkill.Nom as 'NomSkill', maitr.CodeCompetence, perso.Id
				FROM db_indiv.individus indiv 
				LEFT JOIN db_perso.personnages perso ON perso.IdIndividu = indiv.Id
				LEFT JOIN db_perso.maitres maitr ON maitr.IdPersonnage = perso.Id 
				LEFT JOIN db_pilot.competences_regulieres pilotSkill ON pilotSkill.Code = maitr.CodeCompetence
				WHERE maitr.Id = $idPlan";

		$query = $this->db->query($sql);

		return $query->row();
	}

	public function has_paid($idIndiv, $idActiv){
		$this->db->db_select('db_indiv');

		$this->db->where('IdIndividu', $idIndiv);
		$this->db->where('IdActivite', $idActiv);

		$query = $this->db->get('presences');

		$result = $query->row();

		$has_paid = false;

		if($query->num_rows() >= 1){
			$has_paid = true;
		}

		return $has_paid;
	}

	public function has_missives($idPerso, $idActiv, $idIndiv = null, $typeActivite = null){
		if(is_null($idPerso) && !is_null($idIndiv)){
			$this->db->db_select('db_activ');

			$this->db->select('IdPersonnage');
			$this->db->from('inscriptions');
			$this->db->where('IdIndividu', $idIndiv);
			$this->db->where('IdActivite', $idActiv);

			$query = $this->db->get();

			$result = $query->row();

			if(!is_null($result)){
				$idPerso = $result->IdPersonnage;
			} else{
				return false;
			}
		}

		$this->db->db_select('db_perso');

		if($typeActivite == 'CHRONIQ'){
			$strQuery = "SELECT Id
						FROM db_perso.personnages
						WHERE IdIndividu = $idIndiv
						AND CodeEtat IN ('ACTIF', 'NOUVO', 'LEVEL');";

			$query = $this->db->query($strQuery);

			$results = $query->result();
			$arrPersos = array();
			foreach ($results as $result) {
				array_push($arrPersos, $result->Id);
			}

			$this->db->where_in('IdDestinataire', $arrPersos);	
		}else{
			$this->db->where('IdDestinataire', $idPerso);
		}		
		$this->db->where('IdActivite', $idActiv);

		$query = $this->db->get('missives');

		if($query->num_rows() > 0){
			return true;
		} else{
			return false;
		}
	}

	public function has_quests($idPerso, $idActiv, $idIndiv = null){
		if(is_null($idPerso) && !is_null($idIndiv)){
			$this->db->db_select('db_activ');

			$this->db->select('IdPersonnage');
			$this->db->from('inscriptions');
			$this->db->where('IdIndividu', $idIndiv);
			$this->db->where('IdActivite', $idActiv);

			$query = $this->db->get();

			$result = $query->row();
			if(!is_null($result)){
				$idPerso = $result->IdPersonnage;
			} else{
				return false;
			}
		}

		$this->db->db_select('db_perso');

		$strQuery = "SELECT * FROM quetes WHERE IdPersonnage = $idPerso AND (CodeEtat = 'DEM' OR CodeEtat = 'ACTIF' OR CodeEtat = 'SUITE' )";

		$query = $this->db->query($strQuery);

		if($query->num_rows() > 0){
			return true;
		} else{
			return false;
		}
	}

	public function has_missions($idPerso, $idActiv = null, $idIndiv = null){
		if(is_null($idPerso) && !is_null($idIndiv)){
			$this->db->db_select('db_activ');

			$this->db->select('IdPersonnage');
			$this->db->from('inscriptions');
			$this->db->where('IdIndividu', $idIndiv);
			$this->db->where('IdActivite', $idActiv);

			$query = $this->db->get();

			$result = $query->row();
			if(!is_null($result)){
				$idPerso = $result->IdPersonnage;
			} else{
				return false;
			}
		}

		if($idActiv == null){
			return false;
		}
		
		$strQuery = "SELECT *
					FROM db_perso.missions
					WHERE IdActivite = $idActiv 
					AND IdChef = $idPerso
					AND FichierResultat IS NOT NULL";

		$query = $this->db->query($strQuery);

		if($query->num_rows() > 0){
			return true;
		} else{
			return false;
		}
	}

	public function getMissivesNonPNJ($idActiv){
		$this->db->db_select('db_perso');

		$strQuery = "SELECT Objet 
			FROM db_perso.missives 
			WHERE IdActivite = $idActiv 
			  AND (Type = 'PJ' OR ReponseA IS NOT NULL) 
			ORDER BY IdDestinataire;";

		$query = $this->db->query($strQuery);

		return $query->result();
	}

	public function getMissivesToPrint($lastPresence, $idPerso){
		$this->db->db_select('db_activ');
		$codeUnivers = $_SESSION['infoUnivers'];
		$today = date('Y-m-d H:i:s', time());
		#$today = '2024-07-12 17:00:00';

		$strQuery = "SELECT mis.Fichier, act.Nom, mis.Type
					FROM db_perso.missives mis
					LEFT JOIN db_activ.activites act ON act.Id = mis.IdActivite
					WHERE (act.DateDebut > '$lastPresence' AND act.DateDebut < '$today')
					AND (act.Type = 'GN' OR act.Type = 'CHRONIQ') 
					AND act.CodeUnivers = '$codeUnivers' 
					AND mis.IdDestinataire = $idPerso
					ORDER BY act.DateDebut ASC;";

		$query = $this->db->query($strQuery);

		if($query->num_rows() > 0){
			return $query->result();
		}else{
			return false;
		}
	}

	public function getQuetesToPrint($lastPresence, $idPerso){
		$this->db->db_select('db_activ');
		$codeUnivers = $_SESSION['infoUnivers'];
		$today = date('Y-m-d H:i:s', time());
		#$today = '2024-07-12 17:00:00';

		$strQuery = "SELECT que.Fichier, act.Nom
					FROM db_perso.quetes que
					LEFT JOIN db_activ.activites act ON act.Id = que.IdActivite
					WHERE (act.DateDebut > '$lastPresence' AND act.DateDebut < '$today')
					AND (act.Type = 'GN' OR act.Type = 'CHRONIQ') 
					AND act.CodeUnivers = '$codeUnivers' 
					AND que.IdPersonnage = $idPerso
					AND que.Format != 'FAVEUR'
					AND que.CodeEtat = 'ACTIF'
					ORDER BY act.DateDebut ASC;";

		$query = $this->db->query($strQuery);

		if($query->num_rows() > 0){
			return $query->result();
		}else{
			return false;
		}
	}

	public function getMissionsToPrint($lastPresence, $idPerso){
		$this->db->db_select('db_activ');
		$codeUnivers = $_SESSION['infoUnivers'];
		$today = date('Y-m-d H:i:s', time());
		#$today = '2024-07-12 17:00:00';

		$strQuery = "SELECT mis.FichierResultat, act.Nom
					FROM db_perso.missions mis
					LEFT JOIN db_activ.activites act ON act.Id = mis.IdActivite
					WHERE (act.DateDebut > '$lastPresence' AND act.DateDebut < '$today')
					AND (act.Type = 'GN' OR act.Type = 'CHRONIQ')
					AND act.CodeUnivers = '$codeUnivers' 
					AND mis.IdChef = $idPerso
					ORDER BY act.DateDebut ASC;";

		$query = $this->db->query($strQuery);

		if($query->num_rows() > 0){
			return $query->result();
		}else{
			return false;
		}
	}

	public function getMetiers($idPerso){
		$this->db->db_select('db_perso');

		$strQuery = "SELECT `Precision` 
					FROM db_perso.competences_acquises 
					WHERE IdPersonnage = $idPerso 
					AND CodeCompetence LIKE '%METIER%'
					ORDER BY CodeCompetence;";

		$query = $this->db->query($strQuery);

		return $query->result();
	}

	public function getActivePersos($idIndiv){
		$this->db->db_select('db_perso');

		$strQuery = "SELECT * 
					FROM db_perso.personnages 
					WHERE IdIndividu = $idIndiv 
					AND CodeEtat IN ('ACTIF', 'LEVEL', 'NOUVO')
					ORDER BY Id;";

		$query = $this->db->query($strQuery);

		return $query->result();
	}

	public function getGroups(){
		$this->db->db_select('db_group');

		$strQuery = "SELECT * 
					FROM db_group.groupes
					WHERE CodeEtat = 'ACTIF' 
					AND CodeUnivers = '" .$_SESSION['infoUnivers'] ."'
					ORDER BY Nom;";

		$query = $this->db->query($strQuery);

		return $query->result();
	}

	public function getMissivesByGroup($idGroupe, $idActivite){
		$this->db->db_select('db_perso');

		$strQuery = "SELECT Fichier 
					FROM db_perso.missives
					WHERE IdActivite = $idActivite
					AND IdDestinataire IN (SELECT IdPersonnage FROM db_group.membres WHERE IdGroupe = $idGroupe)";

		$query = $this->db->query($strQuery);

		return $query->result();
	}
}

/* End of file Personnages_model.php */
/* Location: ./application/models/Personnages_model.php */ ?>