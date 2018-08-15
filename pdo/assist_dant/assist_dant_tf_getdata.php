<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");
$rs = $db->query('select * from tipus_falta_alumne where idtipus_falta_alumne in ('.TIPUS_FALTA_ALUMNE_ABSENCIA.','.TIPUS_FALTA_ALUMNE_RETARD.','.TIPUS_FALTA_ALUMNE_JUSTIFICADA.','.TIPUS_FALTA_ALUMNE_SEGUIMENT.')');
$result = array();
foreach($rs->fetchAll() as $row) {
    array_push($result, $row);
}
echo json_encode($result);

$rs->closeCursor();
?>