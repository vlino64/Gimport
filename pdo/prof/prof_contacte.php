<?php
  session_start();
  require_once('../bbdd/connect.php');
  require_once('../func/constants.php');
  require_once('../func/generic.php');
  require_once('../func/seguretat.php');
  $db->exec("set names utf8");
	
  $idprofessors = $_REQUEST['idprofessors'];
  
  $imgprof = "../images/prof/".$idprofessors.".jpg";

  if (file_exists($imgprof)) {
	$imgprof = "./images/prof/".$idprofessors.".jpg";
  }
  else {
	$imgprof = "./images/prof/prof.png";
  }
  
  echo "<table>";
  echo "<tr>";
  echo "<td width='70' valign='top'>";
  echo "<img src='".$imgprof."' style='width:51px;height:70px'>";
  echo "</td>";
  echo "<td>";
  echo "<form name='fm_fitxa' method='post'>";
  echo "<div class='fitem'>";
  echo "<label>Codi Professor:</label> ";
  echo "<input name='codi_professor' class='easyui-validatebox' size='55' value='".getCodiProfessor($db,$idprofessors)."'>";
  echo "</div>";
  echo "<hr>";
  
  $dadesprofessorArray  = array(TIPUS_nom_complet,TIPUS_iden_ref,TIPUS_login,
                                TIPUS_contrasenya,TIPUS_data_naixement,
				TIPUS_email,TIPUS_telefon,TIPUS_adreca,
				TIPUS_nom_municipi,TIPUS_codi_postal,
                                TIPUS_nom_profe,TIPUS_cognoms_profe);
							 
  $dadesocultesArray  = array(TIPUS_contrasenya,TIPUS_contrasenya2,TIPUS_login1,TIPUS_login2,
  			    TIPUS_contrasenya_notifica,TIPUS_contrasenya_notifica2,
                            TIPUS_cognom1_alumne,TIPUS_cognom2_alumne,TIPUS_nom_alumne,
                            TIPUS_cognom1_pare,TIPUS_cognom2_pare,TIPUS_nom_pare,
                            TIPUS_cognom1_mare,TIPUS_cognom2_mare,TIPUS_nom_mare,
                            TIPUS_email1,TIPUS_email2,TIPUS_mobil_sms,TIPUS_mobil_sms2);
							  
  $rsTipusContacte = getallTipusContacte($db);
  foreach($rsTipusContacte->fetchAll() as $row) {    
	  if (in_array($row['idtipus_contacte'], $dadesocultesArray)) {
		if ($row['idtipus_contacte']==TIPUS_contrasenya) {
                 $valor = "";
		 echo "<input type='hidden' name='elem".$row['idtipus_contacte']."' class='easyui-validatebox' size='65' value='".$valor."'>";
                }  
	  }
	  else { 
		if (in_array($row['idtipus_contacte'], $dadesprofessorArray)) {
		 echo "<div class='fitem' style='border-bottom:1px dashed #CCC; padding-bottom:3px; margin-bottom:3px; '>";
		 $valor = getValorTipusContacteProfessor($db,$idprofessors,$row['idtipus_contacte']);
                 if ($row['dada_contacte'] != "Email alumne")
                    {echo "<label><strong>".$row['dada_contacte']."</strong></label> ";}
                 else
                     {echo "<label><strong>Email professor</strong></label> ";}
                      echo "<input name='elem".$row['idtipus_contacte']."' class='easyui-validatebox' size='55' value='".$valor."'>";
		 
                      if ($row['idtipus_contacte']==TIPUS_data_naixement) {
			  echo "<br>&nbsp;Format: DD/MM/AAAA";
                      }
                      echo "</div>";
		}		  
	  }
	  
	  /*echo "<div class='fitem' style='border-bottom:1px dashed #CCC; padding-bottom:3px; margin-bottom:3px; '>";
      
      $valor = getValorTipusContacteProfessor($db,$idprofessors,$row['idtipus_contacte']);
      
	  if ($row['idtipus_contacte']==TIPUS_contrasenya_notifica) {
	  }
	  else if ($row['idtipus_contacte']==TIPUS_contrasenya_notifica2) {
	  }
	  
	  else if ($row['idtipus_contacte']==TIPUS_login2) {
	  }
	  
	  else if ($row['idtipus_contacte']==TIPUS_contrasenya2) {
	  }
	  
      else if ($row['idtipus_contacte']==TIPUS_contrasenya) {
          $valor = "";
		  echo "<input type='hidden' name='".$row['idtipus_contacte']."' class='easyui-validatebox' size='65' value='".$valor."'>";
      }
	  else {
	      echo "<label>".$row['dada_contacte'].":</label> ";
      	  echo "<input name='".$row['idtipus_contacte']."' class='easyui-validatebox' size='65' value='".$valor."'>";
	  }
      echo "</div>"; */
	  
  }
  echo "</form>";
  echo "</td>";
?>

<td valign="bottom">
<div style="padding:5px 0;text-align:right;padding-right:5px">
<a href="#" class="easyui-linkbutton" iconCls="icon-ok" plain="true" onclick="saveItem(<?php echo $_REQUEST['index'];?>)">Guardar</a>
<a href="#" class="easyui-linkbutton" iconCls="icon-cancel" plain="true" onclick="cancelItem(<?php echo $_REQUEST['index'];?>)">Cancel.lar</a>
</div>
</td></tr></table>