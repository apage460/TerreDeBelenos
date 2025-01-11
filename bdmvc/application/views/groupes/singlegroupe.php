<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h2 class="page-header">Détails de <?php echo $groupeData->Nom; ?></h2>
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
        <div class="row">
        	<div class="col-xs-12 col-md-6">
                <h3>État</h3>
                <p>
                    <?php echo $groupeData->CodeEtat; ?> 
                    <?php if( $groupeData->CodeEtat != 'OFFIC'  ): ?>
                        <a style="margin-left: 2em" href="<?php echo site_url('groupes/officialGroupe/' .$groupeData->Id); ?>"><button class="btn btn-primary">Officialiser le groupe <span class="fa fa-star"></span></button></a>
                    <?php endif; ?>
                </p>
                <h3>Profils </h3>
                <p class="">
                    <ul>
                        <?php foreach ($profils as $profil) : ?>
                            <li>
                                <?php echo $profil->Nom; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="#" class="pop" data-pop="editProfils"><button class="btn btn-primary">Modifier les Profils <span class="fa fa-refresh"></span></button></a>                
                    <a href="#" class="pop" data-pop="editSpecs"><button class="btn btn-primary">Modifier les Spécialisations <span class="fa fa-refresh"></span></button></a>
                </p>
                <a href="#" class="toggler"><h3>Description <span class="fa fa-caret-down"></span></h3></a>
                <p class="toggled">
                    <?php echo $groupeData->Description; ?>
                </p>
                <a href="#" class="toggler"><h3>Historique <span class="fa fa-caret-down"></span></h3></a>
                <p class="toggled">
                    <?php echo $groupeData->Historique; ?>
                </p>
                <a href="#" class="toggler"><h3>Chefferie <span class="fa fa-caret-down"></span></h3></a>
                <p class="toggled">
                    <?php echo $groupeData->Chefferie; ?>
                </p>
                <a href="#" class="toggler"><h3>Informations Sur le Campement <span class="fa fa-caret-down"></span></h3></a>
                <p class="toggled">
                    <?php echo $groupeData->InfoSup; ?>
                </p>
               
                
        	</div>
            <div class="col-xs-12 col-md-6">
                <h3>Responsables</h3>
                <table class="table table-responsive table-striped">
                    <tr>
                        <th>Nom (Compte)</th>
                        <th>Courriel</th>
                        <th class="text-center">Annul. Responsable</th>
                    </tr>
                    <?php foreach ($responsables as $responsable) : ?>
                        <tr>
                            <td><?php echo $responsable->NomIndiv . '( <em>' .$responsable->Compte .'</em> )';?></td>
                            <td><?php echo $responsable->Courriel; ?></td>
                            <td class="text-center">
                                <a href="<?php echo site_url('groupes/demoteResponsable/' .$responsable->IdResponsable .'/' .$groupeData->Id); ?>">
                                    <button class="btn btn-danger"><span class="fa fa-minus"></span></button>
                                </a>
                                
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>

                <hr>

                <h3>Membres</h3>
                <table class="table table-responsive table-striped">
                    <tr>
                        <th>Membres</th>
                        <th>État</th>
                        <th class="text-center">Consulter/Resp</th>
                        <th class="text-center">Retirer</th>
                    </tr>
                    <?php foreach ($membres as $membre) : ?>
                        <tr>
                            <td>
                                <?php echo $membre->NomPerso .'( <em>' .$membre->NomIndiv .'</em> )'; ?>
                            </td>
                            <td><?php echo $membre->CodeEtat; ?></td>
                            <td class="text-center">
                                <a href="<?php echo site_url('personnages/editPersonnage/' .$membre->IdPersonnage .'/' .$membre->IdIndividu); ?>" target="_blank">
                                    <button class="btn btn-primary"><span class="fa fa-eye"></span></button>
                                </a>
                                <?php if( count($responsables) < 3 && $membre->IdResponsable == null ) : ?>
                                    <a href="<?php echo site_url('groupes/promoteResponsable/' .$membre->IdIndividu .'/' .$groupeData->Id); ?>">
                                        <button class="btn btn-success">
                                            <span class="fa fa-star"></span>
                                        </button>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="<?php echo site_url('groupes/removeFromGroupe/' .$membre->IdPersonnage .'/' .$groupeData->Id); ?>">
                                    <button class="btn btn-danger"><span class="fa fa-close"></span></button>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>

                <hr>

                <h3>Objectifs</h3>

                <?php foreach ($objectifs as $objectif) : ?>
                    <a href="#" class="toggler"><h4><?php echo $objectif->Nom .'( <em>' .$objectif->Type .'</em> )'; ?> <span class="fa fa-caret-down"></span></h4></a>
                    <p class="toggled">
                        <?php echo $objectif->Description; ?>
                    </p>
                <?php endforeach; ?>

            </div>
        </div>

        <div class="row">
            <div class="col-md-8 col-xs-12 toPop" id="editProfils">
                <h3>Modifier les Profils</h3>
                <table class="table table-responsive table-striped">
                    <tr>
                        <th>Profils Actuels</th>
                        <th>Actions</th>
                        <th></th>
                    </tr>
                    <?php foreach ($profils as $profil) : ?>
                        <tr>
                            <td><?php echo $profil->Nom; ?></td>
                            <td>
                                <a href="<?php echo site_url('groupes/removeProfil/' .$groupeData->Id .'/' .$profil->CodeProfil); ?>">
                                    <button class="btn btn-danger">Supprimer <span class="fa fa-close"></span>
                                    </button>
                                </a>
                            </td>
                            <td></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <?php echo form_open('groupes/addProfil/' .$groupeData->Id); ?>
                        <td><strong>Nouveau Profil</strong></td>
                        <td>
                            <select name="newProfil" class="form-control">
                                <?php foreach ($pilotProfils as $pilotProfil) : ?>
                                    <option value="<?php echo $pilotProfil->Code; ?>"><?php echo $pilotProfil->Nom; ?></option>
                                <?php endforeach; ?>                                
                            </select>
                        </td>
                        <td>
                            <button class="btn btn btn-primary" <?php if(count($profils) >= 2) : ?> disabled="disabled" <?php endif; ?>>
                                Ajouter le profil <span class="fa fa-plus"></span>
                            </button>
                        </td>
                        <?php echo form_close(); ?>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 col-xs-12 toPop" id="editSpecs">
                <h3>Modifier les Spécialisations</h3>
                <table class="table table-responsive table-striped">
                    <tr>
                        <th>Spécialisations Actuelles</th>
                        <th>Actions</th>
                        <th></th>
                    </tr>
                    <?php foreach ($specs as $spec) : ?>
                        <tr>
                            <td><?php echo $spec->Description; ?></td>
                            <td><?php echo $spec->CodeEtat; ?></td>
                            <td>
                                <!--<a href="<?php echo site_url('groupes/removeSpec/' .$groupeData->Id .'/' .$spec->Id); ?>">
                                    <button class="btn btn-danger">Supprimer <span class="fa fa-close"></span>
                                    </button>
                                </a>-->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <?php echo form_open('groupes/addSpec/' .$groupeData->Id); ?>
                        <td><strong>Nouveau Profil</strong></td>
                        <td>
                            <select name="newSpec" class="form-control">
                                <?php foreach ($avantages as $avantage) : ?>
                                    <?php foreach ($avantage as $av) : ?>
                                         <option value="<?php echo $av->Code; ?>"><?php echo $av->Nom ?></option>
                                    <?php endforeach ?>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <button class="btn btn btn-primary" <?php if(false /*Dépendera de la date*/) : ?> disabled="disabled" <?php endif; ?>>
                                Ajouter la spécialisation <span class="fa fa-plus"></span>
                            </button>
                        </td>
                        <?php echo form_close(); ?>
                    </tr>
                </table>
            </div>
        </div>        
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->

<script>
    $(function(){
        $('.toggler').on('click',function(e){
            e.preventDefault();
            $(this).next('p').slideToggle();
        })
    });

    $(function(){
        $('.pop').on('click',function(){
            var target = '#' + $(this).attr('data-pop');

            $(target).bPopup({
                opacity : 0.8,
            });
        });
    });

</script>

