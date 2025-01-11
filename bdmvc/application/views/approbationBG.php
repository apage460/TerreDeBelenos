<!-- Page Content -->
<script type="text/javascript">
    $(function(){
        
        var controller = 'Approbations',
            base_url = '<?php echo site_url();?>', 
            data;
        
        $('#listIndividus').on('change', function(event){            
            event.preventDefault(); 
            IdPersonnage = $('#listIndividus :selected').val();
            load_histoire_ajax(IdPersonnage);
            
        });
        
      
        function load_histoire_ajax(IdPersonnage){            
            $.ajax({
                'url' : base_url + '/' + controller + '/getPersonnageHistoire', 
                'type' : 'POST',
                'data' : {'id' : IdPersonnage},
                'success' : function(data){ 
                    var container = $('#histoire');
                    if(data){
                        container.html(data);
                    }
                }
            });
           
        }
     });
</script>

<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h1 class="page-header">Approbations d'historiques</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->

        <div class="row">
            <div class="col-xs-12 col-md-6">
                <select class="form-control" name="listIndividus" id="listIndividus">
                    <option value="">Choisir un personnage</option>
                    <?php foreach($approbationsBG as $approbationBG){ ?>
                    <option value=<?php echo '"' .$approbationBG['Id'] .'"'; ?> >
                        <em><?php echo $approbationBG['Prenom'] .' ' .$approbationBG['Nom']; ?></em>
                    </option>
                    <?php } //END FOREACH ?>
                </select>
            </div>
        </div>
        
        <div id="histoire" class="row">
        </div>
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->