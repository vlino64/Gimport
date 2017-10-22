<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id_alumne         = intval($_REQUEST['id']);
$id_families       = getFamiliaAlumne($db,$id_alumne);

if ($id_families==0) {
	$sql         = "insert into families() values ()";
	$result      = $db->query($sql);
	$id_families = $db->lastInsertId();
	
	$sql         = "insert into alumnes_families(idalumnes,idfamilies) values ('$id_alumne','$id_families')";
	$result      = $db->query($sql);
}
//$codi_alumnes_saga = getCodiSagaAlumne($db,$id_alumne);
$codi_alumnes_saga = $_REQUEST['codi_alumnes_saga'];
$valor_mostrar     = "";
$dadesalumneArray  = array(TIPUS_nom_complet,TIPUS_iden_ref,TIPUS_cognom1_alumne,
  			   TIPUS_cognom2_alumne,TIPUS_nom_alumne,TIPUS_email,
			   TIPUS_login,TIPUS_contrasenya,TIPUS_data_naixement);

$dadesocultesArray  = array(TIPUS_contrasenya,TIPUS_contrasenya_notifica,TIPUS_contrasenya_notifica2,
  			    TIPUS_login1,TIPUS_login2,TIPUS_contrasenya2,TIPUS_nom_profe,
                            TIPUS_cognoms_profe);

for ($tipus_contacte = 1; $tipus_contacte <= TOTAL_TIPUS_CONTACTE; $tipus_contacte++) {
	$sql = '';
        /*
        if ($tipus_contacte != TIPUS_contrasenya_notifica) {
		$valor  = str_replace("'","\'",$_REQUEST[$tipus_contacte]);
	}
	
	if ($tipus_contacte == TIPUS_contrasenya ) {
		$valor  = md5($valor);
	}
        */
        
        if ((! in_array($tipus_contacte, $dadesocultesArray)) && existsIDTipusContacte($db,$tipus_contacte)) {
                
                $valor  = str_replace("'","\'",$_REQUEST['elem'.$tipus_contacte]);
                
		if ($tipus_contacte == TIPUS_nom_complet ) {
			$valor_mostrar = $valor;
		}
		
		if (in_array($tipus_contacte, $dadesalumneArray)) {
		
			if ( existValorTipusContacteAlumne($db,$id_alumne,$tipus_contacte) ) {   
                            $sql = "update contacte_alumne set Valor='$valor' where id_alumne=$id_alumne and id_tipus_contacte=$tipus_contacte";
			}
			else {
                            $sql = "insert into contacte_alumne(id_alumne,id_tipus_contacte,Valor) values ($id_alumne,$tipus_contacte,'$valor')";
			}
                               
                        $result = $db->query($sql);
			
		}
		else {
			if ($tipus_contacte != TIPUS_contrasenya_notifica) {
				if ( existValorTipusContacteFamilies($db,$id_alumne,$tipus_contacte) ) {   
                                    $sql = "update contacte_families set Valor='$valor' where id_families=$id_families and id_tipus_contacte=$tipus_contacte";
				}
				else {
                                    $sql = "insert into contacte_families(id_families,id_tipus_contacte,Valor) values ($id_families,$tipus_contacte,'$valor')";
				}
                                
                                $result = $db->query($sql);
			}
		}
	
	}
	
	
}

echo json_encode(array(
	'codi_alumnes_saga' => $codi_alumnes_saga,
	'Valor' => $valor_mostrar
));
		
/*if ($result){
		echo json_encode(array(
			'codi_alumnes_saga' => $codi_alumnes_saga,
			'Valor' => $valor_mostrar
		));
	} else {
		echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
	}*/

//mysql_close();
?>