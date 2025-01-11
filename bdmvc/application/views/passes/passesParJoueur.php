<script>
    $(function(){

        $('#idPasse').on('change', function(){
            var idPasse = $('#idPasse :selected').val();
            $('#downloadPassList').attr('href', '/bdmvc/index.php/passes/downloadPassList/' + idPasse);
        });
    });
</script>

<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Passes</h1>
                <h2>Associer une passe à un joueur</h2>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <?php if(validation_errors()): ?>

            <div class="row errors-container"><span class="fa fa-exclamation-triangle fa-4x"><?php echo validation_errors(); ?></div>

        <?php endif; ?>
        
        <div class="row">
            <div class="col-xs-12">
                <h3>Chercher un joueur</h3>
                <?php echo form_open('passes/searchJoueurs'); ?>
                <div class="form-group col-md-3 col-xs-12">
                    <label for="pseudo">Pseudo</label>
                    <input type="text" class="form-control" name="pseudo" placeholder="Pseudo">
                </div>
                <div class="form-group col-md-3 col-xs-12">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" class="form-control" name="username" placeholder="Nom d'utilisateur">
                </div>
                <div class="form-group col-md-3 col-xs-12">
                    <label for="prenom">Prénom</label>
                    <input type="text" class="form-control" name="prenom" placeholder="Prénom">
                </div>
                <div class="form-group col-md-3 col-xs-12">
                    <label for="nom">Nom</label>
                    <input type="text" class="form-control" name="nom" placeholder="Nom">
                </div>
                
                <div class="form-group col-md-6 col-xs-12">
                    <button id="searchJoueurs" type="submit" class="btn btn-primary">Chercher un joueur</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <h3>Chercher par passe</h3>
                <?php echo form_open('passes/searchParPasse'); ?>
                <div class="form-group col-md-4 col-xs-12">
                    <select id="idPasse" name="idPasse" class="form-control">
                        <?php foreach ($passes as $passe) : ?>
                            <option value="<?php echo $passe->Id; ?>"><?php echo $passe->Nom; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-xs-12">
                    <button id="searchParPasse" type="submit" class="btn btn-primary">Chercher par passe</button>
                    <a href="<?php echo site_url('passes/downloadPassList') . '/' . $passes[0]->Id ;?>" id="downloadPassList"><button type="button" class="btn btn-primary">Télécharger la liste (.csv) <span class="fa fa-file-excel-o"></span></button></a>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
        <?php if(!empty($joueurs)): ?>
            <div class="row">
                <div class="col-xs-12">
                    <h2>Joueurs</h2>
                
                <!--<pre><?php echo var_dump($joueurs[0]); ?></pre>-->
                    <?php $dateToday = date('Y-m-d', strtotime("today")); ?>
                    <table class="table table-striped table-responsive">
                        <tr>
                            <th>Pseudo</th>
                            <th>Nom d'utilisateur</th>
                            <th>Prénom</th>
                            <th>Nom</th>
                            <th>Passes Actives</th>
                            <th></th>
                        </tr>
                        <?php foreach ($joueurs as $joueur) : ?>
                            <tr>
                                <td><?php echo $joueur["infoJoueur"]->Pseudo; ?></td>
                                <td><?php echo $joueur["infoJoueur"]->Compte; ?></td>
                                <td><?php echo $joueur["infoJoueur"]->Prenom; ?></td>
                                <td><?php echo $joueur["infoJoueur"]->Nom; ?></td>
                                <td>
                                    <?php if(!empty($joueur['passeDuJoueur'])) : ?>
                                        <table class="table table-striped">
                                        <?php foreach( $joueur["passeDuJoueur"] as $passe ): ?>
                                            <tr>
                                                <td><?php if($passe->CodeEtat == 'INACT') : echo '<del>' .$passe->Nom .'</del>'; else: echo $passe->Nom; endif; ?></td>
                                                <?php if($_SESSION['infoUser']->NiveauAcces >= 6 && $passe->CodeEtat == 'ACTIF'): ?>
                                                    <td><a href="<?php echo site_url('passes/unlinkPassPlayer'); ?>/<?php echo $joueur["infoJoueur"]->Id .'/' .$passe->Id; ?>">
                                                            <button class="btn btn-danger"><span class="fa fa-close"></span></button>
                                                        </a>
                                                    </td>
                                                <?php elseif($_SESSION['infoUser']->NiveauAcces >= 6 && $passe->CodeEtat == 'INACT') : ?>
                                                    <td><a href="<?php echo site_url('passes/relinkPassPlayer'); ?>/<?php echo $joueur["infoJoueur"]->Id .'/' .$passe->Id; ?>">
                                                            <button class="btn btn-success"><span class="fa fa-check"></span></button>
                                                        </a>
                                                    </td>
                                                <?php endif; ?>                                                
                                            </tr>
                                        <?php endforeach; ?>
                                        </table>
                                    <?php else:  ?>
                                        Ce joueur n'a pas de passe qui lui est associée.
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo form_open('passes/linkPassPlayer/' . $joueur["infoJoueur"]->Id); ?>
                                    <div class="form-group col-md-9 col-xs-12">
                                        <select name="idPasse" name="idPasse" class="form-control">
                                            <?php foreach ($passes as $passe) : ?>
                                                <option value="<?php echo $passe->Id; ?>"><?php echo $passe->Nom .' ( ' . $passe->Prix .'$ )'; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3 col-xs-12">
                                        <button type="submit" class="btn btn-primary">Associer</button>
                                    </div>
                                    <?php echo form_close(); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
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