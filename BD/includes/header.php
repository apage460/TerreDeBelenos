<!DOCTYPE html>

<html>
<head>
	<title>Base de données bélénoise</title>
	<meta charset="UTF-8">
	<meta name="description" content="Base de données bélénoise" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script type="text/javascript">
	function displayCountyInformationJS($inCountyListValue)
	{	
		var $lCountyData = $inCountyListValue.split(";");

		document.getElementById('leadername').innerHTML = "<b>Dirigeant du comté sélectionné : </b>" + $lCountyData[0];
		document.getElementById('leaderinfo').innerHTML = "<b>Description du dirigeant : </b><br />" + $lCountyData[1];
		document.getElementById('scribename').innerHTML = "<b>Scribe : </b>" + $lCountyData[2];	
	}
	</script>

<?php
if(PAYPAL_ENABLED) {	require_once('includes/paypal_sales_tools.php');	}
if(SQUARE_ENABLED) {	require_once('includes/square_sales_tools.php');	}

if( !defined('WORLD') ) {	echo '<link rel="stylesheet" type="text/css" href="configs/style.css">';		}
else 			{	echo '<link rel="stylesheet" type="text/css" href="configs/'.WORLD.'/style.css">';	}
?>
	<link rel="stylesheet" type="text/css" href="fonts/fonts.css">

</head>
