<script type="text/javascript">
    $(function(){
        
        var controller = 'Inscriptions',
            base_url = '<?php echo site_url();?>', 
            data;
        
        $('#btn-selectActivite').delay(500).on('click', function(event){            
            event.preventDefault(); 
            idActivite = $('#selectActivite :selected').val();
            prenom = $('#prenom').val()
            load_inscriptions_ajax(idActivite, prenom);            
        });
        
      
        function load_inscriptions_ajax(idActivite, prenom){    
            $('#listInscriptions').html('<i class="fa fa-cog fa-spin fa-3x fa-fw"></i>');        
            $.ajax({
                'url' : base_url + '/' + controller + '/getInscriptions', 
                'type' : 'POST',
                'data' : {'idActivite' : idActivite, 'prenom' : prenom},
                'success' : function(data){ 
                    var container = $('#listInscriptions');
                    if(data){
                        container.html(data);
                        $('.idActiv').val(idActiv);
                    }
                },
                'error' : function(err){
                    console.log(err);
                }
            });
           
        }
     });
</script>

<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Inscriptions</h1>

                <h2>Voir les inscriptions de l'activité :</h2>

             </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->

        <div class="row">
            <div class="col-xs-12 col-md-6">
                <select class="form-control" name="selectActivite" id="selectActivite">
                    <?php
                    foreach($activites as $activite){
                    ?>
                        <option value="<?php echo $activite->Id; ?>"><?php echo $activite->Nom; ?></option>
                    <?php
                    } //END FOREACH
                    ?>
                </select>
                <button id="btn-selectActivite" class="btn btn-primary">Afficher</button>
            </div>
        </div>
        

        <hr />

        <div class="row" id="listInscriptions">
        </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->