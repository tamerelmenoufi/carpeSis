<?php
        include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");


        if($_POST['acao'] == 'filtrar'){

            $_SESSION['filtroBeneficiados'] = [];

            unset($_POST['acao']);

            foreach($_POST as $i => $v){
                if($v or $v === '0') $_SESSION['filtroBeneficiados'][$i] = $v;
            }

            exit();

        }
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
<h4 class="Titulo<?=$md5?>">Filtrar Beneficiados</h4>
    <form id="form-<?= $md5 ?>">
        <div class="row">
            <div class="col">

                <div class="form-floating mb-3">
                    <!-- <input type="text" name="cidade" id="cidade" class="form-control" placeholder="Cidade" value="<?=$d->cidade?>"> -->
                    <select name="municipio" id="municipio" class="form-control">
                        <option value="">Todos</option>
                        <?php
                        $q = "select * from municipios order by nome asc";
                        $r = mysqli_query($con, $q);
                        while($s = mysqli_fetch_object($r)){
                        ?>
                        <option value="<?=$s->codigo?>" <?=(($_SESSION['filtroBeneficiados']['municipio'] == $s->codigo)?'selected':false)?>><?=$s->nome?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <label for="municipio">Município</label>
                </div>
                <div class="form-floating mb-3">
                    <!-- <input type="text" name="bairro" id="bairro" class="form-control" placeholder="Bairro" value="<?=$d->bairro?>"> -->
                    <select name="bairro" id="bairro" class="form-control">
                        <option value="">Todos</option>
                        <?php
                        $q = "select * from bairros where municipio = '{$_SESSION['filtroBeneficiados']['municipio']}' order by nome asc";
                        $r = mysqli_query($con, $q);
                        while($s = mysqli_fetch_object($r)){
                        ?>
                        <option value="<?=$s->codigo?>" <?=(($_SESSION['filtroBeneficiados']['bairro'] == $s->codigo)?'selected':false)?>><?=$s->nome?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <label for="bairro">Bairro</label>
                </div>
                <div class="form-floating mb-3">
                    <select name="sexo" class="form-control" id="sexo">
                        <option value="">Todos</option>
                        <option value="m" <?=(($_SESSION['filtroBeneficiados']['sexo'] == 'm')?'selected':false)?>>Masculino</option>
                        <option value="f" <?=(($_SESSION['filtroBeneficiados']['sexo'] == 'f')?'selected':false)?>>Feminino</option>
                    </select>
                    <label for="sexo">Sexo</label>
                </div>
                

                <div class="form-floating mb-3">
                    <select name="situacao" class="form-control" id="situacao">
                        <option value="">Todos</option>
                        <option value="1" <?=(($_SESSION['filtroBeneficiados']['situacao'] == '1')?'selected':false)?>>Liberado</option>
                        <option value="0" <?=(($_SESSION['filtroBeneficiados']['situacao'] == '0')?'selected':false)?>>Bloqueado</option>
                    </select>
                    <label for="situacao">Situação</label>
                </div>

                <div class="form-floating mb-3">
                    <select name="assessor" id="assessor" class="form-control">
                        <option value="">Todos os Líderes</option>
                        <?php
                        $q = "select * from usuarios where perfil = 'assessor' and situacao = '1' and deletado != '1' order by nome asc";
                        $r = mysqli_query($con, $q);
                        while($s = mysqli_fetch_object($r)){
                        ?>
                        <option value="<?=$s->codigo?>" <?=(($_SESSION['filtroBeneficiados']['assessor'] == $s->codigo)?'selected':false)?>><?=$s->nome?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <label for="assessor">Líder Responsável</label>
                </div>

                
                
                <div class="form-floating mb-3">
                    <select name="categoria" id="categoria" class="form-control">
                        <option value="">Todos as Categrorias</option>
                        <?php
                        $q = "select * from categorias where situacao = '1' and deletado != '1' order by nome asc";
                        $r = mysqli_query($con, $q);
                        while($s = mysqli_fetch_object($r)){
                        ?>
                        <option value="<?=$s->codigo?>" <?=(($_SESSION['filtroBeneficiados']['categoria'] == $s->codigo)?'selected':false)?>><?=$s->nome?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <label for="categoria">Categoria da Solicitação</label>
                </div>

                <div class="form-floating mb-3">
                    <select name="sub_categoria" id="sub_categoria" class="form-control">
                        <option value="">Todos as Subcategorias</option>
                        <?php
                        $q = "select * from sub_categorias where categoria = '{$_SESSION['filtroBeneficiados']['categoria']}' and situacao = '1' and deletado != '1' order by nome asc";
                        $r = mysqli_query($con, $q);
                        while($s = mysqli_fetch_object($r)){
                        ?>
                        <option value="<?=$s->codigo?>" <?=(($_SESSION['filtroBeneficiados']['sub_categoria'] == $s->codigo)?'selected':false)?>><?=$s->nome?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <label for="sub_categoria">Subcategoria da Solicitação</label>
                </div>

                <div class="form-floating mb-3">
                    <select name="especialidade" id="especialidade" class="form-control">
                        <option value="">Todos as Especialidades</option>
                        <?php
                        $q = "select * from especialidades where categoria = '{$_SESSION['filtroBeneficiados']['categoria']}' and sub_categoria = '{$_SESSION['filtroBeneficiados']['sub_categoria']}' and situacao = '1' and deletado != '1' order by nome asc";
                        $r = mysqli_query($con, $q);
                        while($s = mysqli_fetch_object($r)){
                        ?>
                        <option value="<?=$s->codigo?>" <?=(($_SESSION['filtroBeneficiados']['especialidade'] == $s->codigo)?'selected':false)?>><?=$s->nome?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <label for="especialidade">Especialidade da Solicitação</label>
                </div>

                
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div style="display:flex; justify-content:end">
                    <button type="submit" class="btn btn-success btn-ms">Filtrar</button>
                </div>
            </div>
        </div>
    </form>

    <script>
        $(function(){
            Carregando('none');


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

            $("#categoria").change(function(){
                categoria = $(this).val();
                sub_categoria = '0';
                $.ajax({
                    url:"src/beneficiados/filtro_sub_categoria.php",
                    type:"POST",
                    data:{
                        categoria
                    },
                    success:function(dados){
                        $("#sub_categoria").html(dados);
                        $("#especialidade").html('<option value="">Todos as Especialidades</option>');
                    }
                })
                $.ajax({
                    url:"src/beneficiados/filtro_especialidade.php",
                    type:"POST",
                    data:{
                        categoria,
                        sub_categoria
                    },
                    success:function(dados){
                        $("#especialidade").html(dados);
                    }
                })
            });
            
            $("#sub_categoria").change(function(){
                categoria = $("#categoria").val();
                sub_categoria = $(this).val();
                $.ajax({
                    url:"src/beneficiados/filtro_especialidade.php",
                    type:"POST",
                    data:{
                        categoria,
                        sub_categoria
                    },
                    success:function(dados){
                        $("#especialidade").html(dados);
                    }
                })
            });


            $('#form-<?=$md5?>').submit(function (e) {

                e.preventDefault();

                var filds = $(this).serializeArray();

                filds.push({name: 'acao', value: 'filtrar'})

                Carregando();

                $.ajax({
                    url:"src/beneficiados/filtro_form.php",
                    type:"POST",
                    data: filds,
                    success:function(dados){

                        $.ajax({
                            url:"src/beneficiados/index.php",
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