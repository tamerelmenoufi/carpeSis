<?php

    function dataBr($dt){
        list($d, $h) = explode(" ",$dt);
        list($y,$m,$d) = explode("-",$d);
        $data = false;
        if($y*1 && $m*1 && $d*1){
            $data = "{$d}/{$m}/$y".(($h)?" {$h}":false);
        }
        return $data;
    }

    function dataMysql($dt){
        list($d, $h) = explode(" ",$dt);
        list($d,$m,$y) = explode("/",$d);
        $data = false;
        if($y*1 && $m*1 && $d*1){
            $data = "{$y}-{$m}-$d".(($h)?" {$h}":false);
        }
        return $data;
    }

