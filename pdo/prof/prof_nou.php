<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$codi_professor = $_REQUEST['codi_professor'];
$sql            = "insert into professors(codi_professor,activat) values ('$codi_professor','S')";
$result         = $db->query($sql);
$id_professor   = $db->lastInsertId();

$dadesocultesArray  = array(TIPUS_contrasenya2,TIPUS_login1,TIPUS_login2,
  			    TIPUS_contrasenya_notifica,TIPUS_contrasenya_notifica2,
                            TIPUS_cognom1_alumne,TIPUS_cognom2_alumne,TIPUS_nom_alumne,
                            TIPUS_cognom1_pare,TIPUS_cognom2_pare,TIPUS_nom_pare,
                            TIPUS_cognom1_mare,TIPUS_cognom2_mare,TIPUS_nom_mare,
                            TIPUS_email1,TIPUS_email2,TIPUS_mobil_sms,TIPUS_mobil_sms2);

for ($tipus_contacte = 1; $tipus_contacte <= TOTAL_TIPUS_CONTACTE; $tipus_contacte++) {
    if ((! in_array($tipus_contacte, $dadesocultesArray)) && existsIDTipusContacte($db,$tipus_contacte)) {
        
        if ($tipus_contacte != TIPUS_contrasenya_notifica) {
		$valor  = str_replace("'","\'",$_REQUEST['elem'.$tipus_contacte]);
	}
	
	if ($tipus_contacte == TIPUS_contrasenya ) {
		$valor  = md5($valor);
	}
	
	if ($tipus_contacte == TIPUS_nom_complet ) {
		$valor_mostrar = $valor;
	}
		
	if ($tipus_contacte != TIPUS_contrasenya_notifica) {
		$sql = "insert into contacte_professor(id_professor,id_tipus_contacte,Valor) values ($id_professor,$tipus_contacte,'$valor')";          
	}
	$result = $db->query($sql);
	
    }
}

        if ($result){
		//echo json_encode(array('success'=>true));
		echo json_encode(array(
			'codi_professor' => $codi_professor,
			'Valor' => $valor_mostrar
		));
	} else {
		echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
	}

//mysql_close();
?>