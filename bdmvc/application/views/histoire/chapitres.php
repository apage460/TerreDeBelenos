<div id="page-wrapper">
  <div class="container-fluid">
    <div class="row">
      <div class="col-xs-12">
        <h1 class="page-header">Chapitres de la trame <em><?= $trame->Nom; ?> </em>( # <?= $trame->Id; ?> )</h1>
        <h3>par <em><?= $trame->Createur; ?></em></h3>

        <blockquote><?= $trame->Description; ?></blockquote>
      </div>        
<!-- /.col-lg-12 -->
    </div>
<!-- /.row -->

    <div class="row">
      <div class="col-xs-12 col-md-6">
        <h3>Ajouter un chapitre</h3>
        <?php echo form_open('Histoire/addChapitre'); ?>
          <input type="hidden" name="IdTrame" value="<?= $trame->Id; ?>">
          <div class="form-group col-xs-12 col-md-6">
            <label for="Numero">Numéro</label>
            <input type="number" name="Numero" min="1" max="9999" class="form-control">
          </div>

          <div class="form-group col-xs-12 col-md-6">
            <label for="CodeEtat">État</label>
            <select name="CodeEtat" id="" class="form-control">
              <option value=""></option>
              <option value="ACTIF">Actif</option>
              <option value="INACT">Inactif</option>
            </select>
          </div>

          <div class="form-group col-xs-12">
            <label for="Texte">Texte</label>
            <textarea name="Texte" cols="30" rows="10" class="form-control"></textarea>
          </div>          

          <div class="form-group col-xs-12">
            <button class="btn btn-primary btn-block">Ajouter le chapitre <span class="fa fa-check"></span></button>
          </div>
        <?php echo form_close(); ?>
      </div>

      <div class="col-xs-12 col-md-6">
        <h3>Chapitres</h3>
        <?php if( !empty($chapitres)): ?>
          <div class="row">
            <div class="col-xs-12">
              <div class="list-group" id="list-tab" role="tablist">
                <?php $i = 0; ?>
                <?php foreach($chapitres as $chapitre) : ?>
                  <?php $date = date( 'd M Y', strtotime($chapitre->DateCreation)) ?>
                  <a class="list-group-item <?php if($i == 0){ echo 'active '; } ?>"
                      data-toggle="list" 
                      href="<?= '#chapitre-' . $chapitre->Id; ?>" 
                      role="tab" 
                      aria-controls="home">
                        <?= $chapitre->Numero . ' - Créé le ' . $date; ?>
                  </a>
                  <?php $i++; ?>
                  <?php endforeach; ?>
              </div>
            </div>
            <div class="col-xs-12">
              <div class="tab-content" id="nav-tabContent">
                <?php $i = 0; ?>
                <?php foreach ($chapitres as $chapitre) : ?>
                  <div class="col-xs-12 tab-pane <?php if($i == 0){ echo 'show'; } ?>" id="<?= 'chapitre-' . $chapitre->Id; ?>">
                    
                    <div class="col-xs-8"  role="tabpanel" aria-labelledby="list-home-list">
                      <?php if($chapitre->CodeEtat == 'INACT') : ?>
                        <h4 style="color: rgb(255,0,0);">CHAPITRE INACTIF</h4>
                      <?php endif; ?>
                    <h4>Texte</h4>
                      <?php if($chapitre->CodeEtat == 'INACT') { echo '<strike>'; } ?>
                      <?= $chapitre->Texte; ?>
                      <?php if($chapitre->CodeEtat == 'INACT') { echo '</strike>'; } ?>
                    </div>

                    <div class="col-xs-4">
                      <h4>Actions</h4>
                      <a href="#" class="pop editChapitre" data-pop="editChapitre" data-id="<?= $chapitre->Id; ?>" data-numero="<?= $chapitre->Numero; ?>" data-texte="<?= $chapitre->Texte; ?>" data-codeEtat="<?= $chapitre->CodeEtat; ?>">
                        <button class="btn btn-primary">
                          <span class="fa fa-edit"></span>
                        </button>
                      </a>
                    </div>
                  </div>
                  <?php $i++; ?>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        <?php else: ?>
          <h4>Cette trame ne contient aucun chapitre.</h4>
        <?php endif; ?>
      </div>
    </div>

    <div class="row">
      <div id="editChapitre" class="col-md-8 col-xs-12 toPop">
        <h3>Modifier le Chapitre</h3>

        <form id="editChapitre-form" method="POST" action="<?php echo site_url('Histoire/editChapitre'); ?>">

          <input type="hidden" name="IdTrame" value="<?= $trame->Id; ?>">

          <div class="form-group col-xs-6">
            <label for="Numero">Numéro</label>
            <input type="number" name="Numero" class="form-control">
            <br>
            <label for="CodeEtat">État</label>
            <select name="CodeEtat" id="select_Etat" class="form-control">
              <option value="ACTIF">Actif</option>
              <option value="INACT">Inactif</option>
            </select>
          </div>

          <div class="form-group col-xs-6">
            <label for="Texte">Texte</label>
            <textarea name="Texte" cols="30" rows="6" class="form-control"></textarea>
          </div>

          <div class="form-group col-md-4 col-md-offset-4">
            <button class="btn btn-primary btn-block">Sauvegarder le chapitre <span class="fa fa-save"></span></button>
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
    $('.list-group-item').on('click', function(){
      $('.list-group-item').removeClass('active');

      $(this).addClass('active');

      $('.tab-pane').removeClass('show');

      $( $(this).attr('href') ).addClass('show')
    });
  });

  $(function(){
    $('.pop.editChapitre').on('click', function(){
      var target = '#' + $(this).attr('data-pop');

      var url = "<?php echo site_url('Histoire/editChapitre'); ?>",
          idChapitre = $(this).attr('data-id'),
          numero = $(this).attr('data-numero'),
          texte = $(this).attr('data-texte'),
          codeEtat = $(this).attr('data-codeEtat');

      $('#editChapitre-form').attr('action', url + '/' + idChapitre);

      $('#editChapitre-form').find('[name="Numero"]').val(numero);

      $('#editChapitre-form').find('[name="Texte"]').val(texte);

      $('#editChapitre-form').find('#select_Etat option').removeAttr('selected');

      $('#editChapitre-form').find('#select_Etat option[value="' + codeEtat + '"]').attr('selected','selected');

      $(target).bPopup({
        opacity : 0.8,
      });
    });
  });
</script>