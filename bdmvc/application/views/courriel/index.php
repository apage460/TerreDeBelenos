<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h1 class="page-header">Envoyer un courriel</h1>
            </div>        
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
		
        <div class="row"> 	
	        <div class="col-xs-12">
	        	<h2>Recherche</h2>
	        	<div class="form-group col-md-3 col-xs-12">
                    <label for="pseudo">Pseudo</label>
                    <input type="text" name="pseudo" id="pseudo" placeholder="Pseudo" class="form-control">
                </div>
                <div class="form-group col-md-3 col-xs-12">
					<label for="prenom">Prénom</label>
					<input type="text" name="prenom" id="prenom" placeholder="Prénom" class="form-control">
				</div>
				<div class="form-group col-md-3 col-xs-12">
					<label for="nom">Nom</label>
					<input type="text" name="nom" id="nom" placeholder="Nom" class="form-control">
				</div>
				<div class="form-group col-md-3 col-xs-12">
					<label for="groupe">Groupe</label>
					<select name="groupe" id="groupe" class="form-control">
						<option selected="selected" value="">Groupes</option>
						<?php foreach ($groupes as $groupe) : ?>
							<option value="<?php echo $groupe->Id; ?>"><?php echo $groupe->Nom; ?></option>
						<?php endforeach; ?>
		        	</select>
				</div>
				<div class="form-group col-md-3 col-xs-12">
					<button class="btn btn-block btn-primary" id="btn-search">Recherche <span class="fa fa-check"></span></button>
				</div>
                <div class="form-group col-md-3 col-md-offset-6 col-xs-12">
                    <button class="btn btn-block btn-primary" id="nextStep">Écrire le courriel <span class="fa fa-envelope"></span></button>
                </div>	
	        </div>
        </div>

        <div class="row">            
        	<div id="searchResults" class="col-xs-12 col-md-6 ">
                <h3>Résultats de recherche</h3>
                <!-- SEARCH RESULTS GO HERE -->        		
        	</div>

            <div class="col-xs-12 col-md-6" style="border-left-style: groove;">
                <h3>Destinataires</h3>
                <table id="destinataires" class="table table-striped">
                    <tr>
                        <th>Courriel</th>
                        <th></th>
                    </tr>
                    <?php 
                        if(isset($destinataires)):
                            foreach ($destinataires as $destinataire) : 
                    ?>
                                <tr>
                                    <td class="emailContainer">
                                        <?php echo $destinataire; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-danger removeCourriel"><span class="fa fa-close"></span></button>
                                    </td>
                                </tr>
                    <?php   endforeach; ?><?php
                        endif; 
                    ?>
                </table>
            </div>
        </div>
        	
           
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->

<script>
    $(document).ready(function(){

        removeBtn = $('.removeCourriel');

        $.each(removeBtn, function(){
            removeBtn.on('click', function(){
                $(this).closest('tr').remove();
            });
        });
    });
</script>

<script>
	$(function(){

        var controller = 'Courriel',
            base_url = '<?php echo site_url();?>', 
            data;

        $('#btn-search').on('click',function(event){
            event.preventDefault();

            prenom = $('#prenom').val();
            nom = $('#nom').val();
            groupe = $('#groupe').val();
            pseudo = $('#pseudo').val();

            launchSearch(prenom, nom, groupe, pseudo);
        })

        function launchSearch(prenom, nom, groupe, pseudo){
            var container = $('#searchResults');
            container.html('<i class="fa fa-cog fa-spin fa-3x fa-fw"></i>');

            $.ajax({
                'url' : base_url + controller + '/search',
                'type' : 'POST',
                'data' : {
                    'prenom' : prenom,
                    'nom' : nom,                    
                    'groupe' : groupe,
                    'pseudo' : pseudo
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

    $('#nextStep').click(function(){
        $(this).children('span').removeClass('fa-envelope').addClass('fa-cog fa-spin');
        var destinataires = [],
            controller = 'Courriel',
            base_url = '<?php echo site_url();?>', 
            data;

        $('.emailContainer').each(function(index){
            destinataires.push($(this).html());
        });

        $.ajax({
            'url' : base_url + controller + '/editEmail',
            'type': 'POST',
            'data': {
                'destinataires': destinataires,
            },
            'success': function(data){
                $('#page-wrapper').html(data);
            },
            'error': function(err){
                console.log(err);
            }
        });
    });
</script>