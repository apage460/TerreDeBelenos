<script type="text/javascript">
    $(function(){

    	$('#notes').keyup(function(){
    		longueurEmailContent = $('#notes').val();

    		if( longueurEmailContent.length >= 10){
    			$('#refusalBtn').removeAttr('disabled');
    		}
    		else{
    			$('#refusalBtn').attr('disabled', 'disabled');
    		}
    	});

        var controller = 'Approbations',
            base_url = '<?php echo site_url();?>', 
            data;
        
        $('#refusalBtn').on('click', function(event){            
            event.preventDefault();
            idApprobation = $('#idApprobation').val();
            emailContent = $('#notes').val();
            email = $('#email').val();
            refusHistoire(idApprobation, emailContent);
            
        });
        
      
        function refusHistoire(idApprobation, emailContent, email){            
            $.ajax({
                'url' : base_url + '/' + controller + '/refusHistoire', 
                'type' : 'POST',
                'data' : {'idApprobation' : idApprobation, 'emailContent' : emailContent, 'email' : email},
                'success' : function(data){ 
                    location.reload();
                },
                'error' : function(err){
                	console.log(err);
                }
            });
        }

        $('#acceptBtn').on('click', function(event){           
            event.preventDefault();
            idApprobation = $('#idApprobation').val();
            emailContent = $('#notes').val();
            email = $('#email').val();
            idPersonnage = $('#listIndividus').val();
            idIndividu = $('#idIndividu').val();

            acceptHistoire(idApprobation, emailContent, email, idPersonnage, idIndividu);            
        });
        
      
        function acceptHistoire(idApprobation, emailContent, email, idPersonnage, idIndividu){            
            $.ajax({
                'url' : base_url + '/' + controller + '/acceptHistoire', 
                'type' : 'POST',
                'data' : {'idApprobation' : idApprobation, 'emailContent' : emailContent, 'email' : email, 'idPersonnage' : idPersonnage, 'idIndividu' : idIndividu},
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

<div class="col-xs-12 col-md-6">
	<p><?php echo $histoire->Histoire; ?></p>
</div>
<div class="col-xs-12 col-md-6">
	<input type="hidden" name="idApprobation" id="idApprobation" value=<?php echo '"' .$histoire->Id .'"'; ?> >
    <input type="hidden" name="email" id="email" value=<?php echo '"' .$individu->Courriel .'"'; ?> >
    <input type="hidden" name="idIndividu" id="idIndividu" value=<?php echo '"' .$individu->Id .'"'; ?> >
	<p><strong>Utilisateur : </strong><?php echo $individu->Compte; ?></p>
	<p><strong>Joueur : </strong><?php echo $individu->Prenom .' ' .$individu->Nom; ?></p>
	<textarea name="notes" id="notes" class="form-control" rows="15" placeholder="Notes"></textarea>

    <button id="acceptBtn" type="button" class="btn btn-success btn-lg btn-block">Accepter <span class="fa fa-check fa-inverse"></span></button>
	<button id="refusalBtn" type="button" class="btn btn-danger btn-lg btn-block" disabled="disabled">Refuser <span class="fa fa-times fa-inverse"></span></button>


	
</div>