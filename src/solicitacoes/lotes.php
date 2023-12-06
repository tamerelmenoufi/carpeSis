<?php
        include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");


        if($_POST['acao'] == 'filtro'){
          $_SESSION['loteBusca'] = $_POST['busca'];
        }
        if($_POST['acao'] == 'limpar'){
          $_SESSION['loteBusca'] = false;      
        }
    
        $where = false;
        if($_SESSION['loteBusca']){
          $where = " and (b.nome like '%{$_SESSION['loteBusca']}%' or c.nome like '%{$_SESSION['loteBusca']}%')";
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
<h4 class="Titulo<?=$md5?>">Lotes</h4>
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
            
          <div class="table-responsive">

            <div class="input-group mb-3">
              <label class="input-group-text" for="inputGroupFile01">Buscar por </label>
              <input type="text" texto_busca<?=$md5?> class="form-control" value="<?=$_SESSION['loteBusca']?>" aria-label="Digite a informação para a busca">
              <button filtrar<?=$md5?> class="btn btn-outline-secondary" type="button">Buscar</button>
              <button limpar<?=$md5?> class="btn btn-outline-danger" type="button">limpar</button>
            </div>

            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th scope="col">Lote</th>
                  <th scope="col">Especialidade</th>
                  <th scope="col" class="text-end"></th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $query = "select a.*, b.nome as categoria_nome, c.nome as especialidade_nome, count(*) as quantidade from servicos a left join categorias b on a.categoria = b.codigo left join especialidades c on a.especialidade = c.codigo where a.deletado != '1' {$where} group by a.lote";
                  $result = mysqli_query($con, $query);
                  while($d = mysqli_fetch_object($result)){
                ?>
                <tr>
                  <td><span style="color:#a1a1a1"><?=$d->lote?></span><br><small><?=dataBr($d->data_pedido)?></small></td>
                  <td><?=$d->especialidade_nome?><br><small><?=$d->categoria_nome?></small></td>

                  <td class="text-end">
                    <button
                      class="btn btn-primary"
                      style="margin-bottom:1px"
                      lote<?=$md5?>="<?=$d->lote?>"
                      data-bs-toggle="offcanvas"
                      href="#offcanvasRight"
                      role="button"
                      aria-controls="offcanvasRight"
                    >
                    <b><?=$d->quantidade?></b> <i class="fa fa-filter"></i>
                    </button>
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
              url:"src/solicitacoes/lotes.php",
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
              url:"src/solicitacoes/lotes.php",
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
        
        $("button[lote<?=$md5?>]").click(function(){
            lote = $(this).attr("lote<?=$md5?>");
            $.ajax({
                url:"src/solicitacoes/index.php",
                type:"POST",
                data:{
                    lote
                },
                success:function(dados){
                    $("#pageHome").html(dados);
                    let myOffCanvas = document.getElementById('offcanvasRight');
                    let openedCanvas = bootstrap.Offcanvas.getInstance(myOffCanvas);
                    openedCanvas.hide();
                }
            });
        })

    })
</script>