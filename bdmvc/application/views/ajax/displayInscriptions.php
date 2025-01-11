<script type="text/javascript">
    $(function(){

    	var controller = 'Inscriptions',
            base_url = '<?php echo site_url();?>', 
            data;
        
        $('.btnPresence').on('click', function(event){            
            event.preventDefault();
            idIndividu = $(this).attr('data-idIndiv');
            idActivite = $('#selectActivite').val();
            commentaires = $(this).parent().siblings('.commentaires').children().val();
            montantDu = $(this).parent().siblings('.montantDu').html();
            nomActivite = $('#selectActivite :selected').html();
            
            addPresence(idIndividu, idActivite, commentaires, montantDu, nomActivite);            
        });
        
      
        function addPresence(idIndividu, idActivite, commentaires, montantDu, nomActivite){            
            $.ajax({
                'url' : base_url + '/' + controller + '/addPresence', 
                'type' : 'POST',
                'data' : {'idIndividu' : idIndividu, 'idActivite' : idActivite, 'commentaires' : null, 'montantDu' : montantDu, 'nomActivite' : nomActivite},
                'success' : function(data){
                	location.reload(); 
                },
                'error' : function(err){
                	console.log(err);
                }
            });
        }
    })
</script>

<div class="table-responsive">
	<table class="table table-striped">
		<tr>
			<th>Nom du Joueur</th>
			<th>Date de l'Inscription</th>
			<th>Crédit</th>
			<th>Prix Inscrit</th>
			<th></th>
		</tr>

		
		<?php foreach($inscriptions as $inscription){ ?>

		<tr>
			<td>
			<input type="hidden" value="<?php echo $inscription->IdIndividu; ?>" />
            <?php echo $inscription->Prenom . ' ' .$inscription->Nom; ?>			
			</td>
			<td><?php echo $inscription->DateInscription; ?></td>
			<td class="montantDu">
            
            <?php if($inscription->Montant_Du > 0){
                      echo $inscription->Montant_Du .' $'; 
                  }
                  else{ ?>
                      0 $
                  <?php } //ENDIF ?>
            </td>
			<td class="commentaires">
                <?php echo $inscription->PrixInscrit; ?>
            </td>
			<td>
                <?php if ($inscription->ActivitesGratuites > 0) : ?>
                        <?php echo form_open('inscriptions/activiteGratuite/' .$inscription->IdIndividu .'/' .$inscription->ActivitesGratuites); ?>
                        <input type="hidden" class="idActiv" name="idActiv" value="<?php echo $inscription->IdActivite; ?>">
                        
                        <button class="btn btn-success">Activité Gratuite</button>
                        
                        <?php echo form_close(); ?>
                        <?php endif; ?>
				<button class="btn btn-primary btnPresence" data-idIndiv="<?php echo $inscription->IdIndividu; ?>"><span class="fa fa-check fa-reverse"></span></button>
			</td>
		</tr>
		<?php } //END FOREACH ?>


		
	</table>

</div>