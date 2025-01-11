<div id="page-wrapper">
  <div class="container-fluid">
    <div class="row">
      <div class="col-xs-12">
        <h1 class="page-header">Administrer les Sorts et Recettes</h1>
      </div>        
      <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->

    <div class="row">
      <div class="col-xs-12 col-md-6">
        <h3>
          Ajouter un sort
        </h3>

        <?php echo form_open('Administration/addSort'); ?>
        <?php if($error == 'errorSort') : echo '<div class="col-xs-12" style="color: red;">'; echo validation_errors(); echo '</div>'; endif; ?>
          <div class="form-group col-md-8">
            <label for="name">Nom</label>
            <input type="text" 
            name="name" 
            <?php if($error == 'errorSort') : echo 'value="' .set_value('name') .'"'; endif; ?>
            class="form-control">
          </div>
          <div class="form-group col-md-8">
            <button class="btn btn-primary">Soumettre <span class="fa fa-check"></span></button>
          </div>
        <?php echo form_close(); ?>

      </div>
      <div class="col-xs-12 col-md-6">
        <h3>Liste des Sorts</h3>
        <div id="sortsContainer" style="max-height: 400px; overflow-y: scroll;">

          <table class="table table-responsive">
            <tr>
              <th>Nom</th>
              <th>Modifier</th>
              <th>Supprimer</th>
            </tr>
            <?php foreach ($sorts as $sort) : ?>
              <tr>
                <td><strong><?php echo $sort->Nom; ?></strong></td>
                <td>
                  <a href="#" class="pop" 
                  data-pop="editSort" 
                  data-id="<?php echo $sort->Id; ?>" 
                  data-type="Sort"
                  >
                    <button class="btn btn-primary"><span class="fa fa-edit"></span></button>
                  </a>
                </td>
                <td><a href="#" class="pop" 
                  data-pop="deleteSort" 
                  data-id="<?php echo $sort->Id; ?>" 
                  data-type="Sort"
                  ><button class="btn btn-danger"><span class="fa fa-trash"></span></button></a></td>
              </tr>
            <?php endforeach; ?>
          </table>
          
        </div>
      </div>

      <div class="toPop row" id="editSort">
          <div class="col-xs-12 formContainer">
            
          </div>            
      </div>
      <div class="toPop row" id="deleteSort">
          <div class="col-xs-12 formContainer">
            
          </div>            
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12 col-md-6">
        <h3>
          Ajouter une recette
        </h3>

        <?php echo form_open('Administration/addRecette'); ?>
        <?php if($error == 'errorRecette') : echo '<div class="col-xs-12" style="color: red;">'; echo validation_errors(); echo '</div>'; endif; ?>
          <div class="form-group col-md-8">
            <label for="name">Nom</label>
            <input type="text" 
            name="name" 
            <?php if($error == 'errorRecette') : echo 'value="' .set_value('name') .'"'; endif; ?> 
            class="form-control">
          </div>
          <div class="form-group col-md-8">
            <button class="btn btn-primary">Soumettre <span class="fa fa-check"></span></button>
          </div>
        <?php echo form_close(); ?>

      </div>
      <div class="col-xs-12 col-md-6">
        <h3>Liste des Recettes</h3>
        <div id="recettesContainer" style="max-height: 400px; overflow-y: scroll;">
          <table class="table table-responsive">
            <tr>
              <th>Nom</th>
              <th>Modifier</th>
              <th>Supprimer</th>
            </tr>
            <?php foreach ($recettes as $recette) : ?>
              <tr>
                <td><strong><?php echo $recette->Nom; ?></strong></td>
                <td><a href="#" class="pop" 
                  data-pop="editRecette" 
                  data-id="<?php echo $recette->Id; ?>" 
                  data-type="Recette"
                  ><button class="btn btn-primary"><span class="fa fa-edit"></span></button></a></td>
                <td><a href="#" class="pop" 
                  data-pop="deleteRecette" 
                  data-id="<?php echo $recette->Id; ?>" 
                  data-type="Recette"
                  ><button class="btn btn-danger"><span class="fa fa-trash"></span></button></a></td>
              </tr>
            <?php endforeach; ?>
          </table>
        </div>
      </div>
      <div class="toPop row" id="editRecette">
        <div class="col-xs-12 formContainer">
                
        </div>            
      </div>
      <div class="toPop row" id="deleteRecette">
          <div class="col-xs-12 formContainer">
          </div>            
      </div>
    </div>
  </div>
  <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->

<script>
    $(function(){
        $('.pop').on('click',function(){
            var target = '#' + $(this).attr('data-pop'),
                id = $(this).attr('data-id'),
                type = $(this).attr('data-type'),
                url = (target.indexOf('delete') == -1) ? '<?php echo site_url(); ?>' + 'Administration/displayEdit' + type + 'Modal/' +id : '<?php echo site_url(); ?>' + 'Administration/displayDelete' + type + 'Modal/' +id ;

            $(target).bPopup({
              opacity : 0.8,
              content: 'ajax',
              contentContainer: target + " .formContainer",
              loadUrl: url,
            });   
        });
    });
</script>