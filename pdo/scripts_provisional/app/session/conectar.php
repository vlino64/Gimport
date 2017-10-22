<?php
	if(!isset($_SESSION)) {
		session_start();
	}
	$cabecera = array();
	$cabecera['cuerpo'] = array();
	$cabecera['errores'] = array();
	$cabecera['mensajes'] = array();
	$cabecera['errores'] = array();
	
	require_once("../config/db.php");
	include_once('../../../func/constants.php');
	include_once('../funciones/funciones.php');
	
	function login($arrays){
		$result = validaFamilia($_POST['username'],$_POST['password'],TIPUS_login,TIPUS_contrasenya);
		$login = array();
		if ( $result != 0 ) {
			$login['access'] = True;
			$login['usuari'] = $result;
			$login['familia'] = $result;
			$login['curs_escolar'] = getCursActual($db)["idperiodes_escolars"];
			$login['curs_escolar_literal'] = getCursActual($db)->Nom;
		}else {
			$login['access'] = False;
		}
		$arrays['cuerpo'] = array('login'=>$login);
		return $arrays;
		
	}
	
	function recogerAlumnos($cabecera){
		$cabecera['cuerpo']['alumnes'] = array();
		$cabecera['cuerpo']['materias'] = array();
		$cabecera['cuerpo']['assistencia'] = array();
		$cabecera['cuerpo']['missatgeria'] = array();
		$materias = array();
		$alumnes = array();
		$assistencia = array();
		$mensajeria = array();
		$rsAlumnes  = getAlumnesFamilia($cabecera['cuerpo']['login']['familia']);
		while ($row = mysql_fetch_assoc($rsAlumnes)) {
			$tmpAlumnes = utf8_encode(getValorTipusContacteAlumne($db,$row['idalumnes'],TIPUS_nom_complet));
			array_push($alumnes,array($row['idalumnes']=>$tmpAlumnes));
			array_push($materias,array($row['idalumnes']=>getHorariAlumne($row['idalumnes'],$cabecera['cuerpo']['login']['curs_escolar'],$cabecera['cuerpo']['login']['curs_escolar_literal'])));
			array_push($assistencia,array($row['idalumnes']=>getAssistencia($row['idalumnes'])));
			array_push($mensajeria,array($row['idalumnes']=>getMensajeria($row['idalumnes'])));
		}
		$cabecera['cuerpo']['alumnes'] = $alumnes;
		$cabecera['cuerpo']['materias'] = $materias;
		$cabecera['cuerpo']['assistencia'] = $assistencia;
		$cabecera['cuerpo']['missatgeria'] = $mensajeria;
		return $cabecera;
	}
	
	if(isset($_POST['android_id'])){
		if($_POST['funcion'] == "login"){
			$cabecera = login($cabecera);
		}else if($_POST['funcion'] == "getAlumnes"){
			$cabecera = login($cabecera);
			if($cabecera['cuerpo']['login']['access']){
				$cabecera = recogerAlumnos($cabecera);
			}else{
				array_push($cabecera['errores'],4);
			}
			
		}else if($_POST['funcion'] == "enviarMensaje"){
			$cabecera = login($cabecera);
			if($cabecera['cuerpo']['login']['access']){
				if(enviarMensaje($_POST['idAlumne'],$_POST['mensaje'])){
					$cabecera = recogerAlumnos($cabecera);
					$cabecera['correo'] = true;
				}else{
					$cabecera = recogerAlumnos($cabecera);
					$cabecera['correo'] = false;
					array_push($cabecera['errores'],5);
				}
			}else{
				array_push($cabecera['errores'],4);
			}
		}else if($_POST['funcion'] == "enviarError"){
			enviarError($_POST['android_id'],$_POST['mensaje']);
		}
		
	}else{
		array_push($cabecera['errores'],0);
	}
	$cabecera['errores']= array_merge($cabecera['errores'],$_SESSION['errores']);
	error_log(json_encode($cabecera,true)); 
	echo json_encode($cabecera,true);