<?php
  session_start();
  include_once('../bbdd/connect.php');
  include_once('../func/constants.php');
  include_once('../func/generic.php');
  $db->exec("set names utf8");
  
  
  // Ordre alfabètic d'alumnes
  
  $sql = "select * from alumnes";
  $rsAlumnes =  $db->query($sql);
  
  foreach($rsAlumnes->fetchAll() as $row) {    
	$idalumne = $row["idalumnes"];
	//echo $idalumne."<br>";
	
	$rsCognom1 = $db->query("select * from contacte_alumne where id_tipus_contacte=4 and id_alumne=".$idalumne);
	foreach($rsCognom1->fetchAll() as $row1) {
	    $cognom_1 = $row1["Valor"];
	}
	
	$rsCognom2 = $db->query("select * from contacte_alumne where id_tipus_contacte=5 and id_alumne=".$idalumne);
	foreach($rsCognom2->fetchAll() as $row2) {
	    $cognom_2 = $row2["Valor"];
	}
	
	$rsNom      = $db->query("select * from contacte_alumne where id_tipus_contacte=6 and id_alumne=".$idalumne);
        foreach($rsNom->fetchAll() as $row3) {
	    $nom = $row3["Valor"];
	}				  
	
	$nom_complet = $cognom_1." ".$cognom_2.", ".$nom;
	$sql = "update contacte_alumne set Valor='$nom_complet' where id_alumne=$idalumne and id_tipus_contacte=1";
	$rec = $db->query($sql);
	 
  }
  
?>

<script type="text/javascript">     
 $.messager.alert('Informaci&oacute;','Alumnes ordenats alfab&egrave;ticament.');
</script>

<?php
  // Reorganització taula incidencia_alumne. Ubicar cada incidencia en una franja horària el més aproximat
  // a la realitat posible.
  
  $dias = array(1,2,3,4,5,6,7);
  
  $rsIncidencies = $db->query("select * from incidencia_alumne where idfranges_horaries=0 order by idincidencia_alumne desc");
  
  foreach($rsIncidencies->fetchAll() as $row) {
	$idincidencia_alumne = $row["idincidencia_alumne"];
	$idalumne_agrupament = $row["idalumne_agrupament"];
	$idgrups_materies    = getGrupMateriaAlumneAgrupament($db,$idalumne_agrupament);
	
	//calcul del dia d'una data determinada
	$data	     = $row["data"];
	$any         = substr($data,0,4);
	$mes         = substr($data,5,2);
	$dia         = substr($data,8,2);
	$iddies_setmana = diaSemana($any,$mes,$dia);
	
        // busquem a l'horari (unitats_clase) quin dia i grupmateria tè una determinada entrada
	$sql  = "SELECT fh.idfranges_horaries ";
	$sql .= "FROM unitats_classe uc ";
	$sql .= "INNER JOIN dies_franges df ON uc.id_dies_franges = df.id_dies_franges ";
	$sql .= "INNER JOIN franges_horaries fh ON df.idfranges_horaries = fh.idfranges_horaries ";
	$sql .= "WHERE uc.idgrups_materies=$idgrups_materies AND df.iddies_setmana=$iddies_setmana ";
	$sql .= "ORDER BY 1 DESC ";

        $rsFranja =  $db->query($sql);
    
        foreach($rsFranja->fetchAll() as $row_2) {
            $sql = "update incidencia_alumne set idfranges_horaries=".$row_2['idfranges_horaries']." where idincidencia_alumne=".$idincidencia_alumnes;
            $rec = $db->query($sql);
	}
	
  }
?>

<script type="text/javascript">     
 $.messager.alert('Informaci&oacute;','Incid&egrave;ncies ordenades correctament.');
</script>

<?php
  // Reorganització taula incidencia_alumne. Desfer la granularitat del camp idalumne_agrupament
  // amb els camps idalumnes, idgrups i id_mat_uf_pla. Millora en la gestió dels històrics.
  
  $sql  = "select ia.* from incidencia_alumne ia ";
  $sql .= "inner join alumnes_grup_materia agm on ia.idalumne_agrupament=agm.idalumnes_grup_materia ";
  $sql .= "where ia.idalumnes=0 ";
  
  $rsIncidencies =  $db->query($sql);
  foreach($rsIncidencies->fetchAll() as $row) {
	$idincidencia_alumne = $row["idincidencia_alumne"];
	$idalumne_agrupament = $row["idalumne_agrupament"];
	$idalumnes           = getAlumneGrupMateria($db,$idalumne_agrupament)["idalumnes"];
	$idgrups_materies    = getAlumneGrupMateria($db,$idalumne_agrupament)["idgrups_materies"];
	$idgrups             = getGrupMateria($db,$idgrups_materies)["id_grups"];
	$idmateria           = getGrupMateria($db,$idgrups_materies)["id_mat_uf_pla"];
	
	//echo $idalumnes." ".$idgrups_materies." ".$idgrups." ".$idmateria."<br>";
	
	$sql    = "update incidencia_alumne set idalumnes=$idalumnes,idgrups=$idgrups,id_mat_uf_pla=$idmateria where idincidencia_alumne=$idincidencia_alumne";
	$rec = $db->query($sql);
	
  }
  $sql    = "delete from incidencia_alumne where idalumnes=0";
  $rec = $db->query($sql);
?>

<script type="text/javascript">     
 $.messager.alert('Informaci&oacute;','Incid&egrave;ncies organitzades correctament.');
</script>

<?php
  // Reorganització taula incidencia_alumne. Desfer la granularitat del camp idalumne_agrupament
  // amb els camps idalumnes, idgrups i id_mat_uf_pla. Millora en la gestió dels històrics.
  
  $sql  = "select ia.* from incidencia_alumne ia ";
  $sql .= "where ia.idprofessors=0 ";
  
  $rsIncidencies = $db->query($sql);
  foreach($rsIncidencies->fetchAll() as $row) {
	$idincidencia_alumne = $row["idincidencia_alumne"];
	$idgrups_materies    = existGrupMateria($db,$row["idgrups"],$row["id_mat_uf_pla"]);
	$idprofessors        = 0;
	
	if ($idgrups_materies != 0) {
            if(getProfessorByGrupMateria($db,$idgrups_materies)["idprofessors"] != 0) {
		$idprofessors = getProfessorByGrupMateria($db,$idgrups_materies)["idprofessors"];
            }
	}
	
	$sql    = "update incidencia_alumne set idprofessors=$idprofessors where idincidencia_alumne=$idincidencia_alumne";
	$rec = $db->query($sql);
	
  }
  $sql    = "delete from incidencia_alumne where idalumnes=0";
  $rec = $db->query($sql);
?>

<script type="text/javascript">     
 $.messager.alert('Informaci&oacute;','Reassignaci&oacute; de professors feta correctament.');
</script>
  
<?php
//mysql_free_result($rsAlumnes);
//mysql_free_result($rsIncidencies);
//mysql_free_result($rsFranja);
//mysql_close();
?>