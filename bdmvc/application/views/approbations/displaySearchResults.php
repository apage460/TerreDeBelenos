<div class="col-xs-12">
	<div class="row">
		<h3>Nombre de Résultat : <?php echo count($searchResults); ?></h3>
	</div>
	<div class="row">
		<table class="table table-striped table-responsive">
			<tr>
				<th>Prénom Joueur</th>
				<th>Nom Joueur</th>
				<th>Date de Naissance</th>
				<th>Prénom Personnage</th>
				<th>Nom Personnage</th>
				<th>Classe</th>
				<th>Race</th>
				<th>Religion</th>
				<th>Niveau</th>
				<th></th>
			</tr>
			<?php foreach ($searchResults as $searchResult) { ?>
				<tr>
					<td><?php echo $searchResult->PrenomJoueur; ?></td>
					<td><?php echo $searchResult->NomJoueur; ?></td>
					<td><?php echo $searchResult->DDN; ?></td>
					<td><?php echo $searchResult->Prenom; ?></td>
					<td><?php echo $searchResult->Nom; ?></td>
					<td><?php echo $searchResult->CodeClasse; ?></td>
					<td><?php echo $searchResult->CodeRace; ?></td>
					<td><?php echo $searchResult->CodeReligion; ?></td>
					<td><?php echo $searchResult->Niveau; ?></td>
					<td>
						<a href="<?php echo site_url('approbations/getPersonnageCours') .'/' .$searchResult->Id ; ?> ">
							<button class="btn btn-primary"><span class="fa fa-eye fa-reverse"></span></button>
						</a>
					</td>
				</tr>
			<?php } //END FOREACH ?>
		</table>
	</div>
</div>