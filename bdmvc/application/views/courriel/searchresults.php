<?php if(!empty($results)): ?>
	<table class="table table-responsive table-striped">
		<h3>Résultats de recherche</h3>
		<h4>( <span class="fa fa-star"></span> ) : Responsable du groupe sélectionné</h4>
		<tr>
			<th>Prénom</th>
			<th>Nom</th>
			<th>Courriel</th>
			<th></th>
		</tr>
		
		<?php foreach ($results as $result) : ?>
			<?php 
				$resp = false;
				if(isset($responsables)) {
					foreach ($responsables as $responsable) {
						if($responsable->IdResponsable == $result->Id){
							$resp = true;
						}
					}
				}
			?>
			<tr>
				<td>
					<?php echo $result->Prenom; ?>
					<?php if($resp) : echo '<span class="fa fa-star"></span>'; endif; ?>
				</td>
				<td><?php echo $result->Nom; ?></td>
				<td><?php echo $result->Courriel; ?></td>
				<td><button data-courriel="<?php echo strtolower($result->Courriel); ?>" class="btn btn-success addCourriel"><span class="fa fa-chevron-right"></span></button></td>
			</tr>
		<?php endforeach; ?>
	</table>
<?php else: ?>
	<h3>Aucun résultat. Désolé !</h3>
<?php endif; ?>

<script>
	$('.addCourriel').click(function(){
		var email = $(this).attr('data-courriel'),
			boolAdd = true;

		$('.emailContainer').each(function(index){
		 	if($(this).html() == email){
			 	boolAdd = false;
			}
		});

		if(boolAdd){
			$('#destinataires tr:last').after('<tr><td class="emailContainer">' + email +'</td><td><button class="btn btn-danger removeCourriel"><span class="fa fa-close"></span></button></td></tr>');
		}

		removeBtn = $('.removeCourriel');

		$.each(removeBtn, function(){
			removeBtn.on('click', function(){
				$(this).closest('tr').remove();
			});
		});
	});
</script>