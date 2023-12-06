<?php
include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");



if($_POST['limpa_filtro']) $_SESSION['lote'] = false;
if($_POST['lote']) $_SESSION['lote'] = $_POST['lote'];

if($_POST['deletar']){
  // $query = "delete from usuarios where codigo = '{$_POST['deletar']}'";
  $query = "update servicos set deletado = '1' where codigo = '{$_POST['deletar']}'";
  mysqli_query($con, $query);
}


?>

<div class="col">
  <div class="m-3">

<?php
if($_SESSION['lote']){

  $query = "select a.*,
                    count(*) as quantidade,
                    c.nome as categoria_nome,
                    b.nome as especialidade_nome
                  from servicos a
                  left join categorias c on a.categoria = c.codigo
                  left join especialidades b on a.especialidade = b.codigo
              where a.deletado != '1' and a.lote = '{$_SESSION['lote']}' group by lote";
  $result = mysqli_query($con, $query);
  $d = mysqli_fetch_object($result);


?>
    <div class="row">
      <div class="col">
        <div class="card mb-3">
            <h5 class="card-header">
              <div class="d-flex justify-content-between">
                <span>LOTE: <?="{$_SESSION['lote']}"?></span>
              </div>
            </h5>
            <div class="card-body">
                <div class="row">
                      
                      <div class="col-md-3">
                        <label style="color:#a1a1a1">Data do Pedido</label>
                        <div><?=dataBr($d->data_pedido)?></div>
                      </div>

                      <div class="col-md-3">
                        <label style="color:#a1a1a1">Beneficiados</label>
                        <div><?=$d->quantidade?></div>
                      </div>

                      <div class="col-md-3">
                        <label style="color:#a1a1a1">Categoria</label>
                        <div><?=$d->categoria_nome?></div>
                      </div>

                      <div class="col-md-3">
                        <label style="color:#a1a1a1">Especialidade</label>
                        <div><?=$d->especialidade_nome?></div>
                      </div>
                </div>
              </div>
          </div>
      </div>
    </div>
<?php
}
?>

    <div class="row">
      <div class="col">

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
                    filtroLotes
                    class="btn btn-info me-2"
                    data-bs-toggle="offcanvas"
                    href="#offcanvasRight"
                    role="button"
                    aria-controls="offcanvasRight"
                ><i class="fa fa-filter"></i> Lotes</button>
                <button
                    limpaFiltroLotes
                    class="btn btn-danger me-2"
                ><i class="fa fa-filter-circle-xmark"></i> Lotes</button>
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
                  <th scope="col">Beneficiado</th>
                  <th scope="col">Solicitação</th>
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
                                   d.nome as beneficiado_nome,
                                   d.cpf as beneficiado_cpf,
                                   DATEDIFF (IF(a.data_finalizacao > 0, a.data_finalizacao, NOW()), a.data_pedido) as periodo
                                  from servicos a
                                  left join categorias c on a.categoria = c.codigo
                                  left join especialidades b on a.especialidade = b.codigo
                                  left join beneficiados d on a.beneficiado = d.codigo
                              where a.deletado != '1' ".(($_SESSION['lote'])?" and a.lote = '{$_SESSION['lote']}'":false)." order by a.data_pedido desc";
                  $result = mysqli_query($con, $query);
                  while($d = mysqli_fetch_object($result)){
                ?>
                <tr>
                  <td><?=$d->lote?></td>
                  <td><?=$d->beneficiado_nome?><br><small><?=$d->beneficiado_cpf?></small></td>
                  <td><?=$d->categoria_nome?><br><small><?=$d->especialidade_nome?></small></td>
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
                url:"src/solicitacoes/form.php",
                type:"POST",
                data:{
                  abrir:'1'
                },
                success:function(dados){
                    $(".MenuRight").html(dados);
                }
            })
        })

        $("button[filtroLotes]").click(function(){
            $.ajax({
                url:"src/solicitacoes/lotes.php",
                success:function(dados){
                    $(".MenuRight").html(dados);
                }
            })
        })

        $("button[limpaFiltroLotes]").click(function(){
            $.ajax({
                url:"src/solicitacoes/index.php",
                type:"POST",
                data:{
                  limpa_filtro:1
                },
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
                  origem:'src/solicitacoes/index.php'
                },
                success:function(dados){
                    $(".MenuRight").html(dados);
                }
            })
        })

        $("button[editar]").click(function(){
            codigo = $(this).attr("editar");
            $.ajax({
                url:"src/solicitacoes/form.php",
                type:"POST",
                data:{
                  codigo,
                  abrir:'1'
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
                            url:"src/solicitacoes/index.php",
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