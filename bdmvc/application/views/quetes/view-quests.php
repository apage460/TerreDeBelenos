<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h1 class="page-header">Voir les quêtes <?#= phpinfo();?></h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <div class="row">
        	<div class="col-xs-12">
        		<ul class="nav nav-tabs">
				    <li class="active"><a data-toggle="tab" href="#actif">En cours <span class="badge badge-light"><?php echo count($quetes['actif']); ?></span></a></li>
				    <li><a data-toggle="tab" href="#dem">Demandes <span class="badge badge-light"><?php echo count($quetes['dem']); ?></span></a></li>
				    <li><a data-toggle="tab" href="#print">Imprimer</a></li>
				</ul>

				<div class="tab-content">
					<div id="actif" class="tab-pane fade in active">
						<h3>Quêtes actives</h3>
						<table class="table-responsive table-striped table">
							<tr>
								<th>Nom du personnage</th>
								<th>Objet</th>
								<th>Suggestion</th>
								<th>État</th>
								<th>Responsable</th>
								<th></th>
							</tr>
						<?php foreach ($quetes['actif'] as $quete) : ?>
		        			<tr>
		        				<td><?php echo $quete->nomPerso; ?></td>
		        				<td><?php echo $quete->Objet; ?></td>
		        				<td><?php echo $quete->Suggestions; ?></td>
		        				<td><?php echo $quete->CodeEtat; ?></td>
		        				<td><?php echo $quete->nomRespo; ?></td>
		        				<td><a class="detailQuete" data-id-quete="<?php echo $quete->Id; ?>" href="#"><button class="btn btn-primary"><span class="fa fa-eye"></span></button></a></td>
		        			</tr>
	        			<?php endforeach; ?>
	        			</table>
					</div>
					<div id="dem" class="tab-pane fade">
						<h3>Demandes de quêtes</h3>
						<table class="table-responsive table-striped table">
							<tr>
								<th>Nom du personnage</th>
								<th>Objet</th>
								<th>Suggestion</th>
								<th>État</th>
								<th>Responsable</th>
								<th></th>
							</tr>
						<?php foreach ($quetes['dem'] as $quete) : ?>
		        			<tr>
		        				<td><?php echo $quete->nomPerso; ?></td>
		        				<td><?php echo $quete->Objet; ?></td>
		        				<td><?php echo $quete->Suggestions; ?></td>
		        				<td><?php echo $quete->CodeEtat; ?></td>
		        				<td><?php echo $quete->nomRespo; ?></td>
		        				<td><a href="#"><button class="btn btn-primary"><span class="fa fa-eye"></span></button></a></td>
		        			</tr>
	        			<?php endforeach; ?>
	        			</table>
					</div>
					<div id="print" class="tab-pane fade">
						<div class="row">
							<div class="col-xs-12 col-md-8 col-md-offset-2">
								<h3>Imprimer les quêtes pour <em><?= $nextGNName->Nom; ?></em></h3>
							</div>
						</div>
						<div class="row">
					    	<div class="col-xs-12 col-md-8 col-md-offset-2">
					    		<?php $chunkSize = 10; ?>
								
					    		<ul class="nav nav-tabs">
					    			<? $printPage = 1; ?>
					    			<?php for($i = 0; $i < count($quetesToPrint); $i += $chunkSize) : ?>
									    <li class="<? if(!isset($_GET['printPage']) && $printPage == 1): echo 'in active'; endif; ?>"><a data-toggle="tab" href="#page<?= $printPage; ?>">Page <?= $printPage; ?></a></li>
									    <? $printPage++; ?>
									<?php endfor; ?>
								</ul>
								<?php if(!empty($quetesToPrint)) : ?>
									<? $printPage = 1; ?>
									<div class="tab-content">
									<?php for($i = 0; $i < count($quetesToPrint); $i += $chunkSize) : ?>
										<div id="<?= 'page' . $printPage; ?>" class="tab-pane fade <? if(!isset($_GET['printPage']) && $printPage == 1): echo 'in active'; endif; ?>">
											<form action="<?= site_url('Quetes/printQuetes/' . $printPage); ?>" method="POST">

								    			<button class="btn btn-primary" id="download">Télécharger la sélection <span class="fa fa-download"></span></button>
												<?php $chunk = array_slice($quetesToPrint, $i, $chunkSize); ?>
												<table class="table table-striped table-responsive">
													<tr>
														<th>Quêtes</th>
														<th>Télécharger? <br> Tout sélectionner <input type="checkbox" name="all"></th>
													</tr>
													<?php foreach($chunk as $quete) : ?>
														<?php $formattedQuete = implode('/', array_splice(explode('/', $quete),7)); ?>
														<tr>
															<td>
																<a href="<?= '/BD/uploads/Quetes/' . $nextGNName->Nom .'/' . end(explode('/',$quete)); ?>" target="_blank">
																	<?= end(explode('/', $quete)); ?> <span class="fa fa-file-pdf-o"></span>
																</a>
															</td>
															<td>
																
																<input type="checkbox" name="quetes[]" value="<?= $formattedQuete; ?>" class="quetes">
															</td>
														</tr>
													<?php endforeach; ?>
												</table>
											</form>
										</div>
										<?php $printPage++; ?>
									<?php endfor; ?>
									</div>
								<?php endif; ?>
							</div>
						</div>												
					</div>
				</div>
	        </div>
        </div>
    </div>
</div>

<script>
	$('input[type="checkbox"][name="all"]').on('click', function(){
		$('input.quetes').not(this).prop('checked', this.checked);
	})

	/*$('#download').on('click', function(e){
		e.preventDefault();

		var checked = $('.quetes:checked'),
			data = [];

		checked.each(function(i, el){
			data[i] = $(el).val();
		});

		const chunkSize = 20;
		for (let i = 0; i < data.length; i += chunkSize) {
		    var chunk = JSON.stringify(data.slice(i, i + chunkSize));
		    $.ajax({
				'url' : '<?= site_url('Quetes/printQuetes'); ?>',
				'type' : 'POST',
				'data' : {chunk},
				'success' : function(data){
				},
				'error' : function(err){
					console.log(err);
				}
			});
		}

		
	});*/
</script>