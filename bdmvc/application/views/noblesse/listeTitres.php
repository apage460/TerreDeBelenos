<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h1 class="page-header">Liste des titres</h1>
            </div>        
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->

        <div class="row">
        	<div class="col-xs-12">
                <h3>Barons</h3>
        		<table class="table table-responsive table-striped">
        			<tr>
        				<th class="col-xs-10 col-md-5">Personnage <em>(Joueur)</em></th>
        				<th class="col-xs-2 col-md-1"></th>
        				<th class="col-xs-10 col-md-5">Personnage <em>(Joueur)</em></th>
        				<th class="col-xs-2 col-md-1"></th>
        			</tr>
        			<?php $count = 0; ?>
        			<?php foreach($titres as $titre) : ?>
                        <?php if($titre->Titre == 'Baron') : ?>
        				<?php if($count == 0) : ?>
                            
            					<tr>
            						<td class="col-xs-10 col-md-5">
            							<?php echo $titre->persoNom ?> <em>(<?php echo $titre->indivNom; ?>)</em>
            							<br>
            							<strong><?php echo $titre->Titre; ?></strong>&nbsp;<em>(<?php echo $titre->Description; ?>)</em>
            						</td>
            						<td class="col-xs-2 col-md-1">
            							<a href="<?php echo site_url('personnages/editPersonnage') .'/' .$titre->idPerso .'/' .$titre->idIndiv; ?>">
            								<button class="btn btn-primary"><span class="fa fa-eye"></span></button>
            							</a>
            						</td>
            				<?php $count++; else: ?>
            						<td class="col-xs-10 col-md-5">
            							<?php echo $titre->persoNom ?> <em>(<?php echo $titre->indivNom; ?>)</em>
            							<br>
            							<strong><?php echo $titre->Titre; ?></strong>&nbsp;<em>(<?php echo $titre->Description; ?>)</em>
            						</td>
            						<td class="col-xs-2 col-md-1">
            							<a href="<?php echo site_url('personnages/editPersonnage') .'/' .$titre->idPerso .'/' .$titre->idIndiv; ?>">
            								<button class="btn btn-primary"><span class="fa fa-eye"></span></button>
            							</a>
            						</td>
            					</tr>
                            <?php $count=0; endif; ?>
        				<?php  endif; ?>
        			<?php endforeach; ?>
        		</table>
        	</div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <h3>Titres de prestige</h3>
                <table class="table table-responsive table-striped">
                    <tr>
                        <th class="col-xs-10 col-md-5">Personnage <em>(Joueur)</em></th>
                        <th class="col-xs-2 col-md-1"></th>
                        <th class="col-xs-10 col-md-5">Personnage <em>(Joueur)</em></th>
                        <th class="col-xs-2 col-md-1"></th>
                    </tr>
                    <?php $count = 0; ?>
                    <?php foreach($titres as $titre) : ?>
                        <?php if($titre->Titre != 'Baron') : ?>
                        <?php if($count == 0) : ?>
                            
                                <tr>
                                    <td class="col-xs-10 col-md-5">
                                        <?php echo $titre->persoNom ?> <em>(<?php echo $titre->indivNom; ?>)</em>
                                        <br>
                                        <strong><?php echo $titre->Titre; ?></strong>&nbsp;<em>(<?php echo $titre->Description; ?>)</em>
                                    </td>
                                    <td class="col-xs-2 col-md-1">
                                        <a href="<?php echo site_url('personnages/editPersonnage') .'/' .$titre->idPerso .'/' .$titre->idIndiv; ?>">
                                            <button class="btn btn-primary"><span class="fa fa-eye"></span></button>
                                        </a>
                                    </td>
                            <?php $count++; else: ?>
                                    <td class="col-xs-10 col-md-5">
                                        <?php echo $titre->persoNom ?> <em>(<?php echo $titre->indivNom; ?>)</em>
                                        <br>
                                        <strong><?php echo $titre->Titre; ?></strong>&nbsp;<em>(<?php echo $titre->Description; ?>)</em>
                                    </td>
                                    <td class="col-xs-2 col-md-1">
                                        <a href="<?php echo site_url('personnages/editPersonnage') .'/' .$titre->idPerso .'/' .$titre->idIndiv; ?>">
                                            <button class="btn btn-primary"><span class="fa fa-eye"></span></button>
                                        </a>
                                    </td>
                                </tr>
                            <?php $count=0; endif; ?>
                        <?php  endif; ?>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->