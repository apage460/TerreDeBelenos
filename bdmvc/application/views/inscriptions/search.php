<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h1 class="page-header">Rechercher des joueurs</h1>
            </div>        
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <hr/>
		
        <div class="row">
        	
          <h3>Identification du joueur</h3>            
			
			<?php echo form_open('inscriptions/searchIndiv/' . $idActiv); ?>

    			<div class="form-group col-md-3 col-xs-12">
    				<label for="compteIndiv">Compte</label>
    				<input type="text" name="compteIndiv" class="form-control">
    			</div>
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

          <div class="form-group col-md-4 col-xs-12">
            <select id="selectIdActiv" <?php if($_SESSION['infoUser']->NiveauAcces <= 5): echo 'disabled="disabled"'; endif; ?> name="idActiv" class="form-control">
              <?php foreach ($activites as $key => $activite) : ?>
                <option value="<?php echo $activite->Id; ?>" <?php if($idActiv !== null && $activite->Id == $idActiv): echo 'selected="selected"'; endif; ?> >
                  <?php echo $activite->Nom; ?>
                </option>
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
				<div class="col-md-8 col-xs-12">
					<table class="table table-striped table-responsive">
						<tr>
							<td><strong>Compte</strong></td>
							<td><strong>Pseudo</strong></td>
							<td><strong>Prénom </strong></td>
							<td><strong>Nom</strong></td>
							<td><strong></strong></td>
						</tr>
						<?php foreach ($results as $key => $result) : ?>
							<tr>
								<td><?php echo $result->Compte; ?></td>
								<td><?php echo $result->Pseudo; ?></td>
								<td><?php echo $result->Prenom; ?></td>
								<td><?php echo $result->Nom; ?></td>
								<td>
									<?php echo form_open('inscriptions/editInscription/' .$result->Id .'/null' ); ?>
										<input type="hidden" id="idActiv" name="idActiv" value="<?php if($idActiv == null): echo $activites[0]->Id; else: echo $idActiv; endif; ?>">
										
										<button class="btn btn-primary"><span class="fa fa-edit"></span></button>

									<?php echo form_close(); ?>
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

<script>
	$('#selectIdActiv').on('change', function(){
		$('#idActiv').val( $('#selectIdActiv :selected').val() );
	})
</script>