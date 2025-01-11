<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h1 class="page-header">Gestion des Accès</h1>
                <h3 id="successMsg"></h3>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->

        <div class="row">
            <div class="col-xs-10">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#accesList" aria-controls="accesList" role="tab" data-toggle="tab">Liste des Accès</a>
                    </li>
                    <li role="presentation">
                        <a href="#ajoutAcces"  aria-controls="ajoutAcces" role="tab" data-toggle="tab">Ajout d'accès</a>
                    </li>
                </ul>

                <div class="tab-content col-md-8 col-xs-12">
                    <div role="tabpanel" class="tab-pane active" id="accesList">
                        <div class="row">   
                            <div class="col-xs-12"> 
                                <table class="table table-striped table-responsive">
                                    <tr>
                                        <th>Prénom</th>
                                        <th>Nom</th>
                                        <th>Niv. Accès</th>
                                        <th>Accès</th>
                                        <th>Univers</th>
                                        <th></th>
                                    </tr>
                                    <?php foreach ($acces as $index => $acc) : ?>
                                        <tr>
                                            <td>
                                            <?php 
                                                if($index == 0 || $acc->Id != $acces[$index-1]->Id){
                                                    echo $acc->Prenom; 
                                                }
                                            ?>                                    
                                            </td>
                                            <td>
                                            <?php 
                                                if($index == 0 || $acc->Id != $acces[$index-1]->Id){
                                                    echo $acc->Nom; 
                                                }
                                            ?>                                    
                                            </td>
                                            <td><?= $acc->NiveauAcces; ?></td>
                                            <td><?= $acc->Acces; ?></td>
                                            <td><?= $acc->CodeUnivers; ?></td>
                                            <td>
                                                <button data-idIndiv="<?= $acc->Id; ?>" data-acces="<?= $acc->Acces; ?>" class="removeAcces btn btn-danger">
                                                    <span class="fa fa-close"></span>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>                    
                                </table>
                            </div>  
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="ajoutAcces">
                        <br>
                        <div class="row">
                            <div class="col-xs-12">
                                <form id="formGetIndividus" action="<?php echo site_url("Administration/searchIndividu") ?>" method="GET">
                                    <div class="col-xs-12 col-md-4">
                                        <label for="prenom">Prénom</label>
                                        <input type="text" name="prenom" id="prenom"class="form-control">
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <label for="nom">Nom</label>
                                        <input type="text" name="nom" id="nom"class="form-control">
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <br>
                                        <button class="btn btn-primary btn-block">
                                            <span class="fa fa-search"></span> Rechercher
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <br><br>
                        <div class="row" id="resultsContainer" style="display: none;">
                            <div class="col-xs-12">
                                <table id="resultsTable" class="table table-responsive table-striped">
                                    <tr>
                                        <th>Prénom</th>
                                        <th>Nom</th>
                                        <th>Accès</th>
                                        <th></th>
                                    </tr>
                                </table>
                            </div>
                        </div>
                   </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->

<script>
    $('button.removeAcces').click(function(){
        $.ajax({
            url : '<?php echo site_url('Administration/removeAcces'); ?>',
            method : 'POST',
            data : {
                'idIndiv' : $(this).attr('data-idIndiv'),
                'acces' : $(this).attr('data-acces')
            },
            success : function(data){
                location.reload();
            },
            error : function(err){
                console.log(err);
            }
        }); 
    });

    $('#formGetIndividus').on('submit', function(e){
        e.preventDefault();

        if( $('#resultsContainer').css('display') != 'none' ){
            $('#resultsContainer').slideToggle();
        }

        var prenom = $(this).find('#prenom').val(),
            nom = $(this).find('#nom').val();

        $.ajax({
            url : '<?php echo site_url('Administration/getIndividus') ?>',
            method : 'GET',
            data : {
                'prenom' : prenom,
                'nom' : nom,
            },
            success : function(data){
                $('#resultsTable tr:not(:first)').remove();
                var html = '';

                if(data){
                    var datarows = JSON.parse(data)

                    datarows.forEach(function(el, index){
                        html += '<tr>';
                        html += '<td>' + el.Prenom + '</td>';
                        html += '<td>' + el.Nom + '</td>';
                        html += '<td><select data-idIndiv="' + el.Id + '" class="form-control">'
                        html += '<option value="Admin">Admin</option>';
                        html += '<option value="Reviseur">Réviseur</option>';
                        html += '<option value="Scenariste">Scénariste</option>';
                        html += '<option value="Scripteur">Scripteur</option>';
                        html += '</select></td>';
                        html += '<td><button data-idIndiv="' + el.Id +'" class="btn btn-success addAcces" onClick="addAcces(' +el.Id +');"><span class="fa fa-plus"></span></button></td>';
                        html += '</tr>';
                    });
                }
                $('#resultsTable').append(html);

                $('#resultsContainer').slideToggle();
            },
            error : function(err){
                console.log(err);
            }
        });
    });

    function addAcces(idIndiv){
        var typeAcces = $('select[data-idIndiv=' +idIndiv +'] option:selected').val();
        $.ajax({
            url : '<?php echo site_url('Administration/addAcces/'); ?>' + idIndiv + '/' +typeAcces,
            method : 'POST',
            data : {},
            processData : false,
            success : function(data){
                if(data == 0){
                    $('#successMsg').html("Cette personne a déjà l'accès " + typeAcces);
                } else {
                    var data = JSON.parse(data);
                    $('#successMsg').html(data.Prenom + ' ' + data.Nom +" a reçu l'accès " + data.Acces);
                }
            },
            error : function(err){
                console.log(err);
            }
        });        
    }
</script>