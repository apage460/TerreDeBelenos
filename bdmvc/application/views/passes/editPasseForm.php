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

            <div class="row errors-container"><span class="fa fa-exclamation-triangle fa-4x"></span><?php echo validation_errors(); ?></div>

        <?php endif; ?>
        <div class="row">
            <div class="col-md-6 col-xs-12">
                <h3>Modifier une passe</h3>
                <?php echo form_open('passes/editPasse/' .$passe->Id); ?>
                <div class="form-group col-xs-12">
                    <label for="nom">Nom</label>
                    <input type="text" class="form-control" name="nom" value="<?php echo $passe->Nom; ?>">
                </div>
                <div class="form-group col-xs-12">
                    <label for="description">Description</label>
                    <input type="text" class="form-control" name="description" value="<?php echo $passe->Description; ?>">
                </div>
                <div class="form-group col-xs-12">
                    <label for="prix">Prix</label>
                    <input type="text" class="form-control" name="prix" value="<?php echo $passe->Prix; ?>">
                </div>
                <div class="form-group col-xs-12">
                    <label for="dateDebut">Date de Début</label>
                    <input type="datetime-local" class="form-control" name="dateDebut" value="<?php echo substr($passe->DateDebut,0,10) .'T' .substr($passe->DateDebut,-8); ?>">
                </div>
                <div class="form-group col-xs-12">
                    <label for="dateFin">Date de Fin</label>
                    <input type="datetime-local" class="form-control" name="dateFin" value="<?php echo substr($passe->DateFin,0,10) .'T' .substr($passe->DateFin,-8); ?>" >
                </div>
                
                <div class="form-group col-xs-12">
                    <button id="btn_addActivite" type="submit" class="btn btn-primary">Modifier une passe <span class="fa fa-calendar"></span></button>
                </div>
                <?php echo form_close(); ?>
            </div>
            <div class="col-md-6 col-xs-12">
                <h3>Cette passe est reliée à...</h3>
                <?php if( !empty($activitesReliees) ): ?>
                    <table class="table table-responsive table-striped">
                        <tr>
                            <th>Type</th>
                            <th>Nom</th>
                            <th></th>
                        </tr>
                    <?php foreach ($activitesReliees as $activitesReliee) : ?>
                        <tr>
                            <td><strong><?php echo $activitesReliee->Type; ?></strong></td>
                            <td><?php echo $activitesReliee->Nom; ?></td>
                            <td><a href="<?php echo site_url('passes'); ?>/deleteActiviteReliee/<?php echo $passe->Id . '/' . $activitesReliee->Id; ?>">
                                    <button class="btn btn-danger"><span class="fa fa-close"></span></button>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <h4>Aucune activite n'est reliée à cette passe.</h4>
                <?php endif; ?>

            </div>
        </div>
        <hr>
        <div class="row">
            <h3>Relier la passe aux activités</h3>
            <div class="col-xs-6">
                <h4>GNs</h4>
                <div class="form-group col-xs-12">
                <?php echo form_open('passes/linkPasses/' .$passe->Id); ?>
                    <select class="form-control" name="activite">
                        <?php foreach ($activites as $activite): ?>
                            <?php if ($activite->Type == 'GN'): ?>
                                <option value="<?php echo $activite->Id; ?>"><?php echo $activite->Nom; ?></option>
                            <?php endif ?>
                        <?php endforeach ?>
                    </select>
                    <button type="submit" class="btn btn-primary">Relier à ce GN</button>
                <?php echo form_close(); ?>
                </div>
            </div>
            <div class="col-xs-6">
                <h4>Banquets</h4>
                <div class="form-group col-xs-12">
                <?php echo form_open('passes/linkPasses/' .$passe->Id); ?>
                    <select class="form-control" name="activite">
                        <?php foreach ($activites as $activite): ?>
                            <?php if ($activite->Type == 'BANQUET'): ?>
                                <option value="<?php echo $activite->Id; ?>"><?php echo $activite->Nom; ?></option>
                            <?php endif ?>
                        <?php endforeach ?>
                    </select>
                    <button type="submit" class="btn btn-primary">Relier à ce Banquet</button>
                <?php echo form_close(); ?>
                </div>
            </div>
            <div class="col-xs-6">
                <h4>Contracts</h4>
                <div class="form-group col-xs-12">
                <?php echo form_open('passes/linkPasses/' .$passe->Id); ?>
                    <select class="form-control" name="activite">
                        <?php foreach ($activites as $activite): ?>
                            <?php if ($activite->Type == 'CONTRACT' || $activite->Type == 'MINI'): ?>
                                <option value="<?php echo $activite->Id; ?>"><?php echo $activite->Nom; ?></option>
                            <?php endif ?>
                        <?php endforeach ?>
                    </select>
                   <button type="submit" class="btn btn-primary">Relier à ce Contract</button>
                    <?php echo form_close(); ?>
                </div>
            </div>
            <div class="col-xs-6">
                <h4>Galas</h4>
                <div class="form-group col-xs-12">
                <?php echo form_open('passes/linkPasses/' .$passe->Id); ?>
                    <select class="form-control" name="activite">
                        <?php foreach ($activites as $activite): ?>
                            <?php if ($activite->Type == 'GALA'): ?>
                                <option value="<?php echo $activite->Id; ?>"><?php echo $activite->Nom; ?></option>
                            <?php endif ?>
                        <?php endforeach ?>
                    </select>
                    <button type="submit" class="btn btn-primary">Relier à ce Gala</button>
                <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->