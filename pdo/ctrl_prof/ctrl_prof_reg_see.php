<?php
  session_start();
  require_once('../bbdd/connect.php');
  require_once('../func/constants.php');
  require_once('../func/generic.php');
  //require_once('../func/seguretat.php');
  $db->exec("set names utf8");
  
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
  
  if ( isset($_REQUEST['c_professor']) && ($_REQUEST['c_professor']==0) ) {
  	$c_professor = 0;
  }
  else if ( isset($_REQUEST['c_professor']) ) {
    $c_professor = $_REQUEST['c_professor'];
  }
  if (! isset($c_professor)) {
    $c_professor = 0;
  }
  
  $curs_escolar  = getCursActual($db)["idperiodes_escolars"];
  //$rsAlumnes       = getAlumnesMateriaGrup($db,$idgrups,$idmateria,TIPUS_nom_complet);  
  $mode_impresio = isset($_REQUEST['mode_impresio'])      ? $_REQUEST['mode_impresio']      : 0;
  if (! $mode_impresio) {
    require_once('../func/seguretat.php');
  }
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
			margin-left: 15px;
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
 
	
	<div class='right'>
            <h5>
            Desde el <a style='color:#000066; font-size:16px; border:1px dashed #CCCCCC; padding:3px 3px 3px 3px '><?= $txt_inici ?></a>
            &nbsp;fins al <a style='color:#000066; font-size:16px; border:1px dashed #CCCCCC; padding:3px 3px 3px 3px '><?= $txt_fi ?></a>
            </h5>
            
		<?php
		  $week         = 1;
		  $day_of_week  = date('w', strtotime("$data_inici"));
                  $day_end_of_week  = date('w', strtotime("$data_fi"));
                  $rest_of_week     = 7 - $day_of_week;
                  $rest_end_of_week = 7 - $day_end_of_week;
		  
                  $data_fi = date("Y-m-d", strtotime("$data_fi +$rest_end_of_week day"));
		  
		  $begin_date   = date("Y-m-d", strtotime("$data_inici"));
                  //$end_date   = date("Y-m-d", strtotime("$data_fi"));
                  
		  $end_date     = date("Y-m-d", strtotime("$begin_date +$rest_of_week day"));
		  
                  $startdate    = strtotime($begin_date);
		  $enddate      = strtotime($end_date);
		  
		  $count_arriba_tard = 0;
		  $count_marxa_aviat = 0;
                  
                  $infTotsProfessors = ($c_professor == 0) ? 1 : 0;
                  
                  if (! $infTotsProfessors) {
                      echo "<h5>Informe de ".getProfessor($db,$c_professor,TIPUS_nom_complet)."</h5>";
                  }
                  
		  echo "<table width='98%'>";
		  echo "<tr>";
		  echo "<td><strong>Setmana</strong></td>";
		  if ($infTotsProfessors) {
                    echo "<td width=300><strong>Professor</strong></td>";
                    echo "<td width=100><strong>Arriba tard</strong></td>";
		    echo "<td width=100><strong>Marxa aviat</strong></td>";
                    echo "<td width=100><strong>No ha vingut</strong></td>";
                  }
                  else {
                    echo "<td width=200><strong>Arriba tard</strong></td>";
		    echo "<td width=200><strong>Marxa aviat</strong></td>";
                    echo "<td width=100><strong>No ha vingut</strong></td>";
                  }
		  echo "</tr>";
		  
		  /*echo "<tr>";
		  echo "<td class='drop'>Setmana  <strong>".$week."</strong></td>";
		  echo "<td class='drop'>".substr($begin_date,8,2)."-".substr($begin_date,5,2)."-".substr($begin_date,0,4)."</td>";
		  echo "<td class='drop'>".substr($end_date,8,2)."-".substr($end_date,5,2)."-".substr($end_date,0,4)."</td>";
		  echo "<td class='drop'>&nbsp;</td>";
		  echo "</tr>";

		  $week++;
		  */
		  		  
		while ($end_date <= $data_fi) {
                    echo "<tr>";
                    echo "<td width=200 class='drop'><strong><font size=+2>".$week;
                    echo "  </font></strong>&nbsp;&nbsp;&nbsp;(".substr($begin_date,8,2)."-".substr($begin_date,5,2)."-".substr($begin_date,0,4)."&nbsp;&nbsp;&nbsp;";
                    echo substr($end_date,8,2)."-".substr($end_date,5,2)."-".substr($end_date,0,4).")";
                    echo "</td>";
		    echo "<td class='drop'>&nbsp;</td>";
		    echo "<td class='drop'>&nbsp;</td>";
                    echo "<td class='drop'>&nbsp;</td>";
                    if ($infTotsProfessors) {
                        echo "<td class='drop'>&nbsp;</td>";
                    }
		    echo "</tr>";
                    
                    $rsProfessors = ($infTotsProfessors) ? getProfessorsActius($db,TIPUS_nom_complet) : getRegistreProfessor($db,$c_professor,TIPUS_nom_complet);
		    foreach($rsProfessors->fetchAll() as $row) {
			$startdate          = strtotime($begin_date);
                        
                        $count_arriba_tard  = 0;
			$count_marxa_aviat  = 0;
                        $count_no_ha_vingut = 0;	
                                
                        $txt_arriba_tard  = "<ul style='list-style:none;margin-top:-20px;;margin-left:-20px;'>";
                        $txt_marxa_aviat  = "<ul style='list-style:none;margin-top:-20px;;margin-left:-20px;'>";
                        $txt_no_ha_vingut = "<ul style='list-style:none;margin-top:-20px;;margin-left:-20px;'>";
                                
			while($startdate <= $enddate) {
                             if (! festiu($db,date("Y-m-d", $startdate),$curs_escolar)) {                              
				$hora_entrada = getHoraEntradaProfessorDia($db,$row["idprofessors"],date("Y-m-d", $startdate),$curs_escolar);
				$hora_sortida = getHoraSortidaProfessorDia($db,$row["idprofessors"],date("Y-m-d", $startdate),$curs_escolar);

				$hora_registre_entrada = substr(getFirstLogProfessor($db,$row["idprofessors"],date("Y-m-d", $startdate),TIPUS_ACCIO_ENTROALCENTRE),0,5);
				$hora_registre_sortida = substr(getLastLogProfessor($db,$row["idprofessors"],date("Y-m-d", $startdate),TIPUS_ACCIO_SURTODELCENTRE),0,5);
					
				if (($hora_entrada != '00:00') && ($hora_registre_entrada != '00:00')) {
                                        if ($hora_entrada < $hora_registre_entrada) {
                                            $count_arriba_tard++;
                                            $txt_arriba_tard .= "<li><b>".date("d-m-Y", $startdate)."</b><br>(".$hora_entrada." Entrada  ".$hora_registre_entrada." Registre)</li>";
                                        }
				}
                               
                                if ($hora_registre_entrada == '00:00') {
                                        $count_no_ha_vingut++;
                                        $txt_no_ha_vingut .= "<li><b>".date("d-m-Y", $startdate)."</b></li>";
                                }
                                        
				if (($hora_sortida != '00:00') && ($hora_registre_sortida != '00:00')) {
                                        if ($hora_sortida > $hora_registre_sortida) {
                                            $count_marxa_aviat++;
                                            $txt_marxa_aviat .= "<li><b>".date("d-m-Y", $startdate)."</b><br>(".$hora_sortida." Sortida  ".$hora_registre_sortida." Registre)</li>";
                                        }
				}
                            }
                            $startdate += 86400;
			}
                        
			$txt_arriba_tard .= "</ul>";
                        $txt_marxa_aviat .= "</ul>";
                                
			if (($count_arriba_tard != 0) || ($count_marxa_aviat != 0) || ($count_no_ha_vingut != 0)) {
                                    echo "<tr>";
                                    echo "<td class='drop'>&nbsp;</td>";
                                    if ($infTotsProfessors) {
                                        echo "<td valign='top' class='drop'>".$row["Valor"]."</td>";
                                        if ($count_arriba_tard > 0) {
                                            echo "<td valign='top' class='drop'><font color=red><strong>".$count_arriba_tard."</strong></font></td>";
                                        }
                                        else {
                                            echo "<td valign='top' class='drop'>&nbsp;</td>";
                                        }
                                        if ($count_marxa_aviat > 0) {
					    echo "<td valign='top' class='drop'><font color=red><strong>".$count_marxa_aviat."</strong></font></td>";
                                        }
                                        else {
                                            echo "<td valign='top' class='drop'>&nbsp;</td>";
                                        }
                                        if ($count_no_ha_vingut > 0) {
                                            echo "<td valign='top' class='drop'><font color=red><strong>".$count_no_ha_vingut."</strong></font></td>";
                                        }
                                        else {
                                            echo "<td valign='top' class='drop'>&nbsp;</td>";
                                        }
                                    }
                                    else {
                                        if ($count_arriba_tard > 0) {
                                            echo "<td valign='top' class='drop'><font color=red><strong>".$count_arriba_tard."</strong></font>".$txt_arriba_tard."</td>";
                                        }
                                        else {
                                            echo "<td valign='top' class='drop'>&nbsp;</td>";
                                        }
                                        if ($count_marxa_aviat > 0) {
                                            echo "<td valign='top' class='drop'><font color=red><strong>".$count_marxa_aviat."</strong></font>".$txt_marxa_aviat."</td>";
                                        }
                                        else {
                                            echo "<td valign='top' class='drop'>&nbsp;</td>";
                                        }
                                        if ($count_no_ha_vingut > 0) {
                                            echo "<td valign='top' class='drop'><font color=red><strong>".$count_no_ha_vingut."</strong></font>".$txt_no_ha_vingut."</td>";
                                        }
                                        else {
                                            echo "<td valign='top' class='drop'>&nbsp;</td>";
                                        }
                                    }
                                    echo "</tr>";
			}

                    }
			
                    $begin_date =  date("Y-m-d", strtotime("$end_date +1 day"));
                    $end_date   =  date("Y-m-d", strtotime("$begin_date +6 day")); 

                    $startdate  = strtotime($begin_date);
                    $enddate    = strtotime($end_date);

                    $week++;
		}
		  
		echo "</table>";
		?>
        
	</div>
 
<?php
	if (isset($rsProfessors)) {
            //mysql_free_result($rsProfessors);
	}
?>

</div>

<script type="text/javascript">
	$('#header').css('visibility', 'hidden');
	$('#footer').css('visibility', 'hidden');
</script>

<?php
//mysql_close();
?>