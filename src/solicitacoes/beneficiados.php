<?php
include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");

if(!$_SESSION['lista_ativa']) {
  $_SESSION['lista_ativa'] = [];
  $_SESSION['ativo_permanente'] = [];
}

if($_POST['del']){
    $key = array_search($_POST['del'], $_SESSION['lista_ativa']);

    $query = "update servicos set deletado = '1' where 
                                    beneficiado = '{$_SESSION['lista_ativa'][$key]}' and
                                    categoria = '{$_SESSION['categoria']}' and
                                    especialidade = '{$_SESSION['especialidade']}' and
                                    situacao = '0' and
                                    deletado != '1'";
    mysqli_query($con, $query);

    unset($_SESSION['lista_ativa'][$key]);
}

if($_POST['add']){
    $_SESSION['lista_ativa'][] = $_POST['add'];
}


if($_POST['acao'] == 'filtro'){
  $_SESSION['beneficiados2Busca'] = $_POST['busca'];
}
if($_POST['acao'] == 'limpar'){
  $_SESSION['beneficiados2Busca'] = false;      
}
$where = false;
if($_SESSION['beneficiados2Busca']){
  $where = " and nome like '%{$_SESSION['beneficiados2Busca']}%'";
}

?>

<div class="col">
  <div class="m-3">


    <div class="row">
      <div class="col">

        <div class="card">
            <h5 class="card-header w-100">
                <div class="d-flex justify-content-between">
                    <span>Lista dos Beneficiados</span>
                    <button class="btn btn-danger btn-sm fechaJanela"><i class="fa fa-close"></i></button>
                </div>
            </h5>
          <div class="card-body">

            <div class="input-group mb-3">
              <label class="input-group-text" for="inputGroupFile01">Buscar por </label>
              <input type="text" texto_busca<?=$md5?> class="form-control" value="<?=$_SESSION['beneficiados2Busca']?>" aria-label="Digite a informação para a busca">
              <button filtrar<?=$md5?> class="btn btn-outline-secondary" type="button">Buscar</button>
              <button limpar<?=$md5?> class="btn btn-outline-danger" type="button">limpar</button>
            </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th scope="col">Beneficiado</th>
                  <th scope="col">CPF</th>
                  <th scope="col"></th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $query = "select * from beneficiados where deletado != '1' {$where} and situacao = '1' order by nome asc";
                  $result = mysqli_query($con, $query);
                  while($d = mysqli_fetch_object($result)){
                ?>
                <tr>
                  <td><?=$d->nome?></td>
                  <td><?=$d->cpf?></td>
                  <td style="width:60px;">
                    <?php
                    if(!$_SESSION['ativo_permanente'][$d->codigo]){
                    if(in_array($d->codigo, $_SESSION['lista_ativa'])){
                    ?>
                    <button delB="<?=$d->codigo?>" class="btn btn-sm btn-danger">
                        <i class="fa fa-trash"></i>
                    </button>
                    <?php
                    }else{
                    ?>
                    <button addB="<?=$d->codigo?>" class="btn btn-sm btn-primary">
                        <i class="fa fa-plus"></i>
                    </button>
                    <?php
                    }
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
              url:"src/solicitacoes/beneficiados.php",
              type:"POST",
              data:{
                acao:"limpar"
              },
              success:function(dados){
                $(".divBgDados").html(dados);
              }
            });
        })

        $("button[filtrar<?=$md5?>]").click(function(){
          busca = $("input[texto_busca<?=$md5?>]").val();

          if(busca){
            // console.log(`campo:${campo} && Busca: ${busca}`);
            Carregando()
            $.ajax({
              url:"src/solicitacoes/beneficiados.php",
              type:"POST",
              data:{
                busca,
                acao:"filtro"
              },
              success:function(dados){
                $(".divBgDados").html(dados);
              }
            });
          }else{
            $.alert('Favor preencher o campo da busca!')
          }

        });
        
        $(".fechaJanela").click(function(){
            $(".divBg").css("display","none");
        })

        $("button[addB]").click(function(){
            add = $(this).attr("addB");
            $.ajax({
                url:"src/solicitacoes/beneficiados.php",
                type:"POST",
                data:{
                    add
                },
                success:function(dados){
                    $(".divBgDados").html(dados);
                    $.ajax({
                      url:"src/solicitacoes/beneficiados_ativos.php",
                      success:function(dados){
                        $(".listaBeneficiados").html(dados);
                      }
                    });
                }
            })
        })

        $("button[delB]").click(function(){
            del = $(this).attr("delB");
            $.ajax({
                url:"src/solicitacoes/beneficiados.php",
                type:"POST",
                data:{
                    del
                },
                success:function(dados){
                    $(".divBgDados").html(dados);
                    $.ajax({
                      url:"src/solicitacoes/beneficiados_ativos.php",
                      success:function(dados){
                        $(".listaBeneficiados").html(dados);
                      }
                    });
                    
                    $.ajax({
                        url:"src/solicitacoes/index.php",
                        type:"POST",
                        success:function(dados){
                            $("#pageHome").html(dados);
                            // let myOffCanvas = document.getElementById('offcanvasRight');
                            // let openedCanvas = bootstrap.Offcanvas.getInstance(myOffCanvas);
                            // openedCanvas.hide();
                        }
                    });
                }
            })
        })


    })
</script>