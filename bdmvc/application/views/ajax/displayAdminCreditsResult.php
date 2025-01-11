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
                'url' : base_url + controller + '/addCredit',
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

    function editBenevolat(idIndiv, nomIndiv){
    	var controller = 'Administration',
            base_url = '<?php echo site_url();?>';
    	$('#editBenevolat').bPopup({
    		onOpen: function(){
    			$.ajax({
    				'url' : base_url + controller + '/getBenevolat/' + idIndiv,
    				'type' : 'GET',
    				'success' : function(data){
    					var data = JSON.parse(data);

    					console.log(data);

    					$('#editBenevolat table tr.added').remove();
    					$('#editBenevolat #saveBenevolat input[name="nombrePoints"]').val('');
    					$('#editBenevolat #saveBenevolat input[name="benevolatRaison"]').val('');

    					$('#editBenevolat .spin').hide();
    					$('#editBenevolat :not(.spin)').show();

    					$('#editBenevolat h3 span').html(nomIndiv);

    					var htmlListe = '',
    						totalPoints = 0;
    					data.forEach(function(el, index){
    						htmlListe += '<tr class="added">';
    						htmlListe += '<td>';
    						htmlListe += el.Raison;
    						htmlListe += '</td>';
    						htmlListe += '<td>';
    						htmlListe += el.Points;
    						htmlListe += '</td>';
    						htmlListe += '</tr>';

    						totalPoints += parseInt(el.Points);
    					});

						htmlListe += '<tr class="added">';
    						htmlListe += '<td>';
    						htmlListe += '<strong>Total</strong>';
    						htmlListe += '</td>';
    						htmlListe += '<td>';
    						htmlListe += totalPoints;
    						htmlListe += '</td>';
    						htmlListe += '</tr>'; 					

    					$('#editBenevolat table').append(htmlListe);

    					$('#editBenevolat #saveBenevolat input[name="nombrePoints"]').attr('min', -totalPoints);

    					$('input[name="benevolatIdIndiv"]').val(idIndiv);
    				},
    				'error' : function(err){
    					console.log(err);
    				}
    			})
    		}
    	});
    }

    function saveBenevolat(){
    	var controller = 'Administration',
            base_url = '<?php echo site_url();?>',
            idIndiv = $('input[name="benevolatIdIndiv"]').val();
    	$.ajax({
			'url' : base_url + controller + '/saveBenevolat/' + idIndiv,
			'type' : 'POST',
			'data' : {
				'idIndiv' : $('input[name="benevolatIdIndiv"]').val(),
				'nombrePoints' : $('input[name="nombrePoints"]').val(),
				'raison' : $('input[name="benevolatRaison"]').val(),
			},
			'success' : function(data){

			},
			'error' : function(err){
				console.log(err);
			}
		});
    }

    $('#saveBenevolat').on('submit', function(e){
    	e.preventDefault();
    	$('#editBenevolat').bPopup().close();
    	saveBenevolat();
    })



</script>

<div class="col-xs-12">
	<div class="row">
		<table class="table table-striped table-responsive">
			<tr>
				<th>Compte</th>
				<th>Prénom</th>
				<th>Nom</th>
				<th></th>
			</tr>
			<?php foreach ($individus as $individu) { ?>
				<tr>
							<td>
								<input type="hidden" id="idIndividu" value="<?php echo $individu->Id; ?>">
								<?php echo $individu->Compte; ?></h4>
							</td>
							<td><?php echo $individu->Prenom; ?></td>
							<td> <?php echo $individu->Nom; ?></td>										
							<td>
								<a href="<?php echo site_url('administration/getCreditsAndDebts')  .'/' .$individu->Id; ?> ">
									<button class="btn btn-primary"><span class="fa fa-eye fa-reverse"></span></button>
								</a>
								<button onclick="editBenevolat('<?= $individu->Id; ?>', '<?= $individu->Prenom .' ' .str_replace("'", "`", $individu->Nom); ?>');" data-pop="editBenevolat" class="btn btn-primary pop">Bélévolat 
									<span class="fa fa-gift fa-reverse"></span>
								</button>								
							</td>					
				</tr>

			<?php } //END FOREACH ?>
		</table>		
	</div>
</div>

<div id="editBenevolat" class="col-xs-6 toPop">
	<div class="spin"><span class="fa fa-spinner fa-pulse fa-3x fa-fw"></span></div>
	<div style="display:none;">
		<h3>Bénévolat de <span></span></h3>
		<div class="col-xs-6 col-md-4">
			<form id="saveBenevolat" data-idIndiv="" action="" method="POST">
				<input name="benevolatIdIndiv" type="hidden" value="">
				<label for="PointsBenevolat">Modifier les points de...</label>
				<br>
				<input class="form-control" name="nombrePoints" type="number" required="required">
				<em>Pour ajouter un (1) point, inscrire "1".</em><br>
				<em>Pour retirer un (1) point, inscrire "-1".</em>
				<br><br>
				<label for="PointsBenevolat">Raison</label>
				<input class="form-control" name="benevolatRaison" type="text" required>
				<button class="btn btn-primary">Sauvegarder <span class="fa fa-save"></span></button>
			</form>			
		</div>
		<div class="col-xs-6 col-md-8">
			<table class="table table-responsive table-striped">
				<tr>
					<th>Raison</th>
					<th>Qté</th>
				</tr>
			</table>
		</div>
		
	</div>
</div>

