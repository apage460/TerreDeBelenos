<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h1 class="page-header">Consulter les présences</h1>
            </div>        
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
		
        <div class="row">
        	
            <h3>Choisir l'activité</h3>            
			
			<?php echo form_open('inscriptions/getPresences'); ?>

			<div class="col-md-4 col-xs-12">
	    		<select id="selectIdActiv" name="idActiv" class="form-control">
					<?php foreach ($activites as $activite) : ?>
						<option <?php if(!empty($results['presences']) && $results['presences'][0]->IdActivite == $activite->Id): echo 'selected="selected"'; endif; ?> value="<?php echo $activite->Id; ?>"><?php echo $activite->Nom; ?></option>
					<?php endforeach; ?>
	    		</select>
	    	</div>
        </div>

        <div class="row">
	        <div class="col-xs-6">
	        	<button class="btn btn-primary btn-lg">Rechercher</button>
	        	<?php echo form_close(); ?>  
    		</div>
        </div>

        <br><br>

        <?php if (isset($results) && (!empty($results['presences']) || !empty($results['inscriptions']) )): ?>
        <div class="row">
	      	<div class="col-xs-12">
		        <ul class="nav nav-tabs" role="tablist">
		          <li role="presentation" class="active">
		            <a href="#inscriptions" aria-controls="inscriptions" role="tab" data-toggle="tab">Inscriptions</a>
		          </li>
		          <li role="presentation">
		            <a href="#presences"  aria-controls="presences" role="tab" data-toggle="tab">Présences</a>
		          </li>
		        </ul>

		        <div class="tab-content col-xs-12">

			        <div role="tabpanel" class="tab-pane active" id="inscriptions">
			        	<?php //echo '<pre>'; var_dump($results['inscriptions']); ?>
			        	<?php $inscriptions = $results['inscriptions']; ?>

			        	<div class="col-md-6 col-xs-12">
			        		<?php if($inscriptions[0]->actType == "CHRONIQ" || $inscriptions[0]->actType == "GN" || $inscriptions[0]->actType == 'TOURNOI'): ?>

			        		<h3><span class="fa fa-cutlery"></span>&nbsp;Nombre total de repas : <?= $inscriptions[0]->totalRepas; ?></h3>
			        		<br>
							<table class="table table-striped table-responsive">								
									<tr>
										<th>Nom du Joueur</th>
										<th>Nb Enfants</th>
										<th>Nb Repas</th>
										<th>Prépayé</th>
										<th></th>
									</tr>
									<?php foreach($inscriptions as $inscription): ?>
										<tr>
											<td><?= $inscription->nomJoueur; ?></td>
											<td><?= $inscription->Enfants; ?></td>
											<td><?= $inscription->Repas; ?></td>
											<td>
												<?php if($inscription->IndPrepaye == 1): ?>
													<span style="color:green;" class="fa fa-check"></span>
												<?php else: ?>
													<span style="color:red;" class="fa fa-times"></span>
												<?php endif; ?>
											</td>
											<td>
												<a href="<?= site_url('inscriptions/editInscription/') .$inscription->IdIndividu .'/' .$inscription->Id; ?>">
													<button class="btn btn-primary"><span class="fa fa-arrow-right"></span></button>
												</a>
											</td>
										</tr>
									<?php endforeach; ?>
							</table>
							<?php endif; ?>

						</div>

			    	</div><!-- /.tab-pane -->

		        	<div role="tabpanel" class="tab-pane" id="presences">
		        		<?php $presences = $results['presences']; ?>
		        		<?php if(count($presences)== 0) : ?>
		        			<div class="row">
		        				<div class="col-xs-12">
		        					<h3>Il n'y a aucune présence d'enregistrée pour l'instant.</h3>
		        				</div>
		        			</div>
		        		<?php else: ?>
				        	<div class="row">
				        		<div class="col-xs-12">
						        	<a href="<?php echo site_url('inscriptions/downloadPresencesList' .'/' .$presences[0]->IdActivite); ?>">
					    	    		<button class="btn-primary btn-lg">Télécharger la liste  (.csv) <span class="fa fa-file-excel-o"></span></button>
					        		</a>
				        		</div>
				        	</div>	
				        	<div class="row">
					        	<div class="col-md-6 col-xs-12">
						        	<div class="col-xs-6 text-left">
							        	<h3><?php echo count($presences) ?> résultats correspondants</h3>
						        	</div>
						        	<?php if($_SESSION['infoUser']->NiveauAcces >= 6): ?>
							        	<div class="col-xs-6 text-right">
							        		<h3>Montant total : <?php echo number_format($total,2); ?> $</h3>
							        	</div>
						        	<?php endif; ?>
					        	</div>
				        	</div>
					        <div class="row">
								<div class="col-md-6 col-xs-12">
									<table class="table table-striped table-responsive">
										<tr>
											<td><strong>Nom du joueur</strong></td>
											<td><strong>Reçu</strong></td>
										</tr>
										<?php foreach ($presences as $presence) : ?>
											<tr>
												<td><?php echo $presence->NomIndivComplet .'<em> (' .$presence->Compte .')</em>'; ?></td>
												<td><?php echo $presence->Recu; ?></td>
											</tr>
										<?php endforeach; ?>
									</table>
								</div>
					        </div>
					    <?php endif; ?>
			    	</div><!-- /.tab-pane -->
			    </div>
		    </div>
    	</div>  
        <?php endif; ?>
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->