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
  
  $box_dg             = isset($_REQUEST['box_dg'])             ? $_REQUEST['box_dg']             : '';
  $box_faltes         = isset($_REQUEST['box_faltes'])         ? $_REQUEST['box_faltes']         : '';
  $box_retards        = isset($_REQUEST['box_retards'])        ? $_REQUEST['box_retards']        : '';
  $box_justificacions = isset($_REQUEST['box_justificacions']) ? $_REQUEST['box_justificacions'] : '';
  $box_incidencies    = isset($_REQUEST['box_incidencies'])    ? $_REQUEST['box_incidencies']    : '';
  $box_CCC            = isset($_REQUEST['box_CCC'])            ? $_REQUEST['box_CCC']            : '';
  
  $curs_escolar       = getCursActual($db)["idperiodes_escolars"]; 
  $mode_impresio      = isset($_REQUEST['mode_impresio'])      ? $_REQUEST['mode_impresio']      : 0;
?>

<?php
  	if (! $mode_impresio) {
?>
  <h4 style="margin-bottom:0px; margin-top:-5px;">
 
  <form id="ff" name="ff" method="post">
  <table bgcolor="whitesmoke" width="100%">
  <tr>
  	<td>
      <input id="box_dg" name="box_dg" type="checkbox" value="dg" checked="checked" /> Dades globals 
      <input id="box_faltes" name="box_faltes" type="checkbox" value="falta" checked="checked" /> Faltes 
      <input id="box_retards" name="box_retards" type="checkbox" value="retard" checked="checked" /> Retards 
      <input id="box_justificacions" name="box_justificacions" type="checkbox" value="justificacio" checked="checked" /> Justificacions 
      <input id="box_incidencies" name="box_incidencies" type="checkbox" value="incidencia" checked="checked" /> Seguiments 
      <input id="box_CCC" name="box_CCC" type="checkbox" value="CCC" checked="checked" /> CCC 
         
      <br />Desde <input id="data_inici" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"></input>
      Fins a <input id="data_fi" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"></input>
    </td>
    <td align="right">
      <a href="#" onclick="doSearch(<?= $c_alumne ?>)">
      <img src="./images/icons/icon_search.png" height="32"/></a>
      <a href="#" onclick="javascript:imprimirPDF(<?= $c_alumne ?>)">
      <img src="./images/icons/icon_pdf.png" height="32"/></a>
      <a href="#" onclick="javascript:imprimirWord(<?= $c_alumne ?>)">
      <img src="./images/icons/icon_word.png" height="32"/></a>
      <a href="#" onclick="javascript:imprimirExcel(<?= $c_alumne ?>)">
      <img src="./images/icons/icon_excel.png" height="32"/></a>
    </td>
  </tr>
  </table>
  </form>
  </h4>

<?php
  	}
?>


 <div id="resultDiv" style="">
  <br />
  <h5>
   Informe de faltes de l'alumne <a><?= getAlumne($db,$c_alumne,TIPUS_nom_complet) ?></a>
 </h5>
 <div class="right">
 <table>
    <tr>
        <td><strong>NUM. FALTES</strong></td>
        <td><strong>NUM. RETARDS</strong></td>
        <td><strong>NUM. JUSTIFICADES</strong></td>
        <td><strong>NUM. SEGUIMENTS</strong></td>
        <td><strong>NUM. CCC</strong></td>
    </tr>
    <tr>
        <td class='drop'><?=getTotalIncidenciasAlumne($db,$c_alumne,TIPUS_FALTA_ALUMNE_ABSENCIA,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalIncidenciasAlumne($db,$c_alumne,TIPUS_FALTA_ALUMNE_RETARD,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalIncidenciasAlumne($db,$c_alumne,TIPUS_FALTA_ALUMNE_JUSTIFICADA,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalIncidenciasAlumne($db,$c_alumne,TIPUS_FALTA_ALUMNE_SEGUIMENT,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalCCCAlumne($db,$c_alumne,$data_inici,$data_fi)?></td>
    </tr>
 </table>
 <br />     
 
 <?php
  if ($box_dg != '') {
 ?>
  <h5>Mat&egrave;ries cursades</h5>
 <table>
    <tr>
    	<td> </td>
        <td><strong>Mat&egrave;ria</strong></td>
        <td><strong>DIES LECTIUS</strong></td>
        <td><strong>NUM. FALTES</strong></td>
        <td><strong>NUM. RETARDS</strong></td>
        <td><strong>NUM. JUSTIFICADES</strong></td>
        <td><strong>NUM. SEGUIMENTS</strong></td>
        <td><strong>NUM. CCC</strong></td>
    </tr>
    
    <?php
		$linea = 1;
		$rsMateries = getMateriesAlumne($db,$curs_escolar,$c_alumne);
                foreach($rsMateries->fetchAll() as $row) { 
		  
		  $grup_materia  = existGrupMateria($db,$row["idgrups"],$row["id_mat_uf_pla"]);
		  $total_classes = classes_entre_dates($db,$data_inici,$data_fi,$grup_materia,$curs_escolar);
		  $escicleloe    = isMateria($db,$row["id_mat_uf_pla"]) ? 0 : 1 ;
		  if ($escicleloe) {
			 $data_inici = getGrupMateria($db,$grup_materia)["data_inici"];
			 $txt_inici  = substr($data_inici,8,2)."-".substr($data_inici,5,2)."-".substr($data_inici,0,4);
			 $data_fi    = getGrupMateria($db,$grup_materia)["data_fi"];
			 $txt_fi     = substr($data_fi,8,2)."-".substr($data_fi,5,2)."-".substr($data_fi,0,4);
		  }
		  
		  $total_absencies = getTotalIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_ABSENCIA,$row["idgrups"],$row["id_mat_uf_pla"],$data_inici,$data_fi);
		  
		  $total_retards = getTotalIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_RETARD,$row["idgrups"],$row["id_mat_uf_pla"],$data_inici,$data_fi);
		 
		  $total_justificacions = getTotalIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_JUSTIFICADA,$row["idgrups"],$row["id_mat_uf_pla"],$data_inici,$data_fi);
		  
		  $total_seguiments = getTotalIncidenciasAlumneGrupMateria($db,$c_alumne,TIPUS_FALTA_ALUMNE_SEGUIMENT,$row["idgrups"],$row["id_mat_uf_pla"],$data_inici,$data_fi);
		  
		  $total_ccc = getTotalCCCAlumneGrupMateria($db,$c_alumne,$row["idgrups"],$row["id_mat_uf_pla"],$data_inici,$data_fi);
		  
		  
		  echo "<tr>";
		  echo "<td valign='top' width='30'>".$linea."</td>";
		  echo "<td valign='top' class='drop'>".$row["materia"]."</td>";
		  echo "<td valign='top' class='drop' width='50'>".$total_classes."</td>";
		  echo "<td valign='top' width='90' class='drop'>";
		  if ($total_absencies != 0) {
			  echo "<strong>".$total_absencies."</strong> (".round(($total_absencies/$total_classes)*100,2).")%";
		  }
		  echo "</td>";
		  
		  echo "<td valign='top' width='90' class='drop'>";
		  if ($total_retards != 0) {
			  echo "<strong>".$total_retards."</strong> (".round(($total_retards/$total_classes)*100,2).")%";
		  }
		  echo "</td>";
		  
		  echo "<td valign='top' width='90' class='drop'>";
		  if ($total_justificacions != 0) {
			  echo "<strong>".$total_justificacions."</strong> (".round(($total_justificacions/$total_classes)*100,2).")%";
		  }
		  echo "</td>";
		  
		  echo "<td valign='top' width='90' class='drop'>";
		  if ($total_seguiments != 0) {
			  echo "<strong>".$total_seguiments."</strong> (".round(($total_seguiments/$total_classes)*100,2).")%";
		  }
		  echo "</td>";
		  
		  echo "<td valign='top' width='90' class='drop'>";
		  if ($total_ccc != 0) {
			  echo "<strong>".$total_ccc."</strong> (".round(($total_ccc/$total_classes)*100,2).")%";
		  }
		  echo "</td>";
		  
		  $linea++;
		}
	?>
    <tr>
    	<td colspan="8"><strong>Totals</strong></td>
    </tr>
    <tr>
    	<td class='drop'> </td>
        <td class='drop'> </td>
        <td class='drop'> </td>
        <td class='drop'><?=getTotalIncidenciasAlumne($db,$c_alumne,TIPUS_FALTA_ALUMNE_ABSENCIA,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalIncidenciasAlumne($db,$c_alumne,TIPUS_FALTA_ALUMNE_RETARD,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalIncidenciasAlumne($db,$c_alumne,TIPUS_FALTA_ALUMNE_JUSTIFICADA,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalIncidenciasAlumne($db,$c_alumne,TIPUS_FALTA_ALUMNE_SEGUIMENT,$data_inici,$data_fi)?></td>
        <td class='drop'><?=getTotalCCCAlumne($db,$c_alumne,$data_inici,$data_fi)?></td>
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
                <td><strong>HORA</strong></td>
                <td><strong>PROFESSOR/A</strong></td>
                <td><strong>MAT&Egrave;RIA</strong></td>
            </tr>
            
                <?php
				   $linea         = 1;
				   $rsIncidencias = getIncidenciasAlumne($db,$c_alumne,TIPUS_FALTA_ALUMNE_ABSENCIA,$data_inici,$data_fi);
                                   foreach($rsIncidencias->fetchAll() as $row) { 
						  echo "<tr>";
						  echo "<td valign='top' width='30'>".$linea."</td>";
						  echo "<td valign='top' width='90' class='drop'>".substr($row["data"],8,2)."-".substr($row["data"],5,2)."-".substr($row["data"],0,4)."</td>";
                                                  echo "<td valign='top' width='80' class='drop'>".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_inici"],0,5)."-".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_fi"],0,5)."</td>";
						  echo "<td valign='top' width='200' class='drop'>".getProfessor($db,$row["idprofessors"],TIPUS_nom_complet)."</td>";
						  echo "<td valign='top' class='drop'>".getMateria($db,$row["id_mat_uf_pla"])["nom_materia"]."</td>";
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
                <td><strong>HORA</strong></td>
                <td><strong>PROFESSOR/A</strong></td>
                <td><strong>MAT&Egrave;RIA</strong></td>
            </tr>
            
                <?php
				   $linea         = 1;
				   $rsIncidencias = getIncidenciasAlumne($db,$c_alumne,TIPUS_FALTA_ALUMNE_RETARD,$data_inici,$data_fi);
                                   foreach($rsIncidencias->fetchAll() as $row) { 
						  echo "<tr>";
						  echo "<td valign='top' width='30'>".$linea."</td>";
						  echo "<td valign='top' width='90' class='drop'>".substr($row["data"],8,2)."-".substr($row["data"],5,2)."-".substr($row["data"],0,4)."</td>";
                                                  echo "<td valign='top' width='80' class='drop'>".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_inici"],0,5)."-".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_fi"],0,5)."</td>";
						  echo "<td valign='top' width='200' class='drop'>".getProfessor($db,$row["idprofessors"],TIPUS_nom_complet)."</td>";
						  echo "<td valign='top' class='drop'>".getMateria($db,$row["id_mat_uf_pla"])["nom_materia"]."</td>";
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
                <td><strong>HORA</strong></td>
                <td><strong>PROFESSOR/A</strong></td>
                <td><strong>MAT&Egrave;RIA</strong></td>
            </tr>
            
                <?php
				   $linea         = 1;
				   $rsIncidencias = getIncidenciasAlumne($db,$c_alumne,TIPUS_FALTA_ALUMNE_JUSTIFICADA,$data_inici,$data_fi);
                                   foreach($rsIncidencias->fetchAll() as $row) { 
						  echo "<tr>";
						  echo "<td valign='top' width='30'>".$linea."</td>";
						  echo "<td valign='top' width='90' class='drop'>".substr($row["data"],8,2)."-".substr($row["data"],5,2)."-".substr($row["data"],0,4)."</td>";
                                                  echo "<td valign='top' width='80' class='drop'>".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_inici"],0,5)."-".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_fi"],0,5)."</td>";
						  echo "<td valign='top' width='200' class='drop'>".getProfessor($db,$row["idprofessors"],TIPUS_nom_complet)."</td>";
						  echo "<td valign='top' class='drop'>".getMateria($db,$row["id_mat_uf_pla"])["nom_materia"]."</td>";
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
                <td><strong>HORA</strong></td>
                <td><strong>PROFESSOR/A</strong></td>
                <td><strong>MAT&Egrave;RIA</strong></td>
                <td><strong>OBSERVACIONS</strong></td>
            </tr>
            
                <?php
				   $linea         = 1;
				   $rsIncidencias = getIncidenciasAlumne($db,$c_alumne,TIPUS_FALTA_ALUMNE_SEGUIMENT,$data_inici,$data_fi);
                                   foreach($rsIncidencias->fetchAll() as $row) { 
						  echo "<tr>";
						  echo "<td valign='top' width='20'>".$linea."</td>";
						  echo "<td valign='top' width='40' class='drop'>".getLiteralTipusIncident($db,$row["id_tipus_incident"])["tipus_incident"]."</td>";
						  echo "<td valign='top' width='70' class='drop'>".substr($row["data"],8,2)."-".substr($row["data"],5,2)."-".substr($row["data"],0,4)."</td>";
                                                  echo "<td valign='top' width='80' class='drop'>".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_inici"],0,5)."-".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_fi"],0,5)."</td>";
						  echo "<td valign='top' width='50' class='drop'>".getProfessor($db,$row["idprofessors"],TIPUS_nom_complet)."</td>";
						  echo "<td valign='top' width='50' class='drop'>".getMateria($db,$row["id_mat_uf_pla"])["nom_materia"]."</td>";
						  echo "<td valign='top' width='300' class='drop'>".nl2br($row["comentari"])."</td>";
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
                <td><strong>HORA</strong></td>
                <td><strong>EXPULSI&Oacute;</strong></td>
                <td><strong>PROFESSOR/A</strong></td>
                <td><strong>MAT&Egrave;RIA</strong></td>
                <td><strong>GRUP</strong></td>
                <td><strong>DESCRIPCI&Oacute;</strong></td>
            </tr>
            
                <?php
				   $linea         = 1;
				   $rsIncidencias = getCCCAlumne($db,$c_alumne,$data_inici,$data_fi);
                                   foreach($rsIncidencias->fetchAll() as $row) { 
						  echo "<tr>";
						  echo "<td valign='top' width='20'>".$linea."</td>";
						  echo "<td valign='top' width='40' class='drop'>".getLiteralTipusCCC($db,$row["id_falta"])["nom_falta"]."</td>";
						  echo "<td valign='top' width='70' class='drop'>".substr($row["data"],8,2)."-".substr($row["data"],5,2)."-".substr($row["data"],0,4)."</td>";
                                                  echo "<td valign='top' width='80' class='drop'>".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_inici"],0,5)."-".substr(getFranjaHoraria($db,$row["idfranges_horaries"])["hora_fi"],0,5)."</td>";
						  echo "<td valign='top' width='40' class='drop'>".$row["expulsio"]."</td>";
						  echo "<td valign='top' width='50' class='drop'>".getProfessor($db,$row["idprofessor"],TIPUS_nom_complet)."</td>";
						  echo "<td valign='top' width='50' class='drop'>".(intval($row["idmateria"]!=0) ? getMateria($db,$row["idmateria"])["nom_materia"] : '')."</td>";
						  echo "<td valign='top' width='50' class='drop'>".(intval($row["idgrup"]!=0) ? getGrup($db,$row["idgrup"])["nom"] : '')."</td>";
						  echo "<td valign='top' width='300' class='drop'><strong>Desc. breu</strong><br>".getLiteralMotiusCCC($db,$row["id_motius"])["nom_motiu"];
						  echo "<br><strong>Desc. detallada</strong><br>".nl2br($row["descripcio_detallada"])."</td>";
						  $linea++;
				   }
				?>          
		</table>
  <?php
  }
  ?>
        
 </div>
 
<?php
	if (isset($rsMateries)) {
    	//mysql_free_result($rsMateries);
	}
	if (isset($rsIncidencias)) {
    	//mysql_free_result($rsIncidencias);
	}
?>

</div>

<iframe id="fitxer_pdf" scrolling="yes" frameborder="0" style="width:10px;height:10px; visibility:hidden" src=""></iframe>

<script type="text/javascript">  		
	var url;
		
	function myformatter(date){  
            var y = date.getFullYear();  
            var m = date.getMonth()+1;  
            var d = date.getDate();  
            return (d<10?('0'+d):d)+'-'+(m<10?('0'+m):m)+'-'+y;
        }
		
	function myparser(s){  
            if (!s) return new Date();  
            var ss = (s.split('-'));  
            var y = parseInt(ss[0],10);  
            var m = parseInt(ss[1],10);  
            var d = parseInt(ss[2],10);  
            if (!isNaN(y) && !isNaN(m) && !isNaN(d)){  
                return new Date(d,m-1,y);
            } else {  
                return new Date();  
            }  
        }
		
	function doSearch(c_alumne){  
			d_inici = $('#data_inici').datebox('getValue');
			d_fi    = $('#data_fi').datebox('getValue');
			
			url = './families/families_informe_see.php?data_inici='+d_inici+'&data_fi='+d_fi+'&c_alumne='+c_alumne+'&mode_impresio=1';
			
			$('#ff').form('submit',{  
						url: url, 
						onSubmit: function(){  
							return $(this).form('validate');  
						},  
						success: function(result){
							$('#resultDiv').html(result);
							$('#idgrup').combobox('setValue', idgrup);
						}  
			}); 			 
        }  
		
		function imprimirPDF(c_alumne){  
			d_inici  = $('#data_inici').datebox('getValue');
			d_fi     = $('#data_fi').datebox('getValue');
			
			box_dg             = '<?= $box_dg ?>';
			box_faltes         = '<?= $box_faltes ?>';
			box_retards        = '<?= $box_retards ?>';
			box_justificacions = '<?= $box_justificacions ?>';
			box_incidencies    = '<?= $box_incidencies ?>';
			box_CCC            = '<?= $box_CCC ?>';
			
			url  = './families/families_informe_print.php?data_inici='+d_inici+'&data_fi='+d_fi+'&c_alumne='+c_alumne+'&mode_impresio=1';
			url += '&box_dg='+box_dg+'&box_faltes='+box_faltes+'&box_retards='+box_retards;
			url += '&box_justificacions='+box_justificacions+'&box_incidencies='+box_incidencies+'&box_CCC='+box_CCC;
			
			$('#fitxer_pdf').attr('src', url);
		}
		
		function imprimirWord(c_alumne){  
			d_inici  = $('#data_inici').datebox('getValue');
			d_fi     = $('#data_fi').datebox('getValue');
			
			box_dg             = '<?= $box_dg ?>';
			box_faltes         = '<?= $box_faltes ?>';
			box_retards        = '<?= $box_retards ?>';
			box_justificacions = '<?= $box_justificacions ?>';
			box_incidencies    = '<?= $box_incidencies ?>';
			box_CCC            = '<?= $box_CCC ?>';
			
			url  = './families/families_informe_print_word.php?data_inici='+d_inici+'&data_fi='+d_fi+'&c_alumne='+c_alumne+'&mode_impresio=1';
			url += '&box_dg='+box_dg+'&box_faltes='+box_faltes+'&box_retards='+box_retards;
			url += '&box_justificacions='+box_justificacions+'&box_incidencies='+box_incidencies+'&box_CCC='+box_CCC;
			
			$('#fitxer_pdf').attr('src', url);
		}
		
		function imprimirExcel(c_alumne){  
			d_inici  = $('#data_inici').datebox('getValue');
			d_fi     = $('#data_fi').datebox('getValue');
			
			box_dg             = '<?= $box_dg ?>';
			box_faltes         = '<?= $box_faltes ?>';
			box_retards        = '<?= $box_retards ?>';
			box_justificacions = '<?= $box_justificacions ?>';
			box_incidencies    = '<?= $box_incidencies ?>';
			box_CCC            = '<?= $box_CCC ?>';
			
			url  = './families/families_informe_print_excel.php?data_inici='+d_inici+'&data_fi='+d_fi+'&c_alumne='+c_alumne+'&mode_impresio=1';
			url += '&box_dg='+box_dg+'&box_faltes='+box_faltes+'&box_retards='+box_retards;
			url += '&box_justificacions='+box_justificacions+'&box_incidencies='+box_incidencies+'&box_CCC='+box_CCC;
			
			$('#fitxer_pdf').attr('src', url);
		}
				
</script>

<script type="text/javascript">
	$('#header').css('visibility', 'hidden');
	$('#footer').css('visibility', 'hidden');		
</script>