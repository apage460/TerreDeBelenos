<script>
    $(function(){

        var controller = 'Administration',
            base_url = '<?php echo site_url();?>', 
            data;

        $('#btn-addCredit').on('click',function(event){
            event.preventDefault();

            idIndividu = $('#idIndividu').val();
            montant = $('#montant').val();
            raison = $('#raison').val();
            commentaires = $('#commentaires').val();

            addCredit(idIndividu, montant, raison, commentaires);
        })

        function addCredit(idIndividu, montant, raison, commentaires){
            var container = $('#searchResults');

            $.ajax({
                'url' : base_url + controller + '/addDebt',
                'type' : 'POST',
                'data' : {
                    'idIndividu' : idIndividu,                    
                    'montant' : montant,
                    'raison' : raison,
                    'commentaires' : commentaires
                },
                'success' : function(data){
                    location.reload();
                },
                'error' : function(err){
                    console.log(err);
                }
            });
        }
    });
</script>

<div class="col-xs-12">
	<div class="row">
		<table class="table table-striped table-responsive">
			<tr>
				<th>Compte - Prénom Nom</th>
				<th></th>
				<th></th>
				<th></th>
			</tr>
			<?php foreach ($individus as $individu) { ?>
				<tr>
					<table class="table">
						<tr>
							<td>
								<input type="hidden" id="idIndividu" value="<?php echo $individu->Id; ?>">
									<h4><span class="fa fa-arrow-right"></span> <?php echo $individu->Compte; ?> - <?php echo $individu->Prenom; ?> <?php echo $individu->Nom; ?></h4>
							</td>
							<td></td>
							<td></td>
										
							<td>
								<a href="<?php echo 'administration/getCreditsAndDebts/' .$individu->Id; ?> ">
									<button class="btn btn-primary"><span class="fa fa-eye fa-reverse"></span></button>
								</a>
							</td>
						</tr>
						<tr>							
							<td><label for="montant">Montant</label><input name="montant" id="montant" class="form-control" type="text"></td>
							<td><label for="raison">Raison</label><input name="raison" id="raison" class="form-control" type="text"></td>
							<td><label for="commentaires">Commentaires</label><input name="commentaires" id="commentaires" class="form-control" type="text"></td>
							<td><label>Ajouter</label>
							<button id="btn-addCredit" class="btn btn-primary btn-block btn-addInscription">$$$</button></td>
						</tr>

					</table>					
				</tr>

			<?php } //END FOREACH ?>
		</table>
		
	</div>
</div>	