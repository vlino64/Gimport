<?php
  session_start();  
  header("Content-type: application/vnd.ms-word");
  header("Content-Disposition: attachment;Filename=Informe.doc");
  header("Pragma: no-cache");
  header("Expires: 0");
  
  require_once('../bbdd/connect.php');
  require_once('../func/constants.php');
  require_once('../func/generic.php');
  require_once('../func/seguretat.php');
  
  if (strrpos($_SERVER['HTTP_USER_AGENT'], 'Linux') === false){
  }
  else {
      $db->exec("set names utf8");
  }
  
  $data_inici = isset($_REQUEST['data_inici']) ? substr($_REQUEST['data_inici'],6,4)."-".substr($_REQUEST['data_inici'],3,2)."-".substr($_REQUEST['data_inici'],0,2) : getCursActual($db)["data_inici"];
  if ($data_inici=='--') {
  	  $data_inici = getCursActual($db)["data_inici"];
  }
  $txt_inici  = isset($_REQUEST['data_inici']) ? $_REQUEST['data_inici'] : '';
  
  $data_fi    = isset($_REQUEST['data_fi'])    ? substr($_REQUEST['data_fi'],6,4)."-".substr($_REQUEST['data_fi'],3,2)."-".substr($_REQUEST['data_fi'],0,2)          : getCursActual($db)["data_fi"];
  if ($data_fi=='--') {
  	  $data_fi = getCursActual($db)["data_fi"];
  }
  $txt_fi     = isset($_REQUEST['data_fi'])    ? $_REQUEST['data_fi'] : '';
  
  if ( isset($_REQUEST['c_alumne']) && ($_REQUEST['c_alumne']==0) ) {
  	$c_alumne = 0;
  }
  else if ( isset($_REQUEST['c_alumne']) ) {
    $c_alumne = $_REQUEST['c_alumne'];
  }
  if (! isset($c_alumne)) {
    $c_alumne = 0;
  }
  
  $curs_escolar    = getCursActual($db)["idperiodes_escolars"]; 
  $grup_materia    = 0;
  $escicleloe      = 0;
  $total_classes   = 0;
  $total_seguiment = 0;
  if ( isset($_REQUEST['grup_materia'])) {
	 $grup_materia = $_REQUEST['grup_materia'];
	 $idgrups      = getGrupMateria($db,$grup_materia)["id_grups"];
	 $idmateria    = getGrupMateria($db,$grup_materia)["id_mat_uf_pla"];
  }
  else {
	 $idgrups      = isset($_REQUEST['idgrups']) ? $_REQUEST['idgrups'] : 0;
	 $idmateria    = isset($_REQUEST['idmateria']) ? $_REQUEST['idmateria'] : 0;
	 $grup_materia = existGrupMateria($db,$idgrups,$idmateria);
  }
  
  if ($idmateria != 0) {
	  $escicleloe      = isMateria($db,$idmateria) ? 0 : 1 ;
	  if ($escicleloe) {
		$data_inici = getGrupMateria($db,$grup_materia)["data_inici"];
		$txt_inici  = substr($data_inici,8,2)."-".substr($data_inici,5,2)."-".substr($data_inici,0,4);
		$data_fi    = getGrupMateria($db,$grup_materia)["data_fi"];
		$txt_fi     = substr($data_fi,8,2)."-".substr($data_fi,5,2)."-".substr($data_fi,0,4);
	  }
	  $total_classes   = classes_entre_dates($db,$data_inici,$data_fi,$grup_materia,$curs_escolar);
  	  $total_seguiment = getTotalSeguimientoGrupMateria($db,$data_inici,$data_fi,$idgrups,$idmateria,$curs_escolar);
  }
  
  if ( isset($_REQUEST['percent']) && ($_REQUEST['percent']==0) ) {
  	$percentatge = 80;
  }
  else if ( isset($_REQUEST['percent']) ) {
    $percentatge = $_REQUEST['percent'];
  }
  if (! isset($percentatge)) {
    $percentatge = 80;
  }
  
  $rsAlumnes     = getAlumnesGrup($db,$idgrups,TIPUS_nom_complet);
  $mode_impresio = isset($_REQUEST['mode_impresio'])      ? $_REQUEST['mode_impresio'] : 0;
  // Per evitar division by zero
  if ($total_seguiment==0) {
	  $total_seguiment = 0.1;
  }
?>

<div>
 
 <?php
    if ($c_alumne == 0) {
 ?>	
 
 <h5>
   Informe de faltes del grup <a><?= getGrup($db,$idgrups)['nom'] ?></a><br />
 </h5>
  <?php
  	if ($idmateria != 0) {
  ?>
     <h5>
     Mat&egrave;ria 
     <a><?=getMateria($db,$idmateria)["nom_materia"]?></a>
     </h5>
     
     <h5>
      Dies lectius calculats  
     <a><?=$total_classes?></a>
       
     Dies lectius reals (seguiment del professor)  
     <a><?=$total_seguiment?></a>
     </h5>
  <?php
	}
  ?>
 <h5>
   Desde el <a><?= $txt_inici ?></a>
   fins al <a><?= $txt_fi ?></a>
 </h5> 
 
 	<?php
 	if ($escicleloe) {
	echo "<div class='demo-info'>";
        echo "<div class='demo-tip icon-tip'></div>";
        echo "<div>Aquesta matèria es de CCFF. Sortirà exclusivament el periode comprés entre la data d'inici i fi de la UF</div>";
        echo "</div>";
	}
   ?>

	<div class='left'>
		 
	</div>
	<div class='right'>
		<table>
         	<tr>
                <td> </td>
            	<td><strong>ALUMNE</strong></td>
                <td><strong>FALTES</strong></td>
                <?php
				  if ($idmateria != 0) {
			    ?>
                <td><strong>% programat</strong></td>
                <td><strong>% real</strong></td>
                <?php
				  }
				?>
                <td><strong>RETARDS</strong></td>
                <td><strong>JUSTIFICADES</strong></td>
                <td><strong>SEGUIMENTS</strong></td>
                <td><strong>CCC</strong></td>
            </tr>
            
                <?php
				   $linea = 1;
                                   foreach($rsAlumnes->fetchAll() as $row) {
						  echo "<tr>";
						  echo "<td valign='top' width='30'>".$linea."</td>";
						  echo "<td valign='top' class='drop'>".$row["Valor"]."</td>";
						  if ($idmateria == 0) {
							  echo "<td valign='top' width='50' class='drop'>".getTotalIncidenciasAlumne($db,$row["idalumnes"],TIPUS_FALTA_ALUMNE_ABSENCIA,$data_inici,$data_fi)."</td>";
							  echo "<td valign='top' width='70' class='drop'>".getTotalIncidenciasAlumne($db,$row["idalumnes"],TIPUS_FALTA_ALUMNE_RETARD,$data_inici,$data_fi)."</td>";
							  echo "<td valign='top' width='90' class='drop'>".getTotalIncidenciasAlumne($db,$row["idalumnes"],TIPUS_FALTA_ALUMNE_JUSTIFICADA,$data_inici,$data_fi)."</td>";
							  echo "<td valign='top' width='90' class='drop'>".getTotalIncidenciasAlumne($db,$row["idalumnes"],TIPUS_FALTA_ALUMNE_SEGUIMENT,$data_inici,$data_fi)."</td>";
							  echo "<td valign='top' width='40' class='drop'>".getTotalCCCAlumne($db,$row["idalumnes"],$data_inici,$data_fi)."</td></tr>";
						  }
						  else {
							  $absencies = getTotalIncidenciasAlumneGrupMateria($db,$row["idalumnes"],TIPUS_FALTA_ALUMNE_ABSENCIA,$idgrups,$idmateria,$data_inici,$data_fi);
						  	  $abs_programat = round(($absencies/$total_classes)*100,2);
						  	  $abs_real      = round(($absencies/$total_seguiment)*100,2);
						  
							  echo "<td valign='top' width='50' class='drop'>".$absencies."</td>";
							  
							  if ($abs_programat>=$percentatge) {
							  	echo "<td valign='top' width='90' bgcolor='#FF0000'><font color='#FFFFFF'>";
							  }
							  else if ($abs_programat>=($percentatge-5)) {
								  echo "<td valign='top' width='90' bgcolor='#FFFF33'><font>";
							  }
							  else {
								  echo "<td valign='top' width='90' class='drop'><font>";
							  }
							  echo $abs_programat."</font></td>";
							  
							  if ($abs_real>=$percentatge) {
								  echo "<td valign='top' width='50' bgcolor='#FF0000'><font color='#FFFFFF'>";
							  }
							  else if ($abs_real>=($percentatge-5)) {
								  echo "<td valign='top' width='50' bgcolor='#FFFF33'><font>";
							  }
							  else {
								  echo "<td valign='top' width='50' class='drop'><font>";
							  }
							  echo $abs_real."</font></td>";
							  
							  echo "<td valign='top' width='70' class='drop'>".getTotalIncidenciasAlumneGrupMateria($db,$row["idalumnes"],TIPUS_FALTA_ALUMNE_RETARD,$idgrups,$idmateria,$data_inici,$data_fi)."</td>";
							  echo "<td valign='top' width='90' class='drop'>".getTotalIncidenciasAlumneGrupMateria($db,$row["idalumnes"],TIPUS_FALTA_ALUMNE_JUSTIFICADA,$idgrups,$idmateria,$data_inici,$data_fi)."</td>";
							  echo "<td valign='top' width='90' class='drop'>".getTotalIncidenciasAlumneGrupMateria($db,$row["idalumnes"],TIPUS_FALTA_ALUMNE_SEGUIMENT,$idgrups,$idmateria,$data_inici,$data_fi)."</td>";
							  echo "<td valign='top' width='40' class='drop'>".getTotalCCCAlumneGrupMateria($db,$row["idalumnes"],$idgrups,$idmateria,$data_inici,$data_fi)."</td></tr>"; 
						  }
						  $linea++;
				   }
				?>          
		</table>
	</div>

<?php
    }
	else {
?>
  <h5 style='margin-bottom:0px;'>
   Informe de faltes de l'alumne <a style=' color: #000066; padding:3px 3px 3px 3px '>
  <?= getAlumne($db,$c_alumne,TIPUS_nom_complet) ?></a>
   Desde el <a style=' color: #000066; padding:3px 3px 3px 3px '><?= $txt_inici ?></a>
    fins al <a style=' color: #000066; padding:3px 3px 3px 3px '><?= $txt_fi ?></a>
 </h5>
 <div class='left'>
		 
 </div>
 <div class='right'>
 <table cellspacing="1">
    <tr>
        <td><strong>FALTES</strong></td>
        <td><strong>RETARDS</strong></td>
        <td><strong>JUSTIFICADES</strong></td>
        <td><strong>SEGUIMENTS</strong></td>
        <td><strong>CCC</strong></td>
    </tr>
    <?php
	  if ($idmateria == 0) {
	?>
    <tr>
        <td class='drop'><?=getTotalIncidenciasAlumne($db,$c_alumne,TIPUS_FALTA_ALUMNE_ABSENCIA,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalIncidenciasAlumne($db,$c_alumne,TIPUS_FALTA_ALUMNE_RETARD,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalIncidenciasAlumne($db,$c_alumne,TIPUS_FALTA_ALUMNE_JUSTIFICADA,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalIncidenciasAlumne($db,$c_alumne,TIPUS_FALTA_ALUMNE_SEGUIMENT,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalCCCAlumne($db,$c_alumne,$data_inici,$data_fi)?></td>
    </tr>
    <?php
	  }
	  else
	    {
	?>
    <tr>
        <td class='drop'><?=getTotalIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_ABSENCIA,$idgrups,$idmateria,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_RETARD,$idgrups,$idmateria,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_JUSTIFICADA,$idgrups,$idmateria,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_SEGUIMENT,$idgrups,$idmateria,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalCCCAlumneGrupMateria($db,$c_alumne,$idgrups,$idmateria,$data_inici,$data_fi)?></td>
    </tr>
    <?php
		}
	?>
 </table>
        <h5>Relaci&oacute; de faltes</h5>
 		<table cellspacing="1">
            <tr>
                <td> </td>
                <td><strong>DATA</strong></td>
                <td><strong>F. HOR&Agrave;RIA</strong></td>
                <td><strong>PROFESSOR/A</strong></td>
                <td><strong>MAT&Egrave;RIA</strong></td>
            </tr>
            
                <?php
				   $linea         = 1;
				   if ($idmateria == 0) {
				    $rsIncidencias = getIncidenciasAlumne($db,$c_alumne,TIPUS_FALTA_ALUMNE_ABSENCIA,$data_inici,$data_fi);
				   }
				   else {
					$rsIncidencias = getIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_ABSENCIA,$idgrups,$idmateria,$data_inici,$data_fi);   
				   }
                                   foreach($rsIncidencias->fetchAll() as $row) {
						  echo "<tr>";
						  echo "<td valign='top' width='15'>".$linea."</td>";
						  echo "<td valign='top' width='100' class='drop'>".substr($row["data"],8,2)."-".substr($row["data"],5,2)."-".substr($row["data"],0,4)."</td>";
						  echo "<td valign='top' width='80' class='drop'>".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_inici"],0,5)."-".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_fi"],0,5)."</td>";
						  echo "<td valign='top' class='drop'>".getProfessor($db,$row["idprofessors"],TIPUS_nom_complet)."</td>";
						  echo "<td valign='top' class='drop'>".getMateria($db,$row["id_mat_uf_pla"])["nom_materia"]."</td></tr>";
						  $linea++;
				   }
				?>          
		</table>
        
        <br />
        <h5>Relaci&oacute; de retards</h5>
 		<table cellspacing="1">
            <tr>
                <td> </td>
                <td><strong>DATA</strong></td>
                <td><strong>F. HOR&Agrave;RIA</strong></td>
                <td><strong>PROFESSOR/A</strong></td>
                <td><strong>MAT&Egrave;RIA</strong></td>
            </tr>
            
                <?php
				   $linea         = 1;
				   if ($idmateria == 0) {
				    $rsIncidencias = getIncidenciasAlumne($db,$c_alumne,TIPUS_FALTA_ALUMNE_RETARD,$data_inici,$data_fi);
				   }
				   else {
					$rsIncidencias = getIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_RETARD,$idgrups,$idmateria,$data_inici,$data_fi);
				   }
				   foreach($rsIncidencias->fetchAll() as $row) {
						  echo "<tr>";
						  echo "<td valign='top' width='15'>".$linea."</td>";
						  echo "<td valign='top' width='100' class='drop'>".substr($row["data"],8,2)."-".substr($row["data"],5,2)."-".substr($row["data"],0,4)."</td>";
						  echo "<td valign='top' width='80' class='drop'>".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_inici"],0,5)."-".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_fi"],0,5)."</td>";
						  echo "<td valign='top' class='drop'>".getProfessor($db,$row["idprofessors"],TIPUS_nom_complet)."</td>";
						  echo "<td valign='top' class='drop'>".getMateria($db,$row["id_mat_uf_pla"])["nom_materia"]."</td></tr>";
						  $linea++;
				   }
				?>          
		</table>
        
        <br />
        <h5>Relaci&oacute; de justificacions</h5>
 		<table cellspacing="1">
            <tr>
                <td> </td>
                <td><strong>DATA</strong></td>
                <td><strong>F. HOR&Agrave;RIA</strong></td>
                <td><strong>PROFESSOR/A</strong></td>
                <td><strong>MAT&Egrave;RIA</strong></td>
                <td><strong>OBSERVACIONS</strong></td>
            </tr>
            
                <?php
				   $linea         = 1;
				   if ($idmateria == 0) {
				    $rsIncidencias = getIncidenciasAlumne($db,$c_alumne,TIPUS_FALTA_ALUMNE_JUSTIFICADA,$data_inici,$data_fi);
				   }
				   else {
					$rsIncidencias = getIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_JUSTIFICADA,$idgrups,$idmateria,$data_inici,$data_fi);
				   }
				   foreach($rsIncidencias->fetchAll() as $row) {
						  echo "<tr>";
						  echo "<td valign='top' width='15'>".$linea."</td>";
						  echo "<td valign='top' width='100' class='drop'>".substr($row["data"],8,2)."-".substr($row["data"],5,2)."-".substr($row["data"],0,4)."</td>";
						  echo "<td valign='top' width='80' class='drop'>".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_inici"],0,5)."-".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_fi"],0,5)."</td>";
						  echo "<td valign='top' class='drop'>".getProfessor($db,$row["idprofessors"],TIPUS_nom_complet)."</td>";
						  echo "<td valign='top' class='drop'>".getMateria($db,$row["id_mat_uf_pla"])["nom_materia"]."</td>";
						  echo "<td valign='top' class='drop'>".nl2br($row["comentari"])."</td></tr>";
						  $linea++;
				   }
				?>          
		</table>

        <br />
        <h5>Relaci&oacute; de seguiments</h5>
 		<table cellspacing="1">
            <tr>
                <td> </td>
                <td><strong>TIPUS</strong></td>
                <td><strong>DATA</strong></td>
                <td><strong>F. HOR&Agrave;RIA</strong></td>
                <td><strong>PROFESSOR/A</strong></td>
                <td><strong>MAT&Egrave;RIA</strong></td>
                <td><strong>OBSERVACIONS</strong></td>
            </tr>
            
                <?php
				   $linea         = 1;
				   if ($idmateria == 0) {
				    $rsIncidencias = getIncidenciasAlumne($db,$c_alumne,TIPUS_FALTA_ALUMNE_SEGUIMENT,$data_inici,$data_fi);
				   }
				   else {
					$rsIncidencias = getIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_SEGUIMENT,$idgrups,$idmateria,$data_inici,$data_fi);
				   }
				   foreach($rsIncidencias->fetchAll() as $row) {
						  echo "<tr>";
						  echo "<td valign='top' width='15'>".$linea."</td>";
						  echo "<td valign='top' width='40' class='drop'>".getLiteralTipusIncident($db,$row["id_tipus_incident"])["tipus_incident"]."</td>";
						  echo "<td valign='top' width='70' class='drop'>".substr($row["data"],8,2)."-".substr($row["data"],5,2)."-".substr($row["data"],0,4)."</td>";
						  echo "<td valign='top' width='80' class='drop'>".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_inici"],0,5)."-".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_fi"],0,5)."</td>";
						  echo "<td valign='top' class='drop'>".getProfessor($db,$row["idprofessors"],TIPUS_nom_complet)."</td>";
						  echo "<td valign='top' class='drop'>".getMateria($db,$row["id_mat_uf_pla"])["nom_materia"]."</td>";
						  echo "<td valign='top' class='drop'>".nl2br($row["comentari"])."</td></tr>";
						  $linea++;
				   }
				?>          
		</table>
        
        <br />
        <h5>Relaci&oacute; de CCC</h5>
 		<table cellspacing="1">
            <tr>
                <td> </td>
                <td><strong>TIPUS CCC</strong></td>
                <td><strong>DATA</strong></td>
                <td><strong>EXPULSI&Oacute;</strong></td>
                <td><strong>PROFESSOR/A</strong></td>
                <td><strong>MAT&Egrave;RIA</strong></td>
                <td><strong>DESCRIPCI&Oacute;</strong></td>
            </tr>
            
                <?php
				   $linea         = 1;
				   if ($idmateria == 0) {
				    $rsIncidencias = getCCCAlumne($db,$c_alumne,$data_inici,$data_fi);
				   }
				   else {
					$rsIncidencias = getCCCAlumneGrupMateria($db,$c_alumne,$idgrups,$idmateria,$data_inici,$data_fi);
				   }
				   foreach($rsIncidencias->fetchAll() as $row) {
						  echo "<tr>";
						  echo "<td valign='top' width='15'>".$linea."</td>";
						  echo "<td valign='top' width='40' class='drop'>".getLiteralTipusCCC($db,$row["id_falta"])["nom_falta"]."</td>";
						  echo "<td valign='top' width='70' class='drop'>".substr($row["data"],8,2)."-".substr($row["data"],5,2)."-".substr($row["data"],0,4)."</td>";
						  echo "<td valign='top' width='40' class='drop'>".$row["expulsio"]."</td>";
						  echo "<td valign='top' class='drop'>".getProfessor($db,$row["idprofessor"],TIPUS_nom_complet)."</td>";
						  echo "<td valign='top' class='drop'>".(intval($row["idmateria"]!=0) ? getMateria($db,$row["idmateria"])["nom_materia"] : '')."</td>";
						  echo "<td valign='top' class='drop'><strong>Desc. breu</strong><br>".getLiteralMotiusCCC($db,$row["id_motius"])["nom_motiu"];
						  echo "<br><strong>Desc. detallada</strong><br>".nl2br($row["descripcio_detallada"])."</td></tr>";
						  $linea++;
				   }
				?>          
		</table>
 </div>
 
<?php
    	if (isset($rsAlumnes)) {
			//mysql_free_result($rsIncidencias);
		}
		if (isset($rsIncidencias)) {
			//mysql_free_result($rsIncidencias);
		}
    }
?>

</div>

<script type="text/javascript">
	$('#header').css('visibility', 'hidden');
	$('#footer').css('visibility', 'hidden');
</script>