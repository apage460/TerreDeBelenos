<div id="page-wrapper">
  <div class="container-fluid">
    <div class="row">
      <div class="col-xs-12">
        <h1 class="page-header">Gestion de Missions</h1>
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12">
        <h2>Créer une mission</h2>

        <div class="row">
          <div class="col-xs-12">
            <?php echo validation_errors(); ?>
            <?php 
              if(isset($file_error)){
                var_dump($file_error);
              }
            ?>
            <form action="<?php echo site_url('histoire/addMission');?>" enctype="multipart/form-data" method="POST">
              <div class="row">
                <div class="form-group col-md-3 col-xs-12">
                  <label for="idActivite">Activité</label>
                  <select name="idActivite" id="idActivite" class="form-control">
                    <?php foreach ($activites as $activite) : ?>
                      <option value="<?= $activite->Id; ?>"><?= $activite->Nom;?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="form-group col-md-3 col-xs-12">
                  <label for="Code">Code de mission*</label>
                  <input type="text" name="Code" id="Code" required="required" class="form-control">
                </div>

                <div class="form-group col-md-3 col-xs-12">
                  <label for="Nom">Nom de mission*</label>
                  <input type="text" name="Nom" id="Nom" required="required" class="form-control">
                </div>

                <div class="form-group col-md-3 col-xs-12">
                  <label for="ProposePar">Proposé par</label>
                  <input type="text" name="ProposePar" id="ProposePar"class="form-control">
                </div>
              </div>

              <div class="row">
                <div class="form-group col-md-3 col-xs-12">
                  <label for="MiseEnSituation">Mise en Situation</label>
                  <textarea rows="5" class="form-control" name="MiseEnSituation" id="MiseEnSituation"></textarea>
                </div>
                <div class="form-group col-md-3 col-xs-12">
                  <label for="Objectif">Objectif</label>
                  <textarea rows="5" name="Objectif" id="Objectif" class="form-control"></textarea>
                </div>
                <div class="form-group col-md-3 col-xs-12">
                  <label for="actionDemandee">Action demandée</label>
                  <textarea rows="5" name="actionDemandee" id="actionDemandee" class="form-control"></textarea>
                </div>
                <div class="form-group col-md-3 col-xs-12">
                  <label for="CodeRoyaume">Duché*</label>
                  <select name="CodeRoyaume" id="CodeRoyaume" class="form-control">
                    <option value="TBD">À déterminer</option>
                    <?php foreach ($royaumes as $royaume) : ?>
                      <option value="<?= $royaume->Code; ?>"><?= $royaume->Nom; ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>

              <div class="row">
                <div class="form-group col-md-3 col-xs-12">
                  <label for="Chads">Chads</label>
                  <input class="form-control" name="Chads" id="Chads" type="number" min="0" value="0">
                </div>
                <div class="form-group col-md-3 col-xs-12">
                  <label for="Faveurs">Faveurs</label>
                  <input name="Faveurs" id="Faveurs" class="form-control" type="text">
                </div>
                <div class="form-group col-md-3 col-xs-12">
                  <label for="AutreRessources">Autres Ressources</label>
                  <input name="AutreRessources" id="AutreRessources" class="form-control" type="text">
                </div>
                <div class="form-group col-md-3 col-xs-12">
                  <label>Succès et Sabotage</label>
                  <div class="checkbox">
                    <label for="IndSucces">
                      <input id="IndSucces" name="IndSucces" value="1" type="checkbox">Mission réussie
                    </label>
                  </div>
                  <div class="checkbox">
                    <label for="IndSabotage">
                      <input id="IndSabotage" name="IndSabotage" value="1" type="checkbox">Mission sabotée
                    </label>
                  </div>
                  <div class="checkbox">
                    <label for="IndExpedition">
                      <input id="IndExpedition" name="IndExpedition" value="1" type="checkbox">Expédition
                    </label>
                  </div>
                </div>
              </div>

              <!-- UPLOAD-->
              <!---<div class="row">
                <div class="form-group col-md-3 col-xs-12">
                  <label for="FichierResultat">Fichier PDF <span class="fa fa-file-pdf-o"></span></label>
                  <input id="FichierResultat" name="FichierResultat" type="file" class="form-control">
                </div>
              </div>-->
              <div class="row">
                <div class="form-group col-md-3 col-xs-12">
                  <button class="btn btn-primary">Créer la mission <span class="fa fa-flag"></span></button>
                </div>
              </div>

            </form>
          </div>
        </div>
        <div class="row">
          <?php if(!empty($missions)) : ?>
          <hr>
          <h2>Missions existantes</h2>
          <div class="col-xs-12"> 
            <table class="table table-striped"> 
              <tr>
                <th>Activité</th>
                <th>Code</th>
                <th>Nom</th>
                <th>Mise en situation</th>
                <th>Action</th>
              </tr>
              <?php foreach ($missions as $mission) : ?>
              <tr>
                <td><?= $mission->nomActivite; ?></td>
                <td><?= $mission->Code; ?></td>
                <td><?= $mission->Nom; ?></td>
                <td><?= $mission->MiseEnSituation; ?></td>
                <td>
                  <button class="btn btn-primary" onClick="editMission(<?= $mission->Id; ?>);"><span class="fa fa-edit"></span></button>
                  <button style="margin-top:5px;" class="btn btn-primary" onClick="populateMission(<?= $mission->Id;?>)"><span class="fa fa-users"></span></button>
                  <button style="margin-top:5px;" class="btn btn-primary" onClick="duplicateMission(<?= $mission->Id;?>, '<?= $mission->Nom; ?>')"><span class="fa fa-copy"></span></button>
                </td>
              </tr>
              <?php endforeach; ?>
            </table>
          </div>  
        <?php else: ?>
          <hr>
          <h2>Aucune mission n'existe</h2>
        <?php endif; ?>
      </div>
    </div>
    <div id="editMission" class="col-md-8 col-xs-12 toPop">
        <h2>Modifier une mission</h2>
        
        <div class="col-xs-9">
          <form id="formEditMission" action="<?php echo site_url('histoire/editMission');?>" enctype="multipart/form-data" method="POST">
            <input type="hidden" id="edit-idChef" name="idChef">
            <div class="row">
              <div class="form-group col-md-4 col-xs-12">
                <label for="edit-idActivite">Activité</label>
                <select name="idActivite" id="edit-idActivite" class="form-control">
                  <?php foreach ($activites as $activite) : ?>
                    <option value="<?= $activite->Id; ?>"><?= $activite->Nom;?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-group col-md-4 col-xs-12">
                <label for="edit-Code">Code de mission*</label>
                <input type="text" name="Code" id="edit-Code" required="required" class="form-control">
              </div>

              <div class="form-group col-md-4 col-xs-12">
                <label for="edit-Nom">Nom de mission*</label>
                <input type="text" name="Nom" id="edit-Nom" required="required" class="form-control">
              </div>              
            </div>

            <div class="row">
              <div class="form-group col-md-4 col-xs-12">
                <label for="edit-ProposePar">Proposé par</label>
                <input type="text" name="ProposePar" id="edit-ProposePar" class="form-control">
              </div>
              <div class="form-group col-md-4 col-xs-12">
                <label for="edit-MiseEnSituation">Mise en Situation</label>
                <textarea class="form-control" name="MiseEnSituation" id="edit-MiseEnSituation"></textarea>
              </div>
              <div class="form-group col-md-4 col-xs-12">
                <label for="edit-Objectif">Objectif</label>
                <textarea name="Objectif" id="edit-Objectif" class="form-control"></textarea>
              </div>              
            </div>

            <div class="row">
              <div class="form-group col-md-4 col-xs-12">
                <label for="actionDemandee">Action demandée</label>
                <textarea rows="5" name="actionDemandee" id="edit-actionDemandee" class="form-control"></textarea>
              </div>
              <div class="form-group col-md-4 col-xs-12">
                <label for="edit-CodeRoyaume">Duché*</label>
                <select name="CodeRoyaume" id="edit-CodeRoyaume" class="form-control">
                  <option value="TBD">À déterminer</option>
                  <?php foreach ($royaumes as $royaume) : ?>
                    <option value="<?= $royaume->Code; ?>"><?= $royaume->Nom; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group col-md-4 col-xs-12">
                <label for="edit-Chads">Chads</label>
                <input class="form-control" name="Chads" id="edit-Chads" type="number" min="0" value="0">
              </div>
            </div>

            <div class="row">
              <div class="form-group col-md-4 col-xs-12">
                <label for="edit-Faveurs">Faveurs</label>
                <input name="Faveurs" id="edit-Faveurs" class="form-control" type="text">
              </div>              
              <div class="form-group col-md-4 col-xs-12">
                <label for="edit-AutreRessources">Autres Ressources</label>
                <input name="AutreRessources" id="edit-AutreRessources" class="form-control" type="text">
              </div>
              <div class="form-group col-md-4 col-xs-12">
                <label>Succès et Sabotage</label>
                <div class="checkbox">
                  <label for="edit-IndSucces">
                    <input id="edit-IndSucces" name="IndSucces" value="1" type="checkbox">Mission réussie
                  </label>
                </div>
                <div class="checkbox">
                  <label for="edit-IndSabotage">
                    <input id="edit-IndSabotage" name="IndSabotage" value="1" type="checkbox">Mission sabotée
                  </label>
                </div>
                <div class="checkbox">
                  <label for="edit-IndExpedition">
                    <input id="edit-IndExpedition" name="IndExpedition" value="1" type="checkbox">Expédition
                  </label>
                </div>
              </div>
            </div>        

            <!-- UPLOAD-->
            <div class="row">
              <div class="form-group col-md-4 col-xs-12">
                <a id="existingFile" href="#" target="_blank"></a>
                <br>
                <label for="FichierResultat">Fichier PDF <span class="fa fa-file-pdf-o"></span></label>
                <input id="FichierResultat" name="FichierResultat" type="file" class="form-control">
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-4 col-xs-12">
                <button class="btn btn-primary">Sauvegarder la mission <span class="fa fa-save"></span></button>
              </div>
            </div>

          </form>
        </div>
        <div class="col-xs-3" id="participantsContainer">
          <table class="table">
          </table>
        </div>
    </div>
    <div id="populateMission" class="col-md-8 col-xs-12 toPop">
      <h2>Peupler la mission</h2>
      <form id="formPopulateMission" action="<?php echo site_url('histoire/populateMission');?>" method="POST">
        <table class="table table-striped">
          <tr class="titles">
            <th>Personnage</th>
            <th>Métier</th>
            <th>Chef?</th>
            <th>Récompense</th>
          </tr>
        </table>

        <button role="submit" class="btn btn-primary">Peupler la Mission <span class="fa fa-users"></span></button>
      </form>
    </div>
  </div>
</div>


<script>
    function fillEditForm(data){
      $('#edit-Code').val(data.Code);
      $('#edit-Nom').val(data.Nom);
      $('#edit-ProposePar').val(data.ProposePar);
      $('#edit-MiseEnSituation').val(data.MiseEnSituation);
      $('#edit-Objectif').val(data.Objectif);
      $('#edit-Cible').val(data.Cible);
      $('#edit-Chads').val(data.Chads);
      $('#edit-Faveur').val(data.Faveur);
      $('#edit-AutreRessources').val(data.AutreRessources);
      $('#edit-idChef').val(data.IdChef);
      $('#edit-actionDemandee').val(data.ActionDemandee);

      var idChef = data.IdChef;

      if(data.IndSucces == 1){
        $('#edit-IndSucces').attr('checked', 'checked');
      } else{
        $('#edit-IndSucces').removeAttr('checked');
      }
      if(data.IndSabotage == 1){
        $('#edit-IndSabotage').attr('checked', 'checked');
      } else{
        $('#edit-IndSabotage').removeAttr('checked');
      }
      if(data.IndExpedition == 1){
        $('#edit-IndExpedition').attr('checked', 'checked');
      } else{
        $('#edit-IndExpedition').removeAttr('checked');
      }

      $('#edit-CodeRoyaume option[value="' +data.CodeRoyaume +'"]').attr('selected','selected');
      $('#edit-idActivite option[value="' +data.IdActivite +'"]').attr('selected','selected');

      if(data.FichierResultat != '' || data.FichierResultat != null){
        $('#existingFile').html(data.FichierResultat).attr('href',"/BD/uploads/Missions/" + data.NomActivite +"/" + data.FichierResultat)
      } else{
        $('#existingFile').html('').attr('href', '#');
      }

      /*PARTICIPANTS*/
      $('#participantsContainer table tr').remove();

      data.participants.forEach(function(el, i){
        var html = '<tr data-idParticipation="' + el.Id + '">';
        if(el.IdPersonnage == idChef){
          html += '<td><span class="fa fa-star"></span><strong>' + el.Prenom + ' ' + el.Nom + "</strong><br>(" + el.Metier +")</td> ";
        } else{
          html += "<td><strong>" + el.Prenom + ' ' + el.Nom + "</strong><br>(" + el.Metier +")</td> ";
        }
        html += "<td><a href=\"#\" onClick=\"removeParticipant(" + el.Id + ",\'" + el.Prenom.replace("'","`") + " " + el.Nom.replace("'","`") + "\');\"><span class=\"fa fa-trash\"></span></a><td>";
        html += "</tr>";

        $('#participantsContainer table').append(html);

      });
    }

    function editMission(idMission){
      var url = "<?php echo site_url('histoire/editMission/');?>" +idMission;     
      $.ajax({
        'url' : '<?php echo site_url('histoire/getMission');?>/' + idMission,
        'method' : 'GET',
        'success' : function(data){
          var data = JSON.parse(data);

          $('#formEditMission').attr('action', url);

          fillEditForm(data);
        },
        'error' : function(err){
          console.log(err);
        }
      });

      $('#editMission').bPopup({
        follow: [false, false],
      });
    }

    function fillPopulateForm(data, metiers, idMission){

      data.forEach(function(el, i){
        var tr = '<tr class="' +idMission +'">';
        tr += '<td>' +el.Prenom +' ' +el.Nom +' <em>(' +el.indPrenom +' ' +el.indNom +')</em></td>';
        tr += '<td><select class="form-control" id="Metier-' +el.Id +'" name="Metier-' +el.Id +'">';
        if(metiers[el.IdPersonnage].length == 0){
          tr += '<option value="AUCUN MÉTIER">AUCUN MÉTIER</option>';
        } else {
          metiers[el.IdPersonnage].forEach(function(el, i){
              tr += '<option value="' +el.Nom +'">' +el.Nom +'</option>';
          });
        }
        tr += '</select>';
        tr += '<td><input type="radio" id="IdChef" name="IdChef" value="'+el.Id + '">' +'</td>';
        tr += '<td><input class="form-control" type="text" id="Resultat-' +el.Id +'" name="Resultat-' +el.Id +'" value=""></td>';
        tr += '</tr>';

        $('#formPopulateMission .titles').after(tr);
      });
    }

    function populateMission(idMission){
      var url = "<?php echo site_url('histoire/populateMission/');?>" +idMission;     
      $.ajax({
        'url' : '<?php echo site_url('histoire/getQRLectures');?>',
        'method' : 'GET',
        'success' : function(data){
          var data = JSON.parse(data);

          $('#formPopulateMission').attr('action', url);
          $('#formPopulateMission tr:not(.titles)').remove();

          fillPopulateForm(data.lectures, data.metiers, idMission);
        },
        'error' : function(err){
          console.log(err);
        }
      })

      $('#populateMission').bPopup({
        follow: [false, false],
      });
    }

    function duplicateMission(idMission, titre){
      if( confirm('Voulez-vous faire un copie de la mission "' + titre +'" ?') == true ){
        $.ajax({
          'url' : '<?php echo site_url('histoire/duplicateMission/');?>' + idMission,
          'method' : 'POST',
          'success' : function(data){

            location.reload();
          },
          'error' : function(err){
            console.log(err);
          }
        });
      } else{
      }
    }

    function removeParticipant(idParticipation, nomPerso){
      if( confirm('Voulez-vous retirer ' + nomPerso + ' de la mission?') == true ){
        $.ajax({
          'url' : '<?php echo site_url('histoire/removeParticipant/');?>' + idParticipation,
          'method' : 'POST',
          'success' : function(data){
            $('#participantsContainer table tr[data-idParticipation|="' + idParticipation + '"]').remove();
            alert('Participant ' + nomPerso +' retiré !');            
          },
          'error' : function(err){
            console.log(err);
          }
        });
      } else{
      }
    }
</script>