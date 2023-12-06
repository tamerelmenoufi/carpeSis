<?php
        include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");

        $Perfil = [
          'assessor' => 'Liderança (Solicitações)',
          'assessor2' => 'Atendimento (Atende as Solicitações)',
          'assessor3' => 'Supervisor (Valida e Finaliza)',
          'assessor4' => 'Marketing e Publicidade',
          'gestor' => 'Coordenador',
          'adm' => 'Administrador do Sistema'
        ];


        if($_POST['acao'] == 'lista'){
          if($_POST['lista']){
            $_SESSION['filtroListaUsuarios'] = $_POST['lista'];
          }else{
            $_SESSION['filtroListaUsuarios'] = [];
          }

          exit();
        }

        if(!$_SESSION['filtroListaUsuarios']){
          $_SESSION['filtroListaUsuarios'] = [];
        }


        if($_POST['acao'] == 'bloquear'){
          // $query = "delete from usuarios where codigo = '{$_POST['deletar']}'";
          $query = "update usuarios set situacao = '0' where perfil != 'adm'";
          mysqli_query($con, $query);
        }  
        
        if($_POST['acao'] == 'ativar'){
          // $query = "delete from usuarios where codigo = '{$_POST['deletar']}'";
          $query = "update usuarios set situacao = '1' where perfil != 'adm'";
          mysqli_query($con, $query);
        }  

        if($_POST['deletar']){
          // $query = "delete from usuarios where codigo = '{$_POST['deletar']}'";
          $query = "update usuarios set deletado = '1' where codigo = '{$_POST['deletar']}'";
          mysqli_query($con, $query);
        }

        if($_POST['situacao']){
          $query = "update usuarios set situacao = '{$_POST['opc']}' where codigo = '{$_POST['situacao']}'";
          mysqli_query($con, $query);
          exit();
        }


        if($_POST['acao'] == 'filtro'){
          $_SESSION['usuarioBuscaCampo'] = $_POST['campo'];
          $_SESSION['usuarioBusca'] = $_POST['busca'];
        }
        if($_POST['acao'] == 'limpar'){
          $_SESSION['usuarioBuscaCampo'] = false;
          $_SESSION['usuarioBusca'] = false;
          $_SESSION['filtroListaUsuarios'] = [];    
        }
    
        $where = false;
        if($_SESSION['usuarioBuscaCampo']){
          $where = " and a.{$_SESSION['usuarioBuscaCampo']} like '%{$_SESSION['usuarioBusca']}%'";
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
          <h5 class="card-header">Lista de Gestores</h5>
          <div class="card-body">

                <div class="input-group mb-3">
                  <label class="input-group-text" for="inputGroupFile01">Buscar por </label>
                  <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" rotulo_busca aria-expanded="false"><?=((!$_SESSION['usuarioBuscaCampo'] or $_SESSION['usuarioBuscaCampo'] == 'nome')?'Nome':(($_SESSION['usuarioBuscaCampo'] == 'perfil')?'Perfil':(($_SESSION['usuarioBuscaCampo'] == 'situacao')?'Situação':(($_SESSION['usuarioBuscaCampo'] == 'perfil')?'Perfil':'CPF'))))?></button>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" opcao_busca="Nome">Nome</a></li>
                    <li><a class="dropdown-item" href="#" opcao_busca="CPF">CPF</a></li>
                    <li><a class="dropdown-item" href="#" opcao_busca="Perfil">Perfil</a></li>
                    <li><a class="dropdown-item" href="#" opcao_busca="Situação">Situação</a></li>
                  </ul>
                  <input type="text" texto_busca<?=$md5?> style="display:<?=(($_SESSION['usuarioBuscaCampo'] == 'situacao' or $_SESSION['usuarioBuscaCampo'] == 'perfil')?'none':'block')?>" class="form-control" value="<?=$_SESSION['usuarioBusca']?>" aria-label="Digite a informação para a busca">
                  <select busca_perfil class="form-control" style="display:<?=(($_SESSION['usuarioBuscaCampo'] != 'perfil')?'none':'block')?>">
                        <option value="assessor" <?=(($_SESSION['usuarioBusca'] == 'assessor')?'selected':false)?>>Liderança (Solicitações)</option>
                        <option value="assessor2" <?=(($_SESSION['usuarioBusca'] == 'assessor2')?'selected':false)?>>Atendimento (Atende as Solicitações)</option>
                        <option value="assessor3" <?=(($_SESSION['usuarioBusca'] == 'assessor3')?'selected':false)?>>Supervisor (Valida e Finaliza)</option>
                        <option value="assessor4" <?=(($_SESSION['usuarioBusca'] == 'assessor4')?'selected':false)?>>Marketing e Publicidade</option>
                        <option value="gestor" <?=(($_SESSION['usuarioBusca'] == 'gestor')?'selected':false)?>>Coordenador</option>
                        <option value="adm" <?=(($_SESSION['usuarioBusca'] == 'adm')?'selected':false)?>>Administrador do Sistema</option>
                  </select>
                  <select busca_situacao class="form-control" style="display:<?=(($_SESSION['usuarioBuscaCampo'] != 'situacao')?'none':'block')?>">
                    <option value="0" <?=(($_SESSION['usuarioBusca'] == '0')?'selected':false)?>>Bloqueado</option>
                    <option value="1" <?=(($_SESSION['usuarioBusca'] == '1')?'selected':false)?>>Liberado</option>
                  </select>
                  <button filtrar<?=$md5?> class="btn btn-outline-secondary" type="button">Buscar</button>
                  <button limpar<?=$md5?> class="btn btn-outline-danger" type="button">limpar</button>
                </div>

            <?php
            if($_SESSION['ProjectPainel']->codigo == 1 or $_SESSION['ProjectPainel']->perfil == 'adm' or $_SESSION['ProjectPainel']->perfil == 'gestor'){
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

              <div style="display:flex; justify-content:end">
                  <?php
                  if($_SESSION['ProjectPainel']->codigo == 1 or $_SESSION['ProjectPainel']->perfil == 'adm'){
                  ?>
                  <button class="btn btn-secondary btn-sm me-3" situacao_todos>
                  <i class="fa-solid fa-toggle-off"></i> Desativar Toddos
                  </button>

                  <button class="btn btn-primary btn-sm me-3" ativar_todos>
                  <i class="fa-solid fa-toggle-on"></i> Ativar Toddos
                  </button>
                  <?php
                  }
                  ?>
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
                  <th scope="col">CPF</th>
                  <th scope="col">Telefone</th>
                  <th scope="col">Função</th>
                  <th scope="col">Perfil</th>
                  <th scope="col">Situacao</th>
                  <th scope="col" class="text-end">Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $query = "select a.*, if(a.codigo = 1, concat(' ',a.nome), a.nome) as ordem, (select nome from usuarios where codigo = a.responsavel_cadastro) as responsavel from usuarios a where a.deletado != '1' {$where} order by ordem asc limit 1000";
                  $result = mysqli_query($con, $query);
                  while($d = mysqli_fetch_object($result)){
                ?>
                <tr>
                  <td>
                    <input type="checkbox" value="<?=$d->codigo?>" <?=(in_array($d->codigo, $_SESSION['filtroListaUsuarios'])?'checked':false)?> lista>
                  </td>
                  <td><?=$d->nome?><br><small style="font-size:10px; color:#a1a1a1">Cadastrado por: <?=$d->responsavel?></small></td>
                  <td><?=$d->cpf?></td>
                  <td><?=$d->telefone?></td>
                  <td><?=$d->funcao?></td>
                  <td><?=$Perfil[$d->perfil]?></td>
                  <td>

                  <div class="form-check form-switch">
                    <input <?=(($_SESSION['ProjectPainel']->codigo == 1 or $_SESSION['ProjectPainel']->perfil == 'adm' or ($_SESSION['ProjectPainel']->perfil == 'gestor' and $d->perfil == 'assessor' and $d->responsavel_cadastro == $_SESSION['ProjectPainel']->codigo))?false:'disabled')?> class="form-check-input situacao" type="checkbox" <?=(($d->codigo == 1)?'disabled':false)?> <?=(($d->situacao)?'checked':false)?> usuario="<?=$d->codigo?>">
                  </div>

                  </td>
                  <td class="text-end">
                    <?php
                    if(
                        (
                          $_SESSION['ProjectPainel']->codigo == 1 or
                          $_SESSION['ProjectPainel']->perfil == 'adm' or
                          ($_SESSION['ProjectPainel']->perfil == 'gestor' and $d->perfil == 'assessor' and $d->codigo != 1 and $d->responsavel_cadastro == $_SESSION['ProjectPainel']->codigo)
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
                          $_SESSION['ProjectPainel']->perfil == 'adm' or
                          ($_SESSION['ProjectPainel']->perfil == 'gestor' and $d->perfil == 'assessor' and $d->responsavel_cadastro == $_SESSION['ProjectPainel']->codigo)
                        )  and $d->codigo != 1
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
              url:"src/usuarios/index.php",
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
                url:"src/usuarios/index.php",
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


        $("a[opcao_busca]").click(function(){
          opc = $(this).attr("opcao_busca");
          $("button[rotulo_busca]").text(opc);
          $("input[texto_busca<?=$md5?>]").val('')
          $("select[busca_perfil]").val('')
          if(opc == 'Nome'){
            $("input[texto_busca<?=$md5?>]").unmask();
            $("input[texto_busca<?=$md5?>]").css('display','block')
            $("select[busca_perfil]").css('display','none')
            $("select[busca_situacao]").css('display','none')
          }else if(opc == 'CPF'){
            $("input[texto_busca<?=$md5?>]").mask("999.999.999-99");
            $("input[texto_busca<?=$md5?>]").css('display','block')
            $("select[busca_perfil]").css('display','none')
            $("select[busca_situacao]").css('display','none')
          }else if(opc == 'Situação'){
            $("input[texto_busca<?=$md5?>]").css('display','none')
            $("select[busca_situacao]").css('display','block')
            $("select[busca_perfil]").css('display','none')
          }else if(opc == 'Perfil'){
            $("input[texto_busca<?=$md5?>]").css('display','none')
            $("select[busca_situacao]").css('display','none')
            $("select[busca_perfil]").css('display','block')
          }
        });

        $("button[limpar<?=$md5?>]").click(function(){
          Carregando()
          $.ajax({
              url:"src/usuarios/index.php",
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
          opc = $("button[rotulo_busca]").text();
          busca = $("input[texto_busca<?=$md5?>]").val();
          campo = false;
          if(opc == 'Nome'){
            campo = 'nome';
          }else if(opc == 'CPF'){
            campo = 'cpf';
          }else if(opc == 'Situação'){
            campo = 'situacao';
            busca = $("select[busca_situacao]").val();
          }else if(opc == 'Perfil'){
            campo = 'perfil';
            busca = $("select[busca_perfil]").val();
          }
          if(campo && busca){
            // console.log(`campo:${campo} && Busca: ${busca}`);
            Carregando()
            $.ajax({
              url:"src/usuarios/index.php",
              type:"POST",
              data:{
                campo,
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


        $("button[EnviarMensagem]").click(function(){
            Carregando()
            $.ajax({
                url:"src/usuarios/wapp.php",
                success:function(dados){
                    $(".MenuRight").html(dados);
                }
            })
        })



        $("button[novoRegistro]").click(function(){
            $.ajax({
                url:"src/usuarios/form.php",
                success:function(dados){
                    $(".MenuRight").html(dados);
                }
            })
        })

        $("button[situacao_todos]").click(function(){
          $.confirm({
            content:"Deseja realmente desativar todos os usuários?",
            title:"Alerta",
            buttons:{
              'SIM':function(){
                Carregando()
                $.ajax({
                    url:"src/usuarios/index.php",
                    type:"POST",
                    data:{
                      acao:'bloquear'
                    },
                    success:function(dados){
                      $("#pageHome").html(dados);
                    }
                })
              },
              'NÃO':function(){
                
              }
            }
          })

        })


        $("button[ativar_todos]").click(function(){
          $.confirm({
            content:"Deseja realmente ativar todos os usuários?",
            title:"Alerta",
            buttons:{
              'SIM':function(){
                Carregando()
                $.ajax({
                    url:"src/usuarios/index.php",
                    type:"POST",
                    data:{
                      acao:'ativar'
                    },
                    success:function(dados){
                      $("#pageHome").html(dados);
                    }
                })
              },
              'NÃO':function(){
                
              }
            }
          })

        })

        $("button[editar]").click(function(){
            codigo = $(this).attr("editar");
            $.ajax({
                url:"src/usuarios/form.php",
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
                            url:"src/usuarios/index.php",
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
                url:"src/usuarios/index.php",
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