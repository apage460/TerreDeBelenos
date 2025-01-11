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

            <div class="row errors-container"><span class="fa fa-exclamation-triangle fa-4x"></span><?php echo validation_errors(); ?></div>

        <?php endif; ?>
        <div class="row">
            <div class="col-xs-12">
                <h3>Modifier une activité</h3>
                <?php echo form_open('activites/editActivite/' .$activite->Id); ?>
                <div class="form-group col-md-4 col-xs-12">
                    <label for="nom">Nom</label>
                    <input type="text" class="form-control" name="nom" value="<?php echo $activite->Nom; ?>">
                </div>
                <div class="form-group col-md-4 col-xs-12">
                    <label for="description">Description</label>
                    <input type="text" class="form-control" name="description" value="<?php echo $activite->Description; ?>">
                </div>
                <div class="form-group col-md-4 col-xs-12">
                    <label for="typeActivite">Type</label>
                    <select name="typeActivite" class="form-control" id="select_typeActivite">
                        <option value="BANQUET" <?php if($activite->Type == 'BANQUET' ): echo 'selected="selected"'; endif; ?> >Banquet</option>
                        <option value="GN" <?php if($activite->Type == 'GN' ): echo 'selected="selected"'; endif; ?>>GN</option>
                        <option value="GALA" <?php if($activite->Type == 'GALA' ): echo 'selected="selected"'; endif; ?> >Gala</option>
                        <option value="CONTRACT" <?php if($activite->Type == 'CONTRACT' ): echo 'selected="selected"'; endif; ?> >Contract</option>
                    </select>
                </div>
                <div class="form-group col-md-6 col-xs-12">
                    <label for="dateDebut">Date de Début</label>
                    <input type="datetime-local" class="form-control" name="dateDebut" value="<?php echo substr($activite->DateDebut,0,10) .'T' .substr($activite->DateDebut,-8); ?>">
                </div>
                <div class="form-group col-md-6 col-xs-12">
                    <label for="dateFin">Date de Fin</label>
                    <input type="datetime-local" class="form-control" name="dateFin" value="<?php echo substr($activite->DateFin,0,10) .'T' .substr($activite->DateFin,-8); ?>" >
                </div>
                
                <div class="form-group col-md-6 col-xs-12">
                    <button id="btn_addActivite" type="submit" class="btn btn-primary">Modifier une activité <span class="fa fa-calendar"></span></button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->