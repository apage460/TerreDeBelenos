<script>
    $(function(){

        var controller = 'Personnages',
            base_url = '<?php echo site_url();?>', 
            data;

        $('.searchField').delay(500).on('change keyup',function(event){
            event.preventDefault();
            prenomJoueur = $('#prenomJoueur').val();
            nomJoueur = $('#nomJoueur').val();

            avantApres = $('#avantApres').val();
            ddn = $('#ddn').val();

            prenomPerso = $('#prenomPerso').val();
            nomPerso = $('#nomPerso').val();

            niveauCtrl = $('#niveauCtrl').val();
            niveau = $('#niveau').val();

            classe = $('#classe').val();
            race = $('#race').val();
            religion = $('#religion').val();

            launchSearch(prenomJoueur, nomJoueur, avantApres, ddn, prenomPerso, nomPerso,niveauCtrl,niveau, classe, race, religion);
        })

        function launchSearch(prenomJoueur, nomJoueur, avantApres, ddn, prenomPerso, nomPerso, niveauCtrl, niveau, classe, race, religion){
            var container = $('#searchResults');
            container.html('<i class="fa fa-cog fa-spin fa-3x fa-fw"></i>');

            $.ajax({
                'url' : base_url + controller + '/searchPersonnages',
                'type' : 'POST',
                'data' : {'prenomJoueur':prenomJoueur,
                'nomJoueur':nomJoueur,
                'avantApres':avantApres,
                'ddn': ddn,
                'prenomPerso':prenomPerso,
                'nomPerso':nomPerso,
                'classe':classe,
                'niveauCtrl' : niveauCtrl,
                'niveau' : niveau,
                'race':race,
                'religion':religion},
                'success' : function(data){
                    if(data){
                        container.html(data);
                    }
                },
                'error' : function(err){
                    console.log(err);
                }
            });
        }
    });
</script>

<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h1 class="page-header">Consultation des personnages</h1>
            </div>        
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <hr/>

        <div class="row">
            <h3>Identification du joueur</h3>
            <form>
            <div class="col-xs-12 col-md-4">
                <label for="prenomJoueur">Prénom</label>
                <input type="text" name="prenomJoueur" id="prenomJoueur"class="form-control searchField">
            </div>
            <div class="col-xs-12 col-md-4">
                <label for="nomJoueur">Nom</label>
                <input type="text" name="nomJoueur" id="nomJoueur" class="form-control searchField">
            </div>
            <div class="col-xs-12 col-md-4 form-inline">
                <label for="ddn">Date de Naissance</label>
                <div class="form-group col-xs-12">                    
                    <div class="col-xs-4">
                        <select class="form-control" name="avantApres" id="avantApres">
                            <option value="<=">Avant le</option>
                            <option value=">=">Après le</option>
                        </select>
                    </div>
                    <div class="col-xs-8">                    
                        <input type="date" name="ddn" id="ddn" class="form-control searchField">
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <h3>Identification du personnage</h3>
            <div class="col-xs-12 col-md-4">
                <label for="prenomPerso">Prénom</label>
                <input type="text" name="prenomPerso" id="prenomPerso" class="form-control searchField">
            </div>
            <div class="col-xs-12 col-md-4">
                <label for="nomPerso">Nom</label>
                <input type="text" name="nomPerso" id="nomPerso" class="form-control searchField">
            </div>
            <div class="col-xs-12 col-md-4 form-inline">
                <label for="niveau">Niveau</label>
                <div class="form-group col-xs-12">                    
                    <div class="col-xs-5">
                        <select class="form-control" name="niveauCtrl" id="niveauCtrl">
                            <option value="<">Plus petit</option>
                            <option value="=">Égale</option>
                            <option value=">">Plus grand</option>
                        </select>
                    </div>
                    <div class="col-xs-6">                    
                        <input type="text" name="niveau" id="niveau" class="form-control searchField">
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-6 col-md-4">
                <label for="classe">Classe</label>
                <select name="classe" id="classe" class="form-control searchField">
                    <option value="">Toutes les classes</option>
                    <option value="BARDE">Barde</option>
                    <option value="CULTIST">Cultiste</option>
                    <option value="MAGE">Mage</option>
                    <option value="HMETIER">Homme de métier</option>
                    <option value="SHAMAN">Shaman</option>
                    <option value="GUERR">Guerrier</option>
                    <option value="ACOLYTE">Acolyte</option>
                    <option value="CLERC">Clerc</option>
                    <option value="MOINE">Moine</option>
                    <option value="VOLEUR">Voleur</option>
                    <option value="PRETRE">Prêtre</option>
                    <option value="ECLAIR">Éclaireur</option>
                </select>
            </div>

            <div class="col-xs-6 col-md-4">
                <label for="race">Race</label>
                <select name="race" id="race" class="form-control searchField">
                    <option value="">Toutes les races</option>
                    <option value="HUMAIN">Humain</option>
                    <option value="ELFE">Elfe</option>
                    <option value="NAIN">Nain</option>
                    <option value="CHAPPY">Chapardeur</option>
                    <option value="ORC">Orque</option>
                    <option value="GOBELIN">Gobelin</option>
                    <option value="ELFNOIR">ElfeNoir</option>
                    <option value="HRAT">Homme-Rat</option>
                </select>
            </div>

            <div class="col-xs-6 col-md-4">
                <label for="religion">Religion</label>
                <select name="religion" id="religion" class="form-control searchField">
                    <option value="">Toutes les religions</option>
                    <option value="">-----</option>
                    <option value="TOUDEMO">Tous les Démons</option>

                    <option value="AMAIRA">Amaï'ra</option>
                    <option value="CHAOSSM">Chaos Sans Magie</option>
                    <option value="CHAOS">Chaos Avec Magie</option>
                    <option value="GODTAKK">Godtakk</option>
                    <option value="KAALKH">Kaalkhorn</option>
                    <option value="KHALII">Khalii</option>
                    <option value="NOCTAVE">Noctave</option>
                    <option value="OTTOKOM">Ottor-kom</option>
                    <option value="TOYASH">Toyash</option>
                    <option value="">-----</option>
                    <option value="TOUDIEU">Tous les Dieux</option>
                    
                    <option value="AYKA">Ayka</option>
                    <option value="GAEA">Gaea</option>
                    <option value="GALLEON">Galléon</option>
                    <option value="GOLGOSM">Golgoth Sans Magie</option>
                    <option value="GOLGOTH">Golgoth Avec Magie</option>
                    <option value="MAKUDAR">Mak'udar</option>
                    <option value="SIBYL">Les Sibylles</option>
                    <option value="SYLVA">Sylva</option>                    
                    <option value="USIRE">Usire</option>
                    <option value="">-----</option>
                    <option value="ESPRITS">Les Esprits</option>
                    <option value="LAVOIE">La Voie</option>
                </select>
            </div>
        </div>
        </form>

        <div class="row">
            <div id="searchResults"></div>
        </div>
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->