<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
        	<div class="col-xs-12">
        		<h1>Gestion de <?php echo $infoPerso->Prenom .' ' .$infoPerso->Nom; ?></h1>
        		<h2>Joueur : <em><?php echo $infoIndiv->Prenom . ' ' . $infoIndiv->Nom; ?></em></h2>
        		
    		</div>
        </div>
        <div class="row">
        	<div class="col-md-7 col-xs-12">
        		<h3>Informations</h3>
        		<table class="table table-striped">
        			<tr>
        				<td><strong>Race</strong></td>
						<td><?php echo $infoPerso->Race; ?></td>
						<td></td>
					</tr>
        			<tr>
        				<td><strong>Classe</strong></td>
        				<td><?php echo $infoPerso->Classe; ?></td>
        				<td><!--
                            <?php if($_SESSION['infoUser']->NiveauAcces >= 6): ?>
                            <button type="button" class="btn btn-primary pop" data-pop="editClasse">Modifier la Classe <span class="fa fa-star"></span></button>
                            <?php endif; ?>-->          
                        </td>
    				</tr>
        			<tr>
        				<td><strong>Religion</strong></td>
        				<td><?php echo $infoPerso->Religion; ?></td>
        				<td>
                            <button type="button" class="btn btn-primary pop" data-pop="editReligion">Modifier la Religion <span class="fa fa-star"></span></button>
                        </td>
    				</tr>
        			<tr>
        				<td><strong>Niveau</strong></td>
        				<td><?php echo $infoPerso->Niveau; ?></td>
        				<td><?php //SI NiveauAccès >=6 pour BÉLÉ OU Autorisé pour Bélé Junior ?>
                            <?php if(($_SESSION['infoUser']->NiveauAcces >= 6 || $infoPerso->Niveau == 0) || ( $_SESSION['infoUnivers'] == 'BELEJR' && in_array($_SESSION['infoUser']->Id,[205]) ) ): //[PAMOORE]?>
                            <a href="<?php echo site_url('personnages/levelUP/' .$infoPerso->Id .'/' .$infoIndiv->Id .'/' .$infoPerso->Niveau ); ?>">
        						<button type="button" class="btn btn-primary pop" data-pop="lvlUp">LVL UP <span class="fa fa-arrow-up"></span></button>
    						</a>
                            <?php endif; ?>
    					</td>
    				</tr>
    				<tr>
    					<td><strong>État</strong></td>
    					<td>
	    					<?php echo $infoPerso->CodeEtat; ?>    						
    					</td>
    					<td></td>
    				</tr>
    				<tr>
        				<td><strong>Points de Vie</strong></td>
        				<td><?php echo $PV[0]->SommePV; ?></td>
        				<td></td>
    				</tr>
        		</table>
        	</div>
        	<div class="col-md-5 col-xs-12">
                <h3>Points de Vie</h3>
                <?php if($PV) : ?>
                    <table class="table table-reponsive table-striped">
                        <tr>
                            <th>Raison</th>
                            <th>Modif</th>
                            <th>Commentaire</th>
                        </tr>
                    <?php $totalPV = 0; ?>
                    <?php foreach ($PV as $rPV) : ?>
                        <?php $totalPV += intval($rPV->PV); ?>
                        <tr>
                            <td><?php echo $rPV->Raison; ?></td>
                            <td><?php echo $rPV->PV; ?></td>
                            <td><?php echo $rPV->Commentaires; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td><strong>Total</strong></td>
                        <td><strong><?= $totalPV; ?></strong></td>
                        <td></td>
                    </tr>
                    </table>
                <?php endif; ?>
        		<button class="btn btn-primary btn-lg btn-block pop" data-pop="declareDeath">Déclarer une Mort</button>
        	</div>
        </div>
        <div class="row">
            <?php if($_SESSION['infoUser']->NiveauAcces >= 5): ?>
                <div class="col-xs-12 col-md-5">
                    <h3>Titres</h3>
                    <button class="btn btn-primary pop" data-pop="addTitre">Ajouter un titre <span class="fa fa-star"></span></button><br><br>
                    <?php if(empty($titres) ): ?>
                        <h4>Ce joueur n'a aucun titre.</h4>                        
                    <?php else: ?>
                    <table class="table table-responsive table-striped">
                    <?php foreach ($titres as $titre) : ?>
                            <tr>
                                <td><strong><?php echo $titre->Titre ?></strong></td>
                                <td><?php echo $titre->Description .'<br><em>' . $titre->Avantages ?></em></td>
                                <td>
                                    <a href="<?php echo site_url('personnages/removeTitre') .'/' .$infoPerso->Id . '/' .$infoIndiv->Id .'/' .$titre->Id; ?>">
                                        <button class="btn btn-danger"><span class="fa fa-trash"></span></button>
                                    </a>
                                </td>
                            </tr>
                    <?php endforeach; ?>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endif; ?>            
            <div class=" <?php if($_SESSION['infoUser']->NiveauAcces >= 5): ?>col-md-offset-2<?php endif;?> col-md-5 col-xs-12">
                <h3>À remettre et valider</h3>
                <table class="table table-striped">
                    <tr>
                        <th>Item</th>
                        <th>Quantité</th>
                    </tr>
                    <tr>
                        <td><strong>A payé ( <?= $nextGN->Nom; ?> )</strong></td>
                        <td>
                            <strong>
                            <?php if($has_paid == true) : echo '<span style="color: green;">Oui</span>'; else : echo '<span style="color: red;">Non</span>'; endif;?>
                            </strong>
                        </td>
                    </tr>
                    <?php if($has_missives === true): ?>
                    <tr>
                        <td><strong>Missives</strong></td>
                        <td><span style="color: green;">Oui</span></td>
                    </tr>
                    <?php endif; ?>

                    <?php if($has_quests == true): ?>
                    <tr>
                        <td><strong>Quêtes</strong></td>
                        <td><span style="color: green;">Oui</span></td>
                    </tr>
                    <?php endif; ?>

                    <?php $mana = 0; ?>
                    <?php foreach ($skills as $skill) :
                        if($skill->UEC && ( strrpos($skill->CodeCompetence, 'MAGI') !== FALSE || strrpos($skill->CodeCompetence, 'ELMMAG') !== FALSE)){
                            $mana += floatval($skill->UEC);
                        } elseif($skill->UEC && ( strrpos($skill->CodeCompetence, 'ETUDEREL') !== FALSE || strrpos($skill->CodeCompetence, 'ETUDEARC') !== FALSE)){
                            $mana += floatval($skill->UEC);                                    
                        } elseif( $skill->CodeCompetence == 'RESERVE' ){
                            $mana += 20;
                        }
                    endforeach; 
                    if($mana > 0): ?>
                        <tr>
                            <td><strong>Éléments de magie</strong></td>
                            <td>
                                <?php $mana = 0; ?>
                                <?php foreach ($skills as $skill) :
                                    if($skill->UEC && ( strrpos($skill->CodeCompetence, 'MAGI') !== FALSE || strrpos($skill->CodeCompetence, 'ELMMAG') !== FALSE)){
                                        $mana += floatval($skill->UEC);
                                    } elseif($skill->UEC && ( strrpos($skill->CodeCompetence, 'ETUDEREL') !== FALSE || strrpos($skill->CodeCompetence, 'ETUDEARC') !== FALSE)){
                                        $mana += floatval($skill->UEC);                                    
                                    } elseif( $skill->CodeCompetence == 'RESERVE' ){
                                        $mana += 20;
                                    }
                                endforeach; ?>
                                <strong><?= $mana; ?></strong>
                            </td>
                        </tr>
                    <?php endif; ?>
                </table>
                <?php if($has_quests || $has_missives || $has_missions): ?>
                    <a href="<?= site_url('personnages/printAll/' . $infoPerso->Id .'/' .$nextGN->Id .'/' .$infoIndiv->Id); ?>" target="_blank"><button class="btn btn-primary btn-lg btn-block">Imprimer le tout  <span class="fa fa-print"></span></button></a>
                <?php endif; ?>
                <a href="<?= site_url('personnages/printMetierRez/' . $infoPerso->Id); ?>" target="_blank"><button class="btn btn-primary btn-lg btn-block">Imprimer les cartes  <span class="fa fa-print"></span></button></a>
            </div>
        </div>

        <div class="row">   
            <div class="col-xs-12 col-md-5">
                <h3>Conditions</h3>
                
                <table class="table table-striped">
                    <tr>
                        <th>Titre</th>
                        <th>Effet</th>
                    </tr>   
                    <?php foreach ($conditions as $condition) : ?>
                        <tr>
                            <td><?= $condition->Nom; ?></td>
                            <td><?= $condition->Description; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>  

        <div class="row">
        	<div class="col-md-7 col-xs-12">
        		<h3>Compétences</h3>
        		<h4>Ce personnage a <strong><?php echo $XP->XP; ?> XP</strong> en réserve.</h4>
                <a href="<?php echo site_url('personnages/editSkills') .'/' .$infoPerso->Id . '/' .$infoIndiv->Id; ?>">
                    <button class="btn btn-block btn-lg btn-primary" data-pop="editSkills" >Gérer les Compétences <span class="fa fa-edit"></span></button>
                </a>
        		<table class="table table-striped">
        			<tr>
        				<th></th>
        				<th>Nom Compétence</th>
        				<th>Type</th>
        				<th>UEC</th>
        				<th>Date d'acquisition</th>
        				<th>Code d'acquisition</th>        				
        			</tr>
        			<?php foreach ($skills as $skill) : ?>	
	        			<tr>
	        				<td>
                                <?php if($skill->UEC && strrpos($skill->CodeCompetence, 'MAGI') === FALSE && strrpos($skill->CodeCompetence, 'ELMMAG') === FALSE) :  ?>
                                    <span class="fa fa-lightbulb-o fa-spin fa-2x"></span>
                                <?php endif; ?>
                            </td>
	        				<td>
	        					<?php if($skill->specNom) : echo $skill->specNom; else : echo $skill->regNom; endif; ?>
	        				</td>
	        				<td><?php echo $skill->Type; ?></td>
	        				<td><?php echo $skill->UEC; ?></td>
	        				<td><?php echo $skill->DateCreation; ?></td>
	        				<td><?php echo $skill->CodeAcquisition; ?></td>
	        			</tr>
        			<?php endforeach; ?>
        		</table>
	        		
        	</div>
        	<div class="col-md-5 col-xs-12">  
                <h3>Sorts et Recettes</h3>
                <h4>&nbsp;</h4>
                <a href="<?php echo site_url('personnages/addSpell') .'/' .$infoPerso->Id . '/' .$infoIndiv->Id; ?>">
                    <button class="btn btn-block btn-lg btn-primary" data-pop="editSkills" >Ajouter un Sort niveau 1</button>
                </a>
                <table class="table table-responsive table-striped">
                    <tr>
                        <th>Compétence</th>
                        <th>Sort</th>
                    </tr>
                    <?php foreach($spells as $spell): ?>
                        <tr>
                            <td><?= $spell->cleanNom; ?></td>
                            <td><?= $spell->Precision; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
        	</div>
        </div>

        <div class="row">
        	<div class="col-md-8 col-xs-12 toPop" id="editReligion">
        		<h3>Modifier la Religion</h3>
	        	<?php echo form_open('personnages/editReligion/' .$infoPerso->Id .'/' .$infoIndiv->Id); ?>
					<table class="table">
						<tr>
							<td><strong>Religion Actuelle</strong></td>
							<td><?php echo $infoPerso->Religion; ?></td>
						</tr>
						<tr>
							<td><strong>Nouvelle Religion</strong></td>
							<td>
								<select name="newReligion" class="form-control">
									<?php foreach ($religions as $religion) : ?>
										<option value="<?php echo $religion->Code?>" <?php if($infoPerso->CodeReligion == $religion->Code) : echo 'selected="selected"'; endif; ?>>
											<?php echo $religion->Nom; ?>
										</option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
					</table>
					<button class="btn btn-primary btn-block">Modifier <span class="fa fa-star"></span></button>
	        	<?php echo form_close(); ?>
        	</div>
        </div>
        <div class="row">
            <div class="col-md-8 col-xs-12 toPop" id="editClasse">
                <h3>Modifier la Classe</h3>
                <?php echo form_open('personnages/editClasse/' .$infoPerso->Id .'/' .$infoIndiv->Id); ?>
                    <input type="hidden" name="codeRace" value="<?php echo $infoPerso->CodeRace; ?>">
                    <table class="table">
                        <tr>
                            <td><strong>Classe Actuelle</strong></td>
                            <td><?php echo $infoPerso->Classe; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Nouvelle Classe</strong></td>
                            <td>
                                <select name="newSubClasse" class="form-control">
                                    <?php foreach ($subClasses as $subClasse) : ?>
                                        <option value="<?php echo $subClasse->Code; ?>" >
                                            <?php echo $subClasse->Nom .' ( ' .$subClasse->subNom .' )'; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <button class="btn btn-primary btn-block">Modifier <span class="fa fa-star"></span></button>
                <?php echo form_close(); ?>
            </div>
        </div>
        <div class="row">
        	<div class="col-md-8 col-xs-12 toPop" id="declareDeath">
        		<h3>Déclarer une mort</h3>
	        	<?php echo form_open('personnages/declareMort/' .$infoPerso->Id .'/' .$infoIndiv->Id );?>
        			 <select name="declareMort" class="form-control">
                        <?php foreach($moyens_resurrection as $moyen): ?>
                            <option value="<?= $moyen->Nom; ?>"><?= $moyen->Nom; ?></option>
                        <?php endforeach; ?>
                            <option value="MortDefinitive">Mort Définitive</option>
                    </select>
					<label for="comment">Commentaires</label>
                    <input name="comment" class="form-control" placeholder="Facultatif" type="text">

                    <button class="btn btn-primary">Déclarer la Mort</button>
        		<?php echo form_close(); ?>
        	</div>
        </div>
        <div class="row">
            <div class="col-md-8 col-xs-12 toPop" id="addTitre">
                <h3>Ajouter un titre</h3>
                <?php echo form_open('personnages/addTitre/' .$infoPerso->Id .'/' .$infoIndiv->Id );?>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="titre">Titre</label>
                        <select name="titre" id="titre" class="form-control">
                            <?php foreach($allTitres as $allTitre) : ?>
                                <option value="<?php echo $allTitre->Code .'|' .$allTitre->Nom; ?>">
                                    <?php echo $allTitre->Nom; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                    <!--
                    <div class="form-group col-md-4">
                        <label for="description">Description</label><input name="description" type="text" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="avantages">Avantages</label><input name="avantages" type="text" class="form-control">
                    </div>-->
                <div class="row">
                    <div class="form-group col-md-4">
                        <button class="btn btn-primary">Ajouter un titre <span class="fa fa-star"></span></button>
                    </div>
                </div>
                <?php echo form_close(); ?>

            </div>
        </div>
        <div class="row">
            <div class="col-md-8 col-xs-12 toPop" id="lireBG">
                <h3>Historique du personnage</h3>
                <div class="col-md-8 col-md-offset-2 col-xs-12">
                    <?php echo $infoPerso->Histoire; ?>
                </div>
            </div>
        </div>

    </div>


</div>



<script>
	$(function(){
		$('.pop').on('click',function(){
			var target = '#' + $(this).attr('data-pop');

			$(target).bPopup({
				opacity : 0.8,
			});
		});
	});
</script>