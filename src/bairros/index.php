<?php
        include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");

        if($_GET['n']){
            $_SESSION['tipo'] = false;
            $_POST['municipio'] = 66;
            $_SESSION['municipio_nome'] = 'Manaus';
            $_POST['zona'] = 'geral';
        }

        if($_POST['tipo']) $_SESSION['tipo'] = $_POST['tipo'];
        if($_POST['municipio']) $_SESSION['municipio'] = $_POST['municipio'];
        if($_POST['municipio_nome']) $_SESSION['municipio_nome'] = $_POST['municipio_nome'];
        if($_POST['zona']) $_SESSION['zona'] = $_POST['zona'];

        if($_POST['zona'] == 'geral') {
          $_SESSION['zona'] = false;
          $_SESSION['tipo'] = false;
        }else if($_POST['zona']){
          $_SESSION['zona'] = $_POST['zona'];
        }

    if($_POST['deletar']){
      // $query = "delete from bairros where codigo = '{$_POST['deletar']}'";
      $query = "update bairros set deletado = '1' where codigo = '{$_POST['deletar']}'";
      mysqli_query($con, $query);
    }

    if($_POST['acao'] == 'filtro'){
      $_SESSION['bairroBusca'] = $_POST['busca'];
    }
    if($_POST['acao'] == 'limpar'){
      $_SESSION['bairroBusca'] = false;      
    }

    $where = false;
    if($_SESSION['bairroBusca']){
      $where = " and nome like '%{$_SESSION['bairroBusca']}%'";
    }

?>


<style>
  td{
    white-space: nowrap;
  }
  #nav-principal{
    border-right:1px solid #dee2e6;
    border-left:1px solid #dee2e6;
    border-bottom:1px solid #dee2e6;
  }
</style>
<div class="col">
  <div class="m-3">


    <div class="row">
      <div class="col">
        <div class="card">
          <h5 class="card-header">
            <div class="d-flex justify-content-between">
              <span><?=(($_SESSION['tipo'] == 'urbano')?'Lista de Bairros / ':(($_SESSION['tipo'])?'Lista das Comunidades / ':false))?><?=$_SESSION['municipio_nome']?><?=(($_SESSION['tipo'] == 'urbano')?' / '.strtoupper($_SESSION['tipo']):false).(($_SESSION['zona'])?' / '.$_SESSION['zona']:false)?></span>
              <?php
              if($_SESSION['municipio'] != '66'){
              ?>
              <button class="btn btn-secondary btn-sm" voltar><i class="fa-solid fa-angles-left"></i> Voltar</button>
              <?php
              }
              ?>
            </div>
          </h5>
          <div class="card-body">

          <div class="card-body">

<?php
if($_SESSION['municipio'] == '66'){
?>

<nav>
  <div class="nav nav-tabs" id="nav-tab" role="tablist">
    <button zona="geral" tipo="" class="nav-link <?=((!$_SESSION['zona'])?'active':false)?>" id="geral" data-bs-toggle="tab" data-bs-target="#nav-geral" type="button" role="tab" aria-controls="nav-principal" aria-selected="<?=(!($_SESSION['zona'])?'true':'false')?>">Zonas</button>
    <button zona="Norte" tipo="urbano" class="nav-link <?=(($_SESSION['zona'] == 'Norte')?'active':false)?>" id="norte" data-bs-toggle="tab" data-bs-target="#nav-norte" type="button" role="tab" aria-controls="nav-principal" aria-selected="<?=(($_SESSION['zona'] == 'Norte')?'true':'false')?>"><span class="d-none d-sm-block">Norte</span><span class="d-block d-sm-none">N</span></button>
    <button zona="Leste" tipo="urbano" class="nav-link <?=(($_SESSION['zona'] == 'Leste')?'active':false)?>" id="leste" data-bs-toggle="tab" data-bs-target="#nav-leste" type="button" role="tab" aria-controls="nav-principal" aria-selected="<?=(($_SESSION['zona'] == 'Leste')?'true':'false')?>"><span class="d-none d-sm-block">Leste</span><span class="d-block d-sm-none">L</span></button>
    <button zona="Sul" tipo="urbano" class="nav-link <?=(($_SESSION['zona'] == 'Sul')?'active':false)?>" id="sul" data-bs-toggle="tab" data-bs-target="#nav-sul" type="button" role="tab" aria-controls="nav-principal" aria-selected="<?=(($_SESSION['zona'] == 'Sul')?'true':'false')?>"><span class="d-none d-sm-block">Sul</span><span class="d-block d-sm-none">S</span></button>
    <button zona="Oeste" tipo="urbano" class="nav-link <?=(($_SESSION['zona'] == 'Oeste')?'active':false)?>" id="oeste" data-bs-toggle="tab" data-bs-target="#nav-oeste" type="button" role="tab" aria-controls="nav-principal" aria-selected="<?=(($_SESSION['zona'] == 'Oeste')?'true':'false')?>"><span class="d-none d-sm-block">Oeste</span><span class="d-block d-sm-none">O</span></button>
    <button zona="Centro-Sul" tipo="urbano" class="nav-link <?=(($_SESSION['zona'] == 'Centro-Sul')?'active':false)?>" id="centro_sul" data-bs-toggle="tab" data-bs-target="#nav-centro-sul" type="button" role="tab" aria-controls="nav-principal" aria-selected="<?=(($_SESSION['zona'] == 'Centro-Sul')?'true':'false')?>"><span class="d-none d-sm-block">Centro-Sul</span><span class="d-block d-sm-none">C-S</span></button>
    <button zona="Centro-Oeste" tipo="urbano" class="nav-link <?=(($_SESSION['zona'] == 'Centro-Oeste')?'active':false)?>" id="centro_oeste" data-bs-toggle="tab" data-bs-target="#nav-centro-oeste" type="button" role="tab" aria-controls="nav-principal" aria-selected="<?=(($_SESSION['zona'] == 'Centro-Oeste')?'true':'false')?>"><span class="d-none d-sm-block">Centro-Oeste</span><span class="d-block d-sm-none">C-O</span></button>
    <button zona="Rural" tipo="rural" class="nav-link <?=(($_SESSION['zona'] == 'Rural')?'active':false)?>" id="rural" data-bs-toggle="tab" data-bs-target="#nav-rural" type="button" role="tab" aria-controls="nav-principal" aria-selected="<?=(($_SESSION['zona'] == 'Rural')?'true':'false')?>"><span class="d-none d-sm-block">Rural</span><span class="d-block d-sm-none">Z-R</span></button>
  </div>
</nav>
<div class="tab-content" id="nav-tabContent">
  <div class="tab-pane fade show active p-3" id="nav-principal" role="tabpanel" aria-labelledby="<?=$_SESSION['esfera']?>" tabindex="0">

<?php
}
?>

                <div class="input-group mb-3">
                  <label class="input-group-text" for="inputGroupFile01">Buscar por </label>
                  <input type="text" texto_busca<?=$md5?> class="form-control" value="<?=$_SESSION['bairroBusca']?>" aria-label="Digite a informação para a busca">
                  <button filtrar<?=$md5?> class="btn btn-outline-secondary" type="button">Buscar</button>
                  <button limpar<?=$md5?> class="btn btn-outline-danger" type="button">limpar</button>
                </div>

            <?php
            if($_SESSION['ProjectPainel']->codigo == 1 or $_SESSION['ProjectPainel']->perfil == 'adm'){
            ?>
            <div style="display:flex; justify-content:end">
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
                  $query = "select * from bairros where
                              municipio = '{$_SESSION['municipio']}' and
                              ".(($_SESSION['tipo'])?"tipo = '{$_SESSION['tipo']}' and ":false)."
                              ".(($_SESSION['zona'])?"zona = '{$_SESSION['zona']}' and ":false)."
                              deletado != '1' {$where} order by nome asc";
                  $result = mysqli_query($con, $query);
                  while($d = mysqli_fetch_object($result)){
                ?>
                <tr>
                  <td><?=$d->nome?></td>
                  <td class="text-end">
                    <?php
                    if(
                          $_SESSION['ProjectPainel']->codigo == 1 or
                          $_SESSION['ProjectPainel']->perfil == 'adm'
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
    <?php
    if($_SESSION['municipio'] == '66' and $_SESSION['tipo'] == 'urbano'){
    ?>
    </div>
    </div>
    <?php
    }
    ?>

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
              url:"src/bairros/index.php",
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
              url:"src/bairros/index.php",
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
                url:"src/bairros/form.php",
                success:function(dados){
                    $(".MenuRight").html(dados);
                }
            })
        })


        $("button[zona]").click(function(){
            zona = $(this).attr("zona");
            tipo = $(this).attr("tipo");
            Carregando();
            $.ajax({
                url:"src/bairros/index.php",
                type:"POST",
                data:{
                  zona,
                  tipo
                },
                success:function(dados){
                    $("#pageHome").html(dados);
                }
            })
        })

        $("button[voltar]").click(function(){
            Carregando();
            $.ajax({
                url:"src/municipios/index.php",
                type:"POST",
                success:function(dados){
                    $("#pageHome").html(dados);
                }
            })
        })

        $("button[editar]").click(function(){
            Carregando();
            codigo = $(this).attr("editar");
            $.ajax({
                url:"src/bairros/form.php",
                type:"POST",
                data:{
                  codigo
                },
                success:function(dados){
                    $(".MenuRight").html(dados);
                }
            })
        })
        //Teste

        $("button[deletar]").click(function(){
            deletar = $(this).attr("deletar");
            $.confirm({
                content:"Você tem certeza que quer deletar o registro?",
                title:false,
                buttons:{
                    'Sim':function(){
                        Carregando();
                        $.ajax({
                            url:"src/bairros/index.php",
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