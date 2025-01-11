<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h1 class="page-header">Rechercher des personnages</h1>
            </div>        
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <hr/>
		
        <div class="row">
        	
            <h3>Identification du joueur</h3>            
			
			<?php echo form_open('personnages/searchPerso'); ?>

			<div class="form-group col-md-3 col-xs-12">
				<label for="pseudoIndiv">Pseudo</label>
				<input type="text" name="pseudoIndiv" class="form-control">
			</div>
			<div class="form-group col-md-3 col-xs-12">
				<label for="prenomIndiv">Prénom</label>
				<input type="text" name="prenomIndiv" class="form-control">
			</div>
			<div class="form-group col-md-3 col-xs-12">
				<label for="nomIndiv">Nom</label>
				<input type="text" name="nomIndiv" class="form-control">
			</div>
			<div class="form-group col-md-3 col-xs-12">	        		
        		<div class="col-xs-6">
        			<label for="ddnIndiv">Date de Naissance</label>
        			<select name="ddnIndivOp" class="form-control">
        				<option value="<=">Avant le</option>
        				<option value=">=">Après le</option>
        			</select>
        		</div>
        		<div class="col-xs-6">
        			<label for="ddnIndiv">&nbsp</label>
        			<input type="date" name="ddnIndiv" class="form-control">
				</div>
			</div>
         </div>

         <div class="row">
         	<h3>Identification du personnage</h3>
        	
        	<div class="form-group col-md-4 col-xs-12">
        		<label for="prenomPerso">Prénom</label>
        		<input type="text" name="prenomPerso" class="form-control">
			</div>
        	<div class="form-group col-md-4 col-xs-12">
        		<label for="nomPerso">Nom</label>
        		<input type="text" name="nomPerso" class="form-control">
			</div>
			<div class="form-group col-md-4 col-xs-12">	        		
        		<div class="col-xs-4">
        			<label for="niveauPerso">Niveau</label>
        			<select name="niveauPersoOp" class="form-control">
        				<option value="<=">Plus Petit</option>
        				<option value="=">Égal</option>
        				<option value=">=">Plus Grand</option>
        			</select>
        		</div>
        		<div class="col-xs-8">
        			<label for="niveauPerso">&nbsp</label>
        			<input type="input" name="niveauPerso" class="form-control">
				</div>
			</div>
			<div class="form-group col-md-4 col-xs-12">
				<label for="classePerso">Classes</label>
				<select name="classePerso" class="form-control">
						<option value="NULL">Choisir une Classe</option>
					<?php foreach ($classes as $classe) : ?>
						<option value="<?php echo $classe->Code; ?>"><?php echo $classe->Nom; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="form-group col-md-4 col-xs-12">
				<label for="racePerso">Races</label>
				<select name="racePerso" class="form-control">
					<option value="NULL">Choisir une Race</option>
					<?php foreach ($races as $race) : ?>
						<option value="<?php echo $race->Code; ?>"><?php echo $race->Nom; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="form-group col-md-4 col-xs-12">
				<label for="religionPerso">Religions</label>
				<select name="religionPerso" class="form-control">
					<option value="NULL">Choisir une Religion</option>
					<?php foreach ($religions as $religion) : ?>
						<option value="<?php echo $religion->Code; ?>"><?php echo $religion->Nom; ?></option>
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

        <?php if (isset($results)): ?>
        	<h3><?php echo count($results) ?> résultats correspondants</h3>
	        <div class="row">
				<div class="col-xs-12">
					<table class="table table-striped table-responsive">
						<tr>
							<td><strong>Pseudo</strong></td>
							<td><strong>Prénom</strong></td>
							<td><strong>Nom</strong></td>
							<td><strong>Personnages</strong></td>
						</tr>
						<?php foreach ($results as $key => $result) : ?>
							<tr>
								<td><?php echo $result['pseudoIndiv']; ?></td>
								<td><?php echo $result['prenomIndiv']; ?></td>
								<td><?php echo $result['nomIndiv']; ?></td>
								<td>
									<?php 	foreach ($result['Personnages'] as $personnage) : ?>
										<ul class="list-inline"">
											<li class="col-xs-4"><?php echo $personnage->Prenom; ?></li>
											<li class="col-xs-4"><?php echo $personnage->Nom; ?></li>
											<li class="col-xs-4" style="margin-bottom: 0.5em;">
												<a href="<?php echo site_url('personnages/editPersonnage') .'/' . $personnage->Id . '/' .$result['idIndiv'] ?>">
													<button class="btn btn-primary"><span class="fa fa-eye"></span></button>
												</a>
											</li>
										</ul>
									<?php endforeach; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</table>
				</div>
	        </div>
        <?php endif; ?>
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->