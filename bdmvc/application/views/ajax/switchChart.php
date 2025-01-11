<div class="col-md-8 col-xs-12">
    <div id="chart">                        
    </div>
</div>

<script>
    new Morris.Bar({
        element : 'chart',
        data : <?php echo json_encode($presencesCount['heures']); ?>,
        xkey : 'heures',
        ykeys : ['nombrePresences'],
        labels : ['nombrePresences']
    });

    $('h3').html('<?php echo 'Présence par heure de ' . $presencesCount['activite']->Nom . '.'; ?>')
</script>

<div class="col-xs-12">
    <h3>Personnages par...</h3>
    <div class="col-xs-12 col-md-4 text-center">
        <h4>Race</h4>
        <div id="donut_races">
        </div>

        <script>
            var arrRaces = <?php echo json_encode($racesCount); ?>;
            new Morris.Donut({
                element: 'donut_races',
                data : arrRaces,
                resize :  true,
            });
        </script>
    </div>
    <div class="col-xs-12 col-md-4 text-center">
        <h4>Classe</h4>
        <div id="donut_classes">
        </div>

        <script>
            var arrClasses = <?php echo json_encode($classesCount); ?>;
            new Morris.Donut({
                element: 'donut_classes',
                data : arrClasses,
                resize :  true,
            });
        </script>
    </div>
    <div class="col-xs-12 col-md-4 text-center">
        <h4>Religion</h4>
        <div id="donut_religions">
        </div>

        <script>
            var arrReligion = <?php echo json_encode($religionCount); ?>;
            new Morris.Donut({
                element: 'donut_religions',
                data : arrReligion,
                resize :  true,
            });
        </script>
    </div>
</div>