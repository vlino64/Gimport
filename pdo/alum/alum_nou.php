<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$codi_alumnes_saga = $_REQUEST['codi_alumnes_saga'];

$sql               = "insert into alumnes(codi_alumnes_saga) values ('$codi_alumnes_saga')";
$result            = $db->query($sql);
$id_alumne         = $db->lastInsertId();

$sql               = "insert into families() values ()";
$result            = $db->query($sql);
$id_families       = $db->lastInsertId();

$sql               = "insert into alumnes_families(idalumnes,idfamilies) values ($id_alumne,$id_families)";
$result            = $db->query($sql);

$dadesalumneArray  = array(TIPUS_nom_complet,TIPUS_iden_ref,TIPUS_cognom1_alumne,
			   TIPUS_cognom2_alumne,TIPUS_nom_alumne,TIPUS_genere,
			   TIPUS_a_determinar,TIPUS_nom_grup,TIPUS_login,TIPUS_contrasenya);	
						   
for ($tipus_contacte = 1; $tipus_contacte <= TOTAL_TIPUS_CONTACTE; $tipus_contacte++) {
	$sql = '';
        if ($tipus_contacte != TIPUS_contrasenya_notifica) {
		$valor  = str_replace("'","\'",$_REQUEST[$tipus_contacte]);
	}
	
	if ($tipus_contacte == TIPUS_contrasenya ) {
		$valor  = md5($valor);
	}
	
	if ($tipus_contacte == TIPUS_nom_complet ) {
		$valor_mostrar = $valor;
	}
	
	if ($tipus_contacte != TIPUS_contrasenya_notifica) {
		if (in_array($tipus_contacte, $dadesalumneArray)) {
			$sql    = "insert into contacte_alumne(id_alumne,id_tipus_contacte,Valor) values ($id_alumne,$tipus_contacte,'$valor')";
			$result = $db->query($sql);
		}
		else {
			$sql    = "insert into contacte_families(id_families,id_tipus_contacte,Valor) values ($id_families,$tipus_contacte,'$valor')";
			$result = $db->query($sql);
		}
	}
	
}

if ($result){
	echo json_encode(array(
			'codi_alumnes_saga' => $codi_alumnes_saga,
			'Valor' => $valor_mostrar
		 ));
	} else {
		echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
	}
	
//mysql_close();
?>
