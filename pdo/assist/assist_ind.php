<?php
  session_start();
  require_once('../bbdd/connect.php');
  require_once('../func/constants.php');
  require_once('../func/generic.php');
  require_once('../func/seguretat.php');
  $db->exec("set names utf8");
  
  $data_inici = isset($_REQUEST['data_inici']) ? substr($_REQUEST['data_inici'],6,4)."-".substr($_REQUEST['data_inici'],3,2)."-".substr($_REQUEST['data_inici'],0,2) : getCursActual($db)["data_inici"];
  if ($data_inici=='--') {
  	  $data_inici = getCursActual($db)["data_inici"];
  }
  $txt_inici  = substr($data_inici,8,2)."-".substr($data_inici,5,2)."-".substr($data_inici,0,4);
  
  $data_fi    = isset($_REQUEST['data_fi'])    ? substr($_REQUEST['data_fi'],6,4)."-".substr($_REQUEST['data_fi'],3,2)."-".substr($_REQUEST['data_fi'],0,2)          : date("Y-m-d");
  if ($data_fi=='--') {
  	  $data_fi = date("Y-m-d");
  }
  $txt_fi     = substr($data_fi,8,2)."-".substr($data_fi,5,2)."-".substr($data_fi,0,4);
  
  if ( isset($_REQUEST['idgrup']) && ($_REQUEST['idgrup']==0) ) {
  	$idgrup = 0;
  }
  else if ( isset($_REQUEST['idgrup']) ) {
    $idgrup = $_REQUEST['idgrup'];
  }
  if (! isset($idgrup)) {
    $idgrup = 0;
  }
  
  if ( isset($_REQUEST['idmateria']) && ($_REQUEST['idmateria']==0) ) {
  	$idmateria = 0;
  }
  else if ( isset($_REQUEST['idmateria']) ) {
    $idmateria = $_REQUEST['idmateria'];
  }
  if (! isset($idmateria)) {
    $idmateria = 0;
  }
  
  $grup_materia  = existGrupMateria($db,$idgrup,$idmateria);
  $curs_escolar  = getCursActual($db)["idperiodes_escolars"];
  $mode_impresio = isset($_REQUEST['mode_impresio'])      ? $_REQUEST['mode_impresio']      : 0;
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

<style type="text/css">
		.right_ind{
			width:720px;
			margin-left:15px;
			border:0px solid #0C0;
		}
		.right_ind table{
			background:#E0ECFF;
		}
		.right_ind td{
			background:#fafafa;
			text-align:left;
			padding:2px;
		}
		.right_ind td{
			background:#E0ECFF;
		}
		.right_ind td.drop{
			background:#fafafa;
		}
		.right_ind td.over{
			background:#FBEC88;
		}
		.item{
			text-align:center;
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
  
 <div id="resultDiv" style="width:780px; margin-top:5px;">
  
  <h2 style="margin-bottom:0px">
  &nbsp;Informe d'indicadors&nbsp;
  <a style=" color: #000066; border:0px dashed #CCCCCC; padding:2px 2px 2px 2px ">
  <?php 
  	if ($idgrup != 0) {
		echo "<br>&nbsp;Grup&nbsp;".getGrup($db,$idgrup)["nom"];
	}
  ?>&nbsp;
  <?php 
  	if ($idmateria != 0) {
	    if (isMateria($db,$idmateria)) {
			echo "<br>&nbsp;Mat&egrave;ria&nbsp;";
		}
		else {
			echo "<br>&nbsp;M&ograve;dul&nbsp;/UF&nbsp;";
		}
		echo getMateria($db,$idmateria)["nom_materia"];
	}
  ?>
  </a>
  <br>
  &nbsp;(<a style=' color: #000066; border:1px dashed #CCCCCC; padding:3px 3px 3px 3px '><?= $txt_inici ?></a>
  -&nbsp;<a style=' color: #000066; border:1px dashed #CCCCCC; padding:3px 3px 3px 3px '><?= $txt_fi ?></a>)
  </h2>

 <br />
 
 <div class="right_ind">
 
 <h5>
 Nombre d'alumnes del grup&nbsp;&nbsp;<a style="color:#000066; font-size:18px; font-weight:bold;"><?= getTotalAlumnesGrup($idgrup) ?></a>
 </h5>
  
 <h5>
 Dies lectius calculats&nbsp;&nbsp;<a style="color:#000066; font-size:18px; font-weight:bold;">
 <?=classes_entre_dates($db,$data_inici,$data_fi,$grup_materia,$curs_escolar)?></a>
 </h5>
 
 <h5>
 Dies lectius reals (a partir del seguiment real del professor)&nbsp;&nbsp;
 <a style="color:#000066; font-size:18px; font-weight:bold;">
 <?=getTotalSeguimientoGrupMateria($db,$data_inici,$data_fi,$idgrup,$idmateria,$curs_escolar)?></a>
 </h5>
 
 <h5>
 % alumnes que superen el percentatge &nbsp;&nbsp;
 <a style="color:#000066; font-size:18px; font-weight:bold;">
 <?=indicadors_profe($db,$data_inici,$data_fi,$idgrup,$idmateria,$curs_escolar)?></a>
 </h5>
 
 
 <br />
 <p>
 <i>NOTA: En el cas de UF's agafarà com a dates d'inici i fi respectivament les definides a la
 configuració del grup matèria corresponent.</i>
 </p>
      
 </div>
    
<?php
	if (isset($rsAlumnes)) {
    	//mysql_free_result($rsAlumnes);
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
		
		
		function imprimirInforme(idgrup){  
			d_inici  = $('#data_inici').datebox('getValue');
			d_fi     = $('#data_fi').datebox('getValue');
			idgrup   = $('#idgrup').combobox('getValue');
						
			url  = './inf_assist/inf_assist_grup_print.php?data_inici='+d_inici+'&data_fi='+d_fi+'&idgrup='+idgrup+'&mode_impresio=1';
			
			$('#fitxer_pdf').attr('src', url);
		}
		
</script>

<script type="text/javascript">
	$('#header').css('visibility', 'hidden');
	$('#footer').css('visibility', 'hidden');
</script>

<?php
//mysql_close();
?>