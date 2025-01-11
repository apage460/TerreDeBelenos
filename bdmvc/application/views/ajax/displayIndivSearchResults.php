<script>
    $(function(){

        var controller = 'Inscriptions',
            base_url = '<?php echo site_url();?>', 
            data;



        $('.btn-addInscription').on('click',function(event){
            event.preventDefault();

            idActivite = $('#selectActivite :selected').val();
            idIndividu = $('#idIndividu').val();
            nomActivite = $('#selectActivite :selected').html();
            typeActivite = $('#selectActivite').attr('data-typeActivite');
            montant = $(this).attr('data-montant');

            addInscription(idActivite, idIndividu, nomActivite, typeActivite, montant);
        })

        function addInscription(idActivite, idIndividu, nomActivite, typeActivite, montant){
            var container = $('#searchResults');
            container.html('<i class="fa fa-cog fa-spin fa-3x fa-fw"></i>');

            $.ajax({
                'url' : base_url + '/' + controller + '/quickInscription',
                'type' : 'POST',
                'data' : {
                    'idActivite' : idActivite,
                    'idIndividu' : idIndividu,
                    'nomActivite' : nomActivite,
                    'typeActivite' : typeActivite,                    
                    'montant' : montant
                },
                'success' : function(data){
                    location.reload();
                },
                'error' : function(err){
                    console.log(err);
                }
            });
        }

        $('#selectActivite').delay(500).on('change',function(event){

            event.preventDefault();

            compte = $('#compte').val();
            prenomJoueur = $('#prenomJoueur').val();
            nomJoueur = $('#nomJoueur').val();
            idActiv = $('#selectActivite :selected').val();

            changeCouts(compte, prenomJoueur, nomJoueur, idActiv);
        })

        function changeCouts(compte, prenomJoueur, nomJoueur, idActiv){
            var container = $('#searchResults');
            container.html('<i class="fa fa-cog fa-spin fa-3x fa-fw"></i>');

            $.ajax({
                'url' : base_url + controller + '/searchIndividus',
                'type' : 'POST',
                'data' : {
                    'compte' : compte,
                    'prenomJoueur' : prenomJoueur,                    
                    'nomJoueur' : nomJoueur,
                    'idActiv' : idActiv
                },
                'success' : function(data){
                    if(data){
                        
                        container.html(data);
                        $('.idActiv').val(idActiv);
                    }
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
		<h3>Nombre de Résultat : <?php echo count($individus); ?></h3>
	</div>
	<div class="row">
		<div class="col-xs-12 col-md-4">
			<h3>Activité :</h3>
			<select name="selectActivite" id="selectActivite" class="form-control">
				<?php foreach ($activites as $activite) { ?>
					<option value="<?php echo $activite->Id; ?>" data-typeActivite="<?php echo $activite->Type; ?>" <?php if($activite->Id == $idActiv): echo 'selected="selected"'; endif; ?> >
						<?php echo $activite->Nom; ?>
					</option>
				<?php } ?>
			</select>
		</div>
	</div>
	<div class="row">
		<table class="table table-striped table-responsive">
			<tr>
				<th>Compte</th>
				<th>Prénom Joueur</th>
				<th>Nom Joueur</th>
				<th>Gratuit / Prix Régulier</th>
				<th>Prix Retard</th>
			</tr>
			<?php foreach ($individus as $individu) { ?>
				<tr>
					<td>
						<input type="hidden" id="idIndividu" value="<?php echo $individu->Id; ?>">
						<?php echo $individu->Compte; ?>
					</td>
					<td><?php echo $individu->Prenom; ?></td>
					<td><?php echo $individu->Nom; ?></td>
								
					<td>
                        <?php if ($individu->ActivitesGratuites > 0) : ?>
                        <?php echo form_open('inscriptions/activiteGratuite/' .$individu->Id .'/' .$individu->ActivitesGratuites); ?>
                        <input type="hidden" class="idActiv" name="idActiv" value="<?php echo $activites[0]->Id; ?>">
						
                        <button class="btn btn-success">Activité Gratuite</button>
                        
                        <?php echo form_close(); ?>
                        <?php endif; ?>
						<button class="btn btn-primary btn-addInscription" data-montant="<?php echo $couts->PrixRegulier; ?>" ><?php echo $couts->PrixRegulier; ?> $</button>
					</td>
					<td>
						<button class="btn btn-primary btn-addInscription" data-montant="<?php echo $couts->PrixRetard; ?>" ><?php echo $couts->PrixRetard; ?> $</button>
					
						<!--<button class="btn btn-primary btn-addInscription" data-montant="0.00" >0 $</button>
						<button class="btn btn-primary btn-addInscription" data-montant="20.00">20 $</button>
						<button class="btn btn-primary btn-addInscription" data-montant="25.00">25 $</button>
						<button class="btn btn-primary btn-addInscription" data-montant="40.00">40 $</button>-->						
					</td>
				</tr>

			<?php } //END FOREACH ?>
		</table>
        <div>
            <pre>
                <?php echo var_dump($individu); ?>
            </pre>
        </div>
	</div>
</div>	