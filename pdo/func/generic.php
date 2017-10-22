<?php
/* ************************
    BIBLIOTECA DE FUNCIONS
	AUTOR: TONI LÓPEZ
	ANY: 2013
 *      DARRERA REVISIÓ: JUNY 2017
***************************  */

// Defincions tipus_contacte
define("TOTAL_TIPUS_CONTACTE", countallTipusContacte($db));
	
for ($i=1;$i<=TOTAL_TIPUS_CONTACTE;$i++) {
   if (existsIDTipusContacte($db,$i)) {
     $nom_info  = getNomInfoTipusContacte($db,$i);
     define("TIPUS_".$nom_info, $i);
   }
}

// Includes
include_once('func_alumnes.php');
include_once('func_families.php');
include_once('func_professors.php');
include_once('func_materies.php');
include_once('func_grups.php');
include_once('func_espaiscentre.php');
include_once('func_incidencies_alumne.php');
include_once('func_incidencies_professor.php');
include_once('func_guardies.php');
include_once('func_ccc.php');
include_once('func_dies.php');
include_once('func_altres_hores.php');

/*  ********************************************************************************************************
/*   getVersio --> Torna la versió de la instal.lació actual
************************************************************************************************************ */
function getVersio($db) {
    $sql = "SELECT versio FROM  `versio_bdd` ORDER BY versio DESC LIMIT 1 ";
    $rec = $db->query($sql);
    foreach($rec->fetchAll() as $row) {
        
    }
    return $row["versio"];
}

/*  ********************************************************************************************************
/*   treureAccents --> Treure accents d'un string
************************************************************************************************************ */
function treureAccents ($cadena){
    $originales  = "ÀÁÂÄÅàáâäÒÓÔÖòóôöÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ";
    $modificadas = "AAAAAaaaaOOOOooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn";

    $cadena = strtr($cadena, utf8_decode($originales), $modificadas);
    $cadena = strtolower($cadena);
    return utf8_encode($cadena);
}

/*  ********************************************************************************************************
/*   treureAccentsSms --> Treure accents d'un string
************************************************************************************************************ */

function treureAccentsSms ($cadena){
    $originales  = array("À","Á","à","á","Ò","Ó","ò","ó","È","É","è","é","Ç","ç","Ì","Í","ì","í","Ù","Ú","Ü","ù","ú","ü");
    $modificadas = array("A","A","à","a","O","O","ò","o","E","E","è","é","C","c","I","I","ì","i","U","U","U","ù","u","u");

    $cadena = str_replace($originales,$modificadas,$cadena);
    return $cadena;
}
	
/*  ********************************************************************************************************
/*   getDadesCentre --> Torna les dades del centre
************************************************************************************************************ */
function getDadesCentre($db) {
    $sql = "SELECT * FROM dades_centre WHERE iddades_centre = 1";
    $rec = $db->query($sql);
    
    $count = 0;
    $result = "";
    
    foreach($rec->fetchAll() as $elem) {
        $count++;
	$result = $elem;
    }

    //mysql_free_result($rec);
    return $result;
}
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
/*   getModulsActius --> Torna els moduls addcionals que el centre té actius
************************************************************************************************************ */
function getModulsActius($db) {
    $sql = "SELECT * FROM config";
    $rec = $db->query($sql);
    $count = 0;
    $result = "";
    foreach($rec->fetchAll() as $row) {
	$count++;
	$result = $row;
    }
	//mysql_free_result($rec);
	if ($count == 0) {
            return 0;
	}
	else {  
            return $result;
	}
}
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
/*   diaSemana --> Torna el dia de la setmana d'una determinada data 
************************************************************************************************************ */
function diaSemana($any,$mes,$dia) {
	// 0->diumenge	 | 6->dissabte
	$dia= date("w",mktime(0, 0, 0, $mes, $dia, $any));
	return $dia;
}
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
/*   getTipusContacte --> Torna el ID de la taula  
************************************************************************************************************ */
function existsIDTipusContacte($db,$idtipus_contacte) {
    $sql  = "SELECT idtipus_contacte FROM tipus_contacte ";
    $sql .= "WHERE idtipus_contacte = $idtipus_contacte";
    
    $rec = $db->query($sql);
    $count = 0;
    $result = "";
    
    foreach($rec->fetchAll() as $row) {
        $count++;
	$result = $row;
    }
   
	//mysql_free_result($rec);
	if ($count == 0) {
		return 0;
	}
	else {  
    	return $result["idtipus_contacte"];
	}
}
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
/*   getTipusContacte --> Torna el ID de la taula  
************************************************************************************************************ */
function getTipusContacte($db,$strTipusContacte) {
    $sql  = "SELECT idtipus_contacte FROM tipus_contacte ";
    $sql .= "WHERE Nom_info_contacte = '$strTipusContacte'";
    $rec = $db->query($sql);
    $count = 0;
    $result = "";
    foreach($rec->fetchAll() as $elem) {
			$count++;
			$result = $row;
	}
	//mysql_free_result($rec);
	if ($count == 0) {
		return 0;
	}
	else {  
    	return $result["idtipus_contacte"];
	}
}
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
/*   getNomInfoTipusContacte --> Torna el Nominfo de la taula tipus_contacte
************************************************************************************************************ */
function getNomInfoTipusContacte($db,$idtipus_contacte) {
    $sql  = "SELECT Nom_info_contacte FROM tipus_contacte ";
    $sql .= "WHERE idtipus_contacte = $idtipus_contacte";
    $rec = $db->query($sql);
    $count = 0;
    $result = "";
    foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
	}
	//mysql_free_result($rec);
	if ($count == 0) {
		return 0;
	}
	else {  
    	return $result["Nom_info_contacte"];
	}
}
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getallPlaEstudis --> Obtè tots els registres de la taula plans_estudis
************************************************************************************************************ */
function getallPlaEstudis($db) {
     $sql  = "SELECT * FROM plans_estudis";
     $rec = $db->query($sql);
	 
     return $rec;
}
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getallPlaEstudis --> Obtè tots els registres de la taula plans_estudis
************************************************************************************************************ */
function getPlaEstudisMateria($db,$id_mat_uf_pla) {
     $sql  = "SELECT idplans_estudis FROM moduls_materies_ufs WHERE id_mat_uf_pla = $id_mat_uf_pla ";
     $rec = $db->query($sql);
	 
     $count = 0;
     $result = "";
     foreach($rec->fetchAll() as $elem) {
			$count++;
			$result = $row;
     }
	 //mysql_free_result($rec);
	 if ($count == 0) {
		return 0;
	 }
	 else {  
    	return $result["idplans_estudis"];
	 }
}
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getallTipusContacte --> Obtè tots els registres de la taula tipus_contacte
************************************************************************************************************ */
function getallTipusContacte($db) {
     $sql  = "SELECT * FROM tipus_contacte ORDER BY ordre";
     $rec = $db->query($sql);
	 
     return $rec;
}
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	countallTipusContacte --> Conta tots els registres de la taula tipus_contacte
************************************************************************************************************ */
function countallTipusContacte($db) {
     $sql  = "SELECT MAX(idtipus_contacte) AS total FROM tipus_contacte";
     $rec = $db->query($sql);
	 
     $count = 0;
     $result = "";
     
     foreach($rec->fetchAll() as $row) {
        $count++;
	$result = $row;
     }
    
     //mysql_free_result($rec);
     return $result['total'];
}
/* ********************************************************************************************************* */

/*   getDiaSetmana --> Dia setmana */
function getDiaSetmana($db,$iddies_setmana) {
    $sql = "SELECT dies_setmana FROM dies_setmana WHERE iddies_setmana = $iddies_setmana";
    $rec = $db->query($sql);
    $count = 0;
    $result = "";
    foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
    }
	//mysql_free_result($rec);
    return $result["dies_setmana"];
}
/* ********************************************************************************************************* */

/*   getDiaSetmana --> Dia setmana */
function getMes($idmes) {
    $idmes       = intval($idmes);
    $array_mesos = array("GENER","FEBRER","MARÇ","ABRIL","MAIG","JUNY","JULIOL","AGOST","SETEMBRE","OCTUBRE","NOVEMBRE","DESEMBRE");

    return $array_mesos[$idmes-1];
}
/* ********************************************************************************************************* */

/*   getFrangesHoraries --> Totes les franges horàries */
function getFrangesHoraries($db) {
    $sql = "SELECT * FROM franges_horaries WHERE activada = 'S' AND esbarjo <> 'S' ORDER BY hora_inici";
    $rec = $db->query($sql);
    
    return $rec;
}
/* ********************************************************************************************************* */

/*   getFranjaHoraria --> Dades d'una franja horària */
function getFranjaHoraria($db,$idfranges_horaries) {
    $sql = "SELECT * FROM franges_horaries WHERE idfranges_horaries = $idfranges_horaries";
    $rec = $db->query($sql);
    
    $count = 0;
    $result = "";
    foreach($rec->fetchAll() as $row) {
	$count++;
	$result = $row;
    }
	//mysql_free_result($rec);
    return $result;
}
/* ********************************************************************************************************* */

/*   getLiteralFranjaHoraria --> Franges horària */
function getLiteralFranjaHoraria($db,$idfranges_horaries) {
    $sql = "SELECT CONCAT(LEFT(hora_inici,5),'-',LEFT(hora_fi,5)) AS hora  FROM franges_horaries WHERE idfranges_horaries = $idfranges_horaries";
    $rec = $db->query($sql);
    
    $count = 0;
    $result = "";
    foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
	}
	//mysql_free_result($rec);
    if ($count == 0) {
		return "";
	}
	else {
		return $result["hora"];
	}
}
/* ********************************************************************************************************* */

/*  getCursActual --> Curso actual */
function getCursActual($db) {
    $sql = "SELECT * FROM periodes_escolars WHERE actual = 'S'";
    $rec = $db->query($sql);
    $count = 0;
    $result = "";
    foreach($rec->fetchAll() as $row) {
	$count++;
	$result = $row;
    }
    //mysql_free_result($rec);
    return $result;
}
/* ********************************************************************************************************* */

/*  getTorn --> Dades torn */
function getTorn($db,$idtorn) {
    $sql = "SELECT * FROM torn WHERE idtorn = '$idtorn'";
    $rec = $db->query($sql);
    $count = 0;
    $result = "";
    foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
	}
	//mysql_free_result($rec);
	return $result;
}
/* ********************************************************************************************************* */

/*  ******************************************************************************************************** */
/*   getLiteralCarrec --> Nom d'un carrec 
************************************************************************************************************ */
function getLiteralCarrec($db,$idcarrecs) {
    $sql = "SELECT * FROM carrecs WHERE idcarrecs=".$idcarrecs;
    $rec = $db->query($sql);
    
    $count = 0;
    $result = "";
    foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
	}
	//mysql_free_result($rec);
    return $result;
}
/* ********************************************************************************************************* */

/*  getIdTutor --> ID del càrrec Tutor a la taula mestra carrecs */
function getIdTutor($db) {
    $sql = "SELECT idcarrecs FROM carrecs WHERE nom_carrec = 'TUTOR'";
    $rec = $db->query($sql);
    $count = 0;
    $result = "";
    foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
	}
	//mysql_free_result($rec);
    return $result;
}
/* ********************************************************************************************************* */

/*  comprovarHoraDia --> comprovar franja horaria */
function comprovarHoraDia($db,$franja) {	
    $sql = "SELECT idfranges_horaries FROM franges_horaries WHERE hora_inici<='$franja' AND hora_fi>='$franja' LIMIT 0,1";
    $rec = $db->query($sql);
    $count = 0;
    $result = "";
	
    foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
	}
	//mysql_free_result($rec);
	if ($count == 0) {
            return 0;
	}
	else {  
            return $result["idfranges_horaries"];
	}
}
/* ********************************************************************************************************* */

/*  comprovarHoraDiaTorn --> comprovar franja horaria diferents torns */
function comprovarHoraDiaTorn($db,$franja) {	
    $sql = "SELECT idfranges_horaries FROM franges_horaries WHERE hora_inici<='$franja' AND hora_fi>='$franja'";
    $rec = $db->query($sql);
    $count = 0;
    $result = "";
		
    return $rec;
}
/* ********************************************************************************************************* */

/*  getSortida --> Dades sortida */
function getSortida($db,$idsortides) {
    $sql = "SELECT * FROM sortides WHERE idsortides = '$idsortides'";
    $rec = $db->query($sql);
    $count = 0;
    $result = "";
    foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
	}
	//mysql_free_result($rec);
	return $result;
}
/* ********************************************************************************************************* */

/*  getUnitatsClasse --> Dades horari */
function getUnitatsClasse($db,$idunitats_classe) {
    $sql = "SELECT * FROM unitats_classe WHERE idunitats_classe = '$idunitats_classe'";
    $rec = $db->query($sql);
    $count = 0;
    $result = "";
    foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
	}
	//mysql_free_result($rec);
	return $result;
}
/* ********************************************************************************************************* */

/*  getDiesFranges --> Dades dia-franja horària */
function getDiesFranges($db,$id_dies_franges) {
    $sql = "SELECT * FROM dies_franges WHERE id_dies_franges = '$id_dies_franges'";
    $rec = $db->query($sql);
    $count = 0;
    $result = "";
    foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
	}
	//mysql_free_result($rec);
	return $result;
}
/* ********************************************************************************************************* */

/*  existDiesFranges --> Existeix id per dia i franja */
function existDiesFranges($db,$dia,$franja) {
    $sql = "SELECT id_dies_franges FROM dies_franges WHERE iddies_setmana=$dia AND idfranges_horaries=$franja";
    $rec = $db->query($sql);
    $count = 0;
    $result = "";
    foreach($rec->fetchAll() as $row) {
			$count++;
			$result = $row;
	}
	//mysql_free_result($rec);
	
	if ($count==0) {
		return 0;
	}
	else {
		return $result["id_dies_franges"];
	}
}
/* ********************************************************************************************************* */

?>