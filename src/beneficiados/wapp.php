<?php
        include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");

        if($_POST['acao'] == 'wapp'){

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

            $where = false;
            if($_SESSION['beneficiadosBusca']){
              $where = " and a.nome like '%{$_SESSION['beneficiadosBusca']}%'";
            }
            if($_SESSION['filtroLista']){
                $where .= " and a.codigo in (".implode(',', $_SESSION['filtroLista']).")";
              }

            $query = "INSERT INTO wapp (lote, tabela_origem, registro, nome, telefone, mensagem, data) select
                                      '".date("YmdHis")."',
                                      'beneficiados',
                                      a.codigo,
                                      a.nome,
                                      a.telefone,
                                      '{$_POST['mensagem']}',
                                      NOW()
                                from beneficiados a
                                  left join bairros b on a.bairro = b.codigo
                                  left join municipios c on a.municipio = c.codigo
                                  left join usuarios d on a.assessor = d.codigo
                                where a.deletado != '1' {$where} ".(($filtro)?" and {$filtro}":false).(($filtro2)?" and {$filtro2}":false).(($_SESSION['ProjectPainel']->perfil != 'adm')? " and a.assessor = '{$_SESSION['ProjectPainel']->codigo}' ":false);
            if(mysqli_query($con, $query)){
                echo "Mensagem adicionada na fila de nevios com sucesso!";
            }else{
                echo "Mensagem não enviada, ocorreu um erro!<br><br>";
            }
            exit();
        }

?>

<style>
    .Titulo<?=$md5?>{
        position:absolute;
        left:60px;
        top:8px;
        z-index:0;
    }
</style>
<h4 class="Titulo<?=$md5?>">Mensagem para WhatsApp</h4>
    <form id="form-<?= $md5 ?>">
        <div class="row">
            <div class="col">

                <div class="form-floating mb-3">
                    <input type="text" name="mensagem" id="mensagem" class="form-control" placeholder="Digite a mensagem">
                    <label for="municipio">Digite a mensagem</label>
                </div>
                
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div style="display:flex; justify-content:end">
                    <button type="submit" class="btn btn-success btn-ms">Enviar</button>
                </div>
            </div>
        </div>
    </form>

<script>
    $(function(){
        Carregando('none')

        $('#form-<?=$md5?>').submit(function (e) {

            e.preventDefault();

            var filds = $(this).serializeArray();

            filds.push({name: 'acao', value: 'wapp'})

            msg = $("#mensagem").val();
            if(!msg.trim()){
                $.alert('Digite uma mensagem para validar o envio!');
                return;
            }


            $.confirm({
                title:"Confirmação de Envio",
                content:"Autoriza o envio de mensagem WhatsApp para o grupo filtrado na lista dos Beneficiados?",
                buttons:{
                    'SIM':function(){
                        $.ajax({
                            url:"src/beneficiados/wapp.php",
                            type:"POST",
                            data: filds,
                            success:function(dados){

                                $.alert(dados);
                                let myOffCanvas = document.getElementById('offcanvasRight');
                                let openedCanvas = bootstrap.Offcanvas.getInstance(myOffCanvas);
                                openedCanvas.hide();
                                Carregando('none')

                            },
                            error:function(erro){

                                // $.alert('Ocorreu um erro!' + erro.toString());
                                //dados de teste
                            }
                        });
                    },
                    'NÃO':function(){
                        Carregando('none')
                    }
                }
            })

            Carregando();

            

        });
    })
</script>