<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");
?>
<div dashboard="paineis" class="m-4"></div>
<div dashboard="tipos_municipios" class="m-4"></div>
<div dashboard="capital_zonas" class="m-4"></div>
<div dashboard="interiores_calhas_rio" class="m-4"></div>
<div dashboard="municipios" class="m-4"></div>
<div dashboard="categorias" class="m-4"></div>
<div dashboard="especialidades" class="m-4"></div>
<div dashboard="empresas_esferas" class="m-4"></div>
<div dashboard="empresas" class="m-4"></div>

<script>
    $(function(){

        AbreDashborad = (loop)=>{
            opc = loop.attr("dashboard");
            $.ajax({
                url:`src/dashboard/telas/${opc}.php`,
                success:function(dados){
                    loop.html(dados);
                }
            });
        }

        Carregando('none');
        $("div[dashboard]").each(function(){
            AbreDashborad($(this));
        });

    })
</script>