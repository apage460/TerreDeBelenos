<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Approuver les demandes de race</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <div class="row">
            <div class="col-md-3 col-xs-12">
                <?php echo form_open('approbations/loadDemande'); ?>
                    <select name="idAppro" class="form-control">
                        <?php foreach($demandes as $demande) : ?>
                            <option value="<?php echo $demande->Id; ?>" 
                            <?php if(isset($result)): if($demande->Id == $result->Id): echo 'selected="selected"'; endif; endif; ?>
                        >
                                <?php echo $demande->Prenom .' ' .$demande->Nom .'( ' .$demande->NomRace .' )'; ?>
                                
                                </option>
                        <?php endforeach; ?>
                    </select>
                    <br>
                    <button class="btn btn-primary btn-lg btn-block">Charger la demande <span class="fa fa-search"></span></button>
                <?php echo form_close(); ?>
            </div>

            <?php if(isset($result)) : ?>
                <div class="col-md-6 col-xs-12">
                    <div class="col-xs-12">
                        <p><?php echo $result->Histoire; ?></p>
                    </div>
                </div>
                <div class="col-md-3 col-xs-12">
                    <div class="col-xs-12">
                        <?php echo form_open('approbations/acceptRace/' .$result->Id, array('name'=>'acceptForm') ); ?>
                            <input id="acceptInputCommentaire" class="form-control" placeholder="Commentaire" type="text" name="acceptCommentaire" onblur="document.refusForm.refusCommentaire.value = this.value;">
                            <button class="btn btn-success btn-block">Approuver la demande <span class="fa fa-check"></span></button>
                        <?php echo form_close(); ?>
                        <br><br>
                        <?php echo form_open('approbations/refusRace/' .$result->Id, array('name'=>'refusForm')); ?>
                            <input id="refusInputCommentaire" type="hidden" name="refusCommentaire">
                            <button disabled="disabled" class="btn btn-danger btn-block">Refuser la demande <span class="fa fa-close"></span></button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
                
            <?php endif; ?>
            
        </div>
        
        
        
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->
<script>
    $('#acceptInputCommentaire').on('keyup', function(){console.log($(this).val().length);
        if($(this).val().length >= 15 ){
            $('#refusInputCommentaire').next().removeAttr('disabled');
        } else {
            $('#refusInputCommentaire').next().attr('disabled', 'disabled');
        }
    });
</script>
