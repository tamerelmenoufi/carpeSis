<?php
        include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");


    if($_POST['acao'] == 'salvar'){

        $data = $_POST;
        $attr = [];

        unset($data['codigo']);
        unset($data['acao']);
        unset($data['add']);

        foreach ($data as $name => $value) {
            $attr[] = "{$name} = '" . addslashes($value) . "'";
        }

        // if($_POST['codigo'] and $_POST['add']){
        //     $attr[] = "categoria = JSON_SET('{}', '$.cat{$_SESSION['categoria']}', {$_SESSION['categoria']})";
        // }else if($_POST['codigo']){
        //     $attr[] = "categoria = JSON_SET(categoria, '$.cat{$_SESSION['categoria']}', {$_SESSION['categoria']})";
        // }else{
        //     $attr[] = "categoria = JSON_SET('{}', '$.cat{$_SESSION['categoria']}', {$_SESSION['categoria']})";
        // }

        $attr[] = "categoria = JSON_SET(if(categoria > 0,categoria,'{}'), '$.cat{$_SESSION['categoria']}', {$_SESSION['categoria']})";
        $attr[] = "especialidade = JSON_SET(if(especialidade > 0,especialidade,'{}'), '$.esp{$_SESSION['especialidade']}', {$_SESSION['especialidade']})";

        // $attr[] = "esfera = '" . addslashes($_SESSION['esfera']) . "'";

        $attr = implode(', ', $attr);

        if($_POST['codigo']){
            $query = "update orgaos set {$attr} where codigo = '{$_POST['codigo']}'";
            mysqli_query($con, $query);
            $codigo = $_POST['codigo'];
        }else{
            $query = "insert into orgaos set date_registro = NOW(), {$attr}";
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

    if($_POST['cnpj']){
        $query = "select * from orgaos where cnpj = '{$_POST['cnpj']}'";
    }else{
        $query = "select * from orgaos where codigo = '{$_POST['codigo']}'";
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
<h4 class="Titulo<?=$md5?>">Registro de Órgãos</h4>
    <form id="form-<?= $md5 ?>">
        <div class="row">
            <div class="col">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" required id="cnpj" name="cnpj" placeholder="CNPJ" <?=(($d->codigo)?'readonly':false)?> value="<?=(($_POST['cnpj'])?:$d->cnpj)?>">
                    <label for="cnpj">CNPJ*</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" required id="nome" name="nome" placeholder="Nome" value="<?=$d->nome?>">
                    <label for="nome">Nome*</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" required id="razao_social" name="razao_social" placeholder="Razao Social" value="<?=$d->razao_social?>">
                    <label for="razao_social">Razao Social*</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" required id="contato_nome" name="contato_nome" placeholder="Contato (Nome)" value="<?=$d->contato_nome?>">
                    <label for="contato_nome">Contato (Nome)*</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" required id="contato_telefone" name="contato_telefone" placeholder="Contato (Telefone)" value="<?=$d->contato_telefone?>">
                    <label for="contato_telefone">Contato (Telefone)*</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" required id="contato_email" name="contato_email" placeholder="Contato (E-mail)" value="<?=$d->contato_email?>">
                    <label for="contato_email">Contato (E-mail)*</label>
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
                    <select name="cidade" id="cidade" required class="form-control">
                        <option value="">:: Selecione a Cidade ::</option>
                        <?php
                        $q = "select * from municipios order by nome asc";
                        $r = mysqli_query($con, $q);
                        while($s = mysqli_fetch_object($r)){
                        ?>
                        <option value="<?=$s->codigo?>" <?=(($d->cidade == $s->codigo)?'selected':false)?>><?=$s->nome?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <label for="cidade">Cidade</label>
                </div>
                <div class="form-floating mb-3">
                    <!-- <input type="text" name="bairro" id="bairro" class="form-control" placeholder="Bairro" value="<?=$d->bairro?>"> -->
                    <select name="bairro" id="bairro" required class="form-control">
                        <option value="">:: Selecione o Bairro ::</option>
                        <?php
                        $q = "select * from bairros where municipio = '{$d->cidade}' order by nome asc";
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
                <div class="form-floating mb-3">
                    <input type="text" name="cep" required id="cep" class="form-control" placeholder="CEP" value="<?=$d->cep?>">
                    <label for="cep">CEP</label>
                </div>
                <?php
                if($d->codigo){
                ?>
                <div class="ExibeEndereco"></div>
                <?php
                }
                ?>
                <div class="form-floating mb-3">
                    <select name="esfera" class="form-control" id="esfera">
                        <option value="estadual" <?=(($d->esfera == 'estadual')?'selected':false)?>>estadual</option>
                        <option value="municipal" <?=(($d->esfera == 'municipal')?'selected':false)?>>municipal</option>
                        <option value="particular" <?=(($d->esfera == 'particular')?'selected':false)?>>particular</option>
                    </select>
                    <label for="esfera">Esfera</label>
                </div>
                <?php
                if($d->codigo != 1 and $_SESSION['ProjectPainel']->perfil == 'adm' ){
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
                    <input type="hidden" id="codigo" value="<?=$d->codigo?>" />
                    <input type="hidden" id="add" value="<?=(($_POST['cnpj'])?'add':false)?>" />
                    <input type="hidden" id="coordenadas" name="coordenadas" value="" />
                </div>
            </div>
        </div>
    </form>

    <script>
        $(function(){
            Carregando('none');
            $("#cnpj").mask("99.999.999/9999-99");
            $("#cep").mask("99999-999");
            $("#contato_telefone").mask("(99) 99999-9999");


            <?php
            if($d->codigo){
            ?>
            $.ajax({
                url:"src/orgaos/mapa_visualizar.php",
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

            $("#cnpj").blur(function(){
                cnpj = $(this).val();
                if(cnpj != '<?=$_POST['cnpj']?>' && cnpj.length == 18){
                    Carregando();
                    $.ajax({
                        url:"src/orgaos/form.php",
                        type:"POST",
                        data:{
                            cnpj,
                            acao:'add'
                        },
                        success:function(dados){
                            $(".MenuRight").html(dados);
                        }
                    })
                }else{
                    $(this).val('');
                }
            })

            $("#cidade").change(function(){
                cidade = $(this).val();
                $.ajax({
                    url:"src/orgaos/filtro_bairros.php",
                    type:"POST",
                    data:{
                        cidade
                    },
                    success:function(dados){
                        $("#bairro").html(dados);
                    }
                })
            });

            $('#form-<?=$md5?>').submit(function (e) {

                e.preventDefault();

                var codigo = $('#codigo').val();
                var add = $('#add').val();
                var filds = $(this).serializeArray();

                if (codigo) {
                    filds.push({name: 'codigo', value: codigo})
                }

                if (add) {
                    filds.push({name: 'add', value: add})
                }

                filds.push({name: 'acao', value: 'salvar'})

                Carregando();

                $.ajax({
                    url:"src/orgaos/form.php",
                    type:"POST",
                    dataType:"JSON",
                    mimeType: 'multipart/form-data',
                    data: filds,
                    success:function(dados){
                        console.log(dados);
                        cod = dados.codigo;
                        // if(dados.status){
                            $.ajax({
                                url:"src/orgaos/index.php",
                                type:"POST",
                                success:function(dados){

                                    d = dados;
                                    $.ajax({
                                        url:"src/orgaos/mapa_visualizar.php",
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