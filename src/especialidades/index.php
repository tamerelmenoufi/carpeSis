<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");

    // $_SESSION['categoria'] = false;
    // $_SESSION['esfera'] = false;

    if($_POST['categoria']){
      if($_POST['categoria'] == 'geral') {
        $_SESSION['categoria'] = false;
        $_SESSION['categoria_nome'] = false;
      }else {
        $_SESSION['categoria'] = $_POST['categoria'];
        $_SESSION['categoria_nome'] = $_POST['categoria_nome'];
      }
    }

    if($_POST['sub_categoria']){
      if($_POST['sub_categoria'] == 'geral') {
        $_SESSION['sub_categoria'] = false;
        $_SESSION['sub_categoria_nome'] = false;
      }else {
        $_SESSION['sub_categoria'] = $_POST['sub_categoria'];
        $_SESSION['sub_categoria_nome'] = $_POST['sub_categoria_nome'];
      }
    }


    if($_POST['deletar']){
      // $query = "delete from especialidades where codigo = '{$_POST['deletar']}'";
      $query = "update especialidades set deletado = '1' where codigo = '{$_POST['deletar']}'";
      mysqli_query($con, $query);
    }

    if($_POST['situacao']){
      $query = "update especialidades set situacao = '{$_POST['opc']}' where codigo = '{$_POST['situacao']}'";
      mysqli_query($con, $query);
      exit();
    }


    if($_POST['acao'] == 'filtro'){
      $_SESSION['especialidadeBusca'] = $_POST['busca'];
    }
    if($_POST['acao'] == 'limpar'){
      $_SESSION['especialidadeBusca'] = false;      
    }

    $where = false;
    if($_SESSION['especialidadeBusca']){
      $where = " and a.nome like '%{$_SESSION['especialidadeBusca']}%'";
    }
?>


<style>
  td{
    white-space: nowrap;
  }
</style>
<div class="col">
  <div class="m-3">


    <div class="row">
      <div class="col">
        <div class="card">
          <h5 class="card-header">
            <div class="d-flex justify-content-between">
              <span>Lista de Especialidades</span>
            </div>

          </h5>
          <div class="card-body">
            <?php
            if($_SESSION['ProjectPainel']->codigo == 1 or $_SESSION['ProjectPainel']->perfil == 'adm'){
            ?>
            <div style="display:flex; justify-content:end;">
                <button
                    novoRegistro
                    class="btn btn-success"
                    data-bs-toggle="offcanvas"
                    href="#offcanvasRight"
                    role="button"
                    aria-controls="offcanvasRight"
                >Novo Cadastro</button>
            </div>
            <?php
            }
            ?>

<div class="d-flex align-items-start mt-3">
  <div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
    <button categoria_conf class="btn btn-secondary btn-sm mb-3" data-bs-toggle="offcanvas" href="#offcanvasRight" role="button" aria-controls="offcanvasRight"><i class="fa-solid fa-gears"></i> Categorias</button>
    <button categoria="geral" class="nav-link <?=((!$_SESSION['categoria'])?'active':false)?>" id="v-geral" data-bs-toggle="pill" data-bs-target="#v-principal" type="button" role="tab" aria-controls="v-geral" aria-selected="<?=((!$_SESSION['categoria'])?'true':false)?>">Geral</button>
    <?php
    $q = "SELECT a.*, (select count(*) from sub_categorias where categoria = a.codigo and deletado != '1' and situacao = '1') as sub_categoria FROM categorias a where a.situacao = '1' and a.deletado != '1' order by a.nome asc";
    $r = mysqli_query($con, $q);
    while($s = mysqli_fetch_object($r)){
      if($s->sub_categoria){
    ?>
    <div class="accordion accordion-flush" id="accordionFlush<?=$s->codigo?>">
      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="<?=(($_SESSION['categoria'] == $s->codigo)?'true':'false')?>" aria-controls="flush-collapseOne">
            <?=$s->nome?>
          </button>
        </h2>
        <div id="flush-collapseOne" class="accordion-collapse collapse <?=(($_SESSION['categoria'] == $s->codigo)?'show':false)?>" data-bs-parent="#accordionFlush<?=$s->codigo?>">
          <div class="accordion-bodyX">
            <div class="list-group">

            <button 
              categoria="<?=$s->codigo?>" 
              categoria_nome = "<?=$s->nome?>"
              sub_categoria="geral" 
              sub_categoria_nome = "Geral" 
              type="button" 
              class="list-group-item list-group-item-action"
            >Geral</button>

            <?php
            $q1 = "SELECT * FROM sub_categorias where categoria = '{$s->codigo}' and deletado != '1' and situacao = '1' order by nome asc";
            $r1 = mysqli_query($con, $q1);
            while($s1 = mysqli_fetch_object($r1)){
            ?>
            <button 
              categoria="<?=$s->codigo?>" 
              categoria_nome = "<?=$s->nome?>" 
              sub_categoria="<?=$s1->codigo?>" 
              sub_categoria_nome = "<?=$s1->nome?>" 
              type="button" 
              class="list-group-item list-group-item-action"
            ><?=$s1->nome?></button>
            <?php
            }
            ?>   
            </div>         
          </div>
        </div>
      </div>
    </div>
    <?php
      }else{
    ?>
    <button categoria="<?=$s->codigo?>" categoria_nome = "<?=$s->nome?>" sub_categoria="geral" sub_categoria_nome = "Geral" class="nav-link text-nowrap <?=(($_SESSION['categoria'] == $s->codigo)?'active':false)?>" id="v-<?=md5($s->nome)?>" data-bs-toggle="pill" data-bs-target="#v-principal" type="button" role="tab" aria-controls="v-<?=md5($s->nome)?>" aria-selected="<?=(($_SESSION['categoria'] == $s->codigo)?'true':false)?>"><?=$s->nome?></button>
    <?php
      }
    }
    ?>
  </div>
  <div class="tab-content w-100" id="v-pills-tabContent">
    <div class="tab-pane fade show active" id="v-principal" role="tabpanel" aria-labelledby="v-pills-home-tab" tabindex="0">



          <div class="input-group mb-3">
            <label class="input-group-text" for="inputGroupFile01">Buscar por </label>
            <input type="text" texto_busca<?=$md5?> class="form-control" value="<?=$_SESSION['especialidadeBusca']?>" aria-label="Digite a informação para a busca">
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
                  $query = "select 
                                  a.*,
                                  b.nome as categoria_nome
                            from especialidades a 
                            left join categorias b on a.categoria = b.codigo 
                            where a.deletado != '1' {$where} ".
                            (($_SESSION['categoria'])?" and a.categoria = '{$_SESSION['categoria']}' ":false).
                            (($_SESSION['sub_categoria'])?" and a.sub_categoria = '{$_SESSION['sub_categoria']}' ":false).
                            "order by a.nome asc";
                  $result = mysqli_query($con, $query);
                  while($d = mysqli_fetch_object($result)){
                ?>
                <tr>
                  <td><?=$d->nome?></td>
                  <td>

                  <div class="form-check form-switch">
                    <input <?=(($_SESSION['ProjectPainel']->codigo == 1 or $_SESSION['ProjectPainel']->perfil == 'adm')?false:'disabled')?> class="form-check-input situacao" type="checkbox" <?=(($d->situacao)?'checked':false)?> usuario="<?=$d->codigo?>">
                  </div>

                  </td>
                  <td class="text-end">


                    <button
                      class="btn btn-primary"
                      style="margin-bottom:1px"
                      especialidade="<?=$d->codigo?>"
                      especialidade_nome="<?=$d->nome?>"
                      categoria="<?=$d->categoria?>"
                      categoria_nome="<?=$d->categoria_nome?>"
                    >
                    Órgãos
                    </button>
                    <?php
                    if(
                        (
                          $_SESSION['ProjectPainel']->codigo == 1 or
                          $_SESSION['ProjectPainel']->perfil == 'adm'
                        )
                    ){
                    ?>                   
                    <button
                      class="btn btn-primary"
                      style="margin-bottom:1px"
                      editar="<?=$d->codigo?>"
                      data-bs-toggle="offcanvas"
                      href="#offcanvasRight"
                      role="button"
                      aria-controls="offcanvasRight"
                    >
                    Editar
                    </button>
                    <button class="btn btn-danger" deletar="<?=$d->codigo?>">
                    Deletar
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
    </div>


  </div>
</div>


<script>
    $(function(){
        Carregando('none');

        $("button[limpar<?=$md5?>]").click(function(){
          Carregando()
          $.ajax({
              url:"src/especialidades/index.php",
              type:"POST",
              data:{
                acao:"limpar"
              },
              success:function(dados){
                $("#pageHome").html(dados);
              }
            });
        })

        $("button[filtrar<?=$md5?>]").click(function(){
          busca = $("input[texto_busca<?=$md5?>]").val();

          if(busca){
            // console.log(`campo:${campo} && Busca: ${busca}`);
            Carregando()
            $.ajax({
              url:"src/especialidades/index.php",
              type:"POST",
              data:{
                busca,
                acao:"filtro"
              },
              success:function(dados){
                $("#pageHome").html(dados);
              }
            });
          }else{
            $.alert('Favor preencher o campo da busca!')
          }

        });

        $("button[novoRegistro]").click(function(){
            $.ajax({
                url:"src/especialidades/form.php",
                success:function(dados){
                    $(".MenuRight").html(dados);
                }
            })
        })

        $("button[categoria_conf]").click(function(){
            $.ajax({
                url:"src/categorias/index.php",
                success:function(dados){
                    $(".MenuRight").html(dados);
                }
            })
        })

        $("button[editar]").click(function(){
            codigo = $(this).attr("editar");
            $.ajax({
                url:"src/especialidades/form.php",
                type:"POST",
                data:{
                  codigo
                },
                success:function(dados){
                    $(".MenuRight").html(dados);
                }
            })
        })


        $("button[categoria]").click(function(){
            categoria = $(this).attr("categoria");
            categoria_nome = $(this).attr("categoria_nome");
            
            sub_categoria = $(this).attr("sub_categoria");
            sub_categoria_nome = $(this).attr("sub_categoria_nome");
            
            $.ajax({
                url:"src/especialidades/index.php",
                type:"POST",
                data:{
                  categoria,
                  categoria_nome,
                  sub_categoria,
                  sub_categoria_nome
                },
                success:function(dados){
                  $("#pageHome").html(dados);
                }
            })
        })

        $("button[especialidade]").click(function(){
            especialidade = $(this).attr("especialidade");
            especialidade_nome = $(this).attr("especialidade_nome");

            categoria = $(this).attr("categoria");
            categoria_nome = $(this).attr("categoria_nome");

            $.ajax({
                url:"src/orgaos/index.php",
                type:"POST",
                data:{
                  especialidade,
                  especialidade_nome,
                  categoria,
                  categoria_nome
                },
                success:function(dados){
                  $("#pageHome").html(dados);
                }
            })
        })

        $("button[deletar]").click(function(){
            deletar = $(this).attr("deletar");
            $.confirm({
                content:"Você tem certeza que quer deletar o registro?",
                title:false,
                buttons:{
                    'Sim':function(){
                        $.ajax({
                            url:"src/especialidades/index.php",
                            type:"POST",
                            data:{
                                deletar
                            },
                            success:function(dados){
                              // $.alert(dados);
                              $("#pageHome").html(dados);
                            }
                        })
                    },
                    'Não':function(){

                    }
                }
            });

        })


        $(".situacao").change(function(){

            situacao = $(this).attr("usuario");
            opc = false;

            if($(this).prop("checked") == true){
              opc = '1';
            }else{
              opc = '0';
            }


            $.ajax({
                url:"src/especialidades/index.php",
                type:"POST",
                data:{
                    situacao,
                    opc
                },
                success:function(dados){
                    // $("#pageHome").html(dados);
                }
            })

        });

    })
</script>