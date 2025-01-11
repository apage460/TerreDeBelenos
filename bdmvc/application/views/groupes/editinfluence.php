<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h2 class="page-header">Consulter l'Influence de <?php echo $groupeData->Nom; ?></h2>
            </div>        
            <!-- /.col-lg-12 -->
            <div class="col-xs-12">
                <h4>
                    <a href="<?php echo site_url('groupes'); ?>">
                    <span class="fa fa-chevron-left"></span> Retourner à la liste des groupes</a>
                </h4>
            </div>
        </div>
        <!-- /.row -->
        <?php 
            if(isset($influences[0]) ) :
                $totalPI = $influences[0]->TotalPI; 
            else :
                $totalPI = 0;
            endif;
        ?>
        <div class="row">
            <div class="col-xs-12">
                <?php if($totalPI > 0): ?>
                    <h3>Ce groupe a : <?php echo $influences[0]->TotalPI; ?> PI en réserve.</h3>
                <?php else: ?>
                    <h3>Ce groupe n'a pas de PI en réserve.</h3>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <h3>Détails</h3>
                <table class="table table-responsive table-striped">
                    <tr>
                        <th>Raison</th>
                        <th class="text-center">Nombre de PI</th>
                        <th>Date</th>
                        <th>Retirer</th>
                    </tr>
                    <?php foreach (array_slice($influences,0,4) as $influence) : ?>
                        <tr>
                            <td><?php echo $influence->Raison ?></td>
                            <td class="text-center"><?php echo $influence->PI; ?></td>
                            <td><?php echo $influence->DateInscription; ?></td>
                            <td>
                                <a href="<?php echo site_url('groupes/deleteInfluence/' .$influence->Id .'/' .$groupeData->Id); ?>">
                                    <button class="btn-danger btn"><span class="fa fa-close"></span></button>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <a href="#" class="pop" data-pop="allInfluence">
                    <button class="btn btn-primary btn-block">Tout voir <span class="fa fa-eye"></span></button>
                </a>
            </div>
            <div class="col-xs-12 col-md-6">
                <h3>Ajouter de l'influence</h3>
                <?php echo form_open('groupes/addInfluence/' .$groupeData->Id); ?>
                
                <div class="form-group">
                    <label for="raison">Raison</label>
                    <select name="raison" id="raison" class="form-control">
                        <option value="Résumé de GN - <?php echo $activite->Nom; ?>">Résumé de GN</option>
                        <option value="Spécialisation - <?php echo $activite->Nom; ?>">Spécialisation</option>
                        <option value="Actions - <?php echo $activite->Nom; ?>">Actions</option>
                        <option value="Ajustement spécial">Ajustement spécial</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="raison">Nombre de PI</label>
                    <select id="selectPI" class="form-control" name="pi">
                        <option value="-10">-10</option>
                        <option value="-9">-9</option>
                        <option value="-8">-8</option>
                        <option value="-7">-7</option>
                        <option value="-6">-6</option>
                        <option value="-5">-5</option>
                        <option value="-4">-4</option>
                        <option value="-3">-3</option>
                        <option value="-2">-2</option>
                        <option value="-1">-1</option>
                        <option value="---" selected="selected">-----</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>    
                    </select>
                </div>

                <br><br>
                <button id="btnAddInfluence" class="btn btn-block btn-primary" <?php if($totalPI >= 10): echo 'disabled="disabled"'; endif; ?>>Ajouter l'influence <span class="fa fa-plus"></span></button>
                <p><em>N.B. Il est impossible d'ajouter de l'influence si le groupe a atteint le maximum de 10 PI.</em></p>

                <?php echo form_close(); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-md-8 toPop" id="allInfluence">
                <h2>Tous les ajouts d'influence</h2>
                <table class="table table-responsive table-striped">
                    <tr>
                        <th>Raison</th>
                        <th class="text-center">Nombre de PI</th>
                        <th>Date</th>
                        <th>Retirer</th>
                    </tr>
                    <?php foreach ($influences as $influence) : ?>
                        <tr>
                            <td><?php echo $influence->Raison ?></td>
                            <td class="text-center"><?php echo $influence->PI; ?></td>
                            <td><?php echo $influence->DateInscription; ?></td>
                            <td>
                                <a href="<?php echo site_url('groupes/deleteInfluence/' .$influence->Id); ?>">
                                    <button class="btn-danger btn"><span class="fa fa-close"></span></button>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>

       
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->

<script>
    $(function(){
        $('.pop').on('click',function(){
            var target = '#' + $(this).attr('data-pop');

            $(target).bPopup({
                opacity : 0.8,
            });
        });

        $('#selectPI').on('change',function(){
            if( $('#selectPI :selected').val() === '---'|| ( parseInt($('#selectPI :selected').val() )+ <?php echo $totalPI; ?>) > 10 ){
                $('#btnAddInfluence').attr('disabled','disabled');
            } else {
                $('#btnAddInfluence').removeAttr('disabled');
            }
        });
    });
</script>