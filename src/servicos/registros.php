<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");

    Verifica([$_SESSION['ProjectPainel']->codigo]);

    if($_POST['servico']) $_SESSION['servico'] = $_POST['servico'];
    if($_POST['origem']) $_SESSION['origem'] = $_POST['origem'];

    


    if($_POST['acao'] == 'situacao'){

        $attr[] = "beneficiado = '" . $_SESSION['beneficiado'] . "'";
        $attr[] = "servico = '" . $_SESSION['servico'] . "'";
        $attr[] = "usuario = '" . $_SESSION['ProjectPainel']->codigo . "'";
        $attr[] = "usuario_perfil = '" . $_SESSION['ProjectPainel']->perfil . "'";
        $attr[] = "descricao = 'Solicitação finalizada e confirmada.'";
        $attr = implode(', ', $attr);
        $query = "insert into servicos_registros set data = NOW(), {$attr}";
        mysqli_query($con, $query);

        $query = "update servicos set situacao = '1', data_finalizacao = NOW() where codigo = '{$_SESSION['servico']}'";
        mysqli_query($con, $query);

    }


    if($_POST['acao'] == 'negado'){

        $attr[] = "beneficiado = '" . $_SESSION['beneficiado'] . "'";
        $attr[] = "servico = '" . $_SESSION['servico'] . "'";
        $attr[] = "usuario = '" . $_SESSION['ProjectPainel']->codigo . "'";
        $attr[] = "usuario_perfil = '" . $_SESSION['ProjectPainel']->perfil . "'";
        $attr[] = "descricao = 'Solicitação finalizada e negada.'";
        $attr = implode(', ', $attr);
        $query = "insert into servicos_registros set data = NOW(), {$attr}";
        mysqli_query($con, $query);

        $query = "update servicos set situacao = '2', data_finalizacao = NOW() where codigo = '{$_SESSION['servico']}'";
        mysqli_query($con, $query);

    }


    if($_POST['acao'] == 'anexo'){

        if($_POST['name'] and $_POST['type'] and $_POST['Base64']){
            $img = base64_decode(str_replace("data:{$_POST['type']};base64,", false, $_POST['Base64']));
            if(!is_dir("anexos")) mkdir("anexos");
            if(!is_dir("anexos/{$_SESSION['servico']}")) mkdir("anexos/{$_SESSION['servico']}");
            $ext = substr($_POST['name'],strrpos($_POST['name'],'.'), strlen($_POST['name']));
            $nome = md5("{$_SESSION['servico']}{$_POST['name']}{$_POST['type']}".date("YmdHis"))."{$ext}";
            file_put_contents("anexos/{$_SESSION['servico']}/{$nome}", $img);
        }

        $attr[] = "beneficiado = '" . $_SESSION['beneficiado'] . "'";
        $attr[] = "servico = '" . $_SESSION['servico'] . "'";
        $attr[] = "usuario = '" . $_SESSION['ProjectPainel']->codigo . "'";
        $attr[] = "usuario_perfil = '" . $_SESSION['ProjectPainel']->perfil . "'";
        $attr[] = "anexo = 'anexos/{$_SESSION['servico']}/{$nome}'";
        $attr[] = "anexo_tipo = '{$_POST['anexo_tipo']}'";
        $attr[] = "descricao = '" . $_POST['name'] . "'";

        $attr = implode(', ', $attr);

        $query = "insert into servicos_registros set data = NOW(), {$attr}";
        mysqli_query($con, $query);
        // exit();

        $attr = [];

        if($_SESSION['ProjectPainel']->perfil == 'assessor2'){
        $attr[] = "assessor2 = JSON_SET(if(assessor2 > 0,assessor2,'{}'), '$.ass{$_SESSION['ProjectPainel']->codigo}', {$_SESSION['ProjectPainel']->codigo})";
        }else if($_SESSION['ProjectPainel']->perfil == 'assessor3'){
        $attr[] = "assessor3 = JSON_SET(if(assessor3 > 0,assessor3,'{}'), '$.ass{$_SESSION['ProjectPainel']->codigo}', {$_SESSION['ProjectPainel']->codigo})";
        }
        ////////////
        if($attr){
            $attr = implode(', ', $attr);
            $query = "update servicos set {$attr} where codigo = '{$_SESSION['servico']}'";
            mysqli_query($con, $query);
        }

    }


    if($_POST['acao'] == 'salvar'){

        $data = $_POST;
        $attr = [];

        unset($data['acao']);

        foreach ($data as $name => $value) {
            $attr[] = "{$name} = '" . addslashes($value) . "'";
        }
            $attr[] = "beneficiado = '" . $_SESSION['beneficiado'] . "'";
            $attr[] = "servico = '" . $_SESSION['servico'] . "'";
            $attr[] = "usuario_perfil = '" . $_SESSION['ProjectPainel']->perfil . "'";
            $attr[] = "usuario = '" . $_SESSION['ProjectPainel']->codigo . "'";

        $attr = implode(', ', $attr);

        $query = "insert into servicos_registros set data = NOW(), {$attr}";
        mysqli_query($con, $query);

        $attr = [];

        if($_SESSION['ProjectPainel']->perfil == 'assessor2'){
        $attr[] = "assessor2 = JSON_SET(if(assessor2 > 0,assessor2,'{}'), '$.ass{$_SESSION['ProjectPainel']->codigo}', {$_SESSION['ProjectPainel']->codigo})";
        }else if($_SESSION['ProjectPainel']->perfil == 'assessor3'){
        $attr[] = "assessor3 = JSON_SET(if(assessor3 > 0,assessor3,'{}'), '$.ass{$_SESSION['ProjectPainel']->codigo}', {$_SESSION['ProjectPainel']->codigo})";
        }

        if($attr){
            $attr = implode(', ', $attr);
            $query = "update servicos set {$attr} where codigo = '{$_SESSION['servico']}'";
            mysqli_query($con, $query);
        }


    }

    $query = "select
                    a.*,
                    b.nome as beneficiado_nome,
                    c.nome as categoria_nome,
                    d.nome as especialidade_nome,
                    e.nome as orgao_nome,
                    e.esfera
                from
                    servicos a
                    left join beneficiados b on a.beneficiado = b.codigo
                    left join categorias c on a.categoria = c.codigo
                    left join especialidades d on a.especialidade = d.codigo
                    left join orgaos e on a.orgao = e.codigo
                where a.codigo = '{$_SESSION['servico']}'";
    $result = mysqli_query($con, $query);
    $d = mysqli_fetch_object($result);
?>
<style>
    .Titulo<?=$md5?>{
        position:absolute;
        left:60px;
        top:8px;
        z-index:0;
    }

    .MudaSituacao<?=$md5?>{
        position:absolute;
        right:30px;
        top:15px;
        z-index:0;
    }
    .Cabecalho<?=$md5?>{
        position:absolute;
        left:0px;
        right:0px;
        top:60px;
        height:60px;
        z-index:1;
        /* border:solid 1px yellow; */
    }
    .Corpo<?=$md5?>{
        position:absolute;
        left:0px;
        right:0px;
        top:120px;
        bottom:80px;
        /* border:solid 1px red; */
        z-index:0;
        overflow-x: hidden;
        overflow-y: auto;
    }
    .Rodape<?=$md5?>{
        position:absolute;
        left:0px;
        right:0px;
        bottom:0px;
        height:80px;
        /* border:solid 1px green; */
        z-index:0;
    }
    .dados_solicitacao label{
        font-size:10px;
        color:#a1a1a1;
        padding:0;
        margin:0;
    }
    .dados_solicitacao div{
        font-size:12px;
        /* color:#333; */
        padding:0;
        margin:0;
    }
    .nome_imagem {
        white-space: nowrap;
        width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        background-color:#eee;
        text-decoration:none;
    }
    .mudar_ststus{
        cursor:pointer;
    }
</style>
<h4 class="Titulo<?=$md5?>">Registros da Solicitação</h4>
<?php
if(
    $_SESSION['ProjectPainel']->codigo == 1 or
    $_SESSION['ProjectPainel']->perfil == 'adm' or
    $_SESSION['ProjectPainel']->perfil == 'assessor3'
){
?>
<div class="MudaSituacao<?=$md5?>">
    <div class="form-check form-switch">
        <?php
        if($d->situacao == '1'){
        ?>
        <span class="text-success"><small class="alert alert-success m-0 p-1"><i class="fa-solid fa-toggle-on"></i> Atendido</small></span>
        <?php
        }elseif($d->situacao == '2'){
        ?>
        <span class="text-danger"><small class="alert alert-danger m-0 p-1"><i class="fa-solid fa-toggle-on"></i> Negado</small></span>
        <?php
        }else{
        ?>
        <span class="text-warning mudar_ststus" opc="atendido"><small class="alert alert-warning m-0 p-1"><i class="fa-solid fa-toggle-on"></i> Finalizar</small></span>
        <span class="text-danger mudar_ststus" opc="negado"><small class="alert alert-danger m-0 p-1"><i class="fa-solid fa-toggle-on"></i> Negar</small></span>
        <?php
        }
        ?>
    </div>
</div>
<?php
}
?>
    <div class="Cabecalho<?=$md5?>">
        <div class="accordion ms-2 me-1 mb-3" id="accordionPanelsStayOpenExample">
            <div class="accordion-item">
                <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="false" aria-controls="panelsStayOpen-collapseOne">
                    Detalhes da Solicitação
                </button>
                </h2>
                <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingOne">
                <div class="accordion-body" style="overflow-x:none; overflow-y:auto;">

                    <div class="dados_solicitacao">
                        <label>Beneficiado</label>
                        <div><?=$d->beneficiado_nome?></div>
                    </div>
                    <div class="dados_solicitacao">
                        <label>Categoria</label>
                        <div><?=$d->categoria_nome?></div>
                    </div>
                    <div class="dados_solicitacao">
                        <label>Especialidade</label>
                        <div><?=$d->especialidade_nome?></div>
                    </div>
                    <div class="dados_solicitacao">
                        <label>Órgao - Esfera</label>
                        <div><?=$d->orgao_nome?> - <?=$d->esfera?></div>
                    </div>
                    <div class="dados_solicitacao">
                        <label>Descrição detalhada do pedido</label>
                        <div><?=str_replace("\n","<br>", $d->detalhes)?></div>
                    </div>

                </div>
                </div>
            </div>
        </div>
    </div>
    <div class="Corpo<?=$md5?>" id="Corpo<?=$md5?>">
        <div class="row p-2">
            <div class="col">

                <?php
                $q = "select a.*, b.nome as usuario_nome from servicos_registros a left join usuarios b on a.usuario = b.codigo where a.servico = '{$_SESSION['servico']}' order by a.data asc";
                $r = mysqli_query($con, $q);
                while($s = mysqli_fetch_object($r)){
                    if(!$s->anexo_tipo){
                ?>
                <div class="alert alert-secondary" role="alert">
                    <?=$s->descricao?>
                    <div class="d-flex justify-content-between fs-6" style="font-size:10px!important; margin-top:15px; margin-bottom:-15px;">
                        <span><?=$s->usuario_nome?></span>
                        <span><?=dataBr($s->data)?></span>
                    </div>
                </div>
                <?php
                    }elseif($s->anexo_tipo == 'doc'){
                ?>
                <div class="alert alert-secondary" role="alert">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-paperclip"></i></span>
                        <a class="form-control nome_imagem" href="src/servicos/<?=$s->anexo?>" target="_blank"><?=$s->descricao?></a>
                    </div>
                    <div class="d-flex justify-content-between fs-6" style="font-size:10px!important; margin-top:15px; margin-bottom:-15px;">
                        <span><?=$s->usuario_nome?></span>
                        <span><?=dataBr($s->data)?></span>
                    </div>
                </div>
                <?php
                    }elseif($s->anexo_tipo == 'img'){
                ?>
                <div class="alert alert-secondary" role="alert">
                    <a class="form-control nome_imagem" href="src/servicos/<?=$s->anexo?>" target="_blank">
                        <img src="src/servicos/<?=$s->anexo?>" style="max-width:100%;" />
                    </a>
                    <!-- <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-paperclip"></i></span>
                        <a class="form-control nome_imagem" href="src/servicos/<?=$s->anexo?>" target="_blank"><?=$s->descricao?></a>
                    </div> -->
                    <div class="d-flex justify-content-between fs-6" style="font-size:10px!important; margin-top:15px; margin-bottom:-15px;">
                        <span><?=$s->usuario_nome?></span>
                        <span><?=dataBr($s->data)?></span>
                    </div>
                </div>
                <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <div class="Rodape<?=$md5?>">
        <form id="form-<?= $md5 ?>">
            <div class="row p-2">
                <div class="col-12">
                    <div style="display:flex; justify-content:end">
                        <textarea required <?=(($d->situacao)?'disabled':false)?> name="descricao" id="descricao" class="form-control me-2" placeholder="Digite aqui"></textarea>
                        <button type="submit" <?=(($d->situacao)?'disabled':false)?> class="btn btn-success btn-ms me-2">
                            <i class="fa-regular fa-paper-plane"></i>
                            <div><small>Enviar</small></div>
                        </button>
                        <button type="button" <?=(($d->situacao)?'disabled':false)?> class="btn btn-warning btn-ms" style="position:relative">
                            <input type="file" id="anexo" style="position:absolute; left:0; right:0; bottom:0; top:0; opacity:0" />
                            <i class="fa-solid fa-paperclip"></i>
                            <div><small>Anexar</small></div>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        $(function(){
            Carregando('none');

            var objDiv = document.getElementById("Corpo<?=$md5?>");
            objDiv.scrollTop = objDiv.scrollHeight;

            $(".accordion-body").css("max-height", $(window).height() - 200);


            if (window.File && window.FileList && window.FileReader) {

            $('input[type="file"]').change(function () {

                if ($(this).val()) {
                    Carregando();
                    var files = $(this).prop("files");
                    for (var i = 0; i < files.length; i++) {
                        (function (file) {
                            var fileReader = new FileReader();
                            fileReader.onload = function (f) {

                            //////////////////////////////////////////////////////////////////


                            EnviaAnexo = (Base64, type, name, anexo_tipo) => {
                                $.ajax({
                                    url:"src/servicos/registros.php",
                                    type:"POST",
                                    data:{
                                        acao:'anexo',
                                        Base64,
                                        type,
                                        name,
                                        anexo_tipo
                                    },
                                    success:function(dados){
                                        console.log(dados);
                                        $(".MenuRight").html(dados);

                                        $.ajax({
                                            url:"<?=$_SESSION['origem']?>",
                                            success:function(dados){
                                                $("#pageHome").html(dados);
                                            },
                                            error:function(erro){

                                                // $.alert('Ocorreu um erro!' + erro.toString());
                                                //dados de teste
                                            }
                                        });

                                    }
                                });
                            }

                            var type = file.type;
                            var name = file.name;

                            if(type.indexOf('image') === -1){
                                var Base64 = f.target.result;
                                var anexo_tipo = 'doc';
                                EnviaAnexo(Base64, type, name, anexo_tipo);

                            }else{

                                var img = new Image();
                                img.src = f.target.result;

                                img.onload = function () {

                                    // CREATE A CANVAS ELEMENT AND ASSIGN THE IMAGES TO IT.
                                    var canvas = document.createElement("canvas");

                                    var value = 50;

                                    // RESIZE THE IMAGES ONE BY ONE.
                                    w = img.width;
                                    h = img.height;
                                    img.width = 800 //(800 * 100)/img.width // (img.width * value) / 100
                                    img.height = (800 * h / w) //(img.height/100)*img.width // (img.height * value) / 100

                                    var ctx = canvas.getContext("2d");
                                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                                    canvas.width = img.width;
                                    canvas.height = img.height;
                                    ctx.drawImage(img, 0, 0, img.width, img.height);

                                    var Base64 = canvas.toDataURL(file.type); //f.target.result;
                                    var anexo_tipo = 'img';

                                    EnviaAnexo(Base64, type, name, anexo_tipo);
                                }
                            }

                            //////////////////////////////////////////////////////////////////




                            };
                            fileReader.readAsDataURL(file);
                        })(files[i]);
                    }
                }
            });
            } else {
            alert('Nao suporta HTML5');
            }



            $('#form-<?=$md5?>').submit(function (e) {

                e.preventDefault();

                var filds = $(this).serializeArray();

                filds.push({name: 'acao', value: 'salvar'})

                Carregando();

                $.ajax({
                    url:"src/servicos/registros.php",
                    type:"POST",
                    // dataType:"JSON",
                    // mimeType: 'multipart/form-data',
                    data: filds,
                    success:function(dados){
                        // console.log(dados);
                        $(".MenuRight").html(dados);
                        $.ajax({
                            url:"<?=$_SESSION['origem']?>",
                            success:function(dados){
                                $("#pageHome").html(dados);
                            },
                            error:function(erro){

                                // $.alert('Ocorreu um erro!' + erro.toString());
                                //dados de teste
                            }
                        });

                    },
                    error:function(erro){

                        // $.alert('Ocorreu um erro!' + erro.toString());
                        //dados de teste
                    }
                });

            });


            $(".mudar_ststus").click(function(){
                opc = $(this).attr("opc");
                $.confirm({
                    content:`Esta ação encerrará a solicitação e a situação será alterada para <b>${opc}</b>.<br>Deseja realmente prosseguir com a ação?`,
                    title:"Finalizar a Solicitação",
                    buttons:{
                        'SIM':function(){
                            Carregando();
                            $.ajax({
                                url:"src/servicos/registros.php",
                                type:"POST",
                                data:{
                                    acao:((opc == 'atendido')?'situacao':'negado'),
                                    opc
                                },
                                success:function(dados){
                                    $(".MenuRight").html(dados);


                                    $.ajax({
                                        url:"<?=$_SESSION['origem']?>",
                                        success:function(dados){
                                            $("#pageHome").html(dados);
                                        },
                                        error:function(erro){

                                            // $.alert('Ocorreu um erro!' + erro.toString());
                                            //dados de teste
                                        }
                                    });

                                },
                                error:function(erro){

                                    // $.alert('Ocorreu um erro!' + erro.toString());
                                    //dados de teste
                                }
                            });
                        },
                        'NÃO':function(){

                        }
                    }
                });
            });

        })
    </script>