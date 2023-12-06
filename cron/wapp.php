<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");

    $query = "select *, (select count(*) from wapp where situacao = '1') as limite from wapp where situacao = '0' order by data asc limit 100";
    $result = mysqli_query($con, $query);
    $tot = 0;
    $blq = [];
    $env = [];
    $sta = [];
    $lm = 1000;
    while($d = mysqli_fetch_object($result)){
        set_time_limit(90);
        if($d->limite < $lm and ($tot + $d->limite) < $lm){
            $tot++;
            $env[] = ['telefone' => $d->telefone, 'mensagem' => $d->mensagem];
            $sta[] = $d->codigo;
            // EnviarWapp('92991886570', 'Enviando mensagem pelo sistema Cerebro');
        }else{
            $blq[] = $d->codigo;
        }
    }

    if($sta){
        echo $q = "update wapp set situacao = '1' where codigo in (".implode(', ', $sta).")";
        mysqli_query($con,$q);
        echo "<hr>";
    }
    if($blq){
        echo $q = "update wapp set situacao = '2' where codigo in (".implode(', ', $blq).")";
        mysqli_query($con,$q);
        echo "<hr>";
    }

    foreach($env as $i => $d){
        // EnviarWapp($d['telefone'], "*Sistema CÉREBRO Informa:* ".$d['mensagem']);
        echo "EnviarWapp('{$d['telefone']}', '*Sistema CÉREBRO Informa:* {$d['mensagem']}')";
        echo "<hr>";
    }



    