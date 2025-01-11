<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Arcanns</h1>
                <h3>Chercher un joueur</h3>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <div class="row">
            <?php echo form_open('individus/arcannSearchIndiv'); ?>

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
        </div>

        <div class="row">
            <div class="col-xs-6">
                <button class="btn btn-primary btn-lg">Rechercher</button>
                <?php echo form_close(); ?>  
            </div>
        </div>

        <?php if(isset($results) && !empty($results)) : ?>
            <div class="row">
                <h3>Résultats</h3>

                <table class="table table-striped table-responsible col-md-6">
                    <tr>
                        <th>Joueur</th>
                        <th></th>
                    </tr>
                    <?php foreach ($results as $key => $result) : ?>
                        <tr>
                            <td><?php echo $result->Prenom .' ' .$result->Nom .' <i>(' .$result->Compte .')</i> '; ?></td>
                            <td>
                                <a href="<?php echo site_url('individus/arcannSingleIndiv') .'/' .$result->Id; ?>">
                                <button class="btn btn-primary"><span class="fa fa-eye"></span></button>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php endif; ?>
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->