<?php

function ConsultaCEP($cep){
    global $con;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://viacep.com.br/ws/{$cep}/json/");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "accept: application/json",
    "Content-Type: application/json",
    ));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $d);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $dados = json_decode($response);

    list($cod_municipio) = mysqli_fetch_row(mysqli_query($con, "select codigo from municipios where nome = '{$dados->localidade}'"));
    $dados->localidade_codigo =  $cod_municipio;

    list($cod_bairro) = mysqli_fetch_row(mysqli_query($con, "select codigo from bairros where municipio = '{$cod_municipio}' and nome = '{$dados->bairro}' and  tipo='urbano'"));
    if(!$cod_bairro){
        mysqli_query($con, "insert into bairros set municipio = '{$cod_municipio}', nome = '{$dados->bairro}', tipo='urbano'");
        $cod_bairro = mysqli_insert_id($con);
    }
    $dados->bairro_codigo =  $cod_bairro;
    
    return json_encode($dados);
}