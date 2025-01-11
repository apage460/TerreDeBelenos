<?php

include_once('../configs/config.cfg');

// ═════ INCLUDES ═════
include_once('../models/user.class.php');
include_once('../services/database.class.php');



// ═════ INITIALIZATION ═════
session_start();

$_race_list = null;
	if( isset($_SESSION['racelist']) ) {$_race_list = $_SESSION['racelist'];}
$_comp_list = null;
	if( isset($_SESSION['complist']) ) {$_comp_list = $_SESSION['complist'];}

$_redirect = null;
$_message = ""; $_msg_class = "";

$_db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS);



// ═════ SESSION HANDLING ═════
if (isset($_SESSION['last_time_renewed']) && (time() - $_SESSION['last_time_renewed'] > SESSION_TIMEOUT)) {
	$_redirect = "../login.php";
}
else if( !isset($_SESSION['authenticated']) ) {$_redirect = "../login.php";}

$_SESSION['last_time_renewed'] = time();



// ═════ CONTROL ═════
if( isset($_POST['action']) ) {
	if( $_POST['action'] == 'get-data') { 
		$lQuery = 	"SELECT rac.Code
				 FROM db_pilot.races rac
				 WHERE rac.IndPermissionRequise = 0";

		$_db->SetQuery($lQuery);
		$_race_list = $_db->FetchResult();

		$lQuery = 	"SELECT comp.Code
				 FROM db_pilot.competences_regulieres comp
				 WHERE comp.CodeEtat = 'ACTIF'
				 ORDER BY comp.Categorie ASC, comp.Code ASC";

		$_db->SetQuery($lQuery);
		$_comp_list = $_db->FetchResult();

	}
	if( $_POST['action'] == 'add-data') { 
		foreach($_race_list as $race) {
			foreach($_comp_list as $comp) {
				$lQuery = 	"INSERT INTO db_pilot.cout_competences_reg (CodeCompReg, CodeRace, CoutXP) 
						 VALUES (:regcompcode, :racecode, 0)";

				$_db->SetQuery($lQuery);
					$_db->Bind(":regcompcode", $comp['Code'], PDO::PARAM_STR);
					$_db->Bind(":racecode", $race['Code'], PDO::PARAM_STR);
				$_db->FetchResult();
			}
		}
	}
}



// ═════ DATA COLLECTION ═════
$_SESSION['racelist'] = $_race_list;
$_SESSION['complist'] = $_comp_list;



// ═════ REDIRECTION ═════
$_db->CloseConnection();

if( isset($_redirect) ) { header('Location: ' . $_redirect); }



// ═════ HEADER ═════
require_once('includes/header.php');

echo '<DIV class="' . $_msg_class . '">' . $_message . '</DIV>';



// ═════ NAVIGATION ═════
echo '<DIV id="navigation">';
echo '<a href="main.php">Retour</a><br />';
echo '</DIV>';
echo '<br />';



// ═════ MAIN ═════
echo '<DIV id="main">';

// PATRON LIST
echo '<form method="post">';
echo '<input type="hidden" name="action" value="get-data"/>';

	echo '<center>';
	echo '<input type="submit" value="Obtenir valeurs"/>';

	echo '<br /><br />';

	echo 'LISTE DES RACES<br />';
	echo '---------------<br />';
	foreach($_race_list as $race) { 
		echo $race['Code'] .'<br />';
	}

	echo '<br /><br />';

	echo 'LISTE DES COMPÉTENCES<br />';
	echo '---------------<br />';
	foreach($_comp_list as $comp) { 
		echo $comp['Code'] .'<br />';
	}

	echo '</center>';
echo '</form>';


echo '<hr width=25% />';


// ADD-A-PATRON
echo '<form method="post">';
echo '<input type="hidden" name="action" value="add-data"/>';

	echo '<center>';

	echo '<input type="submit" value="Insérer les nouvelles données"/>';

	echo '</center>';

echo '</form>';

echo '</DIV>';



// ═════ FOOTER ═════
$footnote = "";
require_once('../includes/footer.php');

?>