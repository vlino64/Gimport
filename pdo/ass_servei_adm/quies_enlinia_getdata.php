<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$page   = isset($_POST['page']) ? intval($_POST['page']) : 1;  
$rows   = isset($_POST['rows']) ? intval($_POST['rows']) : 20;  
$sort   = isset($_POST['sort']) ? strval($_POST['sort']) : '3';  
$order  = isset($_POST['order']) ? strval($_POST['order']) : 'asc';

$offset = ($page-1)*$rows;

$data   = isset($_REQUEST['data']) ? substr($_REQUEST['data'],6,4)."-".substr($_REQUEST['data'],3,2)."-".substr($_REQUEST['data'],0,2) : date("Y-m-d");
$any    = substr($data,0,4);
$mes    = substr($data,5,2);
$dia    = substr($data,8,2);

$result = array();

$sql  = "SELECT LEFT(lp.hora,5) AS hora, lp.id_professor, ";
$sql .= "cp.Valor AS professor, apl.accions, lp.id_accio ";
$sql .= "FROM log_professors lp ";
$sql .= "INNER JOIN contacte_professor      cp ON lp.id_professor = cp.id_professor ";
$sql .= "INNER JOIN accions_professors_log apl ON lp.id_accio     = apl.idaccions_professors_log ";
$sql .= "WHERE lp.data='".$data."' AND (lp.id_accio=".TIPUS_ACCIO_LOGIN." OR lp.id_accio=".TIPUS_ACCIO_LOGOUT.") AND cp.id_tipus_contacte=".TIPUS_nom_complet." ";
$sql .= "ORDER BY $sort $order ";

/*$fp = fopen("log.txt","a");
fwrite($fp, $sql . PHP_EOL);
fclose($fp);*/

$rs = $db->query($sql);
  
$items = array();  
foreach($rs->fetchAll() as $row) {  
    array_push($items, $row);  
}  
$result["rows"] = $items;  
  
echo json_encode($result);

$rs->closeCursor();
//mysql_close();
?>
