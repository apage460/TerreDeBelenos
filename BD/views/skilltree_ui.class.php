<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Skill Tree Views v1.2 r0 ==				║
║	Display skill trees' related UIs.			║
║	Requires skill tree model.				║
╚═══════════════════════════════════════════════════════════════╝
*/

include_once('models/skilltree.class.php');

class SkillTreeUI
{

protected $SkillTree;

public $Error;

	//--CONSTRUCTOR--
	public function __construct($inSkillTree)
	{
		$this->SkillTree = $inSkillTree;
	}


	//--DISPLAY SKILLS THE CHARACTER CAN BUY--
	public function DisplayBuyableSkills()
	{
		// Check if there's a character...
		if( $this->SkillTree == null ) { $this->Error = "No tree defined!"; return; }


		// Prepare data for the form
		$lList = $this->SkillTree->GetSkillTree();


		// Display!
		echo '<div>';
		echo '<span class="section-title">Acheter une nouvelle compétence</span>';
		echo '<hr width=70% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Compétences personnage" class="smalltext-button" />';
		echo 'Retour';
		echo '</button>';

		echo '</form>';

		// Skills
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character-lists"/>';

		echo '<table style="text-align: left;">';
		echo '<tr>';
		echo '<th class="black-cell" style="width:400px;">Compétence</th>';
		echo '<th class="black-cell" style="width:25px;">Prix</th>';
		echo '<th class="black-cell" style="width:50px;">Achats</th>';
		echo '<th class="black-cell" style="width:25px;"></th>';
		echo '</tr>';

		foreach($lList as $i => $item) {
			if( $item['buyable'] ) {
				$lButton = '';
				if( $item['affordable'] ) { $lButton = '<button type="submit" name="buy-skill" value="' .$item['code']. '" class="icon-button"/><img src="images/icon_plus.png" width="16" heigth="16"></button>'; }

				echo '<tr>';
				echo '<td class="white-cell" style="width:400px;">' .$item['name']. '</td>';
				echo '<td class="white-cell" style="width:25px;">' .$item['cost']. '</td>';
				echo '<td class="white-cell" style="width:50px;">' .$item['obtained']. ' / ' .$item['maxpurchases']. '</td>';
				echo '<td class="white-cell" style="width:25px;">' .$lButton. '</td>';
				echo '</tr>';
			}
		}

		echo '</table>';
		echo '</form>';

		echo '<hr width=70% />';

		echo '</div>';
	}


	//--DISPLAY SKILLS THE CHARACTER CAN REFUND--
	public function DisplayRefundableSkills()
	{
		// Check if there's a character...
		if( $this->SkillTree == null ) { $this->Error = "No tree defined!"; return; }


		// Prepare data for the form
		$lSkillList = $this->SkillTree->GetObtainedSkills();


		// Display!
		echo '<div>';
		echo '<span class="section-title">Annuler l\'achat d\'une compétence</span>';
		echo '<hr width=70% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Compétences personnage" class="smalltext-button" />';
		echo 'Retour';
		echo '</button>';

		echo '</form>';

		// Skills
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character-lists"/>';

		echo '<table style="text-align: left;">';
		echo '<tr>	<th class="black-cell" style="width:400px;">Compétence</th>  
				<th class="black-cell" style="width:25px;">Prix</th>  
				<th class="black-cell" style="width:50px;">Entraîné</th>  
				<th class="black-cell" style="width:25px;"></th></tr>';

		foreach($lSkillList as $i => $skill) {
			if( $skill['status'] == 'PRLVL' && !$this->SkillTree->IsActivePrerequisite($skill['code'])) 
			{
				$lSkillName = $skill['name'];
					if( $skill['precision'] ) { $lSkillName .= " - <i>" .$skill['precision']. "</i>"; }

				$lButton = '<button type="submit" name="refund-skill" value="' . $i . '" class="icon-button"/><img src="images/icon_delete.png" width="16" heigth="16"></button>';

				$lStyle = "background-color: lightgreen; font-weight: bold;";
				$lTrained = "Non"; 
					if( $skill['acquisition'] == 'RABAIS' ) { $lTrained = "Oui"; }
				
				echo '<tr>';
				echo '<td class="white-cell" style="width:400px; '.$lStyle.'">'	.$lSkillName.		'</td>';
				echo '<td class="white-cell" style="width:25px; '.$lStyle.'">'	.$skill['xpcost'].	'</td>';
				echo '<td class="white-cell" style="width:50px; '.$lStyle.'">'	.$lTrained.		'</td>';
				echo '<td class="white-cell" style="width:25px; '.$lStyle.'">'	.$lButton. 		'</td>';
				echo '</tr>';			
			}
		}

		echo '</table>';
		echo '</form>';

		echo '<hr width=70% />';

		echo '</div>';
	}


	//--DISPLAY SKILLS THE CHARACTER CAN BUY--
	public function DisplayBuyableTalents()
	{
		// Check if there's a character...
		if( $this->SkillTree == null ) { $this->Error = "No tree defined!"; return; }


		// Prepare data for the form
		$lTalentList = $this->SkillTree->GetTalentTree();
		$lCredits = $this->SkillTree->GetObtainedCredits();
		$lCreditTypes = array_keys($lCredits);


		// Display!
		echo '<div>';
		echo '<span class="section-title">Acquérir une compétence spéciale</span>';
		echo '<hr width=70% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Compétences personnage" class="smalltext-button" />';
		echo 'Retour';
		echo '</button>';

		echo '</form>';

		// Credits
		echo '<div style="width:250px; margin:auto; border:double black 3px;">';
		echo '<span class="section-title">Crédits de quête</span>';
		echo '<table style="text-align: left;">';
		echo '<tr>';
		echo '<th class="black-cell" style="width:125px;">Type</th>';
		echo '<th class="black-cell" style="width:75px;">Crédits</th>';
		echo '</tr>';

		foreach($lCreditTypes as $type) {
			$lTypeName = $this->SkillTree->GetTalentNameByCode($type);
				if(!$lTypeName) { $lTypeName = 'Universel'; }

			echo '<tr>';
			echo '<td class="white-cell" style="width:125px;">' .$lTypeName. '</td>';
			echo '<td class="white-cell" style="width:75px;">' .$lCredits[$type]. ' crédits</td>';
			echo '</tr>';
		}

		echo '</table>';
		echo '</div>';

		// Skills
		echo '<span class="section-title">Compétences accessibles</span>';
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character-lists"/>';

		echo '<table style="text-align: left;">';
		echo '<tr>';
		echo '<th class="black-cell" style="width:300px;">Compétence</th>';
		echo '<th class="black-cell" style="width:75px;">Type</th>';
		echo '<th class="black-cell" style="width:60px;">Prix</th>';
		echo '<th class="black-cell" style="width:25px;"></th>';
		echo '</tr>';

		foreach($lTalentList as $i => $item) {
			if( $item['buyable'] ) {
				$lType = 'Inconnu'; 
					if( $item['type'] == 'MINEURE' ) { $lType = 'Mineure'; } elseif( $item['type'] == 'MAJEURE' ) { $lType = 'Majeure'; }
				$lButton = '';
				if( $item['affordable'] ) { $lButton = '<button type="submit" name="buy-talent" value="' .$item['code']. '" class="icon-button"/><img src="images/icon_plus.png" width="16" heigth="16"></button>'; }

				echo '<tr>';
				echo '<td class="white-cell" style="width:300px;">' .$item['name']. '</td>';
				echo '<td class="white-cell" style="width:75px;">' .$lType. '</td>';
				echo '<td class="white-cell" style="width:60px;">' .$item['cost']. ' crédits</td>';
				echo '<td class="white-cell" style="width:25px;">' .$lButton. '</td>';
				echo '</tr>';
			}
		}

		echo '</table>';
		echo '</form>';

		echo '<hr width=70% />';

		echo '</div>';
	}


	//--DISPLAY SKILLS THE CHARACTER CAN REFUND--
	public function DisplayRefundableTalents()
	{
		// Check if there's a character...
		if( $this->SkillTree == null ) { $this->Error = "No tree defined!"; return; }


		// Prepare data for the form
		$lSkillList = $this->SkillTree->GetObtainedSkills();


		// Display!
		echo '<div>';
		echo '<span class="section-title">Annuler l\'achat d\'une compétence</span>';
		echo '<hr width=70% />';

		// Submenu
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="select-menu-option"/>';

		echo '<button type="submit" name="option" value="Compétences personnage" class="smalltext-button" />';
		echo 'Retour';
		echo '</button>';

		echo '</form>';

		// Skills
		echo '<form method="post">';
		echo '<input type="hidden" name="action" value="manage-character-lists"/>';

		echo '<table style="text-align: left;">';
		echo '<tr>	<th class="black-cell" style="width:400px;">Compétence</th>  
				<th class="black-cell" style="width:25px;">Prix</th>  
				<th class="black-cell" style="width:50px;">Entraîné</th>  
				<th class="black-cell" style="width:25px;"></th></tr>';

		foreach($lSkillList as $i => $skill) {
			if( $skill['status'] == 'PRLVL' && !$this->SkillTree->IsActivePrerequisite($skill['code'])) 
			{
				$lSkillName = $skill['name'];
					if( $skill['precision'] ) { $lSkillName .= " - <i>" .$skill['precision']. "</i>"; }

				$lButton = '<button type="submit" name="refund-skill" value="' . $i . '" class="icon-button"/><img src="images/icon_delete.png" width="16" heigth="16"></button>';

				$lStyle = "background-color: lightgreen; font-weight: bold;";
				$lTrained = "Non"; 
					if( $skill['acquisition'] == 'RABAIS' ) { $lTrained = "Oui"; }
				
				echo '<tr>';
				echo '<td class="white-cell" style="width:400px; '.$lStyle.'">'	.$lSkillName.		'</td>';
				echo '<td class="white-cell" style="width:25px; '.$lStyle.'">'	.$skill['xpcost'].	'</td>';
				echo '<td class="white-cell" style="width:50px; '.$lStyle.'">'	.$lTrained.		'</td>';
				echo '<td class="white-cell" style="width:25px; '.$lStyle.'">'	.$lButton. 		'</td>';
				echo '</tr>';			
			}
		}

		echo '</table>';
		echo '</form>';

		echo '<hr width=70% />';

		echo '</div>';
	}


} // END of SkillTreeUI class

?>
