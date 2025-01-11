<script type="text/javascript">
$(function(){
    
    var controller = 'Personnages',
        base_url = '<?php echo site_url();?>', 
        data;
    
    $('#btn_changeReligion').on('click', function(event){            
        event.preventDefault(); 
        newReligion = $('#religion :selected').val();
        idPerso = $('#hiddenId').val();
        idIndiv = $('#hiddenIdIndiv').val();
        changeReligion_ajax(newReligion, idPerso, idIndiv);            
    });
    
  
    function changeReligion_ajax(newReligion, idPerso, idIndiv){  
        $('#btn_changeReligion span.fa').addClass('fa-spin');
        $.ajax({
            'url' : base_url + '/' + controller + '/changeReligion', 
            'type' : 'POST',
            'data' : {'newReligion' : newReligion, 'idPerso' : idPerso, 'idIndiv' : idIndiv},
            'success' : function(data){ 
                $('#btn_changeReligion span.fa').removeClass('fa-spin');
                location.reload();
            },
            'error' : function(err){
                console.log(err);
            }
        });       
    }

    /* ADJUST XP */
    var controller = 'Personnages',
        base_url = '<?php echo site_url();?>', 
        data;

    $('#commentaireXP').keyup(function(){
        longueurCommentaire = $('#commentaireXP').val();

        if( longueurCommentaire.length >= 15){
            $('#btn_adjustXP').removeAttr('disabled');
        }
        else{
            $('#btn_adjustXP').attr('disabled', 'disabled');
        }
    });
    
    $('#btn_adjustXP').on('click', function(event){            
        event.preventDefault(); 
        idPerso = $('#hiddenId').val();
        nbXP = $('#nbXP').val();
        commentaireXP = $('#commentaireXP').val();
        idIndiv = $('#hiddenIdIndiv').val();
        adjustXP_ajax(idPerso, nbXP, commentaireXP, idIndiv);            
    });
    
  
    function  adjustXP_ajax(idPerso, nbXP, commentaireXP, idIndiv){  
        $('#btn_adjustXP span.fa').addClass('fa-spin');
        $.ajax({
            'url' : base_url + '/' + controller + '/adjustXP', 
            'type' : 'POST',
            'data' : {'idPerso' : idPerso, 'nbXP' : nbXP, 'commentaireXP' : commentaireXP, 'idIndiv' : idIndiv},
            'success' : function(data){ 
                $('#btn_changeReligion span.fa').removeClass('fa-spin');
                location.reload();
            },
            'error' : function(err){
                console.log(err);
            }
        });       
    }

/* Add Skill */
    var controller = 'Personnages',
        base_url = '<?php echo site_url();?>', 
        data;
    $('#btn_addSkill').on('click', function(event){            
        event.preventDefault();
        idPerso = $('#hiddenId').val();
        idIndiv = $('#hiddenIdIndiv').val();
        codeSkill = $('#selectAddSkill :selected').val();
        usagesSkill = $('#selectAddSkill :selected').attr('data-usages');
        
        addSkill_ajax(idPerso, idIndiv, codeSkill, usagesSkill);            
    });
    
  
    function addSkill_ajax(idPerso, idIndiv, codeSkill, usagesSkill){  
        $('#btn_addSkill span.fa').addClass('fa-spin');
        $.ajax({
            'url' : base_url + controller + '/addSkill', 
            'type' : 'POST',
            'data' : {'idPerso' : idPerso, 'idIndiv' : idIndiv, 'codeSkill' : codeSkill, 'usagesSkill' : usagesSkill},
            'success' : function(data){ 
                $('#btn_addSkill span.fa').removeClass('fa-spin');
                location.reload();
            },
            'error' : function(err){
                console.log(err);
            }
        });       
    }
    /* Delete Skill */
    var controller = 'Personnages',
        base_url = '<?php echo site_url();?>', 
        data;
    $('.btn_deleteSkill').on('click', function(event){            
        event.preventDefault();
        idPerso = $('#hiddenId').val();
        idIndiv = $('#hiddenIdIndiv').val();
        idListSkill = $(this).attr('data-idListSkill');
        
        deleteSkill_ajax(idPerso, idIndiv, idListSkill);            
    });
    
  
    function deleteSkill_ajax(idPerso, idIndiv, idListSkill){  
        $('.btn_deleteSkill span.fa').removeClass('fa-trash').addClass('fa-refresh').addClass('fa-spin');
        $.ajax({
            'url' : base_url + controller + '/deleteSkill', 
            'type' : 'POST',
            'data' : {'idPerso' : idPerso, 'idIndiv' : idIndiv, 'idListSkill' : idListSkill},
            'success' : function(data){ 
                $('.btn_deleteSkill span.fa').removeClass('fa-refresh').addClass('fa-trash').removeClass('fa-spin');
                location.reload();
            },
            'error' : function(err){
                console.log(err);
            }
        });       
    }

    /* Travail */
    var controller = 'Personnages',
        base_url = '<?php echo site_url();?>', 
        data;
    $('#btn-paieTravail').on('click', function(event){            
        event.preventDefault();
        idPerso = $('#hiddenId').val();
        idIndiv = $('#hiddenIdIndiv').val();
        idTravail = $(this).attr('data-idtravailleur');
        
        deleteTravail_ajax(idPerso, idIndiv, idTravail);            
    });
    
  
    function deleteTravail_ajax(idPerso, idIndiv, idTravail){  
        $.ajax({
            'url' : base_url + controller + '/deleteTravail', 
            'type' : 'POST',
            'data' : {'idPerso' : idPerso, 'idIndiv' : idIndiv, 'idTravail' : idTravail},
            'success' : function(data){ 
                location.reload();
            },
            'error' : function(err){
                console.log(err);
            }
        });       
    }

    /* LevelUp */
    var controller = 'Personnages',
        base_url = '<?php echo site_url();?>', 
        data;
    $('#btn-lvlUp').on('click', function(event){            
        event.preventDefault();
        idPerso = $('#hiddenId').val();
        idIndiv = $('#hiddenIdIndiv').val();
        
       lvlUp_ajax(idPerso, idIndiv);            
    });
    
  
    function lvlUp_ajax(idPerso, idIndiv){  
        $.ajax({
            'url' : base_url + controller + '/lvlUp', 
            'type' : 'POST',
            'data' : {'idPerso' : idPerso, 'idIndiv' : idIndiv},
            'success' : function(data){ 
                location.reload();
            },
            'error' : function(err){
                console.log(err);
            }
        });       
    }

    /* Modif PV */
    var controller = 'Personnages',
        base_url = '<?php echo site_url();?>', 
        data;

    $('#btn-persoPv').on('click', function(event){            
        event.preventDefault();
        idPerso = $('#hiddenId').val();
        idIndiv = $('#hiddenIdIndiv').val();
        raison = $('#select-persoPv :selected').val();
        commentairesPv = $('#comment-persoPv').val();
        
       modifPv_ajax(idPerso, idIndiv, raison, commentairesPv);            
    });
    
  
    function modifPv_ajax(idPerso, idIndiv, raison, commentairesPv){  
        $.ajax({
            'url' : base_url + controller + '/modifPv', 
            'type' : 'POST',
            'data' : {'idPerso' : idPerso, 'idIndiv' : idIndiv, 'raison' : raison, 'commentairesPv' : commentairesPv},
            'success' : function(data){ 
                location.reload();
            },
            'error' : function(err){
                console.log(err);
            }
        });       
    }

 });
</script><!-- Page Content --><!-- Page Content -->
<?php
    $persoData = $personnageData['persoData'];
    $persoSkills = $personnageData['persoSkills'];
    $persoExperience = $personnageData['persoExperience'];
    $persoQuetes = $personnageData['persoQuetes'];
    $persoPv = $personnageData['persoPv'];

    $persoData = $persoData[0];

    $indivDettes = $individuData['indivDettes'];

    $listSkills = $personnageData['listSkills'];

    $travail = $personnageData['travail'];
?>

<input type="hidden" id="hiddenId" value="<?php echo $persoData->Id; ?>">
<input type="hidden" id="hiddenIdIndiv" value="<?php echo $persoData->IdIndividu; ?>">
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h1 class="page-header">Détails de <?php echo $persoData->Prenom .' ' .$persoData->Nom; ?></h1>
            </div>
           <!-- /.col-lg-12 -->            
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <div class="row">
                    <h3>DÉTAILS</h3>
                    <table class="table table-striped table-responsive">
                        <tr>
                            <td><strong>Nom</strong></td>
                            <td><?php echo $persoData->Prenom .' ' .$persoData->Nom; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Classe / Niveau</strong></td>
                            <td>
                                <?php echo $persoData->persoClasse .' / ' .$persoData->Niveau; ?>
                                <button id="btn-lvlUp" class="btn btn-success">LVL UP</button>
                                <button id="btn-lvlDown" class="btn btn-disable" disabled="disabled">LVL DOWN</button>

                                    
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Race</strong></td>
                            <td><?php echo $persoData->persoRace; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Religion</strong></td>
                            <td><?php echo $persoData->persoReligion; ?>
                                <input type="hidden" id="hiddenReligion" value="<?php echo $persoData->CodeReligion; ?>">
                                <div class="row">
                                    <select name="religion" id="religion" class="form-control searchField">
                                        <option value="TOUDEMO">Tous les Démons</option>

                                        <option value="AMAIRA">Amaï'ra</option>
                                        <option value="CHAOSSM">Chaos</option>
                                        <option value="GODTAKK">Godtakk</option>
                                        <option value="KAALKH">Kaalkhorn</option>
                                        <option value="KHALII">Khalii</option>
                                        <option value="NOCTAVE">Noctave</option>
                                        <option value="OTTOKOM">Ottor-kom</option>
                                        <option value="TOYASH">Toyash</option>

                                        <option value="">-----</option>
                                        
                                        <option value="TOUDIEU">Tous les Dieux</option>
                                        
                                        <option value="AYKA">Ayka</option>
                                        <option value="GAEA">Gaea</option>
                                        <option value="GALLEON">Galléon</option>
                                        <option value="GOLGOSM">Golgoth</option>
                                        <option value="MAKUDAR">Mak'udar</option>
                                        <option value="SIBYL">Les Sibylles</option>
                                        <option value="SYLVA">Sylva</option>                    
                                        <option value="USIRE">Usire</option>
                                        <option value="">-----</option>
                                        <option value="ESPRITS">Les Esprits</option>
                                        <option value="LAVOIE">La Voie</option>
                                    </select>
                                </div>
                                <div class="row">
                                    <button class="btn btn-primary" classe="col-xs-6" id="btn_changeReligion">Modifier <span class="fa fa-refresh fa-reverse"></span></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Provenance</strong></td>
                            <td><?php echo $persoData->Provenance; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Création</strong></td>
                            <td>
                                <?php 
                                    $dateCreation = explode(' ',$persoData->DateCreation);
                                    echo $dateCreation[0];
                                ?>
                            </td>
                        </tr>                        
                    </table>
                </div>

                <div class="row">
                    <h3>XP</h3>
                    
                        <?php 
                            $totalXP = 0;

                            foreach ($persoExperience as $XP){ 
                                $totalXP =  $totalXP + intval($XP['XP']);
                            }
                        ?>

                    <div class="row">   
                        <div class="col-xs-12">
                            <h4>Actuellement : <?php echo $totalXP; ?> Points d'expérience</h4>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <h3>TRAVAIL</h3>

                    <?php if($travail){ ?>
                        <p>Ce personnage a travaillé pour le groupe <strong><?php echo $travail->groupeNom; ?>.</strong></p>
                        <button id="btn-paieTravail" class="btn btn-primary" data-idtravailleur="<?php echo $travail->Id; ?>">Joueur Payé $$$</span></button>
                    <?php } else { ?>
                        <p>Ce personnage n'a pas travaillé.</p>
                    <?php  } //endif ?>                

                </div>

            </div>
            <div class="col-xs-12 col-md-6">
                
                <!--<div class="row">
                    <div class="row">
                        <div class="col-xs-12">
                            <h4>Ajustements</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4  col-xs-12 form-group">
                            <label for="nbXP">Quantité (-5 pour retirer 5XP)</label>
                            <input class="form-control" type="text" name="nbXP" id="nbXP">
                        </div>
                        <div class="form-group col-xs-12 col-md-8">
                            <label for="commentaireXP">Commentaires (ogligatoires | Min. 15)</label>
                            <textarea class="form-control" name="commentaireXP" id="commentaireXP"></textarea>
                        </div>
                        <div class="col-xs-12">
                            <button class="btn btn-primary btn-block" id="btn_adjustXP" disabled="disabled">Ajuster les XP <span class="fa fa-refresh fa-reverse"></button>
                        </div>
                    </div>
                </div>-->

                <div class="row">
                    <h3>POINTS DE VIE</h3>

                    <table class="table table-striped table-responsive">
                        <tr>
                            <th>Raison</th>
                            <th>PV</th>
                            <th>Commentaires</th>
                        </tr>
                        <?php $totalPV = 0; ?>
                        <?php foreach ($persoPv as $pv) { ?>
                            <tr>
                                <td><?php echo $pv['Raison']; ?></td>
                                <td>
                                    <?php 
                                        $totalPV = $totalPV + intval($pv['PV']);                                        
                                        echo $pv['PV'];                                     
                                    ?>                                    
                                </td>
                                <td><?php echo $pv['Commentaires']; ?></td>
                            </tr>
                        <?php } ?>
                    </table>

                    <h4>Total de Points de Vie = <?php echo $totalPV; ?></h4>

                    <div class="col-xs-8">
                        <h4>Déclarer une mort</h4>
                        <select name="" id="select-persoPv" class="form-control">
                            <option value="Shadow Life">Shadow Life</option>
                            <option value="Premiers Soins">Premiers Soins</option>
                            <option value="Résurrection">Résurrection</option>
                            <option value="Rappel à la vie">Rappel à la vie</option>
                            <option value="Possession du Phoenix">Possession du Phoenix</option>
                            <option value="Résurrection Arcanique">Résurrection Arcaniqu</option>
                            <option value="Miracle">Miracle</option>
                            <option value="Feindre la Mort">Feindre la Mort</option>
                            <option value="Renaissance Sauvage">Renaissance Sauvage</option>
                            <option value="Autre">Autre</option>
                        </select>
                    </div>
                    <div class="col-xs-12">
                        <strong>Commentaires</strong>
                        <input name="comment-persoPv" class="form-control" id="comment-persoPv" type="text">
                    </div>
                    <div class="col-xs-12">
                        <button id="btn-persoPv" class="btn btn-success btn-block">Modifier</button>
                    </div>
                    
                </div>
            </div>
            <div class="row">        
                <div class="col-xs-12 col-md-6">
                    <h3 id="toggleCompetences"><a href="#" >Compétences <span class="fa fa-chevron-down"></span></a></h3>
                    <?php //echo var_dump($listSkills); ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <select class="form-control" name="selectAddSkill" id="selectAddSkill">
                                <?php 
                                    foreach ($listSkills as $listSkill) { ?>                                

                                    <option value="<?php echo $listSkill['Code']; ?>" data-usages="<?php echo $listSkill['Usages']; ?>"><?php echo $listSkill['Nom']; ?></option>                                    
                                 <?php } //end foreach ?>
                            </select>
                            <button id="btn_addSkill" class="btn btn-block btn-primary">Ajouter Compétence <span class="fa fa-refresh fa-reverse"></span></button>
                            <table class="table table-responsive maxTableHeight">
                                <tr>
                                    <th>Nom Compétence</th>
                                    <th>Type</th>
                                    <th>UEC</th>
                                    <th>Date d'Acquisition</th>
                                    <th>Code d'Acquisition</th>
                                    <th></th>
                                </tr>
                                <?php foreach ($persoSkills as $skill) { ?>                                                      
                                    <tr>
                                        <td>
                                            <?php 
                                                if($skill['skillNom'] != ''){
                                                    echo $skill['skillNom'];
                                                } else {
                                                    echo $skill['skillSpecNom'];
                                                }
                                            ?>
                                        </td>
                                        <td><?php echo $skill['Type']; ?></td>
                                        <td><?php echo $skill['SUM(skills.Usages)']; ?></td>
                                        <td><?php echo $skill['DateCreation']; ?></td>
                                        <td><?php echo $skill['CodeAcquisition']; ?></td>
                                       <td><button class="btn-danger btn btn_deleteSkill" data-idListSkill="<?php echo $skill['Id'] ?>"><span class="fa fa-trash fa-reverse"></span></button></td>
                                    </tr>
                                <?php } //end foreach ?>
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