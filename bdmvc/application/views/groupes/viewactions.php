<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h1 class="page-header">Consulter les actions</h1>
            </div>        
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-xs-12">
                <table class="table table-responsive table-striped">
                    <tr>
                        <th>Nom du Groupe <br>  ( Nb de PI )</th>
                        <th>
                            <table class="table table-striped">
                                <tr>
                                    <td class="col-xs-2">
                                        Action faite
                                    </td>
                                    <td class="col-xs-3">
                                        Description
                                    </td>
                                    <td class="col-xs-4">
                                        Notes
                                    </td>
                                    <td class="col-xs-1">
                                        Nb d'achat
                                    </td>
                                    <td class="col-xs-1">
                                        Coût total
                                    </td>                                            
                                    <td class="col-xs-1">
                                        Approbation
                                    </td>
                                </tr>
                            </table>
                        </th>
                    </tr>
                    <?php foreach ($demandes as $demande) : ?>
                        <tr>
                            <td class="col-xs-1"><?php echo $demande['Nom'] .'<br><em>(' .$demande['nbPI'] .')</em>'; ?></td>
                            <td>
                                <?php foreach ($demande['actions'] as $action) : ?>                                    
                                    <table class="table table-striped">
                                        <tr>
                                            <td class="col-xs-2">
                                                <?php echo $action->Nom; ?>
                                            </td>
                                            <td class="col-xs-3">
                                                <?php echo $action->Description; ?>
                                            </td>
                                            <td class="col-xs-4">
                                                <?php echo $action->InfoSupp; ?>
                                            </td>
                                            <td class="col-xs-1">
                                                <?php echo $action->Achats; ?>
                                            </td>
                                            <?php if($action->CodeAction != 'SPECIAL'): ?>
                                                <td class="col-xs-1">
                                                    <?php echo $action->CoutInfluence; ?>
                                                </td>
                                                <td class="col-xs-2">
                                                    <a href="<?php echo site_url('groupes/acceptAction/' .$demande['Id'] .'/' .$action->Id); ?>">
                                                        <button class="btn btn-success"><span class="fa fa-check"></span></button>
                                                    </a>
                                                    <a href="#" class="refusAction" data-idGroupe="<?php echo $demande['Id']; ?>" data-idAction="<?php echo $action->Id; ?>">
                                                        <button class="btn btn-danger"><span class="fa fa-close"></span>
                                                        </button>
                                                    </a>                                           
                                                </td>
                                            <?php else: ?>
                                                <td class="col-xs-1">
                                                    <?php echo form_open('groupes/acceptAction/' .$demande['Id'] .'/' .$action->Id); ?>
                                                    <select name="coutPI" id="" class="form-control">
                                                        <?php for ($i=0; $i < $demande['nbPI'] ; $i++) : ?>
                                                            <option value="<?php echo $i+1; ?>"><?php echo $i+1; ?></option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </td>
                                                <td class="col-xs-2">
                                                    <button class="btn btn-success"><span class="fa fa-check"></span></button>                                                    
                                                    <?php echo form_close(); ?>
                                                    <a href="#" class="refusAction" data-idGroupe="<?php echo $demande['Id']; ?>" data-idAction="<?php echo $action->Id; ?>">
                                                        <button class="btn btn-danger"><span class="fa fa-close"></span>
                                                        </button>
                                                    </a>                                              
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    </table>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>

             <div id="displayRefusModal" class="row toPop">
                <div class="col-xs-12 refusContainer">
                    
                </div>
                
            </div>
        </div>

       
       
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->
<script>
    $('.refusAction').click(function(e){
        e.preventDefault();

        idGroupe = $(this).attr('data-idGroupe');
        idAction = $(this).attr('data-idAction');

        $('#displayRefusModal').bPopup({
            content: 'ajax',
            contentContainer: '#displayRefusModal .refusContainer',
            loadUrl: '<?php echo site_url('groupes/displayRefusModal'); ?>/' +idGroupe +'/' +idAction ,
        });
    })
</script>