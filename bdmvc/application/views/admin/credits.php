<script>
    $(function(){

        var controller = 'Administration',
            base_url = '<?php echo site_url();?>', 
            data;

        $('.searchField').delay(500).on('keyup',function(event){
            event.preventDefault();

            compte = $('#compte').val();
            prenomJoueur = $('#prenomJoueur').val();
            nomJoueur = $('#nomJoueur').val();

            launchSearch(compte, prenomJoueur, nomJoueur);
        })

        function launchSearch(compte, prenomJoueur, nomJoueur){
            var container = $('#searchResults');
            container.html('<i class="fa fa-cog fa-spin fa-3x fa-fw"></i>');

            $.ajax({
                'url' : base_url + controller + '/searchIndividusCredit',
                'type' : 'POST',
                'data' : {
                    'compte' : compte,
                    'prenomJoueur' : prenomJoueur,                    
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
                <h1 class="page-header">Ajouter un crédit</h1>
            </div>        
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->

        <div class="row">
            <h3>Identification du joueur</h3>
            <div class="col-xs-12 col-md-4">
                <label for="compte">Compte</label>
                <input type="text" name="compte" id="compte"class="form-control searchField">
            </div>
            <div class="col-xs-12 col-md-4">
                <label for="prenomJoueur">Prénom</label>
                <input type="text" name="prenomJoueur" id="prenomJoueur"class="form-control searchField">
            </div>
            <div class="col-xs-12 col-md-4">
                <label for="nomJoueur">Nom</label>
                <input type="text" name="nomJoueur" id="nomJoueur" class="form-control searchField">
            </div>

        <div class="row">
            <div id="searchResults"></div>
        </div>
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->