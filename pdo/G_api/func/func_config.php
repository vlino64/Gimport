<?php

function configApp($db) {
    $result = array();
    $sql = "SELECT * FROM config_app;";
    $rs = $db->query($sql);
    foreach ($rs->fetchAll() as $row) {
        array_push($result, array(
            'notif_abs' => $row['notif_abs'],
            'notif_ret' => $row['notif_ret'],
            'notif_seg' => $row['notif_seg'],
            'notif_ccc' => $row['notif_ccc'],
            'comunic_abs' => $row['comunic_abs']));
        ;
    }
    return $result;
}

function insereixToken($db, $token) {
    $sql = "DELETE FROM conf_app_token;";
    $rs = $db->query($sql);
    $sql = "INSERT INTO conf_app_token(token) VALUES ('".$token."');";
    $rs = $db->query($sql);
}

function extreuToken($db){
    $sql = "SELECT token FROM conf_app_token;";
    $rs = $db->query($sql);
    $row = $rs->fetch();
    return $row['token'];
}