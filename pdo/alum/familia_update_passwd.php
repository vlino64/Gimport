<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id_alumne      = intval($_REQUEST['id']);
$id_families    = getFamiliaAlumne($db,$id_alumne);
$afegir_tutor_2 = isset($_REQUEST['afegir_tutor_2']) ? 1 : 0 ;

if ($id_families==0) {
	$sql         = "insert into families() values ()";
	$result      = $db->query($sql);
	$id_families = $db->lastInsertId();
	
	$sql         = "insert into alumnes_families(idalumnes,idfamilies) values ('$id_alumne','$id_families')";
	$result      = $db->query($sql);
}

$login_tutor_1  = $_REQUEST['login_tutor_1'];
$contrasenya_1_tutor_1  = $_REQUEST['contrasenya_1_tutor_1'];
$contrasenya_2_tutor_1  = $_REQUEST['contrasenya_2_tutor_1'];

if ( ($contrasenya_1_tutor_1==$contrasenya_2_tutor_1) ) {
   // Accés per la familia per veure tot(e)s els/les german(e)s
   $sql  = "SELECT a.idalumnes FROM alumnes a ";
   $sql .= "INNER JOIN alumnes_families af ON af.idalumnes=a.idalumnes ";
   $sql .= "WHERE af.idfamilies=".$id_families;
   $rs = $db->query($sql);
   foreach($rs->fetchAll() as $row) {
      $sql_al = "update alumnes set acces_familia='S' where idalumnes=".$row['idalumnes'];
      $result = $db->query($sql_al);
   }	   
   $rs->closeCursor();
   
      
   $sql = "delete from contacte_families where id_families=$id_families and id_tipus_contacte=".TIPUS_login;
   $result = $db->query($sql);
   $sql = "delete from contacte_families where id_families=$id_families and id_tipus_contacte=".TIPUS_contrasenya;
   $result = $db->query($sql);
   $sql = "delete from contacte_families where id_families=$id_families and id_tipus_contacte=".TIPUS_contrasenya_notifica;
   $result = $db->query($sql);
   
   $sql = "delete from contacte_families where id_families=$id_families and id_tipus_contacte=".TIPUS_login2;
   $result = $db->query($sql);
   $sql = "delete from contacte_families where id_families=$id_families and id_tipus_contacte=".TIPUS_contrasenya2;
   $result = $db->query($sql);
   $sql = "delete from contacte_families where id_families=$id_families and id_tipus_contacte=".TIPUS_contrasenya_notifica2;
   $result = $db->query($sql);
	   
   $sql = "insert into contacte_families(id_families,id_tipus_contacte,Valor) values ($id_families,".TIPUS_login.",'".$login_tutor_1."')";
   $result = $db->query($sql); 
   $sql = "insert into contacte_families(id_families,id_tipus_contacte,Valor) values ($id_families,".TIPUS_contrasenya.",'".MD5($contrasenya_1_tutor_1)."')";
   $result = $db->query($sql);
   $sql = "insert into contacte_families(id_families,id_tipus_contacte,Valor) values ($id_families,".TIPUS_contrasenya_notifica.",'".$contrasenya_1_tutor_1."')";
   $result = $db->query($sql);
   
}

if ($afegir_tutor_2) {
	$login_tutor_2  = $_REQUEST['login_tutor_2'];
	$contrasenya_1_tutor_2  = $_REQUEST['contrasenya_1_tutor_2'];
	$contrasenya_2_tutor_2  = $_REQUEST['contrasenya_2_tutor_2'];
	
	if ( ($contrasenya_1_tutor_2==$contrasenya_2_tutor_2) ) {  
	    $sql = "insert into contacte_families(id_families,id_tipus_contacte,Valor) values ($id_families,".TIPUS_login2.",'".$login_tutor_2."')";
   	    $result = $db->query($sql); 
            $sql = "insert into contacte_families(id_families,id_tipus_contacte,Valor) values ($id_families,".TIPUS_contrasenya2.",'".MD5($contrasenya_1_tutor_2)."')";
            $result = $db->query($sql);
            $sql = "insert into contacte_families(id_families,id_tipus_contacte,Valor) values ($id_families,".TIPUS_contrasenya_notifica2.",'".$contrasenya_1_tutor_2."')";
            $result = $db->query($sql);
	}
}


if ($result){
		echo json_encode(array('success'=>true));
	} else {
		echo json_encode(array('msg'=>'Algunes contrasenyes no coincideixen.'));
	}

//mysql_close();
?>