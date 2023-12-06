<?php
        include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");


    if($_POST['acao'] == 'salvar'){

        $data = $_POST;
        $attr = [];

        unset($data['codigo']);
        unset($data['acao']);

        foreach ($data as $name => $value) {
            $attr[] = "{$name} = '" . addslashes($value) . "'";
        }

        $attr = implode(', ', $attr);

        if($_POST['codigo']){
            $query = "update categorias set {$attr} where codigo = '{$_POST['codigo']}'";
            mysqli_query($con, $query);
            $codigo = $_POST['codigo'];
        }else{
            $query = "insert into categorias set {$attr}";
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


    $query = "select * from categorias where codigo = '{$_POST['codigo']}'";
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
</style>
<h4 class="Titulo<?=$md5?>">Registro de Categorias</h4>
    <form id="form-<?= $md5 ?>">
        <div class="row">
            <div class="col">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome" value="<?=$d->nome?>">
                    <label for="nome">Nome*</label>
                </div>
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
                <?php
                }
                ?>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div style="display:flex; justify-content:end">
                    <button voltar type="button" class="btn btn-danger btn-ms me-2">Cancelar</button>
                    <button type="submit" class="btn btn-success btn-ms">Salvar</button>
                    <input type="hidden" id="codigo" value="<?=$_POST['codigo']?>" />
                </div>
            </div>
        </div>
    </form>

    <script>
        $(function(){
            Carregando('none');

            $('#form-<?=$md5?>').submit(function (e) {

                e.preventDefault();

                var codigo = $('#codigo').val();
                var filds = $(this).serializeArray();

                if (codigo) {
                    filds.push({name: 'codigo', value: codigo})
                }

                filds.push({name: 'acao', value: 'salvar'})

                Carregando();

                $.ajax({
                    url:"src/categorias/form.php",
                    type:"POST",
                    typeData:"JSON",
                    mimeType: 'multipart/form-data',
                    data: filds,
                    success:function(dados){
                        console.log(dados);
                        // if(dados.status){
                            $.ajax({
                                url:"src/categorias/index.php",
                                type:"POST",
                                success:function(dados){
                                    $(".MenuRight").html(dados);
                                    $.ajax({
                                        url:"src/especialidades/index.php",
                                        success:function(dados){
                                            $("#pageHome").html(dados);
                                        }
                                    });
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

            $("button[voltar]").click(function(){
                $.ajax({
                    url:"src/categorias/index.php",
                    type:"POST",
                    success:function(dados){
                        $(".MenuRight").html(dados);
                    }
                });
            });

        })
    </script>