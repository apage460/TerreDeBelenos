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

            addCredit(idIndividu, montantCredit, raisonCredit);
        })

        function addCredit(idIndividu, montantCredit, raisonCredit){
            var container = $('#searchResults');

            $.ajax({
                'url' : base_url + controller + '/addCredit',
                'type' : 'POST',
                'data' : {
                    'idIndividu' : idIndividu,                    
                    'montantCredit' : montantCredit,
                    'raisonCredit' : raisonCredit,
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
    $inscripteurs = $avertissements['inscripteurs'];
    $blames = $avertissements['avertissements'];
    $infosJoueur = $avertissements['infosJoueur'];
?>

<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h1 class="page-header">Sommaire des Avertissements</h1>
            </div>        
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->

        <div class="row">
                <table class="table table-striped table-responsive">
                    <tr>
                        <th>Type</th>
                        <th>Raison</th>
                        <th>Commentaires</th>
                        <th></th>
                    </tr>
                    <?php foreach ($blames as $blames) { ?>
                        <tr>
                            <td><?php echo $blames['Type']; ?></td>
                            <td><?php echo $blames['Raison']; ?></td>
                            <td><?php echo $blames['Commentaires']; ?></td>
                            <td><button class="btn btn-danger btn-deleteCreditOrDette"><span class="fa fa-trash fa-reverse"></span></button></td>
                        </tr>
                    <?php } //end foreach ?>
                </table>
        </div>

        <div class="row">
            <div class="col-xs-12 col-md-4">
            <input id="idIndividu" type="hidden" value="<?php echo $infosJoueur[0]['Id']; ?>">
                <h2>Ajouter un avertissement</h2>
                <div class="row">
                    <input id="montantCredit" class="form-control" type="text" placeholder="Montant">
                    <input id="raisonCredit" class="form-control" type="text" placeholder="Raison">
                    <button id="btn-addCredit" class="btn btn-success">Ajouter un avertissement</button>
                </div>
            </div>
        </div>           
            
    </div>

    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->