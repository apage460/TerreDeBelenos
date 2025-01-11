<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h1 class="page-header">Consulter ou Annuler une inscription</h1>
            </div>        
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->

        <div class="row">
        	<div class="col-md-8 col-xs-12">
        		<h3>Choisir l'activité</h3> 
					
				<?php echo form_open('inscriptions/getInscriptions'); ?>

				<div class="col-md-4 col-xs-12">
		    		<select id="selectIdActiv" name="idActiv" class="form-control">
						<?php foreach ($activites as $activite) : ?>
							<option <?php if(isset($results) && $results[0]->IdActivite == $activite->Id): echo 'selected="selected"'; endif; ?> value="<?php echo $activite->Id; ?>"><?php echo $activite->Nom; ?></option>
						<?php endforeach; ?>
		    		</select>
		    	</div>
        	</div>
        </div>

        <br><br>
		
        <div class="row">
        	<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active">
					<a href="#inscriptions" aria-controls="inscriptions" role="tab" data-toggle="tab">Inscriptions</a>
				</li>
				<li role="presentation">
					<a href="#groupes"  aria-controls="groupes" role="tab" data-toggle="tab">Groupes</a>
				</li>
	        </ul>

	        <div class="tab-content col-md-8 col-xs-12">
	        	<div role="tabpanel" class="tab-pane active" id="inscriptions">
        	
		            

			        <div class="row">
				        <div class="col-xs-6">
				        	<button class="btn btn-primary btn-lg">Rechercher</button>
				        	<?php echo form_close(); ?>  
			    		</div>
			        </div>

			        <?php if( isset($noResults) ):?>
			        	<div class="row">
			        		<h3>Il ne reste aucune inscription pour cette activité.</h3>
			        	</div>
			    	<?php endif; ?>

			        <?php if (isset($results)): ?>
			        	<h3><?php echo count($results); ?> résultats correspondants</h3>
			        	<?php if($typeActivite == 'CHRONIQ'): ?>
			        		<h4><?= $totalRepas->totalRepas; ?> repas</h4>
			        	<?php endif; ?>
			        	<?php if(($_SESSION['infoUser']->NiveauAcces >= 6 && count($results) != 0 && $_SESSION['infoUnivers'] == 'BELE') || $_SESSION['infoUnivers'] == 'BELEJR'): ?>
				        	<div class="row">
				        		<div class="col-md-8 col-xs-12 btn-lg text-right">
				        			<?php if($_SESSION['infoUnivers'] == 'BELEJR'): ?>
				        				<a style="margin-right: 25%;" target="_blank" href="<?php echo site_url('inscriptions/downloadInscriptionList'.'/' .$results[0]->IdActivite); ?>">
					        				<button class="btn btn-primary">Télécharger la liste des inscrits</button>
					        			</a>
				        			<?php endif; ?>

				        			<a href="<?php echo site_url('inscriptions/deleteAllInscriptions'.'/' .$results[0]->IdActivite); ?>">
				        				<button class="btn btn-primary">Supprimer les inscriptions des absents</button>
				        			</a>
				        		</div>
				        	</div>
			        	<?php endif; ?>
			        	<?php if( count($results) != 0 ):?>
					        <div class="row">
								<div class="col-md-8 col-xs-12">
									<table class="table table-striped table-responsive">
										<tr>
											<td><strong>Nom du joueur</strong></td>
											<td><strong>Nom de personnage</strong></td>
											<?php if($typeActivite == 'CHRONIQ' || $typeActivite == 'TOURNOI') : ?>
												<td>
													<strong>Qté Repas</strong>
												</td>
											<?php endif; ?>
											<td><strong>Coût d'Inscription</strong></td>
											<td><strong></strong></td>
										</tr>
										<?php $totalRepas = 0; ?>
										<?php foreach ($results as $key => $result) : ?>
											<tr>
												<td><?php echo $result->NomIndivComplet; ?></td>
												<td><?php echo $result->NomPersonnage; ?></td>
												<?php if($typeActivite == 'CHRONIQ' || $typeActivite == 'TOURNOI') : ?>
													<td>
														<?= $result->Repas; ?>
														<?php $totalRepas += intval($result->Repas); ?>
													</td>
												<?php endif; ?>
												<td><?php echo $result->PrixInscrit; ?></td>
												<td>
													<a href="<?php echo site_url('inscriptions/deleteInscription') .'/' .$result->IdActivite .'/' .$result->IdIndividu .'/' .$result->IdPersonnage; ?>">
														<button class="btn btn-danger"><span class="fa fa-close"></span></button>
													</a>
												</td>
											</tr>
										<?php endforeach; ?>
										<tr>
											<td><strong>Total</strong></td>
											<td></td>
											<?php if($typeActivite == 'CHRONIQ' || $typeActivite == 'TOURNOI') : ?>
												<td><strong><?= $totalRepas; ?></strong> repas</td>
											<?php endif; ?>
											<td></td>
											<td></td>
										</tr>
									</table>
								</div>
					        </div>
				        <?php endif; ?>
			        <?php endif; ?>
			    </div> <!-- END INSCRIPTIONS PANEL -->
			    <div role="tabpanel" class="tab-pane" id="groupes">
			    	<h3>Groupes présents à l'activité</h3>

			    	<table class="table table-striped">
			    		<tr>
			    			<th>Nom du groupe</th>
			    			<th>Quantité de joueurs</th>
			    		</tr>
			    		<?php foreach($groupes as $groupe): ?>
				    		<tr>
				    			<td><?= $groupe->NomGroupe; ?></td>
				    			<td><?= $groupe->QteJoueurs; ?></td>
				    		</tr>
				    	<?php endforeach; ?>
			    	</table>
			    </div> <!-- END GROUPES PANEL -->
			</div>
		</div>
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->

<script>
	$('#selectIdActiv').on('change', function(){
		$('.idActiv').val( $('#selectIdActiv :selected').val() );
	})
</script>