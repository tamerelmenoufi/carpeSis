<?php
include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");
?>
<option value="">:: Selecione a Especialidade ::</option>
<?php
$q = "select * from especialidades where categoria = '{$_POST['categoria']}' and sub_categoria = '{$_POST['sub_categoria']}' order by nome asc";
$r = mysqli_query($con, $q);
while($s = mysqli_fetch_object($r)){
?>
<option value="<?=$s->codigo?>" <?=(($_POST['especialidade'] == $s->codigo)?'selected':false)?>><?=$s->nome?></option>
<?php
}
?>