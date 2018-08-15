<?php
  session_start();
  header("Content-type: application/vnd.ms-excel");
  header("Content-Disposition: attachment;Filename=Informe.xls");

  require_once('../bbdd/connect.php');
  require_once('../func/constants.php');
  require_once('../func/generic.php');
  require_once('../func/seguretat.php');
  //$db->exec("set names utf8");
  
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
  
  $escicleloe      = isMateria($db,$idmateria) ? 0 : 1 ;
  if ($escicleloe) {
	$data_inici = getGrupMateria($db,$grup_materia)["data_inici"];
	$txt_inici  = substr($data_inici,8,2)."-".substr($data_inici,5,2)."-".substr($data_inici,0,4);
   	$data_fi    = getGrupMateria($db,$grup_materia)["data_fi"];
	$txt_fi     = substr($data_fi,8,2)."-".substr($data_fi,5,2)."-".substr($data_fi,0,4);
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
  
  $curs_escolar    = getCursActual($db)["idperiodes_escolars"];  
  $rsAlumnes       = getAlumnesGrup($db,$idgrups,TIPUS_nom_complet);
  $total_classes   = classes_entre_dates($db,$data_inici,$data_fi,$grup_materia,$curs_escolar);
  $total_seguiment = getTotalSeguimientoGrupMateria($db,$data_inici,$data_fi,$idgrups,$idmateria,$curs_escolar);
  // Per evitar division by zero
  if ($total_seguiment==0) {
	 // $total_seguiment=1;
  }
  
  $mode_impresio      = isset($_REQUEST['mode_impresio'])      ? $_REQUEST['mode_impresio']      : 0;
?>

<style type="text/css">

@page {
	margin: 1cm;
}

body {
  font-family: sans-serif;
  margin: 1.5cm 0;
}

#header,
#footer {
  position: fixed;
  left: 0;
  right: 0;
  color: #aaa;
  font-size: 0.9em;
}

#header {
  top: 0;
  border-bottom: 0.1pt solid #aaa;
  margin-bottom:15px;
}

#footer {
  bottom: 0;
  border-top: 0.1pt solid #aaa;
}

#header table,
#footer table {
  width: 100%;
  border-collapse: collapse;
  border: none;
}

#header td,
#footer td {
  padding: 0;
  width: 50%;
}

.page-number {
  text-align: right;
}

.page-number:before {
  content: " " counter(page);
}

hr {
  page-break-after: always;
  border: 0;
}

</style>

<style type='text/css'>
		.left{
			width:2px;
			float:left;
		}
		.left table{
			background:#E0ECFF;
		}
		.left td{
			background:#eee;
		}
		.right{
			float:right;
			width:1000px;
		}
		.right table{
			background:#E0ECFF;
			width:95%;
		}
		.right td{
			
			text-align:left;
			padding:2px;
		}
		.right td{
			
		}
		.right td.drop{
			background:#fafafa;
		}
		.right td.over{
			
		}
		.item{
			background:#fafafa;
		}
		.assigned{
			border:1px solid #BC2A4D;

		}
		.alumne {
			background:#FFFFFF;
			text-align:left;
			width:400px;
		}	
</style>

<?php
  	if ($mode_impresio) {
?>

<div id="header">
  <table>
    <tr>
      <td>
      <b><?= getDadesCentre($db)["nom"] ?></b><br />
      <?= getDadesCentre($db)["adreca"] ?>&nbsp;&nbsp;
      <?= getDadesCentre($db)["cp"] ?>&nbsp;<?= getDadesCentre($db)["poblacio"] ?>
      </td>
      <td style="text-align: right;">
      		<?php
		$img_logo = '../images/logo.jpg';
                if (file_exists($img_logo)) {
                	echo "<img src='".$img_logo."'>";
		}
		?>
      </td>
    </tr>
  </table>
</div>

<div id="footer">
  <table>
    <tr>
      <td>
        <?= getDadesCentre($db)["tlf"] ?>&nbsp;&nbsp;<?= getDadesCentre($db)["email"] ?>
      </td>
      <td align="right">
  		<div class="page-number"></div>
      </td>
    </tr>
  </table>
</div>

<?php
  	}
?>

<div style='width:1060px;'>
 
 <?php
	if ($c_alumne == 0) {
 ?>	
 
 <h5 style='margin-bottom:0px'>
  &nbsp;Informe de faltes del grup 
  <a style=' color: #000066; font-size:16px; border:1px dashed #CCCCCC; padding:3px 3px 3px 3px '>
  <?= getGrup($db,$idgrups)["nom"] ?></a>&nbsp;<br />
 </h5>
 <h5>
  &nbsp;Mat&egrave;ria&nbsp;
  <a style=' color: #000066; font-size:16px; border:1px dashed #CCCCCC; padding:3px 3px 3px 3px '>
  <?=getMateria($db,$idmateria)["nom_materia"]?></a>
 </h5>
 <h5>
  &nbsp;Desde el <a style='color:#000066; font-size:16px; border:1px dashed #CCCCCC; padding:3px 3px 3px 3px '><?= $txt_inici ?></a>
  &nbsp;&nbsp;fins al <a style='color:#000066; font-size:16px; border:1px dashed #CCCCCC; padding:3px 3px 3px 3px '><?= $txt_fi ?></a>
 </h5>  
 <h5>
 &nbsp;Dies lectius calculats&nbsp;&nbsp;
 <a style="color:#000066; font-size:16px; font-weight:bold; border:1px dashed #CCCCCC;">
 <?=$total_classes?></a>
 &nbsp;&nbsp;
 &nbsp;Dies lectius reals (seguiment del professor)&nbsp;&nbsp;
 <a style="color:#000066; font-size:16px; font-weight:bold; border:1px dashed #CCCCCC;">
 <?=$total_seguiment?></a>
 </h5>
 
 <?php
 	if ($escicleloe) {
            echo "<div class='demo-info'>";
            echo "<div class='demo-tip icon-tip'></div>";
            echo "<div>Aquesta matèria es de CCFF. Sortirà exclusivament el periode comprés entre la data d'inici i fi de la UF</div>";
            echo "</div>";
	}
	
    if ($total_seguiment==0) {
	echo "<div class='error-info'>";
        echo "<div class='error-tip icon-no'></div>";
        echo "<div>No hi han enregistrades dades de seguiment de la matèria.No podem mostrar l'informe d'assistència</div>";
        echo "</div>";
	}
	else {
 ?>
	<div class='left'>
		&nbsp;
	</div>
	<div class='right'>
		<table>
         	<tr>
                <td>&nbsp;</td>
            	<td><strong>ALUMNE</strong></td>
                <td><strong>FALTES</strong></td>
                <td><strong>% programat</strong></td>
                <td><strong>% real</strong></td>
                <td><strong>RETARDS</strong></td>
                <td><strong>JUSTIFICADES</strong></td>
                <td><strong>SEGUIMENTS</strong></td>
                <td><strong>CCC</strong></td>
            </tr>
            
                <?php
				   $linea = 1;
                                   foreach($rsAlumnes->fetchAll() as $row) {
					   	  $absencies = getTotalIncidenciasAlumneGrupMateria($db,$row["idalumnes"],TIPUS_FALTA_ALUMNE_ABSENCIA,$idgrups,$idmateria,$data_inici,$data_fi);
						  $abs_programat = round(($absencies/$total_classes)*100,2);
						  $abs_real      = round(($absencies/$total_seguiment)*100,2);
						  
						  echo "<tr>";
						  echo "<td valign='top' width='30'>".$linea."</td>";
						  echo "<td valign='top' class='drop'>".$row['Valor']."</td>";
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
						  $linea++;
				   }
				?>          
		</table>
	</div>

<?php
	}
    }
	else {
?>
  <h5 style='margin-bottom:0px;'>
  &nbsp;Informe de faltes de l'alumne <a style=' color: #000066; border:1px dashed #CCCCCC; padding:3px 3px 3px 3px '>
  <?= getAlumne($db,$c_alumne,TIPUS_nom_complet) ?></a>
  &nbsp;Desde el <a style=' color: #000066; border:1px dashed #CCCCCC; padding:3px 3px 3px 3px '><?= $txt_inici ?></a>
  &nbsp;&nbsp;fins al <a style=' color: #000066; border:1px dashed #CCCCCC; padding:3px 3px 3px 3px '><?= $txt_fi ?></a>
 </h5>
 <div class='left'>
	&nbsp;
 </div>
 <div class='right'>
 <table>
    <tr>
        <td><strong>FALTES</strong></td>
        <td><strong>RETARDS</strong></td>
        <td><strong>JUSTIFICADES</strong></td>
        <td><strong>SEGUIMENTS</strong></td>
        <td><strong>CCC</strong></td>
    </tr>
    <tr>
        <td class='drop'><?=getTotalIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_ABSENCIA,$idgrups,$idmateria,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_RETARD,$idgrups,$idmateria,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_JUSTIFICADA,$idgrups,$idmateria,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_SEGUIMENT,$idgrups,$idmateria,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalCCCAlumneGrupMateria($db,$c_alumne,$idgrups,$idmateria,$data_inici,$data_fi)?></td>
    </tr>
 </table>
        <h5>Relaci&oacute; de faltes</h5>
 		<table>
            <tr>
                <td>&nbsp;</td>
                <td><strong>DATA</strong></td>
                <td><strong>F. HOR&Agrave;RIA</strong></td>
                <td><strong>PROFESSOR/A</strong></td>
                <td><strong>MAT&Egrave;RIA</strong></td>
            </tr>
            
                <?php
				   $linea         = 1;
				   $rsIncidencias = getIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_ABSENCIA,$idgrups,$idmateria,$data_inici,$data_fi);
				   foreach($rsIncidencias->fetchAll() as $row) {
						  echo "<tr>";
						  echo "<td valign='top' width='30'>".$linea."</td>";
						  echo "<td valign='top' width='100' class='drop'>".substr($row["data"],8,2)."-".substr($row["data"],5,2)."-".substr($row["data"],0,4)."</td>";
						  echo "<td valign='top' width='80' class='drop'>".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_inici"],0,5)."-".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_fi"],0,5)."</td>";
						  echo "<td valign='top' width='200' class='drop'>".getProfessor($db,$row["idprofessors"],TIPUS_nom_complet)."</td>";
						  echo "<td valign='top' class='drop'>".getMateria($db,$row["id_mat_uf_pla"])["nom_materia"]."</td></tr>";
						  $linea++;
				   }
				?>          
		</table>
        
        <br/>
        <h5>Relaci&oacute; de retards</h5>
 		<table>
            <tr>
                <td>&nbsp;</td>
                <td><strong>DATA</strong></td>
                <td><strong>F. HOR&Agrave;RIA</strong></td>
                <td><strong>PROFESSOR/A</strong></td>
                <td><strong>MAT&Egrave;RIA</strong></td>
            </tr>
            
                <?php
				   $linea         = 1;
				   $rsIncidencias = getIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_RETARD,$idgrups,$idmateria,$data_inici,$data_fi);
				   foreach($rsIncidencias->fetchAll() as $row) {
						  echo "<tr>";
						  echo "<td valign='top' width='30'>".$linea."</td>";
						  echo "<td valign='top' width='100' class='drop'>".substr($row["data"],8,2)."-".substr($row["data"],5,2)."-".substr($row["data"],0,4)."</td>";
						  echo "<td valign='top' width='80' class='drop'>".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_inici"],0,5)."-".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_fi"],0,5)."</td>";
						  echo "<td valign='top' width='200' class='drop'>".getProfessor($db,$row["idprofessors"],TIPUS_nom_complet)."</td>";
						  echo "<td valign='top' class='drop'>".getMateria($db,$row["id_mat_uf_pla"])["nom_materia"]."</td></tr>";
						  $linea++;
				   }
				?>          
		</table>
        
        <br/>
        <h5>Relaci&oacute; de justificacions</h5>
 		<table>
            <tr>
                <td>&nbsp;</td>
                <td><strong>DATA</strong></td>
                <td><strong>F. HOR&Agrave;RIA</strong></td>
                <td><strong>PROFESSOR/A</strong></td>
                <td><strong>MAT&Egrave;RIA</strong></td>
                <td><strong>OBSERVACIONS</strong></td>
            </tr>
            
                <?php
				   $linea         = 1;
				   $rsIncidencias = getIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_JUSTIFICADA,$idgrups,$idmateria,$data_inici,$data_fi);
				   foreach($rsIncidencias->fetchAll() as $row) {
						  echo "<tr>";
						  echo "<td valign='top' width='30'>".$linea."</td>";
						  echo "<td valign='top' width='100' class='drop'>".substr($row["data"],8,2)."-".substr($row["data"],5,2)."-".substr($row["data"],0,4)."</td>";
						  echo "<td valign='top' width='80' class='drop'>".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_inici"],0,5)."-".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_fi"],0,5)."</td>";
						  echo "<td valign='top' width='200' class='drop'>".getProfessor($db,$row["idprofessors"],TIPUS_nom_complet)."</td>";
						  echo "<td valign='top' class='drop'>".getMateria($db,$row["id_mat_uf_pla"])["nom_materia"]."</td>";
						  echo "<td valign='top' class='drop'>".nl2br($row["comentari"])."</td></tr>";
						  $linea++;
				   }
				?>          
		</table>

        <br/>
        <h5>Relaci&oacute; de seguiments</h5>
 		<table>
            <tr>
                <td>&nbsp;</td>
                <td><strong>TIPUS</strong></td>
                <td><strong>DATA</strong></td>
                <td><strong>F. HOR&Agrave;RIA</strong></td>
                <td><strong>PROFESSOR/A</strong></td>
                <td><strong>MAT&Egrave;RIA</strong></td>
                <td><strong>OBSERVACIONS</strong></td>
            </tr>
            
                <?php
				   $linea         = 1;
				   $rsIncidencias = getIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_SEGUIMENT,$idgrups,$idmateria,$data_inici,$data_fi);
				   foreach($rsIncidencias->fetchAll() as $row) {
						  echo "<tr>";
						  echo "<td valign='top' width='30'>".$linea."</td>";
						  echo "<td valign='top' width='40' class='drop'>".getLiteralTipusIncident($db,$row["id_tipus_incident"])["tipus_incident"]."</td>";
						  echo "<td valign='top' width='70' class='drop'>".substr($row["data"],8,2)."-".substr($row["data"],5,2)."-".substr($row["data"],0,4)."</td>";
						  echo "<td valign='top' width='80' class='drop'>".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_inici"],0,5)."-".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_fi"],0,5)."</td>";
						  echo "<td valign='top' width='50' class='drop'>".getProfessor($db,$row["idprofessors"],TIPUS_nom_complet)."</td>";
						  echo "<td valign='top' width='50' class='drop'>".getMateria($db,$row["id_mat_uf_pla"])["nom_materia"]."</td>";
						  echo "<td valign='top' width='300' class='drop'>".nl2br($row["comentari"])."</td></tr>";
						  $linea++;
				   }
				?>          
		</table>
        
        <br/>
        <h5>Relaci&oacute; de CCC</h5>
 		<table>
            <tr>
                <td>&nbsp;</td>
                <td><strong>TIPUS CCC</strong></td>
                <td><strong>DATA</strong></td>
                <td><strong>EXPULSI&Oacute;</strong></td>
                <td><strong>PROFESSOR/A</strong></td>
                <td><strong>MAT&Egrave;RIA</strong></td>
                <td><strong>DESCRIPCI&Oacute;</strong></td>
            </tr>
            
                <?php
				   $linea         = 1;
				   $rsIncidencias = getCCCAlumneGrupMateria($db,$c_alumne,$idgrups,$idmateria,$data_inici,$data_fi);
				   foreach($rsIncidencias->fetchAll() as $row) {
						  echo "<tr>";
						  echo "<td valign='top' width='20'>".$linea."</td>";
						  echo "<td valign='top' width='40' class='drop'>".getLiteralTipusCCC($db,$row["id_falta"])["nom_falta"]."</td>";
						  echo "<td valign='top' width='70' class='drop'>".substr($row["data"],8,2)."-".substr($row["data"],5,2)."-".substr($row["data"],0,4)."</td>";
						  echo "<td valign='top' width='40' class='drop'>".$row["expulsio"]."</td>";
						  echo "<td valign='top' width='50' class='drop'>".getProfessor($db,$row["idprofessor"],TIPUS_nom_complet)."</td>";
						  echo "<td valign='top' width='50' class='drop'>".(intval($row["idmateria"]!=0) ? getMateria($db,$row["idmateria"])["nom_materia"] : '')."</td>";
						  echo "<td valign='top' width='400' class='drop'><strong>Desc. breu</strong><br>".getLiteralMotiusCCC($db,$row["id_motius"])["nom_motiu"];
						  echo "<br><strong>Desc. detallada</strong><br>".nl2br($row["descripcio_detallada"])."</td></tr>";
						  $linea++;
				   }
				?>          
		</table>
 </div>
 
<?php
		if (isset($rsAlumnes)) {
			//mysql_free_result($rsAlumnes);
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