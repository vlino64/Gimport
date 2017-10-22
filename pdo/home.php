<?php
	//force redirect to secure page..
        if($_SERVER['SERVER_PORT'] != '443') { 
		header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); 
		exit(); 
	}
	ini_set("session.cookie_lifetime","7200");
	ini_set("session.gc_maxlifetime","7200");
	session_start();
        require_once('./bbdd/connect.php');
        require_once('./func/constants.php');
        require_once('./func/generic.php');

	$db->exec("set names utf8");
        
	$url_horari  	  = "";
	$url_passwd   	  = "";
	$url_guardies 	  = "";
	$width_right_menu = 0;
	$dia 		  = date('w');
	
        $strNoCache       = "";
					
	if ( !isset($_SESSION['usuari']) ) {
		header('Location: index.php');
		$idprofessors         = 0;
		$curs_escolar         = '';
		$curs_escolar_literal = '';
	}
	else {
		$idprofessors         = isset($_SESSION['professor']) ? $_SESSION['professor'] : 0;
		$idalumnes            = isset($_SESSION['alumne'])    ? $_SESSION['alumne']    : 0;
		
		if (isset($_SESSION['familia_1'])) {
			$idfamilies = $_SESSION['familia_1'];
		}
		else if (isset($_SESSION['familia_2'])) {
			$idfamilies = $_SESSION['familia_2'];
		}
		
		$curs_escolar         = getCursActual($db)["idperiodes_escolars"];
		$curs_escolar_literal = getCursActual($db)["Nom"];
	}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tutoria|Assistencia|Faltes</title>
    <meta name="Description" content="Gestión de faltas de assistencia">
    <meta name="Keywords" content="Tutoria,assistencia,aplicatiu,aplicatiu de tutoria,gestion faltas de asistencia,gestion horarios,gestion guardias,asistencia alumnos">
    <meta name="robots" content="index, follow" />
    <!--<meta content="1140" http-equiv="REFRESH" />-->
    <link rel="shortcut icon" type="image/x-icon" href="./images/icons/favicon.ico" />
    <link rel="stylesheet" type="text/css" href="./css/main.css" />
    
	<?php 
		  if (isset($_SESSION['professor'])) {
				echo "<link rel='stylesheet' type='text/css' href='./css/default/easyui.css' />";
		  }
		  else if (isset($_SESSION['alumne'])){
				echo "<link rel='stylesheet' type='text/css' href='./css/default/easyui.css' />";
		  }
		  else if (isset($_SESSION['familia_1'])){
				echo "<link rel='stylesheet' type='text/css' href='./css/default/easyui.css' />";
		  }
		  else if (isset($_SESSION['familia_2'])){
				echo "<link rel='stylesheet' type='text/css' href='./css/default/easyui.css' />";
		  }
	 ?>
    
    <link rel="stylesheet" type="text/css" href="./css/icon.css" />  
    <link rel="stylesheet" type="text/css" href="./css/demo.css" /> 
    <link rel="stylesheet" type="text/css" href="./css/jquery.Jcrop.min.css" />
    <link rel="stylesheet" type="text/css" href="./css/easy-autocomplete.min.css">
    
    <script type="text/javascript" src="./js/jquery-1.8.0.min.js"></script>  
    <script type="text/javascript" src="./js/jquery.easyui.min.js"></script>
    
    <script type="text/javascript" src="./js/datagrid-detailview.js"></script>
    <script type="text/javascript" src="./js/jquery.edatagrid.js"></script>
    <script type="text/javascript" src="./js/jquery.Jcrop.min.js"></script>
    <script type="text/javascript" src="./js/script.js"></script>
    <script type="text/javascript" src="./js/jquery.easy-autocomplete.min.js"></script>

    <script type="text/javascript">
		var registre_entrada = 'NOT_INIT';
		
		function open1(url,a){                       
			currPageItem = $(a).text();
                       	$('body>div.menu-top').menu('destroy');
			$('body>div.window>div.window-body').window('destroy');
			$('#content').panel('refresh',url);
		}
		
		function registreEntrada(op,idprofessors) {
			var url   = '';
			var texte = '';
			var operacio;
			
			if (registre_entrada == 'NOT_INIT') {
				operacio = op;
			}
			else {
				operacio = registre_entrada;
			}


			if (operacio) {
				url              = './ctrl_prof/ctrl_prof_reg_in.php';
				texte            = 'Registre Sortida';
				img              = './images/icons/icon_logout.png';
				txt_confirm      = 'Registrem l\'entrada?';
				icono		 = 'icon-undo';
				
			}
			else {
				url              = './ctrl_prof/ctrl_prof_reg_out.php';
				texte            = 'Registre Entrada';
				img              = './images/icons/icon_login.png';
				txt_confirm      = 'Registrem la sortida?';
				icono		 ='icon-redo';
				
			}
			$.messager.confirm('Confirmar',txt_confirm,function(r){  
                    if (r){
						if (operacio) {
                                                    registre_entrada = 0;
						}
						else {
                                                    registre_entrada = 1;
						}
						
                        $.post(url,{idprofessors:idprofessors},function(result){  
                            if (result.success){ 
				$("#img_registre").attr("src", img);
				$('#img_registre').attr('title', texte);
                                /*$('#registre').linkbutton({				
                                    text: texte,
                                    iconCls: icono,
                                    plain: true,
				});*/
                            } else { 
				$.messager.alert('Error','Registre d\'entrada erroni!','error');
                            }  
                        },'json'); 
                    }  
            });
		}
		
		function sortir(idprofessors) {
			var url = './ctrl_prof/ctrl_prof_reg_out_home.php';
			
			if (idprofessors == 0) {
				location.href = './logout.php'; 
			}
			else {
				$.messager.confirm('Confirmar','VOLS SORTIR DEL SISTEMA?',function(r){  
				if (r){
					location.href = './logout.php';
				}
				});

				/*$.messager.confirm('Confirmar','SORTIR DEL SISTEMA: Vols registrar al mateix temps la sortida del centre?',function(r){  
				if (r){
					$.post(url,{idprofessors:idprofessors},function(result){  
								if (result.success){ 
									location.href = './logout.php';
								} 
								else {   
								}  
					},'json');                         
				}  
				else {
					location.href = './logout.php'; 
				}
				});*/

			}
       }

	   function sortirg(idprofessors) {
	   var url = './ctrl_prof/ctrl_prof_reg_out_home.php';
				
	   if (idprofessors == 0) {
		location.href = './logout.php?google=1'; 
		}
	   else {
		$.messager.confirm('Confirmar','VOLS SORTIR DE GASSIST I GOOGLE?',function(r){  
		if (r){
			 location.href = './logout.php?google=1';
		}
		});
		}
		   }

		
	</script>
    
</head>
<body class="easyui-layout">
	<div id="upper_menu" data-options="region:'north',border:false" style="height:50px;padding:1px;filter:alpha(opacity=80);-moz-opacity:.90;opacity:.90; overflow:hidden; no-repeat top left; z-index:100;">
        <table width="100%" height="45" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top" width="150">
            	<a href="home.php"><img src="images/left_ribbon.png" width="150" border="0"></a>
            </td>
            <td valign="top" width="45">
                <?php
		$img_prof_path = './images/prof/'.$idprofessors.'.jpg';
                if (file_exists($img_prof_path)) {
                	echo "<img src='".$img_prof_path."' width='45' height='45'>";
		}
		?>
            </td>
            <td valign="top">
				<?php 
				if (isset($_SESSION['professor'])) {
					$url_horari       = "./prmat/prmat_see.php?idprofessors=".$idprofessors;
                                        $hora_entra_centre = strtotime(getLastLogProfessor($db,$idprofessors,date("y-m-d"),TIPUS_ACCIO_ENTROALCENTRE));
                                        $hora_surt_centre  = strtotime(getLastLogProfessor($db,$idprofessors,date("y-m-d"),TIPUS_ACCIO_SURTODELCENTRE));					
					$url_passwd       = "./inici/user_update_passwd.php?id=".$idprofessors."&tipus=prof";
					$url_guardies     = "./prgua/prgua_see.php";
					$width_right_menu = 250;
					echo "Hola&nbsp;<strong>".getProfessor($db,$idprofessors,TIPUS_nom_complet)."</strong>";
				}
				else if (isset($_SESSION['alumne'])){
					$url_horari       = "./alum_automat/alum_automat_hor.php?idalumnes=".$idalumnes."&curs=".$curs_escolar."&cursliteral=".$curs_escolar_literal;
					$url_passwd       = "./inici/user_update_passwd.php?id=".$idalumnes."&tipus=alum";
					$url_seg = "./alum_automat/alum_automat_seg_grid.php?id=".$idalumnes;
					$url_informe      = "./alum_automat/alum_automat_see.php?c_alumne=".$idalumnes;
					$url_informe     .= "&box_dg=1&box_faltes=1&box_retards=1&box_justificacions=1&box_incidencies=1&box_CCC=1";
					$url_ccc = "./alum_automat/alum_automat_nova_ccc.php?idalumnes=".$idalumnes;				
					$width_right_menu = 440;
					echo "Hola&nbsp;<strong>".getAlumne($db,$idalumnes,TIPUS_nom_complet)."</strong>";
					
					$alumne_posa_ccc = getModulsActius($db)["alumne_posa_ccc"];
				}
				else if (isset($_SESSION['familia_1']) || isset($_SESSION['familia_2'])) {
					$url_horari       = "";
					$url_passwd       = "./inici/user_update_passwd.php?id=".$idfamilies."&tipus=families";
					$url_informe      = "./families/families_informe_see.php?c_alumne=".$idalumnes;
					$url_informe     .= "&box_dg=1&box_faltes=1&box_retards=1&box_justificacions=1&box_incidencies=1&box_CCC=1";
					$width_right_menu = 280;
					if (isset($_SESSION['familia_1'])) {
						$nom_tutor  = getFamilia($db,$idfamilies,TIPUS_nom_pare)." ";
						$nom_tutor .= getFamilia($db,$idfamilies,TIPUS_cognom1_pare)." ";
						$nom_tutor .= getFamilia($db,$idfamilies,TIPUS_cognom2_pare)." ";
					}
					else if (isset($_SESSION['familia_2'])) {
						$nom_tutor  = getFamilia($db,$idfamilies,TIPUS_nom_mare)." ";
						$nom_tutor .= getFamilia($db,$idfamilies,TIPUS_cognom1_mare)." ";
						$nom_tutor .= getFamilia($db,$idfamilies,TIPUS_cognom2_mare)." ";
					}
					echo "Hola&nbsp;<strong>".$nom_tutor."</strong>";
				}
				?>
                
                <br>
                <strong><?=getDiaSetmana($db,date('w'))?>,&nbsp;<?=date('d')?></strong>
                &nbsp;de&nbsp;<strong><?=getMes(date('m'))?>&nbsp;</strong>del&nbsp;<strong><?=date('Y')?>.</strong>
                <!--Son les <strong>date("H:i")." h"?>.&nbsp;</strong>-->
                Curs&nbsp;<strong><?= $curs_escolar_literal ?></strong>
            </td>
            <td  align="right">
            <?php 
			if (isset($_SESSION['alumne']) || isset($_SESSION['professor'])) {
				if (isset($_SESSION['professor'])) {
				  
				 /*if ($hora_entra_centre > $hora_surt_centre) {
					echo "<a id='registre' title='Registre sortida' href='javascript:void(0)' onClick='registreEntrada(0,".$idprofessors.")'><img id='img_registre' src='./images/icons/icon_logout.png' width='35' height='35' border='0'></a>";
				 }
				 else {
					echo "<a id='registre' title='Registre entrada' href='javascript:void(0)' onClick='registreEntrada(1,".$idprofessors.")'><img id='img_registre' src='./images/icons/icon_login.png' width='35' height='35' border='0'></a>";
				 }*/	 
		
            ?>
            &nbsp;
            <a href="javascript:void(0)" title="Quadre de gu&agrave;rdies general" class="easyui-tooltip" onClick="open1('<?=$url_guardies?>',this)">
            <img src="./images/icons/icon_guard.png" width="35" height="35" border="0"></a>
            
            <?php
			  }
			?>     
            <?php 
				if (isset($_SESSION['alumne'])) {
			?>
				<?php
                    if ($alumne_posa_ccc) {
                ?>
                     <a style="position:relative; top:-7px;" href="#" class="easyui-linkbutton" iconCls="icon-add" onclick="open1('<?=$url_ccc?>',this)">Nova CCC</a>
                <?php
                    }
                ?>
            	<a href="javascript:void(0)" title="Informe assist&egrave;ncia" class="easyui-tooltip" onClick="open1('<?=$url_informe?>',this)">
                <img src="./images/icons/icon_report.png" width="35" height="35" border="0" /></a>
                <a href="javascript:void(0)" title="Seguiment classes" class="easyui-tooltip" onclick="open1('<?=$url_seg?>',this)">
                <img src="./images/icons/icon_class.png"width="35" height="35" border="0" /></a>
                
            <?php
            }
            ?>
            
            <a href="javascript:void(0)" title="El meu horari" class="easyui-tooltip" onClick="open1('<?=$url_horari?>',this)">
            <img src="./images/icons/icon_timetable.png" width="35" height="35" border="0"></a>
            
            <?php
			}
			?>
                
            <a href="javascript:void(0)" title="Canviar contrasenya" class="easyui-tooltip" onClick="open1('<?=$url_passwd?>',this)">
            <img src="./images/icons/icon_key.png" width="35" height="35" border="0"></a><br><br><br><br>
            </td>
            
		   <?php
		   $modul_login_google = getModulsActius($db)["mod_login_google"];
		   if (isset($_SESSION['access_token']) && $_SESSION['access_token'] && $modul_login_google) 
			{
		   ?>	
			<td valign="top" align="right" width="40">
                        <a href="javascript:void(0)" title="Sortir de Gassist" onClick="sortir(<?=$idprofessors?>)">
			<img src="./images/icons/icon_exit_red.png" width="35" height="35" border="0"></a> 
			</td>
			<td valign="top" align="right" width="40">   
			<a href="javascript:void(0)" title="Sortir de Gassist i Google" onClick="sortirg(<?=$idprofessors?>)" class="easyui-tooltip">
			<img src="./images/icons/icon_exit_red_G.png" width="35" height="35" border="0"></a> 
			</td>
			<?php
			}
		   else
			{
			?>			
			<td valign="top" align="right" width="40">                      
			<a href="javascript:void(0)" title="Sortir del sistema" onClick="sortir(<?=$idprofessors?>)" class="easyui-tooltip">
			<img src="./images/icons/icon_exit_red.png" width="35" height="35" border="0"></a>
			</td>				
			<?php	} ?>
            <td valign="top" align="right">
            	<?php
		$img_logo = './images/logo.jpg';
                if (file_exists($img_logo)) {
                    echo "<img src='".$img_logo.$strNoCache."' height='45'>";
		}
		?>
            </td>
          </tr>
        </table>  
    </div>
    
    
    <div data-options="region:'east',split:true,collapsed:false,title:'&nbsp;&nbsp;'" style="width:<?=$width_right_menu?>px;padding:2px;">
    	<?php 
		  if (isset($_SESSION['professor'])) {
			include_once('./inici/menu_right_prof.php');
		  }
		  else if (isset($_SESSION['alumne'])){
			include_once('./inici/menu_right_alum.php');
		  }
		  else if (isset($_SESSION['familia_1']) || isset($_SESSION['familia_2'])){
			include_once('./inici/menu_right_families.php');
		  }
	    ?>
    </div>
    
	<div data-options="region:'south',border:false" style="height:20px;;filter:alpha(opacity=70);-moz-opacity:.70;opacity:.70;">
    </div>
    
	<div id="content" data-options="region:'center',border:false" style="overflow: auto">
    	<?php
                  if (isset($_SESSION['professor'])) {
			$exist_guardia   = 0;
			$rsFrangesTorns  = comprovarHoraDiaTorn($db,date('H:i'));
                        foreach($rsFrangesTorns->fetchAll() as $row_torn) {
                            if (existsGuardiaDiaHoraProfessor($db,$dia,$row_torn['idfranges_horaries'],$curs_escolar,$idprofessors)) {
                                $exist_guardia = 1;
                            }
		        }
		  	if ($exist_guardia) {
                                //echo "Hola, tens una guàrdia!!";
				require('./guard/guard_grid_inici.php');
			}
			else {
				require_once('./assist/assist_grid.php');
			}
		  }
		  
		  else if (isset($_SESSION['alumne'])){
				require_once('./alum_automat/alum_automat_grid.php');
		  }
		  else if (isset($_SESSION['familia_1'])){
				require_once('./families/families_grid.php');
		  }
		  else if (isset($_SESSION['familia_2'])){
				require_once('./families/families_grid.php');
		  }
		  
		  if (isset($rsFrangesTorns)) {
			  //mysql_free_result($rsFrangesTorns);
		  }
	    ?>
    </div>
</body>
</html>