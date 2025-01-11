<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
          <div class="col-xs-12">
            <h1 class="page-header">Avertissements</h1>
          </div>        
    <!-- /.col-lg-12 -->
        </div>
        <div class="row">
            <div class="col-xs-12">
                <ul class="nav nav-tabs" role="tablist">
                  <li role="presentation" class="active">
                    <a href="#avertissements" aria-controls="avertissements" role="tab" data-toggle="tab">Avertissements</a>
                  </li>
                  <li role="presentation">
                    <a href="#addAvertissement"  aria-controls="addAvertissement" role="tab" data-toggle="tab">Ajouter un avertissement</a>
                  </li>
                </ul>

                <div class="tab-content col-xs-12">
                    <div role="tabpanel" class="tab-pane active" id="avertissements">
                        <div class="row">
                            <h3>Filtrer les blames de joueurs</h3>
                            <div class="col-xs-12 col-md-4">
                                <label for="compte">Compte</label>
                                <input type="text" name="compte" id="compte" class="form-control searchField">
                                <h5><em>Le filtre s'appliquera avec au moins 3 caractères</em></h5>
                            </div>
                        </div>

                        <br>
                        <br>
                        <br>

                        <div class="row">
                            <div class="col-xs-12">
                                <table class="table table-striped table-responsive avertissements">
                                    <tr class="first">
                                        <th>Type</th>
                                        <th>Cible</th>
                                        <th>Inscripteur</th>
                                        <th>Annulateur</th>
                                        <th>Commentaires</th>
                                        <th></th>
                                    </tr>
                                    <?php foreach($avertissements as $avertissement) : ?>
                                        <tr data-CibleNom="<?= strtolower($avertissement->Cible_Nom); ?>" data-CiblePseudo="<?= strtolower($avertissement->Cible_Pseudo); ?>">
                                            <td><?= $avertissement->Type; ?></td>
                                            <td>
                                                <?php 
                                                    if(!is_null($avertissement->Cible_Pseudo)) :
                                                        echo $avertissement->Cible_Pseudo . ' <em>(';
                                                    endif; 
                                                    echo $avertissement->Cible_Nom;
                                                    if(!is_null($avertissement->Cible_Pseudo)) :
                                                        echo ')</em>';
                                                    endif;
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                    if(!is_null($avertissement->Insc_Pseudo)) :
                                                        echo $avertissement->Insc_Pseudo . ' <em>(';
                                                    endif; 
                                                    echo $avertissement->Insc_Nom;
                                                    if(!is_null($avertissement->Insc_Pseudo)) :
                                                        echo ')</em>';
                                                    endif;
                                                ?>
                                                <br>
                                                <?= $avertissement->DateInscription; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                    if(!is_null($avertissement->Annul_Pseudo)) :
                                                        echo $avertissement->Annul_Pseudo . ' <em>(';
                                                    endif; 
                                                    echo $avertissement->Annul_Nom;
                                                    if(!is_null($avertissement->Annul_Pseudo)) :
                                                        echo ')</em>';
                                                    endif;
                                                ?>
                                                <br>
                                                <?= $avertissement->DateAnnulation; ?>
                                            </td>
                                            <td><?= $avertissement->Commentaires; ?></td>
                                            <td>
                                                <?php if($_SESSION['infoUser']->NiveauAcces >= 3): ?>
                                                    <a class="editAvertissement" data-idAvertissement="<?= $avertissement->Id; ?>" href="#">
                                                        <button class="btn btn-primary"><span class="fa fa-edit"></span></button>
                                                    </a>
                                                    <a class="annulAvertissement" href="<?= site_url('Administration/annulerAvertissement/' . $avertissement->Id); ?>">
                                                        <button class="btn btn-danger"><span class="fa fa-times"></span></button>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div id="editAvertissement" class="col-md-6 col-xs-12 toPop">
                                <h3></h3>
                                <form id="editAvertissement-form" method="POST" action="<?= 'Administration/editAvertissement/';?>">
                                    <div class="form-group col-xs-6">
                                        <label for="Insc">Inscripteur</label>
                                        <input type="text" name="Insc" class="form-control" placeholder="Inscripteur" readonly>
                                    </div>
                                    <div class="form-group col-xs-6">
                                        <label for="Annul">Annulateur</label>
                                        <input type="text" name="Annul" class="form-control" placeholder="Annulateur" readonly>
                                        <h5><em>Annulé le <span id="dateAnnulation"></span></em></h5>
                                    </div>
                                    <div class="form-group col-xs-12">
                                        <label for="Commentaires">Commentaires</label>
                                        <input type="text" name="Commentaires" class="form-control" placeholder="Commentaires">
                                    </div>
                                    <div class="form-group col-xs-12">
                                        <label for="Raison">Raison</label>
                                        <textarea name="Raison" rows="5" class="form-control" style="resize: none;"></textarea>
                                    </div>

                                    <div class="form-group col-xs-12">
                                        <button type="submit" class="btn btn-primary">Sauvegarder <span class="fa fa-save"></span></button>
                                        <button type="button" class="btn btn-primary closePopup" onClick="closePopup();">Annuler les modifications <span class="fa fa-window-close"></span></button>
                                    </div>
                                </form>
                            </div>            
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="addAvertissement">
                        <div class="row">
                            <h3>Ajouter un avertissement ou blame à un joueur</h3>
                            <div class="col-xs-12">
                                <h4>Rechercher un joueur</h4>
                                <form id="addAvertissement-form" action="#">
                                    <div class="col-xs-12 col-md-4">
                                        <label for="indiv">Prénom, Nom ou Pseudo du Joueur</label>
                                        <input type="text" name="indiv" id="indiv" class="form-control">
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <label for="perso">Prénom, Nom du Personnage</label>
                                        <input type="text" name="perso" id="perso" class="form-control">
                                    </div>
                                    <div class="col-xs-12">
                                        <button class="btn btn-primary">Lancer la recherche <span class="fa fa-search"></span></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row">
                            <h3>Résultat de la recherche</h3>
                            <div id="addAvertSearch_results" class="col-xs-12 col-md-8">
                                <table class="table table-responsive table-striped">
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div id="popAddAvert_form" class="col-md-6 col-xs-12 toPop">
                                <h3>Ajouter un avertissement ou blâme à <span class="nomJoueur"></span></h3>
                                <form method="POST" action="<?= site_url("Administration/addAvertissement"); ?>">
                                    <input type="hidden" name="idIndividu" value="">
                                    <input type="hidden" name="idInscripteur" value="<?= $_SESSION['infoUser']->Id; ?>">
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <label for="type">Type</label>
                                            <select name="type" id="type" name="type" class="form-control">
                                                <option value="AVERT">Avertissement</option>
                                                <option value="BLAME">Blâme</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <br>
                                        <div class="col-xs-6">
                                            <label for="raison">Raison</label>
                                            <input class="form-control" type="text" name="raison" required>
                                        </div>
                                        <div class="col-xs-6">
                                            <label for="commentaires">Commentaires</label>
                                            <textarea class="form-control" name="commentaires" id="commentaires" rows="5" ></textarea>
                                            
                                        </div>
                                    </div>
                                    <div class="row">
                                        <br>
                                        <div class="col-xs-12">
                                            <button class="btn btn-primary">Ajouter <span class="fa fa-plus"></span></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>        
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->

    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->

<script>
    $('.editAvertissement').on('click', function(e){
        e.preventDefault();

        $.ajax({
            'url' : "<?php echo site_url('Administration/getSingleAvertissement/'); ?>" + $(this).attr('data-idAvertissement'),
            'method' : 'GET',
            'data' : {},
            'success' : function(data){

                var data = JSON.parse(data),
                    date = data.DateInscription.split(' ')[0],
                    dateAnnulation = (data.DateAnnulation == null)?'':data.DateAnnulation.split(' ')[0];

                $('#editAvertissement-form')[0].action = "<?php echo site_url("Administration/editAvertissement/"); ?>" + data.Id;

                $('#editAvertissement > h3').html(data.Type + ' de ' + data.Cible_Nom + ' du ' + date);

                $('#editAvertissement-form').find('[name="Insc"]').val(data.Insc_Nom);

                $('#editAvertissement-form').find('[name="Annul"]').val(data.Annul_Nom);
                if(dateAnnulation == ''){
                    $('#editAvertissement-form').find('h5').html('');
                }else{
                    $('#editAvertissement-form').find('#dateAnnulation').html(dateAnnulation);
                }

                $('#editAvertissement-form').find('[name="Commentaires"]').val(data.Commentaires);

                $('#editAvertissement-form').find('[name="Raison"]').val(data.Raison);

                $('#editAvertissement').bPopup({
                    opacity : 0.8,
                    modalClose: false,
                })
            },
            'error' : function(err){
                console.log(err);
            }
        });
    });

    $('.closePopup').on('click', function(e){
        e.preventDefault();

        $('#editAvertissement').bPopup().close();
    });

    $('.annulAvertissement').on('click',function (e){
        if(confirm("Êtes-vous sûr(e) de vouloir annuler cet avertissement ou blâme?") == false){
            e.preventDefault();
        }
    });

    $('#compte').on('keyup', function(e){
        if($(this).val().length < 3){
            e.preventDefault();
            $('table.avertissements tr').show();
        } else{
            var rows = $('table.avertissements tr:not(.first)');
            var needle = $(this).val().toLowerCase();

            rows.each(function(i, el){                
                if( ($(el).data().ciblenom != null && $(el).data().ciblenom.includes(needle)) || 
                    ($(el).data().ciblepseudo != null && $(el).data().ciblepseudo.includes(needle) ) ){
                    $(el).show();
                } else{
                    $(el).hide();
                }
            });
        }
    });

    /*Add Avertissement*/
    $('#addAvertissement-form').on('submit', function(e){
        e.preventDefault();

        var indivData = $(this.indiv).val(),
            persoData = $(this.perso).val()

        $.ajax({
            'url' : "<?= site_url('Administration/addAvertSearch_ajax') ?>",
            'method' : 'POST',
            'data' : {
                'indivData' : indivData,
                'persoData' : persoData
            },
            success : function(data){
                var data = JSON.parse(data),
                    html = '';

                    html += '<tr>';
                    html += '<th>Joueur<th>';
                    html += '<th>Actions<th>';
                    html += '</tr>';

                    data.forEach(function(el, i){
                        var strIndiv = (data[i].Pseudo != null)?data[i].Pseudo + '<br><em>(' + data[i].indivNom + ')</em>':data[i].indivNom;

                        html += '<tr>';
                        html += '<td>' + strIndiv + '<td>';
                        html += '<td><button class="btn btn-primary" onclick="popAddAvert_form(\''+strIndiv +'\','+data[i].Id +')" data-indivId="' +strIndiv +'"><span class="fa fa-plus"></span></button><td>';
                        html += '</tr>';

                    });                    

                $('#addAvertSearch_results table').html(html);
            },
            error : function (err){
                console.log(err);
            }
        });
    });

    function popAddAvert_form(strIndiv, idCible){
        $('#popAddAvert_form').find('h3 .nomJoueur').html(strIndiv);
        $('#popAddAvert_form').find('[name="idIndividu"]').val(idCible);

        $('#popAddAvert_form').bPopup({
            opacity: 0.8
        });


    }

</script>