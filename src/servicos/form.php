<?php
        include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");


    if($_POST['acao'] == 'salvar'){

        $data = $_POST;
        $attr = [];

        unset($data['codigo']);
        unset($data['acao']);
        unset($data['lote']);

        foreach ($data as $name => $value) {
            $attr[] = "{$name} = '" . addslashes($value) . "'";
        }

        if($_POST['lote']){
            $attr[] = "lote = '" . $_POST['lote'] . "'";
        }else{
            $attr[] = "lote = '" . uniqid() . "'";
            $attr[] = "beneficiado = '" . $_SESSION['beneficiado'] . "'";
        }

        $attr = implode(', ', $attr);

        if($_POST['codigo']){
            $query = "update servicos set {$attr} where codigo = '{$_POST['codigo']}' or (lote = '{$_POST['lote']}' and situacao = '0' and deletado != '1')";
            mysqli_query($con, $query);
            $codigo = $_POST['codigo'];
        }else{

            $query = "select * from servicos where
                                                    beneficiado = '{$_SESSION['beneficiado']}' and
                                                    categoria = '{$_POST['categoria']}' and
                                                    especialidade = '{$_POST['especialidade']}' and
                                                    situacao = '0' and
                                                    deletado != '1'
                    ";
            $result = mysqli_query($con, $query);
            if(mysqli_num_rows($result)){
                $d = mysqli_fetch_object($result);
                $codigo = 'error';
            }else{
                $query = "insert into servicos set data_pedido = NOW(), assessor = '{$_SESSION['ProjectPainel']->codigo}', {$attr}";
                mysqli_query($con, $query);
                $codigo = mysqli_insert_id($con);
            }
        }

        $return = [
            'status' => true,
            'codigo' => $codigo,
            'codigoUpdate' => $d->codigo,
        ];

        echo json_encode($return);

        exit();
    }

    $query = "select * from servicos where codigo = '{$_POST['codigo']}'";
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
    .ck-editor__editable_inline {
        min-height: 200px;
    }
</style>
<h4 class="Titulo<?=$md5?>">Registro de Solicitação</h4>
    <form id="form-<?= $md5 ?>">
        <div class="row">
            <div class="col">

                <div class="form-floating mb-3">
                    <div class="form-control"><?=$_SESSION['beneficiado_nome']?></div>
                    <!-- <input type="text" class="form-control" id="beneficiado" name="beneficiado" placeholder="Nome Completo" value="<?=$d->beneficiado_nome?>"> -->
                    <label for="beneficiado">Nome do Beneficiado*</label>
                </div>

                <div class="form-floating mb-3">
                    <select name="categoria" id="categoria" required class="form-control">
                        <option value="">:: Selecione a Categoria ::</option>
                        <?php
                        $q = "select * from categorias where deletado != '1' and situacao = '1' order by nome asc";
                        $r = mysqli_query($con, $q);
                        while($s = mysqli_fetch_object($r)){
                        ?>
                        <option value="<?=$s->codigo?>" <?=(($d->categoria == $s->codigo)?'selected':false)?>><?=$s->nome?></option>
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
                        <option value="">:: Selecione a Especialidade ::</option>
                        <?php
                        $q = "select * from especialidades where categoria = '{$d->categoria}' and deletado != '1' and situacao = '1' order by nome asc";
                        $r = mysqli_query($con, $q);
                        while($s = mysqli_fetch_object($r)){
                        ?>
                        <option value="<?=$s->codigo?>" <?=(($d->especialidade == $s->codigo)?'selected':false)?>><?=$s->nome?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <label for="especialidade">Especialidade</label>
                </div>

                <div class="form-floating mb-3">
                    <select name="orgao" id="orgao" class="form-control">
                        <option value="">:: Selecione o Órgao ::</option>
                        <?php
                        $q = "select * from orgaos where categoria->>'$.cat{$d->categoria}' = '{$d->categoria}' and especialidade->>'$.esp{$d->especialidade}' = '{$d->especialidade}' and deletado != '1' and situacao = '1' order by nome asc";
                        $r = mysqli_query($con, $q);
                        while($s = mysqli_fetch_object($r)){
                        ?>
                        <option value="<?=$s->codigo?>" <?=(($d->orgao == $s->codigo)?'selected':false)?>><?=$s->nome.' - '.$s->esfera?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <label for="orgao">Órgão - Esfera</label>
                </div>
                <div class="form-floating mb-3">
                    <textarea style="height:250px;" name="detalhes" id="detalhes" class="form-control" placeholder="Detalhe o pedido neste espaço" required><?=$d->detalhes?></textarea>
                    <label for="detalhes">Detalhe o pedido neste espaço</label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div style="display:flex; justify-content:end">
                    <button type="submit" class="btn btn-success btn-ms">Salvar</button>
                    <input type="hidden" id="codigo" value="<?=$d->codigo?>" />
                    <input type="hidden" id="lote" name="lote" value="<?=$d->lote?>" />
                </div>
            </div>
        </div>
    </form>

    <script>
        $(function(){
            Carregando('none');

            $("#cpf").mask("999.999.999-99");
            $("#phone").mask("99999999999");

            // ClassicEditor
            // .create( this.querySelector( '#detalhes<?=$md5?>'))
            // .then( editor => {
            //     console.log( editor );
            // } )
            // .catch( error => {
            //     console.error( error );

            // } );


            // $("#categoria").change(function(){
            //     categoria = $(this).val();
            //     $.ajax({
            //         url:"src/servicos/filtro_especialidades.php",
            //         type:"POST",
            //         data:{
            //             categoria
            //         },
            //         success:function(dados){
            //             $("#especialidade").html(dados);
            //             $("#orgao").html('<option value="">:: Selecione o Órgão ::</option>');
            //         }
            //     })
            // });

            // $("#especialidade").change(function(){
            //     categoria = $("#categoria").val();
            //     especialidade = $(this).val();
            //     $.ajax({
            //         url:"src/servicos/filtro_orgaos.php",
            //         type:"POST",
            //         data:{
            //             categoria,
            //             especialidade
            //         },
            //         success:function(dados){
            //             $("#orgao").html(dados);
            //         }
            //     })
            // });






            $("#categoria").change(function(){
                categoria = $(this).val();
                sub_categoria = '0';
                $.ajax({
                    url:"src/servicos/filtro_sub_categoria.php",
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

                $.ajax({
                    url:"src/servicos/filtro_especialidades.php",
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

            $("#sub_categoria").change(function(){
                sub_categoria = $(this).val();
                categoria = $("#categoria").val();

                $.ajax({
                    url:"src/servicos/filtro_especialidades.php",
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
                    url:"src/servicos/filtro_orgaos.php",
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

                // console.log(filds)

                Carregando();

                $.ajax({
                    url:"src/servicos/form.php",
                    type:"POST",
                    dataType:"JSON",
                    mimeType: 'multipart/form-data',
                    data: filds,
                    success:function(dados){
                        console.log(dados);
                        if(dados.codigo == 'error'){
                            AlertaJaExiste = $.dialog({
                                content:`<center>Essa solicitação não pode ser criada.<br>O beneficiado já possui esta solicitação em aberto.<br>Clique no botão abaixo para carregar a solicitação.<br><button editiarCodigoUpdate="${dados.codigoUpdate}" class="btn btn-primary">Editar Solicitação</button></center>`,
                                title:'Solicitação Duplicada',
                            });
                            Carregando('none');
                            $(document).on('click', "button[editiarCodigoUpdate]", function(){
                                Carregando();
                                codigo = $(this).attr("editiarCodigoUpdate");
                                $(".MenuRight").html('');
                                $.ajax({
                                    url:"src/servicos/form.php",
                                    type:"POST",
                                    data:{
                                    codigo
                                    },
                                    success:function(dados){
                                        $(".MenuRight").html(dados);
                                        AlertaJaExiste.close();
                                    }
                                })
                            })
                        }else{
                            $.ajax({
                                url:"src/servicos/index.php",
                                type:"POST",
                                success:function(dados){
                                    $("#pageHome").html(dados);
                                    let myOffCanvas = document.getElementById('offcanvasRight');
                                    let openedCanvas = bootstrap.Offcanvas.getInstance(myOffCanvas);
                                    openedCanvas.hide();
                                }
                            });
                        }
                    },
                    error:function(erro){

                        // $.alert('Ocorreu um erro!' + erro.toString());
                        //dados de teste
                    }
                });

            });

        })
    </script>