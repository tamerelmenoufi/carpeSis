<?php
        include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");

        if($_POST['acao'] == 'cep'){
            echo ConsultaCEP($_POST['cep']);
            exit();
        }


    if($_POST['acao'] == 'salvar'){

        $data = $_POST;
        $attr = [];

        unset($data['codigo']);
        unset($data['acao']);

        foreach ($data as $name => $value) {
            $attr[] = "{$name} = '" . addslashes($value) . "'";
        }

        if($_POST['tipo'] == 'c'){
            $attr[] = "tipo_descricao = ''";
        }
        $attr[] = "deletado = '0'";

        // if(!$_POST['codigo']){
        //     $attr[] = "assessor = '{$_SESSION['ProjectPainel']->codigo}'";
        // }

        $attr = implode(', ', $attr);

        if($_POST['codigo']){
            $query = "update beneficiados set {$attr} where codigo = '{$_POST['codigo']}'";
            mysqli_query($con, $query);
            $codigo = $_POST['codigo'];
        }else{
            $query = "insert into beneficiados set data_registro = NOW(), {$attr}";
            mysqli_query($con, $query);
            $codigo = mysqli_insert_id($con);
        }

        $return = [
            'status' => true,
            'codigo' => $query
        ];

        echo json_encode($return);

        exit();
    }



    if($_POST['cpf']){
        $query = "select * from beneficiados where cpf = '{$_POST['cpf']}'";
    }else{
        $query = "select * from beneficiados where codigo = '{$_POST['codigo']}'";
    }


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
    .ExibeEndereco{
        width:100%;
        height:200px;
        margin-bottom:20px;
    }
</style>
<h4 class="Titulo<?=$md5?>">Registro de Beneficiados</h4>
    <form id="form-<?= $md5 ?>">
        <div class="row">
            <div class="col">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" required id="cpf" name="cpf" placeholder="CPF" <?=(($d->codigo)?'readonly':false)?> value="<?=(($_POST['cpf'])?:$d->cpf)?>">
                    <label for="cpf">CPF*</label>
                </div>
                <!-- <div class="form-floating mb-3">
                    <select name="tipo" class="form-control" id="tipo">
                        <option value="c" <?=(($d->tipo == 'c')?'selected':false)?>>Cidadão</option>
                        <option value="l" <?=(($d->tipo == 'l')?'selected':false)?>>Liderança</option>
                    </select>
                    <label for="tipo">Tipo do Solicitante*</label>
                </div>
                <div tipo style="display:<?=(($d->tipo == 'l')?'block':'none')?>;">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" <?=(($d->tipo == 'l')?'required':false)?> id="tipo_descricao" name="tipo_descricao" placeholder="Tipo de Liderança" value="<?=$d->tipo_descricao?>">
                    <label for="tipo_descricao">Tipo Liderança*</label>
                </div>
                </div> -->
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" required id="nome" name="nome" placeholder="Nome" value="<?=$d->nome?>">
                    <label for="nome">Nome*</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="nome_mae" name="nome_mae" placeholder="Nome da Mãe" value="<?=$d->nome_mae?>">
                    <label for="nome_mae">Nome da Mãe*</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" placeholder="Data de Nascimento" value="<?=$d->data_nascimento?>">
                    <label for="data_nascimento">Data de Nascimento*</label>
                </div>
                <div class="form-floating mb-3">
                    <select name="sexo" class="form-control" id="sexo">
                        <option value="m" <?=(($d->sexo == 'm')?'selected':false)?>>Masculino</option>
                        <option value="f" <?=(($d->sexo == 'f')?'selected':false)?>>Feminino</option>
                    </select>
                    <label for="sexo">Sexo</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="email" name="email" id="email" class="form-control" placeholder="E-mail" value="<?=$d->email?>">
                    <label for="email">E-mail*</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" name="telefone" required id="telefone" class="form-control" placeholder="Telefone" value="<?=$d->telefone?>">
                    <label for="telefone">Telefone*</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" name="cep" id="cep" class="form-control" placeholder="CEP" value="<?=$d->cep?>">
                    <label for="cep">CEP</label>
                </div>
                <!-- <div class="form-floating mb-3">
                    <input type="text" name="icone" required id="icone" class="form-control" placeholder="Icone" value="<?=$d->icone?>">
                    <label for="icone">Icone*</label>
                </div> -->
                <div class="form-floating mb-3">
                    <input type="text" name="logradouro" required id="logradouro" class="form-control" placeholder="Logradouro" value="<?=$d->logradouro?>">
                    <label for="logradouro">Logradouro*</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" name="numero" required id="numero" class="form-control" placeholder="Número" value="<?=$d->numero?>">
                    <label for="numero">Número</label>
                </div>
                <div class="form-floating mb-3">
                    <!-- <input type="text" name="cidade" id="cidade" class="form-control" placeholder="Cidade" value="<?=$d->cidade?>"> -->
                    <select name="municipio" id="municipio" required class="form-control">
                        <option value="">:: Selecione o Município ::</option>
                        <?php
                        $q = "select * from municipios order by nome asc";
                        $r = mysqli_query($con, $q);
                        while($s = mysqli_fetch_object($r)){
                        ?>
                        <option value="<?=$s->codigo?>" <?=(($d->municipio == $s->codigo)?'selected':false)?>><?=$s->nome?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <label for="municipio">Município</label>
                </div>
                <div class="form-floating mb-3">
                    <!-- <input type="text" name="bairro" id="bairro" class="form-control" placeholder="Bairro" value="<?=$d->bairro?>"> -->
                    <select name="bairro" id="bairro" required class="form-control">
                        <option value="">:: Selecione o Bairro ::</option>
                        <?php
                        $q = "select * from bairros where municipio = '{$d->municipio}' order by nome asc";
                        $r = mysqli_query($con, $q);
                        while($s = mysqli_fetch_object($r)){
                        ?>
                        <option value="<?=$s->codigo?>" <?=(($d->bairro == $s->codigo)?'selected':false)?>><?=$s->nome?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <label for="bairro">Bairro</label>
                </div>
                <?php
                if($d->codigo){
                ?>
                <div class="ExibeEndereco"></div>
                <?php
                }
                ?>
                <?php
                if($_SESSION['ProjectPainel']->perfil == 'adm' ){
                    // teste
                ?>

                <div class="form-floating mb-3">
                    <select name="situacao" class="form-control" id="situacao">
                        <option value="1" <?=(($d->situacao == '1')?'selected':false)?>>Liberado</option>
                        <option value="0" <?=(($d->situacao == '0')?'selected':false)?>>Bloqueado</option>
                    </select>
                    <label for="situacao">Situação</label>
                </div>

                <div class="form-floating mb-3">
                    <select name="assessor" id="assessor" required class="form-control">
                        <option value="">:: Selecione o Líder ::</option>
                        <?php
                        $q = "select * from usuarios where perfil = 'assessor' and situacao = '1' and deletado != '1' order by nome asc";
                        $r = mysqli_query($con, $q);
                        while($s = mysqli_fetch_object($r)){
                        ?>
                        <option value="<?=$s->codigo?>" <?=(($d->assessor == $s->codigo)?'selected':false)?>><?=$s->nome?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <label for="assessor">Líder Responsável</label>
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
                    <input type="hidden" id="codigo" value="<?=$d->codigo?>" />
                    <input type="hidden" id="coordenadas" name="coordenadas" value="" />
                </div>
            </div>
        </div>
    </form>

    <script>
        $(function(){
            Carregando('none');
            $("#cpf").mask("999.999.999-99");
            $("#cep").mask("99999-999");
            $("#telefone").mask("(99) 99999-9999");
            // $("#data").mask("99/99/9999");

            $("#cep").blur(function(){
                cep = $(this).val();
                if(cep.length == 9){
                    $.ajax({
                        url:"src/beneficiados/form.php",
                        type:"POST",
                        dataType:"JSON",
                        data:{
                            acao:'cep',
                            cep
                        },
                        success:function(dados){
                            $("#logradouro").val(dados.logradouro);
                            $("#municipio").val(dados.localidade_codigo);
                            $.ajax({
                                url:"src/beneficiados/filtro_bairros.php",
                                type:"POST",
                                data:{
                                    municipio:dados.localidade_codigo,
                                    bairro:dados.bairro_codigo
                                },
                                success:function(dados){
                                    $("#bairro").html(dados);
                                }
                            })

                        }
                    });
                }
            })

            $("#tipo").change(function(){
                tipo = $(this).val();
                if(tipo == 'l'){
                    $("div[tipo]").css("display","block");
                    $("#tipo_descricao").attr('required','required');
                }else{
                    $("div[tipo]").css("display","none");
                    $("#tipo_descricao").removeAttr('required');
                }
            });

            <?php
            if($d->codigo){
            ?>
            $.ajax({
                url:"src/beneficiados/mapa_visualizar.php",
                type:"POST",
                data:{
                    codigo:'<?=$d->codigo?>'
                },
                success:function(dados){
                    $(".ExibeEndereco").html(dados);
                }
            })
            <?php
            }
            ?>

            $("#cpf").blur(function(){
                cpf = $(this).val();

                if(cpf != '<?=$_POST['cpf']?>' && cpf.length == 14 && validarCPF(cpf)){
                    Carregando();
                    $.ajax({
                        url:"src/beneficiados/form.php",
                        type:"POST",
                        data:{
                            cpf,
                            acao:'add'
                        },
                        success:function(dados){
                            $(".MenuRight").html(dados);
                        }
                    })
                }else if(!validarCPF(cpf)){
                    $.alert(`o CPF <b>${cpf}</b> e invalido!`)
                    $(this).val('');
                }else if(!cpf){
                    $(this).val('');
                }
            })

            $("#municipio").change(function(){
                municipio = $(this).val();
                $.ajax({
                    url:"src/beneficiados/filtro_bairros.php",
                    type:"POST",
                    data:{
                        municipio
                    },
                    success:function(dados){
                        $("#bairro").html(dados);
                    }
                })
            });

            $('#form-<?=$md5?>').submit(function (e) {

                e.preventDefault();

                var codigo = $('#codigo').val();
                var filds = $(this).serializeArray();

                if (codigo) {
                    filds.push({name: 'codigo', value: codigo})
                }

                filds.push({name: 'acao', value: 'salvar'})

                telefone = $("#telefone").val();
                if(telefone.length != 15){
                    $("#telefone").val('');
                    return false;
                }

                cpf = $("#cpf").val();
                if(!validarCPF(cpf)){
                    $.alert(`o CPF <b>${cpf}</b> e invalido!`)
                    return false;
                }

                Carregando();

                $.ajax({
                    url:"src/beneficiados/form.php",
                    type:"POST",
                    dataType:"JSON",
                    mimeType: 'multipart/form-data',
                    data: filds,
                    success:function(dados){
                        console.log(dados);
                        // if(dados.status){
                            cod = dados.codigo;
                            console.log(`codigo novo: ${cod}`)
                            $.ajax({
                                url:"src/beneficiados/index.php",
                                type:"POST",
                                success:function(dados){
                                    d = dados;
                                    $.ajax({
                                        url:"src/beneficiados/mapa_visualizar.php",
                                        type:"POST",
                                        data:{
                                            codigo:cod
                                        },
                                        success:function(dados){
                                            $(".ExibeEndereco").html(dados);
                                            $("#pageHome").html(d);
                                            let myOffCanvas = document.getElementById('offcanvasRight');
                                            let openedCanvas = bootstrap.Offcanvas.getInstance(myOffCanvas);
                                            openedCanvas.hide();
                                        }
                                    })

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