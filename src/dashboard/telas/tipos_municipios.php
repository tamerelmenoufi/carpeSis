<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");


    $query = "SELECT 
        (SELECT count(*) FROM servicos a left join beneficiados b on a.beneficiado = b.codigo left join municipios c on b.municipio = c.codigo where a.beneficiado > 0 and a.deletado != '1' and c.codigo = 1 group by c.codigo) as capital, 
        (SELECT count(*) FROM servicos a left join beneficiados b on a.beneficiado = b.codigo left join municipios c on b.municipio = c.codigo where a.beneficiado > 0 and a.deletado != '1' and c.codigo != 1 group by c.codigo) as interior 
    ";
    $result = mysqli_query($con, $query);
    $d = mysqli_fetch_object($result);
?>
<div class="row">
    <?php
        $painel = [
            [
                'rotulo' => 'Capital',
                'quantidade' => $d->capital,
                'cor' => 'secondary',
            ],
            [
                'rotulo' => 'Interior',
                'quantidade' => $d->interior,
                'cor' => 'success',
            ],
        ];

        foreach($painel as $ind => $val){
    ?>
    <div class="col-md-6">
        <div class="alert alert-<?=$val['cor']?> text-center" role="alert">
            <div class="d-flex justify-content-between">
                <span><?=$val['rotulo']?></span>
                <span><b><?=$val['quantidade']?></b></span>
            </div>


        </div>
    </div>
    <?php
        }
    ?>
</div>
<script>
    $(function(){
        Carregando('none');

    })
</script>