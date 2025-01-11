<div class="row">
    <div class="col-xs-12">
		<h2>Refus d'une action</h2>

		<?php echo form_open('groupes/refusAction/' .$idGroupe .'/' .$idAction);?>

			<textarea name="raison" id="raison" cols="30" rows="10" class="form-control" >Raison OBLIGATOIRE</textarea>

			<button class="btn btn-danger" id="refusBtn" disabled="disabled">Refuser <span class="fa fa-close"></span></button>

		<?php echo form_close(); ?>

		<script>
			$('#raison').on('keyup', function(){
				if($(this).val().length > 20){
					$('#refusBtn').removeAttr('disabled');
				} else {
					$('#refusBtn').attr('disabled','disabled');
				}
			})
		</script>
	</div>
</div>