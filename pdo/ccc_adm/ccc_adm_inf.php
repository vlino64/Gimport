<?php
  session_start();
  require_once('../bbdd/connect.php');
  require_once('../func/constants.php');
  require_once('../func/generic.php');
  require_once('../func/seguretat.php');
  $db->exec("set names utf8");
  
  $criteri       = isset($_REQUEST['criteri']) ? $_REQUEST['criteri'] : 'CAP';
  $valor_criteri = isset($_REQUEST['valor_criteri']) ? $_REQUEST['valor_criteri'] : 0;
  
  $data_inici = isset($_REQUEST['data_inici']) ? substr($_REQUEST['data_inici'],6,4)."-".substr($_REQUEST['data_inici'],3,2)."-".substr($_REQUEST['data_inici'],0,2) : '1989-1-1';
  if ($data_inici=='--') {
  	  $data_inici = getCursActual($db)["data_inici"];
  }
  $txt_inici  = isset($_REQUEST['data_inici']) ? $_REQUEST['data_inici'] : '';
  
  $data_fi    = isset($_REQUEST['data_fi'])    ? substr($_REQUEST['data_fi'],6,4)."-".substr($_REQUEST['data_fi'],3,2)."-".substr($_REQUEST['data_fi'],0,2)          : '2189-1-1';
  if ($data_fi=='--') {
  	  $data_fi = getCursActual($db)["data_fi"];
  }
  $txt_fi     = isset($_REQUEST['data_fi'])    ? $_REQUEST['data_fi'] : '';
  
  if ($criteri!='CAP') {
  	

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

	<style type='text/css'>
		.left{
			padding-left: 5px;
			float:left;
		}
		.left table{
			background:#E0ECFF;
		}
		.left td{
			background:#eee;
		}
		.right{
			
			
		}
		.right table{
			background:#E0ECFF;
			
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

<div style=''>
 
  <h5 style='margin-bottom:0px'>
  &nbsp;Informe detallat CCC &nbsp;
  <a style=' color: #000066; border:1px dashed #CCCCCC; padding:3px 3px 3px 3px '>
  <?php
  	switch ($criteri) {
		case "idgrup":
			echo (intval($valor_criteri!=0) ? getGrup($db,$valor_criteri)["nom"] : '');
			$rs = getCCCGrup($db,$valor_criteri,$data_inici,$data_fi);
			break;
		case "idalumne":
			echo getAlumne($db,$valor_criteri,TIPUS_nom_complet);
			$rs = getCCCAlumne($db,$valor_criteri,$data_inici,$data_fi);
			break;
		case "idprofessor":
			echo getProfessor($db,$valor_criteri,TIPUS_nom_complet);
			$rs = getCCCProfessor($db,$valor_criteri,$data_inici,$data_fi);
			break;
	}
  ?>
  </a>&nbsp;
  &nbsp;Desde el <a style=' color: #000066; border:1px dashed #CCCCCC; padding:3px 3px 3px 3px '><?= $txt_inici ?></a>
  &nbsp;&nbsp;fins al <a style=' color: #000066; border:1px dashed #CCCCCC; padding:3px 3px 3px 3px '><?= $txt_fi ?></a>
 </h5><br />
 <div class='left'>
	&nbsp;
 </div>
 <div class='right'>
        <table>
         	<tr>
                <td>&nbsp;</td>
                <td><strong>TIPUS CCC</strong></td>
                <td><strong>DATA</strong></td>
                <td><strong>EXPULSI&Oacute;</strong></td>
                <td><strong>ALUMNE</strong></td>
                <td><strong>PROFESSOR/A</strong></td>
                <td><strong>MAT&Egrave;RIA</strong></td>
                <td><strong>DESCRIPCI&Oacute;</strong></td>
            </tr>
            
                <?php
				    $linea = 1;
				    foreach($rs->fetchAll() as $row) {
                                          if ($row["id_motius"] != 0) {
                                              $literal_motius = getLiteralMotiusCCC($db,$row["id_motius"])["nom_motiu"];
                                          }
                                          else {
                                              $literal_motius = "";
                                          }
                                              
					  echo "<tr>";
					  echo "<td valign='top' width='20'>".$linea."</td>";
					  echo "<td valign='top' class='drop'>".getLiteralTipusCCC($db,$row["id_falta"])["nom_falta"]."</td>";
					  echo "<td valign='top' class='drop'>".substr($row["data"],8,2)."-".substr($row["data"],5,2)."-".substr($row["data"],0,4)."</td>";
					  echo "<td valign='top' class='drop'>".$row["expulsio"]."</td>";
					  echo "<td valign='top' class='drop'>".getAlumne($db,$row["idalumne"],TIPUS_nom_complet)."</td>";
					  echo "<td valign='top' class='drop'>".getProfessor($db,$row["idprofessor"],TIPUS_nom_complet)."</td>";
					  echo "<td valign='top' class='drop'>".(intval($row["idmateria"]!=0) ? getMateria($db,$row["idmateria"])["nom_materia"] : '')."</td>";
					  echo "<td valign='top' class='drop'><strong>Desc. breu</strong><br>".$literal_motius;
					  echo "<br><strong>Desc. detallada</strong><br>".nl2br($row["descripcio_detallada"])."</td>";
					  $linea++;
				   }
				?>          
		</table>
        <?php
			// Limpiamos pantalla
			/*for ($i=0;$i<50;$i++) {
				echo "<br>";
			}*/
		?>
 </div>

</div>

<script type="text/javascript">
	$('#header').css('visibility', 'hidden');
	$('#footer').css('visibility', 'hidden');
</script>

<?php
$rs->closeCursor();
}

//mysql_close();
?>