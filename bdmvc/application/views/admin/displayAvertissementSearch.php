<script>
    $(function(){

        var controller = 'Administration',
            base_url = '<?php echo site_url();?>', 
            data;

        $('#btn-addCredit').on('click',function(event){
            event.preventDefault();

            idIndividu = $('#idIndividu').val();
            montant = $('#montant').val();
            raison = $('#raison').val();
            commentaires = $('#commentaires').val();

            addCredit(idIndividu, montant, raison, commentaires);
        })

        function addCredit(idIndividu, montant, raison, commentaires){
            var container = $('#searchResults');

            $.ajax({
                'url' : base_url + controller + '/addCredit',
                'type' : 'POST',
                'data' : {
                    'idIndividu' : idIndividu,                    
                    'montant' : montant,
                    'raison' : raison,
                    'commentaires' : commentaires
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

<div class="col-xs-12">
    <div class="row">
        <table class="table table-striped table-responsive">
            <tr>
                <th>Compte</th>
                <th>Prénom</th>
                <th>Nom</th>
                <th></th>
            </tr>
            <?php foreach ($individus as $individu) { ?>
                <tr>
                            <td>
                                <input type="hidden" id="idIndividu" value="<?php echo $individu->Id; ?>">
                                <?php echo $individu->Compte; ?></h4>
                            </td>
                            <td><?php echo $individu->Prenom; ?></td>
                            <td> <?php echo $individu->Nom; ?></td>                                     
                            <td>
                                <a href="<?php echo site_url('administration/getavertissements')  .'/' .$individu->Id; ?> ">
                                    <button class="btn btn-primary"><span class="fa fa-eye fa-reverse"></span></button>
                                </a>
                            </td>                   
                </tr>

            <?php } //END FOREACH ?>
        </table>        
    </div>
</div>  