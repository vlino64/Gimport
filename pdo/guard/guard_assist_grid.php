<?php
	session_start();
        require_once('../bbdd/connect.php');
        require_once('../func/constants.php');
        require_once('../func/generic.php');
        require_once('../func/seguretat.php');
	$idmateria          = isset($_REQUEST['idmateria'])          ? $_REQUEST['idmateria']          : 0 ;
	$idgrups            = isset($_REQUEST['idgrups'])            ? $_REQUEST['idgrups']            : 0 ;
	$idprofessors       = isset($_REQUEST['idprofessors'])       ? $_REQUEST['idprofessors']       : 0 ;
	$idfranges_horaries = isset($_REQUEST['idfranges_horaries']) ? $_REQUEST['idfranges_horaries'] : 0 ;
	$idespais_centre    = isset($_REQUEST['idespais_centre'])    ? $_REQUEST['idespais_centre']    : 0 ;
	
	$professor_guardia  = isset($_SESSION['professor'])          ? $_SESSION['professor']          : 0 ;
	$data               = date("Y-m-d");
	
	$db->exec("set names utf8");
	$nom_grup     = getGrup($db,$idgrups)["nom"];
	
        $strNoCache = "";
?>
    
<table width="99%" cellpadding="0" cellspacing="0">
<tr>
<td>
    <h4>&nbsp;&nbsp;Franja hor&agrave;ria <?=getLiteralFranjaHoraria($db,$idfranges_horaries)?></h4>
    <h5>&nbsp;&nbsp;Alumnes de <?=getMateria($db,$idmateria)["nom_materia"]?> de <?=$nom_grup?></h5>
</td>
<td align="right">

<?php
 if (! existsGuardiaSignada($db,$professor_guardia,$idfranges_horaries,$data,$idmateria,$idgrups)) {
?>
<a id="sign_button" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-edit',plain:true" onclick="signarGuardia(<?=$idprofessors?>,<?=$idgrups?>,<?=$idmateria?>,<?=$idfranges_horaries?>)">Signar la gu&agrave;rdia</a>
&nbsp;
<?php
 }
 else {
?>
<a style="background-color:#a1d88b; color:#666; font-weight:bold" id="sign_button" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-edit',plain:true,disabled:true" onclick="signarGuardia(<?=$idprofessors?>,<?=$idgrups?>,<?=$idmateria?>,<?=$idfranges_horaries?>)">Gu&agrave;rdia ja signada</a>
&nbsp;
<?php 
 }
?>

<?php
 if (getDadesCentre($db)['prof_env_sms']) {
?>
<a id="sms_button" href="javascript:void(0)" class="easyui-linkbutton" data-options="plain:true" onclick="enviarSMS(<?=$idgrups?>)">
<img src="./images/envelope.png" height="16" align="absbottom" />&nbsp;Enviar SMS</a>
&nbsp;
<?php
 }
?>

<?php
 if (exitsIncidenciapProfessor($db,$idprofessors,$data,$idfranges_horaries)) {
?>
<a style="background-color:#a1d88b;" id="task_button" href="javascript:void(0)" class="easyui-linkbutton" data-options="plain:true" onclick="comprovarFeina(<?=$idprofessors?>,<?=$idfranges_horaries?>)">
<img src="./images/task_view.png" height="16" align="absbottom" />&nbsp;<strong>Consultar la feina desada</strong></a>
<?php
 }
 else {
?>
<a style="background-color:#fd564d;" id="task_button" href="javascript:void(0)" class="easyui-linkbutton" data-options="plain:true" onclick="comprovarFeina(<?=$idprofessors?>,<?=$idfranges_horaries?>)">
<img src="./images/task_view.png" height="16" align="absbottom" />&nbsp;<strong>No hi ha feina desada</strong></a>
<?php 
 }
?>

&nbsp;<br />       
<a href="javascript:void(0)" class="easyui-linkbutton" plain="true" onclick="informeAssistencia(<?=$idgrups?>,<?=$idmateria?>,'<?=$nom_grup?>')">
<img src="./images/admissions_icon.png" height="16" align="absbottom" />&nbsp;Assist&egrave;ncia</a>
&nbsp;
<a id="horari_button" href="javascript:void(0)" class="easyui-linkbutton" data-options="plain:true" onclick="verHorario(<?=$idgrups?>)"><img src="./images/schedule_icon.png" height="16" align="absbottom" />&nbsp;Horari</a>&nbsp;&nbsp;&nbsp;
</td>
</tr>
</table>

     
    <div id="GrupsScreen" class="grups">  
        <ul>  
            <?php
			   $rsAlumnes = getAlumnesMateriaGrup($db,$idgrups,$idmateria,TIPUS_nom_complet);
			   foreach($rsAlumnes->fetchAll() as $row) {
			     				 
				 if (exitsIncidenciaAlumne($db,$row['idalumnes'],date("Y-m-d"),$idfranges_horaries,$idmateria,$idgrups)) {
				 	$id_tipus_incidencia = getIncidenciaAlumne($db,$row['idalumnes'],date("Y-m-d"),$idfranges_horaries)["id_tipus_incidencia"];
					$idincidencia_alumne = getIncidenciaAlumne($db,$row['idalumnes'],date("Y-m-d"),$idfranges_horaries)["idincidencia_alumne"];
				 	$comentari           = getIncidencia($db,$idincidencia_alumne)["comentari"];
				 }
				 else {
					$id_tipus_incidencia = 0; 
				 }
				 				 
				 // Cas de que un alumne estigui amb una sortida
				 if (existAlumneSortidaData($db,$row['idalumnes'],date("y-m-d"),getFranjaHoraria($db,$idfranges_horaries)["hora_inici"]) != 0) {
					$id_tipus_incidencia = TIPUS_FALTA_ALUMNE_JUSTIFICADA;
				 }
				 // Cas de que un alumne estigui amb una CCC
				 if (existAlumneCCCData($db,$row['idalumnes'],date("y-m-d"),$idfranges_horaries) != 0) {
					$id_tipus_incidencia = TIPUS_FALTA_ALUMNE_CCC;
				 }
				 
				 if ($id_tipus_incidencia==TIPUS_FALTA_ALUMNE_ABSENCIA) {
				 	echo "<div id='al".$row['idalumnes']."' class='itemgrupsfalta'>";
				 }
				 else if ($id_tipus_incidencia==TIPUS_FALTA_ALUMNE_RETARD) {
				 	echo "<div id='al".$row['idalumnes']."' class='itemgrupsretard'>";
				 }
				 else if ($id_tipus_incidencia==TIPUS_FALTA_ALUMNE_SEGUIMENT) {
				 	echo "<div id='al".$row['idalumnes']."' class='itemgrupsincidencia'>";
				 }
				 else if ($id_tipus_incidencia==TIPUS_FALTA_ALUMNE_JUSTIFICADA) {
				 	echo "<div id='al".$row['idalumnes']."' class='itemgrupsjustificada'>";
				 }
				 else if ($id_tipus_incidencia==TIPUS_FALTA_ALUMNE_CCC) {
				 	echo "<div id='al".$row['idalumnes']."' class='itemccc'>";
				 }
				 else {
				 	echo "<div id='al".$row['idalumnes']."' class='itemgrups'>";
				 }
				 
				 echo "<li>";
				 echo substr(getAlumne($db,$row['idalumnes'],TIPUS_cognom1_alumne)." ".getAlumne($db,$row['idalumnes'],TIPUS_cognom2_alumne),0,20)."<br> ";
				 echo substr(getAlumne($db,$row['idalumnes'],TIPUS_nom_alumne),0,23);
				 echo "<table cellspacing=0 cellpadding=0 border=0><tr>";
				 
				 if (isset($_REQUEST['idmateria'])) {
				 	$img_alumne = "../images/alumnes/".$row['idalumnes'].".jpg";
				 }
				 else {
				 	$img_alumne = "./images/alumnes/".$row['idalumnes'].".jpg";
				 }
				 
				 if (file_exists($img_alumne)) {
				   echo "<td width=51><img src='./images/alumnes/".$row['idalumnes'].".jpg".$strNoCache."' width='51' height='70' align='absbottom'></td>";
				 }
				 else {
				   echo "<td width=51><img src='./images/alumnes/alumne.png' width='51' height='70'></td>";
				 }
				 
				 echo "<td valign='top'>";
				 if ($id_tipus_incidencia==TIPUS_FALTA_ALUMNE_JUSTIFICADA) {
				 	if (isset($comentari)) {
						echo substr($comentari,0,90);
					}
				 }
				 else {
				 	 $rsTipusFaltes = getTipusFaltaAlumne($db);
                                         foreach($rsTipusFaltes->fetchAll() as $rowf) {
					   if ($rowf['idtipus_falta_alumne']==TIPUS_FALTA_ALUMNE_ABSENCIA) {
						 echo "<a id='".$row['idalumnes']."' href='javascript:void(0)' class='easyui-linkbutton' data-options='plain:true,toggle:true' onclick='addFalta(this.id)'>".$rowf['tipus_falta']."</a><br>";
					   }
					   else if ($rowf['idtipus_falta_alumne']==TIPUS_FALTA_ALUMNE_RETARD) {
						 echo "<a id='".$row['idalumnes']."' href='javascript:void(0)' class='easyui-linkbutton' data-options='plain:true,toggle:true' onclick='addRetard(this.id)'>".$rowf['tipus_falta']."</a><br>";
					   }
					   else if ($rowf['idtipus_falta_alumne']==TIPUS_FALTA_ALUMNE_SEGUIMENT) {
						 echo "<a id='".$row['idalumnes']."' href='javascript:void(0)' class='easyui-linkbutton' data-options='plain:true,toggle:true' onclick='addIncident(this.id)'>".$rowf['tipus_falta']."</a><br>";
					   }
					   else if ($rowf['idtipus_falta_alumne']==TIPUS_FALTA_ALUMNE_CCC) {
						 echo "<a id='".$row['idalumnes']."' href='javascript:void(0)' class='easyui-linkbutton' data-options='plain:true,toggle:true' onclick='addCCC(this.id)'>".$rowf['tipus_falta']."</a><br>";
					   }
					   
					 }
					 echo "<a id='".$row['idalumnes']."' href='javascript:void(0)' class='easyui-linkbutton' data-options='plain:true,toggle:true' onclick='Cancelar(this.id)'>Cancel.lar</a>";
				 }
				 echo "</td></tr></table>";
                 echo "</li></div>";
			   }
			
			?>   
        </ul> 
        <div class="clear"></div> 
        <br /><br /><br /><br />
    </div>
    
<div id="dlg_incidencia" class="easyui-dialog" style="width:700px;height:420px;padding:10px 20px"  
            closed="true" collapsible="true" resizable="true" modal="true" buttons="#dlg_incidencia-buttons">  
        <form id="fm_incidencia" method="post" novalidate>             	
                <label>Tipus</label>
                <select id="id_tipus_incident" name="id_tipus_incident" class="easyui-combogrid" style="width:600px" data-options="
                    required: false,
                    panelWidth: 600,
                    idField: 'idtipus_incident',
                    textField: 'tipus_incident',
                    url: url,
                    method: 'get',
                    columns: [[
                        {field:'tipus_incident',title:'Motiu',width:600}
                    ]],
                    fitColumns: true
                ">
                </select>
                <br /><br />
                <label>Descripci&oacute;</label><br />
                <textarea name="comentari" style="height:250px; width:650px;"></textarea>
        </form>  
</div>  

<div id="dlg_incidencia-buttons">  
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="saveIncident()">Guardar</a>  
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-redo" onclick="cancelIncident()">Cancel.lar</a>
</div>

<div id="dlg_ccc" class="easyui-dialog" style="width:740px;height:500px;padding:5px 5px"  
            closed="true" collapsible="true" resizable="true" modal="true" buttons="#dlg-buttons_ccc">  
        <form id="fm_ccc" method="post" novalidate>             	
            <input type="hidden" name="id_tipus_sancio" value="0" />
            <input type="hidden" name="data_inici_sancio" value="" />
            <input type="hidden" name="data_fi_sancio" value="" />
            <div class="fitem">  
            	<label style="font-size:16px; font-weight:bolder">Implica expulsi&oacute; de classe?</label>
            	<input id="expulsio" name="expulsio" type="checkbox" value="S">
                <label style="color:#666666">(marcar aquesta opci&oacute; si es treu l'alumne de classe)</label>
            	<br /><br />
                
                <label style="color:#666666">Tipus d'incidència</label>
                <select id="id_falta" name="id_falta" class="easyui-combobox" data-options="
                    required:true,
                    width:250,
                    url:'./ccc_tipus/ccc_tipus_getdata.php',
                    idField:'idccc_tipus',
                    valueField:'idccc_tipus',
                    textField:'nom_falta',
                    panelHeight:'auto'
                ">
                </select>
                
                <br /><br />
                <label style="color:#666666">Descripció breu</label><br />
                <select id="id_motius" name="id_motius" class="easyui-combogrid" style="width:700px" data-options="
                    required: false,
                    panelWidth: 700,
                    idField: 'idccc_motius',
                    textField: 'nom_motiu',
                    url: url,
                    method: 'get',
                    columns: [[
                        {field:'nom_motiu',title:'Motiu',width:700}
                    ]],
                    fitColumns: true
                ">
                </select>
                <br /><br />
                <label style="color:#666666">Fets que s'han produït</label><br />
                <textarea name="descripcio_detallada" style="height:250px; width:700px;"></textarea>
            </div>
        </form>  
    </div>  
    <div id="dlg-buttons_ccc">  
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="saveCCC()">Guardar</a>  
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="cancelCCC()">Cancel.lar</a>
    </div>
</div>
      
<div id="dlg_inf" class="easyui-dialog" style="width:900px;height:600px;"  
            closed="true" maximized="true" maximizable="true" collapsible="true" resizable="true" modal="true" buttons="#dlg_inf-toolbar">
</div>
    
<div id="dlg_inf-toolbar">
    <table cellpadding="0" cellspacing="0" style="width:100%">  
        <tr>  
            <td>
                <a href="#" class="easyui-linkbutton" iconCls="icon-redo" plain="true" onclick="javascript:$('#dlg_inf').dialog('close')">Tancar</a>  
            </td>
        </tr>  
    </table>  
</div>

<div id="dlg_hor" class="easyui-dialog" style="width:900px;height:600px;"  
            closed="true" maximized="true" maximizable="true" collapsible="true" resizable="true" modal="true">            
</div>

<!--
<div id="dlg_hor-toolbar">  
    <table cellpadding="0" cellspacing="0" style="width:100%">  
        <tr>  
            <td>
                
               <a href="#" class="easyui-linkbutton" iconCls="icon-reload" plain="true" onclick="javascript:$('#dlg_hor').dialog('refresh')">Recarregar</a>
               
                <a href="#" class="easyui-linkbutton" iconCls="icon-redo" plain="true" onclick="javascript:$('#dlg_hor').dialog('close')">Tancar</a>  
            </td>
        </tr>  
    </table>  
</div>
-->
    
<div id="dlg_task" class="easyui-dialog" style="width:525px;height:250px;"  
            closed="true" collapsible="true" resizable="true" modal="true" buttons="#dlg_task-toolbar">  
</div>
        
<div id="dlg_task-toolbar">  
         <a href="#" class="easyui-linkbutton" iconCls="icon-redo" plain="true" onclick="tancarTask()">Tancar</a>  
</div>

<div id="dlg_sms" class="easyui-dialog" style="width:900px;height:600px;"  
            closed="true" maximized="true" maximizable="true" collapsible="true" resizable="true" modal="true" buttons="#dlg_sms-toolbar">  
</div>
        
<div id="dlg_sms-toolbar">
		 <a href="#" class="easyui-linkbutton" iconCls="icon-reload" plain="true" onclick="javascript:$('#dlg_sms').dialog('refresh')">Recarregar</a>
         <a href="#" class="easyui-linkbutton" iconCls="icon-redo" plain="true" onclick="tancarSMS()">Tancar</a>  
</div>
    
<iframe id="fitxer_pdf" scrolling="yes" frameborder="0" style="width:10px;height:10px; visibility:hidden" src=""></iframe>
    
    <style type="text/css">
        .grups{  
        }  
        .grups ul{  
            list-style:none;  
            margin:0;
			padding-left:10px; 
        }  
        .grups li{  
            display:inline;  
            float:left;  
			width:138px;
			height:165px;
            margin:1px;  
			border:1px dashed #ccc;
			padding-left:1px;
			padding-top:1px;
			overflow: hidden;
        }  
        .itemgrups{  
            display:block;
			float:left; 
            text-decoration:none;
			color: #777;
			margin:1px;
			height: auto;
			width:auto;
			height:auto;
			overflow: hidden;
        } 
	.itemgrupsfalta{  
            display:block;
			float:left; 
            text-decoration:none;
			background-color:#a70e11;
			color: #eee;
			margin:1px;
			height: auto;
			width:auto;
			height:auto;
			overflow:auto;
        } 
	.itemgrupsretard{  
            display:block;
			float:left; 
            text-decoration:none;
			background-color:#ffcb00;
			color: #222;
			margin:1px;
			height: auto;
			width:auto;
			height:auto;
			overflow:auto;
        } 
	.itemgrupsincidencia{  
            display:block;
			float:left; 
            text-decoration:none;
			background-color:#6eaff2;
			color: #222;
			margin:1px;
			height: auto;
			width:auto;
			height:auto;
			overflow:auto;
        }
	.itemccc{  
            display:block;
			float:left; 
            text-decoration:none;
			background-color:#cc00cc;
			color: #eee;
			margin:1px;
			height: auto;
			width:auto;
			height:auto;
			overflow:auto;
        }
	.itemgrupsjustificada{  
            display:block;
			float:left; 
            text-decoration:none;
			background-color:#a1d88b;
			color: #009a49;
			margin:1px;
			height: auto;
			width:auto;
			height:auto;
			overflow:auto;
        }
        .itemgrups img{  
             
        }  
        .itemgrups p{  
            margin:0;
            text-align:left;  
            color: #777;  
			margin:1px;
        }
		.clear {
			clear:both;
			height:1px;
			overflow:hidden;
		}
    </style>  
    
    <script type="text/javascript">
	var url;
	var idalum;
	var idgrups;
	var idmateria;
	
	$('#id_motius').combogrid({
            url:'./ccc_motius/ccc_motius_getdata.php',
            valueField:'idccc_motius',
            textField:'nom_motiu'
	});
        
        $('#id_tipus_incident').combogrid({
			url:'./assist/incidents_tipus_getdata.php',
			valueField:'idtipus_incident',
			textField:'tipus_incident',
			editable:false
	});
			
	function addFalta(clicked_id)
	{
		idalum = clicked_id;
		idgrups = <?=$idgrups?>;
		idmateria = <?=$idmateria?>;
		idfranges_horaries = <?=$idfranges_horaries?>;
		var alum   = '#al'+idalum;
		$(alum).css('background-color', '#a70e11');
		$(alum).css('color', '#eee');
		url = './assist/assist_nou_absencia.php';
		
		$.post(url,{id:idalum,idgrups:idgrups,idmateria:idmateria,idfranges_horaries:idfranges_horaries},function(result){  
            if (result.success){  
            } else {
	       location.href = './index.php';
               $.messager.show({   
               title: 'Error',  
               msg: result.errorMsg  
               });  
               }  
             },'json');
		 
	}
	
	function addRetard(clicked_id)
	{
		idalum = clicked_id;
		idgrups = <?=$idgrups?>;
		idmateria = <?=$idmateria?>;
		idfranges_horaries = <?=$idfranges_horaries?>;
		var alum = '#al'+idalum;
		$(alum).css('background-color', '#ffcb00');
		$(alum).css('color', '#222');
		url = './assist/assist_nou_retard.php';
		
		$.post(url,{id:idalum,idgrups:idgrups,idmateria:idmateria,idfranges_horaries:idfranges_horaries},function(result){  
            if (result.success){  
            } else {
			   location.href = './index.php';
               $.messager.show({   
               title: 'Error',  
               msg: result.errorMsg  
               });  
               }  
             },'json');
	}
	
	function addIncident(clicked_id)
	{
		idgrups = <?=$idgrups?>;
		idmateria = <?=$idmateria?>;
		idfranges_horaries = <?=$idfranges_horaries?>;
		idalum = clicked_id;
		var alum   = '#al'+idalum;
		
		url = './assist/assist_check_session.php';		
		$.post(url,{},function(result){ 
            if (result.success){  
            } else { 
			   location.href = './index.php'; 
               $.messager.show({   
               title: 'Error',  
               msg: result.errorMsg  
               });  
               }  
        },'json');
		
		$(alum).css('background-color', '#6eaff2');
		$(alum).css('color', '#222');
		$('#dlg_incidencia').dialog('open').dialog('setTitle','Seguiment');
		$('#fm_incidencia').form('load','./assist/assist_getdata_inc.php?id='+clicked_id+'&idgrups='+idgrups+'&idmateria='+idmateria+'&idfranges_horaries='+idfranges_horaries);
				
		url = './assist/assist_edita_inc.php?id='+clicked_id+'&idgrups='+idgrups+'&idmateria='+idmateria+'&idfranges_horaries='+idfranges_horaries;	 		
	}
	
	function saveIncident(){  
            $('#fm_incidencia').form('submit',{  
                url: url, 
                onSubmit: function(){  
                    return $(this).form('validate');  
                },  
                success: function(result){ 
					var result = eval('('+result+')');
                    if (result.msg){ 
                        $.messager.show({  
                            title: 'Error',  
                            msg: result.msg  
                        });  
                    } else {  
                        $('#dlg_incidencia').dialog('close'); 
                    }  
                }  
            });  
    }
	
	function cancelIncident(){
        $('#dlg_incidencia').dialog('close');
		var alum   = '#al'+idalum;
		idgrups = <?=$idgrups?>;
		idmateria = <?=$idmateria?>;
		idfranges_horaries = <?=$idfranges_horaries?>;
		
		/*$(alum).css('background-color', 'whitesmoke');
		$(alum).css('color', '#777');*/
    }
	
	function addCCC(clicked_id)
	{
		idgrups            = <?=$idgrups?>;
		idmateria          = <?=$idmateria?>;
		idfranges_horaries = <?=$idfranges_horaries?>;
		idespais_centre    = <?=$idespais_centre?>;
		
		idalum = clicked_id;
		var alum   = '#al'+idalum;
		
		url = './assist/assist_check_session.php';
		$.post(url,{},function(result){ 
                    if (result.success){  
                    } else { 
                       location.href = './index.php'; 
                       $.messager.show({   
                       title: 'Error',  
                       msg: result.errorMsg  
                       });  
                       }  
                },'json');
		
		$('#dlg_ccc').dialog('open').dialog('setTitle','CCC');
		$('#fm_ccc').form('load','./assist/assist_getdata_ccc.php?id='+clicked_id+'&idgrups='+idgrups+'&idmateria='+idmateria+'&idfranges_horaries='+idfranges_horaries+'&idespais_centre='+idespais_centre);
		
		url = './assist/assist_edita_ccc.php?id='+clicked_id+'&idgrups='+idgrups+'&idmateria='+idmateria+'&idfranges_horaries='+idfranges_horaries+'&idespais_centre='+idespais_centre;	 
	}
	
	function saveCCC(){
            var alum   = '#al'+idalum; 
            
            $('#fm_ccc').form('submit',{  
                url: url, 
                onSubmit: function(){  
                    return $(this).form('validate');  
                },  
                success: function(result){ 
                    var result = eval('('+result+')');
                    if (result.msg){
                        $.messager.show({  
                            title: 'Error',  
                            msg: result.msg  
                        });  
                    } else {  
                        $('#dlg_ccc').dialog('close'); 
                    }  
                    
                    $(alum).css('background-color', '#cc00cc');
                    $(alum).css('color', '#eee');
                } 
            });  
    }
	
	function cancelCCC(){  
        $('#dlg_ccc').dialog('close');
		var alum   = '#al'+idalum;
		idgrups = <?=$idgrups?>;
		idmateria = <?=$idmateria?>;
		idfranges_horaries = <?=$idfranges_horaries?>;
		idespais_centre    = <?=$idespais_centre?>;
		/*$(alum).css('background-color', 'whitesmoke');
		$(alum).css('color', '#777');*/
    }
	
	function Cancelar(clicked_id)
	{
		idalum = clicked_id;
		idgrups = <?=$idgrups?>;
		idmateria = <?=$idmateria?>;
		idfranges_horaries = <?=$idfranges_horaries?>;
		var alum   = '#al'+idalum;
		
		$.messager.confirm('Confirmar','Est&aacute;s segur de que vols esborrar aquesta entrada?',function(r){  
        	if (r){
				$(alum).css('background-color', '#f2f5f7');
				$(alum).css('color', '#777');
				$.post('./assist/assist_esborra.php',{id:idalum,idgrups:idgrups,idmateria:idmateria,idfranges_horaries:idfranges_horaries},function(result){  
					   if (result.success){
					   } else {  
						   $.messager.show({     
							 title: 'Error',  
							 msg: result.msg  
						   });  
					   }  
				},'json');  
			}
        });  
	
	}
	
	function informeAssistencia(idgrups,idmateria,nomgrup){  
		url = './assist/assist_see.php?idgrups='+idgrups+'&idmateria='+idmateria;
		$('#dlg_inf').dialog('open').dialog('setTitle','Assistencia del grup '+nomgrup);
		$('#dlg_inf').dialog('refresh', url);
    }
		
	function doReload(idgrups){
	    d_inici  = $('#data_inici').datebox('getValue');
		d_fi     = $('#data_fi').datebox('getValue');

		url = './assist/assist_see.php?idgrups='+idgrups+'&data_inici='+d_inici+'&data_fi='+d_fi+'&c_alumne=0';
		$('#dlg_inf').dialog('refresh', url);
	}
	
	function comprovarFeina(idprofessors,idfranges_horaries){	
		url = './guard/guard_task.php?idfranges_horaries='+idfranges_horaries+'&idprofessors='+idprofessors;
		$('#dlg_task').dialog('open').dialog('setTitle','Tasca hora de gu&agrave;rdia');
		$('#dlg_task').dialog('refresh', url);
	}
	
	function signarGuardia(idprofessors,idgrups,idmateria,idfranges_horaries){			
		$.messager.confirm('Confirmar','Vols signar aquesta gu&aacute;rdia?',function(r){  
        	if (r){
                    $.post('./guard/guard_sign.php',{idprofessors:idprofessors,idgrups:idgrups,
                          			     id_mat_uf_pla:idmateria,idfranges_horaries:idfranges_horaries},function(result){  
                    if (result.success){
			   	$('#sign_button').css('background-color', '#a1d88b');
				$('#sign_button').css('font-weight', 'bold');
				$('#sign_button').css('color', '#666');
				$('#sign_button').linkbutton('disable');
                    } else {  
                       $.messager.show({     
                         title: 'Error',  
                         msg: result.msg  
                       });  
                    }  
        	},'json');  
		}
        });
	}
	
	function verHorario(idgrups){  
		url = './hor/hor_see.php?idgrups='+idgrups;
			
		$('#dlg_hor').dialog('open').dialog('setTitle','Horari');
		$('#dlg_hor').dialog('refresh', url);
        }
	
	function tancarTask() {
		javascript:$('#dlg_task').dialog('close');
	}
	
	function tancarSMS() {
		javascript:$('#dlg_sms').dialog('close');
	}
	
	function enviarSMS(idgrups){
		url = './tutor_send/tutor_sms.php?idgrups='+idgrups;
			
		$('#dlg_sms').dialog('open').dialog('setTitle','Enviar SMS');
		$('#dlg_sms').dialog('refresh', url);
		$('#sms_button').linkbutton('disable');
	}
				
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

	</script>