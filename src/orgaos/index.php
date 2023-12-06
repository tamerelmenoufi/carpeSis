<?php
        include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");

        if($_POST['especialidade']){
          if($_POST['especialidade'] == 'geral') {
            $_SESSION['especialidade'] = false;
            $_SESSION['especialidade_nome'] = false;
          }else {
            $_SESSION['especialidade'] = $_POST['especialidade'];
            $_SESSION['especialidade_nome'] = $_POST['especialidade_nome'];
          }
        }

        if($_POST['esfera'] == 'geral') {
          $_SESSION['esfera'] = false;
        }else if($_POST['esfera']){
          $_SESSION['esfera'] = $_POST['esfera'];
        }

        // if(!$_SESSION['esfera']){
        //   $_SESSION['esfera'] = 'estadual';
        // }

        if($_POST['deletar']){
          // $query = "delete from orgaos where codigo = '{$_POST['deletar']}'";
          $query = "update orgaos set 
                                      categoria = JSON_REMOVE(categoria, '$.cat{$_SESSION['categoria']}'),
                                      especialidade = JSON_REMOVE(especialidade, '$.esp{$_SESSION['especialidade']}')
                    where codigo = '{$_POST['deletar']}'";
          mysqli_query($con, $query);
        }

        if($_POST['situacao']){
          $query = "update orgaos set situacao = '{$_POST['opc']}' where codigo = '{$_POST['situacao']}'";
          mysqli_query($con, $query);
          exit();
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
              <span>Lista de Órgãos / <?=$_SESSION['categoria_nome']?> / <?=$_SESSION['especialidade_nome']?><?=(($_SESSION['esfera'])?" / Esfera - {$_SESSION['esfera']}":false)?></span>
              <button class="btn btn-secondary btn-sm" voltar><i class="fa-solid fa-angles-left"></i> Voltar</button>
            </div>
          </h5>
          <div class="card-body">

          <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
              <button esfera="geral" class="nav-link <?=((!$_SESSION['esfera'])?'active':false)?>" id="geral" data-bs-toggle="tab" data-bs-target="#nav-geral" type="button" role="tab" aria-controls="nav-principal" aria-selected="<?=(!($_SESSION['esfera'])?'true':'false')?>">Geral</button>
              <button esfera="estadual" class="nav-link <?=(($_SESSION['esfera'] == 'estadual')?'active':false)?>" id="estadual" data-bs-toggle="tab" data-bs-target="#nav-estadual" type="button" role="tab" aria-controls="nav-principal" aria-selected="<?=(($_SESSION['esfera'] == 'estadual')?'true':'false')?>">Estadual</button>
              <button esfera="municipal" class="nav-link <?=(($_SESSION['esfera'] == 'municipal')?'active':false)?>" id="municipal" data-bs-toggle="tab" data-bs-target="#nav-municipal" type="button" role="tab" aria-controls="nav-principal" aria-selected="<?=(($_SESSION['esfera'] == 'municipal')?'true':'false')?>">Municipal</button>
              <button esfera="particular" class="nav-link <?=(($_SESSION['esfera'] == 'particular')?'active':false)?>" id="particular" data-bs-toggle="tab" data-bs-target="#nav-particular" type="button" role="tab" aria-controls="nav-principal" aria-selected="<?=(($_SESSION['esfera'] == 'particular')?'true':'false')?>">Particular</button>
            </div>
          </nav>
          <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active p-3" id="nav-principal" role="tabpanel" aria-labelledby="<?=$_SESSION['esfera']?>" tabindex="0">

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
                      <th scope="col">Cidade</th>
                      <th scope="col">Bairro</th>
                      <th scope="col">Zona</th>
                      <th scope="col">Esfera</th>
                      <th scope="col">Situacao</th>
                      <th scope="col" class="text-end">Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $query = "select
                                      a.*,
                                      b.nome as bairro_nome,
                                      b.zona,
                                      c.nome as cidade_nome,
                                      a.esfera
                                from orgaos a
                                  left join bairros b on a.bairro = b.codigo
                                  left join municipios c on a.cidade = c.codigo
                                where
                                      a.categoria->'$.cat{$_SESSION['categoria']}' = {$_SESSION['categoria']} and 
                                      a.especialidade->'$.esp{$_SESSION['especialidade']}' = {$_SESSION['especialidade']}
                                      ".(($_SESSION['esfera'])?" and a.esfera = '{$_SESSION['esfera']}'":false)."
                                order by a.nome asc";
                      $result = mysqli_query($con, $query);
                      while($d = mysqli_fetch_object($result)){
                    ?>
                    <tr>
                      <td><?=$d->nome?></td>
                      <td><?=$d->cidade_nome?></td>
                      <td><?=$d->bairro_nome?></td>
                      <td><?=$d->zona?></td>
                      <td><?=$d->esfera?></td>
                      <td>

                      <div class="form-check form-switch">
                        <input <?=(($_SESSION['ProjectPainel']->codigo == 1 or $_SESSION['ProjectPainel']->perfil == 'adm')?false:'disabled')?> class="form-check-input situacao" type="checkbox" <?=(($d->situacao)?'checked':false)?> usuario="<?=$d->codigo?>">
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


<script>
    $(function(){
        Carregando('none');
        $("button[novoRegistro]").click(function(){
            $.ajax({
                url:"src/orgaos/form.php",
                success:function(dados){
                    $(".MenuRight").html(dados);
                }
            })
        })

        $("button[voltar]").click(function(){
            Carregando();
            $.ajax({
                url:"src/especialidades/index.php",
                type:"POST",
                success:function(dados){
                    $("#pageHome").html(dados);
                }
            })
        })

        $("button[esfera]").click(function(){
            esfera = $(this).attr("esfera");
            Carregando();
            $.ajax({
                url:"src/orgaos/index.php",
                type:"POST",
                data:{
                  esfera
                },
                success:function(dados){
                    $("#pageHome").html(dados);
                }
            })
        })

        $("button[editar]").click(function(){
            codigo = $(this).attr("editar");
            $.ajax({
                url:"src/orgaos/form.php",
                type:"POST",
                data:{
                  codigo
                },
                success:function(dados){
                    $(".MenuRight").html(dados);
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
                            url:"src/orgaos/index.php",
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
                url:"src/orgaos/index.php",
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