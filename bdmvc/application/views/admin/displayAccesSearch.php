<script>
    $(function(){

        var controller = 'Administration',
            base_url = '<?php echo site_url();?>', 
            data;

        $('#btn-updateNiveauAcces').on('click',function(event){
            event.preventDefault();

            $(this).removeClass('btn-primary').addClass('btn-success');
            $(this).children('span').removeClass('fa-check').addClass('fa-cog fa-spin fa-fw')

            idIndividu = $('#idIndividu').val();
            newNiveauAcces = $('#selectNiveauAcces-'+idIndividu).val();

            updateNiveauAcces(idIndividu, newNiveauAcces);
        })

        function updateNiveauAcces(idIndividu, newNiveauAcces){

            $.ajax({
                'url' : base_url + controller + '/updateNiveauAcces',
                'type' : 'POST',
                'data' : {
                    'idIndividu' : idIndividu,                    
                    'newNiveauAcces' : newNiveauAcces
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
                <th>Niveau d'Accès</th>
                <th>Nouveau Niveau</th>
            </tr>
            <?php foreach ($individus as $individu) { 

                switch($individu->NiveauAcces) {
                    case 1:
                        $nomNiveau = '(Joueur)';
                        break;
                    case 2:
                        $nomNiveau = '(Inscripteur)';
                        break;
                    case 3:
                        $nomNiveau = '(Scripteur)';
                        break;
                    case 4:
                        $nomNiveau = '(Arbitre)';
                        break;
                    case 5:
                        $nomNiveau = '(Responsable)';
                        break;
                    case 6:
                        $nomNiveau = '(Organisateur)';
                        break;
                    case 7:
                        $nomNiveau = '(DBA)';
                        break;
                }
            ?>

                <tr>
                            <td>
                                <input type="hidden" id="idIndividu" value="<?php echo $individu->Id; ?>">
                                <?php echo $individu->Compte; ?></h4>
                            </td>
                            <td><?php echo $individu->Prenom; ?></td>
                            <td> <?php echo $individu->Nom; ?></td>
                            <td><?php echo $individu->NiveauAcces .' ' .$nomNiveau; ?></td>                                    
                            <td>
                                <select class="form-control" name="selectNiveauAcces" id="selectNiveauAcces-<?php echo $individu->Id; ?>">
                                        <option value="1">1 (Joueur)</option>
                                        <option value="2">2 (Inscripteur)</option>
                                        <option value="3">3 (Scripteur)</option>
                                        <option value="4">4 (Arbitre)</option>
                                        <?php if($_SESSION['infoUser']->NiveauAcces >= 6): ?><option value="5">5 (Responsable)</option><?php endif; ?>
                                        <?php if($_SESSION['infoUser']->NiveauAcces >= 6): ?><option value="6">6 (Organisateur)</option><?php endif; ?>
                                        <?php if($_SESSION['infoUser']->NiveauAcces >= 7): ?><option value="7">7 (DBA)</option><?php endif; ?>
                                </select>

                                <button id="btn-updateNiveauAcces" class="btn btn-primary btn-block" <?php if($_SESSION['infoUser']->NiveauAcces <= 5): echo 'disabled="disabled"'; endif; ?>>
                                    Modifier <span class="fa fa-check fa-reverse"></span>
                                </button>
                            </td>                   
                </tr>

            <?php } //END FOREACH ?>
        </table>        
    </div>
</div>  