<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
        	<div class="col-xs-12">
        		<h1>Gestion des compétences de <?php echo $infoPerso->Prenom .' ' .$infoPerso->Nom; ?></h1>
        		<h2>Joueur : <em><?php echo $infoIndiv->Prenom . ' ' . $infoIndiv->Nom; ?></em></h2>
        		<h3>
        			<a href="<?php echo site_url('personnages/editPersonnage/' .$infoPerso->Id .'/' .$infoIndiv->Id); ?>">
        				<span class="fa fa-chevron-left"></span>Retour à la fiche du Personnage
    				</a>
				</h3>
        		
    		</div>
        <div class="row">
        	<div class="col-md-7 col-xs-12">
        		<h3>Compétences</h3>
        		<table class="table table-striped">
        			<tr>
        				<th></th>
        				<th>Nom Compétence</th>
        				<th>Type</th>
        				<th>UEC</th>
        				<th>Date d'acquisition</th>
        				<th>Code d'acquisition</th>
        				<th></th>        				
        			</tr>
        			<?php foreach ($skills as $skill) : ?>        			
	        			<tr>
	        				<td><?php if($skill->UEC) :  ?><span class="fa fa-lightbulb-o fa-spin fa-2x"></span><?php endif; ?></td>
	        				<td>
	        					<?php if($skill->specNom) : echo $skill->specNom; else : echo $skill->regNom; endif; ?>
	        				</td>
	        				<td><?php echo $skill->Type; ?></td>
	        				<td><?php echo $skill->UEC; ?></td>
	        				<td><?php echo $skill->DateCreation; ?></td>
	        				<td><?php echo $skill->CodeAcquisition; ?></td>
	        				<td>
	        					<a href="<?php echo site_url('personnages/deleteSkill') .'/' .$infoPerso->Id .'/' .$infoIndiv->Id .'/' .$skill->Id .'/' .$skill->CodeEtat; ?>">
	        						<button class="btn btn-danger"><span class="fa fa-close"></span></button>
	        					</a>
	        				</td>
	        			</tr>
        			<?php endforeach; ?>
        		</table>
        	</div>
        	<div class="col-md-5 col-xs-12">
        		<h4>Ajouter une compétence</h4>
        		<h5>Ce personnage a <strong><?php echo $XP->XP; ?> XP</strong> en réserve.</h5>
        		<?php echo form_open('personnages/paySkill/' . $infoPerso->Id .'/' .$infoIndiv->Id); ?>
        			<select name="paySkill" id="paySkill" class="form-control">
                        <option value="">Compétences</option>
        				<?php foreach ($regSkills as $key => $regSkill) : ?>
                            
                            <option value="<?php echo $regSkill['code'] .',' .$regSkill['cost']; ?>" <?php if($regSkill['buyable'] == false || $regSkill['affordable'] == false): echo 'disabled="disabled"'; endif; ?>>
                                <?php echo $regSkill['name'] .' (' .$regSkill['cost'] .' XP)'; ?>
                            </option>

        				<?php endforeach; ?>
        			</select>
        			<button class="btn btn-block btn-primary">
        				Acheter la compétence <span class="fa fa-edit"></span>
    				</button>
        		<?php echo form_close(); ?>
                <br>
                <hr>
                <br>
                <h4>Donner une compétence</h4>
                <?php echo form_open('personnages/giveSkill/' . $infoPerso->Id .'/' .$infoIndiv->Id ); ?>
                        <select name="giveSkill" class="form-control">
                            <option value="">Compétences</option>
                        <?php foreach ($regSkills as $regSkill) : ?>
                                <?php if( substr($regSkill['code'], 0 , 5) == 'RECET' || substr($regSkill['code'], 0 , 4) == 'SORT') : ?>
                                    
                                    <option value="<?php echo $regSkill['code'] .',' . $regSkill['uses'] .',' .$regSkill['cost']; ?>">
                                        <?php echo $regSkill['name']; ?>
                                    </option>

                                <?php endif; ?>
                                
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-block btn-success">Donner la compétence <span class="fa fa-gift"></span></button>
                    <?php echo form_close(); ?>
        		<br>
        		<hr>
        		<br>
        		<?php if($_SESSION['infoUser']->NiveauAcces >= 7) : ?>
                    <h3>ADMIN SEULEMENT</h3>
        			<h4>Donner une compétence</h4>
	        		<?php echo form_open('personnages/giveSkill/' . $infoPerso->Id .'/' .$infoIndiv->Id ); ?>
	        			<select name="giveSkill" class="form-control">
	        				<option value="">Compétences</option>
                            <?php foreach ($regSkills as $regSkill) : ?>
                                <?php $uec = $regSkill['uses'] ;?>
                                <option value="<?php echo $regSkill['code'].',' .$uec .',' .$regSkill['cost']; ?>" >
                                    <?php echo $regSkill['name']; ?>
                                </option>
                            <?php endforeach; ?>
	        				<option value="">-------</option>
	        				<?php foreach ($specSkills as $specSkill) : ?>
	        					<option value="<?php echo $specSkill->Code .',' .$specSkill->Type; ?>">
	        						<?php echo $specSkill->Nom; ?>
        						</option>
	        				<?php endforeach; ?>
	        			</select>
						<button class="btn btn-block btn-success">Donner la compétence <span class="fa fa-gift"></span></button>
	        		<?php echo form_close(); ?>
        		<?php endif; ?>


        	</div>
        </div>

    </div>
</div>