<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Arcanns</h1>
                <h3>Compte de <i><?php echo $user->Prenom .' ' .$user ->Nom; ?></i></h3>
            </div>
            <div class="col"><h3>En banque : <?php echo $qtyArcann->Total; ?> arcanns</h3></div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-md-6">
                <h4>Retrait</h4>
                <?php if($retrait == true): ?>
                    <h4>Retrait complété!</h4>
                <?php endif; ?>
                <?php echo  form_open('individus/retraitArcann/' . $user->Id) ?>
                <div class="form-group col-xs-6">
                    <input type="number" class="form-control" name="retrait_qty" min="1" max="<?php echo $qtyArcann->Total; ?>">
                    <br>    
                    <button class="btn btn-primary btn-lg btn-block">Retirer <span class="fa fa-money"></span></button>
                </div>
                <?php echo  form_close(); ?>
            </div>
            <div class="col-md-6">
                <h4>Transférer</h4>
            </div>
        </div>

    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->