<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");
?>
<div class="row">
    <div class="col-md-12">
        <ul class="list-group">
        <?php

            $query = "select a.*, (select count(*) from servicos where orgao = a.codigo) as qt from orgaos a group by a.esfera order by a.esfera";
            $result = mysqli_query($con, $query);

            while($d = mysqli_fetch_object($result)){
        ?>
            <li class="list-group-item">
                <div class="row">
                    <div class="col">
                        <div class="d-flex justify-content-between">
                            <span><?=$d->esfera?></span>
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