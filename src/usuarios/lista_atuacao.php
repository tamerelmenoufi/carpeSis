<?php
        include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");

        if($_POST['acao'] == 'local_padrao'){
          mysqli_query($con, "update usuarios set municipio = '{$_POST['municipio']}', bairro = '{$_POST['bairro']}' where codigo = '{$_POST['usuario']}'");
          mysqli_query($con, "update usuarios_atuacao set situacao = '0' where usuario = '{$_POST['usuario']}'");
          mysqli_query($con, "update usuarios_atuacao set situacao = '1' where codigo = '{$_POST['codigo']}'");
          exit();
        }

    if($_POST['deletar']){
      $query = "delete from usuarios_atuacao where codigo = '{$_POST['deletar']}'";
      mysqli_query($con, $query);
    }

    if($_POST['acao'] == 'novo'){
      $query = "insert into usuarios_atuacao set usuario = '{$_POST['usuario']}', municipio = '{$_POST['municipio']}', bairro = '{$_POST['bairro']}'";
      mysqli_query($con, $query);
    }

?>

<style>
  td{
    white-space: nowrap;
  }
</style>

            <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th scope="col">Município</th>
                  <th scope="col">Bairro/Comunidade</th>
                  <th scope="col">Situação</th>
                  <th scope="col" class="text-end">Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $query = "select a.*, b.nome as municipio_nome, c.nome as bairro_nome from usuarios_atuacao a left join municipios b on a.municipio = b.codigo left join bairros c on a.bairro = c.codigo where a.usuario = '{$_POST['usuario']}'";
                  $result = mysqli_query($con, $query);
                  while($d = mysqli_fetch_object($result)){
                ?>
                <tr>
                  <td><?=$d->municipio_nome?></td>
                  <td><?=$d->bairro_nome?></td>
                  <td>
                    <div class="form-check form-switch">
                      <input 
                            class="form-check-input situacao<?=$md5?>" 
                            type="radio" 
                            name="situacao" 
                            value="<?=$d->codigo?>" 
                            <?=(($d->situacao)?'checked':false)?> 
                            usuario="<?=$d->usuario?>"
                            municipio = "<?=$d->municipio?>"      
                            bairro = "<?=$d->bairro?>"      
                      >
                    </div>
                  </td>
                  <td class="text-end">                  
                    <button type="button" class="btn btn-danger" deletar<?=$md5?>="<?=$d->codigo?>">
                    Deletar
                    </button>
                  </td>
                </tr>
                <?php
                  }
                ?>
              </tbody>
            </table>
                </div>
        

<script>
    $(function(){
        Carregando('none');


        $("button[deletar<?=$md5?>]").click(function(){
            deletar = $(this).attr("deletar<?=$md5?>");
            blq = $(`.situacao<?=$md5?>[value="${deletar}"]`).prop("checked");
            // console.log(blq)
            if(blq) {
              $.alert('<center>Localização está ativa para o usuário<br> exclusão bloqueada!</center>')
              return;
            }
            $.confirm({
                content:"Você tem certeza que quer deletar o registro?",
                title:false,
                buttons:{
                    'Sim':function(){
                        $.ajax({
                            url:"src/usuarios/lista_atuacao.php",
                            type:"POST",
                            data:{
                                deletar,
                                usuario:'<?=$_POST['usuario']?>'
                            },
                            success:function(dados){
                              $(".lista_atuacao").html(dados);
                            }
                        })
                    },
                    'Não':function(){

                    }
                }
            });

        })

        $(".situacao<?=$md5?>").click(function(){
          municipio = $(this).attr("municipio")
          bairro = $(this).attr("bairro")
          usuario = $(this).attr("usuario")
          codigo = $(this).val()
          $.ajax({
            url:"src/usuarios/lista_atuacao.php",
            type:"POST",
            data:{
              codigo,
              municipio,
              bairro,
              usuario,
              acao:"local_padrao"
            },
            success:function(dados){

            }
          });
        })


    })
</script>