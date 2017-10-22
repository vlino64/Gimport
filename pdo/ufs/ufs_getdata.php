<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->query("SET NAMES 'utf8'");

$s_pe    = isset($_POST['s_pe']) ? $_POST['s_pe'] : '';

$sql  = "SELECT ufs.*,mmuf.*,pe.Acronim_pla_estudis FROM unitats_formatives ufs ";
$sql .= "INNER JOIN moduls_materies_ufs mmuf ON ufs.idunitats_formatives=mmuf.id_mat_uf_pla ";
$sql .= "INNER JOIN plans_estudis pe ON mmuf.idplans_estudis=pe.idplans_estudis ";
$sql .= "WHERE mmuf.id_mat_uf_pla<>0 ";

if (isset($_POST['s_pe'])) {
   $sql .= "AND mmuf.idplans_estudis=".$s_pe;
}

/*$myFile = "log.txt";
$fh = fopen($myFile, 'a') or die("can't open file");
fwrite($fh, $sql."<br>");*/

$rs = $db->query($sql);

$result = array();
foreach($rec->fetchAll() as $row) {
	array_push($result, $row);
}

echo json_encode($result);

$rs->closeCursor();
//mysql_close();
?>

