<div class="col-xs-12">
	<h3>Retirer la recette <?php echo $recette->Nom ?></h3>
</div>

<?php echo form_open('administration/deleteRecette/' .$recette->Id);?>

	<div class="form-group col-xs-12 align-center">
		<h4>Êtes-vous certain de vouloir retirer cette recette? <em>Faite ESC pour annuler</em></h4>
		<br><br>
		<button class="btn btn-danger btn-block">Oui <span class="fa fa-trash"></span></button>
	</div>

<?php echo form_close(); ?>
