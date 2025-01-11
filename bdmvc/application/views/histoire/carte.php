<div id="page-wrapper">
  <div class="container-fluid">
    <div class="row">
      <div class="col-xs-12">
        <h1 class="page-header">Carte Interactive</h1>
      </div>        
<!-- /.col-lg-12 -->
    </div>
<!-- /.row -->

    <div class="row">
      <div class="col-md-6 col-xs-12">
        <br>
        <a href="<?php echo site_url('Histoire/generateMapConfig'); ?>">
          <button class="btn btn-block btn-lg btn-primary">
            Générer le fichier de configuration <span class="fa fa-check"></span>
          </button>
        </a>
        <br><br>
        <?php if( $jsonSuccess ): ?>
          <h4>Mise à jour du fichier de configuration complétée.</h4>
        <?php endif; ?>
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12">
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="<?php if(!isset($active) || $active == 'baronnies'): ?>active <?php endif; ?>">
            <a href="#baronnies" aria-controls="baronnies" role="tab" data-toggle="tab">Baronnies</a>
          </li>
          <li role="presentation" class="<?php if(isset($active) && $active == 'comtes'): ?>active <?php endif; ?>">
            <a href="#comtes"  aria-controls="comtes" role="tab" data-toggle="tab">Comtés</a>
          </li>
          <li role="presentation" class="<?php if(isset($active) && $active == 'duches'): ?>active <?php endif; ?>">
            <a href="#duches"  aria-controls="duches" role="tab" data-toggle="tab">Duchés</a>
          </li>
          <li role="presentation" class="<?php if(isset($active) && $active == 'royaumes'): ?>active <?php endif; ?>">
            <a href="#royaumes" aria-controls="royaumes" role="tab" data-toggle="tab">Royaumes</a>
          </li>
        </ul>

        <div class="tab-content col-md-8 col-md-offset-2 col-xs-12">
          <div role="tabpanel" class="tab-pane <?php if(!isset($active) || $active == 'baronnies'): ?>active <?php endif; ?>" id="baronnies">
            <h2>Baronnies</h2>

            <?php if( !empty($baronnies) ): ?>

              <table class="table table-striped table-responsive">
                <thead>
                  <tr>
                    <td>Cadastre</td>
                    <td>Nom</td>
                    <td>Comté</td>
                    <td></td>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($baronnies as $b) : ?>
                  <tr>
                    <td><?= $b->Cadastre; ?></td>
                    <td><?= $b->Nom; ?></td>
                    <td><?= $b->NomComte; ?></td>
                    <td>
                      <button class="btn btn-primary pop baronnies" data-pop="editBaronnie" data-id="<?= $b->Id; ?>" data-idComte="<?= $b->IdComte; ?>" data-nom="<?= $b->Nom; ?>" data-baron="<?= $b->Baron; ?>">
                        <span class="fa fa-edit"></span>
                      </button>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>

            <?php endif; ?>
          </div>

          <div role="tabpanel" class="tab-pane <?php if(isset($active) && $active == 'comtes'): ?>active <?php endif; ?>" id="comtes">
            <h2>Comtés</h2>

            <?php if( !empty($comtes) ): ?>

              <table class="table table-striped table-responsive">
                <thead>
                  <tr>
                    <td>Nom</td>
                    <td>Type</td>
                    <td>Duché</td>
                    <td>Dirigeant</td>
                    <td></td>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($comtes as $c) : ?>
                  <tr>
                    <td><?= $c->Nom; ?></td>
                    <td><?= $c->Type; ?></td>
                    <td><?= $c->NomDuche; ?></td>
                    <td><?= $c->Dirigeant; ?></td>
                    <td>
                      <button class="btn btn-primary pop comtes" data-pop="editComte" data-id="<?= $c->Id; ?>" data-nom="<?= $c->Nom; ?>" data-couleur="<?= $c->Couleur; ?>" data-codeDuche="<?= $c->CodeDuche; ?>" data-dirigeant="<?= $c->Dirigeant; ?>" data-descriptionDirigeant="<?= $c->DescriptionDirigeant; ?>" data-scribe="<?= $c->Scribe; ?>" data-descriptionScribe="<?= $c->DescriptionScribe; ?>">
                        <span class="fa fa-edit"></span>
                      </button>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>

            <?php endif; ?>
          </div>

          <div role="tabpanel" class="tab-pane <?php if(isset($active) && $active == 'duches'): ?>active <?php endif; ?>" id="duches">
            <h2>Duchés</h2>

            <?php if( !empty($duches) ): ?>

              <table class="table table-striped table-responsive">
                <thead>
                  <tr>
                    <td>Nom</td>
                    <td>Description</td>
                    <td>Royaume</td>
                    <td></td>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($duches as $d) : ?>
                  <tr>
                    <td><?= $d->Nom; ?></td>
                    <td><?= $d->Description; ?></td>
                    <td><?= $d->NomRoyaume; ?></td>
                    <td>
                      <button class="btn btn-primary pop duches" data-pop="editDuche" data-couleur="<?= $d->Couleur; ?>" data-code="<?= $d->Code?>" data-nom="<?= $d->Nom; ?>" data-dirigeant="<?= $d->Dirigeant; ?>" data-codeRoyaume="<?= $d->CodeRoyaume; ?>" data-description="<?= $d->Description; ?>">
                        <span class="fa fa-edit"></span>
                      </button>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>

            <?php endif; ?>
          </div>
          
          <div role="tabpanel" class="tab-pane <?php if(isset($active) && $active == 'royaumes'): ?>active <?php endif; ?>" id="royaumes">
            <h2>Royaumes</h2>

            <?php if( !empty($royaumes) ): ?>

              <table class="table table-striped table-responsive">
                <thead>
                  <tr>
                    <td>Nom</td>
                    <td>Description</td>
                    <td>État</td>
                    <td></td>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($royaumes as $r) : ?>
                  <tr>
                    <td><?= $r->Nom; ?></td>
                    <td><?= $r->Description; ?></td>
                    <td><?= $r->CodeEtat; ?></td>
                    <td>
                      <button class="btn btn-primary pop royaumes" data-pop="editRoyaume" data-code="<?= $r->Code; ?>" data-couleur="<?= $r->Couleur; ?>" data-nom="<?= $r->Nom; ?>" data-dirigeant="<?= $r->Dirigeant; ?>" data-description="<?= $r->Description; ?>" data-codeEtat="<?= $r->CodeEtat; ?>">
                        <span class="fa fa-edit"></span>
                      </button>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>

            <?php endif; ?>
          </div>
        </div>
      </div>

    </div>

    <div class="row">
      <div id="editBaronnie" class="col-md-8 col-xs-12 toPop">
        <h3>Modifier la Baronnie</h3>

        <form id="editBaronnie-form" method="POST" action="<?php echo site_url('Histoire/editBaronnie'); ?>">

          <div class="form-group col-xs-6">
            <label for="Nom">Nom</label>
            <input type="text" name="Nom" class="form-control" placeholder="Nom">
          </div>

          <div class="form-group col-xs-6">
            <label for="idComte">Comté</label>
            <select name="idComte" id="select_comte" class="form-control">
              <option value="">Comtés</option>
              <?php foreach( $comtes as $c ) : ?>
                <option value="<?= $c->Id; ?>">
                  <?= $c->Nom; ?>
                </option>
              <?php endforeach;?>
            </select>
          </div>

          <div class="form-group col-xs-6">
            <label for="Baron">Baron</label>
            <input type="text" name="Baron" class="form-control" placeholder="Baron">
          </div>

          <div class="form-group col-xs-6">
            <label for="CodeEtat">État</label>
            <select name="CodeEtat" class="form-control" id="select_CodeEtat_baronnie">
              <option value="ACTIF">ACTIVE</option>
              <option value="INACT">INACTIVE</option>
            </select>
          </div>

          <div class="form-group col-xs-12">
            <button type="submit" class="btn btn-primary">Sauvegarder <span class="fa fa-save"></span></button>
            <button class="btn btn-primary">Annuler <span class="fa fa-window-close"></span></button>
          </div>

          
        </form>

      </div>
    </div>

    <div class="row">
      <div id="editComte" class="col-md-8 col-xs-12 toPop">
        <h3>Modifier le Comté</h3>

        <form id="editComte-form" method="POST" action="<?php echo site_url('Histoire/editComte'); ?>">

          <div class="form-group col-xs-6">
            <label for="Nom">Nom</label>
            <input type="text" name="Nom" class="form-control" placeholder="Nom">
          </div>

          <div class="form-group col-xs-6">
            <label for="Couleur">Couleur</label>
            <input type="color" name="Couleur" class="form-control" placeholder="Couleur">
          </div>

          <div class="form-group col-xs-6">
            <label for="CodeDuche">Duché</label>
            <select name="CodeDuche" id="select_Duche" class="form-control">
              <option value="">Duchés</option>
              <?php foreach( $duches as $d ) : ?>
                <option value="<?= $d->Code; ?>">
                  <?= $d->Nom; ?>
                </option>
              <?php endforeach;?>
            </select>
          </div>

          <div class="form-group col-xs-6">
            <label for="Dirigeant">Comte</label>
            <input type="text" name="Dirigeant" class="form-control" placeholder="Dirigeant">
          </div>

          <div class="form-group col-xs-6">
            <label for="DescriptionDirigeant">Description du Comte</label>
            <textarea name="DescriptionDirigeant" class="form-control" cols="30" rows="8"></textarea>
          </div>

          <div class="form-group col-xs-6">
            <label for="Scribe">Scibe</label>
            <input type="text" name="Scribe" class="form-control" placeholder="Scribe">
            <br>
            <label for="DescriptionScribe">Description du Scribe</label>
            <input type="text" name="DescriptionScribe" class="form-control" placeholder="DescriptionScribe">
            <br>
            <label for="CodeEtat">État</label>
            <select name="CodeEtat" class="form-control" id="select_CodeEtat_comte">
              <option value="ACTIF">ACTIF</option>
              <option value="INACT">INACTIF</option>
            </select>
          </div>

          <div class="form-group col-xs-12">
            <button type="submit" class="btn btn-primary">Sauvegarder <span class="fa fa-save"></span></button>
            <button class="btn btn-primary">Annuler <span class="fa fa-window-close"></span></button>
          </div>

          
        </form>

      </div>
    </div>

    <div class="row">
      <div id="editDuche" class="col-md-8 col-xs-12 toPop">
        <h3>Modifier le Duché</h3>

        <form id="editDuche-form" method="POST" action="<?php echo site_url('Histoire/editDuche'); ?>">

          <div class="form-group col-xs-6">
            <label for="Nom">Nom</label>
            <input type="text" name="Nom" class="form-control" placeholder="Nom">
          </div>

          <div class="form-group col-xs-6">
            <label for="CodeDuche">Royaume</label>
            <select name="CodeRoyaume" id="select_royaume" class="form-control">
              <option value="">Royaumes</option>
              <?php foreach( $royaumes as $r ) : ?>
                <option value="<?= $r->Code; ?>">
                  <?= $r->Nom; ?>
                </option>
              <?php endforeach;?>
            </select>
          </div>

          <div class="form-group col-xs-6">
            <label for="Description">Description</label>
            <textarea name="Description" class="form-control" cols="30" rows="8"></textarea>
          </div>

          <div class="form-group col-xs-6">
            <label for="Dirigeant">Dirigeant</label>
            <input type="text" name="Dirigeant" class="form-control">
          </div>

          <div class="form-group col-xs-6">
            <div class="form-group col-xs-12">
              <label for="Couleur">Couleur</label>
              <input type="color" name="Couleur" class="form-control" placeholder="Couleur">
            </div>
            <div class="form-group col-xs-12">
              <label for="CodeEtat">État</label>
              <select name="CodeEtat" class="form-control" id="select_CodeEtat_duche">
                <option value="ACTIF">ACTIF</option>
                <option value="INACT">INACTIF</option>
              </select>
            </div>
          </div>

          <div class="form-group col-xs-12">
            <button type="submit" class="btn btn-primary">Sauvegarder <span class="fa fa-save"></span></button>
            <button class="btn btn-primary">Annuler <span class="fa fa-window-close"></span></button>
          </div>

          
        </form>

      </div>
    </div>

    <div class="row">
      <div id="editRoyaume" class="col-md-8 col-xs-12 toPop">
        <h3>Modifier le Royaume</h3>

        <form id="editRoyaume-form" method="POST" action="<?php echo site_url('Histoire/editRoyaume'); ?>">

          <div class="form-group col-xs-6">
            <label for="Nom">Nom</label>
            <input type="text" name="Nom" class="form-control" placeholder="Nom">
          </div>

          <div class="form-group col-xs-6">
            <label for="Description">Description</label>
            <textarea name="Description" class="form-control" cols="30" rows="8"></textarea>
          </div>

          <div class="form-group col-xs-6">
            <label for="Dirigeant">Dirigeant</label>
            <input type="text" name="Dirigeant" class="form-control">
          </div>

          <div class="form-group col-xs-6">
            <div class="form-group col-xs-12">
              <label for="Couleur">Couleur</label>
              <input type="color" name="Couleur" class="form-control" placeholder="Couleur">
            </div>
            <div class="form-group col-xs-12">
              <label for="CodeEtat">État</label>
              <select name="CodeEtat" class="form-control" id="select_CodeEtat_duche">
                <option value="ACTIF">ACTIF</option>
                <option value="INACT">INACTIF</option>
              </select>
            </div>
          </div>

          <div class="form-group col-xs-12">
            <button type="submit" class="btn btn-primary">Sauvegarder <span class="fa fa-save"></span></button>
            <button class="btn btn-primary">Annuler <span class="fa fa-window-close"></span></button>
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
    $('.pop.baronnies').on('click',function(){
      var target = '#' + $(this).attr('data-pop');

      var url = "<?php echo site_url('Histoire/editBaronnie'); ?>",
          idBaronnie = $(this).attr('data-id'),
          idComte = $(this).attr('data-idComte'),
          baron = $(this).attr('data-baron'),
          nom = $(this).attr('data-nom');

      $('#editBaronnie-form').attr('action', url + '/' + idBaronnie);

      $('#editBaronnie-form').find('[name="Nom"]').val(nom);

      $('#editBaronnie-form').find('[name="Baron"]').val(baron);

      $('#editBaronnie-form').find('#select_comte option').removeAttr('selected');

      $('#editBaronnie-form').find('#select_comte option[value="' + idComte + '"]').attr('selected','selected');

      $(target).bPopup({
        opacity : 0.8,
      });
    });

    $('.pop.comtes').on('click',function(){
      var target = '#' + $(this).attr('data-pop');

      var url = "<?php echo site_url('Histoire/editComte'); ?>",
          idComte = $(this).attr('data-id'),
          codeDuche = $(this).attr('data-codeDuche'),
          nom = $(this).attr('data-nom'),
          couleur = $(this).attr('data-couleur'),
          dirigeant = $(this).attr('data-dirigeant'),
          descriptionDirigeant = $(this).attr('data-descriptionDirigeant'),
          scribe = $(this).attr('data-scribe'),
          descriptionScribe = $(this).attr('data-descriptionScribe');

      $('#editComte-form').attr('action', url + '/' + idComte);

      $('#editComte-form').find('[name="Nom"]').val(nom);

      $('#editComte-form').find('[name="Couleur"]').val(couleur);

      $('#editComte-form').find('[name="Dirigeant"]').val(dirigeant);

      $('#editComte-form').find('[name="DescriptionDirigeant"]').val(descriptionDirigeant);

      $('#editComte-form').find('[name="Scribe"]').val(scribe);

      $('#editComte-form').find('[name="DescriptionScribe"]').val(descriptionScribe);

      $('#editComte-form').find('#select_Duche option').removeAttr('selected');

      $('#editComte-form').find('#select_Duche option[value="' + codeDuche + '"]').attr('selected','selected');


      $(target).bPopup({
        opacity : 0.8,
      });
    });

    $('.pop.duches').on('click',function(){
      var target = '#' + $(this).attr('data-pop');

      var url = "<?php echo site_url('Histoire/editDuche'); ?>",
          codeDuche = $(this).attr('data-code'),
          nom = $(this).attr('data-nom'),
          codeRoyaume = $(this).attr('data-codeRoyaume'),
          couleur = $(this).attr('data-Couleur'),
          description = $(this).attr('data-description');
          dirigeant = $(this).attr('data-dirigeant');

      $('#editDuche-form').attr('action', url + '/' + codeDuche);

      $('#editDuche-form').find('[name="Nom"]').val(nom);

      $('#editDuche-form').find('[name="Couleur"]').val(couleur);

      $('#editDuche-form').find('[name="Description"]').val(description);

      $('#editDuche-form').find('[name="Dirigeant"]').val(dirigeant);

      $('#editDuche-form').find('#select_royaume option').removeAttr('selected');

      $('#editDuche-form').find('#select_royaume option[value="' + codeRoyaume + '"]').attr('selected','selected');

      $(target).bPopup({
        opacity : 0.8,
      });
    });

    $('.pop.royaumes').on('click',function(){
      var target = '#' + $(this).attr('data-pop');

      var url = "<?php echo site_url('Histoire/editRoyaume'); ?>",
          codeRoyaume = $(this).attr('data-code'),
          nom = $(this).attr('data-nom'),
          codeEtat = $(this).attr('data-codeEtat'),
          couleur = $(this).attr('data-couleur'),
          description = $(this).attr('data-description');
          dirigeant = $(this).attr('data-dirigeant');

      $('#editRoyaume-form').attr('action', url + '/' + codeRoyaume);

      $('#editRoyaume-form').find('[name="Nom"]').val(nom);

      $('#editRoyaume-form').find('[name="Couleur"]').val(couleur);

      $('#editRoyaume-form').find('[name="Description"]').val(description);

      $('#editRoyaume-form').find('[name="Dirigeant"]').val(dirigeant);

      $('#editRoyaume-form').find('#select_CodeEtat_royaume option').removeAttr('selected');

      $('#editRoyaume-form').find('#select_CodeEtat_royaume option[value="' + codeEtat + '"]').attr('selected','selected');

      $(target).bPopup({
        opacity : 0.8,
      });
    });

  });
</script>