<?php

    include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");

    if($_POST['beneficiado']) $_SESSION['beneficiado'] = $_POST['beneficiado'];

    if($_POST['deletar']){
      // $query = "delete from usuarios where codigo = '{$_POST['deletar']}'";
      $query = "update servicos set deletado = '1' where codigo = '{$_POST['deletar']}'";
      mysqli_query($con, $query);
    }

    if($_POST['situacao']){
      $query = "update servicos set situacao = '{$_POST['opc']}' where codigo = '{$_POST['situacao']}'";
      mysqli_query($con, $query);
      exit();
    }


    $query = "select 
                    a.*, 
                    b.nome as municipio_nome, 
                    c.nome as bairro_nome                    
        from beneficiados a left join municipios b on a.municipio = b.codigo left join bairros c on a.bairro = c.codigo where a.codigo = '{$_SESSION['beneficiado']}'";
    $result = mysqli_query($con, $query);
    $beneficiado = mysqli_fetch_object($result);

    $_SESSION['beneficiado_nome'] = $beneficiado->nome;

?>


<style>
  td{
    white-space: nowrap;
  }
  .ExibeEndereco{
    min-height:200px;
  }
  .dados_beneficiado label{
    font-size:10px;
    color:#a1a1a1;
    padding:0;
    margin:0;
  }
</style>
<div class="col">
  <div class="m-3">


    <div class="row">
      <div class="col">

        <div class="card mb-3">
            <h5 class="card-header">
              <div class="d-flex justify-content-between">
                <span><?="{$beneficiado->nome} - {$beneficiado->cpf}"?></span>
                <button class="btn btn-secondary btn-sm" voltar><i class="fa-solid fa-angles-left"></i> Voltar</button>
              </div>
            </h5>
            <div class="card-body">
                <div class="row">
                  <div class="col-md-6 dados_beneficiado mb-2">
                    <label>Nome da Mãe</label>
                    <div><?=$beneficiado->nome_mae?></div>
                    <div class="row">
                      <div class="col-md-6">
                        <label>Data de Nscimento</label>
                        <div><?=dataBr($beneficiado->data_nascimento)?></div>
                      </div>
                      <div class="col-md-6">
                        <label>Sexo</label>
                        <div><?=(($beneficiado->sexo == 'm')?'Masculino':'Feminino')?></div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <label>E-mail</label>
                        <div><?=$beneficiado->email?></div>
                      </div>
                      <div class="col-md-6">
                        <label>Telefone</label>
                        <div><?=$beneficiado->telefone?></div>
                      </div>
                    </div>
                    <label>Endereço</label>
                    <div><?="{$beneficiado->logradouro}, {$beneficiado->numero}, {$beneficiado->municipio_nome}, {$beneficiado->bairro_nome}, {$beneficiado->cep}"?></div>

                  </div>
                  <div class="col-md-6 ExibeEndereco"></div>
                </div>
            </div>
        </div>


        <div class="card">
          <h5 class="card-header">Solicitações</h5>
          <div class="card-body">
            <?php
            if(
                $_SESSION['ProjectPainel']->codigo == 1 or
                $_SESSION['ProjectPainel']->perfil == 'adm' or
                $_SESSION['ProjectPainel']->perfil == 'assessor'
              ){
            ?>
            <div style="display:flex; justify-content:end">
                <button
                    novoRegistro
                    class="btn btn-success"
                    data-bs-toggle="offcanvas"
                    href="#offcanvasRight"
                    role="button"
                    aria-controls="offcanvasRight"
                >Nova Solicitação</button>
            </div>
            <?php
            }
            ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th scope="col">Lote</th>
                  <th scope="col">Categoria</th>
                  <th scope="col">Especialidade</th>
                  <th scope="col">Datas</th>
                  <th scope="col">Periodo</th>
                  <th scope="col">Situacao</th>
                  <th scope="col" class="text-end">Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $query = "select a.*,
                                   (select count(*) from servicos_registros where servico = a.codigo) as registros,
                                   c.nome as categoria_nome,
                                   b.nome as especialidade_nome,
                                   DATEDIFF (IF(a.data_finalizacao > 0, a.data_finalizacao, NOW()), a.data_pedido) as periodo
                                  from servicos a
                                  left join categorias c on a.categoria = c.codigo
                                  left join especialidades b on a.especialidade = b.codigo
                              where a.beneficiado = '{$_SESSION['beneficiado']}' and a.deletado != '1' order by a.data_pedido desc";
                  $result = mysqli_query($con, $query);
                  while($d = mysqli_fetch_object($result)){
                ?>
                <tr>
                  <td><?=$d->lote?></td>
                  <td><?=$d->categoria_nome?></td>
                  <td><?=$d->especialidade_nome?></td>
                  <td>De <?=dataBr($d->data_pedido)?><br><?=((dataBr($d->data_finalizacao))?"Ate ".dataBr($d->data_finalizacao):'<span style="color:orange">Em andamento</span>')?></td>
                  <td style="color:<?=(($d->situacao != '1')?'red':'green')?>"><?=$d->periodo.(($d->periodo > 1)?' dias':' dia')?></td>
                  <td><?php
                    if($d->situacao == '1'){
                  ?>
                    <small class="alert alert-success m-0 p-1">Atendido</small>
                  <?php
                    }elseif($d->situacao == '2'){
                      ?>
                        <small class="alert alert-danger m-0 p-1">Negado</small>
                      <?php
                    }else{
                      ?>
                        <small class="alert alert-warning m-0 p-1">Pendente</small>
                      <?php
                    }
            
                  ?></td>

                  <td class="text-end">
                    <button
                      class="btn btn-warning"
                      style="margin-bottom:1px"
                      registros="<?=$d->codigo?>"
                      data-bs-toggle="offcanvas"
                      href="#offcanvasRight"
                      role="button"
                      aria-controls="offcanvasRight"
                    >
                    <?=$d->registros?> Registros
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
                      <?=(($d->situacao or $d->registros)?'disabled':false)?>
                    >
                    Editar
                    </button>
                    <button <?=(($d->situacao or $d->registros)?'disabled':false)?> class="btn btn-danger" deletar="<?=$d->codigo?>">
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
        $("button[novoRegistro]").click(function(){
            $.ajax({
                url:"src/servicos/form.php",
                success:function(dados){
                    $(".MenuRight").html(dados);
                }
            })
        })

        $.ajax({
            url:"src/beneficiados/mapa_visualizar.php",
            type:"POST",
            data:{
                codigo:'<?=$beneficiado->codigo?>'
            },
            success:function(dados){
                $(".ExibeEndereco").html(dados);
            }
        })

        $("button[voltar]").click(function(){
            Carregando();
            $.ajax({
                url:"src/beneficiados/index.php",
                type:"POST",
                success:function(dados){
                    $("#pageHome").html(dados);
                }
            })
        })

        $("button[registros]").click(function(){
            Carregando();
            servico = $(this).attr("registros");
            $.ajax({
                url:"src/servicos/registros.php",
                type:"POST",
                data:{
                  servico,
                  origem:'src/servicos/index.php'
                },
                success:function(dados){
                    $(".MenuRight").html(dados);
                }
            })
        })

        $("button[editar]").click(function(){
            codigo = $(this).attr("editar");
            $.ajax({
                url:"src/servicos/form.php",
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
                            url:"src/servicos/index.php",
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