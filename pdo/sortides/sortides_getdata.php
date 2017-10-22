<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id_professor = isset($_SESSION['professor']) ? $_SESSION['professor'] : 0 ;

$page    = isset($_POST['page']) ? intval($_POST['page']) : 1;  
$rows    = isset($_POST['rows']) ? intval($_POST['rows']) : 20;  
$sort    = isset($_POST['sort']) ? strval($_POST['sort']) : '7';  
$order   = isset($_POST['order']) ? strval($_POST['order']) : 'desc';

$curs_escolar    = getCursActual($db)["idperiodes_escolars"];
$data_inici_curs = getCursActual($db)["data_inici"];
$data_fi_curs    = getCursActual($db)["data_fi"];

$offset = ($page-1)*$rows;
$result  = array();

$sql  = "SELECT COUNT(s.idsortides) ";
$sql .= "FROM sortides s ";
$sql .= "INNER JOIN sortides_professor sp ON sp.id_sortida = s.idsortides ";
$sql .= "WHERE sp.id_professorat=".$id_professor;
  
$rs = $db->query($sql);
foreach($rs->fetchAll() as $row) {
    $result["total"] = $row[0]; 
}

$sql  = "SELECT s.idsortides,s.hora_inici,s.hora_fi,s.lloc,s.descripcio,s.tancada,s.data_inici AS data, ";
$sql .= "CONCAT(SUBSTR(s.data_inici,9,2),'-',SUBSTR(s.data_inici,6,2),'-',SUBSTR(s.data_inici,1,4)) AS data_inici, ";
$sql .= "CONCAT(SUBSTR(s.data_fi,9,2),'-',SUBSTR(s.data_fi,6,2),'-',SUBSTR(s.data_fi,1,4)) AS data_fi ";
$sql .= "FROM sortides s ";
$sql .= "INNER JOIN sortides_professor sp ON sp.id_sortida = s.idsortides ";
$sql .= "WHERE sp.id_professorat=".$id_professor;
$sql .= " AND s.data_inici BETWEEN '$data_inici_curs' AND '$data_fi_curs' ";
$sql .= " ORDER BY $sort $order LIMIT $offset,$rows";

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
