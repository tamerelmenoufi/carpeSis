<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");


    $query = "SELECT 
                    count(*) as qt, 
                    calha_rio, 
                    (SELECT 
                            count(*) 
                        from servicos d 
                        left join beneficiados e on d.beneficiado = e.codigo
                        left join bairros f on e.municipio = f.municipio
                    where 
                        f.municipio != 1 and 
                        d.deletado != '1' and 
                        f.tipo = 'Urbano') as urbano, 
                        
                        (SELECT 
                            count(*) 
                        from servicos g 
                        left join beneficiados h on g.beneficiado = h.codigo
                        left join bairros i on h.municipio = i.municipio
                    where 
                        i.municipio != 1 and 
                        g.deletado != '1' and 
                        i.tipo = 'Rural') as rural

                FROM servicos a 
                    left join beneficiados b on a.beneficiado = b.codigo 
                    left join municipios c on b.municipio = c.codigo 
                where 
                    a.beneficiado > 0 and 
                    c.codigo != 1 and 
                    a.deletado != '1' 
                group by c.calha_rio";

    $result = mysqli_query($con, $query);
    while($d = mysqli_fetch_object($result)){
        $r[$d->calha_rio] = ($d->qt);
        $r['Urbano'] = ($d->urbano);
        $r['Rural'] = ($d->rural);
    }

?>
<div class="row">
    <div class="col-md-8">
        <ul class="list-group">
        <?php

            $query = "select * from municipios group by calha_rio order by calha_rio";
            $result = mysqli_query($con, $query);

            while($d = mysqli_fetch_object($result)){
        ?>
            <li class="list-group-item list-group-item-info">
                <div class="row">
                    <div class="col">
                        <div class="d-flex justify-content-between">
                            <span><?=$d->calha_rio?></span>
                            <span><b><?=(($r[$d->calha_rio])?:0)?></b></span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped bg-success" role="progressbar" aria-label="Segment one" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">15%</div>
                            <div class="progress-bar progress-bar-striped bg-danger" role="progressbar" aria-label="Segment two" style="width: 30%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100">30%</div>
                            <div class="progress-bar progress-bar-striped bg-warning" role="progressbar" aria-label="Segment three" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">20%</div>
                        </div>
                    </div>
                </div>
            </li>

        <?php
            }
        ?>
            <li class="list-group-item list-group-item-warning">
                <div class="row">
                    <div class="col">
                        <div class="d-flex justify-content-between">
                            <span>Urbano</span>
                            <span><b><?=(($r['Urbano'])?:0)?></b></span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped bg-success" role="progressbar" aria-label="Segment one" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">15%</div>
                            <div class="progress-bar progress-bar-striped bg-danger" role="progressbar" aria-label="Segment two" style="width: 30%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100">30%</div>
                            <div class="progress-bar progress-bar-striped bg-warning" role="progressbar" aria-label="Segment three" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">20%</div>
                        </div>
                    </div>
                </div>
            </li>
            <li class="list-group-item list-group-item-success">
                <div class="row">
                    <div class="col">
                        <div class="d-flex justify-content-between">
                            <span>Rural</span>
                            <span><b><?=(($r['Ruarl'])?:0)?></b></span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped bg-success" role="progressbar" aria-label="Segment one" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">15%</div>
                            <div class="progress-bar progress-bar-striped bg-danger" role="progressbar" aria-label="Segment two" style="width: 30%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100">30%</div>
                            <div class="progress-bar progress-bar-striped bg-warning" role="progressbar" aria-label="Segment three" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">20%</div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="col-md-4">
        <!-- <div style="margin:10px; width:70%; height:70%;"> -->
        <!-- <center> -->
            <canvas id="Tipos<?= $md5 ?>"></canvas>
        <!-- </center> -->
        <!-- </div> -->
    </div>
</div>
<script>
    $(function(){
        Carregando('none');

    })



    <?php

    $Rotulos = ['Urbano','Rural'];
    $Quantidade = [70, 30];
    $R = (($Rotulos)?"'".implode("','",$Rotulos)."'":0);
    $Q = (($Quantidade)?implode(",",$Quantidade):0);

?>

    const TiposCtx<?=$md5?> = document.getElementById('Tipos<?=$md5?>');

    const Tipos<?=$md5?> = new Chart(TiposCtx<?=$md5?>,
        {
            type: 'pie',
            data: {
                labels: [<?=$R?>],
                datasets: [{
                    label: [<?=$R?>],
                    data: [<?=$Q?>],
                    backgroundColor: [
                        'rgb(75, 192, 192, 0.2)',
                        'rgb(255, 159, 64, 0.2)',
                                            ],
                    borderColor: [
                        'rgb(75, 192, 192, 1)',
                        'rgb(255, 159, 64, 1)',
                    ],
                    borderWidth: 1,
                    rotulos: [<?=$R?>]
                }]
            },
            options:{
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Solicitações por Zonas'
                    }
                }
            }
        }
    );

</script>