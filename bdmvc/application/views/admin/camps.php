<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
          <div class="col-xs-12">
            <h1 class="page-header">Campements</h1>
          </div>
          <div class="row">
            <div class="col-md-3 col-xs-6">
                <button class="btn btn-lg btn-block btn-primary" onClick="saveCamepements();">Sauvegarder <span class="fa fa-save"></span></button>
            </div>
        </div>           
    <!-- /.col-lg-12 -->
        </div>
        <div class="row">
            <div class="col-md-3 col-xs-12">
                <h4>Évaluer les campements pour</h4>
                <select class="form-control" disabled="disabled" name="idActivite" id="idActivite">
                    <option value="<?= $activite->Id; ?>"><?= $activite->Nom; ?></option>
                </select>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-xs-12">
                <table class="table table-striped table-responsive table-condensed">
                    <tr>
                        <th>Campement</th>
                        <!--<th>Responsable</th>-->
                        <th>Évaluation</th>
                        <th>Commentaires</th>
                    </tr>
                    <?php foreach($campements as $campement): ?>
                        <tr data-codeCampement="<?= $campement->Code; ?>">
                            <td><?= $campement->Nom; ?></td>
                            <!--<td><?php // $campement->Responsables; ?></td>-->
                            <td>
                                <div>
                                    <label for="proprete-title" style="margin-right: 0.5em;">Propreté</label>
                                    <label class="checkbox-inline" for="<?= $campement->Code; ?>-proprete1">
                                        <input type="checkbox" value="1" id="<?= $campement->Code; ?>-proprete1" name="<?= $campement->Code; ?>-proprete"> Oui
                                    </label>
                                </div>
                                <hr style="margin: 0.5em;">
                                <div>
                                    <label for="securite-title" style="margin-right: 0.5em;">Sécurité</label>
                                    <label class="checkbox-inline" for="<?= $campement->Code; ?>-securite1">
                                        <input type="checkbox" value="1" id="<?= $campement->Code; ?>-securite1" name="<?= $campement->Code; ?>-securite"> Oui
                                    </label>
                                </div>
                                <hr style="margin: 0.5em;">
                                <div>
                                    <label for="rangement-title" style="margin-right: 0.5em;">Rangement</label>
                                    <label class="checkbox-inline" for="<?= $campement->Code; ?>-rangement1">
                                        <input type="checkbox" value="1" id="<?= $campement->Code; ?>-rangement1" name="<?= $campement->Code; ?>-rangement"> Oui
                                    </label>
                                </div>
                            </td>
                            <td>
                                <textarea class="form-control" name="<?= $campement->Code; ?>-commentaire" id="" style="resize: none;" cols="30" rows="5"></textarea>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>        
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->

    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->
<script>
    $('input[type="checkbox"], textarea').click(function(){
        $(this).parents('tr').addClass('edited');
    });

    function saveCamepements(){
        var rows = $('tr.edited'),
            data = [];

        rows.each(function(index, row){
            var codeCampement = $(row).attr('data-codeCampement'),
                idActivite = $('select[name="idActivite"] option:selected').val(),
                nomActivite = $('select[name="idActivite"] option:selected').html();

            rowData = {
                'codeCampement' : codeCampement,
                'idActiv' : idActivite,
                'nomActivite' : nomActivite,
                'proprete' : ($(row).find('input[name="' +codeCampement +'-proprete"]:checked').val())?$(row).find('input[name="' +codeCampement +'-proprete"]:checked').val():0,
                'securite' : ($(row).find('input[name="' +codeCampement +'-securite"]:checked').val())?$(row).find('input[name="' +codeCampement +'-securite"]:checked').val():0,
                'rangement' : ($(row).find('input[name="' +codeCampement +'-rangement"]:checked').val())?$(row).find('input[name="' +codeCampement +'-rangement"]:checked').val():0,
                'commentaire' : $(row).find('textarea[name="' +codeCampement +'-commentaire"]').val(),                
            };

            data.push(rowData);
        });

        if(data.length > 0){
            sendData = JSON.stringify(data);

            $.ajax({
                'url' : '<?= site_url("/administration/rateCampements"); ?>',
                'method' : 'POST',
                'data' : {sendData},
                'success' : function(data){
                    location.reload();
                },
                'error' : function(err){
                    console.log(err);
                }
            });
        }
    }
</script>