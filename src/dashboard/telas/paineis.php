<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");
?>
<div class="row">
    <?php

        $query = "SELECT 
                        (SELECT count(*) from servicos where situacao = '1' and beneficiado > 0 and deletado != '1') as atendidos, 
                        (SELECT count(*) from servicos where situacao = '0' and beneficiado > 0 and deletado != '1') as pendentes,
                        (SELECT count(*) from orgaos where situacao = '1' and deletado != '1') as empresas,
                        (SELECT count(*) from beneficiados where situacao = '1' and deletado != '1') as beneficiados,
                        (SELECT count(*) from usuarios where situacao = '1' and deletado != '1') as assessores,
                        (SELECT count(*) from servicos where beneficiado > 0 and deletado != '1') as servicos

                ";
        $result = mysqli_query($con, $query);
        $d = mysqli_fetch_object($result);


        $painel = [
            [
                'rotulo' => 'Atendidos',
                'quantidade' => $d->atendidos,
                'cor' => 'primary',
            ],
            [
                'rotulo' => 'Pendentes',
                'quantidade' => $d->pendentes,
                'cor' => 'secondary',
            ],
            [
                'rotulo' => 'Parceiros',
                'quantidade' => $d->empresas,
                'cor' => 'success',
            ],
            [
                'rotulo' => 'Beneficiados',
                'quantidade' => $d->beneficiados,
                'cor' => 'danger',
            ],
            [
                'rotulo' => 'Equipe',
                'quantidade' => $d->assessores,
                'cor' => 'warning',
            ],
            [
                'rotulo' => 'Categorias',
                'quantidade' => $d->servicos,
                'cor' => 'info',
            ],
        ];

        foreach($painel as $ind => $val){
    ?>
    <div class="col-md-2">
        <div class="alert alert-<?=$val['cor']?> text-center" role="alert">
            <h1><?=(($val['quantidade'])?:0)?></h1>
            <span><?=$val['rotulo']?></span>
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