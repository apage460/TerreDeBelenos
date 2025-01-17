<script>
    $(function(){

        var controller = 'Administration',
            base_url = '<?php echo site_url();?>', 
            data;

        $('.searchField').delay(500).on('keyup',function(event){
            event.preventDefault();

            compte = $('#compte').val();
            prenomJoueur = $('#prenomJoueur').val();
            pseudoJoueur = $('#pseudoJoueur').val();
            nomJoueur = $('#nomJoueur').val();

            launchSearch(compte, prenomJoueur, nomJoueur, pseudoJoueur);
        })

        function launchSearch(compte, prenomJoueur, nomJoueur, pseudoJoueur){
            var container = $('#searchResults');
            container.html('<i class="fa fa-cog fa-spin fa-3x fa-fw"></i>');

            $.ajax({
                'url' : base_url + controller + '/searchIndividusCredit',
                'type' : 'POST',
                'data' : {
                    'compte' : compte,
                    'prenomJoueur' : prenomJoueur,
                    'pseudoJoueur' : pseudoJoueur,                  
                    'nomJoueur' : nomJoueur
                },
                'success' : function(data){
                    if(data){
                        container.html(data);
                    }
                },
                'error' : function(err){
                    console.log(err);
                }
            });
        }
    });
</script>

<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h1 class="page-header">Crédit et joueurs</h1>
            </div>        
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->

        <div class="row">
            <div class="col-xs-12">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#recherche">Recherche</a></li>
                    <li><a data-toggle="tab" href="#sommaire">Sommaire</a></li>
                </ul>

                <div class="tab-content">
                    <div id="recherche" class="tab-pane fade in active">
                        <h3>Recherche de joueur</h3>
                        
                        <div class="col-xs-12 col-md-3">
                            <label for="compte">Compte</label>
                            <input type="text" name="compte" id="compte"class="form-control searchField">
                        </div>
                        <div class="col-xs-12 col-md-3">
                            <label for="pseudoJoueur">Pseudo</label>
                            <input type="text" name="pseudoJoueur" id="pseudoJoueur"class="form-control searchField">
                        </div>
                        <div class="col-xs-12 col-md-3">
                            <label for="prenomJoueur">Prénom</label>
                            <input type="text" name="prenomJoueur" id="prenomJoueur"class="form-control searchField">
                        </div>
                        <div class="col-xs-12 col-md-3">
                            <label for="nomJoueur">Nom</label>
                            <input type="text" name="nomJoueur" id="nomJoueur" class="form-control searchField">
                        </div>

                        <div class="row">
                            <div id="searchResults"></div>
                        </div>
                    </div>
                    <div id="sommaire" class="tab-pane fade">
                        <h3>Sommaire</h3>
                        <div class="col-xs-12 col-md-6">
                            <h4>Crédits</h4>
                            <table class="table table-striped table-responsvie">
                                <tr>
                                    <th>Nom</th>
                                    <th>Montant</th>
                                    <th>Raison</th>
                                    <th>Date</th>
                                    <th>Commentaires</th>
                                    <th></th>
                                </tr>
                                <?php foreach ($sommaire as $som) : ?>
                                    <?php if($som->Montant > 0): ?>
                                        <tr>
                                            <td><?= $som->nomIndiv; ?></td>
                                            <td><?= $som->Montant; ?></td>
                                            <td><?= $som->Raison; ?></td>
                                            <td><?= $som->DateInscription; ?></td>
                                            <td><?= $som->Commentaires; ?></td>
                                            <td>
                                                <a href="<?= site_url('Administration/deleteCreditOuDette') .'/' .$som->Id; ?>">
                                                    <button class="btn btn-danger"><span class="fa fa-trash"></span></button>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </table>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <h4>Dettes</h4>
                            <table class="table table-striped table-responsive">
                                <tr>
                                    <th>Nom</th>
                                    <th>Montant</th>
                                    <th>Raison</th>
                                    <th>Date</th>
                                    <th>Commentaires</th>
                                    <th></th>
                                </tr>
                                <?php foreach ($sommaire as $som) : ?>
                                    <?php if($som->Montant < 0): ?>
                                        <tr>
                                            <td><?= $som->nomIndiv; ?></td>
                                            <td><?= $som->Montant; ?></td>
                                            <td><?= $som->Raison; ?></td>
                                            <td><?= $som->DateInscription; ?></td>
                                            <td><?= $som->Commentaires; ?></td>
                                            <td>
                                                <a href="<?= site_url('Administration/deleteCreditOuDette') .'/' .$som->Id; ?>">
                                                    <button class="btn btn-danger"><span class="fa fa-trash"></span></button>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->