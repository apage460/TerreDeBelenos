<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Mécénat</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <?php if(validation_errors()): ?>

            <div class="row errors-container"><span class="fa fa-exclamation-triangle fa-4x"><?php echo validation_errors(); ?></div>

        <?php endif; ?>
        
        <div class="row">
            <div class="col-xs-12">
                <h3>Chercher un joueur</h3>
                <?php echo form_open('mecenat/searchJoueurs'); ?>
                <div class="form-group col-md-4 col-xs-12">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" class="form-control" name="username" placeholder="Nom d'utilisateur">
                </div>
                <div class="form-group col-md-4 col-xs-12">
                    <label for="prenom">Prénom</label>
                    <input type="text" class="form-control" name="prenom" placeholder="Prénom">
                </div>
                <div class="form-group col-md-4 col-xs-12">
                    <label for="nom">Nom</label>
                    <input type="text" class="form-control" name="nom" placeholder="Nom">
                </div>
                
                <div class="form-group col-md-6 col-xs-12">
                    <button id="searchJoueurs" type="submit" class="btn btn-primary">Chercher un joueur</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
        <?php if(!empty($joueurs)): ?>
            <div class="row">
                <div class="col-xs-12">
                    <h2>Joueurs</h2>
                    <table class="table table-striped table-responsive">
                        <tr>
                            <th>Nom d'utilisateur</th>
                            <th>Prénom</th>
                            <th>Nom</th>
                            <th></th>
                        </tr>
                        <?php foreach ($joueurs as $joueur): ?>
                            <tr>
                                <td><?php echo $joueur->Compte; ?></td>
                                <td><?php echo $joueur->Prenom; ?></td>
                                <td><?php echo $joueur->Nom; ?></td>
                                <td>
                                    <a href="<?php echo site_url('mecenat/mecenatParJoueur') ?>/<?php echo $joueur->Id; ?>">
                                        <button class="btn btn-primary"><span class="fa fa-edit"></span></button>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <hr>
        <?php endif; ?>
       
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->