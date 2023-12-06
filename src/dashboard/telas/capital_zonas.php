<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");


    $query = "SELECT count(*) as qt, zona FROM servicos a left join beneficiados b on a.beneficiado = b.codigo left join bairros c on b.bairro = c.codigo where a.beneficiado > 0 and c.municipio = 1 and a.deletado != '1' group by c.tipo";
    $result = mysqli_query($con, $query);
    while($d = mysqli_fetch_object($result)){
        $r[$d->zona] = $d->qt;
    }


?>
<div class="row">
    <div class="col-md-12">
        <ul class="list-group">
        <?php
            $painel = [
                [
                    'rotulo' => 'Norte',
                    'quantidade' => $r['Norte'],
                ],
                [
                    'rotulo' => 'Leste',
                    'quantidade' => $r['Leste'],
                ],
                [
                    'rotulo' => 'Sul',
                    'quantidade' => $r['Sul'],
                ],
                [
                    'rotulo' => 'Oeste',
                    'quantidade' => $r['Oeste'],
                ],
                [
                    'rotulo' => 'Centro-Sul',
                    'quantidade' => $r['Centro-Sul'],
                ],
                [
                    'rotulo' => 'Centro-Oeste',
                    'quantidade' => $r['Centro-Oeste'],
                ],
            ];

            foreach($painel as $ind => $val){
        ?>
            <li class="list-group-item list-group-item-warning">
                <div class="row">
                    <div class="col">
                        <div class="d-flex justify-content-between">
                            <span><?=$val['rotulo']?></span>
                            <span><b><?=(($val['quantidade'])?:0)?></b></span>
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
            <li class="list-group-item list-group-item-success">
                <div class="row">
                    <div class="col">
                        <div class="d-flex justify-content-between">
                        <span>Rural</span>
                        <span><b><?=(($r['Rural'])?:0)?></b></span>
                    </div>
                    </div>
                    <div class="col">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped bg-success" role="progressbar" aria-label="Segment one" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">15%</div>
                        <div class="progress-bar progress-bar-striped bg-danger" role="progressbar" aria-label="Segment two" style="width: 30%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100">30%</div>
                        <div class="progress-bar progress-bar-striped bg-warning" role="progressbar" aria-label="Segment three" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">20%</div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
<script>
    $(function(){
        Carregando('none');

    })
</script>