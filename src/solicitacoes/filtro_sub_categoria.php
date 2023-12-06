<?php
include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");
?>
<option value="">:: Selecione a sub categoria ::</option>
<?php
$q = "select * from sub_categorias where categoria = '{$_POST['categoria']}' order by nome asc";
$r = mysqli_query($con, $q);
while($s = mysqli_fetch_object($r)){
?>
<option value="<?=$s->codigo?>"><?=$s->nome?></option>
<?php
}
?>