<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h1 class="page-header">Gérer l'influence </h1>
            </div>
            <div class="row">
                <div class="col-md-3 col-xs-6">
                    <button class="btn btn-lg btn-block btn-primary" onClick="saveGroupes();">Sauvegarder <span class="fa fa-save"></span></button>
                </div>
            </div>      
            <!-- /.col-lg-12 -->
            <br>
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <table class="table table-responsive table-striped">
                    <tr>
                        <th>Nom</th>
                        <th class="text-center">Qte actuelle / Max</th>
                        <th class="text-center">Missions</th>
                        <th class="text-center">Campement</th>
                        <th class="text-center">Titres</th>
                        <th class="text-center">Déportations</th>
                        <th class="text-center">Historique</th>                     
                    </tr>
                    <?php foreach ($groupes as $groupe) : ?>
                        <tr data-idGroupe="<?= $groupe->Id; ?>">
                            <td><?php echo $groupe->Nom; ?></td>

                            <td class="text-center"><strong><?= $groupe->TotalPoints; ?> / <?= $groupe->InfluenceMaximum; ?></strong></td>

                            <td class="text-center">
                                <input type="radio" class="mission" data-idGroupe=<?= $groupe->Id; ?> name="mission-<?=$groupe->Id;?>" checked="checked" value="0"> 0
                                <?php if($groupe->InfluenceMaximum - $groupe->TotalPoints >= 1 ): ?>
                                    <input type="radio" class="mission" style="margin-left: 0.5rem;" data-idGroupe=<?= $groupe->Id; ?> name="mission-<?=$groupe->Id;?>" value="1"> 1
                                <?php endif; if($groupe->InfluenceMaximum - $groupe->TotalPoints >= 2 ): ?>
                                    <input type="radio" class="mission" style="margin-left: 0.5rem;" data-idGroupe=<?= $groupe->Id; ?> name="mission-<?=$groupe->Id;?>" value="2"> 2
                                <?php endif; if($groupe->InfluenceMaximum - $groupe->TotalPoints >= 3 ) : ?>
                                    <input type="radio" class="mission" style="margin-left: 0.5rem;" data-idGroupe=<?= $groupe->Id; ?> name="mission-<?=$groupe->Id;?>" value="3"> 3
                                <?php endif; ?>
                            </td>

                            <td>
                                <select class="form-control" data-idGroupe="<?= $groupe->Id; ?>" onClick="edited = true;" name="campement">
                                    <option value=""></option>
                                    <?php foreach ($campements as $camp): ?>
                                        <option value="<?= $camp->Code; ?>">
                                            <?= $camp->Nom; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>

                            <td>
                                <select class="form-control" name="titre" data-idGroupe="<?= $groupe->Id; ?>">
                                    <option value=" "></option>
                                    <option value="Baron d'Hyden">Baron d'Hyden</option>
                                    <option value="Maire d'Hyden">Maire d'Hyden</option>
                                    <option value="Baron des Brumes">Baron des Brumes</option>
                                    <option value="Baron de Syptosis">Baron de Syptosis</option>
                                    <option value="Comte de Francourt">Comte de Francourt</option>
                                </select>
                            </td>

                            <td class="text-left">
                                <? for ($i=0; $i <= ( $groupe->InfluenceMaximum - $groupe->TotalPoints); $i++) : ?> 
                                    <input type="radio" class="deportation" <? if($i > 0): echo 'style="margin-left: 0.5rem;"'; else: echo 'checked="checked"'; endif;?> data-idGroupe=<?= $groupe->Id; ?> name="deportation-<?=$groupe->Id;?>" value="<?= $i; ?>"> <?= $i; ?>
                                    <?php if($i % 3 == 0 && $i > 0): echo '<br/><br/>'; endif; ?>
                                <? endfor; ?>
                            </td>

                            <td class="text-center">
                                <button class="btn btn-primary pop" data-pop="historique" data-idGroupe="<?= $groupe->Id; ?>">
                                    <span class="fa fa-book"></span>
                                </button>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 col-xs-12 toPop" id="historique">
                <h3>Historique d'achat du groupe</h3>
                <h4 class="nomGroupe"></h4>
                <table style="width:50%" class="table table-reponsive table-striped achats col-xs-6">
                    <tr class="first">
                        <th>Action</th>
                        <th>Date d'inscription</th>
                        <th>Date d'approbation</th>
                        <th>Coût</th>
                        <th></th>
                    </tr>
                </table>
                <table style="width:50%" class="table table-reponsive table-striped gains  col-xs-6">
                    <tr class="first">
                        <th style="border-left: 1px solid black">Raison</th>
                        <th>Date d'inscription<br>&nbsp;</th>
                        <th>Points</th>
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
    function tagEdited(idGroupe){
        var select = $('select[data-idGroupe = "' +idGroupe +'"]');

        $(select).parents('tr[data-idGroupe="' +idGroupe +'"]').addClass('edited');
    }

    $('select[name="campement"], select[name="titre"]').on('change', function(e){
        e.preventDefault();
        var idGroupe = $(this).attr('data-idGroupe');

        $(this).parents('tr[data-idGroupe="' +idGroupe +'"]').addClass('edited');

    });

    $('input.mission, input.deportation').on('click', function(e){
        $(this).parents('tr').addClass('edited');
    });

    function saveGroupes(){
        var rows = $('tr.edited');
            data = [];

        rows.each(function(index, row){
            var idGroupe = $(row).attr('data-idGroupe'),
                campement = $('select[name="campement"][data-idGroupe ="' + idGroupe + '"] option:selected').html(),
                mission = $('input[name="mission-'+idGroupe +'"]:checked').val(),
                deportation = $('input[name="deportation-'+idGroupe +'"]:checked').val(),
                titre = $('select[name="titre"][data-idGroupe ="' + idGroupe + '"] option:selected').val()


            rowData = {
                'IdGroupe' : $(row).attr('data-idGroupe'),
                'Campement' : campement,
                'Mission' : mission,
                'Deportation' : (typeof deportation === "undefined")?'0':deportation,
                'Titre': titre
            };

            data.push(rowData);
        });

        if(data.length > 0){
            sendData = JSON.stringify(data);

            $.ajax({
                'url' : '<?= site_url("/groupes/updateGroupes"); ?>',
                'method' : 'POST',
                'data' : {sendData},
                'success' : function(data){
                    location.reload();
                },
                'error' : function(err){
                    console.log(err);
                }
            });
        }
    }

    function deleteInfluence(type, id){
        $.ajax({
            'url' : '<?= site_url("/groupes/deleteInfluence2/"); ?>' + type + '/' + id,
            'method' : 'POST',
            'data' : {
                'type' : type,
                'id' : id
            },
            'success' : function(){
                $('tr.' + id).css('text-decoration','line-through');
            },
            'error' : function(err){
                console.log(err);
            }
        });
    }

    $(function(){
        $('.pop').on('click',function(){
            var target = '#' + $(this).attr('data-pop');

            if($(this).attr('data-pop') == 'historique'){
                var idGroupe = $(this).attr('data-idGroupe')
                $.ajax({
                    'url' : '<?= site_url("/groupes/getHistoriqueGroupe/"); ?>' + idGroupe,
                    'method' : 'GET',
                    'success' : function(data){
                        data = JSON.parse(data);
                        if(data.length > 0){
                            var achats = '';
                            var gains = '';
                            $('#historique .nomGroupe').html(data[0].Nom);

                            data.forEach(function(el,index){
                                if(el.Description != null){
                                    achats += '<tr class="' +el.Id +'">';
                                    achats += '<td>' + el.Description + '</td>';
                                    achats += '<td>' + el.DateCreation + '</td>';
                                    achats += '<td>' + el.DateApprobation + '</td>';
                                    achats += '<td>' + el.CoutInfluence + '</td>';
                                    achats += '<td><button class="btn btn-danger" onClick="deleteInfluence(\'achat\','+ el.Id +')"><span class="fa fa-trash"></span></button></td>';
                                    achats += '</tr>';
                                } else {
                                    gains += '<tr class="' +el.InfId +'">';
                                    gains += '<td style="border-left: 1px solid black">' + el.InfRaison + '</td>';
                                    gains += '<td>' + el.InfPoints + '</td>';
                                    gains += '<td>' + el.InfDateInscription + '</td>';
                                    gains += '<td><button class="btn btn-danger" onClick="deleteInfluence(\'gain\','+ el.InfId +')"><span class="fa fa-trash"></span></button></td>';
                                    gains += '</tr>';
                                }
                            });

                            $('#historique table tr:not(.first)').remove();
                            
                            $('#historique table.achats').append(achats);
                            $('#historique table.gains').append(gains);
                        } else {

                        }                    
                    },
                    'error' : function(err){
                        console.log(err);
                    }
                });
            }

            $(target).bPopup({
                follow: [false, false],
                opacity : 0.8,
                onClose: function() { location.reload(); }
            });
        });
    });
</script>