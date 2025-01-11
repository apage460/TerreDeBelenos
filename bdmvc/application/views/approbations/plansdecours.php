<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h1 class="page-header">Plans de cours</h1>
            </div>        
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-xs-12">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#dem">Demandes</span></a></li>
                    <li><a data-toggle="tab" href="#approved">Approuvés </a></li>
                </ul>

                <div class="tab-content">
                    <div id="dem" class="tab-pane fade in active">
                        <h3>Demandes d'approbation</h3>

                        <table class="table table-responsive table-striped">
                            <tr>
                                <th>Personnages</th>
                                <th>Compétence</th>
                                <th>Date de la demande</th>
                                <th style="text-align: center;">Visualiser</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                            <?php foreach($plansDeCours as $plan): ?>
                            <tr>
                                <td><?= $plan->NomPerso; ?></td>
                                <td><?= $plan->NomSkill; ?></td>
                                <td><?= $plan->DateCreation; ?></td>
                                <td style="text-align: center;">
                                    <a href="<?= '/BD/uploads/Plans de cours/Plan de cours - ' .$plan->CodeCompetence .' - P' .$plan->IdPersonnage .'.pdf'?>" target="_blank">
                                        <button class="btn btn-primary"><span class="fa fa-file-pdf-o"></span></button>
                                    </a>
                                </td>
                                <td style="text-align: center;">
                                    <a class="approv" href="<?php echo site_url('Approbations/approvPlanDeCour/') . $plan->Id; ?>">
                                        <button class="btn btn-success"><span class="fa fa-check"></span></button>
                                    </a>
                                    &nbsp;&nbsp;
                                    <a class="refus" attr-idPlan="<?= $plan->Id; ?>" href="#">
                                        <button class="btn btn-danger"><span class="fa fa-times"></span></button>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>

                        <div class="row">
                            <div class="col-md-6 col-xs-12 toPop" id="refusPlan">
                                <h3>Refuser un plan de cours</h3>
                                <?php echo form_open('approbations/refusPlanDeCour/');?>
                                    <input type="hidden" name="idPlan">

                                    <input class="form-control" type="text" name="raison" required>
                                    <br>
                                    <button class="btn btn-primary">Refuser le Plan de Cours</button>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                        
                    </div>
                    <div id="approved" class="tab-pane fade">
                        <h3>Plans de cours approuvés</h3>
                        <ul class="alphabet">
                            <li><a class="lettre" data-lettre="" href="#">Tous</a></li>
                            <li><a class="lettre" data-lettre="a" href="#">A</a></li>
                            <li><a class="lettre" data-lettre="b" href="#">B</a></li>
                            <li><a class="lettre" data-lettre="c" href="#">C</a></li>
                            <li><a class="lettre" data-lettre="d" href="#">D</a></li>
                            <li><a class="lettre" data-lettre="e" href="#">E</a></li>
                            <li><a class="lettre" data-lettre="f" href="#">F</a></li>
                            <li><a class="lettre" data-lettre="g" href="#">G</a></li>
                            <li><a class="lettre" data-lettre="h" href="#">H</a></li>
                            <li><a class="lettre" data-lettre="i" href="#">I</a></li>
                            <li><a class="lettre" data-lettre="j" href="#">J</a></li>
                            <li><a class="lettre" data-lettre="k" href="#">K</a></li>
                            <li><a class="lettre" data-lettre="l" href="#">L</a></li>
                            <li><a class="lettre" data-lettre="m" href="#">M</a></li>
                            <li><a class="lettre" data-lettre="n" href="#">N</a></li>
                            <li><a class="lettre" data-lettre="o" href="#">O</a></li>
                            <li><a class="lettre" data-lettre="p" href="#">P</a></li>
                            <li><a class="lettre" data-lettre="q" href="#">Q</a></li>
                            <li><a class="lettre" data-lettre="r" href="#">R</a></li>
                            <li><a class="lettre" data-lettre="s" href="#">S</a></li>
                            <li><a class="lettre" data-lettre="t" href="#">T</a></li>
                            <li><a class="lettre" data-lettre="u" href="#">U</a></li>
                            <li><a class="lettre" data-lettre="v" href="#">V</a></li>
                            <li><a class="lettre" data-lettre="w" href="#">W</a></li>
                            <li><a class="lettre" data-lettre="x" href="#">X</a></li>
                            <li><a class="lettre" data-lettre="y" href="#">Y</a></li>
                            <li><a class="lettre" data-lettre="z" href="#">Z</a></li>
                        </ul>
                        <div class="row">
                            <div class="col-xs-6">
                                <table id="listePlansDeCours" class="table table-striped table-responsive">
                                    <tr>
                                        <th>Personnage (Individu)</th>
                                        <th></th>
                                    </tr>
                                    <?php foreach($approuves as $pdc): ?>
                                        <?php if(!is_null($pdc->persoFull)) : ?>
                                        <tr class="<?= strtolower(substr($pdc->persoFull[0], 0, 1)); ?>">
                                            <td><?= $pdc->persoFull .'<em>(' .$pdc->indivFull .')</em>'; ?></td>
                                            <td>
                                                <a href="#" class="pdc" data-idPerso="<?php echo $pdc->idPerso;  ?>" >
                                                <button class="btn btn-primary" >
                                                    <span class="fa fa-search"></span></button>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </table>  
                            </div>
                        </div>

                        
                    </div>
                </div>
            </div>
        </div>
        <div class="row" >
            <div class="toPop col-md-6 col-xs-12" id="listPDC">
                <h3>Plans de cours de <span></span></h3>
                <table class="table table-striped">
                    <tr>
                        <th>Compétence</th>
                        <th></th>
                    </tr>
                </table>
            </div>                            
        </div>       
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->

<script>
    $('a.approv').on('click', function(e){
        if(confirm('Voulez-vous approuver ce plan de cours?') == false){
            e.preventDefault();
        }
    });

    $('a.refus').on('click', function(e){
        if(confirm('Voulez-vous refuser ce plan de cours?') == false){
            e.preventDefault();
        } else {
            $('input[name="idPlan"]').val($(this).attr('attr-idplan'));
            $('#refusPlan').bPopup({
                opacity: 0.8
            });
        }
    });

    $('a.lettre').on('click', function(e){
        e.preventDefault();
        var lettre = $(this).attr('data-lettre'),
            regex = /[a-z]/g;

        if(lettre.match(regex)){
            $('#listePlansDeCours tr:not(:first)').hide('fade');
            $('#listePlansDeCours tr.' + lettre).show('fade');
        } else{
            $('#listePlansDeCours tr:not(:first)').show('fade');
        }
    });

    $('a.pdc').on('click', function(e){
        e.preventDefault();

        var idPerso = $(this).attr('data-idPerso');

        $.ajax({
            url : '<?= site_url('approbations/getPlansdeCours/')?>' + idPerso,
            method : 'GET',
            data : {

            },
            success : function(data){
                var data = JSON.parse(data),
                    html = '';

                    console.log(data);

                data.plansDeCours.forEach(function(el, index){
                    html += '<tr>';
                    html += '<td>';
                    html += el.competence.Nom;
                    html += '</td>';
                    html += '<td>';
                    html += '<a target="_blank" href="' +el.url +'">';
                    html += '<button class="btn btn-primary"><span class="fa fa-file-pdf-o"></span></button>';
                    html += '</a>'
                    html += '</td>';
                    html += '</tr>';
                });

                $('#listPDC h3 span').empty();
                $('#listPDC table tr:not(:first)').remove();

                $('#listPDC table').append(html);

                $('#listPDC h3 span').html(data.perso);
                $('#listPDC').bPopup({
                    opacity : 0.8,
                });

            },
            error : function(err){
                console.log(err);
            }
        });

    })
</script>