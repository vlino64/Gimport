<?php
session_start();	 
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');

$idprofdireccio  = intval($_REQUEST['id']);

$sql = "DELETE FROM prof_direccio WHERE idprofdireccio=$idprofdireccio";

/*$fp = fopen("log.txt","a");
fwrite($fp, $sql . PHP_EOL);
fclose($fp);*/

$result = $db->query($sql);
if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>$sql));
}

//mysql_close();
?>