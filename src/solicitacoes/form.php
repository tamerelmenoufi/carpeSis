<?php
        include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");

        if(!$_SESSION['lista_ativa'] or $_POST['abrir']) {
            $_SESSION['lista_ativa'] = [];
            $_SESSION['ativo_permanente'] = [];
        }


    if($_POST['acao'] == 'salvar'){

        $data = $_POST;
        $attr = [];

        unset($data['codigo']);
        unset($data['lote']);
        unset($data['acao']);
        unset($data['detalhes']);

        if($_SESSION['ativo_permanente']){
            $attr[] = "detalhes = concat(detalhes,'<p>" . addslashes($_POST['detalhes']) . "</p>')";
        }else{
            $attr[] = "detalhes = '" . addslashes($_POST['detalhes']) . "'";
        }

        foreach ($data as $name => $value) {
            $attr[] = "{$name} = '" . addslashes($value) . "'";
        }

        if($_POST['lote']){
            $attr[] = "lote = '" . $_POST['lote'] . "'";
        }else{
            $attr[] = "lote = '" . uniqid() . "'";
        }

        $attr = implode(', ', $attr);

        foreach($_SESSION['lista_ativa'] as $ind => $beneficiado){

            if($_SESSION['ativo_permanente'][$beneficiado]){
                $query = "update servicos set {$attr} where 
                                                            beneficiado = '{$beneficiado}' and
                                                            categoria = '{$_POST['categoria']}' and
                                                            especialidade = '{$_POST['especialidade']}' and
                                                            situacao = '0' and
                                                            deletado != '1'";
                mysqli_query($con, $query);

            }else{

                $query = "select * from servicos where
                                                        beneficiado = '{$beneficiado}' and
                                                        categoria = '{$_POST['categoria']}' and
                                                        especialidade = '{$_POST['especialidade']}' and
                                                        situacao = '0' and
                                                        deletado != '1'
                        ";
                $result = mysqli_query($con, $query);

                if(!mysqli_num_rows($result)){
                    $query = "insert into servicos set 
                                                        data_pedido = NOW(), 
                                                        assessor = '{$_SESSION['ProjectPainel']->codigo}', 
                                                        beneficiado = '{$beneficiado}',
                                                        {$attr}";
                    mysqli_query($con, $query);
                }else{

                    $query = "update servicos set {$attr} where 
                                                        beneficiado = '{$beneficiado}' and
                                                        categoria = '{$_POST['categoria']}' and
                                                        especialidade = '{$_POST['especialidade']}' and
                                                        situacao = '0' and
                                                        deletado != '1'";
                    mysqli_query($con, $query);

                }
            }
        }

        $return = [
            'status' => true
        ];

        echo json_encode($return);

        exit();
    }

    $query = "select * from servicos where (codigo = '{$_POST['codigo']}' or lote = '{$_SESSION['lote']}') and deletado != '1' group by lote";
    $result = mysqli_query($con, $query);
    $d = mysqli_fetch_object($result);

    $_SESSION['categoria'] = $d->categoria;
    $_SESSION['especialidade'] = $d->especialidade;

    
    if($_SESSION['lote']){
        $lote = $_SESSION['lote'];
    }else if($d->lote){
        $lote = $d->lote;
    }else{
        $lote = false;
    }

   $q = "select a.beneficiado, (select count(*) from servicos_registros where servico = a.codigo) as registros from servicos a where a.lote = '{$lote}' and a.deletado != '1' group by a.beneficiado";
    $ba = mysqli_query($con, $q);
    while($b = mysqli_fetch_object($ba)){
        $_SESSION['lista_ativa'][] = $b->beneficiado;
        if($b->registros) $_SESSION['ativo_permanente'][$b->beneficiado] = true;
    }

?>
<style>
    .Titulo<?=$md5?>{
        position:absolute;
        left:60px;
        top:8px;
        z-index:0;
    }
    .ck-editor__editable_inline {
        min-height: 200px;
    }
    .divBg{
        position:absolute;
        left:0;
        top:0;
        bottom:0;
        right:0;
        background-color:rgb(0,0,0,0.5);
        display:none;
        overflow-y:auto;
        z-index:10;
    }
    .divBgDados{
        overflow-y:"auto";
    }
</style>

<div class="divBg">
    <div class="divBgDados"></div>
</div>


<h4 class="Titulo<?=$md5?>">Registro de Solicitação <?=(($lote)?" - lote ({$lote})":false)?></h4>
    <form id="form-<?= $md5 ?>">
        <div class="row">
            <div class="col">

                <div class="btn btn-primary btn-sm acaoBeneficiados mb-1"><i class="fa fa-users"></i> Adicioanr Beneficiarios</div>
                <div class="form-floating mb-3">
                    <div class="form-control listaBeneficiados"></div>
                    <!-- <input type="text" class="form-control" id="beneficiado" name="beneficiado" placeholder="Nome Completo" value="<?=$d->beneficiado_nome?>"> -->
                    <label for="beneficiado" style="cursor:pointer">Adicionar lista de Beneficiados*</label>
                </div>

                <div class="form-floating mb-3">
                    <select name="categoria" id="categoria" required class="form-control">
                        <option value="" <?=(($_SESSION['ativo_permanente'])?'disabled':false)?>>:: Selecione a Categoria ::</option>
                        <?php
                        $q = "select * from categorias where deletado != '1' and situacao = '1' order by nome asc";
                        $r = mysqli_query($con, $q);
                        while($s = mysqli_fetch_object($r)){
                        ?>
                        <option value="<?=$s->codigo?>" <?=(($d->categoria == $s->codigo)?'selected':false)?> <?=(($_SESSION['ativo_permanente'] and $d->categoria != $s->codigo)?'disabled':false)?>><?=$s->nome?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <label for="categoria">Categoria</label>
                </div>

                <div class="form-floating mb-3">
                    <select name="sub_categoria" id="sub_categoria" class="form-control">
                        <option value="" <?=(($_SESSION['ativo_permanente'])?'disabled':false)?>>:: Selecione a Categoria ::</option>
                        <?php
                        $q = "select * from sub_categorias where deletado != '1' and situacao = '1' and categoria = '{$d->categoria}' order by nome asc";
                        $r = mysqli_query($con, $q);
                        while($s = mysqli_fetch_object($r)){
                        ?>
                        <option value="<?=$s->codigo?>" <?=(($d->sub_categoria == $s->codigo)?'selected':false)?> <?=(($_SESSION['ativo_permanente'] and $d->sub_categoria != $s->codigo)?'disabled':false)?>><?=$s->nome?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <label for="sub_categoria">Sub Categoria</label>
                </div>

                <div class="form-floating mb-3">
                    <select name="especialidade" id="especialidade" required class="form-control">
                        <option value="" <?=(($_SESSION['ativo_permanente'])?'disabled':false)?>>:: Selecione a Especialidade ::</option>
                        <?php
                        $q = "select * from especialidades where categoria = '{$d->categoria}' and deletado != '1' and situacao = '1' order by nome asc";
                        $r = mysqli_query($con, $q);
                        while($s = mysqli_fetch_object($r)){
                        ?>
                        <option value="<?=$s->codigo?>" <?=(($d->especialidade == $s->codigo)?'selected':false)?> <?=(($_SESSION['ativo_permanente'] and $d->especialidade != $s->codigo)?'disabled':false)?>><?=$s->nome?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <label for="especialidade">Especialidade</label>
                </div>

                <div class="form-floating mb-3">
                    <select name="orgao" id="orgao" class="form-control">
                        <option value="" <?=(($_SESSION['ativo_permanente'])?'disabled':false)?>>:: Selecione o Órgao ::</option>
                        <?php
                        $q = "select * from orgaos where categoria->>'$.cat{$d->categoria}' = '{$d->categoria}' and especialidade->>'$.esp{$d->especialidade}' = '{$d->especialidade}' and deletado != '1' and situacao = '1' order by nome asc";
                        $r = mysqli_query($con, $q);
                        while($s = mysqli_fetch_object($r)){
                        ?>
                        <option value="<?=$s->codigo?>" <?=(($d->orgao == $s->codigo)?'selected':false)?> <?=(($_SESSION['ativo_permanente'] and $d->orgao != $s->codigo)?'disabled':false)?>><?=$s->nome.' - '.$s->esfera?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <label for="orgao">Órgão - Esfera</label>
                </div>
                <?=(($_SESSION['ativo_permanente'])?"<p>{$d->detalhes}</p>":false)?>
                <div class="form-floating mb-3">
                    <textarea style="height:250px;" name="detalhes" id="detalhes" class="form-control" placeholder="Detalhe o pedido neste espaço" required><?=((!$_SESSION['ativo_permanente'])?$d->detalhes:false)?></textarea>
                    <label for="detalhes">Detalhe o pedido neste espaço</label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div style="display:flex; justify-content:end">
                    <button type="submit" class="btn btn-success btn-ms">Salvar</button>
                    <input type="hidden" id="codigo" value="<?=$d->codigo?>" />
                    <input type="hidden" id="lote" name="lote" value="<?=$lote?>" />
                </div>
            </div>
        </div>
    </form>

    <script>
        $(function(){
            Carregando('none');

            $("#cpf").mask("999.999.999-99");
            $("#phone").mask("99999999999");

            $.ajax({
                url:"src/solicitacoes/beneficiados_ativos.php",
                success:function(dados){
                $(".listaBeneficiados").html(dados);
                }
            });

            $(".acaoBeneficiados").click(function(){
                $(".divBg").css("display","block");
                $.ajax({
                    url:"src/solicitacoes/beneficiados.php",
                    type:"POST",
                    success:function(dados){
                        $(".divBgDados").html(dados);
                    }
                })
            })

            $("#categoria").change(function(){
                categoria = $(this).val();
                $.ajax({
                    url:"src/solicitacoes/filtro_sub_categoria.php",
                    type:"POST",
                    data:{
                        categoria
                    },
                    success:function(dados){
                        $("#sub_categoria").html(dados);
                        $("#especialidade").html('<option value="">:: Selecione a Especialidade ::</option>');
                        $("#orgao").html('<option value="">:: Selecione o Órgão ::</option>');
                    }
                })

            });

            $("#sub_categoria").change(function(){
                sub_categoria = $(this).val();
                categoria = $("#categoria").val();

                $.ajax({
                    url:"src/solicitacoes/filtro_especialidades.php",
                    type:"POST",
                    data:{
                        categoria,
                        sub_categoria
                    },
                    success:function(dados){
                        $("#especialidade").html(dados);
                        $("#orgao").html('<option value="">:: Selecione o Órgão ::</option>');
                    }
                })
            });

            $("#especialidade").change(function(){
                categoria = $("#categoria").val();
                especialidade = $(this).val();
                $.ajax({
                    url:"src/solicitacoes/filtro_orgaos.php",
                    type:"POST",
                    data:{
                        categoria,
                        especialidade
                    },
                    success:function(dados){
                        $("#orgao").html(dados);
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

                console.log(filds)

                Carregando();

                $.ajax({
                    url:"src/solicitacoes/form.php",
                    type:"POST",
                    dataType:"JSON",
                    mimeType: 'multipart/form-data',
                    data: filds,
                    success:function(dados){
                        // console.log(dados);
                        $.alert('Dados atualizados com sucesso!');
                        $.ajax({
                            url:"src/solicitacoes/index.php",
                            type:"POST",
                            success:function(dados){
                                $("#pageHome").html(dados);
                                let myOffCanvas = document.getElementById('offcanvasRight');
                                let openedCanvas = bootstrap.Offcanvas.getInstance(myOffCanvas);
                                openedCanvas.hide();
                            }
                        });
                    },
                    error:function(erro){
                        // $.alert('Ocorreu um erro!' + erro.toString());
                        //dados de teste
                    }
                });

            });

        })
    </script>