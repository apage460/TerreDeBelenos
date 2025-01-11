<div id="page-wrapper">
    <div class="container-fluid">
        <!-- STEP 0 -->
        <?php if(!isset($step)): ?>
            <div class="row">
            	<div class="col-xs-12">
            		<h1>Création d'un personnage</h1>
            		<h2>Recherche de joueur</h2>
                    <?php echo form_open('personnages/createNewPersonnage',array(
                            'method' => 'get'
                        )); ?>
                        <div class="row">
                            <div class="form-group col-md-3 col-xs-12">
                                <label for="pseudoIndiv">Pseudo</label>
                                <input type="text" name="pseudoIndiv" class="form-control">
                            </div>
                            <div class="form-group col-md-3 col-xs-12">
                                <label for="prenomIndiv">Prénom</label>
                                <input type="text" name="prenomIndiv" class="form-control">
                            </div>
                            <div class="form-group col-md-3 col-xs-12">
                                <label for="nomIndiv">Nom</label>
                                <input type="text" name="nomIndiv" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6">
                                <button class="btn btn-primary btn-lg">Rechercher</button>
                                <?php echo form_close(); ?>  
                            </div>
                        </div>
                    <?php echo form_close(); ?>
        		</div>
                <?php if(isset($searchResults)): ?>
                    <div class="col-xs-9">
                        
                        <table class="table table-striped">
                            <tr>
                                <th>Joueur</th>
                                <th>Compte</th>
                                <th></th>
                            </tr>
                            <?php foreach($searchResults as $result) : ?>
                                <?php if(!is_null($result->Pseudo)){
                                    $formattedNom = $result->Pseudo;
                                } else{
                                    $formattedNom = $result->Prenom .' ' .$result->Nom;
                                } ?>        
                                <tr>                                
                                    <td><?= $formattedNom; ?></td>
                                    <td><?= $result->Compte; ?></td>
                                    <td><a href="<?= site_url('personnages/createNewPersonnage/1/' . $result->Id) ?>"><button class="btn btn-primary"><span class="fa fa-arrow-right"></span></button></a></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>                    
                    </div>
                <?php endif; ?>
            </div>
        <?php elseif ($step == 1) : ?>
            <?php if(!is_null($indiv->Pseudo)){
                $formattedNom = $indiv->Pseudo;
            } else{
                $formattedNom = $indiv->Prenom .' ' .$indiv->Nom;
            } ?> 
            <div class="row">
                <div class="col-xs-12">
                    <h1>Création de personnage pour <?= $formattedNom?> <em>(<?= $indiv->Compte; ?>)</em></h1>
                    <hr>
                    <h3>Étape <?= $step; ?></h3>

                    <?php echo form_open('personnages/createNewPersonnage/2/' .$indiv->Id,array(
                            'method' => 'post'
                        )); ?>
                        <div class="row">
                            <div class="form-group col-md-3 col-xs-12">
                                <label for="persoPrenom">Prénom</label>
                                <input type="text" name="persoPrenom" required="required" placeholder="Prénom" class="form-control">
                            </div>
                            <div class="form-group col-md-3 col-xs-12">
                                <label for="persoNom">Nom</label>
                                <input type="text" name="persoNom" required="required" placeholder="Nom" class="form-control">
                            </div>
                            <div class="form-group col-md-3 col-xs-12">
                                <label for="persoRace">Race</label>
                                <select name="persoRace" class="form-control">
                                    <?php foreach($pilotRaces as $race): ?>
                                        <option value="<?= $race->Code; ?>"><?= $race->Nom; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6">
                                <button class="btn btn-primary btn-lg">Étape suivante</button>
                                <?php echo form_close(); ?>  
                            </div>
                        </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        <!-- STEP 2 -->
        <?php elseif ($step == 2) : ?>
            <?php if(!is_null($indiv->Pseudo)){
                $formattedNom = $indiv->Pseudo;
            } else{
                $formattedNom = $indiv->Prenom .' ' .$indiv->Nom;
            } ?> 
            <div class="row">
                <div class="col-xs-12">
                    <h1>Création de personnage pour <?= $formattedNom?> <em>(<?= $indiv->Compte; ?>)</em></h1>
                    <hr>
                    <h3>Étape <?= $step; ?></h3>

                        <div class="row">
                            <div class="form-group col-md-3 col-xs-12">
                                <label for="persoPrenom">Prénom</label>
                                <input type="text" name="persoPrenom" disabled="disabled" value="<?=$_SESSION['newPerso']['prenom'] ?>" class="form-control">
                            </div>
                            <div class="form-group col-md-3 col-xs-12">
                                <label for="persoNom">Nom</label>
                                <input type="text" name="persoNom" disabled="disabled" value="<?= $_SESSION['newPerso']['nom'] ?>" class="form-control">
                            </div>
                            <div class="form-group col-md-3 col-xs-12">
                                <label for="persoRace">Race</label>
                                <select name="persoRace" class="form-control" disabled="disabled">
                                    <?php foreach($pilotRaces as $race): ?>
                                        <option value="<?= $race->Code; ?>" <?php if($_SESSION['newPerso']['race'] == $race->Code): echo 'selected="selected"'; endif;?>>
                                            <?= $race->Nom; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <?php echo form_open('personnages/createNewPersonnage/3/' .$indiv->Id,array(
                            'method' => 'post'
                        )); ?>
                        <div class="row">
                            <div class="form-group col-md-offset-3 col-md-3 col-xs-12">
                                <label for="persoRace">Race</label>
                                <select name="persoClasse" class="form-control">
                                    <?php foreach($pilotClasses as $classe): ?>
                                        <option value="<?= $classe->Code; ?>" <?php if($_SESSION['newPerso']['race'] == $race->Code): echo 'selected="selected"'; endif;?>>
                                            <?= $classe->Nom; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6 col-md-3 col-md-offset-3 text-center">
                                <button class="btn btn-primary btn-lg">Étape suivante</button>
                                <?php echo form_close(); ?>  
                            </div>
                        </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        <!-- STEP 3 -->
        <?php elseif ($step == 3) : ?>
            <?php if(!is_null($indiv->Pseudo)){
                $formattedNom = $indiv->Pseudo;
            } else{
                $formattedNom = $indiv->Prenom .' ' .$indiv->Nom;
            } ?> 
            <div class="row">
                <div class="col-xs-12">
                    <h1>Création de personnage pour <?= $formattedNom?> <em>(<?= $indiv->Compte; ?>)</em></h1>
                    <hr>
                    <h3>Étape <?= $step; ?></h3>
                    <div class="row">
                        <div class="form-group col-md-3 col-xs-12">
                            <label for="persoPrenom">Prénom</label>
                            <input type="text" name="persoPrenom" disabled="disabled" value="<?=$_SESSION['newPerso']['prenom'] ?>" class="form-control">
                        </div>
                        <div class="form-group col-md-3 col-xs-12">
                            <label for="persoNom">Nom</label>
                            <input type="text" name="persoNom" disabled="disabled" value="<?= $_SESSION['newPerso']['nom'] ?>" class="form-control">
                        </div>
                        <div class="form-group col-md-3 col-xs-12">
                            <label for="persoRace">Race</label>
                            <select name="persoRace" class="form-control" disabled="disabled">
                                <?php foreach($pilotRaces as $race): ?>
                                    <option value="<?= $race->Code; ?>" <?php if($_SESSION['newPerso']['race'] == $race->Code): echo 'selected="selected"'; endif;?>>
                                        <?= $race->Nom; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-offset-3 col-md-3 col-xs-12">
                            <label for="persoClasse">Classe</label>
                            <select name="persoClasse" class="form-control" disabled="disabled">
                                <?php foreach($pilotClasses as $classe): ?>
                                    <option value="<?= $classe->Code; ?>">
                                        <?= $classe->Nom; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php echo form_open('personnages/createNewPersonnage/4/' .$indiv->Id,array(
                        'method' => 'post'
                    )); ?>
                    <div class="row">
                        <div class="form-group col-md-offset-3 col-md-3 col-xs-12">
                            <label for="persoArchetype">Archétype</label>
                            <select name="persoArchetype" class="form-control">
                                <?php foreach($pilotArchetypes as $archetype): ?>
                                    <option value="<?= $archetype->Code; ?>">
                                        <?= $archetype->Nom; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-offset-3 col-md-3 col-xs-12">
                            <label for="persoReligion">Religion</label>
                            <select name="persoReligion" class="form-control">
                                <?php foreach($pilotReligions as $religion): ?>
                                    <option value="<?= $religion->Code; ?>">
                                        <?= $religion->Nom; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-offset-3 col-md-3 col-xs-12">
                            <label for="persoProvenance">Provenance</label>
                            <select name="persoProvenance" class="form-control">
                                <?php foreach($pilotProvenances as $provenance): ?>
                                    <option value="<?= $provenance->Nom; ?>">
                                        <?= $provenance->Nom; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-offset-3 col-md-3 col-xs-12">
                            <label for="persoAllegeance">Allégeance</label>
                            <select name="persoAllegeance" class="form-control">
                                <?php foreach($pilotProvenances as $allegeance): ?>
                                    <option value="<?= $allegeance->Nom; ?>">
                                        <?= $allegeance->Nom; ?>
                                    </option>
                                <?php endforeach; ?>
                                <option value="Bandit">Bandit</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6 col-md-3 col-md-offset-3 text-center">
                            <button class="btn btn-primary btn-lg">Étape suivante</button>
                            <?php echo form_close(); ?>  
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif($step == 4): ?>
            <div class="row">
                
            </div>
            <div class="row">
                
            </div>
            <pre><?= var_dump($_SESSION); ?> </pre>
            <pre><?= var_dump($startingSpecSkills)?></pre>
            <pre><?= var_dump($startingRegularSkills)?></pre>
        <?php endif; ?>
    </div>
</div>