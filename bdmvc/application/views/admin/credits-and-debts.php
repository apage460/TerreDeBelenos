<script>
    $(function(){

        var controller = 'Administration',
            base_url = '<?php echo site_url();?>', 
            data;

        $('#btn-addCredit').on('click',function(event){
            event.preventDefault();

            idIndividu = $('#idIndividu').val();
            montantCredit = $('#montantCredit').val();
            raisonCredit = $('#raisonCredit').val();
            commentairesCredit = $('#commentairesCredit').val();

            addCredit(idIndividu, montantCredit, raisonCredit,commentairesCredit);
        })

        function addCredit(idIndividu, montantCredit, raisonCredit,commentairesCredit){
            var container = $('#searchResults');

            $.ajax({
                'url' : base_url + controller + '/addCredit',
                'type' : 'POST',
                'data' : {
                    'idIndividu' : idIndividu,                    
                    'montantCredit' : montantCredit,
                    'raisonCredit' : raisonCredit,
                    'commentairesCredit' : commentairesCredit,
                },
                'success' : function(data){
                    location.reload();
                },
                'error' : function(err){
                    console.log(err);
                }
            });
        }

        $('#btn-addDette').on('click',function(event){
            event.preventDefault();

            idIndividu = $('#idIndividu').val();
            montantDette = $('#montantDette').val();
            raisonDette = $('#raisonDette').val();
            commentairesDette = $('#commentairesDette').val();

            addDette(idIndividu, montantDette, raisonDette, commentairesDette);
        })

        function addDette(idIndividu, montantDette, raisonDette, commentairesDette){
            var container = $('#searchResults');

            $.ajax({
                'url' : base_url + controller + '/addDette',
                'type' : 'POST',
                'data' : {
                    'idIndividu' : idIndividu,                    
                    'montantDette' : montantDette,
                    'raisonDette' : raisonDette,
                    'commentairesDette' : commentairesDette
                },
                'success' : function(data){
                    location.reload();
                },
                'error' : function(err){
                    console.log(err);
                }
            });
        }

        $('.btn-deleteCreditOrDette').on('click',function(event){
            event.preventDefault();

            idSomme = $(this).attr('data-idSomme');

            deleteCreditOrDette(idSomme);
        })

        function deleteCreditOrDette(idSomme){
            var container = $('#searchResults');

            $.ajax({
                'url' : base_url + controller + '/removeCreditOrDebt',
                'type' : 'POST',
                'data' : {
                    'idSomme' : idSomme,
                },
                'success' : function(data){
                    location.reload();
                },
                'error' : function(err){
                    console.log(err);
                }
            });
        }

    });
</script>

<?php 
    $total = 0;

    $sommes = $CreditsAndDebts['sommes'];
    $infosJoueurs = $CreditsAndDebts['infosJoueurs'];
?>

<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h1 class="page-header">Sommaire des Crédits et Dettes </h1>
            </div>        
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->

        <div class="row">
                <table class="table table-striped table-responsive">
                    <tr>
                        <th>Raison</th>
                        <th>Montant</th>
                        <th>Commentaires</th>
                        <th></th>
                    </tr>
                    <?php foreach ($sommes as $somme) { ?>
                        <tr>
                            <td><?php echo $somme['Raison']; ?></td>
                            <td>
                                <?php 
                                    $total = $total + intval($somme['Montant']);
                                    echo $somme['Montant'] .' $'; 
                                ?>                                    
                            </td>
                            <td><?php echo $somme['Commentaires']; ?></td>
                            <td><button class="btn btn-danger btn-deleteCreditOrDette" data-idSomme="<?php echo $somme['Id']; ?> "><span class="fa fa-trash fa-reverse"></span></button></td>
                        </tr>
                    <?php } //end foreach ?>
                </table>
        </div>
        <div class="row">
        <?php if($total < 0) { ?>
            <h2 class="red">Total : <?php echo $total .' $'; ?></h2>
            <?php } else { ?>
            <h2 class="green">Total : <?php echo $total .' $'; ?></h2>
            <?php } ?>
        </div>

        <div class="row">
            <div class="col-xs-12 col-md-4">
            <input id="idIndividu" type="hidden" value="<?php echo $infosJoueurs[0]['Id']; ?>">
                <h2>Ajouter un crédit</h2>
                <div class="row">
                    <input id="montantCredit" class="form-control" type="text" placeholder="Montant">
                    <input id="raisonCredit" class="form-control" type="text" placeholder="Raison">
                    <input id="commentairesCredit" class="form-control" type="text" placeholder="Commentaires">
                    <button id="btn-addCredit" class="btn btn-success">Ajouter un CRÉDIT</button>
                </div>
            </div>
            <div class="col-xs-12 col-md-4 col-md-offset-2">
                <h2>Ajouter une dette</h2>
                <div class="row">
                    <input id="montantDette" class="form-control" type="text" placeholder="Montant">
                    <input id="raisonDette" class="form-control" type="text" placeholder="Raison">
                    <input id="commentairesDette" class="form-control" type="text" placeholder="Commentaires"> 
                    <button id="btn-addDette" class="btn btn-danger">Ajouter une DETTE</button>
                </div>
            </div>
            <div class="col-md-2"></div>
        </div>           
            

        
            

    </div>

    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->