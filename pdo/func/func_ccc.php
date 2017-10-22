<?php
/*  ********************************************************************************************************
	getCCC --> Detall d'una CCC
************************************************************************************************************ */
function getCCC($db,$idccc_taula_principal) {
    $sql  = "SELECT ccc_tp.* ";
    $sql .= "FROM ccc_taula_principal ccc_tp ";
    $sql .= "WHERE ccc_tp.idccc_taula_principal = ".$idccc_taula_principal;
    $rec = $db->query($sql);
         
    foreach($rec->fetchAll() as $row) {
	$count++;
	$result = $row;
    }
	 	 
     return $result;
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getLastThreeMothsCCCAlumne --> Darreres CCC d'un alumne
************************************************************************************************************ */
function getLastThreeMothsCCCAlumne($db,$idalumne) {
    $sql  = "SELECT data,hora,descripcio_detallada FROM ccc_taula_principal ";
    $sql .= "WHERE idalumne=".$idalumne." AND data > CURDATE() - INTERVAL 3 MONTH ";
    $sql .= "ORDER BY data DESC ";
    $rec = $db->query($sql);
    
    return $rec;
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	countLastThreeMothsCCCAlumne --> Darreres CCC d'un alumne
************************************************************************************************************ */
function countLastThreeMothsCCCAlumne($db,$idalumne) {
    $sql  = "SELECT COUNT(*) AS TOTAL FROM ccc_taula_principal ";
    $sql .= "WHERE data > CURDATE() - INTERVAL 3 MONTH ";
    $sql .= "ORDER BY data DESC ";
    $rec = $db->query($sql);
    foreach($rec->fetchAll() as $row) {
	$count++;
	$result = $row;
    }

    return $result['TOTAL'];
  }
/* ********************************************************************************************************* */
    

/*  ******************************************************************************************************** */
/*   getCCCEnteredbyAlumne --> Nom de CCC introduïdaper alumne
************************************************************************************************************ */
function getCCCEnteredbyAlumne($db,$idccc_alumne_principal) {
    $sql = "SELECT * FROM ccc_alumne_principal WHERE idccc_alumne_principal=".$idccc_alumne_principal;
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
/*   getTipusCCC --> Els tipus de CCC 
************************************************************************************************************ */
function getTipusCCC($db) {
    $sql = "SELECT * FROM ccc_tipus";
    $rec = $db->query($sql);
    
    return $rec;
}
/* ********************************************************************************************************* */

/*  ******************************************************************************************************** */
/*   getLiteralTipusCCC --> Nom d'un tipus de CCC 
************************************************************************************************************ */
function getLiteralTipusCCC($db,$idccc_tipus) {
    $sql = "SELECT * FROM ccc_tipus WHERE idccc_tipus=".$idccc_tipus;
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
/*   getMesuresCCC --> Les mesures de CCC 
************************************************************************************************************ */
function getMesuresCCC($db) {
    $sql = "SELECT * FROM ccc_tipus_mesura";
    $rec = $db->query($sql);
    
    return $rec;
}
/* ********************************************************************************************************* */

/*  ******************************************************************************************************** */
/*   getLiteralMesuresCCC --> Nom d'una mesura de CCC 
************************************************************************************************************ */
function getLiteralMesuresCCC($db,$idccc_tipus_mesura) {
    $sql = "SELECT * FROM ccc_tipus_mesura WHERE idccc_tipus_mesura=".$idccc_tipus_mesura;
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
/*   getMotiusCCC --> Els motius de CCC 
************************************************************************************************************ */
function getMotiusCCC($db) {
    $sql = "SELECT * FROM ccc_motius";
    $rec = $db->query($sql);
    
    return $rec;
}
/* ********************************************************************************************************* */

/*  ******************************************************************************************************** */
/*   getLiteralMotiusCCC --> Nom d'un motiu de CCC 
************************************************************************************************************ */
function getLiteralMotiusCCC($db,$idccc_motius) {
    $sql = "SELECT * FROM ccc_motius WHERE idccc_motius=".$idccc_motius;
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

/*  ********************************************************************************************************
	getCarrecsComunicacioTipusCCC --> Obtè els càrrecs als que s'els hi comunica un tipus de CCC
************************************************************************************************************ */
function getCarrecsComunicacioTipusCCC($db,$id_tipus) {
	 if ($id_tipus == 'undefined') {
	   $id_tipus = 0;
	 }
	 $sql  = "SELECT * FROM ccc_tipus_comunicacio_carrec WHERE id_tipus=$id_tipus ";
	 $rec = $db->query($sql);

	 return $rec;
}
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getCCCAlumne --> Total d'un alumne entre dates
	                      Per informes de faltes d'assistència 
************************************************************************************************************ */
function getCCCAlumne($db,$idalumne,$data_inici,$data_fi) {
	 $sql  = "SELECT ccc_tp.* ";
	 $sql .= "FROM ccc_taula_principal ccc_tp ";
	 $sql .= "WHERE ccc_tp.idalumne = ".$idalumne;
         $sql .= " AND ccc_tp.data BETWEEN '".$data_inici."' AND '".$data_fi."' ";
	 $sql .= "ORDER BY ccc_tp.data DESC ";
	 $rec = $db->query($sql);
	 	 
     return $rec;
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getCCCAlumneGrupMateria --> Total d'un alumne,grup i materia entre dates
************************************************************************************************************ */
function getCCCAlumneGrupMateria($db,$idalumne,$idgrup,$idmateria,$data_inici,$data_fi) {
	 $sql  = "SELECT ccc_tp.* ";
	 $sql .= "FROM ccc_taula_principal ccc_tp ";
	 $sql .= "WHERE ccc_tp.idalumne = ".$idalumne;
         $sql .= " AND ccc_tp.idgrup=".$idgrup." AND ccc_tp.idmateria=".$idmateria;
         $sql .= " AND ccc_tp.data BETWEEN '".$data_inici."' AND '".$data_fi."' ";
	 $sql .= "ORDER BY ccc_tp.data DESC ";
	 $rec = $db->query($sql);
	 	 
     return $rec;
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getCCCProfessor --> Total d'un professor entre dates
	                    Per informes de faltes d'assistència 
************************************************************************************************************ */
function getCCCProfessor($db,$idprofessor,$data_inici,$data_fi) {
	 $sql  = "SELECT ccc_tp.* ";
	 $sql .= "FROM ccc_taula_principal ccc_tp ";
	 $sql .= "WHERE ccc_tp.idprofessor = ".$idprofessor;
         $sql .= " AND ccc_tp.data BETWEEN '".$data_inici."' AND '".$data_fi."' ";
	 $sql .= "ORDER BY ccc_tp.data DESC ";
	 $rec = $db->query($sql);
	 	 
     return $rec;
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getCCCGrup --> Total d'un grup entre dates
	               Per informes de faltes d'assistència 
************************************************************************************************************ */
function getCCCGrup($db,$idgrup,$data_inici,$data_fi) {
	 $sql  = "SELECT ccc_tp.* ";
	 $sql .= "FROM ccc_taula_principal ccc_tp ";
	 $sql .= "WHERE ccc_tp.idgrup = ".$idgrup;
         $sql .= " AND ccc_tp.data BETWEEN '".$data_inici."' AND '".$data_fi."' ";
	 $sql .= "ORDER BY ccc_tp.data DESC ";
	 $rec = $db->query($sql);
	 	 
     return $rec;
  }
/* ********************************************************************************************************* */
  
  /*  ********************************************************************************************************
	getCCCGrupMateriasProfessor --> Total d'un grup entre dates
	               Per informes de faltes d'assistència 
************************************************************************************************************ */
function getCCCGrupMateriasProfessor($db,$idprofessor,$idgrup,$data_inici,$data_fi) {
	 $sql  = "SELECT ccc_tp.* ";
	 $sql .= "FROM ccc_taula_principal ccc_tp ";
	 $sql .= "WHERE ccc_tp.idgrup = ".$idgrup;
         $sql .= " AND ccc_tp.data BETWEEN '".$data_inici."' AND '".$data_fi."' ";
         $sql .= " AND ccc_tp.idmateria IN (SELECT mm.id_mat_uf_pla FROM prof_agrupament pa ";
         
         $sql .= "INNER JOIN grups_materies gm ON pa.idagrups_materies=gm.idgrups_materies ";
         $sql .= "INNER JOIN moduls_materies_ufs mm ON gm.id_mat_uf_pla = mm.id_mat_uf_pla ";
         $sql .= "WHERE pa.idprofessors='".$idprofessor."' ) ";
         
	 $sql .= "ORDER BY ccc_tp.data DESC ";
	 $rec = $db->query($sql);
	 	 
     return $rec;
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getCCCProfessorGrup --> Total d'un professor i grup entre dates
	                        Per informes de faltes d'assistència 
************************************************************************************************************ */
function getCCCProfessorGrup($db,$idprofessor,$idgrup,$data_inici,$data_fi) {
	 $sql  = "SELECT ccc_tp.* ";
	 $sql .= "FROM ccc_taula_principal ccc_tp ";
	 $sql .= "WHERE ccc_tp.idprofessor = ".$idprofessor." AND ccc_tp.idgrup = ".$idgrup;
         $sql .= " AND ccc_tp.data BETWEEN '".$data_inici."' AND '".$data_fi."' ";
	 $sql .= "ORDER BY ccc_tp.data DESC ";
	 $rec = $db->query($sql); 
	 	 
     return $rec;
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getCCCGrupMateria --> Total d'un grup i materia entre dates
	                              Per informes de faltes d'assistència 
************************************************************************************************************ */
function getCCCGrupMateria($db,$idgrup,$idmateria,$data_inici,$data_fi) {
	 $sql  = "SELECT ccc_tp.* ";
	 $sql .= "FROM ccc_taula_principal ccc_tp ";
	 $sql .= "WHERE ccc_tp.idgrup = ".$idgrup." AND ccc_tp.idmateria = ".$idmateria;
         $sql .= " AND ccc_tp.data BETWEEN '".$data_inici."' AND '".$data_fi."' ";
	 $sql .= "ORDER BY ccc_tp.data DESC ";
	 $rec = $db->query($sql);
	 	 
     return $rec;
  }
/* ********************************************************************************************************* */
  
/*  ********************************************************************************************************
	getCCCGrupAlumne --> Total d'un grup i alumne entre dates
	                              Per informes de faltes d'assistència 
************************************************************************************************************ */
function getCCCGrupAlumne($db,$idgrup,$idalumne,$data_inici,$data_fi) {
	 $sql  = "SELECT ccc_tp.* ";
	 $sql .= "FROM ccc_taula_principal ccc_tp ";
	 $sql .= "WHERE ccc_tp.idgrup = ".$idgrup." AND ccc_tp.idalumne = ".$idalumne;
         $sql .= " AND ccc_tp.data BETWEEN '".$data_inici."' AND '".$data_fi."' ";
	 $sql .= "ORDER BY ccc_tp.data DESC ";
	 $rec = $db->query($sql);
	 	 
     return $rec;
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getCCCGrupAlumneMateriasProfessor --> Total d'un grup i alumne entre dates
                                            Per informes de faltes d'assistència 
************************************************************************************************************ */
function getCCCGrupAlumneMateriasProfessor($db,$idprofessor,$idgrup,$idalumne,$data_inici,$data_fi) {
	 $sql  = "SELECT ccc_tp.* ";
	 $sql .= "FROM ccc_taula_principal ccc_tp ";
	 $sql .= "WHERE ccc_tp.idgrup = ".$idgrup." AND ccc_tp.idalumne = ".$idalumne;
         $sql .= " AND ccc_tp.data BETWEEN '".$data_inici."' AND '".$data_fi."' ";
         $sql .= " AND ccc_tp.idmateria IN (SELECT mm.id_mat_uf_pla FROM prof_agrupament pa ";
         
         $sql .= "INNER JOIN grups_materies gm ON pa.idagrups_materies=gm.idgrups_materies ";
         $sql .= "INNER JOIN moduls_materies_ufs mm ON gm.id_mat_uf_pla = mm.id_mat_uf_pla ";
         $sql .= "WHERE pa.idprofessors='".$idprofessor."' ) ";
         
	 $sql .= "ORDER BY ccc_tp.data DESC ";
	 $rec = $db->query($sql);
	 	 
     return $rec;
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getInformeTotalCCC_Criterio --> Per informes de faltes d'assistència 
************************************************************************************************************ */
function getInformeTotalCCC_Criterio($db,$data_inici,$data_fi,$criteri) {
	 $sql  = "SELECT ccc_tp.".$criteri.",COUNT(ccc_tp.id_falta) AS total ";
	 $sql .= "FROM ccc_taula_principal ccc_tp ";
	 //$sql .= "WHERE ccc_tp.idgrup = ".$idgrup;
         $sql .= " WHERE ccc_tp.data BETWEEN '".$data_inici."' AND '".$data_fi."'";
	 $sql .= " GROUP BY 1 ORDER BY 2 DESC "; 	 
	 $rec = $db->query($sql);

	 return $rec;
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getInformeTotalCCC_Criterio_Subcriterio --> Per informes de faltes d'assistència 
************************************************************************************************************ */
function getInformeTotalCCC_Criterio_Subcriterio($db,$data_inici,$data_fi,$criteri,$valor_criteri,$subcriteri) {
	 $sql  = "SELECT ccc_tp.".$subcriteri.",COUNT(ccc_tp.id_falta) AS total ";
	 $sql .= "FROM ccc_taula_principal ccc_tp ";
	 $sql .= "WHERE ccc_tp.".$criteri." = ".$valor_criteri;
         $sql .= " AND ccc_tp.data BETWEEN '".$data_inici."' AND '".$data_fi."'";
	 $sql .= " GROUP BY 1 ORDER BY 2 DESC "; 	 
	 
	 $rec = $db->query($sql);

	 return $rec;
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getTotalCCCAlumne --> Total CCC d'un alumne entre dates
	                      Per informes de faltes d'assistència 
************************************************************************************************************ */
function getTotalCCCAlumne($db,$idalumne,$data_inici,$data_fi) {
	 $sql  = "SELECT COUNT(ccc_tp.id_falta) AS total ";
	 $sql .= "FROM ccc_taula_principal ccc_tp ";
	 $sql .= "WHERE ccc_tp.idalumne = ".$idalumne;
         $sql .= " AND ccc_tp.data BETWEEN '".$data_inici."' AND '".$data_fi."'";
	 	 
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
       return $result["total"];
	 }
	
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getTotalCCCAlumneGrupMateria --> Total CCC d'un alumne, grup i materia entre dates
************************************************************************************************************ */
function getTotalCCCAlumneGrupMateria($db,$idalumne,$idgrup,$idmateria,$data_inici,$data_fi) {
	 $sql  = "SELECT COUNT(ccc_tp.id_falta) AS total ";
	 $sql .= "FROM ccc_taula_principal ccc_tp ";
	 $sql .= "WHERE ccc_tp.idalumne = ".$idalumne;
         $sql .= " AND ccc_tp.idgrup=".$idgrup." AND ccc_tp.idmateria=".$idmateria;
	 $sql .= " AND ccc_tp.data BETWEEN '".$data_inici."' AND '".$data_fi."'";
	 	 
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
       return $result["total"];
	 }
	
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getTotalCCCProfessor --> Total CCC d'un professor entre dates
	                         Per informes de faltes d'assistència 
************************************************************************************************************ */
function getTotalCCCProfessor($db,$idprofessor,$data_inici,$data_fi) {
	 $sql  = "SELECT COUNT(ccc_tp.id_falta) AS total ";
	 $sql .= "FROM ccc_taula_principal ccc_tp ";
	 $sql .= "WHERE ccc_tp.idprofessor = ".$idprofessor;
         $sql .= " AND ccc_tp.data BETWEEN '".$data_inici."' AND '".$data_fi."'";
	 	 
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
       return $result["total"];
	 }
	
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getTotalCCCProfessorGrup --> Total CCC d'un professor i grup entre dates
	                             Per informes de faltes d'assistència 
************************************************************************************************************ */
function getTotalCCCProfessorGrup($db,$idprofessor,$idgrup,$data_inici,$data_fi) {
     $sql  = "SELECT COUNT(ccc_tp.id_falta) AS total ";
     $sql .= "FROM ccc_taula_principal ccc_tp ";
     $sql .= "WHERE ccc_tp.idgrup = ".$idgrup;
     $sql .= " AND ccc_tp.data BETWEEN '".$data_inici."' AND '".$data_fi."' ";
     $sql .= " AND ccc_tp.idmateria IN (SELECT mm.id_mat_uf_pla FROM prof_agrupament pa ";
         
     $sql .= "INNER JOIN grups_materies gm ON pa.idagrups_materies=gm.idgrups_materies ";
     $sql .= "INNER JOIN moduls_materies_ufs mm ON gm.id_mat_uf_pla = mm.id_mat_uf_pla ";
     $sql .= "WHERE pa.idprofessors='".$idprofessor."' ) ";
	 	 
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
       return $result["total"];
	 }
	
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getTotalCCCGrupMateria --> Total CCC d'un grup i materia entre dates
************************************************************************************************************ */
function getTotalCCCGrupMateria($db,$idgrup,$idmateria,$data_inici,$data_fi) {
     $sql  = "SELECT COUNT(ccc_tp.id_falta) AS total ";
     $sql .= "FROM ccc_taula_principal ccc_tp ";
     $sql .= "WHERE ";
     $sql .= " ccc_tp.idgrup=".$idgrup." AND ccc_tp.idmateria=".$idmateria;
     $sql .= " AND ccc_tp.data BETWEEN '".$data_inici."' AND '".$data_fi."'";
	 	 
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
       return $result["total"];
     }
	
  }
/* ********************************************************************************************************* */  
  
/*  ********************************************************************************************************
	getTotalCCCProfessorAlumneGrupMateria --> Total CCC d'un professor, alumne, grup i materia entre dates
************************************************************************************************************ */
function getTotalCCCProfessorAlumneGrupMateria($db,$idprofessor,$idalumne,$idgrup,$idmateria,$data_inici,$data_fi) {
     $sql  = "SELECT COUNT(ccc_tp.id_falta) AS total ";
     $sql .= "FROM ccc_taula_principal ccc_tp ";
     $sql .= "WHERE ccc_tp.idprofessor = ".$idprofessor." AND ccc_tp.idalumne=".$idalumne;
     $sql .= " AND ccc_tp.idgrup=".$idgrup." AND ccc_tp.idmateria=".$idmateria;
     $sql .= " AND ccc_tp.data BETWEEN '".$data_inici."' AND '".$data_fi."'";
	 	 
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
       return $result["total"];
     }
	
  }
/* ********************************************************************************************************* */  
  
  
/*  ********************************************************************************************************
	getTotalCCCProfessorAlumne --> Total CCC d'un professor i alumne entre dates
	                               Per informes de faltes d'assistència 
************************************************************************************************************ */
function getTotalCCCProfessorAlumne($db,$idprofessor,$idalumne,$data_inici,$data_fi) {
	 $sql  = "SELECT COUNT(ccc_tp.id_falta) AS total ";
	 $sql .= "FROM ccc_taula_principal ccc_tp ";
	 $sql .= "WHERE ccc_tp.idprofessor = ".$idprofessor. " AND ccc_tp.idalumne = ".$idalumne;
	 $sql .= " AND ccc_tp.data BETWEEN '".$data_inici."' AND '".$data_fi."'";
	 	 
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
       return $result["total"];
	 }
	
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getTotalCCCProfessorAlumneGrup --> Total CCC d'un professor, alumne i grup entre dates
	                                   Per informes de faltes d'assistència 
************************************************************************************************************ */
function getTotalCCCProfessorAlumneGrup($db,$idprofessor,$idalumne,$idgrup,$data_inici,$data_fi) {
	 $sql  = "SELECT COUNT(ccc_tp.id_falta) AS total ";
	 $sql .= "FROM ccc_taula_principal ccc_tp ";
	 $sql .= "WHERE ccc_tp.idprofessor = ".$idprofessor. " AND ccc_tp.idalumne = ".$idalumne;
	 $sql .= " AND ccc_tp.idgrup =".$idgrup;
	 $sql .= " AND ccc_tp.data BETWEEN '".$data_inici."' AND '".$data_fi."'";
	 	 
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
       return $result["total"];
	 }
	
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getTotalCCCAlumneGrup --> Total CCC d'un alumne i grup entre dates
	                          Per informes de faltes d'assistència 
************************************************************************************************************ */
function getTotalCCCAlumneGrup($db,$idalumne,$idgrup,$data_inici,$data_fi) {
	 $sql  = "SELECT COUNT(ccc_tp.id_falta) AS total ";
	 $sql .= "FROM ccc_taula_principal ccc_tp ";
	 $sql .= "WHERE ccc_tp.idalumne = ".$idalumne. " AND ccc_tp.idgrup = ".$idgrup;
	 $sql .= " AND ccc_tp.data BETWEEN '".$data_inici."' AND '".$data_fi."'";
	 	 
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
       return $result["total"];
	 }
	
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getTotalCCCGrup --> Total CCC d'un grup entre dates
	                    Per informes de faltes d'assistència 
************************************************************************************************************ */
function getTotalCCCGrup($db,$idgrup,$data_inici,$data_fi) {
	 $sql  = "SELECT COUNT(ccc_tp.id_falta) AS total ";
	 $sql .= "FROM ccc_taula_principal ccc_tp ";
	 $sql .= "WHERE ccc_tp.idgrup = ".$idgrup;
         $sql .= " AND ccc_tp.data BETWEEN '".$data_inici."' AND '".$data_fi."'";
	 	 
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
       return $result["total"];
	 }
	
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getTotalCCCAlumnebyTipus --> Total CCC d'un alumne per tipus i entre dates
	                             Per informes de faltes d'assistència 
************************************************************************************************************ */
function getTotalCCCAlumnebyTipus($db,$idalumne,$id_falta,$data_inici,$data_fi) {
	 $sql  = "SELECT ccc_tp.idalumne, ccc_tp.id_falta, COUNT(ccc_tp.id_falta) AS total ";
	 $sql .= "FROM ccc_taula_principal ccc_tp ";
	 $sql .= "WHERE ccc_tp.idalumne = ".$idalumne." AND ccc_tp.id_falta=".$id_falta;
         $sql .= " AND ccc_tp.data BETWEEN '".$data_inici."' AND '".$data_fi."'";
	 	 
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
       return $result["total"];
	 }
	
  }
/* ********************************************************************************************************* */

/*  ********************************************************************************************************
	getTotalCCCProfessorbyTipus --> Total CCC d'un professor per tipus i entre dates
	                                Per informes de faltes d'assistència 
************************************************************************************************************ */
function getTotalCCCProfessorbyTipus($db,$idprofessor,$id_falta,$data_inici,$data_fi) {
	 $sql  = "SELECT ccc_tp.idalumne, ccc_tp.id_falta, COUNT(ccc_tp.id_falta) AS total ";
	 $sql .= "FROM ccc_taula_principal ccc_tp ";
	 $sql .= "WHERE ccc_tp.idprofessor = ".$idprofessor." AND ccc_tp.id_falta=".$id_falta;
         $sql .= " AND ccc_tp.data BETWEEN '".$data_inici."' AND '".$data_fi."'";
	 	 
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
           return $result["total"];
   }
	
 }
/* ********************************************************************************************************* */
?>