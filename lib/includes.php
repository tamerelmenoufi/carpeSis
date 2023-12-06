<?php
    session_start();

    include("/appinc/connect.php");
    $con = AppConnect('cerebro');
    $conApi = AppConnect('information_schema');
    include("fn.php");
    include("vendor/cep/classe.php");
    include "AppWapp.php";

    $md5 = md5(date("YmdHis"));

    $localPainel = "https://{$_SERVER["HTTP_HOST"]}/";

    if($_GET['ln']){
        $_SESSION['lng'] = $_GET['ln'];
    }

    foreach($_SESSION as $ind => $val){
        $_SESSION[$ind] = $val;
    }

    function Verifica($Verifica = []){
        $blq = false;
        foreach($Verifica as $ind => $val){
            if(!$val) $blq = true;
        }

        if($blq){
            echo "<script>window.location.href='./';</script>";
            exit();
        }
    }