<div id="page-wrapper">
    <div class="container-fluid">
    	<div class="row">
    		<h1>Missives pour <?= $nextGNName; ?></h1>
    	</div>
	    <div class="row">
	    	<div class="col-xs-12 col-md-8 col-md-offset-2">
	    		<?php $chunkSize = 50; ?>
							
	    		<ul class="nav nav-tabs">
	    			<? $printPage = 1; ?>
	    			<?php for($i = 0; $i < count($missives); $i += $chunkSize) : ?>
					    <li class="<? if(!isset($_GET['printPage']) && $printPage == 1): echo 'in active'; endif; ?>"><a data-toggle="tab" href="#page<?= $printPage; ?>">Page <?= $printPage; ?></a></li>
					    <? $printPage++; ?>
					<?php endfor; ?>
					<li><a data-toggle="tab" href="#parGroupe">par Groupe</a></li>
				</ul>
				<?php if(!empty($missives)) : ?>
					<? $printPage = 1; ?>
					<div class="tab-content">
					<?php for($i = 0; $i < count($missives); $i += $chunkSize) : ?>
						<div id="<?= 'page' . $printPage; ?>" class="tab-pane fade <? if(!isset($_GET['printPage']) && $printPage == 1): echo 'in active'; endif; ?>">
							<form action="<?= site_url('Personnages/printMissives/' . $printPage); ?>" method="POST">

				    			<button class="btn btn-primary">Télécharger la sélection <span class="fa fa-download"></span></button>
								<?php $chunk = array_slice($missives, $i, $chunkSize); ?>
								<table class="table table-striped table-responsive">
									<tr>
					    				<th>Titre</th>
					    				<th>Télécharger? <br> Tout sélectionner <input type="checkbox" name="all"></th>
					    			</tr>
									<?php foreach($chunk as $missive) : ?>
										<?php $formattedQuete = implode('/', array_splice(explode('/', $quete),7)); ?>
										<tr>
											<td>
												<a href="<?= '/BD/uploads/Missives/' . $nextGNName .'/' . end(explode('/',$missive)); ?>" target="_blank">
													<?= end(explode('/', $missive)); ?> <span class="fa fa-file-pdf-o"></span>
												</a>
											</td>
											<td>
												
												<input type="checkbox" name="missives[]" value="<?= $missive; ?>" class="missive">
											</td>
										</tr>
									<?php endforeach; ?>
								</table>
							</form>
						</div>
						<?php $printPage++; ?>
					<?php endfor; ?>
						<div id="parGroupe" class="tab-pane fade">
							<h4>Imprimer les missives d'un groupe</h4>
							<form action="<?= site_url('Personnages/printMissivesByGroup'); ?>" target="_blank" method="POST">
								<input type="hidden" name="idActivite" value="<?= $nextGNId; ?>">

								<div class="col-md-6 col-xs-12">
									<select name="idGroup" id="idGroup" class="form-control">
										<?php foreach($groupes as $groupe) : ?>
											<option value="<?= $groupe->Id; ?>"><?= $groupe->Nom; ?></option>
										<?php endforeach; ?>
									</select>
									<br><br>	
									<button class="btn btn-primary btn-block">Afficher les missives <span class="fa fa-file-pdf-o"></span></button>
								</div>
							</form>
						</div>
					</div>
				<?php endif; ?>
	    	</div>
		</div>
	</div>
</div>

<script>
	$('input[type="checkbox"][name="all"]').on('click', function(){
		$('input.missive').not(this).prop('checked', this.checked);
	})
</script>
