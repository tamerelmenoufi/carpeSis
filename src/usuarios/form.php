<?php
        include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");


    if($_POST['acao'] == 'salvar'){

        $data = $_POST;
        $attr = [];

        unset($data['codigo']);
        unset($data['acao']);
        unset($data['senha']);
        unset($data['img_antigo']);
        unset($data['imagem']);


        if($_POST['imagem']){
            $img_antigo = $_POST['img_antigo'];
            if(is_file("fotos/".$img_antigo)) unlink("fotos/{$img_antigo}");
            $imagem = explode("base64,",$_POST['imagem']);
            $img = base64_decode($imagem[1]);
            if(!is_dir("fotos")) mkdir("fotos");
            $nome = md5($img);
            if(!is_dir("fotos/")) mkdir("fotos/".$nome);
            file_put_contents("fotos/{$nome}", $img);
            $attr[] = "foto = '" . $nome . "'";
        }


        if($_POST['perfil'] != 'assessor'){
            unset($data['municipio']);
            unset($data['bairro']);
            unset($data['zona']);

            $attr[] = "municipio = ''";
            $attr[] = "bairro = ''";
            $attr[] = "zona = ''";
            $attr[] = "deletado = '0'";

            mysqli_query($con, "delete from usuarios_atuacao where usuario = '{$_POST['codigo']}'");
        }

        foreach ($data as $name => $value) {
            $attr[] = "{$name} = '" . addslashes($value) . "'";
        }
        if($_POST['senha']){
            $attr[] = "senha = '" . md5($_POST['senha']) . "'";
        }
        if(!$_POST['codigo']){
            $attr[] = "responsavel_cadastro = '" . $_SESSION['ProjectPainel']->codigo . "'";
        }

        $attr = implode(', ', $attr);

        if($_POST['codigo']){
            $query = "update usuarios set {$attr} where codigo = '{$_POST['codigo']}'";
            mysqli_query($con, $query);
            $codigo = $_POST['codigo'];
        }else{
            $query = "insert into usuarios set data_cadastro = NOW(), {$attr}";
            mysqli_query($con, $query);
            $codigo = mysqli_insert_id($con);
        }

        $return = [
            'status' => true,
            'codigo' => $codigo
        ];

        echo json_encode($return);

        exit();
    }

    $query = "select * from usuarios where codigo = '{$_POST['codigo']}'";
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
    div[atuacao]{
        display:<?=(( ($d->perfil == 'assessor' or $_SESSION['ProjectPainel']->perfil == 'gestor') and $_POST['codigo'] )?'flex':'none')?>;
    }
</style>
<h4 class="Titulo<?=$md5?>">Registro de Gestores</h4>
    <form id="form-<?= $md5 ?>">
        <div class="row">
            <div class="col">
                <div foto style="margin-top:20px; margin-bottom:20px;">
                    <?php
                    if(is_file("fotos/{$d->foto}")){
                    ?>
                    <img src="src/usuarios/fotos/<?=$d->foto?>" style="max-width:100%; padding-bottom:10px;" />
                    <?php
                    }
                    ?>
                    <button class="btn btn-secondary w-100" style="position:relative">
                        <i class="fa fa-camera"></i> Inserir Foto
                        <input type="file" id="foto" img_antigo="<?=$d->foto?>" style="position:absolute; left:0; right:0; top:0; bottom:0; opacity:0" />
                    </button>
                    
                </div>
                <div class="form-floating mb-3">
                    <input type="text" required class="form-control" id="nome" name="nome" placeholder="Nome Completo" value="<?=$d->nome?>">
                    <label for="nome">Nome Completo*</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" required name="cpf" id="cpf" class="form-control" placeholder="CPF" value="<?=$d->cpf?>">
                    <label for="cpf">CPF*</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" required name="telefone" id="telefone" class="form-control" placeholder="Telefone" value="<?=$d->telefone?>">
                    <label for="telefone">Telefone*</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" name="email" id="email" class="form-control" placeholder="E-mail" value="<?=$d->email?>">
                    <label for="email">E-mail</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" required name="endereco" id="endereco" class="form-control" placeholder="Endereco Completo" value="<?=$d->endereco?>">
                    <label for="endereco">Endereco Completo</label>
                </div>
                <?php
                if( ($_SESSION['ProjectPainel']->codigo == 1 or $_SESSION['ProjectPainel']->perfil == 'adm'  or $_SESSION['ProjectPainel']->perfil == 'gestor') and $d->codigo != 1 and $_SESSION['ProjectPainel']->codigo != $d->codigo ){
                ?>
                <div class="form-floating mb-3">
                    <select required name="perfil" class="form-control" id="perfil">
                        <option value="assessor" <?=(($d->perfil == 'assessor')?'selected':false)?>>Liderança (Solicitações)</option>
                        <option value="assessor2" <?=(($_SESSION['ProjectPainel']->perfil == 'gestor')?'disabled':false)?> <?=(($d->perfil == 'assessor2')?'selected':false)?>>Atendimento (Atende as Solicitações)</option>
                        <option value="assessor3" <?=(($_SESSION['ProjectPainel']->perfil == 'gestor')?'disabled':false)?> <?=(($d->perfil == 'assessor3')?'selected':false)?>>Supervisor (Valida e Finaliza)</option>
                        <option value="assessor4" <?=(($_SESSION['ProjectPainel']->perfil == 'gestor')?'disabled':false)?> <?=(($d->perfil == 'assessor4')?'selected':false)?>>Marketing e Publicidade</option>
                        <option value="gestor" <?=(($_SESSION['ProjectPainel']->perfil == 'gestor')?'disabled':false)?> <?=(($d->perfil == 'gestor')?'selected':false)?>>Coordenador</option>
                        <option value="adm" <?=(($_SESSION['ProjectPainel']->codigo != '1')?'disabled':false)?> <?=(($d->perfil == 'adm')?'selected':false)?>>Administrador do Sistema</option>
                    </select>
                    <label for="perfil">Perfil</label>
                </div>

                <div class="form-floating mb-3">
                    <select required name="classificacao" class="form-control" id="classificacao">
                        <option value="bronze" <?=(($d->classificacao == 'bronze')?'selected':false)?>>bronze</option>
                        <option value="prata" <?=(($d->classificacao == 'prata')?'selected':false)?>>prata</option>
                        <option value="outo" <?=(($d->classificacao == 'outo')?'selected':false)?>>ouro</option>
                        <option value="diamante" <?=(($d->classificacao == 'diamante')?'selected':false)?>>diamante</option>
                    </select>
                    <label for="classificacao">Classificação</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" required name="funcao" id="funcao" class="form-control" placeholder="Informe a Função" value="<?=$d->funcao?>">
                    <label for="endereco">Função</label>
                </div>                

                <div class="w-100" atuacao>

                    <div class="row">
                        <div class="col-md-5">
                            <div atuacao class="form-floating mb-3">
                                <select id="municipio" class="form-control">
                                    <option value="">:: Selecione ::</option>
                                    <?php
                                    $q = "select * from municipios order by nome asc";
                                    $r = mysqli_query($con, $q);
                                    while($s = mysqli_fetch_object($r)){
                                    ?>
                                    <option value="<?=$s->codigo?>"><?="{$s->nome}".(($s->codigo != 1)?" - {$s->calha_rio}":false)?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                <label for="municipio">Municipio</label>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-floating mb-3">
                                <select id="bairro" class="form-control">
                                    <option value="">:: Selecione ::</option>
                                    <?php
                                    $q = "select * from bairros where municipio = '' order by nome asc";
                                    $r = mysqli_query($con, $q);
                                    while($s = mysqli_fetch_object($r)){
                                    ?>
                                    <option value="<?=$s->codigo?>"><?="{$s->nome}".(($d->municipio == 1)?" - {$s->zona}":false)?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                <label for="bairro">Bairro/comunidade</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="btn btn-primary addAtuacao"><i class="fa fa-user"></i></div>
                        </div>
                    </div>
                </div>

                <div atuacao>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="w-100 lista_atuacao"></div>
                        </div>
                    </div>
                </div>
                
                <div class="form-floating mb-3">
                    <textarea style="height:150px" name="observacoes" id="observacoes" class="form-control me-2" placeholder="Digite aqui"><?=$d->observacoes?></textarea>
                    <label for="observacoes">Observações</label>
                </div>
                <?php
                }
                
                if(
                    ($_SESSION['ProjectPainel']->codigo == 1 or
                    $_SESSION['ProjectPainel']->perfil == 'adm' or
                    $_SESSION['ProjectPainel']->perfil == 'gestor' or
                    $_SESSION['ProjectPainel']->codigo == $d->codigo or
                    !$d->codigo) and $d->codigo != 1
                ){
                ?>

                <div class="form-floating mb-3">
                    <input type="text" required name="login" id="login" class="form-control" placeholder="Login" value="<?=$d->login?>">
                    <label for="login">Login</label>
                </div>
                <?php
                }
                if(($_SESSION['ProjectPainel']->perfil == 'adm' and $d->codigo != 1) or $_SESSION['ProjectPainel']->codigo == $d->codigo){
                    // teste
                ?>
                <div class="form-floating mb-3">
                    <input <?=((!$d->codigo)?'required':false)?> type="text" name="senha" id="senha" class="form-control" placeholder="Senha" value="">
                    <label for="senha">Senha</label>
                </div>

                <?php
                }
                if($d->codigo != 1 and $_SESSION['ProjectPainel']->perfil == 'adm' and $_SESSION['ProjectPainel']->codigo != $d->codigo){
                    // teste
                ?>

                <div class="form-floating mb-3">
                    <select name="situacao" class="form-control" id="situacao">
                        <option value="1" <?=(($d->situacao == '1')?'selected':false)?>>Liberado</option>
                        <option value="0" <?=(($d->situacao == '0')?'selected':false)?>>Bloqueado</option>
                    </select>
                    <label for="situacao">Situação</label>
                </div>
                <?php
                }
                ?>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div style="display:flex; justify-content:end">
                    <button type="submit" class="btn btn-success btn-ms">Salvar</button>
                    <input type="hidden" id="codigo" value="<?=$_POST['codigo']?>" />
                </div>
            </div>
        </div>
    </form>

    <script>
        $(function(){
            Carregando('none');

            $("#cpf").mask("999.999.999-99");
            $("#telefone").mask("(99) 99999-9999");


            $.ajax({
                url:"src/usuarios/lista_atuacao.php",
                type:"POST",
                data:{
                    usuario:'<?=$d->codigo?>'
                },
                success:function(dados){
                    $(".lista_atuacao").html(dados);
                }
            })

            $(".addAtuacao").click(function(){
                municipio = $("#municipio").val();
                bairro = $("#bairro").val();
                if(!municipio || !bairro){
                    $.alert('Favor informe o município e bairro de atução!');
                    return;
                }

                $.ajax({
                    url:"src/usuarios/lista_atuacao.php",
                    type:"POST",
                    data:{
                        municipio,
                        bairro,
                        usuario:'<?=$d->codigo?>',
                        acao:'novo'
                    },
                    success:function(dados){
                        $(".lista_atuacao").html(dados);
                        $("#municipio").val('');
                        $("#bairro").html('<option value="">:: Selecione ::</option>');
                    }
                })
            });

            $("#perfil").change(function(){
                opc = $(this).val();
                usu = '<?=$_POST['codigo']?>';
                if(opc == 'assessor' && usu){
                    $("div[atuacao]").css("display","flex");
                    // $("#municipio").attr("required","required");
                    // $("#bairro").attr("required","required");    
                }else{
                    $("div[atuacao]").css("display","none");
                    // $("#municipio").removeAttr("required");
                    // $("#bairro").removeAttr("required");
                }
            })

            $("#municipio").change(function(){
                municipio = $(this).val();
                $.ajax({
                    url:"src/usuarios/filtro_bairros.php",
                    type:"POST",
                    data:{
                        municipio
                    },
                    success:function(dados){
                        $("#bairro").html(dados);
                    }
                })
            });




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

                            var type = file.type;
                            var name = file.name;

                            if(type.indexOf('image') === -1){
                                $.alert('Formato de arquivo invalido!');
                                Carregando('none');
                                return false
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


                                    $("#foto").attr("Base64", Base64);
                                    $("#foto").attr("tipo", type);
                                    $("#foto").attr("nome", name);

                                    $("div[foto]").children("img").remove();

                                    $("div[foto]").prepend(`<img src="${Base64}" style="max-width:100%; padding-bottom:10px;" />`);
                                    Carregando('none');


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

                var codigo = $('#codigo').val();
                var imagem = $('#foto').attr("Base64");
                var img_antigo = $('#foto').attr("img_antigo");
                var filds = $(this).serializeArray();

                if (codigo) {
                    filds.push({name: 'codigo', value: codigo})
                }

                if(imagem){
                    filds.push({name: 'imagem', value: imagem})
                }
                if(img_antigo){
                    filds.push({name: 'img_antigo', value: img_antigo})
                }


                filds.push({name: 'acao', value: 'salvar'})

                Carregando();

                $.ajax({
                    url:"src/usuarios/form.php",
                    type:"POST",
                    typeData:"JSON",
                    mimeType: 'multipart/form-data',
                    data: filds,
                    success:function(dados){
                        console.log(dados);
                        // if(dados.status){
                            $.ajax({
                                url:"src/usuarios/index.php",
                                type:"POST",
                                success:function(dados){
                                    $("#pageHome").html(dados);
                                    let myOffCanvas = document.getElementById('offcanvasRight');
                                    let openedCanvas = bootstrap.Offcanvas.getInstance(myOffCanvas);
                                    openedCanvas.hide();
                                }
                            });
                        // }
                    },
                    error:function(erro){

                        // $.alert('Ocorreu um erro!' + erro.toString());
                        //dados de teste
                    }
                });

            });

        })
    </script>