<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/app/projectCerebro/lib/includes.php");

    if($_POST['acao'] == 'coordenadas'){
        $query = "update orgaos set coordenadas = '{$_POST['coordenadas']}' where codigo = '{$_POST['codigo']}'";
        mysqli_query($con, $query);

        echo 'success';

        exit();
    }

    $md5 = md5($md5.$_POST['codigo']);

    $query = "select
                    a.*,
                    b.nome as nome_bairro,
                    c.nome as nome_municipio
                from orgaos a
                    left join bairros b on a.bairro = b.codigo
                    left join municipios c on a.cidade = c.codigo
                where a.codigo = '{$_POST['codigo']}'";
    $result = mysqli_query($con, $query);
    $d = mysqli_fetch_object($result);

    $endereco =  "{$d->logradouro}, {$d->numero}, {$d->nome_bairro}, {$d->nome_municipio}, ";

    $coordenadas = explode(',', $d->coordenadas);

?>

<style>

    #map<?=$md5?> {
        position:relative;
        height: 100%;
        width:100%;
        /* opacity:0.6;
        z-index:0; */
    }

</style>

    <div id="map<?=$md5?>"></div>

    <script>
        coordenadas<?=$md5?> = '<?=$d->coordenadas?>';
        endereco<?=$md5?> = "<?=$endereco?>";
        geocoder<?=$md5?> = new google.maps.Geocoder();
        map<?=$md5?> = new google.maps.Map(document.getElementById("map<?=$md5?>"), {
            zoomControl: true,
            mapTypeControl: false,
            draggable: true,
            scaleControl: true,
            scrollwheel: false,
            navigationControl: false,
            streetViewControl: false,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            fullscreenControl: true,
            <?php
            if($d->coordenadas){
            ?>
            center: { lat: <?=$coordenadas[0]?>, lng: <?=$coordenadas[1]?> },
            zoom: 18,

            <?php
            }
            ?>
        }

        );

        marker<?=$md5?> = new google.maps.Marker({
            position: { lat: <?=(($coordenadas[0])?:0)?>, lng: <?=(($coordenadas[1])?:0)?> },
            map:map<?=$md5?>,
            title: "Hello World!",
            draggable:false,
        });

        geocoder<?=$md5?>.geocode({ 'address': endereco<?=$md5?> + ', Amazonas, Brasil', 'region': 'BR' }, (results, status) => {

            if (status == google.maps.GeocoderStatus.OK) {
                if (results[0] && !coordenadas<?=$md5?>) {

                    var latitude<?=$md5?> = results[0].geometry.location.lat();
                    var longitude<?=$md5?> = results[0].geometry.location.lng();

                    coordenadas = `${latitude<?=$md5?>},${longitude<?=$md5?>}`;
                    var location<?=$md5?> = new google.maps.LatLng(latitude<?=$md5?>, longitude<?=$md5?>);
                    marker<?=$md5?>.setPosition(location<?=$md5?>);
                    map<?=$md5?>.setCenter(location<?=$md5?>);
                    map<?=$md5?>.setZoom(18);


                    // Inclusão automática das coordenadas antes da confirmação pelo cliente
                    $.ajax({
                        url:"src/orgaos/mapa_visualizar.php",
                        type:"POST",
                        data:{
                            coordenadas,
                            codigo:'<?=$d->codigo?>',
                            acao:'coordenadas'
                        },
                        success:function(dados){
                            console.log(dados)
                            console.log('Atualização Corredenadas:')
                            console.log(coordenadas)
                        }
                    });
                    // Inclusão automática das coordenadas antes da confirmação pelo cliente

                }
            }
        });




</script>