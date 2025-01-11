<script type="text/javascript">
    $(function(){

    	var controller = 'Inscriptions',
            base_url = '<?php echo site_url();?>', 
            data;
        
        $('.btnDelPresence').on('click', function(event){            
            event.preventDefault();

            idIndividu = $(this).attr('data-idIndiv');
            idActivite = $('#selectActivite').val();
            
            addPresence(idIndividu, idActivite);            
        });
        
      
        function addPresence(idIndividu, idActivite){            
            $.ajax({
                'url' : base_url + '/' + controller + '/deletePresence', 
                'type' : 'POST',
                'data' : {'idIndividu' : idIndividu, 'idActivite' : idActivite},
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
			<th>Date de la prise de présence</th>
			<th>Commentaires</th>
			<th></th>
		</tr>

		
		<?php foreach($presences as $presence){ ?>

		<tr>
			<td>
			<input type="hidden" value="<?php echo $presence->IdIndividu; ?>" />
            <?php echo $presence->Prenom . ' ' .$presence->Nom; ?>			
			</td>
			<td><?php echo $presence->DateInscription; ?></td>
			<td class="commentaires"><textarea class="form-control"><?php echo $presence->Commentaires; ?></textarea></td>
			<td>
				<button class="btn btn-danger btnDelPresence" data-idIndiv="<?php echo $presence->IdIndividu; ?>"><span class="fa fa-trash fa-reverse"></span></button>
			</td>
		</tr>
		<?php } //END FOREACH ?>


		
	</table>
</div>