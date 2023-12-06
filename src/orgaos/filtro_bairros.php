<?php
include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");
?>
<option value="">:: Selecione o Bairro ::</option>
<?php
$q = "select * from bairros where municipio = '{$_POST['cidade']}' order by nome asc";
$r = mysqli_query($con, $q);
while($s = mysqli_fetch_object($r)){
?>
<option value="<?=$s->codigo?>" <?=(($d->bairro == $s->codigo)?'selected':false)?>><?=$s->nome?></option>
<?php
}
?>