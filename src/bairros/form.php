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
            $query = "update bairros set {$attr} where codigo = '{$_POST['codigo']}'";
            mysqli_query($con, $query);
            $codigo = $_POST['codigo'];
        }else{
            $query = "insert into bairros set {$attr}";
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


    $query = "select * from bairros where codigo = '{$_POST['codigo']}'";
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
<h4 class="Titulo<?=$md5?>">Registro<?=(($_SESSION['tipo'] == 'urbano')?' de Bairros':(($_SESSION['tipo'])?'Lista de Comunidade':false))?></h4>
    <form id="form-<?= $md5 ?>">
        <div class="row">
            <div class="col">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" required id="nome" name="nome" placeholder="Nome Completo" value="<?=$d->nome?>">
                    <label for="nome">Nome*</label>
                </div>
                <?php
                if($_SESSION['municipio'] == '1'){
                ?>
                <div class="form-floating mb-3">
                    <select name="zona" id="zona" required class="form-select">
                        <option value="">:: Selecione a Zona ::</option>
                        <option value="Norte" <?=(($d->zona == 'Norte' or (!$d->zona and $_SESSION['zona'] == 'Norte'))?'selected':false)?>>Norte</option>
                        <option value="Leste" <?=(($d->zona == 'Leste' or (!$d->zona and $_SESSION['zona'] == 'Leste'))?'selected':false)?>>Leste</option>
                        <option value="Sul" <?=(($d->zona == 'Sul' or (!$d->zona and $_SESSION['zona'] == 'Sul'))?'selected':false)?>>Sul</option>
                        <option value="Oeste" <?=(($d->zona == 'Oeste' or (!$d->zona and $_SESSION['zona'] == 'Oeste'))?'selected':false)?>>Oeste</option>
                        <option value="Centro-Sul" <?=(($d->zona == 'Centro-Sul' or (!$d->zona and $_SESSION['zona'] == 'Centro-Sul'))?'selected':false)?>>Centro-Sul</option>
                        <option value="Centro-Oeste" <?=(($d->zona == 'Centro-Oeste' or (!$d->zona and $_SESSION['zona'] == 'Centro-Oeste'))?'selected':false)?>>Centro-Oeste</option>
                        <option value="Rural" <?=(($d->zona == 'Rural' or (!$d->zona and $_SESSION['zona'] == 'Rural'))?'selected':false)?>>Rural</option>
                    </select>
                    <label for="zona">Zona*</label>
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
                    <input type="hidden" name="tipo" value="<?=$_SESSION['tipo']?>" />
                    <input type="hidden" name="municipio" value="<?=$_SESSION['municipio']?>" />
                    <?php
                    if($_SESSION['municipio'] != '1'){
                    ?>
                    <input type="hidden" name="zona" value="<?=$_SESSION['zona']?>" />
                    <?php
                    }
                    ?>
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
                    url:"src/bairros/form.php",
                    type:"POST",
                    typeData:"JSON",
                    mimeType: 'multipart/form-data',
                    data: filds,
                    success:function(dados){
                        console.log(dados);
                        // if(dados.status){
                            $.ajax({
                                url:"src/bairros/index.php",
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