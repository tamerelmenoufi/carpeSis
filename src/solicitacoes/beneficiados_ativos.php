<?php
include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");


if($_POST['del']){
    $key = array_search($_POST['del'], $_SESSION['lista_ativa']);

    $query = "update servicos set deletado = '1' where 
                                    beneficiado = '{$_SESSION['lista_ativa'][$key]}' and
                                    categoria = '{$_SESSION['categoria']}' and
                                    especialidade = '{$_SESSION['especialidade']}' and
                                    situacao = '0' and
                                    deletado != '1'";
    mysqli_query($con, $query);

    unset($_SESSION['lista_ativa'][$key]);
}

?>
<style>
    .listaB{
        padding:5px;
        border-radius:5px;
        font-size:10px;
        margin:5px;
        width:auto;
        position:relative;
        background-color:#b6effb;
        float:left;
    }
    .apagarB{
        cursor:pointer;
        color:red;
        margin-left:10px;
    }
    

</style>
<?php
if($_SESSION['lista_ativa']){

    $query = "select * from beneficiados where codigo in (".implode(", ", $_SESSION['lista_ativa']).")";
    $result = mysqli_query($con, $query);
    while($d = mysqli_fetch_object($result)){
?>
<div class="listaB">
    <span><?=$d->nome?></span>
    <?php
    if(!$_SESSION['ativo_permanente'][$d->codigo]){
    ?>
    <i class="fa fa-close apagarB" cod="<?=$d->codigo?>"></i>
    <?php
    }
    ?>
</div>
<?php
    }
}
?>

<script>
    $(function(){

        $(".apagarB").click(function(){
            del = $(this).attr("cod");
            obj = $(this).parent("div");
            $.ajax({
                url:"src/solicitacoes/beneficiados.php",
                type:"POST",
                data:{
                    del
                },
                success:function(dados){
                    obj.remove();
                    $.ajax({
                        url:"src/solicitacoes/index.php",
                        type:"POST",
                        success:function(dados){
                            $("#pageHome").html(dados);
                            // let myOffCanvas = document.getElementById('offcanvasRight');
                            // let openedCanvas = bootstrap.Offcanvas.getInstance(myOffCanvas);
                            // openedCanvas.hide();
                        }
                    });
                }
            })
        })

    })
</script>