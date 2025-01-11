<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Passes</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <?php if(validation_errors()): ?>

            <div class="row errors-container"><span class="fa fa-exclamation-triangle fa-4x"><?php echo validation_errors(); ?></div>

        <?php endif; ?>
        
        <div class="row">
            <div class="col-xs-12">
                <h3>Créer une nouvelle passe</h3>
                <?php echo form_open('passes/addPasse'); ?>
                <div class="form-group col-md-4 col-xs-12">
                    <label for="nom">Nom</label>
                    <input type="text" class="form-control" name="nom" placeholder="Nom">
                </div>
                <div class="form-group col-md-4 col-xs-12">
                    <label for="description">Description</label>
                    <input type="text" class="form-control" name="description" placeholder="Description">
                </div>
                <div class="form-group col-md-4 col-xs-12">
                    <label for="prix">Prix (N'inscrivez pas les cents !)</label>
                    <input type="text" class="form-control" name="prix" placeholder="ex : 180">
                </div>
                <div class="form-group col-md-6 col-xs-12">
                    <label for="dateDebut">Date de Début</label>
                    <input type="datetime-local" class="form-control" name="dateDebut">
                </div>
                <div class="form-group col-md-6 col-xs-12">
                    <label for="dateFin">Date de Fin</label>
                    <input type="datetime-local" class="form-control" name="dateFin">
                </div>
                
                <div class="form-group col-md-6 col-xs-12">
                    <button id="btn_addActivite" type="submit" class="btn btn-primary">Créer une passe <span class="fa fa-ticket"></span></button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
        <div class="row">
            <h2>Passes valides</h2>
            <div class="col-xs-12">
                <table class="table table-stripped table-responsive">
                    <tr>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Prix</th>
                        <th>Date Début</th>
                        <th>Date Fin</th>
                        <th></th>
                        <th></th>
                    </tr>
                    <?php foreach ($passes as $passe) : ?>
                        <tr>
                            <td><?php echo $passe->Nom; ?></td>
                            <td><?php echo $passe->Description; ?></td>
                            <td><?php echo $passe->Prix; ?></td>
                            <td><?php echo $passe->DateDebut; ?></td>
                            <td><?php echo $passe->DateFin; ?></td>

                            <td>
                                <?php if($_SESSION['infoUser']->NiveauAcces >= 6): ?>
                                    <a href="<?php echo site_url(); ?>passes/editPasseForm/<?php echo $passe->Id; ?>">
                                        <button class="btn btn-primary" <?php if( $passe->DateFin < date('Y-m-d') ) : echo 'disabled="disabled"'; endif; ?> ><span class="fa fa-edit"></span></button>
                                    </a>
                                <?php endif; ?>                                
                            </td>
                            <td>
                                <?php if($_SESSION['infoUser']->NiveauAcces >= 7): ?>
                                    <a href="<?php echo site_url(); ?>activites/deletePasse/<?php echo $passe->Id; ?>">
                                        <button class="btn btn-danger" <?php if( $passe->DateFin < date('Y-m-d') ) : echo 'disabled="disabled"'; endif; ?> ><span class="fa fa-close"></span></button>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <?php 

                            ?>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->