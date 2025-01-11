<script>
    $(function(){

        var controller = 'Approbations',
            base_url = '<?php echo site_url();?>', 
            data;

        $('.btn_activateSkill').on('click',function(event){
            event.preventDefault();
            idCours = $(this).attr('data-idCours');

            activateSkill(idCours);
        })

        function activateSkill(idCours){
            $.ajax({
                'url' : base_url + controller + '/activateSkill',
                'type' : 'POST',
                'data' : {'idCours':idCours},
                'success' : function(data){
                    location.reload();
                },
                'error' : function(err){
                    console.log(err);
                }
            });
        }

        $('.btn_desactivateSkill').on('click',function(event){
            event.preventDefault();
            idCours = $(this).attr('data-idCours');

            desactivateSkill(idCours);
        })

        function desactivateSkill(idCours){
            $.ajax({
                'url' : base_url + controller + '/desactivateSkill',
                'type' : 'POST',
                'data' : {'idCours':idCours},
                'success' : function(data){
                    console.log('patate');
                    location.reload();
                },
                'error' : function(err){
                    console.log(err);
                }
            });
        }

        /*ADD SKILL TO LIST */

        $('#btn_addSkill').on('click',function(event){
            event.preventDefault();
            codeCours = $('#select_skillList :selected').val();
            idPersonnage = $('#idPersonnage').val();

            addSkill(codeCours, idPersonnage);
        })

        function addSkill(codeCours, idPersonnage){
            $.ajax({
                'url' : base_url + controller + '/addSkill',
                'type' : 'POST',
                'data' : {'codeCours':codeCours, 'idPersonnage' : idPersonnage},
                'success' : function(data){
                    location.reload();
                },
                'error' : function(err){
                    console.log(err);
                }
            });
        }
    });
</script>


<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
            <?php 
            $infoPerso = $personnageData['infoPerso'];

            if($personnageData['listeCours'] == null){
                echo '<h1 class="page-header">' .$infoPerso[0]['Prenom'] .' ' .$infoPerso[0]['Nom'] .' ne peut donné aucun cours.</h1>';
                echo '</div></div>';
            }
            else{

                $skillList = $personnageData['listeCours'];
                
            ?>
                <h1 class="page-header">Liste de cours donnés de <?php echo $infoPerso[0]['Prenom'] .' ' .$infoPerso[0]['Nom']; ?></h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <div class="row">
            <div class="col-xs-12 col-lg-8">
                <table class="table table-striped-table-responsive">
                    <tr>
                        <th>Compétence</th>
                        <th>Date</th>
                        <th>État</th>
                        <th></th>
                    </tr>
                    <?php 
                        foreach ($skillList as $competence) {
                    ?>
                        <tr>
                            <td><?php echo $competence['NomSkill']; ?></td>
                            <td><?php echo $competence['DateCreation']; ?></td>
                            <td><b><?php echo $competence['CodeEtat']; ?><b></td>
                            <td>
                                <?php 
                                    if($competence['CodeEtat'] == 'INACT'){ ?>
                                        <button class="btn_activateSkill btn btn-success" data-idCours="<?php echo $competence['Id']?>"><span class="fa fa-check fa-reverse"></span></button>
                                <?php
                                    } //endif
                                    else{
                                ?>
                                    <button class="btn_desactivateSkill btn btn-danger" data-idCours="<?php echo $competence['Id']?>"><span class="fa fa-remove fa-reverse"></span></button>
                                <?php
                                    }//end else
                                ?>
                            </td>
                        </tr>
                    <?php
                        } //end foreach
                    ?>
                </table>
            </div>
        </div>
        <?php } //endelse ?>
        <div class="row">
            <div class="col-xs-12 col-lg-4">
            <input id="idPersonnage" type="hidden" value="<?php echo $infoPerso[0]['Id']; ?>">
                <select id="select_skillList" class="form-control">
                    <?php 
                        foreach ($listeCompetences as $competence) { 
                    ?>
                            <option value="<?php echo $competence['Code']; ?>"><?php echo $competence['Nom']; ?></option>
                    <?php
                        } //end foreach
                    ?>
                </select>
                <button id="btn_addSkill" class="btn btn-primary">Ajouter à la liste <span class="fa fa-plus fa-reverse"></span></button>
            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->