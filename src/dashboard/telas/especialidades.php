<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");
?>
<style>
    .categoria{
        font-size:11px;
        color:#a1a1a1;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <ul class="list-group">
        <?php

            $query = "select 
                            a.*, 
                            b.nome as categoria_nome,
                            (select count(*) from servicos where especialidade = a.codigo) as qt
                            
                    from especialidades a left join categorias b on a.categoria = b.codigo where a.situacao = '1' and a.deletado != '1' order by a.nome";
            $result = mysqli_query($con, $query);

            while($d = mysqli_fetch_object($result)){
        ?>
            <li class="list-group-item">
                <div class="row">
                    <div class="col">
                        <div class="d-flex justify-content-between">
                            <span><?=$d->nome?> <small class="categoria"><?=$d->categoria_nome?></small></span>
                            <span><?=$d->qt?></span>
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
        </ul>
    </div>
</div>
<script>
    $(function(){
        Carregando('none');

    })
</script>