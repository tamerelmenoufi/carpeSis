<?php
        include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");

        $_SESSION['categoria'] = false;
        $_SESSION['esfera'] = false;


    if($_POST['deletar']){
      // $query = "delete from categorias where codigo = '{$_POST['deletar']}'";
      $query = "update categorias set deletado = '1' where codigo = '{$_POST['deletar']}'";
      mysqli_query($con, $query);
    }

    if($_POST['situacao']){
      $query = "update categorias set situacao = '{$_POST['opc']}' where codigo = '{$_POST['situacao']}'";
      mysqli_query($con, $query);
      exit();
    }

    if($_POST['acao'] == 'filtro'){
      $_SESSION['categoriaBusca'] = $_POST['busca'];
    }
    if($_POST['acao'] == 'limpar'){
      $_SESSION['categoriaBusca'] = false;      
    }

    $where = false;
    if($_SESSION['categoriaBusca']){
      $where = " and a.nome like '%{$_SESSION['categoriaBusca']}%'";
    }
?>


<style>
  td{
    white-space: nowrap;
  }
  .Titulo<?=$md5?>{
      position:absolute;
      left:60px;
      top:8px;
      z-index:0;
  }
</style>
<h4 class="Titulo<?=$md5?>">Lista de Categorias</h4>
<div class="col">
  <div class="m-3">
    <div class="row">
      <div class="col">
        <div class="card">
          <!-- <h5 class="card-header">
            <div class="d-flex justify-content-between">
              <span>Lista de Categorias</span>
            </div>

          </h5> -->
          <div class="card-body">
            <?php
            if($_SESSION['ProjectPainel']->codigo == 1 or $_SESSION['ProjectPainel']->perfil == 'adm'){
            ?>
            <div style="display:flex; justify-content:end; margin-bottom:10px;">
                <button
                    novoRegistro<?=$md5?>
                    class="btn btn-success"
                    XXXdata-bs-toggle="offcanvas"
                    XXXhref="#offcanvasRight"
                    XXXrole="button"
                    XXXaria-controls="offcanvasRight"
                >Novo Cadastro</button>
            </div>
            <?php
            }
            ?>

            <div class="input-group mb-3">
              <label class="input-group-text" for="inputGroupFile01">Buscar por </label>
              <input type="text" texto_busca<?=$md5?> class="form-control" value="<?=$_SESSION['categoriaBusca']?>" aria-label="Digite a informação para a busca">
              <button filtrar<?=$md5?> class="btn btn-outline-secondary" type="button">Buscar</button>
              <button limpar<?=$md5?> class="btn btn-outline-danger" type="button">limpar</button>
            </div>

          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th scope="col">Nome</th>
                  <th scope="col">Situacao</th>
                  <th scope="col" class="text-end">Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $query = "select a.*, (select count(*) from sub_categorias where categoria = a.codigo and deletado != '1') as qt from categorias a where a.deletado != '1' {$where} order by a.nome asc";
                  $result = mysqli_query($con, $query);
                  while($d = mysqli_fetch_object($result)){
                ?>
                <tr>
                  <td><?=$d->nome?></td>
                  <td>

                  <div class="form-check form-switch">
                    <input <?=(($_SESSION['ProjectPainel']->codigo == 1 or $_SESSION['ProjectPainel']->perfil == 'adm')?false:'disabled')?> class="form-check-input situacao<?=$md5?>" type="checkbox" <?=(($d->situacao)?'checked':false)?> usuario="<?=$d->codigo?>">
                  </div>

                  </td>
                  <td class="text-end">
                    <?php
                    if(
                        (
                          $_SESSION['ProjectPainel']->codigo == 1 or
                          $_SESSION['ProjectPainel']->perfil == 'adm'
                        )
                    ){
                    ?>

                    <button
                      class="btn btn-warning"
                      style="margin-bottom:1px"
                      categoria<?=$md5?>="<?=$d->codigo?>"
                      categoria_nome<?=$md5?>="<?=$d->nome?>"
                    >
                    <?=str_pad($d->qt , 3 , '0' , STR_PAD_LEFT)?>
                    <i class="fa-solid fa-bars-staggered"></i>
                    </button>
                    <button
                      class="btn btn-primary"
                      style="margin-bottom:1px"
                      editar<?=$md5?>="<?=$d->codigo?>"
                      XXXdata-bs-toggle="offcanvas"
                      XXXhref="#offcanvasRight"
                      XXXrole="button"
                      XXXaria-controls="offcanvasRight"
                    >
                    <i class="fa fa-edit"></i>
                    </button>
                    <button class="btn btn-danger" deletar<?=$md5?>="<?=$d->codigo?>">
                    <i class="fa fa-trash"></i>
                    </button>
                    <?php
                    }
                    ?>
                  </td>
                </tr>
                <?php
                  }
                ?>
              </tbody>
            </table>
                </div>
          </div>
        </div>
      </div>
    </div>


  </div>
</div>


<script>
    $(function(){
        Carregando('none');

        $("button[limpar<?=$md5?>]").click(function(){
          Carregando()
          $.ajax({
              url:"src/categorias/index.php",
              type:"POST",
              data:{
                acao:"limpar"
              },
              success:function(dados){
                $(".MenuRight").html(dados);
              }
            });
        })

        $("button[filtrar<?=$md5?>]").click(function(){
          busca = $("input[texto_busca<?=$md5?>]").val();

          if(busca){
            // console.log(`campo:${campo} && Busca: ${busca}`);
            Carregando()
            $.ajax({
              url:"src/categorias/index.php",
              type:"POST",
              data:{
                busca,
                acao:"filtro"
              },
              success:function(dados){
                $(".MenuRight").html(dados);
              }
            });
          }else{
            $.alert('Favor preencher o campo da busca!')
          }

        });

        $("button[novoRegistro<?=$md5?>]").click(function(){
            $.ajax({
                url:"src/categorias/form.php",
                success:function(dados){
                    $(".MenuRight").html(dados);
                }
            })
        })

        $("button[categoria<?=$md5?>]").click(function(){
          categoria = $(this).attr("categoria<?=$md5?>");
          categoria_nome = $(this).attr("categoria_nome<?=$md5?>");
            $.ajax({
                url:"src/categorias/sub_categorias.php",
                type:"POST",
                data:{
                  categoria,
                  categoria_nome
                },
                success:function(dados){
                    $(".MenuRight").html(dados);
                }
            })
        })

        $("button[editar<?=$md5?>]").click(function(){
            codigo = $(this).attr("editar<?=$md5?>");
            $.ajax({
                url:"src/categorias/form.php",
                type:"POST",
                data:{
                  codigo
                },
                success:function(dados){
                    $(".MenuRight").html(dados);
                }
            })
        })


        $("button[deletar<?=$md5?>]").click(function(){
            deletar = $(this).attr("deletar<?=$md5?>");
            $.confirm({
                content:"Você tem certeza que quer deletar o registro?",
                title:false,
                buttons:{
                    'Sim':function(){
                        $.ajax({
                            url:"src/categorias/index.php",
                            type:"POST",
                            data:{
                                deletar
                            },
                            success:function(dados){
                              // $.alert(dados);
                              $(".MenuRight").html(dados);

                              $.ajax({
                                  url:"src/especialidades/index.php",
                                  success:function(dados){
                                    // $.alert(dados);
                                    $("#pageHome").html(dados);
                                  }
                              })

                            }
                        })
                    },
                    'Não':function(){

                    }
                }
            });

        })


        $(".situacao<?=$md5?>").change(function(){

            situacao = $(this).attr("usuario");
            opc = false;

            if($(this).prop("checked") == true){
              opc = '1';
            }else{
              opc = '0';
            }


            $.ajax({
                url:"src/categorias/index.php",
                type:"POST",
                data:{
                    situacao,
                    opc
                },
                success:function(dados){
                    // $("#pageHome").html(dados);
                    $.ajax({
                        url:"src/especialidades/index.php",
                        success:function(dados){
                            $("#pageHome").html(dados);
                        }
                    });
                }
            })

        });

    })
</script>