<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");
?>

<style>
.menu-cinza{
  padding:8px;
  font-size:15px;
  border-bottom:1px solid #d7d7d7;
  cursor:pointer;
}

.texto-cinza{
  color:#5e5e5e;
}

</style>
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
  <div class="offcanvas-header">
    <img src="img/logo.svg" style="height:120px;" alt=""> <span style="font-size:40px; font-weight:bold; color:#8580bc">Cérebro</span>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <h4 style="color:#057a34">Gestão política</h4>


    <div class="row mb-1 menu-cinza">
      <div class="col">
        <a url="src/dashboard/index.php" class="text-decoration-none texto-cinza" data-bs-dismiss="offcanvas" aria-label="Close">
          <i class="fa-solid fa-chart-line"></i> Dashboard
        </a>
      </div>
    </div>

    <div class="row mb-1 menu-cinza">
      <div class="col">
        <a url="src/usuarios/index.php" class="text-decoration-none texto-cinza" data-bs-dismiss="offcanvas" aria-label="Close">
          <i class="fa-solid fa-chart-line"></i> Gestores
        </a>
      </div>
    </div>
    <h5>Tabelas</h5>
    <h6><small>Localização</small></h6>
    <div class="row mb-1 menu-cinza">
      <div class="col">
        <a url="src/bairros/index.php?n=1" class="text-decoration-none texto-cinza" data-bs-dismiss="offcanvas" aria-label="Close">
          <i class="fa-solid fa-chart-line"></i> Capital
        </a>
      </div>
    </div>
    <div class="row mb-1 menu-cinza">
      <div class="col">
        <a url="src/municipios/index.php?n=1" class="text-decoration-none texto-cinza" data-bs-dismiss="offcanvas" aria-label="Close">
          <i class="fa-solid fa-chart-line"></i> Interior
        </a>
      </div>
    </div>
    <div class="row mb-1 menu-cinza">
      <div class="col">
        <a url="src/especialidades/index.php" class="text-decoration-none texto-cinza" data-bs-dismiss="offcanvas" aria-label="Close">
          <i class="fa-solid fa-chart-line"></i> Especialidade
        </a>
      </div>
    </div>
    <div class="row mb-1 menu-cinza">
      <div class="col">
        <a url="src/beneficiados/index.php" class="text-decoration-none texto-cinza" data-bs-dismiss="offcanvas" aria-label="Close">
          <i class="fa-solid fa-chart-line"></i> Beneficiados
        </a>
      </div>
    </div>
    <div class="row mb-1 menu-cinza">
      <div class="col">
        <a url="src/solicitacoes/index.php" class="text-decoration-none texto-cinza" data-bs-dismiss="offcanvas" aria-label="Close">
          <i class="fa-solid fa-chart-line"></i> Solicitações
        </a>
      </div>
    </div>



  </div>
</div>

<script>
  $(function(){
    $("a[url]").click(function(){
      Carregando();
      url = $(this).attr("url");
      $.ajax({
        url,
        success:function(dados){
          $("#pageHome").html(dados);
        }
      });
    });
  })
</script>