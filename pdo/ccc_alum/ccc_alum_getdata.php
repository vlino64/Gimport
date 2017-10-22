<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$page    = isset($_POST['page']) ? intval($_POST['page']) : 1;  
$rows    = isset($_POST['rows']) ? intval($_POST['rows']) : 20;  
$sort    = isset($_POST['sort']) ? strval($_POST['sort']) : '10';  
$order   = isset($_POST['order']) ? strval($_POST['order']) : 'desc';

$idprofessor = isset($_SESSION['professor']) ? $_SESSION['professor'] : 0 ;
$data_inici   = isset($_REQUEST['data_inici']) ? substr($_REQUEST['data_inici'],6,4)."-".substr($_REQUEST['data_inici'],3,2)."-".substr($_REQUEST['data_inici'],0,2) : getCursActual($db)["data_inici"];
$data_fi      = isset($_REQUEST['data_fi'])    ? substr($_REQUEST['data_fi'],6,4)."-".substr($_REQUEST['data_fi'],3,2)."-".substr($_REQUEST['data_fi'],0,2)          : getCursActual($db)["data_fi"];

$offset = ($page-1)*$rows;
$result  = array();

$sql  = "SELECT COUNT(idccc_taula_principal) ";
$sql .= "FROM ccc_taula_principal ";
$sql .= "WHERE idprofessor=".$idprofessor." AND data BETWEEN '".$data_inici."' AND '".$data_fi."'";
  
$rs = $db->query($sql);  
$row = mysql_fetch_row($rs);  
$result["total"] = $row[0]; 

$sql  = "SELECT tp.*,ca.Valor AS alumne, ";
$sql .= "CONCAT(SUBSTR(tp.data,9,2),'-',SUBSTR(tp.data,6,2),'-',SUBSTR(tp.data,1,4)) AS data_ccc ";
$sql .= "FROM ccc_alumne_principal tp ";
$sql .= "INNER JOIN contacte_alumne    ca ON ca.id_alumne          = tp.idalumne ";
$sql .= "WHERE tp.idprofessor=".$idprofessor." AND ca.id_tipus_contacte=".TIPUS_nom_complet." AND tp.data BETWEEN '".$data_inici."' AND '".$data_fi."' ";
$sql .= " ORDER BY $sort $order LIMIT $offset,$rows";

$rs = $db->query($sql);

/*$fp = fopen("log.txt","a");
fwrite($fp, $sql . PHP_EOL);
fclose($fp);*/

$items = array();  
foreach($rec->fetchAll() as $row) {  
    array_push($items, $row);  
}  
$result["rows"] = $items;  
  
echo json_encode($result);

$rs->closeCursor();
//mysql_close();
?>
