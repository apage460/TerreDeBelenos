<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h1 class="page-header">Consulter les institutions</h1>
            </div>  
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <div class="row">
            <table class="table table-responsive table-striped">
                <tr>
                    <th>Nom</th>
                    <th>Comte</th>
                    <th>Groupe</th>
                    <th>Profil</th>
                    <th>Niveau</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($institutions as $institution) : ?>
                    <tr>
                        <td><?= $institution->Nom; ?></td>
                        <td><?= $institution->nomComte; ?></td>
                        <td><?= $institution->nomGroupe; ?></td>
                        <td><?= $institution->nomProfil; ?></td>
                        <td><?= $institution->Niveau; ?></td>
                        <td>
                            <button class="btn btn-primary pop institution" data-pop="editInstitution" data-idInstitution="<?= $institution->Id; ?>">
                                <span class="fa fa-edit"></span>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="row">
            <div class="col-xs-12 col-md-8 toPop" id="editInstitution">
                <?= form_open('groupes/saveInstitution',array('id' => 'editInstitutionForm')); ?>
                <div class="form-group col-xs-12">
                    <div class="form-group col-xs-4">
                      <label for="Nom">Nom</label>
                      <input type="text" name="Nom" class="form-control" placeholder="Nom">
                    </div>
                    <div class="form-group col-xs-4">
                      <label for="Comte">Comte</label>
                      <select name="Comte" class="form-control">
                        <?php foreach($comtes as $comte) : ?>
                            <option value="<?= $comte->Id; ?>"><?= $comte->Nom;?></option>
                        <?php endforeach;?>
                      </select>
                    </div>
                    <div class="form-group col-xs-4">
                      <label for="Groupe">Groupe</label>
                      <select name="Groupe" class="form-control">
                        <?php foreach($groupes as $groupe) : ?>
                            <option value="<?= $groupe->Id; ?>"><?= $groupe->Nom;?></option>
                        <?php endforeach;?>
                      </select>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-xs-4">
                      <label for="Profil">Profil</label>
                      <select name="Profil" class="form-control">
                        <?php foreach($profils as $profil) : ?>
                            <option value="<?= $profil->Code; ?>"><?= $profil->Nom;?></option>
                        <?php endforeach;?>
                      </select>
                    </div>
                    <div class="form-group col-xs-4">
                      <label for="Niveau">Niveau</label>
                      <input type="number" name="Niveau" min="1" max="3" class="form-control" placeholder="Niveau">
                    </div>
                    <div class="form-group col-xs-4">
                      <label for="CodeEtat">État</label>
                      <select class="form-control" name="CodeEtat" id="CodeEtat">
                          <option value="ACTIF">Actif</option>
                          <option value="INACT">Inactif</option>
                      </select>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="form-group col-xs-4">
                      <label for="Chef">Chef</label>
                      <input type="text" name="Chef" class="form-control" placeholder="Chef">
                    </div>
                    <div class="form-group col-xs-4">
                      <label for="Description">Description</label>
                      <textarea type="text" name="Description" rows="15" class="form-control" placeholder="Description"></textarea>
                    </div>
                    <div class="form-group col-xs-4">
                      <label for="AgendaCache">Agenda Caché</label>
                      <textarea type="text" name="AgendaCache" rows="15" class="form-control" placeholder="AgendaCache"></textarea>
                    </div>
                </div>
                <div class="form-group col-xs-4 col-xs-offset-8">
                    <button class="btn btn-primary btn-lg btn-block">Sauvegarder <span class="fa fa-save"></span></button>
                </div>
                <?= form_close(); ?>
            </div>
        </div>  
       
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->

<script>
    $('button.institution').on('click', function(e){
        e.preventDefault();
        var idInstitution = $(this).attr('data-idInstitution');

        $.ajax({
            'url' : '<?php echo site_url('groupes/getInstitution'); ?>',
            'method' : 'POST',
            'data' : {
                'idInstitution' : idInstitution
            },
            'success' : function(data){
                var data = JSON.parse(data),
                    form = $('#editInstitutionForm');
                
                form.attr('action','<?php echo site_url("groupes/saveInstitution/"); ?>' + idInstitution)

                form.find('[name="Nom"]').val(data.Nom);
                form.find('[name="Niveau"]').val(data.Niveau);
                form.find('[name="Chef"]').val(data.Chef);
                form.find('[name="Description"]').val(data.Description);
                form.find('[name="AgendaCache"]').val(data.AgendaCache);

                form.find('[name="Comte"] option').removeAttr('selected');
                form.find('[name="Comte"]').find('[value="' +data.IdComte +'"]').attr('selected','selected');

                form.find('[name="Groupe"] option').removeAttr('selected');
                form.find('[name="Groupe"]').find('[value="' +data.IdGroupe +'"]').attr('selected','selected');

                form.find('[name="Profil"] option').removeAttr('selected');
                form.find('[name="Profil"]').find('[value="' +data.CodeProfil +'"]').attr('selected','selected');

                form.find('[name="CodeEtat"] option').removeAttr('selected');
                form.find('[name="CodeEtat"]').find('[value="' +data.CodeEtat +'"]').attr('selected','selected');

            },
            'error' : function(err){
                console.log(err);
            }
        })

        $('#editInstitution').bPopup({
            'opacity' : 0.8
        });
    });
</script>