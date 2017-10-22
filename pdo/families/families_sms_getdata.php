<?php
session_start();
//require_once('../func/seguretat.php');
//require_once('../bbdd/connect.php');
require_once('../bbdd/connect_sms.php');
require_once('../func/constants.php');
//require_once('../func/generic.php');
$dbSMS->exec("set names utf8");

$actual_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$page    = isset($_POST['page']) ? intval($_POST['page']) : 1;  
$rows    = isset($_POST['rows']) ? intval($_POST['rows']) : 20;  
$sort    = isset($_POST['sort']) ? strval($_POST['sort']) : '4';  
$order   = isset($_POST['order']) ? strval($_POST['order']) : 'desc';

$data_inici = isset($_REQUEST['data_inici']) ? substr($_REQUEST['data_inici'],6,4)."-".substr($_REQUEST['data_inici'],3,2)."-".substr($_REQUEST['data_inici'],0,2) : '1989-01-01'; 
$data_fi    = isset($_REQUEST['data_fi'])    ? substr($_REQUEST['data_fi'],6,4)."-".substr($_REQUEST['data_fi'],3,2)."-".substr($_REQUEST['data_fi'],0,2)          : '2189-01-01';
$idalumne   = isset($_REQUEST['idalumne']) ? intval($_REQUEST['idalumne']) : 0;

if ($idalumne != 0) {
    //$tlf_alumne = getAlumne($db,$idalumne,TIPUS_mobil_sms);
}

$offset = ($page-1)*$rows;
$result  = array();

$sql  = "SELECT COUNT(*) ";
$sql .= "FROM vista_log_sms vls ";
$sql .= "WHERE centre='".HEADER_SMS."' AND DATE(vls.data_hora) BETWEEN '".$data_inici."' AND '".$data_fi."' ";
if ($idalumne != 0) {
    //$sql .= " AND SUBSTR(vls.data_hora,3,9)='".$tlf_alumne."' ";
    $sql .= " AND vls.idAlumne=".$idalumne." ";
}

$rs = $dbSMS->query($sql);
foreach($rs->fetchAll() as $row) { 
    $result["total"] = $row[0]; 
}

$sql  = "SELECT vls.*, ";
$sql .= "CONCAT(SUBSTR(vls.data_hora,9,2),'-',SUBSTR(vls.data_hora,6,2),'-',SUBSTR(vls.data_hora,1,4)) AS data, ";
$sql .= "SUBSTR(vls.data_hora,11,9) AS hora ";
$sql .= "FROM vista_log_sms vls ";
$sql .= "WHERE centre='".HEADER_SMS."' AND DATE(vls.data_hora) BETWEEN '".$data_inici."' AND '".$data_fi."' ";
if ($idalumne != 0) {
    //$sql .= " AND SUBSTR(vls.data_hora,3,9)='".$tlf_alumne."' ";
    $sql .= " AND vls.idAlumne=".$idalumne." ";
}
$sql .= "ORDER BY $sort $order LIMIT $offset,$rows";

$rs = $dbSMS->query($sql);

$items = array();  
foreach($rs->fetchAll() as $row) {  
    array_push($items, $row);  
}  
$result["rows"] = $items;  
  
echo json_encode($result);

$rs->closeCursor();
//mysql_close();
?>
