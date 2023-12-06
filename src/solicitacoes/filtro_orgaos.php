<?php
include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");
?>
<option value="">:: Selecione o Órgão ::</option>
<?php
$q = "select * from orgaos where
            categoria->>'$.cat{$_POST['categoria']}' = '{$_POST['categoria']}' and
            especialidade->>'$.esp{$_POST['especialidade']}' = '{$_POST['especialidade']}'
        order by nome asc";
$r = mysqli_query($con, $q);
while($s = mysqli_fetch_object($r)){
?>
<option value="<?=$s->codigo?>"><?=$s->nome.' - '.$s->esfera?></option>
<?php
}
?>