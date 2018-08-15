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
  
  if ( isset($_REQUEST['idprofessor']) && ($_REQUEST['idprofessor']==0) ) {
    $idprofessor = 0;
  }
  else if ( isset($_REQUEST['idprofessor']) ) {
    $idprofessor = $_REQUEST['idprofessor'];
  }
  if (! isset($idprofessor)) {
    $idprofessor = 0;
  }
  
  if ( isset($_REQUEST['idgrup']) && ($_REQUEST['idgrup']==0) ) {
  	$idgrup = 0;
  }
  else if ( isset($_REQUEST['idgrup']) ) {
    $idgrup = $_REQUEST['idgrup'];
  }
  if (! isset($idgrup)) {
    $idgrup = 0;
  }
  
  if ( isset($_REQUEST['c_materia']) && ($_REQUEST['c_materia']==0) ) {
    $c_materia = 0;
  }
  else if ( isset($_REQUEST['c_materia']) ) {
    $c_materia = $_REQUEST['c_materia'];
  }
  if (! isset($c_materia)) {
    $c_materia = 0;
  } 
  
  if ( isset($_REQUEST['c_alumne']) && ($_REQUEST['c_alumne']==0) ) {
  	$c_alumne = 0;
  }
  else if ( isset($_REQUEST['c_alumne']) ) {
    $c_alumne = $_REQUEST['c_alumne'];
  }
  if (! isset($c_alumne)) {
    $c_alumne = 0;
  }
  
  $box_al             = isset($_REQUEST['box_al'])             ? $_REQUEST['box_al']             : '';
  $box_faltes         = isset($_REQUEST['box_faltes'])         ? $_REQUEST['box_faltes']         : '';
  $box_retards        = isset($_REQUEST['box_retards'])        ? $_REQUEST['box_retards']        : '';
  $box_justificacions = isset($_REQUEST['box_justificacions']) ? $_REQUEST['box_justificacions'] : '';
  $box_incidencies    = isset($_REQUEST['box_incidencies'])    ? $_REQUEST['box_incidencies']    : '';
  $box_CCC            = isset($_REQUEST['box_CCC'])            ? $_REQUEST['box_CCC']            : '';
 
  $mode_impresio      = isset($_REQUEST['mode_impresio'])      ? $_REQUEST['mode_impresio']      : 0;
?>

 <?php
  	if (! $mode_impresio) {
  ?>
  <h4 style="margin-bottom:0px">
  <form id="ff" name="ff" method="post"> 
  Grup 
  <input id="idgrup" name="idgrup" class="easyui-combobox" style="width:250px" data-options="required:false,panelWidth:250">
   Mat&egrave;ria
  <select id="c_materia" class="easyui-combobox" name="c_materia" style="width:400px;" data-options="valueField:'idmateria',textField:'materia'"></select>
  <p>Alumne
  <select id="c_alumne" name="c_alumne" class="easyui-combobox" name="state" style="width:330px;">
      <option value="0">Tots els alumnes ...</option>
  </select></p>
  <p>
  <input id="box_al" name="box_al" type="checkbox" value="alumne" /> Alumnes 
  <input id="box_faltes" name="box_faltes" type="checkbox" value="falta" /> Faltes 
  <input id="box_retards" name="box_retards" type="checkbox" value="retard" /> Retards 
  <input id="box_justificacions" name="box_justificacions" type="checkbox" value="justificacio" /> Justificacions 
  <input id="box_incidencies" name="box_incidencies" type="checkbox" value="incidencia" /> Seguiments 
  <input id="box_CCC" name="box_CCC" type="checkbox" value="CCC" /> CCC 
  </p>
  Desde <input id="data_inici" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"></input>
  Fins a <input id="data_fi" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"></input>
  </h4>
  <p align="right" style=" border:0px solid #0C6; height:32px; background:whitesmoke;">
  <a href="#" onclick="doSearch(<?= $idprofessor ?>)">
  <img src="./images/icons/icon_search.png" height="32"/></a>
  <a href="#" onclick="javascript:imprimirPDF(<?= $idprofessor ?>)">
  <img src="./images/icons/icon_pdf.png" height="32"/></a>
  <a href="#" onclick="javascript:imprimirWord(<?= $idprofessor ?>)">
  <img src="./images/icons/icon_word.png" height="32"/></a>
  <a href="#" onclick="javascript:imprimirExcel(<?= $idprofessor ?>)">
  <img src="./images/icons/icon_excel.png" height="32"/></a>
  </form>
  </p>
  <?php
  	}
  ?>
  
 <div id="resultDiv">
  
  <h2>
  Informe d'assist&egrave;ncia
    (<a><?= $txt_inici ?></a>
   - <a><?= $txt_fi ?></a>)
  <br />
  <a>
  <?php 
  	if ($idprofessor != 0) {
		echo getProfessor($db,$idprofessor,TIPUS_nom_complet);
	}
   ?>  
   <?php 
  	if ($idgrup != 0) {
		echo getGrup($db,$idgrup)["nom"];
	}
   ?>
   <?php 
  	if ($c_materia != 0) {
		echo "<br>".getMateria($db,$c_materia)["nom_materia"];
	}
   ?>
   <?php 
  	if ($c_alumne != 0) {
		echo "<br>".getAlumne($db,$c_alumne,TIPUS_nom_complet);
	}
   ?>
   </a>
  </h2>

 <br />
 
 <div class="right">
 
 <?php
  if ($box_al != '') {
 ?>
 <h5>Alumnes del grup, quadre general</h5>
 <table>
    <tr>
    	<td> </td>
        <td><strong>ALUMNE</strong></td>
        <td><strong>NUM. FALTES</strong></td>
        <td><strong>NUM. RETARDS</strong></td>
        <td><strong>NUM. JUSTIFICADES</strong></td>
        <td><strong>NUM. SEGUIMENTS</strong></td>
        <td><strong>NUM. CCC</strong></td>
    </tr>
    <?php
		$linea = 1;
                if ($c_materia == 0) {
                    $rsAlumnes = getAlumnesGrup($db,$idgrup,TIPUS_nom_complet); 
                }
                else {
                    $rsAlumnes = getAlumnesGrupMateria($db,$idgrup,$c_materia,TIPUS_nom_complet);
                }
                
		foreach($rsAlumnes->fetchAll() as $row) {
		  echo "<tr>";
		  echo "<td valign='top' width='30'>".$linea."</td>";
		  echo "<td valign='top' class='drop'>".$row["Valor"]."</td>";
		  echo "<td valign='top' width='90' class='drop'>".getTotalIncidenciasProfessorAlumne($db,$idprofessor,$row["idalumnes"],TIPUS_FALTA_ALUMNE_ABSENCIA,$data_inici,$data_fi)."</td>";
		  echo "<td valign='top' width='90' class='drop'>".getTotalIncidenciasProfessorAlumne($db,$idprofessor,$row["idalumnes"],TIPUS_FALTA_ALUMNE_RETARD,$data_inici,$data_fi)."</td>";
		  echo "<td valign='top' width='90' class='drop'>".getTotalIncidenciasProfessorAlumne($db,$idprofessor,$row["idalumnes"],TIPUS_FALTA_ALUMNE_JUSTIFICADA,$data_inici,$data_fi)."</td>";
		  echo "<td valign='top' width='90' class='drop'>".getTotalIncidenciasProfessorAlumne($db,$idprofessor,$row["idalumnes"],TIPUS_FALTA_ALUMNE_SEGUIMENT,$data_inici,$data_fi)."</td>";
		  echo "<td valign='top' width='90' class='drop'>".getTotalCCCProfessorAlumneGrup($db,$idprofessor,$row["idalumnes"],$idgrup,$data_inici,$data_fi)."</td></tr>";
		  $linea++;
		}
	?>
    <tr>
    	<td colspan="7"><strong>Totals</strong></td>
    </tr>
    <tr>
    	<td class='drop'> </td>
        <td class='drop'> </td>
        <td class='drop'><?=getTotalIncidenciasProfessorGrup($db,$idprofessor,$idgrup,TIPUS_FALTA_ALUMNE_ABSENCIA,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalIncidenciasProfessorGrup($db,$idprofessor,$idgrup,TIPUS_FALTA_ALUMNE_RETARD,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalIncidenciasProfessorGrup($db,$idprofessor,$idgrup,TIPUS_FALTA_ALUMNE_JUSTIFICADA,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalIncidenciasProfessorGrup($db,$idprofessor,$idgrup,TIPUS_FALTA_ALUMNE_SEGUIMENT,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalCCCProfessorGrup($db,$idprofessor,$idgrup,$data_inici,$data_fi)?></td>
    </tr>
 </table>
 <br />
 <?php
  }
 ?>
 
 <?php
  if ($box_faltes != '') {
 ?>
 <h5>Relaci&oacute; de faltes</h5>
 <table>
            <tr>
                <td> </td>
                <td><strong>DATA</strong></td>
                <td><strong>F. HOR&Agrave;RIA</strong></td>
                <td><strong>ALUMNE/A</strong></td>
                <td><strong>MAT&Egrave;RIA</strong></td>
            </tr>
           
                <?php
				   $linea         = 1;
                                   if ($c_alumne == 0 && $c_materia == 0) {
                                     $rsIncidencias = getIncidenciasGrupMateriasProfessor($db,$idprofessor,$idgrup,TIPUS_FALTA_ALUMNE_ABSENCIA,$data_inici,$data_fi);
                                   }
                                   else if ($c_alumne != 0 && $c_materia == 0) {
                                     $rsIncidencias = getIncidenciasGrupAlumneMateriasProfessor($db,$idprofessor,$idgrup,$c_alumne,TIPUS_FALTA_ALUMNE_ABSENCIA,$data_inici,$data_fi);
                                   }
                                   else if ($c_alumne == 0 && $c_materia != 0) {
                                     $rsIncidencias = getIncidenciasGrupMateria($db,$idgrup,$c_materia,TIPUS_FALTA_ALUMNE_ABSENCIA,$data_inici,$data_fi);
                                   }
                                   else {
                                     $rsIncidencias = getIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_ABSENCIA,$idgrup,$c_materia,$data_inici,$data_fi);
                                   }
                                   foreach($rsIncidencias->fetchAll() as $row) {
						  echo "<tr>";
						  echo "<td valign='top' width='30'>".$linea."</td>";
						  echo "<td valign='top' width='90' class='drop'>".substr($row["data"],8,2)."-".substr($row["data"],5,2)."-".substr($row["data"],0,4)."</td>";
						  echo "<td valign='top' width='80' class='drop'>".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_inici"],0,5)."-".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_fi"],0,5)."</td>";
						  echo "<td valign='top' width='200' class='drop'>".getAlumne($db,$row["idalumnes"],TIPUS_nom_complet)."</td>";
						  echo "<td valign='top' class='drop'>".getMateria($db,$row["id_mat_uf_pla"])["nom_materia"]."</td></tr>";
						  $linea++;
				   }
				?>          
  </table>
  <br />
  <?php
  }
  ?> 
        
  <?php
  if ($box_retards != '') {
  ?> 
  <h5>Relaci&oacute; de retards</h5>
  <table>
            <tr>
                <td> </td>
                <td><strong>DATA</strong></td>
                <td><strong>F. HOR&Agrave;RIA</strong></td>
                <td><strong>ALUMNE/A</strong></td>
                <td><strong>MAT&Egrave;RIA</strong></td>
            </tr>
            
                <?php
				   $linea         = 1;
                                   if ($c_alumne == 0 && $c_materia == 0) {
                                     $rsIncidencias = getIncidenciasGrupMateriasProfessor($db,$idprofessor,$idgrup,TIPUS_FALTA_ALUMNE_RETARD,$data_inici,$data_fi);
                                   }
                                   else if ($c_alumne != 0 && $c_materia == 0) {
                                     $rsIncidencias = getIncidenciasGrupAlumneMateriasProfessor($db,$idprofessor,$idgrup,$c_alumne,TIPUS_FALTA_ALUMNE_RETARD,$data_inici,$data_fi);
                                   }
                                   else if ($c_alumne == 0 && $c_materia != 0) {
                                     $rsIncidencias = getIncidenciasGrupMateria($db,$idgrup,$c_materia,TIPUS_FALTA_ALUMNE_RETARD,$data_inici,$data_fi);
                                   }
                                   else {
                                     $rsIncidencias = getIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_RETARD,$idgrup,$c_materia,$data_inici,$data_fi);
                                   }
				   foreach($rsIncidencias->fetchAll() as $row) {
						  echo "<tr>";
						  echo "<td valign='top' width='30'>".$linea."</td>";
						  echo "<td valign='top' width='90' class='drop'>".substr($row["data"],8,2)."-".substr($row["data"],5,2)."-".substr($row["data"],0,4)."</td>";
						  echo "<td valign='top' width='80' class='drop'>".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_inici"],0,5)."-".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_fi"],0,5)."</td>";
						  echo "<td valign='top' width='200' class='drop'>".getAlumne($db,$row["idalumnes"],TIPUS_nom_complet)."</td>";
						  echo "<td valign='top' class='drop'>".getMateria($db,$row["id_mat_uf_pla"])["nom_materia"]."</td></tr>";
						  $linea++;
				   }
				?>          
  </table>
  <br />   
  <?php
  }
  ?>
  
  <?php
  if ($box_justificacions != '') {
  ?>     
  <h5>Relaci&oacute; de justificacions</h5>
  <table>
            <tr>
                <td> </td>
                <td><strong>DATA</strong></td>
                <td><strong>F. HOR&Agrave;RIA</strong></td>
                <td><strong>ALUMNE/A</strong></td>
                <td><strong>MAT&Egrave;RIA</strong></td>
                <td><strong>OBSERVACIONS</strong></td>
            </tr>
            
                <?php
				   $linea         = 1;
                                   if ($c_alumne == 0 && $c_materia == 0) {
                                     $rsIncidencias = getIncidenciasGrupMateriasProfessor($db,$idprofessor,$idgrup,TIPUS_FALTA_ALUMNE_JUSTIFICADA,$data_inici,$data_fi);
                                   }
                                   else if ($c_alumne != 0 && $c_materia == 0) {
                                     $rsIncidencias = getIncidenciasGrupAlumneMateriasProfessor($db,$idprofessor,$idgrup,$c_alumne,TIPUS_FALTA_ALUMNE_JUSTIFICADA,$data_inici,$data_fi);
                                   }
                                   else if ($c_alumne == 0 && $c_materia != 0) {
                                     $rsIncidencias = getIncidenciasGrupMateria($db,$idgrup,$c_materia,TIPUS_FALTA_ALUMNE_JUSTIFICADA,$data_inici,$data_fi);
                                   }
                                   else {
                                     $rsIncidencias = getIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_JUSTIFICADA,$idgrup,$c_materia,$data_inici,$data_fi);
                                   }
				   foreach($rsIncidencias->fetchAll() as $row) {
						  echo "<tr>";
						  echo "<td valign='top' width='30'>".$linea."</td>";
						  echo "<td valign='top' width='90' class='drop'>".substr($row["data"],8,2)."-".substr($row["data"],5,2)."-".substr($row["data"],0,4)."</td>";
						  echo "<td valign='top' width='80' class='drop'>".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_inici"],0,5)."-".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_fi"],0,5)."</td>";
						  echo "<td valign='top' width='200' class='drop'>".getAlumne($db,$row["idalumnes"],TIPUS_nom_complet)."</td>";
						  echo "<td valign='top' class='drop'>".getMateria($db,$row["id_mat_uf_pla"])["nom_materia"]."</td>";
						  echo "<td valign='top' class='drop'>".nl2br($row["comentari"])."</td></tr>";
						  $linea++;
				   }
				?>          
  </table>
  <br />   
  <?php
  }
  ?>
   
  <?php
  if ($box_incidencies != '') {
  ?>
  <h5>Relaci&oacute; de seguiments</h5>
  <table>
            <tr>
                <td> </td>
                <td><strong>TIPUS</strong></td>
                <td><strong>DATA</strong></td>
                <td><strong>F. HOR&Agrave;RIA</strong></td>
                <td><strong>ALUMNE/A</strong></td>
                <td><strong>MAT&Egrave;RIA</strong></td>
                <td><strong>OBSERVACIONS</strong></td>
            </tr>
            
                <?php
				   $linea         = 1;
                                   if ($c_alumne == 0 && $c_materia == 0) {
                                     $rsIncidencias = getIncidenciasGrupMateriasProfessor($db,$idprofessor,$idgrup,TIPUS_FALTA_ALUMNE_SEGUIMENT,$data_inici,$data_fi);
                                   }
                                   else if ($c_alumne != 0 && $c_materia == 0) {
                                     $rsIncidencias = getIncidenciasGrupAlumneMateriasProfessor($db,$idprofessor,$idgrup,$c_alumne,TIPUS_FALTA_ALUMNE_SEGUIMENT,$data_inici,$data_fi);
                                   }
                                   else if ($c_alumne == 0 && $c_materia != 0) {
                                     $rsIncidencias = getIncidenciasGrupMateria($db,$idgrup,$c_materia,TIPUS_FALTA_ALUMNE_SEGUIMENT,$data_inici,$data_fi);
                                   }
                                   else {
                                     $rsIncidencias = getIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_SEGUIMENT,$idgrup,$c_materia,$data_inici,$data_fi);
                                   }
				   foreach($rsIncidencias->fetchAll() as $row) {
						  echo "<tr>";
						  echo "<td valign='top' width='20'>".$linea."</td>";
						  echo "<td valign='top' width='40' class='drop'>".getLiteralTipusIncident($db,$row["id_tipus_incident"])["tipus_incident"]."</td>";
						  echo "<td valign='top' width='70' class='drop'>".substr($row["data"],8,2)."-".substr($row["data"],5,2)."-".substr($row["data"],0,4)."</td>";
						  echo "<td valign='top' width='80' class='drop'>".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_inici"],0,5)."-".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_fi"],0,5)."</td>";
						  echo "<td valign='top' width='100' class='drop'>".getAlumne($db,$row["idalumnes"],TIPUS_nom_complet)."</td>";
						  echo "<td valign='top' width='50' class='drop'>".getMateria($db,$row["id_mat_uf_pla"])["nom_materia"]."</td>";
						  echo "<td valign='top' width='300' class='drop'>".nl2br($row["comentari"])."</td></tr>";
						  $linea++;
				   }
				?>          
	</table>
    <br />   
	<?php
    }
    ?>
    
    <?php
    if ($box_CCC != '') {
    ?>
    <h5>Relaci&oacute; de CCC</h5>
 	<table>
            <tr>
                <td> </td>
                <td><strong>TIPUS CCC</strong></td>
                <td><strong>DATA</strong></td>
                <td><strong>EXPULSI&Oacute;</strong></td>
                <td><strong>ALUMNE/A</strong></td>
                <td><strong>MAT&Egrave;RIA</strong></td>
                <td><strong>DESCRIPCI&Oacute;</strong></td>
            </tr>
            
                <?php
				   $linea         = 1;
                                   if ($c_alumne == 0 && $c_materia == 0) {
                                     $rsIncidencias = getCCCGrupMateriasProfessor($db,$idprofessor,$idgrup,$data_inici,$data_fi);
                                   }
                                   else if ($c_alumne != 0 && $c_materia == 0) {
                                     $rsIncidencias = getCCCGrupAlumneMateriasProfessor($db,$idprofessor,$idgrup,$c_alumne,$data_inici,$data_fi);
                                   }
                                   else if ($c_alumne == 0 && $c_materia != 0) {
                                     $rsIncidencias = getCCCGrupMateria($db,$idgrup,$c_materia,$data_inici,$data_fi);
                                   }
                                   else {
                                     $rsIncidencias = getCCCAlumneGrupMateria($db,$c_alumne,$idgrup,$c_materia,$data_inici,$data_fi);
                                   }
                                   
				   foreach($rsIncidencias->fetchAll() as $row) {
						  echo "<tr>";
						  echo "<td valign='top' width='20'>".$linea."</td>";
						  echo "<td valign='top' width='40' class='drop'>".getLiteralTipusCCC($db,$row["id_falta"])["nom_falta"]."</td>";
						  echo "<td valign='top' width='70' class='drop'>".substr($row["data"],8,2)."-".substr($row["data"],5,2)."-".substr($row["data"],0,4)."</td>";
						  echo "<td valign='top' width='40' class='drop'>".$row["expulsio"]."</td>";
						  echo "<td valign='top' width='120' class='drop'>".getAlumne($db,$row["idalumne"],TIPUS_nom_complet)."</td>";
						  echo "<td valign='top' width='50' class='drop'>".(intval($row["idmateria"]!=0) ? getMateria($db,$row["idmateria"])["nom_materia"] : '')."</td>";
						  echo "<td valign='top' width='320' class='drop'><strong>Desc. breu</strong><br>".getLiteralMotiusCCC($db,$row["id_motius"])["nom_motiu"];
						  echo "<br><strong>Desc. detallada</strong><br>".nl2br($row["descripcio_detallada"])."</td></tr>";
						  $linea++;
				   }
				?>          
	</table>
    <?php
    }
    ?>
        
 </div>
    
<?php
	if (isset($rsAlumnes)) {
    	//mysql_free_result($rsAlumnes);
	}
	if (isset($rsIncidencias)) {
    	//mysql_free_result($rsIncidencias);
	}
	if (isset($rsEquipDocent)) {
    	//mysql_free_result($rsEquipDocent);
	}
?>

</div>