<?php
  session_start();
  require_once('../bbdd/connect.php');
  require_once('../func/constants.php');
  require_once('../func/generic.php');
  require_once('../func/seguretat.php');
  $db->exec("set names utf8");
  
  $txt_ca_inici = substr(getCursActual($db)["data_inici"],8,2)."-".substr(getCursActual($db)["data_inici"],5,2)."-".substr(getCursActual($db)["data_inici"],0,4);
  $txt_ca_fi  = substr(getCursActual($db)["data_fi"],8,2)."-".substr(getCursActual($db)["data_fi"],5,2)."-".substr(getCursActual($db)["data_fi"],0,4);
  
  $data_inici = isset($_REQUEST['data_inici']) ? substr($_REQUEST['data_inici'],6,4)."-".substr($_REQUEST['data_inici'],3,2)."-".substr($_REQUEST['data_inici'],0,2) : getCursActual($db)["data_inici"];
  if ($data_inici=='--') {
  	  $data_inici = getCursActual($db)["data_inici"];
  }
  $txt_inici  = isset($_REQUEST['data_inici']) ? $_REQUEST['data_inici'] : $txt_ca_inici;
  
  $data_fi    = isset($_REQUEST['data_fi'])    ? substr($_REQUEST['data_fi'],6,4)."-".substr($_REQUEST['data_fi'],3,2)."-".substr($_REQUEST['data_fi'],0,2)          : getCursActual($db)["data_fi"];
  if ($data_fi=='--') {
  	  $data_fi = getCursActual($db)["data_fi"];
  }
  $txt_fi     = isset($_REQUEST['data_fi'])    ? $_REQUEST['data_fi'] : $txt_ca_fi;
  
  $periode	    = getCursActual($db)["idperiodes_escolars"];
  $percentatge      = isset($_REQUEST['percentatge'])    ? $_REQUEST['percentatge']    : 5;
  $mode_impresio    = isset($_REQUEST['mode_impresio'])  ? $_REQUEST['mode_impresio']  : 0;
  
  $classeperDia = 6;
  $diesLectius   = dies_entre_dates($db,$data_inici,$data_fi,$periode);
  $diesLectius = $diesLectius * $classeperDia;
  $maxIncidenciesPermeses = round( ($diesLectius*$percentatge) / 100 );
?>

<style type="text/css">
@page {
	margin: 1cm;
}

body {
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

<style type="text/css">
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
		}
		.right table{
			background:#E0ECFF;
			width:100%;
		}
		.right td{
			background:#fafafa;
			text-align:left;
			padding:2px;
		}
		.right td{
			background:#E0ECFF;
		}
		.right td.drop{
			background:#fafafa;
		}
		.right td.over{
			background:#FBEC88;
		}
		.item{
			text-align:center;
			/*border:1px solid #499B33;*/
			background:#fafafa;
			/*width:100px;*/
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
   
 <?php
  	if (! $mode_impresio) {
  ?>
  <h4 style="margin-bottom:0px">
  <form id="ff" name="ff" method="post">
  Desde <input id="data_inici" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"></input>
  Fins a <input id="data_fi" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"></input>
  &nbsp;&nbsp;
  Percentatge&nbsp;<input id="percentatge" class="easyui-numberbox" value="5" size="5" data-options="precision:0,required:true,min:0,max:100">&nbsp;%&nbsp;
  </h4>
  <p align="right" style=" border:0px solid #0C6; height:32px; background:whitesmoke;">
  <a href="#" onclick="doSearch()">
  <img src="./images/icons/icon_search.png" height="32"/></a>
  <a href="#" onclick="javascript:imprimirPDF()">
  <img src="./images/icons/icon_pdf.png" height="32"/></a>
  <a href="#" onclick="javascript:imprimirWord()">
  <img src="./images/icons/icon_word.png" height="32"/></a>
  <a href="#" onclick="javascript:imprimirExcel()">
  <img src="./images/icons/icon_excel.png" height="32"/></a>
  </form>
  </p>
  <?php
  	}
  ?>
  
 <div id="resultDiv" style="width:960px; margin-top:-5px;">
  
  <h2 style="margin-bottom:0px">
  Dades globals
  <a style=" color: #000066; border:0px dashed #CCCCCC; padding:2px 2px 2px 2px ">
  
   &nbsp;(<a style=' color: #000066; border:1px dashed #CCCCCC; padding:3px 3px 3px 3px '><?= $txt_inici ?></a>
   -&nbsp;<a style=' color: #000066; border:1px dashed #CCCCCC; padding:3px 3px 3px 3px '><?= $txt_fi ?></a>)
  </h2>
  <br />

 <div class="right">
 
 <!--
 <h5>Dies lectius&nbsp; $diesLectius </h5>
 <h5>Percentage&nbsp; $percentatge &nbsp;%&nbsp;&nbsp;&nbsp;Max faltes permeses&nbsp; $maxIncidenciesPermeses</h5>
 -->
 
 <table>
    <tr>
    	<td>&nbsp;</td>
        <td><strong>PLA D'ESTUDIS</strong></td>
        <td><strong>ALUM</strong></td>
        <td><strong>FALTES</strong></td>
        <td><strong>RETARDS</strong></td>
        <td><strong>JUSTIF</strong></td>
        <td><strong>SEGUIM</strong></td>
    </tr>
    <?php
		$linea = 1;
		$rsPlaEstudis = getallPlaEstudis($db);
                foreach($rsPlaEstudis->fetchAll() as $row) {
		  // Nombre d'alumnes total que cursen el pla d'estudis
		  $total_al_pe = getTotalAlumnesPlaEstudis($db,$row["idplans_estudis"]);
		  
		  // Nombre d'alumnes amb mes absencies de les permeses pel percentatge estipulat
		  $total_absencies = 0;
		  $rsTotalAlumnes  = getIncidenciasPlaEstudis($db,$row["idplans_estudis"],TIPUS_FALTA_ALUMNE_ABSENCIA,$data_inici,$data_fi);
                  foreach($rsTotalAlumnes->fetchAll() as $row_al) {
				if ($row_al["total"] >= $maxIncidenciesPermeses) {
					$total_absencies++;
				}
		  }
		  
		  // Nombre d'alumnes amb mes retards de les permeses pel percentatge estipulat
		  $total_retards   = 0;
		  $rsTotalAlumnes  = getIncidenciasPlaEstudis($db,$row["idplans_estudis"],TIPUS_FALTA_ALUMNE_RETARD,$data_inici,$data_fi);
		  foreach($rsTotalAlumnes->fetchAll() as $row_al) {
				if ($row_al["total"] >= $maxIncidenciesPermeses) {
					$total_retards++;
				}
		  }
		  
		  // Nombre d'alumnes amb mes justificacions de les permeses pel percentatge estipulat
		  $total_justificades   = 0;
		  $rsTotalAlumnes  = getIncidenciasPlaEstudis($db,$row["idplans_estudis"],TIPUS_FALTA_ALUMNE_JUSTIFICADA,$data_inici,$data_fi);
		  foreach($rsTotalAlumnes->fetchAll() as $row_al) {
				if ($row_al["total"] >= $maxIncidenciesPermeses) {
					$total_justificades++;
				}
		  }
		  
		  // Nombre d'alumnes amb mes seguiments dels permesos pel percentatge estipulat
		  $total_seguiment   = 0;
		  $rsTotalAlumnes  = getIncidenciasPlaEstudis($db,$row["idplans_estudis"],TIPUS_FALTA_ALUMNE_SEGUIMENT,$data_inici,$data_fi);
		  foreach($rsTotalAlumnes->fetchAll() as $row_al) {
				if ($row_al["total"] >= $maxIncidenciesPermeses) {
					$total_seguiment++;
				}
		  }
			
		  echo "<tr>";
		  echo "<td valign='top' width='20' class='drop'>".$linea."</td>";
		  echo "<td valign='top' width='400' class='drop'>".$row["Nom_plan_estudis"]."</td>";
		  
		  echo "<td valign='top' width='90' class='drop'>";
		  if ($total_al_pe != 0) {
			  echo "<strong>".$total_al_pe."</strong>";
		  }
		  echo "</td>";
		  
		  echo "<td valign='top' width='90' class='drop'>";
		  if ($total_absencies != 0) {
			  echo "<strong>".$total_absencies."</strong>&nbsp;(".round(($total_absencies/$total_al_pe)*100,2).")%";
		  }
		  echo "</td>";
		  
		  echo "<td valign='top' width='90' class='drop'>";
		  if ($total_retards != 0) {
			  echo "<strong>".$total_retards."</strong>&nbsp;(".round(($total_retards/$total_al_pe)*100,2).")%";
		  }
		  echo "</td>";
		  
		  echo "<td valign='top' width='90' class='drop'>";
		  if ($total_justificades != 0) {
			  echo "<strong>".$total_justificades."</strong>&nbsp;(".round(($total_justificades/$total_al_pe)*100,2).")%";
		  }
		  echo "</td>";
		  
		  echo "<td valign='top' width='90' class='drop'>";
		  if ($total_seguiment != 0) {
			  echo "<strong>".$total_seguiment."</strong>&nbsp;(".round(($total_seguiment/$total_al_pe)*100,2).")%";
		  }
		  echo "</td>";	  
		  
		  $linea++;
		}
	?>
    <tr>
    	<td colspan="7"><strong>&nbsp;</strong></td>
    </tr>
    
 </table>
 <br />
 
        
 </div>
    
<?php
	if (isset($rsPlaEstudis)) {
    	//mysql_free_result($rsPlaEstudis);
	}
	if (isset($rsTotalAlumnes)) {
    	//mysql_free_result($rsTotalAlumnes);
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
		
		function doSearch(){  
			d_inici      = $('#data_inici').datebox('getValue');
			d_fi         = $('#data_fi').datebox('getValue');
			percentatge  = $('#percentatge').val();
			
			url = './inf_global/inf_global_see.php?data_inici='+d_inici+'&data_fi='+d_fi+'&percentatge='+percentatge+'&mode_impresio=1';
			
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
		
		function imprimirPDF(idgrup){  
			d_inici     = $('#data_inici').datebox('getValue');
			d_fi        = $('#data_fi').datebox('getValue');
			percentatge = $('#percentatge').val();
						
			url  = './inf_global/inf_global_print.php?data_inici='+d_inici+'&data_fi='+d_fi+'&percentatge='+percentatge+'&mode_impresio=1';
			
			$('#fitxer_pdf').attr('src', url);
		}
		
		function imprimirWord(idgrup){  
			d_inici     = $('#data_inici').datebox('getValue');
			d_fi        = $('#data_fi').datebox('getValue');
			percentatge = $('#percentatge').val();
			
			url  = './inf_global/inf_global_print_word.php?data_inici='+d_inici+'&data_fi='+d_fi+'&percentatge='+percentatge+'&mode_impresio=1';
			
			$('#fitxer_pdf').attr('src', url);
		}
		
		function imprimirExcel(idgrup){  
			d_inici     = $('#data_inici').datebox('getValue');
			d_fi        = $('#data_fi').datebox('getValue');
			percentatge = $('#percentatge').val();
			
			url  = './inf_global/inf_global_print_excel.php?data_inici='+d_inici+'&data_fi='+d_fi+'&percentatge='+percentatge+'&mode_impresio=1';
			
			$('#fitxer_pdf').attr('src', url);
		}
		
</script>

<script type="text/javascript">
	$('#header').css('visibility', 'hidden');
	$('#footer').css('visibility', 'hidden');
	
	/*$('#idplans_estudis').combobox({
		url:'./inf_global/pe_getdata.php',
		valueField:'idplans_estudis',
		textField:'Nom_plan_estudis'
	});*/
</script>

<?php
//mysql_close();
?>