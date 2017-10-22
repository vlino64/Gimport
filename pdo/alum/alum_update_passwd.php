<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id_alumne      = intval($_REQUEST['id']);
$id_families    = getFamiliaAlumne($db,$id_alumne);

if ($id_families==0) {
	$sql         = "insert into families() values ()";
	$result      = $db->query($sql);
	$id_families = $db->lastInsertId();
	
	$sql         = "insert into alumnes_families(idalumnes,idfamilies) values ('$id_alumne','$id_families')";
	$result      = $db->query($sql);
}

$contrasenya_1  = $_REQUEST['contrasenya_1'];
$contrasenya_2  = $_REQUEST['contrasenya_2'];

if ($contrasenya_1==$contrasenya_2) {
   $sql = "update alumnes set acces_alumne='S' where idalumnes=$id_alumne";
   $result = $db->query($sql);
   
   $sql = "delete from contacte_alumne where id_alumne=$id_alumne and id_tipus_contacte=".TIPUS_contrasenya;
   $result = $db->query($sql);
   
   $sql = "delete from contacte_alumne where id_alumne=$id_alumne and id_tipus_contacte=".TIPUS_contrasenya_notifica;
   $result = $db->query($sql);
   
   //$sql = "update contacte_alumne set Valor=MD5('$contrasenya_1') where id_alumne=$id_alumne and id_tipus_contacte=".TIPUS_contrasenya;
   $sql = "insert into contacte_alumne(id_alumne,id_tipus_contacte,Valor) values ($id_alumne,".TIPUS_contrasenya.",'".MD5($contrasenya_1)."')";
   $result = $db->query($sql);
   
   $sql = "insert into contacte_alumne(id_alumne,id_tipus_contacte,Valor) values ($id_alumne,".TIPUS_contrasenya_notifica.",'".$contrasenya_1."')";
   $result = $db->query($sql);
}

if ($result){
		echo json_encode(array('success'=>true));
	} else {
		echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
	}

//mysql_close();
?>