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

            <div class="row"><?php echo validation_errors(); ?></div>

        <?php endif; ?>
        
        <div class="row">
            <div class="col-xs-12">
                <h3>Modifier une activité</h3>
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
                        <option value="1">Banquet</option>
                    </select>
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
                    <button id="btn_addActivite" type="submit" class="btn btn-primary">Modifier une activité <span class="fa fa-calendar"></span></button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->