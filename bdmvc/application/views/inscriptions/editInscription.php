<div id="page-wrapper">
    <div class="container-fluid">
   	

	    	<div class="row">
	    		<div class="col-xs-12">
	    			<h2>Éditer la présence de <?php echo $inscription->Prenom .' ' . $inscription->Nom; ?></h2>
	    			<h2>Pour <?php echo $inscription->NomActiv; ?></h2> 
	    		</div>
	    	</div>
		<?php if(!isset($inscription->NomPersonnage) && $hasPaid == null && ($typeActivite->Type != 'TOURNOI' && $typeActivite->Type != 'ACTEDEG')): ?>
	    	<div class="row">
	    		<?php echo form_open('inscriptions/addInscription/' .$inscription->Id .'/' .$inscription->IdActiv, array('class' => 'col-md-8 col-xs-12')); ?>

				
					<h3>Choisir quel personnage inscrire</h3>
					<select name="idPerso" id="" class="form-control">
						<?php foreach ($personnages as $personnage) : ?>
							<option value="<?php echo $personnage->Id; ?>"><?php echo $personnage->NomComplet; ?></option>
						<?php endforeach; ?>
					</select>
					<br><hr><br>
					<button class="btn btn-primary btn-block">Inscrire le joueur <span class="fa fa-check"></span></button>

				</div>


	    		<?php echo form_close(); ?>
	    	</div>
	    <?php elseif(($hasPaid == null && !isset($inscription->NomPersonnage)) && ($typeActivite->Type == 'TOURNOI' || $typeActivite->Type == 'ACTEDEG')): ?>
	    	<div class="row">
	    		<div class="col-xs-12 col-md-8 col-md-offset-2">
	    			<h3><?php echo $inscription->Prenom .' ' . $inscription->Nom; ?> est inscrit<?php if($inscription->Sexe == 'F'): echo 'e'; endif; ?> à <?= $inscription->NomActiv; ?></h3>
	    			<h4>Détails</h4>
		    		<ul>
		    			<li>Type de billet : <strong><?= $inscription->BilletPrincipal; ?></strong></li>
		    			<?php if($inscription->Enfants > 0): ?>
		    				<li>Enfants : <strong><?= $inscription->Enfants; ?></strong></li>
		    			<?php endif; ?>
		    			<?php if($inscription->Repas > 0): ?>
		    				<li>Repas : <strong><?= $inscription->Repas; ?></strong></li>
		    			<?php endif; ?>
		    			<li>Type d'inscription : <strong><?= $inscription->TypeInscription; ?></strong></li>
		    		</ul>


		    		<?php if($inscription->IndPrepaye == 0 && $indivHasPass == false): ?>
		    			<h4 style="color:red;"><span class="fa fa-times fa-danger"></span> Ce joueur n'a pas prépayé</h4>
		    		<?php else: ?>
		    			<h4 style="color:green;"><span class="fa fa-check fa-success"></span>Ce joueur a prépayé (<?= $inscription->PrixInscrit; ?> $)</h4>
		    		<?php endif; ?>

	    		</div>
	    	</div>
	    	<?php if($hasDebts[0]->Montant != null): ?>
    		<div class="row">
    			<div class="col-xs-12 col-md-8 col-md-offset-2">
	    			<h4 style="color:red;">    				
	    				Ce joueur a une dette ! ( <?php echo $hasDebts[0]->Montant; ?> $ ) - 
	    				<a href="<?php echo site_url('administration/getCreditsAndDebts/' .$inscription->IdIndividu); ?>" target="_blank"><button class="btn btn-primary">Consulter</button>
	    				</a>
	    				<form style="display: inline-block;">
							<button type="button" class="btn btn-primary" onClick="history.go(0)">
								<span class="fa fa-refresh"></span>
							</button>
						</form>
	    			</h4>	
				</div>
    		</div>
    		<?php endif; ?>
    		<div class="row">
	    		<?php echo form_open('inscriptions/addPresence/' .$inscription->Id .'/' .$inscription->IdActiv .'/', array('class' => 'col-md-8 col-xs-12')); ?>

			    	<input type="hidden" name="typeActivite" value="<?= $typeActivite->Type; ?>">

		    		<div class="col-xs-12 col-md-8 col-md-offset-2">
		    			<h4>À payer : </h4>
		    			<input type="text" class="form-control" name="montant" value="<?php echo $inscription->PrixInscrit; ?>">
		    			<input type="hidden" name="idActiv" value="<?php echo $inscription->IdActiv; ?>">
			    		<br>

		    			<?php if($inscription->ActivitesGratuites > 0) :?>
		    				<a href="<?php echo site_url('inscriptions/addFreePresence') .'/' .$inscription->Id .'/' .$inscription->IdActiv.'/' .$inscription->IdPersonnage; ?>">
		    					<button type="button" class="btn btn-success btn-block">Utiliser une Activité Gratuite !</button>
	    					</a>
		    				<br><br>
	    				<?php endif; ?>

	    				<?php if($_SESSION['infoUser']->NiveauAcces >= 5 && $_SESSION['infoUnivers'] == 'BELE'): ?>
	    					<a href="<?php echo site_url('inscriptions/addFreePresence') .'/' .$inscription->Id .'/' .$inscription->IdActiv.'/' .$inscription->IdPersonnage .'/isBenevole' ; ?>">
		    					<button type="button" id="inscriptionBenevole" class="btn btn-warning btn-block">
		    						Inscrire un bénévole
		    					</button>
	    					</a>
	    					<br><br>
		    			<?php endif; ?>

		    			<button type="submit" class="btn btn-primary btn-block">
		    				Ajouter la présence <span class="fa fa-check"></span>
		    			</button>			    			
		    		</div>			

	    		<?php echo form_close(); ?>
	    	</div>
	    <?php elseif(($hasPaid == null && isset($inscription->NomPersonnage)) && $isLocation == false ): ?>
	    	<div class="row">
	    		<div class="col-xs-12 col-md-8 col-md-offset-2">
	    			<h3><?php echo $inscription->Prenom .' ' . $inscription->Nom; ?> est inscrit<?php if($inscription->Sexe == 'F'): echo 'e'; endif; ?> avec son personnage :</h3>
	    			<h3><?php echo $inscription->NomPersonnage; ?></h3>
			    		<h4>Détails</h4>
			    		<ul>
			    			<li>Type d'inscription : <strong><?= $inscription->TypeInscription; ?></strong></li>
			    			<?php if($inscription->Enfants > 0): ?>
			    				<li>Enfants : <?= $inscription->Enfants; ?> </li>
			    			<?php endif; ?>
			    			<?php if($inscription->Repas > 0): ?>
			    				<li>Repas : <?= $inscription->Repas; ?> </li>
			    			<?php endif; ?>
			    			<?php if($inscription->GroupeCadre == 1 && $_SESSION['infoUnivers'] == 'BELEJR'): ?>
			    				<li>Groupe Cadre : <strong>OUI</strong></li>
			    			<?php endif; ?>
			    		</ul>

		    		
			    		<?php if($inscription->IndPrepaye == 0 && $indivHasPass == false ): ?>
			    			<h4 style="color:red;"><span class="fa fa-times fa-danger"></span> Ce joueur n'a pas prépayé</h4>
			    		<?php else: ?>
			    			<h4 style="color:green;"><span class="fa fa-check fa-success"></span>Ce joueur a prépayé (<?= $inscription->PrixInscrit; ?> $)</h4>
			    		<?php endif; ?>
			    </div>
		    </div>
		    <?php if($belePoints->belePoints > 0 && !is_null($belePoints) && $inscription->PrixInscrit > 0) : ?>
		    	<div class="row">
		    		<div class="col-xs-12 col-md-8 col-md-offset-2">
		    			<h3>Ce joueur a des BelePoints!</h3>
		    			<h4>Utiliser des BelePoints (1 BP = 20$) :</h4>

		    			<div class="col-md-3">
		    				<select class="form-control" name="belePoints" id="belePoints">
			    				<option value="0">0 BP</option>
			    				<?php for($i = 1; ($i <= $belePoints->belePoints && $i <= ($inscription->PrixInscrit/20)); $i++ ): ?>
			    					<option value="<?= $i ?>"><?= $i . ' BP'; ?></option>
			    				<?php endfor; ?>
			    			</select>
		    			</div>
		    		</div>
		    	</div>
		    <?php endif; ?>
	    	<?php if($hasDebts[0]->Montant != null): ?>
    		<div class="row">
    			<div class="col-xs-12 col-md-8 col-md-offset-2">
	    			<h4 style="color:red;">    				
	    				Ce joueur a une dette ! ( <?php echo $hasDebts[0]->Montant; ?> $ ) - 
	    				<a href="<?php echo site_url('administration/getCreditsAndDebts/' .$inscription->IdIndividu); ?>" target="_blank"><button class="btn btn-primary">Consulter</button>
	    				</a>
	    				<form style="display: inline-block;">
							<button type="button" class="btn btn-primary" onClick="history.go(0)">
								<span class="fa fa-refresh"></span>
							</button>
						</form>
	    			</h4>	
				</div>
    		</div>
    		<?php endif; ?>
	    	<div class="row">
	    		<?php echo form_open('inscriptions/addPresence/' .$inscription->Id .'/' .$inscription->IdActiv .'/' .$inscription->IdPersonnage, array('class' => 'col-md-8 col-xs-12')); ?>


			    	<input type="hidden" name="typeActivite" value="<?= $typeActivite->Type; ?>">
			    	<input type="hidden" name="belePointsUsed" value="0">

	    			<?php if($typeActivite->Type == 'CHRONIQ') : ?>
	    				<hr>
			    		<div class="row">
			    			<div class="col-xs-12 col-md-12">
			    				<h3>Informations relatives à la Chronique</h3>
			    			</div>
			    		</div>
			    		<div class="row">
			    			<div class="col-xs-12 col-md-6 col-md-offset-3">
			    				<?php if(!is_null($levelPerso)) : $niveau = $levelPerso->Niveau; else: $niveau = '0'; endif;?>
			    				<input type="hidden" name="niveauPersonnage" value="<?= $niveau; ?>">
			    			</div>
			    		</div>
			    		<div class="row">
			    			<div class="col-xs-12 col-md-6">
			    				<label for="classeAlternative">Classe pour l'événement</label>
			    				<select class="form-control" name="classeAlternative" id="classeAlternative">
			    					<?php foreach($availableSkills as $availableSkill){
			    						if($availableSkill->Categorie == 'A'){
			    							if($inscription->ClasseAlternative == 'Artisan'){
			    								echo '<option selected="selected" value="Artisan">';
			    							}else{
			    								echo '<option value="Artisan">';
			    							}
			    							echo 'Artisan';
			    							echo' </option>';
			    						} elseif($availableSkill->Categorie == 'G'){
			    							if($inscription->ClasseAlternative == 'Guerrier'){
			    								echo '<option selected="selected" value="Guerrier">';
			    							}else{
			    								echo '<option value="Guerrier">';
			    							}
			    							echo 'Guerrier';
			    							echo' </option>';
			    						} elseif($availableSkill->Categorie == 'M'){
			    							if($inscription->ClasseAlternative == 'Mage'){
			    								echo '<option selected="selected" value="Mage">';
			    							}else{
			    								echo '<option value="Mage">';
			    							}
			    							echo 'Mage';
			    							echo' </option>';
			    						} elseif($availableSkill->Categorie == 'P'){
			    							if($inscription->ClasseAlternative == 'Prêtre'){
			    								echo '<option selected="selected" value="Prêtre">';
			    							}else{
			    								echo '<option value="Prêtre">';
			    							}
			    							echo 'Prêtre';
			    							echo' </option>';
			    						} elseif($availableSkill->Categorie == 'V'){
			    							if($inscription->ClasseAlternative == 'Voleur'){
			    								echo '<option selected="selected" value="Voleur">';
			    							}else{
			    								echo '<option value="Voleur">';
			    							}
			    							echo 'Voleur';
			    							echo' </option>';
			    						}//endif
			    					} //endForeach ?>
			    				</select>
			    				<label for="groupe">Groupe</label>
			    				<select class="form-control" name="groupe" id="groupe">
			    					<option value="0--Aucun">Aucun Groupe</option>
			    					<?php foreach($groups as $group): ?>
			    						<option <?php if($inscription->IdGroupe == $group->Id): echo 'selected="selected"'; endif; ?> value="<?= $group->Id .'--' .$group->Nom; ?>">
			    							<?= $group->Nom; ?>
			    						</option>
			    					<?php endforeach; ?>
			    				</select>
			    				<label for="identite">Identite</label>
			    				<input type="text" class="form-control" name="identite" id="identite" value="<?= $inscription->Identite ?>">
			    			</div>
			    			<div class="col-xs-12 col-md-6">
			    				<?php 
			    				if($niveau < 5){
									$jetons = 3;
								}elseif($niveau < 9){
									$jetons = 4;
								}elseif($niveau < 14){
									$jetons = 5;
								}else{
									$jetons = 6;
								}
								?>
								<h4>Quantité de jetons : <strong><?= $jetons; ?></strong></h4>
			    			
			    				<!-- MISSIVES ET QUÊTES -->
			    				<?php if($has_missives != false || $has_quests != false ) :?>
			    					<h4><strong>Ce joueur a des missives et quêtes à récupérer!</strong></h4>
				    				<a target="_blank" href="<?= site_url('personnages/printAll/' . $inscription->IdPersonnage .'/' .$inscription->IdActivite .'/' .$inscription->IdIndividu); ?>" target="_blank"><button type="button" role="button" class="btn btn-primary btn-lg btn-block">Imprimer missives et quêtes  <span class="fa fa-print"></span></button></a>
				    			<?php endif; ?>
				    			<br>
				    			<?php if($inscription->TypeInscription != 'PNJ') : ?>
					    			<a href="<?= site_url('personnages/printMetierRez/' . $inscription->IdPersonnage .'/' .$inscription->IdActiv); ?>" target="_blank"><button type="button" role="button" class="btn btn-primary btn-lg btn-block">Imprimer les cartes métier <span class="fa fa-print"></span></button></a>
					    		<?php else: ?>
					    			<h4>Ce personnage n'a pas de métier.</h4>
					    		<?php endif; ?>
			    			</div>
			    		</div>
			    		<hr>
			    	<?php endif; ?>

		    		<div class="col-xs-12 col-md-8 col-md-offset-2">
		    			<h3>À payer : </h3>
		    			<div class="form-group">
		    				<h3>Le joueur paie sa passe.</h3>
		    					<?php foreach ($activePasses as $passe) : ?>
		    						<label><input class="passes" data-passe-cout="<?= $passe->Prix; ?>" name="paidPasses[]" value="<?= $passe->Id; ?>" type="checkbox"> <?= $passe->Nom;?></label>
		    						<br>
		    					<?php endforeach; ?>
		    			</div>
		    			<input type="text" class="form-control" data-montant="<?= $inscription->PrixInscrit; ?>" name="montant" value="<?php echo $inscription->PrixInscrit; ?>">
		    			<input type="hidden" name="idActiv" value="<?php echo $inscription->IdActiv; ?>">
			    		<br>

		    			<?php if($inscription->ActivitesGratuites > 0) :?>
		    				<a href="<?php echo site_url('inscriptions/addFreePresence') .'/' .$inscription->Id .'/' .$inscription->IdActiv.'/' .$inscription->IdPersonnage; ?>">
		    					<button type="button" class="btn btn-success btn-block">Utiliser une Activité Gratuite !</button>
	    					</a>
		    				<br><br>
	    				<?php endif; ?>

	    				<?php if($_SESSION['infoUser']->NiveauAcces >= 5 && $_SESSION['infoUnivers'] == 'BELE'): ?>
	    					<a href="<?php echo site_url('inscriptions/addFreePresence') .'/' .$inscription->Id .'/' .$inscription->IdActiv.'/' .$inscription->IdPersonnage .'/isBenevole' ; ?>">
		    					<button type="button" id="inscriptionBenevole" class="btn btn-warning btn-block">
		    						Inscrire un bénévole
		    					</button>
	    					</a>
	    					<br><br>
		    			<?php endif; ?>

		    			<button type="submit" class="btn btn-primary btn-block">
		    				Ajouter la présence <span class="fa fa-check"></span>
		    			</button>			    			
		    		</div>			

	    		<?php echo form_close(); ?>


	    		<div id="noMetier" class="col-xs-10 col-md-6 toPop">
	    			<h3>Ce personnage n'a pas de métier!</h3>

	    			<?php echo form_open('inscriptions/addMetier/' . $inscription->IdPersonnage . '/' . $inscription->IdIndividu . '/' . $inscription->IdActivite); ?>
	    			<div class="col-xs-4">
		    			<label for="metier">Métier</label>
		    			<select class="form-control" name="metier" id="metier">
		    				<?php foreach($metiers as $metier): ?>
		    					<option value="<?= $metier->Nom; ?>"><?= $metier->Nom; ?></option>
		    				<?php endforeach; ?>	
		    			</select>
		    			<br>
		    			<button class="btn btn-primary">Ajouter le métier</button>
		    		</div>
	    			<?php echo form_close(); ?>
	    		</div>

	    		
	    	</div>
    	<?php else: ?>
    		<div class="row">
    			<div class="col-xs-12 col-md-8 col-md-offset-2 text-center">
    				<h2><?php echo $inscription->Prenom .' ' . $inscription->Nom; ?></h2>
    				<h3>est inscrit<?php if($inscription->Sexe == 'F'): echo 'e'; endif; ?> avec son personnage :</h3>
	    			<h2><?php echo $inscription->NomPersonnage; ?>.</h2>
	    			<h3>Son inscription est maintenant payée et sa présence notée.</h3>
	    			<h2>Bon GN !</h2>

	    			<!-- MISSIVES ET QUÊTES -->
    				<?php if($has_missives != false || $has_quests != false ) :?>
    					<h4><strong>Ce joueur a des missives et quêtes à récupérer!</strong></h4>
	    				<a target="_blank" href="<?= site_url('personnages/printAll/' . $inscription->IdPersonnage .'/' .$inscription->IdActivite .'/' .$inscription->IdIndividu); ?>" target="_blank"><button type="button" role="button" class="btn btn-primary btn-lg ">Imprimer missives et quêtes  <span class="fa fa-print"></span></button></a>
	    				<br><br>
	    			<?php endif; ?>
	    			<?php if($inscription->TypeInscription != 'PNJ') : ?>
		    			<a href="<?= site_url('personnages/printMetierRez/' . $inscription->IdPersonnage .'/' .$inscription->IdActiv); ?>" target="_blank"><button type="button" role="button" class="btn btn-primary btn-lg">Imprimer les cartes métier <span class="fa fa-print"></span></button></a>
		    		<?php else: ?>
		    			<h4>Ce personnage n'a pas de métier.</h4>
		    		<?php endif; ?>

		    		<hr>

	    			<a href="<?php echo site_url('inscriptions') . '/index/' . $inscription->IdActiv; ?>">
	    				<button class="btn btn-primary btn-lg ">
	    					<span class="fa fa-chevron-right"></span>
	    					Inscrire un autre joueur
	    					<span class="fa fa-chevron-left"></span>
    					</button>
	    			</a>

    			</div>
    		</div>
    	<?php endif; ?>

    	<div id="rightToOrgani" class="col-xs-10 col-md-8 toPop text-center" style="color:red;">
			<h3><span class="fa fa-exclamation-triangle fa-spin fa-3x fa-fw"></span> ATTENTION <span class="fa fa-exclamation-triangle fa-spin fa-3x fa-fw"></span></h3>
			<h4>Ce joueur doit consulter un organisateur immédiatement.</h4>
			<h4>Arrêtez le processus d'inscription.</h4>
			<h5>(Appuyez sur Échap/Escape pour fermer cette fenêtre.)</h5>
		</div>
        
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->
<script>
	<?php if($hasNoMetier == true && $inscription->TypeInscription != 'PNJ'): ?>
		$('#noMetier').bPopup({
			modalClose: false
		});
	<?php endif; ?>

	<?php if(($typeActivite->Type != 'TOURNOI' && $typeActivite->Type != 'ACTEDEG') && $inscription->Id == 3556): ?>
		$('#rightToOrgani').bPopup({
			modalClose: false
		});
	<?php endif; ?>

	$('#belePoints').on('change', function(){
		var nbPoints = parseFloat($('#belePoints option:selected').val()),
			montantOG = parseFloat($('[name="montant"]').attr('data-montant')),
			newMontant = (montantOG - (nbPoints*20)).toFixed(2);

		$('[name="montant"]').val(newMontant);
		$('[name="belePointsUsed"]').val(nbPoints);
	});

	$('.passes').on('click', function(){
		var prix = parseFloat( $(this).attr('data-passe-cout') );
		var montant = parseFloat($('[name="montant"]').val() );

		if($(this).is(':checked')){			
			$('[name="montant"]').val( (montant + prix).toFixed(2) );
		}else{
			$('[name="montant"]').val( (montant - prix).toFixed(2) );
		}
	});

</script>

