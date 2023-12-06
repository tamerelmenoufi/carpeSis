<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");


    // $query = "SELECT 
    //                 count(*) as qt, 
    //                 c.codigo,
    //                 d.tipo as zonas
    //             FROM servicos a 
    //                 left join beneficiados b on a.beneficiado = b.codigo 
    //                 left join municipios c on b.municipio = c.codigo 
    //                 left join bairros d on b.bairro = d.codigo 
    //             where 
    //                 a.beneficiado > 0 and 
    //                 c.codigo != 1 and 
    //                 a.deletado != '1' 
    //             group by c.codigo d.tipo";

    // $result = mysqli_query($con, $query);
    // while($d = mysqli_fetch_object($result)){
    //     $r[$d->codigo][$d->zonas] = ($d->qt);
    // }

?>
<style>
    .calha{
        font-size:11px;
        color:#a1a1a1;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <ul class="list-group">
        <?php

            $query = "select * from municipios where codigo != '1' group by nome";
            $result = mysqli_query($con, $query);

            while($d = mysqli_fetch_object($result)){
        ?>
            <li class="list-group-item">
                <div class="row">
                    <div class="col-md-5">
                        <div class="d-flex justify-content-between">
                            <span><?=$d->nome?> <small class="calha"><?=$d->calha_rio?></small></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex justify-content-between">
                            <span class="mb-2">
                                <span class="badge text-bg-warning"><b><?=$r[$d->codigo]['Urbano']?></b> Urbano</span>
                                <span class="badge text-bg-success"><b><?=$r[$d->codigo]['Rural']?></b> Rural</span>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-4">

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
        </ul>
    </div>
</div>
<script>
    $(function(){
        Carregando('none');

    })
</script>