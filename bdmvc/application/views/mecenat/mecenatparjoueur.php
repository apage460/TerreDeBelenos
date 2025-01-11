<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Mécénat de <?php echo $infoJoueur->Prenom .' ' .$infoJoueur->Nom ?></h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <?php if(validation_errors()): ?>

            <div class="row errors-container"><span class="fa fa-exclamation-triangle fa-4x"><?php echo validation_errors(); ?></div>

        <?php endif; ?>
        
        <div class="row">
            <div class="col-xs-12">
                <h3>Ajouter du Mécénat</h3>
                <?php echo form_open('mecenat/addMecenat/' .$infoJoueur->Id); ?>
                <div class="form-group col-md-6 col-xs-12">
                    <label for="projet">Projet</label>
                    <input type="text" class="form-control" name="projet" placeholder="Nom du projet">
                </div>
                <div class="form-group col-md-6 col-xs-12">
                    <label for="montant">Montant (NE PAS METTRE LES DECIMALES)</label>
                    <input type="text" class="form-control" name="montant" placeholder="Montant">
                </div>
                
                <div class="form-group col-md-6 col-xs-12">
                    <button id="searchJoueurs" type="submit" class="btn btn-primary">Ajouter le Mécénat</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
        <?php if(!empty($infoMecenat)): ?>
           <div class="row">
                <div class="col-xs-12">
                    <h2>Mécénat du joueur</h2>
                    <table class="table table-striped table-responsive">
                        <tr>
                            <th>Projet</th>
                            <th>Montant</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                        <?php foreach ($infoMecenat as $mecenat): ?>
                            <tr>
                                <td><?php echo $mecenat->Projet; ?></td>
                                <td><?php echo $mecenat->Montant; ?></td>
                                <td><?php echo $mecenat->DateInscription; ?></td>
                                <td>
                                    <a href="<?php echo site_url('mecenat/deleteMecenat'); ?>/<?php echo $infoJoueur->Id ?>/<?php echo $mecenat->Id; ?>">
                                        <button class="btn btn-danger"><span class="fa fa-close"></span></button>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <h3>Ce joueur n'a pas de mécénat.</h3>
        <?php endif; ?>
       
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->