<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <h1 class="page-header">Écrire le courriel</h1>
        </div>        
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->  
    <div class="row">   
        <div class="col-md-6 col-xs-12">
            <h3><label for="">Destinataires</label></h3>
            <?php if(!empty($destinataires)) : ?>
                <p id="destList">
                    <?php 
                    $backStr = '';
                    foreach ( $destinataires as $key => $dest ) :
                        $backStr .= $dest .',';
                        echo '<span>' .$dest .'<a href="" class="smallClose"><span class="fa fa-close"></span></a>';
                        if($key != (count($destinataires)-1)):
                            echo ' , </span>';
                        else :
                            echo '</span>';
                        endif;
                    endforeach; 
                    ?>
                </p>
                 <a href="<?php echo site_url('Courriel/index/?dest=') .$backStr; ?>">
                    <h4><span class="fa fa-chevron-left"></span>&nbsp Ajouter des destinataires</h4>
                </a>
            <?php else : ?>
                <h4>Vous n'avez sélectionné aucun destinataire</h4>
                 <a href="<?php echo site_url('Courriel') ?>">
                    <h4><span class="fa fa-chevron-left"></span>&nbsp Ajouter des destinataires</h4>
                </a>
            <?php endif; ?>

             <div class="form-group col-md-6 col-xs-12">
                <h3><label for="sujet">Sujet :</label></h3>
                <input type="text" name="sujet" id="sujet" class="form-control" placeholder="Sujet">
            </div>
            <div class="form-group col-xs-12">
                <h3><label for="texte">Texte :</label></h3>
                <textarea name="texte" id="texte" class="form-control" placeholder="Texte" rows="15"></textarea>
            </div>
            <div class="form-group col-md-6 col-xs-12">
                <h3><label for="signature">Signature :</label></h3>
                <select name="signature" class="form-control" id="signature">
                    <option value="L'équipe d'animation,<br>Les Terres de Bélénos">Équipe d'animation</option>
                    <option value="Signature2">Signature2</option>
                    <option value="Signature3">Signature3</option>
                    <option value="Signature4">Signature4</option>
                </select>
            </div>
        </div>
        <h4>Aperçu</h4>
        <div class="col-md-6 col-xs-12" style="background-image: url('https://www.toptal.com/designers/subtlepatterns/patterns/vintage-concrete.png');">
            
            <div style="width:600px; margin: 0 auto">
                <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td id="emailHeader"><img src="http://www.terres-de-belenos.com/wp-content/images/logo_belenos.png" alt="logo" style="height:200px; width:auto;"></td>
                    </tr>
                    <tr>
                        <td id="emailText" style="padding-top: 25px; padding-bottom: 15px;"></td>
                    </tr>
                    <tr>
                        <td id="emailFooter"><br><br></td>
                    </tr>
                </table>
            </div>
        </div>
    </div> 
    <div class="row">
        <a href="#" id="sendEmail"><button class="btn btn-success">Envoyer <span class="fa fa-fa-paper-plane"></span></button></a>
    </div>
</div>
<!-- /.container-fluid -->

<script>
    $('#texte').summernote({
        toolbar : [
            ['style',['bold','italic','underline','clear']],
            ['para',['ul','ol','paragraph']],
            ['fontsize',['fontsize']],
            ['insert',['link','table','picture','video','hr']],
            ['misc',['codeview']]
        ],
    });

    $('.note-image-input').attr('disabled','disabled');

    $('.panel-body').delay(500).on('keyup',function(){
            $('#emailText').html( $('.panel-body').html());
        });
</script>

<script>
    $(function(){
        $('.smallClose').click(function(e){
            e.preventDefault();

            $(this).parent('span').remove();
        })

        $('#emailFooter').html('-----<br><br>' + $('#signature :selected').val());

        $('#signature').delay(500).on('change',function(){
            $('#emailFooter').html('-----<br><br>' + $('#signature :selected').val());
        });
    })

    $('#sendEmail').click(function(e){
        e.preventDefault();

        var dest = $('#destList').text(),
            sujet = $('#sujet').val(),
            texte = $('.panel-body').html(),
            signature = $('#signature :selected').val(),
            controller = 'Courriel',
            base_url = '<?php echo site_url();?>', 
            data;

        $.ajax({
            'url' : base_url + controller + '/sendEmail',
            'type': 'POST',
            'data': {
                'destinataires': dest.trim(),
                'sujet': sujet,
                'texte': texte,
                'signature': signature
            },
            'success': function(data){
                $('#page-wrapper').html(data);
            },
            'error': function(err){
                console.log(err);
            }
        });
    })
</script>