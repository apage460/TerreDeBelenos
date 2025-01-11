<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Activités</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <?php if(validation_errors()): ?>

            <div class="row errors-container"><span class="fa fa-exclamation-triangle fa-4x"><?php echo validation_errors(); ?></div>

        <?php endif; ?>
        
        <div class="row">
            <div class="col-xs-12">
                <h3>Créer une activité</h3>
                <?php echo form_open('activites/addActivite'); ?>
                <div class="form-group col-md-4 col-xs-12">
                    <label for="nom">Nom</label>
                    <input type="text" class="form-control" name="nom" placeholder="Nom">
                </div>
                <div class="form-group col-md-4 col-xs-12">
                    <label for="description">Description</label>
                    <input type="text" class="form-control" name="description" placeholder="Description">
                </div>
                <div class="form-group col-md-4 col-xs-12">
                    <label for="typeActivite">Type</label>
                    <select name="typeActivite" class="form-control">
                        <?php foreach ($typeActivites as $typeActivite) : ?>
                            <option value="<?= $typeActivite->Type; ?>"><?= $typeActivite->Type; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group col-md-4 col-xs-12">
                    <label for="cout">Coût</label>
                    <input type="number" placeholder="0" min="0" class="form-control" name="cout" >
                </div>

                <div class="form-group col-md-4 col-xs-12">
                    <label for="coutMineur">Coût Mineur</label>
                    <input type="number" placeholder="0" min="0" class="form-control" name="coutMineur">
                </div>

                <div class="form-group col-md-4 col-xs-12">
                    <label for="coutEnfant">Coût Enfant</label>
                    <input type="number" placeholder="0" min="0" class="form-control" name="coutEnfant">
                </div>

                <div class="form-group col-md-4 col-xs-12">
                    <label for="dateDebut">Date de Début</label>
                    <input type="datetime-local" class="form-control" name="dateDebut">
                </div>
                <div class="form-group col-md-4 col-xs-12">
                    <label for="dateFin">Date de Fin</label>
                    <input type="datetime-local" class="form-control" name="dateFin">
                </div>
                <div class="form-group col-md-4 col-xs-12">
                    <label for="nbXP">Nombre XP</label>
                    <input type="number" class="form-control" placeholder="0" min="0" name="nbXP">
                </div>
                
                <div class="form-group col-md-6 col-xs-12">
                    <button id="btn_addActivite" type="submit" class="btn btn-primary">Créer une activité <span class="fa fa-calendar"></span></button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
        <div class="row">
            <h2>Activités des 12 derniers mois</h2>
            <div class="col-xs-12" id="activiteContainer">
                <table class="table table-striped  table-responsive">
                    <tr>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Type</th>
                        <th>Date Début</th>
                        <th>Date Fin</th>
                        <th></th>
                    </tr>
                    <?php foreach ($activites as $activite) : ?>
                        <tr>
                            <td><?php echo $activite->Nom; ?></td>
                            <td><?php echo $activite->Description; ?></td>
                            <td><?php echo $activite->Type; ?></td>
                            <td><?php echo $activite->DateDebut; ?></td>
                            <td><?php echo $activite->DateFin; ?></td>

                            <td>
                                <?php if($_SESSION['infoUser']->NiveauAcces >= 6): ?>
                                    <a href="<?php echo site_url(); ?>activites/editActiviteForm/<?php echo $activite->Id; ?>">
                                        <button class="btn btn-primary" <?php if( $activite->DateDebut < date('Y-m-d') ) : echo 'disabled="disabled"'; endif; ?> ><span class="fa fa-edit"></span></button>
                                    </a>
                                <?php endif; ?>
                                <?php if($_SESSION['infoUser']->NiveauAcces >= 7): ?>
                                    <a href="<?php echo site_url(); ?>activites/deleteActivite/<?php echo $activite->Id; ?>">
                                        <button class="btn btn-danger" <?php if( $activite->DateDebut < date('Y-m-d') ) : echo 'disabled="disabled"'; endif; ?> ><span class="fa fa-close"></span></button>
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