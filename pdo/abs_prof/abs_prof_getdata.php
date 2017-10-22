<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$page          = isset($_POST['page']) ? intval($_POST['page']) : 1;  
$rows          = isset($_POST['rows']) ? intval($_POST['rows']) : 20;  
$sort          = isset($_POST['sort']) ? strval($_POST['sort']) : '2,9';  
$order         = isset($_POST['order']) ? strval($_POST['order']) : 'asc';

$id_professor  = isset($_SESSION['professor']) ? $_SESSION['professor'] : 0 ;
$data_inici    = isset($_REQUEST['data_inici_tutor']) ? substr($_REQUEST['data_inici_tutor'],6,4)."-".substr($_REQUEST['data_inici_tutor'],3,2)."-".substr($_REQUEST['data_inici_tutor'],0,2) : getCursActual($db)["data_inici"];
$data_fi       = isset($_REQUEST['data_fi_tutor'])    ? substr($_REQUEST['data_fi_tutor'],6,4)."-".substr($_REQUEST['data_fi_tutor'],3,2)."-".substr($_REQUEST['data_fi_tutor'],0,2)          : getCursActual($db)["data_fi"];
$id_tipus_incidencia = isset($_REQUEST['id_tipus_incidencia']) ? $_REQUEST['id_tipus_incidencia'] : 0 ;

if (isset($_REQUEST['id_tipus_incidencia']) && $_REQUEST['id_tipus_incidencia']!='') {
	//$where = " AND ip.id_tipus_incidencia=$id_tipus_incidencia";
	$where = " ";
}
else {
	$where = " ";
}

$offset     = ($page-1)*$rows;
$result     = array();

$sql  = "SELECT count(*)";
$sql .= "FROM incidencia_professor ip ";
$sql .= "WHERE idprofessors=$id_professor AND ip.data BETWEEN '".$data_inici."' AND '".$data_fi."'";

$rs = $db->query($sql);

foreach($rs->fetchAll() as $row) {
    $result["total"] = $row[0];
}

$sql  = "SELECT distinct(ip.idincidencia_professor),ip.data,ip.comentari,";
$sql .= "CONCAT(SUBSTR(ip.data,9,2),'-',SUBSTR(ip.data,6,2),'-',SUBSTR(ip.data,1,4)) AS data_incidencia, ";
$sql .= "ip.id_tipus_incidencia,tf.tipus_falta,m.nom_materia, ";
$sql .= "CONCAT(ELT(WEEKDAY(ip.data) + 1, 'DILLUNS', 'DIMARTS', 'DIMECRES', 'DIJOUS', 'DIVENDRES', 'DISSABTE', 'DIUMENGE')) AS dia, ";
$sql .= "CONCAT(LEFT(fh.hora_inici,5),'-',LEFT(fh.hora_fi,5)) AS hora,ip.idfranges_horaries ";
$sql .= "FROM incidencia_professor ip ";
$sql .= "INNER JOIN franges_horaries       fh ON  ip.idfranges_horaries   = fh.idfranges_horaries ";
$sql .= "INNER JOIN tipus_falta_professor  tf ON  ip.id_tipus_incidencia  = tf.idtipus_falta_professor ";	 
$sql .= "INNER JOIN materia                 m ON  ip.id_mat_uf_pla        = m.idmateria ";
$sql .= "WHERE idprofessors=$id_professor AND ip.data BETWEEN '".$data_inici."' AND '".$data_fi."' $where ";

$sql .= "UNION ";

$sql .= "SELECT distinct(ip.idincidencia_professor),ip.data,ip.comentari,";
$sql .= "CONCAT(SUBSTR(ip.data,9,2),'-',SUBSTR(ip.data,6,2),'-',SUBSTR(ip.data,1,4)) AS data_incidencia, ";
$sql .= "ip.id_tipus_incidencia,tf.tipus_falta,CONCAT(m.nom_modul,'-',uf.nom_uf) AS nom_materia, ";
$sql .= "CONCAT(ELT(WEEKDAY(ip.data) + 1, 'DILLUNS', 'DIMARTS', 'DIMECRES', 'DIJOUS', 'DIVENDRES', 'DISSABTE', 'DIUMENGE')) AS dia, ";
$sql .= "CONCAT(LEFT(fh.hora_inici,5),'-',LEFT(fh.hora_fi,5)) AS hora,ip.idfranges_horaries ";
$sql .= "FROM incidencia_professor ip ";
$sql .= "INNER JOIN franges_horaries       fh ON  ip.idfranges_horaries   = fh.idfranges_horaries ";
$sql .= "INNER JOIN tipus_falta_professor  tf ON  ip.id_tipus_incidencia  = tf.idtipus_falta_professor ";	 
$sql .= "INNER JOIN unitats_formatives     uf ON  ip.id_mat_uf_pla     = uf.idunitats_formatives ";
$sql .= "INNER JOIN moduls_ufs             mu ON  ip.id_mat_uf_pla     = mu.id_ufs ";
$sql .= "INNER JOIN moduls                  m ON  mu.id_moduls         = m.idmoduls ";
$sql .= "WHERE idprofessors=$id_professor AND ip.data BETWEEN '".$data_inici."' AND '".$data_fi."' $where ";

$sql .= "ORDER BY $sort $order";

$rs = $db->query($sql);

$items = array();  
foreach($rs->fetchAll() as $row) {  
    array_push($items, $row);  
}  
$result["rows"] = $items;  
  
echo json_encode($result);

//$rs->closeCursor();
//mysql_close();
?>