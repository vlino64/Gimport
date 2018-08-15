<?php
        //session_start();
        require_once('./bbdd/connect.php');
        require_once('./func/constants.php');
        require_once('./func/generic.php');
        require_once('./func/seguretat.php');

        $idprofessors = isset($_SESSION['professor'])    ? $_SESSION['professor']    : 0;
	$curs_escolar = getCursActual($db)["idperiodes_escolars"];
	
	$idgrups_ant            = 0;
	$idmateria_ant          = 0;
	$idfranges_horaries_ant = 0;
	$cp                     = 0;
	
        $hosting	  = 1; //0 per instal.lacions lliures
        $modul_ccc        = getModulsActius($db)["mod_ccc"];
	$modul_ass_servei = getModulsActius($db)["mod_ass_servei"];
	$modul_reg_prof   = getModulsActius($db)["mod_reg_prof"];
	$alumne_posa_ccc  = getModulsActius($db)["alumne_posa_ccc"];
                 
	if ($idprofessors==0 || $curs_escolar==0) {
		//exit;
	}	
?>
   
    <div class="easyui-accordion" style="width:220px; border-top:1px solid #aacae6;">
     
	<div title="Classes d'avui" style="overflow:auto;padding:1px; ">
            <?php  
            $periode = getCursActual($db)["idperiodes_escolars"];
            $data = date("Y/m/d");
            if (!festiu($db,$data,$periode))    
                {
                $rsFranges      = getFrangesHoraries($db);
		foreach($rsFranges->fetchAll() as $row) {
		  $aquesta_classe = 0;
		  $rsFrangesTorns = comprovarHoraDiaTorn($db,date('H:i'));
		  
                  foreach($rsFrangesTorns->fetchAll() as $row_torn) {					
                      if ($row['idfranges_horaries'] == $row_torn['idfranges_horaries']) {
			$aquesta_classe = 1;
                      }
		  }
                  
		  $rsGuardia = getGuardiaDiaHoraProfessor($db,date('w'),$row['idfranges_horaries'],$curs_escolar,$idprofessors);
                  foreach($rsGuardia->fetchAll() as $row_g) {

                        $start_date = new DateTime(date("Y/m/d")." ".$row['hora_inici'], new DateTimeZone('Pacific/Nauru'));                      
                        $end_date   = new DateTime(date("Y/m/d")." ".date("H:i:s"),new DateTimeZone('Pacific/Nauru'));
                        $interval   = $start_date->diff($end_date);
                        $hours      = $interval->format('%h'); 
                        $minutes    = $interval->format('%i');
                        $diff_date  = ($hours * 60 + $minutes);
                        //echo  'Diff. in minutes is: '.($hours * 60 + $minutes);                             
                      
		 	$fi_link = "</a>";
		 	if ($aquesta_classe) {
				$link    = "<a style='color:#333; font-weight:bold; text-decoration:none;' href='javascript:void(0)' onClick='open1(\"./guard/guard_grid.php?act=1&idprofessors=".$idprofessors.'&hora='.$row['hora_fi']."\",this)'>";
				echo $link."<div style='border:1px dashed #162b48; background:url(./images/fons_quadre_classe_actual.png); width:214px; margin-bottom:2px;'>".substr($row['hora_inici'],0,5)."-".substr($row['hora_fi'],0,5)."";
			}
			else {
                            if ($diff_date <= 30) {
                                $link    = "<a style='color:#333; font-weight:bold; text-decoration:none;' href='javascript:void(0)' onClick='open1(\"./guard/guard_grid.php?ad=1&act=1&idprofessors=".$idprofessors.'&hora='.$row['hora_inici']."\",this)'>";
                                echo $link."<div style='border:1px dashed #162b48; background:url(./images/fons_quadre_classe_actual.png); width:214px; margin-bottom:2px;'>".substr($row['hora_inici'],0,5)."-".substr($row['hora_fi'],0,5)."";
                            }
                            else {
                                $link    = "<a style='color:#333; font-weight:bold; text-decoration:none;' href='javascript:void(0)' onClick='open1(\"./guard/guard_grid.php?ad=1&act=1&idprofessors=".$idprofessors.'&hora='.$row['hora_inici']."\",this)'>";
                                echo $link."<div style='border:1px dashed #162b48; background:url(./images/fons_quadre_guardia.png); width:214px; margin-bottom:2px;'>".substr($row['hora_inici'],0,5)."-".substr($row['hora_fi'],0,5)."";
                            }
			}
		 	echo "<div>GU&Agrave;RDIA<br>".$row_g['espaicentre']."</div>".$fi_link."</div>";
		  }
				  
		  $rsMateries = getMateriesDiaHoraProfessor($db,date('w'),$row['idfranges_horaries'],$curs_escolar,$idprofessors);
                  foreach($rsMateries->fetchAll() as $rowm) {
                      $fi_link = "</a>";
                      $cp = 0;
                      $grup_materia = existGrupMateria($db,$rowm['idgrups'],$rowm['idmateria']);
                      //$pl  = existLogProfessorDataFranjaGrupMateria($db,$idprofessors,TIPUS_ACCIO_PASALLISTA,date("Y-m-d"),$row['idfranges_horaries'],$grup_materia);
                      
                      if ( ($idgrups_ant == $rowm['idgrups']) &&
			     ($idmateria_ant == $rowm['idmateria']) ) {
				$cp = $idfranges_horaries_ant;				
                      }
                        
                      if ($aquesta_classe) {
                          $_SESSION['grup_classe_actual']    = $rowm['idgrups'] ;
			  $_SESSION['materia_classe_actual'] = $rowm['idmateria'] ;
			  $_SESSION['fh_classe_actual']      = $row['idfranges_horaries'] ;
		  
			  $link    = "<a style='color:#333; font-weight:bold; text-decoration:none;' href='javascript:void(0)' onClick='open1(\"./assist/assist_grid.php?cp=".$cp."&act=1&idprofessors=".$idprofessors."&idgrups=".$rowm['idgrups']."&idmateria=".$rowm['idmateria']."&idfranges_horaries=".$row['idfranges_horaries']."&idespais_centre=".$rowm['idespais_centre']."\",this)'>";
			  echo "<div style='border:1px dashed #162b48; background:url(./images/fons_quadre_classe_actual.png); width:214px; margin-bottom:2px;'>";
			  echo "<div>$link".substr($row['hora_inici'],0,5)."-".substr($row['hora_fi'],0,5)."$fi_link</div>";
                          echo "<div>$link".$rowm['materia']."$fi_link</div>";
                          echo "<div>$link".$rowm['grup']."$fi_link</div>";
			  echo "<div>$link".$rowm['espaicentre']."$fi_link</div>";
			  echo "</div>";
                      }
		      else {
                          $link    = "<a style='color:#162b48; text-decoration:none;' href='javascript:void(0)' onClick='open1(\"./assist/assist_grid.php?cp=".$cp."&act=0&idprofessors=".$idprofessors."&idgrups=".$rowm['idgrups']."&idmateria=".$rowm['idmateria']."&idfranges_horaries=".$row['idfranges_horaries']."&idespais_centre=".$rowm['idespais_centre']."\",this)'>";
			  $linkwhite = "<a style='color:white; text-decoration:none;' href='javascript:void(0)' onClick='open1(\"./assist/assist_grid.php?cp=".$cp."&act=0&idprofessors=".$idprofessors."&idgrups=".$rowm['idgrups']."&idmateria=".$rowm['idmateria']."&idfranges_horaries=".$row['idfranges_horaries']."\",this)'>";
			  echo "<div style='background:url(./images/fons_quadre_classe.png); width:217px; margin-bottom:2px;'>";
			  echo "<div style=''>$linkwhite".substr($row['hora_inici'],0,5)."-".substr($row['hora_fi'],0,5)."$fi_link</div>";
                          echo "<div style=''>$link".$rowm['materia']."$fi_link</div>";
                          echo "<div style=''>$link".$rowm['grup']."$fi_link</div>";
			  echo "<div style=''>$link".$rowm['espaicentre']."$fi_link</div>";
			  echo "</div>";
                      }
                      
                      $idgrups_ant            = $rowm['idgrups'];
                      $idmateria_ant          = $rowm['idmateria'];
                      $idfranges_horaries_ant = $row['idfranges_horaries'];
                  }
		}
				
		if (isset($rsFranges)) {
                    //mysql_free_result($rsFranges);
		}
		if (isset($rsFrangesTorns)) {
                    //mysql_free_result($rsFrangesTorns);
		}
		if (isset($rsGuardia)) {
                    //mysql_free_result($rsGuardia);
		}
		if (isset($rsMateries)) {
                    //mysql_free_result($rsMateries);
		}
              }            
              ?>
                   
	</div>
        
        <div title="Les meves gestions" style="overflow:auto;padding:1px;">
          <ul style="list-style:none; padding-left:0px; padding-left:3px; text-align:left">
          <li style=""><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./assist_dant/assist_dant_grid.php',this)">Assist&egrave;ncia altres dies</a></li>
          <li style=""><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./alum_class/alum_class_grid.php?idprofessor=<?=$idprofessors?>',this)">Gesti&oacute; alumnes</a></li>
          <li style="padding-bottom:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./seg_class/seg_class_grid.php?idprofessor=<?=$idprofessors?>',this)">Seguiment classes</a></li>
          <li style="border-top:1px dashed #ccc; padding-top:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./mat_automat/mat_automat_grid.php',this)">Automatriculacions</a></li>
          <li style="padding-bottom:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./uf_class/uf_class_grid.php?idprofessor=<?=$idprofessors?>',this)">Establir dates UF</a></li>
          <li style="border-top:1px dashed #ccc; padding-top:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./inf_assist/inf_assist_prof_see.php?idprofessor=<?=$idprofessors?>',this)">Els meus informes</a></li>
          
          <?php 
          
                 
		 
         if (($hosting) AND ($modul_ccc))
            print('<li style=""><a style=\'color:#000033; text-decoration:none;\' href="javascript:void(0)" onclick="open1(\'./ccc/ccc_grid.php\',this)">Les meves CCC</a></li>');
         else
            print('<li style=""><a style=\'color:#6a6a6a; text-decoration:none;\' href="javascript:void(0)" >Les meves CCC(N/D)</a></li>');
 		 
	 if (($hosting) AND ($alumne_posa_ccc))
            print('<li style="padding-bottom:3px;"><a style=\'color:#000033; text-decoration:none;\' href="javascript:void(0)" onclick="open1(\'./ccc_alum/ccc_alum_grid.php\',this)">CCC per aprovar</a></li>');
         else
            print('<li style="padding-bottom:3px;"><a style=\'color:#6a6a6a; text-decoration:none;\' href="javascript:void(0)" >CCC per aprovar(N/D)</a></li>');
			
         if (($hosting) AND ($modul_ass_servei))
            print('<li style="border-top:1px dashed #ccc; padding-top:3px;"><a style=\'color:#000033; text-decoration:none;\' href="javascript:void(0)" onclick="open1(\'./abs_prof/com_abs_prof_grid.php\',this)">Comunicaci&oacute; abs&egrave;ncia</a></li>');
         else
            print('<li style="border-top:1px dashed #ccc; padding-top:3px;"><a style=\'color:#6a6a6a; text-decoration:none;\' href="javascript:void(0)">Comunicaci&oacute; abs&egrave;ncia.(N/D)</a></li>');
         
         if (($hosting) AND ($modul_ass_servei))
            print('<li style="padding-bottom:3px;"><a style=\'color:#000033; text-decoration:none;\' href="javascript:void(0)" onclick="open1(\'./abs_prof/abs_prof_grid.php\',this)">Abs&egrave;ncies comunicades</a></li>');
         else
            print('<li style="padding-bottom:3px;"><a style=\'color:#6a6a6a; text-decoration:none;\' href="javascript:void(0)">Abs&egrave;ncies comunicades.(N/D)</a></li>');
        
         if (($hosting) AND ($modul_ass_servei))
            print('<li style="border-top:1px dashed #ccc; padding-top:3px;"><a style=\'color:#000033; text-decoration:none;\' href="javascript:void(0)" onclick="open1(\'./sortides/plan_sortides_grid.php\',this)">Planificaci&oacute; sortida</a></li>');
         else
            print('<li style="border-top:1px dashed #ccc; padding-top:3px;"><a style=\'color:#6a6a6a; text-decoration:none;\' href="javascript:void(0)">Planificaci&oacute; sortida.(N/D)</a></li>');
         
         if (($hosting) AND ($modul_ass_servei))
	    print('<li><a style=\'color:#000033; text-decoration:none;\' href="javascript:void(0)" onclick="open1(\'./sortides/sortides_grid.php\',this)">Sortides enregistrades</a></li>');
         else
	    print('<li><a style=\'color:#6a6a6a; text-decoration:none;\' href="javascript:void(0)">Sortides enregistrades.(N/D)</a></li>');
         ?>
          
          </ul>
        </div>
                
        <?php
            $rsCarrecs = getCargosProfessor($db,$idprofessors);
            foreach($rsCarrecs->fetchAll() as $row) {
			   
			   if ($row['nom_carrec'] == 'SUPERADMINISTRADOR') {
			     $_SESSION['super'] = 1;
			   }
			   
			   if ($row['nom_carrec'] == 'ADMINISTRADOR') {
			     $_SESSION['admin'] = 1;
			   }
			   
			   if ( ($row['nom_carrec'] != 'SUPERADMINISTRADOR') && ($row['nom_carrec'] != 'ADMINISTRADOR') ) {
        ?>
		<div title="<strong><?= substr($row['nom_carrec'],0,3)."</strong>&nbsp;".$row['nom'] ?>" style="overflow:auto;padding:1px;">
			<ul title="<?= $row['nom_carrec'] ?>" class="easyui-tooltip" style="list-style:none; padding-left:1px; text-align:left;">
             <?php
			   if ($row['nom_carrec'] == 'COORDINADOR') {
			     echo "<li><a style='color:#000033; text-decoration:none;' href='javascript:void(0)' onclick='open1(\"./tutor/tutor_alum_grid.php?grup=".$row['idgrups']."\",this)'>Gesti&oacute; alumnes</a></li> ";
			     echo "<li><a style='color:#000033; text-decoration:none;' href='javascript:void(0)' onclick='open1(\"./tutor/tutor_grid.php?grup=".$row['idgrups']."\",this)'>Justificar faltas</a></li> ";
				 //cho "<li><a style='color:#000033; text-decoration:none;' href='javascript:void(0)' onclick='open1(\"./tutor/tutor_ccc_grid.php?grup=".$row['idgrups']."\",this)'>Gestionar CCC</a></li> ";
	
			   
			  if (($hosting) AND ($modul_ccc))
				echo "<li><a style='color:#000033; text-decoration:none;' href='javascript:void(0)' onclick='open1(\"./tutor_ccc/tutor_ccc_grid.php?grup=".$row['idgrups']."\",this)'>Gestionar CCC</a></li> ";
			  else
				echo "<li><a style='color:#6a6a6a; text-decoration:none;' href='javascript:void(0)'>Gestionar CCC</a></li> ";


			   }
			   if ($row['nom_carrec'] == 'TUTOR') {
			     echo "<li><a style='color:#000033; text-decoration:none;' href='javascript:void(0)' onclick='open1(\"./tutor/tutor_alum_grid.php?grup=".$row['idgrups']."\",this)'>Gesti&oacute; alumnes</a></li> ";
			     echo "<li><a style='color:#000033; text-decoration:none;' href='javascript:void(0)' onclick='open1(\"./tutor/tutor_grid.php?grup=".$row['idgrups']."\",this)'>Justificar faltes</a></li> ";
			     echo "<li><a style='color:#000033; text-decoration:none;' href='javascript:void(0)' onclick='open1(\"./tutor_msg/tutor_msg_grid.php?grup=".$row['idgrups']."\",this)'>Missatges families</a></li> ";
                             echo "<li><a style='color:#000033; text-decoration:none;' href='javascript:void(0)' onclick='open1(\"./tutor_sms/tutor_sms_grid.php?idgrup=".$row['idgrups']."\",this)'>SMS enviats</a></li> ";
 
			  if (($hosting) AND ($modul_ccc))
			     echo "<li><a style='color:#000033; text-decoration:none;' href='javascript:void(0)' onclick='open1(\"./tutor_ccc/tutor_ccc_grid.php?grup=".$row['idgrups']."\",this)'>Gestionar CCC</a></li> ";
			  else
			     echo "<li><a style='color:#6a6a6a; text-decoration:none;' href='javascript:void(0)'>Gestionar CCC</a></li> ";

			   }
			    if ($row['nom_carrec'] == 'RESPONSABLE DE FALTES') {
			     echo "<li><a style='color:#000033; text-decoration:none;' href='javascript:void(0)' onclick='open1(\"./conserge/conserge_grid.php\",this)'>Incid&egrave;ncies del dia</a></li> ";
			     echo "<li><a style='color:#000033; text-decoration:none;' href='javascript:void(0)' onclick='open1(\"./assist_adm/assist_adm_grid.php\",this)'>Passar faltes per grup</a></li> ";
			     echo "<li><a style='color:#000033; text-decoration:none;' href='javascript:void(0)' onclick='open1(\"./assist_adm_al/assist_adm_al_grid.php\",this)'>Passar faltes per alumne</a></li> ";
                             echo "<li><a style='color:#000033; text-decoration:none;' href='javascript:void(0)' onclick='open1(\"./sms_sent/sms_sent_grid.php\",this)'>SMS enviats</a></li> ";

                            if (($hosting) AND ($modul_ass_servei))
				print('<li style="border-top:1px dashed #ccc; padding-top:3px;"><a style=\'color:#000033; text-decoration:none;\' href="javascript:void(0)" onclick="open1(\'./guard/guard_grid_adm.php?act=0\',this)">Qui està a classe</a></li>');
                            else
				print('<li style="border-top:1px dashed #ccc; padding-top:3px;"><a style=\'color:#6a6a6a; text-decoration:none;\' href="javascript:void(0)" >Qui està a classe.(N/D)</a></li>');              
                            
                            echo "<li style='padding-bottom:3px;'><a style='color:#000033; text-decoration:none;' href='javascript:void(0)' onclick='open1(\"./alum/alum_grid.php\",this)'>Manteniment alumnes</a></li>";
            }
	    ?>
            </ul>
		</div>
        <?php
		      }
		    }		
		?>
        
        <?php
	   if (isset($_SESSION['super']) || isset($_SESSION['admin'])) {
	?>
        <div title="Tauler de control" style="overflow:auto;padding:1px;">
              <ul style="list-style:none; padding-left:0px; padding-left:3px; text-align:left">
              <li><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./conserge/conserge_grid.php',this)">Incid&egrave;ncies del dia</a></li>
              <li style=""><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./assist_adm/assist_adm_grid.php',this)">Passar faltes per grup</a></li>
              <li style=""><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./assist_adm_al/assist_adm_al_grid.php',this)">Passar faltes per alumne</a></li>
              <li style="border-top:1px dashed #ccc; padding-top:3px;padding-bottom:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./guard_sign/guard_sign_grid.php',this)">Gu&agrave;rdies signades</a></li>
              <li style="border-top:1px dashed #ccc; padding-top:3px;padding-bottom:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./subst/subst_grid.php',this)">Substitucions professorat</a></li>
              <li style="border-top:1px dashed #ccc; padding-top:3px;padding-bottom:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./sms_sent/sms_sent_grid.php',this)">SMS enviats</a></li>
              
	      <!--  <li style="border-top:1px dashed #ccc; padding-top:3px;padding-bottom:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./ccc_adm/ccc_adm_grid.php',this)">CCC introdu&iuml;des</a></li> -->
                        
	<?php 
			  
            if (($hosting) AND ($modul_ccc))
		print('<li style="border-top:1px dashed #ccc; padding-top:3px;padding-bottom:3px;"><a style=\'color:#000033; text-decoration:none;\' href="javascript:void(0)" onclick="open1(\'./ccc_adm/ccc_adm_grid.php\',this)">CCC introdu&iuml;des</a></li>');
	    else
		print('<li style="border-top:1px dashed #ccc; padding-top:3px;padding-bottom:3px;"><a style=\'color:#6a6a6a; text-decoration:none;\' href="javascript:void(0)">CCC introdu&iuml;des.(N/D)</a></li>');
			  
            if (($hosting) AND ($modul_ass_servei))
		print('<li style="border-top:1px dashed #ccc; padding-top:3px;"><a style=\'color:#000033; text-decoration:none;\' href="javascript:void(0)" onclick="open1(\'./abs_prof_adm/abs_prof_adm_grid.php\',this)">Professorat absent</a></li>');
	    else
		print('<li style="border-top:1px dashed #ccc; padding-top:3px;"><a style=\'color:#6a6a6a; text-decoration:none;\' href="javascript:void(0)" >Professorat absent.(N/D)</a></li>');
			  
            if (($hosting) AND ($modul_ass_servei))
		print('<li style="padding-bottom:3px;"><a style=\'color:#000033; text-decoration:none;\' href="javascript:void(0)" onclick="open1(\'./abs_prof_adm/com_abs_prof_adm_grid.php\',this)">Introdu&iuml;r professorat absent</a></li>');
	    else
		print('<li style="padding-bottom:3px;"><a style=\'color:#6a6a6a; text-decoration:none;\' href="javascript:void(0)" >Introdu&iuml;r professorat absent.(N/D)</a></li>');
			  
            if (($hosting) AND ($modul_ass_servei))
		print('<li style="border-top:1px dashed #ccc; padding-top:3px; padding-bottom:3px;"><a style=\'color:#000033; text-decoration:none;\' href="javascript:void(0)" onclick="open1(\'./sortides_adm/sortides_adm_grid.php\',this)">Sortides enregistrades</a></li>');
	    else
		print('<li style="border-top:1px dashed #ccc; padding-top:3px; padding-bottom:3px;"><a style=\'color:#6a6a6a; text-decoration:none;\' href="javascript:void(0)">Sortides enregistrades.(N/D)</a></li>');
			  
            if (($hosting) AND ($modul_ass_servei))
		print('<li style="border-top:1px dashed #ccc; padding-top:3px;"><a style=\'color:#000033; text-decoration:none;\' href="javascript:void(0)" onclick="open1(\'./ass_servei/quies_enlinia.php\',this)">Qui està en línia</a></li>');
	    else
		print('<li style="border-top:1px dashed #ccc; padding-top:3px;"><a style=\'color:#6a6a6a; text-decoration:none;\' href="javascript:void(0)" >Qui està en línia.(N/D)</a></li>');
            
            if (($hosting) AND ($modul_ass_servei))
		//print('<li style="border-top:1px dashed #ccc; padding-top:3px;"><a style=\'color:#000033; text-decoration:none;\' href="javascript:void(0)" onclick="open1(\'./guard/guard_grid_adm.php?rf=50&act=0\',this)">Qui està a classe</a></li>');
                print('<li style="border-top:1px dashed #ccc; padding-top:3px;"><a style=\'color:#000033; text-decoration:none;\' href="javascript:void(0)" onClick="open1(\'./guard/guard_grid.php?act=0\',this)">Qui està a classe</a></li>');
	    else                                                                                                                                            
		print('<li style="border-top:1px dashed #ccc; padding-top:3px;"><a style=\'color:#6a6a6a; text-decoration:none;\' href="javascript:void(0)" >Qui està a classe.(N/D)</a></li>');

            if (($hosting) AND ($modul_ass_servei))
		print('<li style="border-top:1px dashed #ccc; padding-top:3px;"><a style=\'color:#000033; text-decoration:none;\' href="javascript:void(0)" onclick="open1(\'./inf_passa_llista/inf_passa_llista_grid.php\',this)">Qui passa llista</a></li>');
	    else
		print('<li style="border-top:1px dashed #ccc; padding-top:3px;"><a style=\'color:#6a6a6a; text-decoration:none;\' href="javascript:void(0)" >Qui passa llista.(N/D)</a></li>');
                      
        ?>
                    
              
            </ul>
		</div>
        
        <?php
		}
		?>
        
        <?php
	  if (isset($_SESSION['super'])) {
	?>
        <div title="Configuracions generals" style="overflow:auto;padding:1px;">
            <ul style="list-style:none; padding-left:0px; padding-left:3px; text-align:left">
              <li><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./dades_centre/dades_centre_grid.php',this)">Dades centre</a></li>
              <li><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./ce/ce_grid.php',this)">Cursos escolars</a></li>
              <li><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./pe/pe_grid.php',this)">Plan estudis</a></li>
              <li><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./ec/ec_grid.php',this)">Espais de centre</a></li>
              <li><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./torn/torn_grid.php',this)">Torns</a></li>
              <li><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./fh/fh_grid.php',this)">Franges hor&agrave;ries</a></li>
              <li style="padding-bottom:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./textos_sms/textos_sms_grid.php',this)">Textos SMS</a></li>
              <li style="border-top:1px dashed #ccc; padding-top:3px;padding-bottom:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./incidents_tipus/incidents_tipus_grid.php',this)">Tipus seguiments</a></li>
              
              <!-- <li style="border-top:1px dashed #ccc; padding-top:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./ccc_tipus/ccc_tipus_grid.php',this)">CCC Tipus</a></li> -->
                                      
			  <?php 
			   if (($hosting) AND ($modul_ccc))
					print('<li style="border-top:1px dashed #ccc; padding-top:3px;"><a style=\'color:#000033; text-decoration:none;\' href="javascript:void(0)" onclick="open1(\'./ccc_tipus/ccc_tipus_grid.php\',this)">CCC Tipus</a></li>');
		      else
					print('<li style="border-top:1px dashed #ccc; padding-top:3px;"><a style=\'color:#6a6a6a; text-decoration:none;\' href="javascript:void(0)">CCC Tipus.(N/D)</a></li>');
			  
            if (($hosting) AND ($modul_ccc))
					print('<li style="padding-top:3px;"><a style=\'color:#000033; text-decoration:none;\' href="javascript:void(0)" onclick="open1(\'./ccc_motius/ccc_motius_grid.php\',this)">CCC Motius</a></li>');
		      else
					print('<li style="border-top:1px dashed #ccc; padding-top:3px;"><a style=\'color:#6a6a6a; text-decoration:none;\' href="javascript:void(0)">CCC Motius.(N/D)</a></li>');
			  
            if (($hosting) AND ($modul_ccc))
					print('<li><a style=\'color:#000033; text-decoration:none;\' href="javascript:void(0)" onclick="open1(\'./ccc_mesures/ccc_mesures_grid.php\',this)">CCC Mesures</a></li>');
		      else
					print('<li><a style=\'color:#6a6a6a; text-decoration:none;\' href="javascript:void(0)" >CCC Mesures.(N/D)</a></li>');
			  
            if (($hosting) AND ($modul_ccc))
					print('<li style="padding-bottom:3px;"><a style=\'color:#000033; text-decoration:none;\' href="javascript:void(0)" onclick="open1(\'./ccc_limits/ccc_limits_grid.php\',this)">CCC L&iacute;mits</a></li>');
		      else
					print('<li style="padding-bottom:3px;"><a style=\'color:#6a6a6a; text-decoration:none;\' href="javascript:void(0)" >CCC L&iacute;mits.(N/D)</a></li>');
			  ?>
              
              <li style="border-top:1px dashed #ccc; padding-top:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./func/ordena_alumnes.php',this)">Organitzar fitxers</a></li>
            </ul>
	</div>
        
        <div title="Mat&egrave;ries/M&ograve;duls/UF's" style="overflow:auto;padding:1px;">
            <ul style="list-style:none; padding-left:0px; padding-left:3px; text-align:left">              
              <li><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./mat/mat_grid.php',this)">Mat&egrave;ries</a></li>
              <li><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./mod/mod_grid.php',this)">M&ograve;duls professionals</a></li>
              <!--<li><a href="javascript:void(0)" onclick="open1('./ufs/ufs_grid.php',this)">Unitats Formatives</a></li>-->
            </ul>
	</div>
        
        <div title="Grups" style="overflow:auto;padding:1px;">
            <ul style="list-style:none; padding-left:0px; padding-left:3px; text-align:left">
              <li style="padding-bottom:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./grup/grup_grid.php',this)">Grups</a></li>
              <li style="border-top:1px dashed #ccc; padding-top:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./grma/grma_grid.php',this)">Mat&egrave;ries :: grups</a></li>
              <li style="padding-bottom:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./hor/hor_grid.php',this)">Horaris :: mat&egrave;ries :: grups</a></li> 
              
              <li style="border-top:1px dashed #ccc; padding-top:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./grmod/grmod_grid.php',this)">M&ograve;duls :: UF's :: grups</a></li>
                 
              <li><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./hormod/hormod_grid.php',this)">Horaris :: m&ograve;duls :: UF's :: grups</a></li>
            </ul>
	</div>
        
        <div title="Professors" style="overflow:auto;padding:1px;">
            <ul style="list-style:none; padding-left:0px; padding-left:3px; text-align:left">
              <li style="padding-bottom:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./prof/prof_grid.php',this)">Manteniment</a></li>
              <li style="border-top:1px dashed #ccc; padding-top:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./prmat/prmat_grid.php',this)">Mat&egrave;ries :: professors</a></li>
              <li style="padding-bottom:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./prmod/prmod_grid.php',this)">M&ograve;duls :: UF's :: professors</a></li>
              <li style="border-top:1px dashed #ccc; padding-top:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./prgua/prgua_grid.php',this)">Gu&agrave;rdies</a></li>
              
              <li style="padding-top:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./prdir/prdir_grid.php',this)">Direcci&oacute;</a></li>
              <li style="padding-top:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./prcoo/prcoo_grid.php',this)">Coordinacions</a></li>
              <li style="padding-top:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./prate/prate_grid.php',this)">Atencions</a></li>
              <li style="padding-top:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./prper/prper_grid.php',this)">Perman&egrave;ncies</a></li>
              <li style="padding-top:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./prreu/prreu_grid.php',this)">Reunions</a></li>
              <li style="border-bottom:1px dashed #ccc; padding-top:3px; padding-bottom:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./pralt/pralt_grid.php',this)">Altres</a></li>
              
              <li><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./prcar/prcar_grid.php',this)">C&agrave;rrecs</a></li>
            </ul>
	</div>
     
        <div title="Alumnes" style="overflow:auto;padding:1px;">
	    <ul style="list-style:none; padding-left:0px; padding-left:3px; text-align:left">
              <li style="padding-bottom:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./alum/alum_grid.php',this)">Manteniment</a></li>
              <li style="border-top:1px dashed #ccc; padding-top:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./algr/algr_grid.php',this)">Grups :: mat&egrave;ries :: alumnes</a></li>
              <li><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./almat/almat_grid.php',this)">Mat&egrave;ries :: alumnes</a></li>
              <li><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./almat_tree/almat_tree_grid.php',this)">Matriculacions :: alumnes</a></li>
            </ul>
	</div>

    <?php
	}
    ?>
        
    <?php
	  if (isset($_SESSION['super']) || isset($_SESSION['admin'])) {
    ?>      
        <div title="Informes" style="overflow:auto;padding:1px;">
            <ul style="list-style:none; padding-left:0px; padding-left:3px; text-align:left">
              <li style="padding-bottom:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./inf_assist/inf_assist_grup_see.php?idgrup=0',this)">Grups</a></li>
              <li style="border-top:1px dashed #ccc; padding-top:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./inf_assist/inf_assist_alum_see.php?idalumne=0',this)">Alumnes</a></li>
              <li style="border-top:1px dashed #ccc; padding-top:3px;"><a style='color:#000033; text-decoration:none;' href="javascript:void(0)" onclick="open1('./inf_global/inf_global_see.php?idalumne=0',this)">Dades Globals</a></li>
         
         <?php     
         if (($hosting) AND ($modul_reg_prof))     
            {
            print('<li style="border-top:1px dashed #ccc; padding-top:3px;"><a style=\'color:#000033; text-decoration:none;\' href="javascript:void(0)" onclick="open1(\'./ctrl_prof/ctrl_prof_grid_in.php?idalumne=0\',this)">Control Registre Professorat</a></li>');
           /* print('<li style="padding-top:3px;"><a style=\'color:#000033; text-decoration:none;\' href="javascript:void(0)" onclick="open1(\'./ctrl_prof/ctrl_prof_grid_out.php?idalumne=0\',this)">Professorat marxa aviat</a></li>');*/
            }
         else 
            {
            print('<li style="border-top:1px dashed #ccc; padding-top:3px;"><a style=\'color:#6a6a6a; text-decoration:none;\' href="javascript:void(0)" >Control Registre Professorat</a></li>');
          
		   /* print('<li style="padding-top:3px;"><a style=\'color:#6a6a6a; text-decoration:none;\' href="javascript:void(0)" >Professorat marxa aviat</a></li>');*/
            }
              
         ?>     
            </ul>
	</div>
    <?php
	}
    ?>  
        
    </div>
    
<div id="dlg_hor" class="easyui-dialog" style="width:1100px;height:650px;"  
            closed="true" collapsible="true" resizable="true" modal="true" toolbar="#dlg_hor-toolbar">  
        <!--<iframe width="890" height="680" scrolling="auto" src="./jcrop/demos/crop.php?idprofessors=5" style="border:0px;"></iframe>-->
</div>
    
<div id="dlg_hor-toolbar">  
    <table cellpadding="0" cellspacing="0" style="width:100%">  
        <tr>  
            <td>
                <a href="#" class="easyui-linkbutton" iconCls="icon-reload" plain="true" onclick="javascript:$('#dlg_hor').dialog('refresh')">Recarregar</a>
                <a href="#" class="easyui-linkbutton" iconCls="icon-print" plain="true" onclick="javascript:imprimirInforme()">Imprimir</a>  
                <a href="#" class="easyui-linkbutton" iconCls="icon-cancel" plain="true" onclick="javascript:$('#dlg_hor').dialog('close')">Tancar</a>  
            </td>
        </tr>  
    </table>  
</div>

<script type="text/javascript">  
        var url;	
		
	function verHorario(idprofessors){  
		url = './prmat/prmat_see.php?idprofessors='+idprofessors;
		$('#dlg_hor').dialog('open').dialog('setTitle','El teu horari');
		$('#dlg_hor').dialog('refresh', url);
        }
</script>
