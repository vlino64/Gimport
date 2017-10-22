<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idgrups   = isset($_REQUEST['idgrups']) ? $_REQUEST['idgrups'] : 0;
$rsAlumnes = getAlumnesGrup($db,$idgrups,TIPUS_nom_complet);

foreach($rsAlumnes->fetchAll() as $row) {
    $sql       = "update alumnes set acces_alumne='S' where idalumnes=$row['idalumnes']";
    $result    = $db->query($sql);
}

if ($result){
    echo json_encode(array('success'=>true));
} else {
    echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

if (isset($rsAlumnes)) {
    //mysql_free_result($rsAlumnes);
}
//mysql_close();
?>