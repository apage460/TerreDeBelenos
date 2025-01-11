<div id="page-wrapper">
  <div class="container-fluid">
    <div class="row">
      <div class="col-xs-12">
        <h1 class="page-header">Trames</h1>
      </div>        
<!-- /.col-lg-12 -->
    </div>
<!-- /.row -->

    <div class="row">
      <div class="col-xs-12">
        <h3>Ajouter une trame</h3>

        <?php if(validation_errors()): ?>

            <div class="row errors-container"><span class="fa fa-exclamation-triangle fa-4x"><?php echo validation_errors(); ?></div>

        <?php endif; ?>
        <?php echo form_open('Histoire/addTrame'); ?>

          <div class="form-group col-xs-4">
            <label for="Id">Identification  (Chiffres seulement)*</label>
            <input type="number" min="1" max="9999" name="Id" class="form-control" required placeholder="XXXX">
            <hr>
            <label for="Nom">Nom de la Trame*</label>
            <input type="text" name="Nom" class="form-control" required placeholder="Nom">
          </div>          

          <div class="form-group col-xs-4">
            <label for="IdComte">Comté*</label>
            <select name="IdComte" class="form-control">
              <option value="">Sélectionner un Comté</option>
              <?php $d = ''; ?>
              <?php 
              foreach( $comtes as $c ): 
                if ( $c->NomDuche != $d ){
                  $d = $c->NomDuche;
                  echo '<optgroup label="' .$c->NomDuche .'">';
                }
              ?>

                    <option value="<?= $c->Id; ?>"><?= $c->Nom; ?></option>

                <?php if ( $c->NomDuche != $d ){ echo '</optgroup>'; } ?>                
              <?php endforeach; ?>
            </select>
            <hr>
            <label for="CodeEtat">État de la trame</label>
            <select class="form-control" name="CodeEtat">
              <option value="ACTIF" selected="selected">Active</option>
              <option value="INACT">Inactive</option>
            </select>
          </div>

          <div class="form-group col-xs-4">
            <label for="Description">Description*</label>
            <textarea name="Description" id="" cols="30" rows="6" class="form-control">Description...</textarea>
          </div>

          <div class="form-group col-xs-4 col-xs-offset-4">
            <button class="btn btn-block btn-primary">Ajouter la trame <span class="fa fa-plus"></span></button>
          </div>

        <?php echo form_close(); ?>
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12">
        <h3>Consulter les trames</h3>
      </div>
      <table class="table table-responsive table-striped">
        <tr>
          <th>Ident.</th>
          <th>Comté</th>
          <th>Titre</th>
          <th>Desc.</th>
          <th>État</th>
          <th>Créateur</th>
          <th>Date Création</th>
          <th></th>
        </tr>
        <?php foreach($trames as $trame): ?>
          <tr>
            <td><?= $trame->Id; ?></td>
            <td><?= $trame->Comte; ?></td>
            <td><?= $trame->Nom; ?></td>
            <td><?= $trame->Description; ?></td>
            <td><?= $trame->CodeEtat; ?></td>
            <td><?= $trame->Createur; ?></td>
            <td><?= $trame->DateCreation; ?></td>
            <td>
              <button class="btn btn-primary pop" data-pop="editTrame" data-Id="<?= $trame->Id; ?>" data-IdComte="<?= $trame->IdComte; ?>" data-Nom="<?= $trame->Nom; ?>" data-CodeEtat="<?= $trame->CodeEtat; ?>" data-Description="<?= $trame->Description; ?>">
                <span class="fa fa-edit"></span>
              </button>
              <a href="<?= site_url('Histoire/getChapitres/' .$trame->Id); ?>">
                <button class="btn btn-primary">
                  <span class="fa fa-eye"></span>
                </button>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>

    <div class="row">
      <div id="editTrame" class="col-md-8 col-xs-12 toPop">
        <form id="editTrame-form" method="POST" action="<?php echo site_url('Histoire/editTrame'); ?>">

          <div class="form-group col-xs-6">
            <label for="Identification">Identification</label>
            <input name="Id" class="form-control" type="text">
          </div>

          <div class="form-group col-xs-6">
            <label for="">Comté</label>
            <select name="IdComte" id="select_comte" class="form-control">
              <option value="">Sélectionner un Comté</option>
              <?php $d = ''; ?>
              <?php 
              foreach( $comtes as $c ): 
                if ( $c->NomDuche != $d ){
                  $d = $c->NomDuche;
                  echo '<optgroup label="' .$c->NomDuche .'">';
                }
              ?>

                    <option value="<?= $c->Id; ?>"><?= $c->Nom; ?></option>

                <?php if ( $c->NomDuche != $d ){ echo '</optgroup>'; } ?>                
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group col-xs-6">
            <label for="">Nom</label>
            <input name="Nom" class="form-control" type="text">
            <hr>
            <label for="CodeEtat">État de la trame</label>
            <select class="form-control" id="select_codeEtat" name="CodeEtat">
              <option value="ACTIF" selected="selected">Active</option>
              <option value="INACT">Inactive</option>
            </select>
          </div>
          
          <div class="form-group col-xs-6">
            <label for="Description">Description</label>
            <textarea name="Description" class="form-control" cols="30" rows="6"></textarea>
          </div>

          <div class="form-group col-xs-12">
            <button class="btn btn-primary">Sauvegarder <span class="fa fa-save"></span></button>
          </div>

        </form>
      </div>
    </div>

  </div>
<!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->

<script>
  $(function(){
    $('.pop').on('click', function(){
      var target = '#' + $(this).attr('data-pop'),
          url = "<?php echo site_url('Histoire/editTrame'); ?>",
          id = $(this).attr('data-Id'),
          idComte = $(this).attr('data-IdComte'),
          nom = $(this).attr('data-Nom'),
          description = $(this).attr('data-Description'),
          codeEtat = $(this).attr('data-CodeEtat');

      $('#editTrame-form').attr('action', url + '/' + id);

      $('#editTrame-form').find('[name="Id"]').val(id);

      $('#editTrame-form').find('#select_comte option[value="' + idComte + '"]').attr('selected','selected');

      $('#editTrame-form').find('[name="Nom"]').val(nom);

      $('#editTrame-form').find('[name="Description"]').val(description);

      $('#editTrame-form').find('#select_codeEtat option[value="' + codeEtat + '"]').attr('selected','selected');

      $(target).bPopup({
        opacity : 0.8,
      });

    });
  })
</script>