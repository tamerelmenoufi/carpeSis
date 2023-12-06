<?php
        include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");

        if($_POST['acao'] == 'lista'){
          if($_POST['lista']){
            $_SESSION['filtroLista'] = $_POST['lista'];
          }else{
            $_SESSION['filtroLista'] = [];
          }

          exit();
        }

        if($_POST['limparFiltrarBeneficiados']){
          $_SESSION['filtroBeneficiados'] = [];
        }

        if(!$_SESSION['filtroLista']){
          $_SESSION['filtroLista'] = [];
        }

        if($_POST['deletar']){
          // $query = "delete from beneficiados where codigo = '{$_POST['deletar']}'";
          $query = "update beneficiados set deletado = '1' where codigo = '{$_POST['deletar']}'";
          mysqli_query($con, $query);
        }

        if($_POST['situacao']){
          $query = "update beneficiados set situacao = '{$_POST['opc']}' where codigo = '{$_POST['situacao']}'";
          mysqli_query($con, $query);
          exit();
        }

        $filtro = false;
        $filtro2 = false;
        $ignore = ['categoria','sub_categoria','especialidade'];
        if($_SESSION['filtroBeneficiados']){
          $filtro = [];
          $filtro2 = [];
          foreach($_SESSION['filtroBeneficiados'] as $i => $v){
            if(!in_array($i,$ignore)){
              $filtro[] = "a.{$i} = '{$v}'"; 
            }else{
              $filtro2[] = "e.{$i} = '{$v}'"; 
            }
          }
          if($filtro) $filtro = implode(" and ", $filtro);
          if($filtro2) $filtro2 = implode(" and ", $filtro2);
        }

        if($_POST['acao'] == 'filtro'){
          $_SESSION['beneficiadosBusca'] = $_POST['busca'];
        }
        if($_POST['acao'] == 'limpar'){
          $_SESSION['beneficiadosBusca'] = false;
          $_SESSION['filtroLista'] = [];    
        }
    
        $where = false;
        if($_SESSION['beneficiadosBusca']){
          $where = " and a.nome like '%{$_SESSION['beneficiadosBusca']}%'";
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
              <span>Lista de Beneficiados</span>
            </div>
          </h5>
          <div class="card-body">

              <?php
              if($_SESSION['ProjectPainel']->codigo == 1 or $_SESSION['ProjectPainel']->perfil == 'adm' or $_SESSION['ProjectPainel']->perfil == 'assessor'){
              ?>
              <div class="d-flex justify-content-between">
                <div>
                  <button
                      EnviarMensagem
                      class="btn btn-success"
                      data-bs-toggle="offcanvas"
                      href="#offcanvasRight"
                      role="button"
                      aria-controls="offcanvasRight"
                  >Enviar Manesagem</button>
                </div>

                <div>
                  <div class="input-group">
                    <label class="input-group-text" for="inputGroupFile01">Busca rápida </label>
                    <input type="text" texto_busca<?=$md5?> class="form-control" value="<?=$_SESSION['beneficiadosBusca']?>" aria-label="Digite a informação para a busca">
                    <button filtrar<?=$md5?> class="btn btn-outline-secondary" type="button">Buscar</button>
                    <button limpar<?=$md5?> class="btn btn-outline-danger" type="button">limpar</button>
                  </div>
                </div>

                <div>
                  <button
                      limparFiltrarBeneficiados
                      class="btn btn-danger"
                      style="margin-right:10px;"
                  >Limpar Filtro</button>
                  <button
                      filtrarBeneficiados
                      class="btn btn-warning"
                      data-bs-toggle="offcanvas"
                      href="#offcanvasRight"
                      role="button"
                      aria-controls="offcanvasRight"
                      style="margin-right:10px;"
                  >Filtro</button>
                  <button
                      novoRegistro
                      class="btn btn-success"
                      data-bs-toggle="offcanvas"
                      href="#offcanvasRight"
                      role="button"
                      aria-controls="offcanvasRight"
                  >Novo Cadastro</button>
                </div>
              </div>
              <?php
              }
              ?>

              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th scope="col">
                        <input type="checkbox" todos>
                      </th>
                      <th scope="col">Nome</th>
                      <th scope="col">Cidade</th>
                      <th scope="col">Bairro</th>
                      <th scope="col">Zona</th>
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
                                      d.nome as lider
                                from beneficiados a
                                  left join bairros b on a.bairro = b.codigo
                                  left join municipios c on a.municipio = c.codigo
                                  left join usuarios d on a.assessor = d.codigo
                                  left join servicos e on a.codigo = e.beneficiado
                                where a.deletado != '1' {$where} ".(($filtro)?" and {$filtro}":false).(($filtro2)?" and {$filtro2}":false).(($_SESSION['ProjectPainel']->perfil != 'adm')? " and a.assessor = '{$_SESSION['ProjectPainel']->codigo}' ":false)."
                                group by a.codigo order by a.nome asc limit 1000";
                      $result = mysqli_query($con, $query);
                      while($d = mysqli_fetch_object($result)){
                    ?>
                    <tr>
                      <td>
                        <input type="checkbox" value="<?=$d->codigo?>" <?=(in_array($d->codigo, $_SESSION['filtroLista'])?'checked':false)?> lista>
                      </td>
                      <td><?=$d->nome?><br><small style="color:#a1a1a1"><b>Líder: </b><?=(($d->lider)?:'Não definido')?></small></td>
                      <td><?=$d->cidade_nome?></td>
                      <td><?=$d->bairro_nome?></td>
                      <td><?=$d->zona?></td>
                      <td>

                      <div class="form-check form-switch">
                        <input <?=(($_SESSION['ProjectPainel']->codigo == 1 or $_SESSION['ProjectPainel']->perfil == 'adm')?false:'disabled')?> class="form-check-input situacao" type="checkbox" <?=(($d->situacao)?'checked':false)?> usuario="<?=$d->codigo?>">
                      </div>

                      </td>
                      <td class="text-end">
                        <button
                          class="btn btn-warning"
                          style="margin-bottom:1px"
                          solicitacoes="<?=$d->codigo?>"
                        >
                        Solicitações
                        </button>
                        <?php
                        if(
                            (
                              $_SESSION['ProjectPainel']->codigo == 1 or
                              $_SESSION['ProjectPainel']->perfil == 'adm' or
                              $_SESSION['ProjectPainel']->perfil == 'assessor'
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
                        <?php
                        }
                        if(
                            (
                              $_SESSION['ProjectPainel']->codigo == 1 or
                              $_SESSION['ProjectPainel']->perfil == 'adm'
                            )
                        ){
                        ?>
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


<script>
    $(function(){
        Carregando('none');

        $("button[limpar<?=$md5?>]").click(function(){
          Carregando()
          $.ajax({
              url:"src/beneficiados/index.php",
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
              url:"src/beneficiados/index.php",
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

        $("input[todos]").click(function(){
          
          if($(this).prop("checked") == true){
            $("input[lista]").prop("checked", true);
            
          }else{
            $("input[lista]").prop("checked", false);
          }

          lista = [];
          $("input[lista]").each(function(){
              if($(this).prop("checked") == true){
                lista.push($(this).val());
              }
          })

          $.ajax({
              url:"src/beneficiados/index.php",
              type:"POST",
              data:{
                acao:'lista',
                lista
              },
              success:function(dados){
                  // console.log(`Retorno sessão: ${dados}`)
              }
          })
          // console.log(lista)

        });

        $("input[lista]").click(function(){
          
          lista = [];
          $("input[lista]").each(function(){
              if($(this).prop("checked") == true){
                lista.push($(this).val());
              }
          })

          $.ajax({
                url:"src/beneficiados/index.php",
                type:"POST",
                data:{
                  acao:'lista',
                  lista
                },
                success:function(dados){
                    // console.log(`Retorno sessão: ${dados}`)
                }
            })

          // console.log(lista)

        });
        


        $("button[novoRegistro]").click(function(){
            Carregando()
            $.ajax({
                url:"src/beneficiados/form.php",
                success:function(dados){
                    $(".MenuRight").html(dados);
                }
            })
        })

        $("button[EnviarMensagem]").click(function(){
            Carregando()
            $.ajax({
                url:"src/beneficiados/wapp.php",
                success:function(dados){
                    $(".MenuRight").html(dados);
                }
            })
        })
      
        $("button[filtrarBeneficiados]").click(function(){
            Carregando()
            $.ajax({
                url:"src/beneficiados/filtro_form.php",
                success:function(dados){
                    $(".MenuRight").html(dados);
                }
            })
        })

        $("button[limparFiltrarBeneficiados]").click(function(){
            Carregando()
            $.ajax({
                url:"src/beneficiados/index.php",
                type:"POST",
                data:{
                  limparFiltrarBeneficiados:'1'
                },
                success:function(dados){
                  $("#pageHome").html(dados);
                }
            })
        })
      

        $("button[solicitacoes]").click(function(){
            Carregando();
            beneficiado = $(this).attr("solicitacoes");
            $.ajax({
                url:"src/servicos/index.php",
                type:"POST",
                data:{
                  beneficiado
                },
                success:function(dados){
                  $("#pageHome").html(dados);
                }
            })
        })

        $("button[editar]").click(function(){
            codigo = $(this).attr("editar");
            $.ajax({
                url:"src/beneficiados/form.php",
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
                            url:"src/beneficiados/index.php",
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
                url:"src/beneficiados/index.php",
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