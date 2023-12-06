<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");

    if($_GET['n']){
      $_SESSION['calha_rio'] = false;
    }

    if($_POST['calha_rio']){
      if($_POST['calha_rio'] == 'geral') $_SESSION['calha_rio'] = false;
      else $_SESSION['calha_rio'] = $_POST['calha_rio'];
    }

    $_SESSION['tipo'] = false;
    $_SESSION['municipio'] = false;
    $_SESSION['municipio_nome'] = false;
    $_SESSION['zona'] = false;

    if($_POST['deletar']){
      // $query = "delete from municipios where codigo = '{$_POST['deletar']}'";
      $query = "update municipios set deletado = '1' where codigo = '{$_POST['deletar']}'";
      mysqli_query($con, $query);
    }

    if($_POST['acao'] == 'filtro'){
      $_SESSION['municipioBusca'] = $_POST['busca'];
    }
    if($_POST['acao'] == 'limpar'){
      $_SESSION['municipioBusca'] = false;      
    }

    $where = false;
    if($_SESSION['municipioBusca']){
      $where = " and nome like '%{$_SESSION['municipioBusca']}%'";
    }

?>


<style>
  td{
    white-space: nowrap;
  }
  .mh-1{
    margin-left:2px;
    margin-right:2px;
  }
  a[zona]{
    cursor:pointer;
  }
</style>
<div class="col">
  <div class="m-3">



  <div class="d-flex align-items-start">
  <div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
    <button calha_rio="geral" class="nav-link <?=((!$_SESSION['calha_rio'])?'active':false)?>" id="v-geral" data-bs-toggle="pill" data-bs-target="#v-principal" type="button" role="tab" aria-controls="v-geral" aria-selected="<?=((!$_SESSION['calha_rio'])?'true':false)?>">Calhas de Rio</button>
    <?php
    $q = "SELECT calha_rio FROM municipios group by calha_rio order by calha_rio asc";
    $r = mysqli_query($con, $q);
    while($s = mysqli_fetch_object($r)){
    ?>
    <button calha_rio="<?=$s->calha_rio?>" class="nav-link text-nowrap <?=(($_SESSION['calha_rio'] == $s->calha_rio)?'active':false)?>" id="v-<?=md5($s->calha_rio)?>" data-bs-toggle="pill" data-bs-target="#v-principal" type="button" role="tab" aria-controls="v-<?=md5($s->calha_rio)?>" aria-selected="<?=(($_SESSION['calha_rio'] == $s->calha_rio)?'true':false)?>"><?=$s->calha_rio?></button>
    <?php
    }
    ?>
  </div>
  <div class="tab-content w-100" id="v-pills-tabContent">
    <div class="tab-pane fade show active" id="v-principal" role="tabpanel" aria-labelledby="v-pills-home-tab" tabindex="0">

        <div class="row">
          <div class="col">
            <div class="card">
              <h5 class="card-header">Lista de Municípios <?=(($_SESSION['calha_rio'])?' / '.$_SESSION['calha_rio']:false)?></h5>
              <div class="card-body">

                <div class="input-group mb-3">
                  <label class="input-group-text" for="inputGroupFile01">Buscar por </label>
                  <input type="text" texto_busca<?=$md5?> class="form-control" value="<?=$_SESSION['municipioBusca']?>" aria-label="Digite a informação para a busca">
                  <button filtrar<?=$md5?> class="btn btn-outline-secondary" type="button">Buscar</button>
                  <button limpar<?=$md5?> class="btn btn-outline-danger" type="button">limpar</button>
                </div>
              
                <?php
                /*
                if($_SESSION['ProjectPainel']->codigo == 1 or $_SESSION['ProjectPainel']->perfil == 'adm'){
                ?>
                <div style="display:flex; justify-content:end">
                    <button
                        novoRegistro
                        class="btn btn-success btn-sm"
                        data-bs-toggle="offcanvas"
                        href="#offcanvasRight"
                        role="button"
                        aria-controls="offcanvasRight"
                    ><i class="fa-solid fa-plus"></i> Novo Cadastro</button>
                </div>
                <?php
                }
                //*/
                ?>
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th scope="col">Nome</th>
                      <th scope="col" class="text-end">Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $query = "select * from municipios where ".(($_SESSION['calha_rio'])?"calha_rio = '{$_SESSION['calha_rio']}' and ":false)." codigo != '66' and deletado != '1' {$where} order by nome asc";
                      $result = mysqli_query($con, $query);
                      while($d = mysqli_fetch_object($result)){
                    ?>
                    <tr>
                      <td><?=$d->nome?></td>
                      <td class="d-flex justify-content-end">

                        <?php
                        /*
                        if($d->codigo == 1){
                        ?>
                        <div class="dropdown mh-1">
                          <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-city"></i> Urbano
                          </button>
                          <ul class="dropdown-menu" urbano="<?=$d->codigo?>" municipio="<?=$d->nome?>">
                            <li><a class="dropdown-item" zona="Norte">Norte</a></li>
                            <li><a class="dropdown-item" zona="Leste">Leste</a></li>
                            <li><a class="dropdown-item" zona="Sul">Sul</a></li>
                            <li><a class="dropdown-item" zona="Oeste">Oeste</a></li>
                            <li><a class="dropdown-item" zona="Centro-Sul">Centro-Sul</a></li>
                            <li><a class="dropdown-item" zona="Centro-Oeste">Centro-Oeste</a></li>
                          </ul>
                        </div>
                        <?php
                        }else{
                          */
                        ?>
                        <button
                          class="btn btn-secondary btn-sm mh-1"
                          style="margin-bottom:1px"
                          urbano="<?=$d->codigo?>"
                          municipio="<?=$d->nome?>"
                        >
                        <i class="fa-solid fa-city"></i> Urbano
                        </button>
                        <?php
                        // }
                        ?>
                        <button
                          class="btn btn-primary btn-sm mh-1"
                          style="margin-bottom:1px"
                          rural="<?=$d->codigo?>"
                          municipio="<?=$d->nome?>"
                        >
                        <i class="fa-solid fa-tree"></i> Rural
                        </button>
                        <?php
                        /*
                        ?>
                        <button
                          class="btn btn-primary btn-sm mh-1"
                          style="margin-bottom:1px"
                          editar="<?=$d->codigo?>"
                          data-bs-toggle="offcanvas"
                          href="#offcanvasRight"
                          role="button"
                          aria-controls="offcanvasRight"
                        >
                        <i class="fa-solid fa-pen-to-square"></i> Editar
                        </button>
                        <?php
                        /*
                        if(
                              $_SESSION['ProjectPainel']->codigo == 1 or
                              $_SESSION['ProjectPainel']->perfil == 'adm'
                        ){
                        ?>
                        <button class="btn btn-sm btn-danger mh-1" deletar="<?=$d->codigo?>">
                        <i class="fa-solid fa-trash-can"></i> Deletar
                        </button>
                        <?php
                        }
                        //*/
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
              url:"src/municipios/index.php",
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
              url:"src/municipios/index.php",
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
            Carregando();
            $.ajax({
                url:"src/municipios/form.php",
                success:function(dados){
                    $(".MenuRight").html(dados);
                }
            })
        })

        $("button[calha_rio]").click(function(){
            Carregando();
            calha_rio = $(this).attr("calha_rio");
            $.ajax({
                url:"src/municipios/index.php",
                type:"POST",
                data:{
                  calha_rio
                },
                success:function(dados){
                  $("#pageHome").html(dados);
                }
            })
        })

        $("button[editar]").click(function(){
            Carregando();
            codigo = $(this).attr("editar");
            $.ajax({
                url:"src/municipios/form.php",
                type:"POST",
                data:{
                  codigo
                },
                success:function(dados){
                    $(".MenuRight").html(dados);
                }
            })
        })

        $("a[zona]").click(function(){
            Carregando();
            municipio = $(this).parent("li").parent("ul").attr("urbano");
            municipio_nome = $(this).parent("li").parent("ul").attr("municipio");
            zona = $(this).attr("zona");
            $.ajax({
                url:"src/bairros/index.php",
                type:"POST",
                data:{
                  tipo:'urbano',
                  municipio,
                  municipio_nome,
                  zona
                },
                success:function(dados){
                    $("#pageHome").html(dados);
                }
            })
        })

        $("button[urbano]").click(function(){
            Carregando();
            municipio = $(this).attr("urbano");
            municipio_nome = $(this).attr("municipio");
            $.ajax({
                url:"src/bairros/index.php",
                type:"POST",
                data:{
                  tipo:'urbano',
                  municipio,
                  municipio_nome
                },
                success:function(dados){
                    $("#pageHome").html(dados);
                }
            })
        })

        $("button[rural]").click(function(){
            Carregando();
            municipio = $(this).attr("rural");
            municipio_nome = $(this).attr("municipio");
            $.ajax({
                url:"src/bairros/index.php",
                type:"POST",
                data:{
                  tipo:'rural',
                  municipio,
                  municipio_nome
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
                        Carregando();
                        $.ajax({
                            url:"src/municipios/index.php",
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

    })
</script>